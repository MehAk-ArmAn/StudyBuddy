<?php

namespace Tests\Feature\Apps;

use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyW34SubjectsTest extends TestCase
{
    use RefreshDatabase;

    private function app(string $slug = 'w34-maths', bool $active = true): StudyBuddyMiniAppPlatform
    {
        return StudyBuddyMiniAppPlatform::query()->create([
            'slug' => $slug, 'name' => 'Counting Explorer', 'category' => 'Math',
            'status' => 'beta', 'is_active' => $active,
            'is_download_enabled' => false, 'is_web_enabled' => false,
        ]);
    }

    private function user(bool $admin, string $email): User
    {
        return User::query()->create(['name' => 'W34 Tester', 'email' => $email,
            'password' => 'ExamplePassword123!', 'is_admin' => $admin, 'role' => 'independent_learner']);
    }

    public function test_all_twenty_eight_taxonomy_subjects_exist_without_public_empty_categories(): void
    {
        $this->assertCount(28, config('studybuddy_w34_subjects.subjects'));
        $this->get('/subjects')->assertOk()->assertDontSee('Early Education')
            ->assertSee('SUBJECT ATLAS')->assertDontSee('Mathematics</span>', false);
    }

    public function test_admin_can_classify_real_app_and_visitors_can_find_it(): void
    {
        $app = $this->app();
        $admin = $this->user(true, 'w34-admin@example.org');
        $this->actingAs($admin)->post("/admin/control-room/apps/{$app->id}/subjects", [
            'primary_subject' => 'mathematics', 'learning_stage' => 'primary',
            'topics' => 'counting, addition', 'learning_outcome' => 'Practise number facts',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('studybuddy_w34_subject_assignments', [
            'app_id' => $app->id, 'primary_subject' => 'mathematics',
        ]);
        $this->get('/subjects')->assertOk()->assertSee('Mathematics')->assertSee('Counting Explorer');
        $this->get('/subjects/mathematics?q=addition&stage=primary')->assertOk()->assertSee('Counting Explorer');
        // The global footer can legitimately mention other published games.
        // Only the Geography results area should exclude a Mathematics game.
        $this->get('/subjects/geography')->assertOk()
            ->assertSee('Geography games')
            ->assertSee('No published games match these filters yet.')
            ->assertDontSee('<h3>Counting Explorer</h3>', false);
        $this->get('/apps/'.$app->slug)->assertOk()->assertSee('Mathematics');
    }

    public function test_unpublished_game_does_not_appear_in_subject_discovery(): void
    {
        $app = $this->app('w34-hidden', false);
        DB::table('studybuddy_w34_subject_assignments')->insert([
            'app_id' => $app->id, 'primary_subject' => 'geography',
            'topics' => 'flags', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->get('/subjects')->assertOk()->assertDontSee('Counting Explorer')->assertDontSee('Geography</span>', false);
    }

    public function test_regular_user_cannot_change_classifications(): void
    {
        $app = $this->app();
        $normal = $this->user(false, 'w34-normal@example.org');
        $this->actingAs($normal)->post("/admin/control-room/apps/{$app->id}/subjects", [
            'primary_subject' => 'mathematics',
        ])->assertRedirect();
        $this->assertDatabaseCount('studybuddy_w34_subject_assignments', 0);
    }

    public function test_bad_subject_is_rejected_and_existing_category_is_not_rewritten(): void
    {
        $app = $this->app();
        $admin = $this->user(true, 'w34-invalid@example.org');
        $this->actingAs($admin)->post("/admin/control-room/apps/{$app->id}/subjects", [
            'primary_subject' => 'wizardry',
        ])->assertSessionHasErrors('primary_subject');
        $this->assertSame('Math', $app->fresh()->category);
        $this->assertDatabaseCount('studybuddy_w34_subject_assignments', 0);
    }
}
