<?php

namespace Tests\Feature\Learning;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyClassProgressTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $email, string $role = 'student', bool $verified = true): User
    {
        return User::query()->create([
            'name' => $role === 'teacher' ? 'Teacher Test' : 'Learner Test',
            'email' => $email, 'password' => 'ExamplePassword123!',
            'role' => $role, 'is_admin' => false,
            'email_verified_at' => $verified ? now() : null,
            'date_of_birth' => now()->subYears($role === 'teacher' ? 32 : 15)->toDateString(),
            'role_verification_status' => $role === 'teacher' ? ($verified ? 'verified' : 'pending_admin_review') : 'not_required',
            'adult_verification_status' => $role === 'teacher' ? ($verified ? 'verified' : 'pending_review') : 'not_required',
        ]);
    }

    private function classFor(User $teacher, string $name): int
    {
        return DB::table('studybuddy_learning_groups')->insertGetId([
            'owner_id' => $teacher->id, 'type' => 'class', 'name' => $name,
            'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function member(User $teacher, User $student, int $group, string $status = 'connected_with_code'): void
    {
        DB::table('studybuddy_group_members')->insert([
            'owner_id' => $teacher->id, 'group_id' => $group, 'user_id' => $student->id,
            'email' => $student->email, 'display_name' => $student->name,
            'status' => $status, 'member_role' => 'student',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function assigned(User $teacher, User $student, int $group): int
    {
        $assignment = DB::table('studybuddy_assignments')->insertGetId([
            'owner_id' => $teacher->id, 'group_id' => $group,
            'title' => 'Pack04 Maths Practice', 'status' => 'assigned', 'type' => 'task',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return DB::table('studybuddy_assignment_recipients')->insertGetId([
            'assignment_id' => $assignment, 'user_id' => $student->id,
            'email' => $student->email, 'display_name' => $student->name,
            'status' => 'assigned', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function test_guest_cannot_complete_tasks(): void
    {
        $this->post('/dashboard/tasks/123/complete')->assertRedirect('/login');
    }

    public function test_verified_student_can_complete_own_connected_task_and_teacher_sees_real_class_progress(): void
    {
        $teacher = $this->user('pack04-teacher@example.test', 'teacher');
        $student = $this->user('pack04-student@example.test');
        $group = $this->classFor($teacher, 'Private Physics Group');
        $this->member($teacher, $student, $group);
        $recipient = $this->assigned($teacher, $student, $group);

        $this->actingAs($teacher)->get('/dashboard')->assertOk()
            ->assertSee('Private Physics Group')->assertSee('0 / 1 tasks completed')
            ->assertDontSee('some private math answer');
        $this->actingAs($student)->get('/dashboard')->assertOk()
            ->assertSee('I finished this task')->assertSee('Pack04 Maths Practice');
        $this->actingAs($student)->post(route('studybuddy.tasks.complete', $recipient))
            ->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('studybuddy_assignment_recipients', [
            'id' => $recipient, 'status' => 'completed',
        ]);
        $this->assertNotNull(DB::table('studybuddy_assignment_recipients')->find($recipient)->completed_at);
        $this->actingAs($student)->post(route('studybuddy.tasks.complete', $recipient))
            ->assertRedirect(route('dashboard'));
        $this->actingAs($teacher)->get('/dashboard')->assertOk()
            ->assertSee('1 / 1 tasks completed')->assertSee('Class progress, without guesswork.');
        $this->assertDatabaseCount('studybuddy_point_transactions', 0);
    }

    public function test_other_students_and_unconnected_learners_cannot_complete_someone_elses_assignment(): void
    {
        $teacher = $this->user('pack04-t2@example.test', 'teacher');
        $owner = $this->user('pack04-owner@example.test');
        $intruder = $this->user('pack04-intruder@example.test');
        $class = $this->classFor($teacher, 'Secure Class Four');
        $this->member($teacher, $owner, $class);
        $recipient = $this->assigned($teacher, $owner, $class);
        $this->actingAs($intruder)->post(route('studybuddy.tasks.complete', $recipient))->assertNotFound();
        $this->assertDatabaseHas('studybuddy_assignment_recipients', ['id' => $recipient, 'status' => 'assigned']);
        DB::table('studybuddy_group_members')->where('group_id', $class)->update(['status' => 'invited']);
        $this->actingAs($owner)->post(route('studybuddy.tasks.complete', $recipient))->assertNotFound();
        $this->assertDatabaseHas('studybuddy_assignment_recipients', ['id' => $recipient, 'status' => 'assigned']);
    }

    public function test_unverified_teachers_and_other_class_owners_cannot_see_progress(): void
    {
        $teacherA = $this->user('pack04-teacher-a@example.test', 'teacher');
        $teacherB = $this->user('pack04-teacher-b@example.test', 'teacher');
        $unverified = $this->user('pack04-pending@example.test', 'teacher', false);
        $student = $this->user('pack04-learner@example.test');
        $group = $this->classFor($teacherA, 'Secret Mountain Class');
        $this->member($teacherA, $student, $group);
        $this->assigned($teacherA, $student, $group);

        $this->actingAs($teacherB)->get('/dashboard')->assertOk()
            ->assertDontSee('Secret Mountain Class')->assertDontSee('1 connected learners');
        $this->actingAs($unverified)->get('/dashboard')->assertOk()
            ->assertSee('Complete email, adult and educator verification')
            ->assertDontSee('Secret Mountain Class');
        $this->actingAs($teacherA)->get('/dashboard')->assertOk()->assertSee('Secret Mountain Class');
    }

    public function test_pending_unlinked_roster_does_not_count_and_no_grades_are_shared(): void
    {
        $teacher = $this->user('pack04-gate@example.test', 'teacher');
        $student = $this->user('pack04-student2@example.test');
        $group = $this->classFor($teacher, 'Verified Only Class');
        $this->member($teacher, $student, $group, 'invited');
        $this->assigned($teacher, $student, $group);
        $this->actingAs($teacher)->get('/dashboard')->assertOk()
            ->assertSee('Verified Only Class')->assertSee('0 / 0 tasks completed');
    }

    public function test_unverified_student_cannot_submit_completion(): void
    {
        $teacher = $this->user('pack04-teacher-verify@example.test', 'teacher');
        $student = $this->user('pack04-student-unverified@example.test', 'student', false);
        $group = $this->classFor($teacher, 'Student Email Check');
        $this->member($teacher, $student, $group);
        $recipient = $this->assigned($teacher, $student, $group);
        $this->actingAs($student)->post(route('studybuddy.tasks.complete', $recipient))->assertForbidden();
        $this->assertDatabaseHas('studybuddy_assignment_recipients', ['id' => $recipient, 'status' => 'assigned']);
    }
}
