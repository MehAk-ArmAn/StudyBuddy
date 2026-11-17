<?php

namespace Tests\Feature\Apps;

use App\Models\StudyBuddyMiniAppPlatform;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyBuddyLiveCatalogueTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The homepage also has editorial worlds and optional legacy CMS snippets.
     * Do not mistake those for entries in the canonical LIVE app shelf.
     */
    private function liveShelf(string $html): string
    {
        // The shelf contains no nested <section>, so capture just this one
        // server-rendered Blade component without parsing the whole page.
        $found = preg_match(
            '~<section\b[^>]*\bdata-studybuddy-live-catalogue\b[^>]*>(.*?)</section>~is',
            $html,
            $matches,
            PREG_UNMATCHED_AS_NULL
        );
        $this->assertSame(1, $found, 'The single live catalogue shelf must render on the homepage.');
        return $matches[1] ?? '';
    }

    public function test_homepage_counts_actual_active_apps_not_demo_concepts(): void
    {
        StudyBuddyMiniAppPlatform::query()->create([
            'slug' => 'live-counting-quest', 'name' => 'Live Counting Quest',
            'category' => 'Math', 'status' => 'beta', 'is_active' => true,
            'is_download_enabled' => false, 'is_web_enabled' => false,
            'age_min' => 8, 'age_max' => 12,
        ]);
        StudyBuddyMiniAppPlatform::query()->create([
            'slug' => 'hidden-game', 'name' => 'Hidden Counting Game',
            'category' => 'Math', 'status' => 'beta', 'is_active' => false,
        ]);
        StudyBuddyMiniAppPlatform::query()->create([
            'slug' => 'demo-next-planet', 'name' => 'Demo Next Planet',
            'category' => 'Science', 'status' => 'beta', 'is_active' => true,
        ]);

        $response = $this->get('/')->assertOk()
            ->assertSee('data-live-app-count="1"', false)
            ->assertSee('8–12')
            ->assertSee('Learning subjects');

        // A demo may appear in a labelled *editorial preview* elsewhere on the
        // homepage. It must never be counted or listed as a real CMS app here.
        $shelf = $this->liveShelf($response->getContent());
        $this->assertStringContainsString('Live Counting Quest', $shelf);
        $this->assertStringNotContainsString('Hidden Counting Game', $shelf);
        $this->assertStringNotContainsString('Demo Next Planet', $shelf);
    }

    public function test_homepage_has_truthful_empty_state(): void
    {
        $response = $this->get('/')->assertOk()
            ->assertSee('data-live-app-count="0"', false);
        $this->assertStringContainsString(
            'No published learning apps just yet.',
            $this->liveShelf($response->getContent())
        );
    }
}
