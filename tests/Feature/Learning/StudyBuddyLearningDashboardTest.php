<?php

namespace Tests\Feature\Learning;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyLearningDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function member(string $email, string $role = 'student'): User
    {
        return User::query()->create([
            'name' => 'Learning Dashboard Test', 'email' => $email,
            'password' => 'ExamplePassword123!', 'role' => $role,
        ]);
    }

    public function test_guest_cannot_view_private_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_student_sees_empty_truthful_snapshot_without_fake_scores(): void
    {
        $learner = $this->member('snapshot-empty@example.org');
        $this->actingAs($learner)->get('/dashboard')->assertOk()
            ->assertSee("What you've actually explored.", false)
            ->assertSee('No graded Math Sprint results yet.')
            ->assertSee('No recorded learning activity yet.');
    }

    public function test_only_signed_in_learner_sees_their_actual_journal_and_grades(): void
    {
        $owner = $this->member('snapshot-owner@example.org', 'independent_learner');
        $other = $this->member('snapshot-other@example.org');
        $visitName = 'Private Ocean Math Visit 29Z';
        $now = now();
        DB::table('studybuddy_learning_sessions')->insert([
            'user_id' => $owner->id, 'app_slug' => 'private-ocean-math',
            'app_name' => $visitName, 'status' => 'finished', 'launch_source' => 'web_launcher',
            'started_at' => $now, 'ended_at' => $now,
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $runId = DB::table('studybuddy_w32_math_runs')->insertGetId([
            'user_id' => $owner->id, 'level' => 'explorer', 'status' => 'completed',
            'questions_json' => '[]', 'score' => 4, 'total' => 5,
            'started_at' => $now, 'submitted_at' => $now,
            'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('studybuddy_w32_achievements')->insert([
            'user_id' => $owner->id, 'run_id' => $runId, 'code' => 'math-first-pass',
            'earned_at' => $now, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->actingAs($owner)->get('/dashboard')->assertOk()
            ->assertSee($visitName)
            ->assertSee('Explorer · 4 / 5 correct')
            ->assertSee('Math Star')
            ->assertSee('Saved app visits');
        $this->actingAs($other)->get('/dashboard')->assertOk()
            ->assertDontSee($visitName)->assertDontSee('Explorer · 4 / 5 correct')
            ->assertDontSee('Math Star');
    }

    public function test_parent_and_teacher_dashboards_do_not_show_private_student_snapshot(): void
    {
        foreach (['parent', 'teacher'] as $role) {
            $user = $this->member('snapshot-'.$role.'@example.org', $role);
            $this->actingAs($user)->get('/dashboard')->assertOk()
                ->assertDontSee('YOUR PRIVATE LEARNING STORY');
        }
    }

    public function test_ungraded_math_attempts_are_not_presented_as_scores(): void
    {
        $user = $this->member('snapshot-ungraded@example.org');
        $now = now();
        DB::table('studybuddy_w32_math_runs')->insert([
            'user_id' => $user->id, 'level' => 'starter', 'status' => 'active',
            'questions_json' => '[]', 'total' => 5,
            'started_at' => $now, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $this->actingAs($user)->get('/dashboard')->assertOk()
            ->assertSee('No graded Math Sprint results yet.')->assertDontSee('Starter · 0 / 5 correct');
    }
}
