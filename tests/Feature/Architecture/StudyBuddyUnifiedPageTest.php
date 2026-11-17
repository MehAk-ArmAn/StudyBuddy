<?php

namespace Tests\Feature\Architecture;

use App\Models\StudyBuddyMiniAppPlatform;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyBuddyUnifiedPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_has_one_laravel_shell_and_no_demo_app_shelf(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<html\b/i', $html));
        $this->assertSame(1, preg_match_all('/<head\b/i', $html));
        $this->assertSame(1, preg_match_all('/<body\b/i', $html));
        $this->assertSame(1, preg_match_all('/<main\b/i', $html));
        $this->assertSame(1, preg_match_all('/<footer\b/i', $html));
        $this->assertStringNotContainsString('Demo concept', $html);
        $this->assertStringContainsString('data-studybuddy-unified-home', $html);
    }

    public function test_real_published_app_comes_from_one_catalogue(): void
    {
        StudyBuddyMiniAppPlatform::query()->create([
            'slug' => 'unified-audit-reading',
            'name' => 'Unified Reading World',
            'category' => 'English',
            'status' => 'live',
            'is_active' => true,
            'is_web_enabled' => false,
            'is_download_enabled' => false,
        ]);
        $this->get('/')->assertOk()->assertSee('Unified Reading World');
    }
}
