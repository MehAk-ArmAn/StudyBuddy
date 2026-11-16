<?php

namespace Tests\Feature\Brand;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * StudyBuddy's identity must survive reseeding, template edits and the loss of
 * any third-party image host.
 */
class BrandIdentityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    // ── Identity values ─────────────────────────────────────────────────

    public function test_the_brand_name_and_slogan_are_the_canonical_ones(): void
    {
        $this->assertSame('StudyBuddy', config('studybuddy.brand.name'));
        $this->assertSame('Learn. Play. Grow. Your Way.', config('studybuddy.brand.slogan'));
    }

    public function test_a_fresh_install_seeds_the_correct_brand_identity(): void
    {
        $settings = \App\Models\SiteSetting::pluck('value', 'key');

        $this->assertSame('StudyBuddy', $settings['brand_name']);
        $this->assertSame('Learn. Play. Grow. Your Way.', $settings['brand_slogan']);
        $this->assertSame(config('studybuddy.icons.logo'), $settings['logo_path']);
        $this->assertSame(config('studybuddy.icons.favicon_32'), $settings['favicon_path']);
    }

    public function test_the_homepage_shows_the_brand_name_and_slogan(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('StudyBuddy')
            ->assertSee('Learn. Play. Grow. Your Way.');
    }

    // ── Icons exist on disk ─────────────────────────────────────────────

    /**
     * Identity artwork is kept local on purpose: the site must not lose its
     * favicon because a third-party host is unreachable.
     *
     */
    #[DataProvider('iconKeys')]
    public function test_every_brand_icon_file_ships_with_the_app(string $key): void
    {
        $path = config('studybuddy.icons.'.$key);

        $this->assertNotNull($path, "No path configured for icon '{$key}'.");
        $this->assertFileExists(public_path($path), "Missing brand asset for '{$key}'.");
        $this->assertGreaterThan(0, filesize(public_path($path)));
    }

    public static function iconKeys(): array
    {
        return [
            'logo' => ['logo'],
            'mark' => ['mark'],
            'favicon.ico' => ['favicon_ico'],
            'favicon 16' => ['favicon_16'],
            'favicon 32' => ['favicon_32'],
            'apple touch' => ['apple_touch'],
            'pwa 192' => ['pwa_192'],
            'pwa 512' => ['pwa_512'],
            'maskable 512' => ['maskable_512'],
            'social' => ['social'],
        ];
    }

    public function test_the_pwa_icons_are_the_sizes_they_claim_to_be(): void
    {
        foreach ([
            'favicon_16' => 16,
            'favicon_32' => 32,
            'pwa_192' => 192,
            'pwa_512' => 512,
            'maskable_512' => 512,
            'apple_touch' => 180,
        ] as $key => $expected) {
            [$width, $height] = getimagesize(public_path(config('studybuddy.icons.'.$key)));

            $this->assertSame($expected, $width, "{$key} should be {$expected}px wide.");
            $this->assertSame($expected, $height, "{$key} should be {$expected}px tall.");
        }
    }

    // ── Rendered head ───────────────────────────────────────────────────

    public function test_the_head_declares_a_working_favicon_and_touch_icon(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('rel="icon"', $html);
        $this->assertStringContainsString('favicon.ico', $html);
        $this->assertStringContainsString('rel="apple-touch-icon"', $html);
        $this->assertStringContainsString('rel="manifest"', $html);
    }

    public function test_the_head_carries_social_metadata(): void
    {
        $html = $this->get('/')->getContent();

        foreach (['og:title', 'og:description', 'og:image', 'og:site_name', 'twitter:card'] as $tag) {
            $this->assertStringContainsString($tag, $html, "Missing {$tag}.");
        }
    }

    /**
     * The social image should be the brand mark, not the 16px favicon.
     */
    public function test_the_social_image_is_a_real_brand_visual(): void
    {
        preg_match('/property="og:image" content="([^"]+)"/', $this->get('/')->getContent(), $m);

        $this->assertNotEmpty($m, 'No og:image was rendered.');

        $path = parse_url($m[1], PHP_URL_PATH);
        [$width] = getimagesize(public_path(ltrim($path, '/')));

        $this->assertGreaterThanOrEqual(200, $width, 'The social image is too small to be useful.');
    }

    public function test_page_titles_do_not_repeat_the_brand_name(): void
    {
        foreach (['/apps', '/login', '/register', '/about-us', '/support'] as $path) {
            preg_match('/<title>(.*?)<\/title>/s', $this->get($path)->getContent(), $m);
            $title = trim($m[1] ?? '');

            $this->assertSame(
                1,
                substr_count($title, 'StudyBuddy'),
                "Title for {$path} repeats the brand: {$title}"
            );
        }
    }

    public function test_no_framework_branding_is_exposed(): void
    {
        $html = $this->get('/')->getContent();

        foreach (['Laravel', 'laravel.svg', 'Powered by'] as $needle) {
            $this->assertStringNotContainsString($needle, $html);
        }
    }

    // ── Manifest ────────────────────────────────────────────────────────

    public function test_the_manifest_is_valid_and_branded(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true);

        $this->assertIsArray($manifest, 'manifest.webmanifest is not valid JSON.');
        $this->assertStringStartsWith('StudyBuddy', $manifest['name']);
        $this->assertSame('StudyBuddy', $manifest['short_name']);
        $this->assertSame('/', $manifest['start_url']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertNotEmpty($manifest['description']);
    }

    public function test_every_manifest_icon_actually_exists(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true);

        $this->assertNotEmpty($manifest['icons']);

        foreach ($manifest['icons'] as $icon) {
            $path = public_path(ltrim($icon['src'], '/'));

            $this->assertFileExists($path, "Manifest references a missing icon: {$icon['src']}");

            [$width, $height] = getimagesize($path);
            $this->assertSame($icon['sizes'], "{$width}x{$height}", "Manifest size is wrong for {$icon['src']}.");
        }
    }

    public function test_the_manifest_offers_a_maskable_icon(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true);

        $purposes = array_column($manifest['icons'], 'purpose');

        $this->assertContains('maskable', $purposes);
    }

    // ── Nothing depends on a third-party host ───────────────────────────

    /**
     * Regression: brand and page artwork used to be loaded from
     * raw.githubusercontent.com, so the site's identity depended on GitHub.
     */
    public function test_no_public_page_loads_images_from_a_third_party_host(): void
    {
        foreach (['/', '/apps', '/for-parents', '/for-teachers', '/about-us', '/support', '/login', '/register'] as $path) {
            $html = $this->get($path)->getContent();

            preg_match_all('/<img[^>]+src="([^"]+)"/', $html, $matches);

            foreach ($matches[1] as $src) {
                $this->assertStringNotContainsString(
                    'githubusercontent.com',
                    $src,
                    "{$path} still loads an image from GitHub: {$src}"
                );
            }
        }
    }

    public function test_stored_image_paths_are_all_local(): void
    {
        foreach ([
            ['site_settings', 'value'],
            ['media_assets', 'path'],
            ['pages', 'hero_image_path'],
            ['page_sections', 'image_path'],
            ['page_section_items', 'image_path'],
            ['homepage_sections', 'image_path'],
            ['homepage_section_items', 'image_path'],
        ] as [$table, $column]) {
            foreach (\DB::table($table)->pluck($column) as $value) {
                if ($value && str_contains((string) $value, 'githubusercontent.com')) {
                    $this->fail("{$table}.{$column} still points at GitHub: {$value}");
                }
            }
        }

        $this->assertTrue(true);
    }
}
