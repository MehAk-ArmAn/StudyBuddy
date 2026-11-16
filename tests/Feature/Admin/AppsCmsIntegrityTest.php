<?php

namespace Tests\Feature\Admin;

use App\Models\StudyBuddyAppCatalogItem;
use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use ZipArchive;

class AppsCmsIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private ?User $adminUser = null;

    private function admin(): User
    {
        return $this->adminUser ??= User::forceCreate([
            'name' => 'Admin',
            'email' => 'apps-integrity@studybuddy.test',
            'password' => bcrypt('secret-password'),
            'is_admin' => true,
            'role' => 'admin',
        ]);
    }

    /** @return array<string, mixed> */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Flag Frenzy',
            'slug' => 'flag-frenzy',
            'category' => 'Geography',
            'status' => 'live',
            'tagline' => 'Spot the right flag.',
            'description' => 'A short geography challenge.',
            'points_reward' => 30,
            'estimated_minutes' => 8,
            'save_action' => 'publish',
        ], $overrides);
    }

    public function test_content_studio_has_one_clear_route_to_the_canonical_apps_workspace(): void
    {
        $legacy = StudyBuddyAppCatalogItem::create([
            'app_key' => 'retired-entry',
            'title' => 'Retired Entry',
            'launch_status' => 'planned',
            'is_active' => false,
        ]);

        $this->actingAs($this->admin())
            ->get('/admin/control-room/content-studio')
            ->assertOk()
            ->assertSee('Manage Apps')
            ->assertSee(route('admin.control-room.apps.index'), false)
            ->assertDontSee('Mini-App Catalog')
            ->assertDontSee('Retired Entry');

        $before = $legacy->refresh()->getRawOriginal();

        $this->actingAs($this->admin())
            ->patch("/admin/control-room/content-studio/apps/{$legacy->id}", [
                'title' => 'Should Not Save',
                'launch_status' => 'live',
            ])
            ->assertRedirect(route('admin.control-room.apps.index'));

        $this->assertSame($before, $legacy->fresh()->getRawOriginal());
    }

    public function test_apps_pagination_uses_the_local_accessible_controls(): void
    {
        foreach (range(1, 13) as $position) {
            StudyBuddyMiniAppPlatform::create([
                'name' => sprintf('App %02d', $position),
                'slug' => sprintf('app-%02d', $position),
                'category' => 'Learning',
                'status' => 'planned',
                'is_active' => false,
                'sort_order' => $position,
            ]);
        }

        $this->actingAs($this->admin())
            ->get('/admin/control-room/apps')
            ->assertOk()
            ->assertSee('aria-label="Apps pages"', false)
            ->assertSee('Page 1')
            ->assertSee('Next');

        $this->actingAs($this->admin())
            ->get('/admin/control-room/apps?page=2')
            ->assertOk()
            ->assertSee('Page 2')
            ->assertSee('Previous');
    }

    public function test_store_and_support_destinations_must_be_secure_urls(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'ios_url' => 'http://apps.example.test/flag-frenzy',
                'support_url' => 'ftp://support.example.test/flag-frenzy',
            ]))
            ->assertSessionHasErrors(['ios_url', 'support_url']);

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 0);
    }

    public function test_typed_artwork_paths_cannot_traverse_or_be_protocol_relative(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'image_url' => '/assets/apps/../private.png',
                'hero_image' => '//images.example.test/cover.png',
            ]))
            ->assertSessionHasErrors(['image_url', 'hero_image']);

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 0);
    }

    public function test_replacing_one_shared_artwork_field_keeps_the_other_file(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload([
            'image_url_file' => UploadedFile::fake()->image('shared.png'),
        ]));

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');
        $sharedPath = $app->image_url;
        $storedSharedPath = str_replace('/storage/', '', $sharedPath);
        $app->update(['hero_image' => $sharedPath]);

        $this->actingAs($this->admin())
            ->put("/admin/control-room/apps/{$app->id}", $this->validPayload([
                'hero_image' => $sharedPath,
                'image_url_file' => UploadedFile::fake()->image('replacement.png'),
            ]))
            ->assertSessionHasNoErrors();

        $app->refresh();
        $this->assertSame($sharedPath, $app->hero_image);
        $this->assertNotSame($sharedPath, $app->image_url);
        Storage::disk('public')->assertExists($storedSharedPath);
    }

    public function test_new_artwork_uses_the_updated_slug_directory(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload());
        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->actingAs($this->admin())
            ->put("/admin/control-room/apps/{$app->id}", $this->validPayload([
                'name' => 'Flag Voyage',
                'slug' => 'flag-voyage',
                'image_url_file' => UploadedFile::fake()->image('voyage.png'),
            ]))
            ->assertSessionHasNoErrors();

        $this->assertStringStartsWith(
            '/storage/studybuddy/apps/flag-voyage/',
            (string) $app->fresh()->image_url
        );
    }

    public function test_failed_replacement_zip_keeps_the_last_good_draft_build_private(): void
    {
        $this->withIsolatedPublisherPaths(function (string $storage, string $tmp): void {
            $firstZip = $this->zipUpload($tmp.'/uploads', [
                'index.html' => '<!doctype html><h1>Last good build</h1>',
                'assets/app.js' => 'window.goodBuild = true;',
            ]);

            $this->actingAs($this->admin())
                ->post('/admin/control-room/apps', $this->validPayload([
                    'web_app_zip' => $firstZip,
                    'save_action' => 'draft',
                ]))
                ->assertSessionHasNoErrors();

            $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');
            $previous = $app->only([
                'web_play_url',
                'web_app_entry_path',
                'web_app_package_path',
                'web_app_uploaded_at',
            ]);
            $build = $storage.'/app/studybuddy-web-apps/flag-frenzy/index.html';
            $this->assertFileExists($build);

            $brokenZip = $this->zipUpload(
                $tmp.'/uploads',
                ['readme.txt' => 'No entry file'],
                'broken.zip'
            );

            $this->actingAs($this->admin())
                ->put("/admin/control-room/apps/{$app->id}", $this->validPayload([
                    'web_app_zip' => $brokenZip,
                    'is_web_enabled' => '1',
                    'save_action' => 'publish',
                    'tagline' => 'Details still save safely.',
                ]))
                ->assertSessionHasErrors('web_app_zip');

            $app->refresh();
            $this->assertFalse($app->is_active);
            $this->assertSame('Details still save safely.', $app->tagline);
            $this->assertSame($previous['web_play_url'], $app->web_play_url);
            $this->assertSame($previous['web_app_entry_path'], $app->web_app_entry_path);
            $this->assertSame($previous['web_app_package_path'], $app->web_app_package_path);
            $this->assertSame(
                $previous['web_app_uploaded_at']->getTimestamp(),
                $app->web_app_uploaded_at->getTimestamp()
            );
            $this->assertStringContainsString('Last good build', File::get($build));
            $this->assertTrue($app->hasPublishedWebApp());
            $this->get('/apps/flag-frenzy')->assertNotFound();
        });
    }

    public function test_switching_an_uploaded_build_to_an_external_url_removes_managed_files(): void
    {
        $this->withIsolatedPublisherPaths(function (string $storage, string $tmp): void {
            $zip = $this->zipUpload($tmp.'/uploads', [
                'index.html' => '<!doctype html><h1>Managed build</h1>',
            ]);

            $this->actingAs($this->admin())
                ->post('/admin/control-room/apps', $this->validPayload([
                    'web_app_zip' => $zip,
                ]))
                ->assertSessionHasNoErrors();

            $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');
            $package = $storage.'/app/'.$app->web_app_package_path;
            $this->assertFileExists($package);

            $this->actingAs($this->admin())
                ->put("/admin/control-room/apps/{$app->id}", $this->validPayload([
                    'name' => 'Flag Voyage',
                    'slug' => 'flag-voyage',
                    'web_play_url' => 'https://games.example.test/flag-voyage',
                    'is_web_enabled' => '1',
                ]))
                ->assertSessionHasNoErrors();

            $app->refresh();
            $this->assertSame('flag-voyage', $app->slug);
            $this->assertSame('https://games.example.test/flag-voyage', $app->web_play_url);
            $this->assertNull($app->web_app_entry_path);
            $this->assertNull($app->web_app_package_path);
            $this->assertNull($app->web_app_uploaded_at);
            $this->assertTrue($app->hasPublishedWebApp());
            $this->assertDirectoryDoesNotExist($storage.'/app/studybuddy-web-apps/flag-frenzy');
            $this->assertDirectoryDoesNotExist($storage.'/app/studybuddy-web-apps/flag-voyage');
            $this->assertFileDoesNotExist($package);
        });
    }

    public function test_remove_browser_version_overrides_the_prefilled_external_url(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'web_play_url' => 'https://games.example.test/flag-frenzy',
                'is_web_enabled' => '1',
            ]))
            ->assertSessionHasNoErrors();

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->actingAs($this->admin())
            ->put("/admin/control-room/apps/{$app->id}", $this->validPayload([
                'web_play_url' => 'https://games.example.test/flag-frenzy',
                'is_web_enabled' => '1',
                'remove_web_app' => '1',
            ]))
            ->assertSessionHasNoErrors();

        $app->refresh();
        $this->assertNull($app->web_play_url);
        $this->assertFalse($app->is_web_enabled);
        $this->assertFalse($app->hasPublishedWebApp());
    }

    /** @param callable(string, string): void $callback */
    private function withIsolatedPublisherPaths(callable $callback): void
    {
        $originalPublic = app()->publicPath();
        $originalStorage = app()->storagePath();
        $tmp = sys_get_temp_dir().'/studybuddy-apps-integrity-'.Str::uuid()->toString();
        $public = $tmp.'/public';
        $storage = $tmp.'/storage';
        File::ensureDirectoryExists($public);
        File::ensureDirectoryExists($storage.'/app');
        app()->usePublicPath($public);
        app()->useStoragePath($storage);

        try {
            $callback($storage, $tmp);
        } finally {
            app()->usePublicPath($originalPublic);
            app()->useStoragePath($originalStorage);
            File::deleteDirectory($tmp);
        }
    }

    /** @param array<string, string> $entries */
    private function zipUpload(string $directory, array $entries, string $name = 'web-app.zip'): UploadedFile
    {
        File::ensureDirectoryExists($directory);
        $path = $directory.'/'.Str::uuid()->toString().'.zip';
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true);

        foreach ($entries as $entry => $contents) {
            $this->assertTrue($zip->addFromString($entry, $contents));
        }

        $this->assertTrue($zip->close());

        return new UploadedFile($path, $name, 'application/zip', null, true);
    }
}
