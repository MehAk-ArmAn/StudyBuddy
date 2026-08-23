<?php

namespace Tests\Feature\Apps;

use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\User;
use App\Services\StudyBuddyWebAppPublisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * StudyBuddy is a launcher, not just a directory of store links.
 *
 * These cover the rule that matters publicly: a platform button appears only
 * when that platform is genuinely configured.
 */
class BrowserLauncherTest extends TestCase
{
    use RefreshDatabase;

    private string $originalPublicPath;

    private string $originalStoragePath;

    private string $testingRoot;

    private string $testingPublicPath;

    private string $testingStoragePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalPublicPath = app()->publicPath();
        $this->originalStoragePath = app()->storagePath();
        $this->testingRoot = $this->originalStoragePath.'/framework/testing/browser-launcher-'.Str::uuid();
        $this->testingPublicPath = $this->testingRoot.'/public';
        $this->testingStoragePath = $this->testingRoot.'/storage';
        File::ensureDirectoryExists($this->testingPublicPath);
        File::ensureDirectoryExists($this->testingStoragePath.'/app');
        app()->usePublicPath($this->testingPublicPath);
        app()->useStoragePath($this->testingStoragePath);

        StudyBuddyMiniAppPlatform::query()->delete();
    }

    protected function tearDown(): void
    {
        app()->usePublicPath($this->originalPublicPath);
        app()->useStoragePath($this->originalStoragePath);
        File::deleteDirectory($this->testingRoot);

        parent::tearDown();
    }

    private function app(array $overrides = []): StudyBuddyMiniAppPlatform
    {
        return StudyBuddyMiniAppPlatform::create(array_merge([
            'slug' => 'launcher-test',
            'name' => 'Launcher Test',
            'category' => 'Testing',
            'status' => 'live',
            'points_reward' => 10,
            'estimated_minutes' => 5,
            'is_active' => true,
        ], $overrides));
    }

    /** Publishes a real static build so the file checks are honest. */
    private function publishBuild(string $slug = 'launcher-test'): void
    {
        $directory = StudyBuddyWebAppPublisher::buildDirectory($slug);
        File::ensureDirectoryExists($directory);
        File::put($directory.'/index.html', '<!doctype html><title>Launcher Test</title><h1>Playing</h1>');
    }

    private function admin(): User
    {
        return User::forceCreate([
            'name' => 'Launcher Admin',
            'email' => 'launcher-admin@studybuddy.test',
            'password' => bcrypt('secret-password'),
            'is_admin' => true,
            'role' => 'admin',
        ]);
    }

    // ── Browser version off ─────────────────────────────────────────────

    public function test_no_browser_button_when_browser_play_is_disabled(): void
    {
        $this->app(['is_web_enabled' => false, 'web_play_url' => '/web-apps/launcher-test/index.html']);

        $this->get('/apps/launcher-test')
            ->assertOk()
            ->assertDontSee('Play in browser');
    }

    public function test_no_browser_button_when_enabled_but_nothing_is_published(): void
    {
        $this->app(['is_web_enabled' => true, 'web_play_url' => null]);

        $this->get('/apps/launcher-test')
            ->assertOk()
            ->assertDontSee('Play in browser')
            ->assertSee('Coming soon');
    }

    /**
     * Regression: a build whose files are gone must not advertise itself as
     * playable, or the launcher would open an empty frame.
     */
    public function test_no_browser_button_when_the_build_files_are_missing(): void
    {
        $app = $this->app([
            'is_web_enabled' => true,
            'web_play_url' => '/web-apps/launcher-test/index.html',
            'web_app_entry_path' => 'web-apps/launcher-test/index.html',
        ]);

        $this->assertFalse($app->hasPublishedWebApp());
        $this->get('/apps/launcher-test')->assertDontSee('Play in browser');
    }

    // ── Uploaded build ──────────────────────────────────────────────────

    public function test_an_uploaded_build_shows_the_browser_button_and_launches(): void
    {
        $this->publishBuild();

        $app = $this->app([
            'is_web_enabled' => true,
            'web_play_url' => '/web-apps/launcher-test/index.html',
            'web_app_entry_path' => 'web-apps/launcher-test/index.html',
        ]);

        $this->assertTrue($app->usesUploadedBuild());
        $this->assertFalse($app->usesExternalBrowserUrl());

        $this->get('/apps/launcher-test')
            ->assertOk()
            ->assertSee('Play in browser')
            ->assertSee(route('studybuddy.final.web-play', 'launcher-test'));

        // The launcher itself opens and frames our own build.
        $this->get('/play/launcher-test')
            ->assertOk()
            ->assertSee('Launcher Test')
            ->assertDontSee('allow-same-origin');

        $launcherScript = File::get($this->originalPublicPath.'/assets/js/studybuddy-launcher-v3.js');
        $this->assertStringContainsString("event.origin !== 'null'", $launcherScript);
        $this->assertStringContainsString('event.source !== frame.contentWindow', $launcherScript);
    }

    public function test_the_published_build_is_actually_served(): void
    {
        $this->publishBuild();
        $this->app([
            'is_web_enabled' => true,
            'web_play_url' => '/web-apps/launcher-test/index.html',
            'web_app_entry_path' => 'web-apps/launcher-test/index.html',
        ]);

        $response = $this->get('/app-builds/launcher-test/index.html');

        $response->assertOk();
        $response->assertHeader('Cross-Origin-Resource-Policy', 'cross-origin');
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
        $this->assertStringNotContainsString('immutable', $cacheControl);

        // The asset route streams the file, so the body is empty in tests;
        // assert on the file it decided to send instead.
        $this->assertStringContainsString(
            'Playing',
            file_get_contents($response->baseResponse->getFile()->getPathname())
        );

        $this->assertFileDoesNotExist(public_path('web-apps/launcher-test/index.html'));
    }

    public function test_inactive_build_assets_are_private_and_admin_preview_is_not_cached(): void
    {
        $this->publishBuild();
        $app = $this->app([
            'is_active' => false,
            'is_web_enabled' => true,
            'web_play_url' => '/web-apps/launcher-test/index.html',
            'web_app_entry_path' => 'web-apps/launcher-test/index.html',
        ]);

        $this->get('/app-builds/launcher-test/index.html')->assertNotFound();

        $response = $this->actingAs($this->admin())
            ->get(route('admin.control-room.apps.preview.asset', [
                'app' => $app,
                'path' => 'index.html',
            ]))
            ->assertOk();

        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('private', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
    }

    public function test_legitimate_double_dot_filename_is_served_but_parent_segments_are_not(): void
    {
        $this->publishBuild();
        File::put(
            StudyBuddyWebAppPublisher::buildDirectory('launcher-test').'/asset..js',
            'window.launcherTest = true;'
        );
        $this->app([
            'is_web_enabled' => true,
            'web_play_url' => '/web-apps/launcher-test/index.html',
            'web_app_entry_path' => 'web-apps/launcher-test/index.html',
        ]);

        $response = $this->get('/app-builds/launcher-test/asset..js')->assertOk();
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('must-revalidate', $cacheControl);
        $this->assertStringNotContainsString('immutable', $cacheControl);
    }

    /**
     * The asset route must not hand out files from outside the app's own
     * published build directory.
     */
    public function test_the_asset_route_refuses_to_escape_the_build_directory(): void
    {
        $this->publishBuild();
        $this->app([
            'is_web_enabled' => true,
            'web_play_url' => '/web-apps/launcher-test/index.html',
            'web_app_entry_path' => 'web-apps/launcher-test/index.html',
        ]);

        foreach ([
            '/app-builds/launcher-test/../../../.env',
            '/app-builds/launcher-test/..%2F..%2F..%2F.env',
        ] as $path) {
            $this->get($path)->assertNotFound();
        }
    }

    // ── External build ──────────────────────────────────────────────────

    public function test_an_external_url_shows_the_browser_button(): void
    {
        $app = $this->app([
            'is_web_enabled' => true,
            'web_play_url' => 'https://games.example.com/launcher-test/',
        ]);

        $this->assertTrue($app->usesExternalBrowserUrl());
        $this->assertFalse($app->usesUploadedBuild());

        $this->get('/apps/launcher-test')->assertOk()->assertSee('Play in browser');
    }

    /**
     * An app we do not host is handed off in a new tab. Framing it would break
     * against the other site's security headers.
     */
    public function test_an_external_app_is_handed_off_rather_than_framed(): void
    {
        $this->app([
            'is_web_enabled' => true,
            'web_play_url' => 'https://games.example.com/launcher-test/',
        ]);

        $response = $this->get('/play/launcher-test');

        $response->assertOk()
            ->assertSee('https://games.example.com/launcher-test/')
            ->assertDontSee('<iframe', false);
    }

    public function test_changing_the_launch_address_changes_where_the_public_page_points(): void
    {
        $app = $this->app([
            'is_web_enabled' => true,
            'web_play_url' => 'https://old.example.com/game/',
        ]);

        $this->get('/play/launcher-test')->assertSee('https://old.example.com/game/');

        $app->update(['web_play_url' => 'https://new.example.com/game/']);

        $this->get('/play/launcher-test')
            ->assertSee('https://new.example.com/game/')
            ->assertDontSee('https://old.example.com/game/');
    }

    public function test_turning_the_browser_version_off_removes_the_button(): void
    {
        $app = $this->app([
            'is_web_enabled' => true,
            'web_play_url' => 'https://games.example.com/launcher-test/',
        ]);

        $this->get('/apps/launcher-test')->assertSee('Play in browser');

        $app->update(['is_web_enabled' => false]);

        $this->get('/apps/launcher-test')->assertDontSee('Play in browser');
    }

    // ── Store links ─────────────────────────────────────────────────────

    public function test_store_buttons_appear_only_for_configured_stores(): void
    {
        $this->app([
            'is_download_enabled' => true,
            'android_url' => 'https://play.google.com/store/apps/details?id=fun.studybuddy.test',
        ]);

        $this->get('/apps/launcher-test')
            ->assertOk()
            ->assertSee('Google Play')
            ->assertDontSee('App Store');
    }

    public function test_both_store_buttons_appear_when_both_are_configured(): void
    {
        $this->app([
            'is_download_enabled' => true,
            'android_url' => 'https://play.google.com/store/apps/details?id=fun.studybuddy.test',
            'ios_url' => 'https://apps.apple.com/app/id123456789',
        ]);

        $this->get('/apps/launcher-test')
            ->assertSee('Google Play')
            ->assertSee('App Store');
    }

    public function test_no_store_buttons_when_downloads_are_switched_off(): void
    {
        $this->app([
            'is_download_enabled' => false,
            'android_url' => 'https://play.google.com/store/apps/details?id=fun.studybuddy.test',
        ]);

        $this->get('/apps/launcher-test')->assertDontSee('Google Play');
    }

    public function test_removing_a_store_url_removes_its_button(): void
    {
        $app = $this->app([
            'is_download_enabled' => true,
            'android_url' => 'https://play.google.com/store/apps/details?id=fun.studybuddy.test',
        ]);

        $this->get('/apps/launcher-test')->assertSee('Google Play');

        $app->update(['android_url' => null]);

        $this->get('/apps/launcher-test')->assertDontSee('Google Play');
    }

    public function test_an_app_with_nowhere_to_go_says_coming_soon(): void
    {
        $this->app();

        $this->get('/apps/launcher-test')
            ->assertOk()
            ->assertSee('Coming soon')
            ->assertDontSee('Play in browser')
            ->assertDontSee('Google Play');
    }

    /**
     * With a browser build available it leads; otherwise the first store link
     * is promoted so the card always has one obvious action.
     */
    public function test_the_browser_action_leads_when_several_platforms_exist(): void
    {
        $this->publishBuild();

        $app = $this->app([
            'is_web_enabled' => true,
            'web_play_url' => '/web-apps/launcher-test/index.html',
            'web_app_entry_path' => 'web-apps/launcher-test/index.html',
            'is_download_enabled' => true,
            'android_url' => 'https://play.google.com/store/apps/details?id=fun.studybuddy.test',
        ]);

        $actions = $app->availableActions();

        $this->assertSame('browser', $actions[0]['key']);
        $this->assertTrue($actions[0]['primary']);
        $this->assertSame('android', $actions[1]['key']);
        $this->assertFalse($actions[1]['primary']);
    }

    public function test_a_store_link_leads_when_there_is_no_browser_build(): void
    {
        $app = $this->app([
            'is_download_enabled' => true,
            'ios_url' => 'https://apps.apple.com/app/id123456789',
        ]);

        $actions = $app->availableActions();

        $this->assertCount(1, $actions);
        $this->assertSame('ios', $actions[0]['key']);
        $this->assertTrue($actions[0]['primary']);
    }

    public function test_an_unpublished_app_is_not_reachable_at_all(): void
    {
        $this->app(['is_active' => false, 'is_web_enabled' => true, 'web_play_url' => 'https://games.example.com/x/']);

        $this->get('/apps/launcher-test')->assertNotFound();
        $this->get('/play/launcher-test')->assertNotFound();
    }
}
