<?php

namespace Tests\Unit;

use App\Models\StudyBuddyMiniAppPlatform;
use PHPUnit\Framework\TestCase;

/**
 * Pure logic on the app model — no database needed.
 */
class StudyBuddyMiniAppPlatformTest extends TestCase
{
    /**
     * Regression: safeHeroImage() only read `hero_image`, but the artwork for
     * every existing app lives in `image_url`. That made every public app card
     * fall through to the same generic placeholder.
     */
    public function test_hero_image_falls_back_to_the_card_image(): void
    {
        $app = new StudyBuddyMiniAppPlatform(['image_url' => '/assets/apps/math.png']);

        $this->assertSame('/assets/apps/math.png', $app->safeHeroImage());
    }

    public function test_hero_image_wins_when_both_are_set(): void
    {
        $app = new StudyBuddyMiniAppPlatform([
            'hero_image' => '/assets/apps/hero.png',
            'image_url' => '/assets/apps/card.png',
        ]);

        $this->assertSame('/assets/apps/hero.png', $app->safeHeroImage());
    }

    public function test_hero_image_is_null_when_no_artwork_is_set(): void
    {
        $this->assertNull((new StudyBuddyMiniAppPlatform())->safeHeroImage());
    }

    public function test_accent_colours_are_stable_for_the_same_app(): void
    {
        $first = (new StudyBuddyMiniAppPlatform(['slug' => 'math-quest']))->accentColors();
        $second = (new StudyBuddyMiniAppPlatform(['slug' => 'math-quest']))->accentColors();

        $this->assertSame($first, $second);
        $this->assertCount(2, $first);
        $this->assertMatchesRegularExpression('/^#[0-9a-f]{6}$/i', $first[0]);
    }

    public function test_different_apps_can_get_different_accents(): void
    {
        $slugs = ['math-quest', 'spelling-sprint', 'reading-garden', 'focus-forest', 'quiz-galaxy'];

        $palettes = array_map(
            fn (string $slug): string => implode('', (new StudyBuddyMiniAppPlatform(['slug' => $slug]))->accentColors()),
            $slugs
        );

        $this->assertGreaterThan(1, count(array_unique($palettes)));
    }

    public function test_initials_use_the_first_two_words(): void
    {
        $this->assertSame('FF', (new StudyBuddyMiniAppPlatform(['name' => 'Flag Frenzy']))->initials());
        $this->assertSame('M', (new StudyBuddyMiniAppPlatform(['name' => 'Maths']))->initials());
        $this->assertSame('SB', (new StudyBuddyMiniAppPlatform())->initials());
    }

    public function test_availability_labels_are_human_readable(): void
    {
        $this->assertSame('Launching soon', (new StudyBuddyMiniAppPlatform(['status' => 'live']))->availabilityLabel());
        $this->assertSame('Available now', (new StudyBuddyMiniAppPlatform([
            'status' => 'live',
            'android_url' => 'https://play.google.com/store/apps/details?id=fun.studybuddy.maths',
            'is_download_enabled' => true,
        ]))->availabilityLabel());
        $this->assertSame('In testing', (new StudyBuddyMiniAppPlatform(['status' => 'beta']))->availabilityLabel());
        $this->assertSame('On the way', (new StudyBuddyMiniAppPlatform(['status' => 'planned']))->availabilityLabel());
    }

    public function test_role_visibility_defaults_to_everyone(): void
    {
        $app = new StudyBuddyMiniAppPlatform();

        $this->assertTrue($app->visibleForRole('student'));
        $this->assertTrue($app->visibleForRole('teacher'));
        $this->assertTrue($app->visibleForRole(null));
    }

    public function test_role_visibility_respects_an_explicit_audience(): void
    {
        $app = new StudyBuddyMiniAppPlatform(['audience_roles' => ['teacher']]);

        $this->assertTrue($app->visibleForRole('teacher'));
        $this->assertFalse($app->visibleForRole('student'));
    }
}
