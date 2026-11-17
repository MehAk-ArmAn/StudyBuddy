<?php

namespace Tests\Feature\Learning;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyW32MathSprintTest extends TestCase
{
    use RefreshDatabase;

    private function learner(string $email): User
    {
        return User::query()->create([
            'name' => 'W32 Learner', 'email' => $email,
            'password' => 'ExamplePassword123!', 'role' => 'independent_learner',
        ]);
    }

    private function startSprint(User $user, string $level = 'starter'): object
    {
        $this->actingAs($user)->post('/learning/math-sprint/start', ['level' => $level])
            ->assertRedirect(route('studybuddy.w32.index'));
        return DB::table('studybuddy_w32_math_runs')->where('user_id', $user->id)->latest('id')->first();
    }

    private function answers(object $run, int $correct = 5): array
    {
        $questions = json_decode($run->questions_json, true, 512, JSON_THROW_ON_ERROR);
        $answers = [];
        foreach ($questions as $i => $question) {
            $answers[$i] = $i < $correct ? $question['answer'] : $question['answer'] + 100;
        }
        return $answers;
    }

    public function test_guests_cannot_view_or_submit_math_sprint(): void
    {
        $this->get('/learning/math-sprint')->assertRedirect('/login');
        $this->post('/learning/math-sprint/start', ['level' => 'starter'])->assertRedirect('/login');
        $this->post('/learning/math-sprint/1/submit', ['answers' => [1,2,3,4,5]])->assertRedirect('/login');
    }

    public function test_start_records_one_private_linked_learning_session_and_resumes_without_duplicates(): void
    {
        $u = $this->learner('w32a@example.org');
        $r = $this->startSprint($u);
        $this->assertSame('active', $r->status);
        $this->assertDatabaseHas('studybuddy_learning_sessions', [
            'id' => $r->learning_session_id, 'user_id' => $u->id,
            'app_slug' => 'studybuddy-math-sprint', 'launch_source' => 'w32_server_graded',
            'status' => 'active',
        ]);
        $this->actingAs($u)->get('/learning/math-sprint')->assertOk()->assertSee('Check my answers');
        $this->actingAs($u)->post('/learning/math-sprint/start', ['level' => 'explorer'])
            ->assertRedirect(route('studybuddy.w32.index'));
        $this->assertDatabaseCount('studybuddy_w32_math_runs', 1);
        $this->assertDatabaseCount('studybuddy_learning_sessions', 1);
    }

    public function test_five_server_graded_answers_earn_two_once_only_and_do_not_mint_points(): void
    {
        $u = $this->learner('w32b@example.org');
        // Compare the persisted database balance on both sides. A newly created
        // Eloquent model can expose null before DB defaults (e.g. 0) are hydrated.
        $pointsBefore = DB::table('users')->where('id', $u->id)->value('cosmic_points');
        $r = $this->startSprint($u, 'explorer');
        $this->actingAs($u)->post("/learning/math-sprint/{$r->id}/submit", [
            'answers' => $this->answers($r), 'score' => 999, 'points' => 999999,
            'achievement' => 'everything',
        ])->assertRedirect(route('studybuddy.w32.result', $r->id));
        $this->assertDatabaseHas('studybuddy_w32_math_runs', [
            'id' => $r->id, 'user_id' => $u->id, 'status' => 'completed', 'score' => 5,
        ]);
        $this->assertDatabaseHas('studybuddy_learning_sessions', [
            'id' => $r->learning_session_id, 'status' => 'finished',
        ]);
        $this->assertDatabaseHas('studybuddy_w32_achievements', [
            'user_id' => $u->id, 'code' => 'math-first-pass',
        ]);
        $this->assertDatabaseHas('studybuddy_w32_achievements', [
            'user_id' => $u->id, 'code' => 'math-perfect-five',
        ]);
        $this->actingAs($u)->post("/learning/math-sprint/{$r->id}/submit", [
            'answers' => [0,0,0,0,0],
        ])->assertRedirect(route('studybuddy.w32.result', $r->id));
        $this->assertDatabaseCount('studybuddy_w32_achievements', 2);
        $pointsAfter = DB::table('users')->where('id', $u->id)->value('cosmic_points');
        $this->assertSame($pointsBefore, $pointsAfter, 'Math Sprint must never change the persisted points balance.');
        $this->actingAs($u)->get("/learning/math-sprint/results/{$r->id}")
            ->assertOk()->assertSee('A perfect orbit!');
    }

    public function test_wrong_answers_cannot_claim_a_badge(): void
    {
        $u = $this->learner('w32c@example.org');
        $r = $this->startSprint($u);
        $this->actingAs($u)->post("/learning/math-sprint/{$r->id}/submit", [
            'answers' => $this->answers($r, 3),
        ])->assertRedirect(route('studybuddy.w32.result', $r->id));
        $this->assertDatabaseHas('studybuddy_w32_math_runs', ['id' => $r->id, 'score' => 3]);
        $this->assertDatabaseCount('studybuddy_w32_achievements', 0);
    }

    public function test_four_correct_earns_math_star_but_not_perfect_orbit(): void
    {
        $u = $this->learner('w32d@example.org');
        $r = $this->startSprint($u);
        $this->actingAs($u)->post("/learning/math-sprint/{$r->id}/submit", [
            'answers' => $this->answers($r, 4),
        ])->assertRedirect(route('studybuddy.w32.result', $r->id));
        $this->assertDatabaseHas('studybuddy_w32_math_runs', ['id' => $r->id, 'score' => 4]);
        $this->assertDatabaseHas('studybuddy_w32_achievements', ['user_id' => $u->id, 'code' => 'math-first-pass']);
        $this->assertDatabaseMissing('studybuddy_w32_achievements', ['user_id' => $u->id, 'code' => 'math-perfect-five']);
    }

    public function test_other_learner_cannot_submit_or_read_private_results(): void
    {
        $owner = $this->learner('w32owner@example.org');
        $other = $this->learner('w32other@example.org');
        $r = $this->startSprint($owner);
        $this->actingAs($other)->post("/learning/math-sprint/{$r->id}/submit", [
            'answers' => $this->answers($r),
        ])->assertNotFound();
        $this->actingAs($other)->get("/learning/math-sprint/results/{$r->id}")->assertNotFound();
        $this->assertDatabaseHas('studybuddy_w32_math_runs', ['id' => $r->id, 'status' => 'active']);
    }

    public function test_expired_run_does_not_award_a_badge_and_closes_the_session(): void
    {
        $u = $this->learner('w32expire@example.org');
        $r = $this->startSprint($u);
        DB::table('studybuddy_w32_math_runs')->where('id', $r->id)
            ->update(['started_at' => now()->subMinutes(35)]);
        $this->actingAs($u)->post("/learning/math-sprint/{$r->id}/submit", [
            'answers' => $this->answers($r),
        ])->assertRedirect(route('studybuddy.w32.index'));
        $this->assertDatabaseHas('studybuddy_w32_math_runs', ['id' => $r->id, 'status' => 'expired']);
        $this->assertDatabaseHas('studybuddy_learning_sessions', ['id' => $r->learning_session_id, 'status' => 'left']);
        $this->assertDatabaseCount('studybuddy_w32_achievements', 0);
    }

    public function test_other_active_learning_app_blocks_new_math_sprint(): void
    {
        $u = $this->learner('w32otherapp@example.org');
        DB::table('studybuddy_learning_sessions')->insert([
            'user_id' => $u->id, 'app_slug' => 'another-game', 'app_name' => 'Another Game',
            'status' => 'active', 'started_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->actingAs($u)->from('/learning/math-sprint')
            ->post('/learning/math-sprint/start', ['level' => 'starter'])
            ->assertRedirect('/learning/math-sprint')->assertSessionHasErrors('session');
        $this->assertDatabaseCount('studybuddy_w32_math_runs', 0);
    }
}
