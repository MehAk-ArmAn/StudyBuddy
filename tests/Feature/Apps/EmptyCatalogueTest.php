<?php

namespace Tests\Feature\Apps;

use App\Models\StudyBuddyMiniAppPlatform;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * StudyBuddy ships with no apps on purpose: the old catalogue was development
 * fixtures, and the real apps get added through the Apps CMS.
 *
 * Starting empty must look deliberate, not broken.
 */
class EmptyCatalogueTest extends TestCase
{
    use RefreshDatabase;

    /** Names from the retired demo catalogue that must never come back. */
    private const DEMO_NAMES = [
        'Math Quest',
        'Spelling Sprint',
        'Reading Garden',
        'Focus Forest',
        'Planner City',
        'Quiz Galaxy',
        'Shapes Lab',
        'Flashcard Castle',
    ];

    public function test_migrations_leave_no_apps_behind(): void
    {
        $this->assertSame(0, StudyBuddyMiniAppPlatform::count());
    }

    public function test_the_legacy_catalogue_table_is_empty_too(): void
    {
        $this->assertSame(0, \DB::table('studybuddy_app_catalog_items')->count());
    }

    public function test_seeding_does_not_recreate_the_demo_apps(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->assertSame(0, StudyBuddyMiniAppPlatform::count());
        $this->assertSame(0, \DB::table('studybuddy_app_catalog_items')->count());
    }

    public function test_the_apps_page_shows_an_intentional_empty_state(): void
    {
        $response = $this->get('/apps');

        // The page says the shelf is empty once, then offers somewhere to go.
        // It must not stack a second "nothing matched your filters" panel on
        // top of that — there are no filters to have missed.
        $response->assertOk()
            ->assertSee('No apps here yet.')
            ->assertSee('Have a look around')
            ->assertSee('How roles work');

        foreach (self::DEMO_NAMES as $name) {
            $response->assertDontSee($name);
        }
    }

    /**
     * Filters over an empty catalogue are just noise, so they are not rendered.
     */
    public function test_the_apps_page_hides_its_filters_when_there_is_nothing_to_filter(): void
    {
        $this->get('/apps')->assertDontSee('Search by app, category, or skill');
    }

    public function test_the_apps_page_does_not_advertise_the_admin_panel(): void
    {
        $this->get('/apps')
            ->assertDontSee('Admin Panel')
            ->assertDontSee('managed directly from');
    }

    public function test_the_homepage_works_and_shows_no_app_cards(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $response = $this->get('/');
        $response->assertOk();

        foreach (self::DEMO_NAMES as $name) {
            $response->assertDontSee($name);
        }
    }

    public function test_a_published_app_appears_on_the_homepage_strip(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        StudyBuddyMiniAppPlatform::create([
            'slug' => 'colour-lab',
            'name' => 'Colour Lab',
            'category' => 'Colours',
            'status' => 'live',
            'tagline' => 'Mix two, guess the third.',
            'points_reward' => 10,
            'estimated_minutes' => 5,
            'is_active' => true,
        ]);

        $this->get('/')->assertOk()->assertSee('Colour Lab');
        $this->get('/apps')->assertOk()->assertSee('Colour Lab');
    }

    /**
     * Regression: the footer used to hard-code links to the demo apps, so it
     * pointed at pages that no longer exist.
     */
    public function test_the_footer_does_not_link_to_apps_that_do_not_exist(): void
    {
        $response = $this->get('/');

        foreach (['/apps/math-quest', '/apps/spelling-sprint', '/apps/quiz-galaxy'] as $url) {
            $response->assertDontSee($url);
        }
    }

    public function test_public_pages_do_not_leak_cms_instructions(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        foreach (['/', '/apps', '/for-parents', '/for-teachers', '/support', '/privacy-policy', '/data-deletion'] as $path) {
            $response = $this->get($path);
            $response->assertOk();

            foreach ([
                'editable from admin',
                'can be changed in admin',
                'Replace this starter content',
                'admin-managed content system',
                'What you can edit',
                'Editable policy content',
            ] as $leak) {
                $response->assertDontSee($leak, false);
            }
        }
    }

    public function test_no_invented_review_scores_are_published(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->assertSame(
            0,
            \DB::table('page_section_items')->whereIn('badge_text', ['4.6', '4.7', '4.8', '4.9', '5.0'])->count()
        );
    }
}
