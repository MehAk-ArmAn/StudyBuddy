<?php

namespace Tests\Feature\Brand;

use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Visitors should never read anything about how StudyBuddy was built.
 *
 * This renders every public page and fails if developer, CMS or placeholder
 * language reappears — including after a fresh seed.
 */
class PublicCopyTest extends TestCase
{
    use RefreshDatabase;

    /** Public pages a visitor can reach without signing in. */
    private const PUBLIC_ROUTES = [
        '/', '/apps', '/for-parents', '/for-teachers', '/about-us', '/about',
        '/support', '/privacy-policy', '/data-deletion', '/terms', '/cookies',
        '/disclaimer', '/contact', '/login', '/register', '/roles', '/search',
        '/community', '/community-guidelines', '/copyright', '/learning-hub',
        '/learning-paths', '/rewards', '/parents-center', '/teacher-studio',
        '/safety-support',
    ];

    /** Pages signed-in learners use as part of the public product experience. */
    private const LEARNER_ROUTES = [
        '/dashboard', '/command-center', '/verification-center', '/my-quest',
        '/points-wallet', '/profile',
    ];

    /** Wording that belongs in the admin or the codebase, never on the site. */
    private const DEVELOPER_LANGUAGE = [
        'Admin Panel',
        'admin',
        'CMS',
        'admin-managed',
        'editable from admin',
        'editable in admin',
        'can be changed in admin',
        'managed directly from',
        'Editable policy content',
        'What you can edit',
        'content system',
        'database-driven',
        'app package',
        'ZIP package',
        'Laravel',
        'Blade template',
        'seeder',
        'migration',
        'route error',
        'development',
        'demo apps',
        'fake apps',
        'old test',
        'removed while testing',
        'Phase 4',
        'single source for every',
        'real KYC',
        'liveness provider',
    ];

    /** Text that means the page was never finished. */
    private const PLACEHOLDER_LANGUAGE = [
        'Replace this',
        'starter content',
        'Lorem ipsum',
        'lorem ipsum',
        'placeholder',
        'Placeholder',
        'TODO',
        'TBD',
        'FIXME',
        'Add your email',
        'your@email',
        'Insert link',
        'Update this in admin',
        'Sample content',
        'dummy',
        'still being written',
        'still being built',
        'features planned',
        'tools planned',
        'future dashboards',
        'product direction',
        'tested and finished',
        'being prepared',
    ];

    /** Numbers or claims we have no data to back up. */
    private const FAKE_SOCIAL_PROOF = [
        'happy learners',
        'active learners',
        'trusted by',
        'Trusted by',
        'award-winning',
        'testimonial',
        'download count',
        'schools worldwide',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    /** Renders a page and returns only what a visitor can actually read. */
    private function visibleText(string $path): string
    {
        $response = $this->get($path);
        $response->assertOk("{$path} should render successfully.");
        $html = $response->getContent();

        $body = preg_replace('#<(script|style|svg|head|noscript)\b[^>]*>.*?</\1>#is', ' ', $html);
        $body = preg_replace('#<!--.*?-->#s', ' ', $body);

        return preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5));
    }

    public function test_no_public_page_uses_developer_or_cms_language(): void
    {
        foreach (self::PUBLIC_ROUTES as $path) {
            $text = $this->visibleText($path);

            foreach (self::DEVELOPER_LANGUAGE as $phrase) {
                $this->assertStringNotContainsStringIgnoringCase(
                    $phrase,
                    $text,
                    "{$path} exposes developer wording: \"{$phrase}\""
                );
            }
        }
    }

    public function test_no_public_page_contains_placeholder_text(): void
    {
        foreach (self::PUBLIC_ROUTES as $path) {
            $text = $this->visibleText($path);

            foreach (self::PLACEHOLDER_LANGUAGE as $phrase) {
                $this->assertStringNotContainsStringIgnoringCase(
                    $phrase,
                    $text,
                    "{$path} still contains placeholder text: \"{$phrase}\""
                );
            }
        }
    }

    public function test_learner_pages_do_not_expose_internal_or_unfinished_language(): void
    {
        $learner = User::forceCreate([
            'name' => 'Copy Test Learner',
            'email' => 'copy-test-learner@studybuddy.test',
            'email_verified_at' => now(),
            'password' => bcrypt('secret-password'),
            'role' => 'student',
            'is_admin' => false,
        ]);

        $this->actingAs($learner);

        foreach (self::LEARNER_ROUTES as $path) {
            $text = $this->visibleText($path);

            foreach (array_merge(self::DEVELOPER_LANGUAGE, self::PLACEHOLDER_LANGUAGE) as $phrase) {
                $this->assertStringNotContainsStringIgnoringCase(
                    $phrase,
                    $text,
                    "{$path} exposes internal or unfinished wording: \"{$phrase}\""
                );
            }
        }
    }

    public function test_no_public_page_invents_social_proof(): void
    {
        foreach (self::PUBLIC_ROUTES as $path) {
            $text = $this->visibleText($path);

            foreach (self::FAKE_SOCIAL_PROOF as $phrase) {
                $this->assertStringNotContainsStringIgnoringCase(
                    $phrase,
                    $text,
                    "{$path} makes an unbacked claim: \"{$phrase}\""
                );
            }
        }
    }

    public function test_a_published_app_catalogue_and_detail_page_keep_product_copy_clean(): void
    {
        $app = StudyBuddyMiniAppPlatform::create([
            'slug' => 'reading-orbit',
            'name' => 'Reading Orbit',
            'category' => 'Reading',
            'tagline' => 'Build comprehension one short story at a time.',
            'description' => 'A calm reading experience with focused questions and clear progress.',
            'long_description' => 'Learners read a short passage, think through each question, and finish with a clear sense of what they understood.',
            'preview_text' => 'Choose a passage, complete one focused round, and review the answers together.',
            'status' => 'live',
            'icon' => '📖',
            'android_url' => 'https://play.google.com/store/apps/details?id=fun.studybuddy.readingorbit',
            'android_package_id' => 'fun.studybuddy.readingorbit',
            'points_reward' => 15,
            'estimated_minutes' => 10,
            'age_min' => 8,
            'age_max' => 12,
            'audience_roles' => ['student', 'parent', 'teacher'],
            'learning_tags' => ['reading', 'comprehension'],
            'learning_outcomes' => ['Recall key details', 'Explain the main idea'],
            'is_web_enabled' => false,
            'is_download_enabled' => true,
            'is_active' => true,
        ]);

        foreach (['/apps', '/apps/'.$app->slug] as $path) {
            $text = $this->visibleText($path);

            foreach (array_merge(
                self::DEVELOPER_LANGUAGE,
                self::PLACEHOLDER_LANGUAGE,
                self::FAKE_SOCIAL_PROOF
            ) as $phrase) {
                $this->assertStringNotContainsStringIgnoringCase(
                    $phrase,
                    $text,
                    "{$path} exposes unsuitable app-page wording: \"{$phrase}\""
                );
            }
        }

        $this->assertStringContainsString('Reading Orbit', $this->visibleText('/apps'));
        $this->assertStringContainsString('Google Play', $this->visibleText('/apps/'.$app->slug));
    }

    public function test_the_legal_pages_carry_real_content(): void
    {
        $privacy = $this->visibleText('/privacy-policy');

        $this->assertStringContainsString('What we collect', $privacy);
        $this->assertStringContainsString('Why we collect it', $privacy);
        $this->assertStringContainsString('date of birth', $privacy);
        $this->assertStringContainsString('guardian or child email connections', $privacy);
        $this->assertStringContainsString('Verification information', $privacy);
        $this->assertStringContainsString('internet protocol (IP) address', $privacy);
        $this->assertGreaterThan(400, strlen($privacy), 'The privacy page is too thin to be useful.');

        $deletion = $this->visibleText('/data-deletion');

        $this->assertStringContainsString('Send us the request', $deletion);
        $this->assertGreaterThan(200, strlen($deletion));
    }

    /**
     * The build checklist and platform status are internal, not visitor content.
     */
    public function test_internal_release_pages_are_not_publicly_readable(): void
    {
        $this->get('/platform-roadmap')->assertRedirect();
        $this->get('/launch-readiness')->assertRedirect();
    }

    public function test_error_pages_are_branded_and_not_technical(): void
    {
        $response = $this->get('/a-page-that-does-not-exist');

        $response->assertNotFound();

        $html = $response->getContent();

        $this->assertStringContainsString('rel="icon"', $html);
        $this->assertStringContainsString('StudyBuddy', $html);
        $this->assertStringNotContainsString('Stack trace', $html);
        $this->assertStringNotContainsString('vendor/laravel', $html);
    }

    public function test_every_public_page_has_its_own_title(): void
    {
        foreach (['/apps', '/login', '/register', '/about-us', '/support', '/privacy-policy'] as $path) {
            preg_match('/<title>(.*?)<\/title>/s', $this->get($path)->getContent(), $m);
            $title = trim($m[1] ?? '');

            $this->assertNotSame('', $title, "{$path} has no title.");
            $this->assertStringNotContainsString('Untitled', $title);
            $this->assertStringContainsString('StudyBuddy', $title);
        }
    }

    public function test_meaningful_images_carry_alt_text(): void
    {
        foreach (['/', '/apps', '/about-us'] as $path) {
            preg_match_all('/<img[^>]*>/i', $this->get($path)->getContent(), $matches);

            foreach ($matches[0] as $tag) {
                $this->assertMatchesRegularExpression(
                    '/\salt="/i',
                    $tag,
                    "An image on {$path} has no alt attribute: {$tag}"
                );

                // Unhelpful alt text is worse than none.
                $this->assertDoesNotMatchRegularExpression(
                    '/\salt="(image|photo|picture|img)"/i',
                    $tag,
                    "An image on {$path} has meaningless alt text: {$tag}"
                );
            }
        }
    }

    /**
     * The hero is the largest paint on the homepage; lazy-loading it would
     * delay the very thing a visitor is waiting for.
     */
    public function test_the_homepage_hero_image_is_not_lazy_loaded(): void
    {
        preg_match('/<img class="hero-art"[^>]*>/', $this->get('/')->getContent(), $m);

        $this->assertNotEmpty($m, 'The homepage hero image is missing.');
        $this->assertStringNotContainsString('loading="lazy"', $m[0]);
        $this->assertStringContainsString('fetchpriority="high"', $m[0]);
    }
}
