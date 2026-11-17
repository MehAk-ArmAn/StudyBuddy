<?php

namespace Tests\Feature\Learning;

use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyW31ConnectedProgressTest extends TestCase
{
    use RefreshDatabase;

    private function learner(string $email): User
    {
        // The StudyBuddy project does not provide the standard UserFactory.
        return User::query()->create([
            'name' => 'W31 Learner', 'email' => $email,
            'password' => 'ExamplePassword123!', 'role' => 'independent_learner',
        ]);
    }

    private function app(string $slug = 'w31-math', array $overrides = []): StudyBuddyMiniAppPlatform
    {
        return StudyBuddyMiniAppPlatform::query()->create(array_merge([
            'slug' => $slug, 'name' => 'W31 Math World', 'category' => 'Math',
            'is_active' => true, 'is_web_enabled' => true,
            'web_play_url' => 'https://example.org/w31-demo',
        ], $overrides));
    }

    public function test_guests_cannot_create_visits_or_view_private_insights(): void
    {
        $this->get('/learning/connected/insights')->assertRedirect('/login');
        $this->postJson('/learning/connected/start', ['app_slug' => 'w31-math'])->assertUnauthorized();
    }

    public function test_logged_in_launcher_creates_private_w30_session_without_awarding_points(): void
    {
        $u = $this->learner('w31a@example.org'); $this->app();
        $res = $this->actingAs($u)->postJson('/learning/connected/start', ['app_slug' => 'w31-math']);
        $res->assertOk()->assertJsonPath('ok', true)->assertJsonPath('resumed', false);
        $id = $res->json('session_id');
        $this->assertDatabaseHas('studybuddy_learning_sessions', [
            'id' => $id, 'user_id' => $u->id, 'app_slug' => 'w31-math',
            'status' => 'active', 'launch_source' => 'web_launcher',
        ]);
        $again = $this->actingAs($u)->postJson('/learning/connected/start', ['app_slug' => 'w31-math']);
        $again->assertOk()->assertJsonPath('session_id', $id)->assertJsonPath('resumed', true);
        $this->assertSame(1, DB::table('studybuddy_learning_sessions')->where('user_id', $u->id)->count());
        $this->assertDatabaseMissing('studybuddy_point_transactions', ['user_id' => $u->id, 'source_slug' => 'w31-math']);
    }

    public function test_unpublished_apps_and_disallowed_roles_are_rejected(): void
    {
        $u=$this->learner('w31b@example.org');
        $this->app('w31-hidden', ['is_active'=>false]);
        $this->app('w31-teachers', ['audience_roles'=>['teacher']]);
        $this->actingAs($u)->postJson('/learning/connected/start', ['app_slug'=>'w31-hidden'])->assertNotFound();
        $this->actingAs($u)->postJson('/learning/connected/start', ['app_slug'=>'w31-teachers'])->assertForbidden();
        $this->assertDatabaseCount('studybuddy_learning_sessions', 0);
    }

    public function test_different_active_app_requires_explicit_close(): void
    {
        $u=$this->learner('w31c@example.org'); $this->app();$this->app('w31-english');
        $this->actingAs($u)->postJson('/learning/connected/start', ['app_slug'=>'w31-math'])->assertOk();
        $this->actingAs($u)->postJson('/learning/connected/start', ['app_slug'=>'w31-english'])->assertStatus(409)
            ->assertJsonPath('ok', false);
        $this->assertDatabaseCount('studybuddy_learning_sessions', 1);
    }

    public function test_one_account_cannot_write_to_another_account_session(): void
    {
        $owner=$this->learner('w31-owner@example.org');$other=$this->learner('w31-other@example.org');$this->app();
        $res=$this->actingAs($owner)->postJson('/learning/connected/start', ['app_slug'=>'w31-math']);
        $id=$res->json('session_id');
        $this->actingAs($other)->postJson("/learning/connected/{$id}/heartbeat")->assertNotFound();
        $this->actingAs($other)->postJson("/learning/connected/{$id}/finish")->assertNotFound();
        $this->assertDatabaseHas('studybuddy_learning_sessions',['id'=>$id,'status'=>'active','user_id'=>$owner->id]);
        // This is a PUBLIC published app, so its normal catalogue recommendation
        // may appear to both learners. Test private session isolation with a
        // uniquely identifying entry title found ONLY in the owner's history.
        $privateJournalLabel = 'W31 OWNER-ONLY JOURNAL ENTRY 8C7A4D';
        DB::table('studybuddy_learning_sessions')->where('id', $id)->update([
            'app_name' => $privateJournalLabel,
        ]);
        $this->actingAs($owner)->get('/learning/connected/insights')
            ->assertOk()->assertSee($privateJournalLabel);
        $this->actingAs($other)->get('/learning/connected/insights')
            ->assertOk()->assertDontSee($privateJournalLabel);
    }

    public function test_server_timed_heartbeat_and_finish_are_observations_not_scores(): void
    {
        $u=$this->learner('w31d@example.org');$this->app();
        $id=$this->actingAs($u)->postJson('/learning/connected/start', ['app_slug'=>'w31-math'])->json('session_id');
        DB::table('studybuddy_learning_sessions')->where('id',$id)
            ->update(['last_observed_at'=>now()->subSeconds(24)]);
        $response=$this->actingAs($u)->postJson("/learning/connected/{$id}/heartbeat", [
            // Malicious client-supplied score/duration fields are intentionally ignored.
            'observed_seconds'=>999999, 'score'=>999999, 'mastered'=>true,
        ]);
        $response->assertOk();
        $observed=(int) DB::table('studybuddy_learning_sessions')->where('id',$id)->value('observed_seconds');
        $this->assertGreaterThanOrEqual(20,$observed);
        $this->assertLessThanOrEqual(30,$observed);
        DB::table('studybuddy_learning_sessions')->where('id',$id)
            ->update(['last_observed_at'=>now()->subSeconds(120)]);
        $this->actingAs($u)->postJson("/learning/connected/{$id}/heartbeat", ['kind'=>'resume'])
            ->assertOk()->assertJsonPath('observed_seconds', $observed);
        $this->actingAs($u)->postJson("/learning/connected/{$id}/finish")
            ->assertOk()->assertJsonPath('status','finished');
        $this->actingAs($u)->postJson("/learning/connected/{$id}/finish")->assertOk();
        $this->actingAs($u)->postJson("/learning/connected/{$id}/heartbeat")->assertStatus(409);
        $this->assertDatabaseMissing('studybuddy_point_transactions',['user_id'=>$u->id,'source_slug'=>'w31-math']);
    }

    public function test_private_insights_show_only_this_accounts_connected_history(): void
    {
        $u=$this->learner('w31e@example.org');$this->app();$this->app('w31-next',['name'=>'Next Discovery']);
        $this->actingAs($u)->get('/learning/connected/insights')->assertOk()
            ->assertSee('Your curiosity has a')->assertSee('Next Discovery');
        $res=$this->actingAs($u)->postJson('/learning/connected/start', ['app_slug'=>'w31-math']);
        $this->actingAs($u)->postJson('/learning/connected/'.$res->json('session_id').'/finish')->assertOk();
        $this->actingAs($u)->get('/learning/connected/insights')->assertOk()->assertSee('W31 Math World');
    }
}
