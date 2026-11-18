<?php

namespace Tests\Feature\Learning;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyPack09PulseTest extends TestCase
{
    use RefreshDatabase;

    private function member(string $email, string $role = 'student'): User
    {
        return User::forceCreate([
            'name' => 'Cosmic Explorer', 'email' => $email,
            'email_verified_at' => now(), 'password' => bcrypt('SamplePass12345!'),
            'role' => $role, 'is_admin' => false,
            'role_profile' => ['learning_goal' => 'Keep making progress', 'sb_p08_setup' => ['accent' => 'cyan']],
        ]);
    }

    public function test_my_day_and_post_actions_are_private(): void
    {
        $this->get('/my-day')->assertRedirect('/login');
        $this->post('/my-day/plans', [])->assertRedirect('/login');
        $this->post('/my-day/notifications/read', ['notice' => 'all'])->assertRedirect('/login');
        $this->actingAs($this->member('p09-learner@example.test'))->get('/my-day')
            ->assertOk()->assertSee('Your month, at a glance.')
            ->assertSee('Your learning, not a made-up score.')
            ->assertSee('studybuddy-p07/banners/progress.webp', false);
    }

    public function test_plan_is_persisted_in_only_its_owners_existing_profile(): void
    {
        $one = $this->member('p09-owner@example.test');
        $other = $this->member('p09-other@example.test');
        $date = now()->addDays(3)->toDateString();
        $this->actingAs($one)->post('/my-day/plans', [
            'title' => 'Read my private chapter', 'date' => $date, 'time' => '16:45',
            'notes' => 'Just for me',
        ])->assertRedirect();
        $one->refresh(); $other->refresh();
        $this->assertSame('Keep making progress', $one->role_profile['learning_goal']);
        $this->assertSame('cyan', $one->role_profile['sb_p08_setup']['accent']);
        $this->assertCount(1, $one->role_profile['sb_p09_plans']);
        $this->assertArrayNotHasKey('sb_p09_plans', $other->role_profile);
        $this->get('/my-day?month='.substr($date, 0, 7).'&day='.$date)
            ->assertOk()->assertSee('Read my private chapter')->assertSee('Just for me');
        $this->actingAs($other)->get('/my-day?month='.substr($date, 0, 7).'&day='.$date)
            ->assertOk()->assertDontSee('Read my private chapter');
    }

    public function test_only_owner_can_remove_a_plan_and_invalid_input_is_rejected(): void
    {
        $one = $this->member('p09-remove-owner@example.test');
        $other = $this->member('p09-remove-other@example.test');
        $date = now()->addDay()->toDateString();
        $this->actingAs($one)->post('/my-day/plans', ['title' => 'Private secret plan', 'date' => $date])
            ->assertRedirect();
        $id = $one->fresh()->role_profile['sb_p09_plans'][0]['id'];
        $this->actingAs($other)->delete('/my-day/plans/'.$id)->assertNotFound();
        $this->actingAs($one)->post('/my-day/plans', ['title' => 'X', 'date' => now()->subDay()->toDateString()])
            ->assertSessionHasErrors(['title', 'date']);
        $this->delete('/my-day/plans/'.$id)->assertRedirect();
        $this->assertCount(0, $one->fresh()->role_profile['sb_p09_plans']);
    }

    public function test_notification_preferences_are_account_only_and_do_not_change_privileges(): void
    {
        $user = $this->member('p09-settings@example.test');
        $this->actingAs($user)->put('/my-day/notifications/preferences', [
            'plan_alerts' => '1', 'quiet_enabled' => '1',
            'quiet_start' => '22:30', 'quiet_end' => '06:30',
        ])->assertRedirect();
        $user->refresh();
        $this->assertFalse($user->role_profile['sb_p09_settings']['assignment_alerts']);
        $this->assertTrue($user->role_profile['sb_p09_settings']['plan_alerts']);
        $this->assertSame('22:30', $user->role_profile['sb_p09_settings']['quiet_start']);
        $this->assertSame('student', $user->role);
        $this->assertFalse($user->is_admin);
        $this->assertSame('Keep making progress', $user->role_profile['learning_goal']);
        $this->put('/my-day/notifications/preferences', ['quiet_start' => 'nope', 'quiet_end' => '08:00'])
            ->assertSessionHasErrors('quiet_start');
    }

    public function test_verified_assignment_deadlines_are_visible_only_to_the_connected_learner(): void
    {
        $learner = $this->member('p09-connected@example.test');
        $other = $this->member('p09-outsider@example.test');
        $teacher = $this->member('p09-teacher@example.test', 'teacher');
        $groupId = DB::table('studybuddy_learning_groups')->insertGetId([
            'owner_id' => $teacher->id, 'type' => 'class', 'name' => 'Sample Class',
            'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('studybuddy_group_members')->insert([
            'group_id' => $groupId, 'owner_id' => $teacher->id, 'user_id' => $learner->id,
            'email' => $learner->email, 'member_role' => 'student',
            'status' => 'connected_with_code', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $due = now()->addDays(2);
        $assignmentId = DB::table('studybuddy_assignments')->insertGetId([
            'owner_id' => $teacher->id, 'group_id' => $groupId,
            'title' => 'Verified class science task', 'type' => 'task',
            'status' => 'assigned', 'due_at' => $due, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('studybuddy_assignment_recipients')->insert([
            'assignment_id' => $assignmentId, 'user_id' => $learner->id,
            'email' => $learner->email, 'status' => 'assigned',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->actingAs($learner)->get('/my-day?month='.$due->format('Y-m').'&day='.$due->toDateString())
            ->assertOk()->assertSee('Verified class science task');
        $this->actingAs($other)->get('/my-day?month='.$due->format('Y-m').'&day='.$due->toDateString())
            ->assertOk()->assertDontSee('Verified class science task');
        // A revoked Connect Code membership removes access even if a recipient row remains.
        DB::table('studybuddy_group_members')->where('user_id', $learner->id)->update(['status' => 'revoked']);
        $this->actingAs($learner)->get('/my-day?month='.$due->format('Y-m').'&day='.$due->toDateString())
            ->assertOk()->assertDontSee('Verified class science task');
    }

    public function test_unread_notification_can_only_be_acknowledged_by_its_owner(): void
    {
        $one = $this->member('p09-alert-owner@example.test');
        $other = $this->member('p09-alert-other@example.test');
        $date = now()->addDays(2)->toDateString();
        $this->actingAs($one)->post('/my-day/plans', [
            'title' => 'Plan awaiting acknowledgment', 'date' => $date,
        ])->assertRedirect();
        $uuid = $one->fresh()->role_profile['sb_p09_plans'][0]['id'];
        $id = 'plan:'.$uuid;
        $this->actingAs($other)->post('/my-day/notifications/read', ['notice' => $id])->assertNotFound();
        $this->actingAs($one)->post('/my-day/notifications/read', ['notice' => $id])->assertRedirect();
        $this->assertContains($id, $one->fresh()->role_profile['sb_p09_seen']);
        $this->get('/my-day')->assertOk()->assertSee('Seen');
    }

    public function test_weekly_stats_count_only_the_signed_in_account_recorded_events(): void
    {
        $owner = $this->member('p09-activity-owner@example.test');
        $other = $this->member('p09-activity-other@example.test');
        DB::table('studybuddy_learning_sessions')->insert([
            'user_id' => $owner->id, 'app_slug' => 'math-sprint', 'app_name' => 'Math Sprint',
            'status' => 'finished', 'launch_source' => 'web_launcher',
            'started_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('studybuddy_learning_sessions')->insert([
            'user_id' => $other->id, 'app_slug' => 'other', 'app_name' => 'Other learner private app',
            'status' => 'finished', 'launch_source' => 'web_launcher',
            'started_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->actingAs($owner)->get('/my-day')->assertOk()->assertSee('App visits')->assertSee('>1</td>', false)
            ->assertDontSee('Other learner private app');
    }
}
