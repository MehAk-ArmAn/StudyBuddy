<?php

namespace Tests\Feature\Apps;

use App\Http\Requests\Admin\StudyBuddyAppRequest;
use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\User;
use App\Services\StudyBuddyWebAppPublisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;
use ZipArchive;

/**
 * Hosting a real `flutter build web` artefact.
 *
 * Flutter writes `<base href="/">` into index.html, so an untouched build asks
 * the site root for flutter_bootstrap.js, main.dart.js, canvaskit/ and assets/.
 * Every one of those 404s and the learner gets a white rectangle. StudyBuddy
 * therefore re-points the base at publish time, which is what allows a single
 * unmodified ZIP to be published under any slug without rebuilding it.
 */
class FlutterWebBuildTest extends TestCase
{
    use RefreshDatabase;

    private string $originalPublicPath;

    private string $originalStoragePath;

    private string $testingRoot;

    private ?User $admin = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalPublicPath = app()->publicPath();
        $this->originalStoragePath = app()->storagePath();
        $this->testingRoot = $this->originalStoragePath.'/framework/testing/flutter-build-'.Str::uuid();

        File::ensureDirectoryExists($this->testingRoot.'/public');
        File::ensureDirectoryExists($this->testingRoot.'/storage/app');

        app()->usePublicPath($this->testingRoot.'/public');
        app()->useStoragePath($this->testingRoot.'/storage');

        StudyBuddyMiniAppPlatform::query()->delete();
    }

    protected function tearDown(): void
    {
        app()->usePublicPath($this->originalPublicPath);
        app()->useStoragePath($this->originalStoragePath);
        File::deleteDirectory($this->testingRoot);

        parent::tearDown();
    }

    // ── Fixtures ────────────────────────────────────────────────────────

    private function admin(): User
    {
        return $this->admin ??= User::forceCreate([
            'name' => 'Build Admin',
            'email' => 'build-admin@studybuddy.test',
            'password' => bcrypt('secret-password'),
            'is_admin' => true,
            'role' => 'admin',
        ]);
    }

    private function app(array $overrides = []): StudyBuddyMiniAppPlatform
    {
        return StudyBuddyMiniAppPlatform::create(array_merge([
            'slug' => 'example-slug',
            'name' => 'Example Slug',
            'category' => 'Testing',
            'status' => 'live',
            'points_reward' => 10,
            'estimated_minutes' => 5,
            'is_active' => true,
            'is_web_enabled' => true,
        ], $overrides));
    }

    /** The head of a genuine `flutter build web --release` index.html. */
    private function flutterIndex(string $base = '<base href="/">'): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
          <!-- change the href value below to reflect the base path -->
          {$base}
          <meta charset="UTF-8">
          <title>example</title>
          <link rel="manifest" href="manifest.json">
        </head>
        <body>
          <script src="flutter_bootstrap.js" async></script>
        </body>
        </html>
        HTML;
    }

    /** @return array<string, string> */
    private function flutterEntries(string $base = '<base href="/">'): array
    {
        return [
            'index.html' => $this->flutterIndex($base),
            'flutter_bootstrap.js' => '_flutter.loader.load({});',
            'main.dart.js' => '(function(){ /* dart2js output */ })();',
            'flutter_service_worker.js' => "self.addEventListener('install', () => {});",
            'manifest.json' => '{"name":"example"}',
            'canvaskit/canvaskit.js' => 'export default function () {}',
            'canvaskit/canvaskit.wasm' => "\0asm\1\0\0\0",
            'assets/AssetManifest.bin.json' => '{}',
            'assets/fonts/MaterialIcons-Regular.otf' => 'OTTO',
            'assets/assets/branding/logo.png' => 'PNGDATA',
        ];
    }

    /** @param array<string, string> $entries */
    private function zipUpload(array $entries, string $name = 'flutter-web.zip'): UploadedFile
    {
        $directory = $this->testingRoot.'/zips';
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

    private function publish(
        StudyBuddyMiniAppPlatform $app,
        ?array $entries = null
    ): StudyBuddyMiniAppPlatform {
        $result = app(StudyBuddyWebAppPublisher::class)->publish(
            $app,
            $this->zipUpload($entries ?? $this->flutterEntries())
        );

        $app->forceFill($result)->save();

        return $app->refresh();
    }

    private function publishedIndex(string $slug): string
    {
        return File::get(
            StudyBuddyWebAppPublisher::buildDirectory($slug).'/index.html'
        );
    }

    // ── A + B: the configured upload ceiling ────────────────────────────

    public function test_the_browser_zip_limit_is_configured_at_60_mb(): void
    {
        $rules = (new StudyBuddyAppRequest())->rules()['web_app_zip'];

        // 61440 KB is how Laravel spells 60 MB for an uploaded file.
        $this->assertContains('max:61440', $rules);
        $this->assertContains('mimes:zip', $rules);
    }

    public function test_a_zip_over_the_limit_is_rejected_in_plain_language(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', [
                'name' => 'Oversized Build',
                'slug' => 'oversized-build',
                'category' => 'Testing',
                'status' => 'live',
                'points_reward' => 10,
                'estimated_minutes' => 5,
                'is_web_enabled' => '1',
                'web_app_zip' => UploadedFile::fake()->create('huge.zip', 61441, 'application/zip'),
            ])
            ->assertSessionHasErrors(['web_app_zip' => 'Keep the web build ZIP under 60 MB.']);

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 0);
    }

    // ── C + D + E: central base-href normalisation ──────────────────────

    public function test_publishing_repoints_a_root_base_href_at_the_app_build_path(): void
    {
        $this->publish($this->app());

        $index = $this->publishedIndex('example-slug');

        $this->assertStringContainsString('<base href="/app-builds/example-slug/">', $index);
        $this->assertStringNotContainsString('<base href="/">', $index);

        // Exactly one base element: a second would be ignored by the browser
        // and would make the real value ambiguous.
        $this->assertSame(1, preg_match_all('#<base\b[^>]*>#i', $index));
    }

    public function test_the_same_build_published_under_another_slug_gets_that_slug(): void
    {
        $entries = $this->flutterEntries();

        $this->publish($this->app(['slug' => 'mathibble', 'name' => 'Mathibble']), $entries);
        $this->publish($this->app(['slug' => 'maths-game', 'name' => 'Maths Game']), $entries);

        $this->assertStringContainsString(
            '<base href="/app-builds/mathibble/">',
            $this->publishedIndex('mathibble')
        );

        $this->assertStringContainsString(
            '<base href="/app-builds/maths-game/">',
            $this->publishedIndex('maths-game')
        );
    }

    public function test_a_base_tag_is_inserted_when_the_build_has_none(): void
    {
        $entries = $this->flutterEntries();
        $entries['index.html'] = '<!DOCTYPE html><html><head><title>No base</title></head>'
            .'<body><script src="main.dart.js"></script></body></html>';

        $this->publish($this->app(), $entries);

        $index = $this->publishedIndex('example-slug');

        $this->assertStringContainsString('<base href="/app-builds/example-slug/">', $index);
        $this->assertStringContainsString('<title>No base</title>', $index);

        // It has to land inside <head>, before the entry script runs.
        $this->assertLessThan(
            strpos($index, '<script src="main.dart.js">'),
            strpos($index, '<base href=')
        );
    }

    public function test_the_served_base_href_follows_the_admin_preview_mount(): void
    {
        $app = $this->publish($this->app(['is_active' => false]));

        $response = $this->actingAs($this->admin())
            ->get('/admin/control-room/apps/'.$app->id.'/preview/build/index.html')
            ->assertOk();

        $this->assertStringContainsString(
            '<base href="/admin/control-room/apps/'.$app->id.'/preview/build/">',
            $response->getContent()
        );
    }

    public function test_a_build_published_before_this_existed_is_repaired_as_it_is_served(): void
    {
        // Exactly what was on disk for apps published by the old publisher:
        // a root base href and no launcher bridge.
        $directory = StudyBuddyWebAppPublisher::buildDirectory('example-slug');
        File::ensureDirectoryExists($directory.'/canvaskit');
        File::put($directory.'/index.html', $this->flutterIndex());
        File::put($directory.'/canvaskit/canvaskit.js', 'export default function () {}');

        $this->app([
            'web_play_url' => '/web-apps/example-slug/index.html',
            'web_app_entry_path' => 'web-apps/example-slug/index.html',
        ]);

        $body = $this->get('/app-builds/example-slug/index.html')->assertOk()->getContent();

        $this->assertStringContainsString('<base href="/app-builds/example-slug/">', $body);
        $this->assertStringContainsString('studybuddy:app-ready', $body);
        $this->assertSame(1, substr_count($body, 'data-studybuddy-bridge'));

        // Repairing on the way out must not rewrite what is stored.
        $this->assertStringContainsString('<base href="/">', File::get($directory.'/index.html'));
    }

    public function test_a_freshly_published_build_is_not_given_a_second_bridge(): void
    {
        $this->publish($this->app());

        $body = $this->get('/app-builds/example-slug/index.html')->assertOk()->getContent();

        $this->assertSame(1, substr_count($body, 'data-studybuddy-bridge'));
        $this->assertSame(1, preg_match_all('#<base\b[^>]*>#i', $body));
    }

    // ── F–J: what the browser needs the responses to say ────────────────

    public function test_flutter_entry_files_are_served_with_the_right_content_types(): void
    {
        $this->publish($this->app());

        $this->get('/app-builds/example-slug/index.html')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/html; charset=UTF-8');

        foreach (['flutter_bootstrap.js', 'main.dart.js'] as $script) {
            $type = $this->get('/app-builds/example-slug/'.$script)
                ->assertOk()
                ->headers->get('Content-Type');

            // Either spelling is executable; text/javascript is the current
            // standard one. "text/html" is what a 404 page would return, and
            // that is what strict MIME checking refuses to run.
            $this->assertContains(
                $type,
                ['text/javascript; charset=UTF-8', 'application/javascript; charset=UTF-8'],
                $script.' must be served as JavaScript.'
            );
        }

        $this->get('/app-builds/example-slug/canvaskit/canvaskit.wasm')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/wasm');
    }

    public function test_nested_flutter_assets_are_served(): void
    {
        $this->publish($this->app());

        $this->get('/app-builds/example-slug/assets/AssetManifest.bin.json')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/json; charset=UTF-8');

        $this->get('/app-builds/example-slug/assets/fonts/MaterialIcons-Regular.otf')
            ->assertOk()
            ->assertHeader('Content-Type', 'font/otf');

        $this->get('/app-builds/example-slug/assets/assets/branding/logo.png')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $this->get('/app-builds/example-slug/assets/does-not-exist.png')->assertNotFound();
    }

    public function test_a_hosted_build_may_not_widen_its_service_worker_scope(): void
    {
        $this->publish($this->app());

        // Without this header a service worker is capped at its own folder and
        // can never claim /, /apps or /admin.
        $this->get('/app-builds/example-slug/flutter_service_worker.js')
            ->assertOk()
            ->assertHeaderMissing('Service-Worker-Allowed');
    }

    public function test_the_build_policy_stays_on_our_origin_apart_from_the_font_host(): void
    {
        $this->publish($this->app());

        $policy = $this->get('/app-builds/example-slug/index.html')
            ->assertOk()
            ->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("frame-ancestors 'self'", $policy);
        $this->assertStringContainsString("object-src 'none'", $policy);
        $this->assertStringContainsString('https://fonts.gstatic.com', $policy);

        // A blanket https: source would let a hosted app pull code from
        // anywhere at all.
        $this->assertStringNotContainsString('https:;', $policy);
        $this->assertStringNotContainsString("script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: https:", $policy);
    }

    // ── K + L: the protections that must survive all of this ────────────

    public function test_path_traversal_out_of_a_build_is_still_refused(): void
    {
        $this->publish($this->app());

        File::put($this->testingRoot.'/storage/app/secret.txt', 'not yours');

        foreach ([
            '/app-builds/example-slug/../secret.txt',
            '/app-builds/example-slug/..%2Fsecret.txt',
            '/app-builds/example-slug/assets/../../secret.txt',
        ] as $url) {
            $response = $this->get($url);

            $this->assertNotSame(
                200,
                $response->getStatusCode(),
                $url.' must not escape the build folder.'
            );

            if ($response->getStatusCode() === 200) {
                continue;
            }

            $this->assertStringNotContainsString('not yours', $response->getContent());
        }
    }

    public function test_an_executable_entry_blocks_the_whole_publish(): void
    {
        $entries = $this->flutterEntries();
        $entries['tools/shell.php'] = '<?php echo "owned";';

        try {
            app(StudyBuddyWebAppPublisher::class)->publish(
                $this->app(),
                $this->zipUpload($entries)
            );

            $this->fail('A ZIP containing PHP must not publish.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('blocked executable', $exception->getMessage());
        }

        $this->assertDirectoryDoesNotExist(
            StudyBuddyWebAppPublisher::buildDirectory('example-slug')
        );
    }

    // ── M + N + O: the rest of the app lifecycle ────────────────────────

    public function test_deleting_an_app_removes_its_hosted_build(): void
    {
        $app = $this->publish($this->app());
        $directory = StudyBuddyWebAppPublisher::buildDirectory('example-slug');

        $this->assertFileExists($directory.'/index.html');

        $this->actingAs($this->admin())
            ->delete('/admin/control-room/apps/'.$app->id, ['confirm_name' => $app->name])
            ->assertRedirect();

        $this->assertDirectoryDoesNotExist($directory);
        $this->assertDatabaseMissing('studybuddy_mini_app_platforms', ['id' => $app->id]);
    }

    public function test_unpublishing_hides_the_build_without_deleting_it(): void
    {
        $app = $this->publish($this->app());

        $this->actingAs($this->admin())
            ->patch('/admin/control-room/apps/'.$app->id.'/publish')
            ->assertRedirect();

        $this->assertFalse($app->refresh()->is_active);

        // Public addresses stop resolving...
        $this->get('/app-builds/example-slug/index.html')->assertNotFound();
        $this->get('/play/example-slug')->assertNotFound();

        // ...but the files are still there for republishing.
        $this->assertFileExists(
            StudyBuddyWebAppPublisher::buildDirectory('example-slug').'/index.html'
        );

        $this->actingAs($this->admin())
            ->patch('/admin/control-room/apps/'.$app->id.'/publish')
            ->assertRedirect();

        $this->assertTrue($app->refresh()->is_active);
        $this->get('/app-builds/example-slug/index.html')->assertOk();
    }

    public function test_a_draft_browser_preview_stays_admin_only(): void
    {
        $app = $this->publish($this->app(['is_active' => false]));

        $this->get('/admin/control-room/apps/'.$app->id.'/preview/build/index.html')
            ->assertRedirect();

        $this->get('/admin/control-room/apps/'.$app->id.'/preview/play')
            ->assertRedirect();

        $this->get('/app-builds/example-slug/index.html')->assertNotFound();

        $this->actingAs($this->admin())
            ->get('/admin/control-room/apps/'.$app->id.'/preview/build/index.html')
            ->assertOk();
    }

    // ── The launcher must not claim more than it knows ───────────────────

    public function test_the_launcher_does_not_announce_a_running_app_on_frame_load(): void
    {
        $this->publish($this->app());

        $this->get('/play/example-slug')
            ->assertOk()
            ->assertSee('Loading app…', false)
            ->assertSee("We couldn't start this activity.", false)
            ->assertDontSee('App running');

        $launcher = File::get($this->originalPublicPath.'/assets/js/studybuddy-launcher-v3.js');

        $this->assertStringNotContainsString("setState('App running'", $launcher);
        $this->assertStringContainsString('studybuddy:app-ready', $launcher);
    }

    public function test_a_published_build_carries_the_readiness_bridge(): void
    {
        $this->publish($this->app());

        $index = $this->publishedIndex('example-slug');

        $this->assertStringContainsString('studybuddy:app-ready', $index);
        $this->assertStringContainsString('flutter-first-frame', $index);

        // The build's own bundles are never rewritten.
        $this->assertSame(
            '(function(){ /* dart2js output */ })();',
            File::get(StudyBuddyWebAppPublisher::buildDirectory('example-slug').'/main.dart.js')
        );
    }
}
