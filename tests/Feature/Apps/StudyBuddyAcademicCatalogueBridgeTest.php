<?php

namespace Tests\Feature\Apps;

use App\Models\StudyBuddyMiniAppPlatform;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyAcademicCatalogueBridgeTest extends TestCase
{
    use RefreshDatabase;

    private function app(string $slug, bool $active = true): StudyBuddyMiniAppPlatform
    {
        return StudyBuddyMiniAppPlatform::query()->create([
            'slug' => $slug, 'name' => 'Orbit Maths Quest',
            'category' => 'Game', 'status' => 'beta', 'is_active' => $active,
            'is_web_enabled' => false, 'is_download_enabled' => false,
        ]);
    }

    private function classify(StudyBuddyMiniAppPlatform $app): void
    {
        DB::table('studybuddy_w34_subject_assignments')->insert([
            'app_id' => $app->id, 'primary_subject' => 'mathematics',
            'learning_stage' => 'primary', 'topics' => 'fractions, multiplication',
            'learning_outcome' => 'Practise number facts',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** Only inspect this page's academic bridge, never unrelated global links. */
    private function atlas(string $html): string
    {
        $found = preg_match(
            '~<section\b[^>]*\bclass="[^"]*\bsb-subject-bridge\b[^"]*"[^>]*>(.*?)</section>~is',
            $html,
            $matches,
            PREG_UNMATCHED_AS_NULL
        );
        $this->assertSame(1, $found, 'The academic subject section must render once on /apps.');
        return $matches[1] ?? '';
    }

    public function test_canonical_apps_page_links_to_real_assigned_subject(): void
    {
        $app = $this->app('orbit-maths-quest');
        $this->classify($app);
        $response = $this->get('/apps')->assertOk()
            ->assertSee('Orbit Maths Quest')->assertSee('Primary');
        $bridge = $this->atlas($response->getContent());
        $this->assertStringContainsString('THE ACADEMIC ATLAS', $bridge);
        $this->assertStringContainsString('Discover by', $bridge);
        $this->assertStringContainsString('/subjects/mathematics', $bridge);

        $this->get('/apps?q=fractions')->assertOk()->assertSee('Orbit Maths Quest');
    }

    public function test_unpublished_assignments_do_not_create_subject_links(): void
    {
        $app = $this->app('hidden-maths-quest', false);
        $this->classify($app);
        $response = $this->get('/apps')->assertOk();
        $bridge = $this->atlas($response->getContent());
        $this->assertStringContainsString('No games have an academic subject assigned yet.', $bridge);
        $this->assertStringNotContainsString('/subjects/mathematics', $bridge);
    }
}
