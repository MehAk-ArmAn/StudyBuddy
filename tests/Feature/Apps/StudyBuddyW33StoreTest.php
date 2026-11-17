<?php

namespace Tests\Feature\Apps;

use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\User;
use App\Services\StudyBuddyW33DesktopKit;
use App\Services\StudyBuddyWebAppPublisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class StudyBuddyW33StoreTest extends TestCase
{
    use RefreshDatabase;

    private function user(bool $admin, string $email): User
    {
        return User::query()->create(['name' => 'W33 Tester', 'email' => $email,
            'password' => 'ExamplePassword123!', 'is_admin' => $admin, 'role' => 'independent_learner']);
    }

    private function app(string $slug = 'w33-test-app', array $extra = []): StudyBuddyMiniAppPlatform
    {
        return StudyBuddyMiniAppPlatform::query()->create(array_merge([
            'slug' => $slug, 'name' => 'W33 App', 'category' => 'Math', 'status' => 'beta',
            'is_active' => true, 'is_download_enabled' => true, 'is_web_enabled' => false,
        ], $extra));
    }

    public function test_only_admin_can_edit_showcase_or_upload_packages(): void
    {
        $app = $this->app();
        $this->get("/admin/control-room/apps/{$app->id}/showcase")->assertRedirect();
        $normal = $this->user(false, 'w33-normal@example.org');
        $this->actingAs($normal)->get("/admin/control-room/apps/{$app->id}/showcase")->assertRedirect();
        $this->actingAs($normal)->post("/admin/control-room/apps/{$app->id}/showcase", ['why_play' => 'Hacked'])
            ->assertRedirect();
        $this->assertDatabaseCount('studybuddy_w33_showcases', 0);
    }

    public function test_admin_updates_showcase_and_public_page_displays_real_highlights(): void
    {
        $admin = $this->user(true, 'w33-admin@example.org');
        $app = $this->app();
        $this->actingAs($admin)->post("/admin/control-room/apps/{$app->id}/showcase", [
            'version' => '1.2', 'why_play' => 'Real Flutter learning world',
            'highlights' => "Real challenges\nAdaptive lessons", 'whats_new' => 'Bug fixes',
            'privacy_summary' => 'Private by design',
        ])->assertRedirect();
        $this->assertDatabaseHas('studybuddy_w33_showcases', ['app_id' => $app->id, 'version' => '1.2']);
        $this->actingAs($admin)->get('/apps/'.$app->slug)->assertOk()
            ->assertSee('Real challenges')->assertSee('Adaptive lessons')->assertSee('Private by design');
    }

    public function test_screen_captures_are_scoped_to_published_app_and_private_when_unpublished(): void
    {
        $admin = $this->user(true, 'w33-shot-admin@example.org');
        $app = $this->app('w33-private-shot');
        Storage::fake('local');
        $upload = UploadedFile::fake()->createWithContent('shot.png', file_get_contents(__DIR__.'/../../Fixtures/w33-screenshot.png'));
        $this->actingAs($admin)->post("/admin/control-room/apps/{$app->id}/showcase/screenshots", [
            'screenshots' => [$upload],
        ])->assertRedirect()->assertSessionHasNoErrors();
        $shot = DB::table('studybuddy_w33_screenshots')->where('app_id', $app->id)->first();
        $this->assertNotNull($shot);
        $this->actingAs($admin)->get("/apps/{$app->slug}/showcase/screenshots/{$shot->id}")->assertOk();
        $app->update(['is_active' => false]);
        $this->actingAs($admin)->get("/apps/{$app->slug}/showcase/screenshots/{$shot->id}")->assertNotFound();
        $this->actingAs($admin)->get("/admin/control-room/apps/{$app->id}/showcase/screenshots/{$shot->id}/image")->assertOk();
    }

    public function test_native_desktop_download_exists_only_for_its_published_app(): void
    {
        Storage::fake('local');
        $admin = $this->user(true, 'w33-pkg-admin@example.org');
        $app = $this->app('w33-pkg');
        $upload = UploadedFile::fake()->createWithContent('mygame.dmg', 'sample package bytes');
        $this->actingAs($admin)->post("/admin/control-room/apps/{$app->id}/showcase/desktop/mac", [
            'desktop_file' => $upload, 'release_version' => '1.0',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->actingAs($admin)->get('/apps/w33-pkg/desktop/mac')->assertOk()->assertDownload('mygame.dmg');
        $app->update(['is_active' => false]);
        $this->actingAs($admin)->get('/apps/w33-pkg/desktop/mac')->assertNotFound();
        $this->actingAs($admin)->get('/apps/w33-pkg/desktop/windows')->assertNotFound();
    }

    public function test_installable_manifest_and_electron_kit_use_published_web_build_without_rewriting_the_game(): void
    {
        if (! class_exists(ZipArchive::class) && ! class_exists(\PharData::class)) $this->markTestSkipped('ZIP writer unavailable');
        $admin = $this->user(true, 'w33-pwa@example.org');
        $app = $this->app('w33-pwa-test', [
            'is_web_enabled' => true,
            'web_play_url' => '/web-apps/w33-pwa-test/index.html',
            'web_app_entry_path' => 'web-apps/w33-pwa-test/index.html',
        ]);
        $directory = StudyBuddyWebAppPublisher::buildDirectory($app->slug);
        File::ensureDirectoryExists($directory);
        File::put($directory.'/index.html', '<html><head><base href="/app-builds/w33-pwa-test/"></head><body>Game</body></html>');
        File::put($directory.'/game.js', 'console.log("a real game build")');
        try {
            $this->actingAs($admin)->get('/apps/w33-pwa-test/install-desktop')
                ->assertRedirect('/app-builds/w33-pwa-test/index.html?sb33install=1');
            $this->actingAs($admin)->get('/app-builds/w33-pwa-test/studybuddy-w33-manifest.webmanifest')
                ->assertOk()->assertSee('standalone');
            $zipPath = app(StudyBuddyW33DesktopKit::class)->create($app);
            if (class_exists(ZipArchive::class)) {
                $zip = new ZipArchive();
                $this->assertSame(true, $zip->open($zipPath));
                $entry = (string) $zip->getFromName('web/index.html');
                $this->assertNotFalse($zip->getFromName('desktop/main.cjs'));
                $zip->close();
            } else {
                $zip = new \PharData($zipPath);
                $entry = $zip['web/index.html']->getContent();
                $this->assertTrue(isset($zip['desktop/main.cjs']));
                unset($zip);
            }
            $this->assertStringContainsString('<base href="./">', $entry);
            File::delete($zipPath);
        } finally {
            File::deleteDirectory($directory);
        }
    }
}
