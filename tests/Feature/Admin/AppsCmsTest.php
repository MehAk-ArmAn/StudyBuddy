<?php

namespace Tests\Feature\Admin;

use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\User;
use App\Services\StudyBuddyWebAppPublisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use ZipArchive;

/**
 * Covers the workflow the admin actually cares about: adding a brand-new
 * learning app and getting it onto the public site.
 */
class AppsCmsTest extends TestCase
{
    use RefreshDatabase;

    private ?User $admin = null;

    /** Keep every test on the intentionally empty production baseline. */
    protected function setUp(): void
    {
        parent::setUp();

        StudyBuddyMiniAppPlatform::query()->delete();
    }

    /** Memoised so repeated calls inside one test reuse the same account. */
    private function admin(): User
    {
        return $this->admin ??= User::forceCreate([
            'name' => 'Admin',
            'email' => 'admin@studybuddy.test',
            'password' => bcrypt('secret-password'),
            'is_admin' => true,
            'role' => 'admin',
        ]);
    }

    private function learner(): User
    {
        return User::forceCreate([
            'name' => 'Learner',
            'email' => 'learner@studybuddy.test',
            'password' => bcrypt('secret-password'),
            'is_admin' => false,
            'role' => 'student',
        ]);
    }

    /** @return array<string, mixed> */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Flag Frenzy',
            'category' => 'Geography',
            'status' => 'live',
            'tagline' => 'Four almost-identical flags. Good luck.',
            'description' => 'Spot the right flag before the timer runs out.',
            'points_reward' => 30,
            'estimated_minutes' => 8,
            'is_active' => '1',
        ], $overrides);
    }

    // ── Access control ──────────────────────────────────────────────────

    public function test_guests_cannot_reach_the_apps_cms(): void
    {
        $this->get('/admin/control-room/apps')->assertRedirect();
        $this->post('/admin/control-room/apps', $this->validPayload())->assertRedirect();
    }

    public function test_signed_in_non_admins_cannot_reach_the_apps_cms(): void
    {
        $this->actingAs($this->learner())
            ->get('/admin/control-room/apps')
            ->assertRedirect();

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 0);
    }

    public function test_a_non_admin_cannot_create_an_app(): void
    {
        $this->actingAs($this->learner())
            ->post('/admin/control-room/apps', $this->validPayload())
            ->assertRedirect();

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 0);
    }

    // ── The core flow ───────────────────────────────────────────────────

    public function test_an_admin_can_open_the_list_and_the_create_form(): void
    {
        $this->actingAs($this->admin())->get('/admin/control-room/apps')->assertOk();
        $this->actingAs($this->admin())->get('/admin/control-room/apps/create')->assertOk();
    }

    public function test_an_admin_can_create_an_app_and_the_slug_is_generated(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload())
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('studybuddy_mini_app_platforms', [
            'slug' => 'flag-frenzy',
            'name' => 'Flag Frenzy',
            'category' => 'Geography',
            'is_active' => true,
        ]);
    }

    public function test_a_published_app_reaches_the_public_pages(): void
    {
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload());

        $this->get('/apps')->assertOk()->assertSee('Flag Frenzy');
        $this->get('/apps/flag-frenzy')->assertOk()->assertSee('Flag Frenzy');
    }

    public function test_an_unpublished_app_is_hidden_from_the_public_pages(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload(['is_active' => null]));

        $this->get('/apps')->assertOk()->assertDontSee('Flag Frenzy');
        $this->get('/apps/flag-frenzy')->assertNotFound();
    }

    public function test_an_admin_can_edit_an_app_and_the_change_reaches_the_public_page(): void
    {
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload());
        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->actingAs($this->admin())
            ->put("/admin/control-room/apps/{$app->id}", $this->validPayload([
                'slug' => 'flag-frenzy',
                'name' => 'Flag Dash',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame('Flag Dash', $app->fresh()->name);
        $this->get('/apps')->assertSee('Flag Dash');
    }

    public function test_publish_toggle_flips_public_visibility(): void
    {
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload());
        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->actingAs($this->admin())->patch("/admin/control-room/apps/{$app->id}/publish");
        $this->assertFalse($app->fresh()->is_active);
        $this->get('/apps')->assertDontSee('Flag Frenzy');

        $this->actingAs($this->admin())->patch("/admin/control-room/apps/{$app->id}/publish");
        $this->assertTrue($app->fresh()->is_active);
        $this->get('/apps')->assertSee('Flag Frenzy');
    }

    public function test_save_actions_are_authoritative_for_draft_publish_and_unpublish(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'save_action' => 'draft',
                // The clicked action must win over a stale checked box.
                'is_active' => '1',
            ]))
            ->assertSessionHasNoErrors();

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->assertFalse($app->is_active);
        $this->get('/apps/flag-frenzy')->assertNotFound();

        $this->actingAs($this->admin())
            ->put("/admin/control-room/apps/{$app->id}", $this->validPayload([
                'slug' => 'flag-frenzy',
                'save_action' => 'publish',
                'is_active' => null,
            ]))
            ->assertSessionHasNoErrors();

        $this->assertTrue($app->fresh()->is_active);
        $this->get('/apps/flag-frenzy')->assertOk()->assertSee('Flag Frenzy');

        $this->actingAs($this->admin())
            ->put("/admin/control-room/apps/{$app->id}", $this->validPayload([
                'slug' => 'flag-frenzy',
                'save_action' => 'draft',
                'is_active' => '1',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertFalse($app->fresh()->is_active);
        $this->get('/apps')->assertDontSee('Flag Frenzy');
        $this->get('/apps/flag-frenzy')->assertNotFound();
    }

    public function test_an_admin_can_preview_a_draft_page_and_browser_launch_while_guests_cannot(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'save_action' => 'draft',
                'is_web_enabled' => '1',
                'web_play_url' => 'https://games.example.test/flag-frenzy/',
            ]))
            ->assertSessionHasNoErrors();

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->assertFalse($app->is_active);

        $this->actingAs($this->admin())
            ->get(route('admin.control-room.apps.preview', $app))
            ->assertOk()
            ->assertSee('Private preview')
            ->assertSee('Flag Frenzy')
            ->assertSee(route('admin.control-room.apps.preview.play', $app), false);

        $this->actingAs($this->admin())
            ->get(route('admin.control-room.apps.preview.play', $app))
            ->assertOk()
            ->assertSee('Private browser test')
            ->assertSee('https://games.example.test/flag-frenzy/', false);

        auth()->logout();

        $this->get(route('admin.control-room.apps.preview', $app))->assertRedirect();
        $this->get(route('admin.control-room.apps.preview.play', $app))->assertRedirect();
        $this->get('/apps/flag-frenzy')->assertNotFound();
        $this->get('/play/flag-frenzy')->assertNotFound();
    }

    public function test_legacy_final_platform_update_routes_do_not_mutate_apps(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'android_url' => 'https://play.google.com/store/apps/details?id=com.studybuddy.flags',
            ]));

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');
        $originalAttributes = $app->getAttributes();

        foreach ([
            'admin.control-room.final.apps.update',
            'studybuddy.admin.final.apps.update',
        ] as $routeName) {
            $this->actingAs($this->admin())
                ->patch(route($routeName, $app), [
                    'name' => 'Mutated outside the CMS',
                    'status' => 'paused',
                    'is_active' => false,
                    'android_url' => 'https://example.test/not-google-play',
                    'android_package_id' => 'com.example.mutated',
                ])
                ->assertRedirect(route('admin.control-room.apps.edit', $app));

            $this->assertSame($originalAttributes, $app->fresh()->getAttributes());
        }
    }

    public function test_reorder_saves_new_positions(): void
    {
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload());
        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps/reorder', ['order' => [$app->id => 7]])
            ->assertSessionHasNoErrors();

        $this->assertSame(7, $app->fresh()->sort_order);
    }

    // ── Validation ──────────────────────────────────────────────────────

    public function test_creating_an_app_without_a_name_fails_validation(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload(['name' => '']))
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 0);
    }

    public function test_a_duplicate_slug_is_rejected(): void
    {
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload());

        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload(['name' => 'Different name', 'slug' => 'flag-frenzy']))
            ->assertSessionHasErrors('slug');

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 1);
    }

    public function test_an_inverted_age_range_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload(['age_min' => 14, 'age_max' => 7]))
            ->assertSessionHasErrors('age_max');
    }

    public function test_a_store_link_that_is_not_a_url_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload(['android_url' => 'play-store-please']))
            ->assertSessionHasErrors('android_url');
    }

    public function test_google_play_url_fills_an_empty_android_package_id(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'android_url' => 'https://play.google.com/store/apps/details?hl=en&id=com.studybuddy.flags&gl=AE',
                'android_package_id' => '',
                'save_action' => 'draft',
            ]))
            ->assertSessionHasNoErrors();

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->assertSame('com.studybuddy.flags', $app->android_package_id);
        $this->assertSame(
            'https://play.google.com/store/apps/details?hl=en&id=com.studybuddy.flags&gl=AE',
            $app->android_url
        );
    }

    /** @return array<string, array{string}> */
    public static function invalidGooglePlayUrls(): array
    {
        return [
            'malformed' => ['play-store-please'],
            'different host' => ['https://example.test/store/apps/details?id=com.studybuddy.flags'],
            'insecure http' => ['http://play.google.com/store/apps/details?id=com.studybuddy.flags'],
            'missing id' => ['https://play.google.com/store/apps/details?hl=en'],
        ];
    }

    #[DataProvider('invalidGooglePlayUrls')]
    public function test_invalid_google_play_listing_urls_are_rejected(string $url): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'android_url' => $url,
                'android_package_id' => '',
            ]))
            ->assertSessionHasErrors('android_url');

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 0);
    }

    public function test_a_malformed_package_id_from_google_play_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'android_url' => 'https://play.google.com/store/apps/details?id=not-a-package',
                'android_package_id' => '',
            ]))
            ->assertSessionHasErrors('android_package_id');

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 0);
    }

    public function test_an_explicit_package_id_must_match_the_google_play_url(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'android_url' => 'https://play.google.com/store/apps/details?id=com.studybuddy.flags',
                'android_package_id' => 'com.studybuddy.different',
            ]))
            ->assertSessionHasErrors('android_package_id');

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 0);
    }

    public function test_android_package_ids_cannot_be_reused_by_another_app(): void
    {
        $playUrl = 'https://play.google.com/store/apps/details?id=com.studybuddy.flags';

        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'android_url' => $playUrl,
                'android_package_id' => '',
            ]))
            ->assertSessionHasNoErrors();

        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'name' => 'Shape Sorter',
                'android_url' => $playUrl,
                'android_package_id' => '',
            ]))
            ->assertSessionHasErrors('android_package_id');

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 1);
    }

    public function test_clearing_both_age_limits_restores_the_all_ages_value(): void
    {
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload([
            'age_min' => 7,
            'age_max' => 14,
        ]));

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->actingAs($this->admin())
            ->put("/admin/control-room/apps/{$app->id}", $this->validPayload([
                'slug' => 'flag-frenzy',
                'age_min' => null,
                'age_max' => null,
            ]))
            ->assertSessionHasNoErrors();

        $app->refresh();

        $this->assertNull($app->age_min);
        $this->assertNull($app->age_max);
        $this->assertSame('All ages', $app->age_range);
    }

    public function test_a_maximum_only_age_limit_is_saved_and_rendered_clearly(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'age_min' => null,
                'age_max' => 10,
                'save_action' => 'publish',
            ]))
            ->assertSessionHasNoErrors();

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->assertNull($app->age_min);
        $this->assertSame(10, $app->age_max);
        $this->assertSame('Up to 10', $app->age_range);

        $this->get('/apps')->assertOk()->assertSee('Up to 10');
        $this->get('/apps/flag-frenzy')->assertOk()->assertSee('Up to 10');
    }

    /**
     * Regression: an app used to be creatable with no audience at all, which
     * hid it from every role filter on the public Apps page.
     */
    public function test_an_app_created_without_roles_is_visible_to_every_audience(): void
    {
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload());

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->assertEqualsCanonicalizing(
            ['student', 'parent', 'teacher', 'independent_learner'],
            $app->audience_roles
        );

        foreach (['student', 'parent', 'teacher', 'independent_learner'] as $role) {
            $this->get('/apps?role='.$role)->assertSee('Flag Frenzy');
        }
    }

    /**
     * Regression: `long_description`, `image_url` and `age_range` were missing
     * from the model's $fillable, so the form silently discarded them.
     */
    public function test_long_description_image_and_age_range_are_actually_saved(): void
    {
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload([
            'long_description' => 'Sixty flags, four choices, one timer.',
            'image_url' => '/assets/studybuddy-imgs/apps/flag-frenzy.png',
            'age_min' => 7,
            'age_max' => 14,
        ]));

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->assertSame('Sixty flags, four choices, one timer.', $app->long_description);
        $this->assertSame('/assets/studybuddy-imgs/apps/flag-frenzy.png', $app->image_url);
        $this->assertSame('7-14', $app->age_range);
    }

    public function test_tags_and_outcomes_are_split_into_lists(): void
    {
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload([
            'learning_tags_text' => 'flags, capitals ,continents',
            'learning_outcomes_text' => "Recognise 60 flags\n\nMatch flags to continents",
        ]));

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->assertSame(['flags', 'capitals', 'continents'], $app->learning_tags);
        $this->assertSame(['Recognise 60 flags', 'Match flags to continents'], $app->learning_outcomes);
    }

    // ── Media ───────────────────────────────────────────────────────────

    public function test_card_and_detail_artwork_use_separate_helpers_with_safe_fallbacks(): void
    {
        $card = 'https://cdn.example.test/flag-card.png';
        $cover = 'https://cdn.example.test/flag-cover.png';
        $app = StudyBuddyMiniAppPlatform::create($this->validPayload([
            'slug' => 'flag-frenzy', 'image_url' => $card, 'hero_image' => $cover,
        ]));

        $this->assertSame($card, $app->cardImage());
        $this->assertSame($cover, $app->detailImage());
        $this->get('/apps')->assertOk()->assertSee($card, false)->assertDontSee($cover, false);
        $this->get('/apps/flag-frenzy')->assertOk()->assertSee($cover, false)->assertDontSee($card, false);

        $app->update(['image_url' => null]);
        $this->assertSame($cover, $app->fresh()->cardImage());
        $app->update(['image_url' => $card, 'hero_image' => null]);
        $this->assertSame($card, $app->fresh()->detailImage());
    }

    public function test_uploaded_artwork_is_stored_as_a_relative_path(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload([
            'image_url_file' => UploadedFile::fake()->image('flag.png', 600, 450),
        ]));

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        // A relative path keeps working when the site moves to another domain.
        $this->assertStringStartsWith('/storage/studybuddy/apps/', $app->image_url);
        $this->assertStringNotContainsString('http://', (string) $app->image_url);

        Storage::disk('public')->assertExists(
            str_replace('/storage/', '', $app->image_url)
        );
    }

    public function test_replacing_artwork_removes_the_previous_upload(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload([
            'image_url_file' => UploadedFile::fake()->image('first.png'),
        ]));

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');
        $first = str_replace('/storage/', '', (string) $app->image_url);

        $this->actingAs($this->admin())->put("/admin/control-room/apps/{$app->id}", $this->validPayload([
            'slug' => 'flag-frenzy',
            'image_url_file' => UploadedFile::fake()->image('second.png'),
        ]));

        Storage::disk('public')->assertMissing($first);
        $this->assertCount(1, Storage::disk('public')->allFiles('studybuddy/apps/flag-frenzy'));
    }

    public function test_a_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post('/admin/control-room/apps', $this->validPayload([
                'image_url_file' => UploadedFile::fake()->create('notes.pdf', 40, 'application/pdf'),
            ]))
            ->assertSessionHasErrors('image_url_file');
    }

    public function test_a_valid_web_build_zip_creates_a_published_app_and_owned_files(): void
    {
        $this->withIsolatedPublisherPaths(function (string $public, string $storage, string $tmp): void {
            $zip = $this->zipUpload($tmp.'/uploads', [
                'index.html' => '<!doctype html><h1>Playable</h1>',
                'assets/app.css' => 'body{color:#123456}',
            ]);

            $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload([
                'web_app_zip' => $zip, 'save_action' => 'publish',
            ]))->assertSessionHasNoErrors();

            $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');
            $this->assertTrue($app->is_active);
            $this->assertTrue($app->is_web_enabled);
            $this->assertSame('/web-apps/flag-frenzy/index.html', $app->web_play_url);
            $this->assertSame('web-apps/flag-frenzy/index.html', $app->web_app_entry_path);
            $this->assertFileExists($storage.'/app/studybuddy-web-apps/flag-frenzy/index.html');
            $this->assertFileExists($storage.'/app/studybuddy-web-apps/flag-frenzy/assets/app.css');
            $this->assertFileDoesNotExist($public.'/web-apps/flag-frenzy/index.html');
            $this->assertFileExists($storage.'/app/'.$app->web_app_package_path);
        });
    }

    public function test_an_invalid_web_build_zip_leaves_no_app_or_owned_files(): void
    {
        $this->withIsolatedPublisherPaths(function (string $public, string $storage, string $tmp): void {
            $zip = $this->zipUpload($tmp.'/uploads', ['readme.txt' => 'No index file.'], 'invalid.zip');

            $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload([
                'image_url_file' => UploadedFile::fake()->image('flag-card.png'),
                'web_app_zip' => $zip,
                'save_action' => 'publish',
            ]))->assertSessionHasErrors('web_app_zip');

            $this->assertDatabaseCount('studybuddy_mini_app_platforms', 0);
            $this->assertFalse(File::exists($public.'/web-apps/flag-frenzy'));
            $this->assertFalse(File::exists($storage.'/app/studybuddy-web-apps/flag-frenzy'));
            $this->assertSame([], File::allFiles($storage.'/app'));
            $this->assertSame([], Storage::disk('public')->allFiles());
            $this->assertFalse(Storage::disk('public')->directoryExists('studybuddy/apps/flag-frenzy'));
        });
    }

    public function test_publisher_refuses_a_symlink_build_target_without_touching_its_destination(): void
    {
        $this->withIsolatedPublisherPaths(function (string $public, string $storage, string $tmp): void {
            $outside = $tmp.'/outside';
            File::ensureDirectoryExists($outside);
            File::put($outside.'/keep.txt', 'keep me');
            File::ensureDirectoryExists(StudyBuddyWebAppPublisher::buildRoot());

            $target = StudyBuddyWebAppPublisher::buildDirectory('flag-frenzy');
            if (! function_exists('symlink') || ! @symlink($outside, $target)) {
                $this->markTestSkipped('Symbolic links are unavailable on this filesystem.');
            }

            $zip = $this->zipUpload($tmp.'/uploads', [
                'index.html' => '<!doctype html><h1>Replacement</h1>',
            ]);
            $app = new StudyBuddyMiniAppPlatform(['slug' => 'flag-frenzy', 'name' => 'Flag Frenzy']);

            try {
                app(StudyBuddyWebAppPublisher::class)->publish($app, $zip);
                $this->fail('Publishing through a symbolic-link target should fail.');
            } catch (\RuntimeException $exception) {
                $this->assertStringContainsString('symbolic link', $exception->getMessage());
            }

            $this->assertTrue(is_link($target));
            $this->assertSame('keep me', File::get($outside.'/keep.txt'));
        });
    }

    public function test_failed_restore_retains_the_last_good_build_backup(): void
    {
        $this->withIsolatedPublisherPaths(function (string $public, string $storage, string $tmp): void {
            $target = StudyBuddyWebAppPublisher::buildDirectory('flag-frenzy');
            File::ensureDirectoryExists($target);
            File::put($target.'/index.html', '<h1>Last good build</h1>');

            $zip = $this->zipUpload($tmp.'/uploads', [
                'index.html' => '<!doctype html><h1>Replacement</h1>',
            ]);
            $app = new StudyBuddyMiniAppPlatform(['slug' => 'flag-frenzy', 'name' => 'Flag Frenzy']);

            $publisher = new class extends StudyBuddyWebAppPublisher
            {
                private int $moves = 0;

                protected function moveDirectory(string $from, string $to): bool
                {
                    $this->moves++;

                    return $this->moves === 1
                        ? parent::moveDirectory($from, $to)
                        : false;
                }
            };

            try {
                $publisher->publish($app, $zip);
                $this->fail('The simulated move failure should abort publishing.');
            } catch (\RuntimeException $exception) {
                $this->assertStringContainsString('private launcher storage', $exception->getMessage());
            }

            $backups = glob(StudyBuddyWebAppPublisher::buildRoot().'/.flag-frenzy-backup-*') ?: [];
            $this->assertCount(1, $backups);
            $this->assertFileExists($backups[0].'/index.html');
            $this->assertStringContainsString('Last good build', File::get($backups[0].'/index.html'));
            $this->assertDirectoryDoesNotExist($target);
        });
    }

    public function test_upgrade_migration_moves_an_existing_managed_public_build_to_private_storage(): void
    {
        $this->withIsolatedPublisherPaths(function (string $public, string $storage): void {
            StudyBuddyMiniAppPlatform::create($this->validPayload([
                'slug' => 'flag-frenzy',
                'is_web_enabled' => true,
                'web_play_url' => '/web-apps/flag-frenzy/index.html',
                'web_app_entry_path' => 'web-apps/flag-frenzy/index.html',
            ]));

            File::ensureDirectoryExists($public.'/web-apps/flag-frenzy');
            File::put($public.'/web-apps/flag-frenzy/index.html', '<h1>Existing build</h1>');

            $migration = require database_path('migrations/2026_08_21_000300_move_web_app_builds_to_private_storage.php');
            $migration->up();

            $this->assertFileDoesNotExist($public.'/web-apps/flag-frenzy/index.html');
            $this->assertFileExists($storage.'/app/studybuddy-web-apps/flag-frenzy/index.html');
        });
    }

    // ── Deletion ────────────────────────────────────────────────────────

    public function test_deleting_requires_the_exact_app_name(): void
    {
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload());
        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->actingAs($this->admin())
            ->delete("/admin/control-room/apps/{$app->id}", ['confirm_name' => 'wrong name'])
            ->assertSessionHasErrors('confirm_name');

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 1);
    }

    public function test_deleting_with_the_right_name_removes_the_app_and_its_files(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload([
            'image_url_file' => UploadedFile::fake()->image('flag.png'),
        ]));

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');
        $stored = str_replace('/storage/', '', (string) $app->image_url);

        $this->actingAs($this->admin())
            ->delete("/admin/control-room/apps/{$app->id}", ['confirm_name' => 'Flag Frenzy'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('studybuddy_mini_app_platforms', 0);
        Storage::disk('public')->assertMissing($stored);
        $this->get('/apps/flag-frenzy')->assertNotFound();
    }

    public function test_deleting_one_app_leaves_the_others_alone(): void
    {
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload());
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload([
            'name' => 'Shape Sorter', 'tagline' => 'Circles, squares and the odd hexagon.',
        ]));

        $app = StudyBuddyMiniAppPlatform::firstWhere('slug', 'flag-frenzy');

        $this->actingAs($this->admin())
            ->delete("/admin/control-room/apps/{$app->id}", ['confirm_name' => 'Flag Frenzy']);

        $this->assertDatabaseHas('studybuddy_mini_app_platforms', ['slug' => 'shape-sorter']);
        $this->get('/apps')->assertOk()->assertSee('Shape Sorter');
    }

    // ── Search & filter ─────────────────────────────────────────────────

    public function test_the_list_can_be_searched_and_filtered(): void
    {
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload());
        $this->actingAs($this->admin())->post('/admin/control-room/apps', $this->validPayload([
            'name' => 'Shape Sorter', 'category' => 'Maths', 'status' => 'planned', 'is_active' => null,
            'tagline' => 'Circles, squares and the odd hexagon.',
            'description' => 'Sort shapes into the right bins.',
        ]));

        // Consume the "app was created" flash first, otherwise it would still
        // be on the page and the "don't see" assertions below would read it
        // instead of the filtered table.
        $this->actingAs($this->admin())->get('/admin/control-room/apps');

        $this->actingAs($this->admin())->get('/admin/control-room/apps?q=flag')
            ->assertSee('Flag Frenzy')->assertDontSee('Shape Sorter');

        $this->actingAs($this->admin())->get('/admin/control-room/apps?visibility=hidden')
            ->assertSee('Shape Sorter')->assertDontSee('Flag Frenzy');

        $this->actingAs($this->admin())->get('/admin/control-room/apps?status=planned')
            ->assertSee('Shape Sorter')->assertDontSee('Flag Frenzy');
    }

    /** @param callable(string, string, string): void $callback */
    private function withIsolatedPublisherPaths(callable $callback): void
    {
        $originalPublic = app()->publicPath();
        $originalStorage = app()->storagePath();
        $tmp = sys_get_temp_dir().'/studybuddy-apps-cms-'.Str::uuid()->toString();
        $public = $tmp.'/public';
        $storage = $tmp.'/storage';
        File::ensureDirectoryExists($public);
        File::ensureDirectoryExists($storage.'/app');
        app()->usePublicPath($public);
        app()->useStoragePath($storage);
        Storage::fake('public');

        try {
            $callback($public, $storage, $tmp);
        } finally {
            Storage::forgetDisk('public');
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
