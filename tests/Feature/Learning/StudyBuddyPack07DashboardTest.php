<?php

namespace Tests\Feature\Learning;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyPack07DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $role, string $email): User
    {
        return User::forceCreate([
            'name' => 'Phase Seven Member',
            'email' => $email,
            'email_verified_at' => now(),
            'password' => bcrypt('ExamplePassword123!'),
            'role' => $role,
            'is_admin' => false,
        ]);
    }

    public function test_student_dashboard_contains_real_data_command_center(): void
    {
        $learner = $this->account('student', 'p07-student@example.org');
        $this->actingAs($learner)->get('/dashboard')->assertOk()
            ->assertSee('id="sbp07-dashboard"', false)
            ->assertSee('A little progress, a whole universe.')
            ->assertSee('Open assignments')
            ->assertSee('Nothing assigned right now.')
            ->assertSee('id="sbp07-workspace"', false)
            ->assertSee('id="sb-learning-snapshot-heading"', false)
            ->assertSee("What you've actually explored.", false);
    }

    public function test_teacher_dashboard_counts_all_owned_assignments_not_only_recent_fourteen(): void
    {
        $teacher = $this->account('teacher', 'p07-teacher@example.org');
        $other = $this->account('teacher', 'p07-other-teacher@example.org');
        for ($i = 0; $i < 17; $i++) {
            DB::table('studybuddy_assignments')->insert([
                'owner_id' => $teacher->id, 'title' => 'P07 assignment '.$i,
                'type' => 'task', 'status' => 'draft',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        DB::table('studybuddy_assignments')->insert([
            'owner_id' => $other->id, 'title' => 'OTHER TEACHER PRIVATE WORK',
            'type' => 'task', 'status' => 'draft',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->actingAs($teacher)->get('/dashboard')->assertOk()
            ->assertSee('Your teaching day, made clearer.')
            ->assertSee('Assignments created')
            ->assertSee('>17</strong>', false)
            ->assertDontSee('OTHER TEACHER PRIVATE WORK');
    }

    public function test_parent_dashboard_shows_family_action_without_private_learner_snapshot(): void
    {
        $parent = $this->account('parent', 'p07-parent@example.org');
        $this->actingAs($parent)->get('/dashboard')->assertOk()
            ->assertSee('Your family learning space.')
            ->assertSee('Connect a child')
            ->assertDontSee('YOUR PRIVATE LEARNING STORY');
    }
}
