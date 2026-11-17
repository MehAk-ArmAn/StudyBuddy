<?php

namespace Tests\Feature\Learning;

use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyW30LearningActivityTest extends TestCase
{
    use RefreshDatabase;

    private function learner(string $email): User
    {
        return User::query()->create([
            'name' => 'Test Learner', 'email' => $email,
            'password' => 'ExamplePassword123!', 'role' => 'independent_learner',
        ]);
    }

    private function app(): StudyBuddyMiniAppPlatform
    {
        return StudyBuddyMiniAppPlatform::query()->create([
            'slug' => 'w30-test-portal', 'name' => 'Test Portal', 'category' => 'Math',
            'is_active' => true, 'is_web_enabled' => true,
            'web_play_url' => 'https://example.org/safe-app',
        ]);
    }

    public function test_guests_cannot_see_or_write_private_sessions(): void
    {
        $this->get('/learning/activity')->assertRedirect('/login');
        $this->post('/learning/activity/start', ['app_slug' => 'anything'])->assertRedirect('/login');
    }

    public function test_learner_can_start_and_finish_without_earning_points(): void
    {
        $user = $this->learner('w30a@example.org');
        $this->app();
        $this->actingAs($user)->post('/learning/activity/start', ['app_slug' => 'w30-test-portal'])
            ->assertRedirect(route('studybuddy.learning.activity'));
        $session = DB::table('studybuddy_learning_sessions')->where('user_id', $user->id)->first();
        $this->assertNotNull($session);
        $this->actingAs($user)->post('/learning/activity/'.$session->id.'/finish', ['reflection' => 'I tried numbers.'])
            ->assertRedirect(route('studybuddy.learning.activity'));
        $this->assertSame('finished', DB::table('studybuddy_learning_sessions')->find($session->id)->status);
        $this->assertDatabaseMissing('studybuddy_point_transactions', [
            'user_id' => $user->id, 'source_type' => 'mini_app_session',
        ]);
    }

    public function test_one_open_session_per_account_and_idempotent_finish(): void
    {
        $user = $this->learner('w30b@example.org'); $this->app();
        $this->actingAs($user)->post('/learning/activity/start', ['app_slug' => 'w30-test-portal']);
        $this->actingAs($user)->post('/learning/activity/start', ['app_slug' => 'w30-test-portal'])
            ->assertSessionHasErrors('app_slug');
        $this->assertSame(1, DB::table('studybuddy_learning_sessions')->where('user_id', $user->id)->count());
        $id = DB::table('studybuddy_learning_sessions')->where('user_id', $user->id)->value('id');
        $this->actingAs($user)->post('/learning/activity/'.$id.'/finish')->assertRedirect();
        $this->actingAs($user)->post('/learning/activity/'.$id.'/finish')->assertSessionHasErrors('session');
    }

    public function test_other_accounts_cannot_read_modify_or_delete_private_sessions(): void
    {
        $owner = $this->learner('w30owner@example.org');
        $other = $this->learner('w30other@example.org'); $this->app();
        $this->actingAs($owner)->post('/learning/activity/start', ['app_slug' => 'w30-test-portal']);
        $id = DB::table('studybuddy_learning_sessions')->where('user_id', $owner->id)->value('id');
        $this->actingAs($other)->get('/learning/activity')->assertOk()->assertSee('No recorded sessions yet')->assertDontSee('SESSION IN PROGRESS');
        $this->actingAs($other)->post('/learning/activity/'.$id.'/finish')->assertNotFound();
        $this->actingAs($other)->delete('/learning/activity/'.$id)->assertRedirect();
        $this->assertDatabaseHas('studybuddy_learning_sessions', ['id' => $id, 'user_id' => $owner->id, 'status' => 'active']);
    }

    public function test_unpublished_apps_cannot_be_logged_as_played(): void
    {
        $user = $this->learner('w30c@example.org');
        StudyBuddyMiniAppPlatform::query()->create([
            'slug' => 'w30-concept', 'name' => 'Concept Only', 'category' => 'Math',
            'is_active' => true, 'is_web_enabled' => false,
        ]);
        $this->actingAs($user)->post('/learning/activity/start', ['app_slug' => 'w30-concept'])
            ->assertSessionHasErrors('app_slug');
        $this->assertDatabaseCount('studybuddy_learning_sessions', 0);
    }

    public function test_learner_can_delete_own_history(): void
    {
        $user = $this->learner('w30d@example.org'); $this->app();
        $this->actingAs($user)->post('/learning/activity/start', ['app_slug' => 'w30-test-portal']);
        $id = DB::table('studybuddy_learning_sessions')->where('user_id', $user->id)->value('id');
        $this->actingAs($user)->delete('/learning/activity/'.$id)->assertRedirect();
        $this->assertDatabaseMissing('studybuddy_learning_sessions', ['id' => $id]);
    }
}
