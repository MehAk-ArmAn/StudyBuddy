<?php

namespace Tests\Feature\School;

use App\Models\SchoolOrganization;
use App\Models\SchoolOrganizationMember;
use App\Models\StudyBuddySchoolTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyPack11BSchoolOSTest extends TestCase
{
    use RefreshDatabase;

    private function person(string $email, string $role = 'teacher', bool $admin = false): User
    {
        return User::forceCreate([
            'name' => 'QA '.explode('@', $email)[0], 'email' => $email,
            'password' => bcrypt('QAPrivatePassword123!'),
            'email_verified_at' => now(), 'role' => $role, 'is_admin' => $admin,
            'role_verification_status' => 'approved',
            'adult_verification_status' => 'approved',
        ]);
    }

    private function school(User $owner, string $domain = 'school-a.example.test', string $status = 'approved'): SchoolOrganization
    {
        $org = SchoolOrganization::create([
            'name' => 'QA School', 'domain' => $domain, 'city' => 'Test City',
            'country' => 'United Arab Emirates', 'official_email' => 'office@'.$domain,
            'requested_by' => $owner->id, 'status' => $status,
        ]);
        if ($status === 'approved') $this->join($org, $owner, 'owner');
        return $org;
    }

    private function join(SchoolOrganization $org, User $person, string $role = 'teacher'): SchoolOrganizationMember
    {
        return SchoolOrganizationMember::create([
            'organization_id' => $org->id, 'user_id' => $person->id,
            'role' => $role, 'status' => 'active', 'joined_at' => now(),
        ]);
    }

    private function task(SchoolOrganization $org, User $creator, ?User $assignee = null): StudyBuddySchoolTask
    {
        return StudyBuddySchoolTask::create([
            'organization_id' => $org->id, 'created_by' => $creator->id,
            'assigned_to' => $assignee?->id, 'title' => 'QA curriculum planning',
            'details' => 'Prepare an agenda.', 'lane' => 'planned', 'priority' => 'normal',
        ]);
    }

    public function test_board_is_private_to_approved_school_members(): void
    {
        $owner = $this->person('owner11b@example.test');
        $outsider = $this->person('outsider11b@example.test');
        $org = $this->school($owner);
        $this->get(route('schools.board', $org))->assertRedirect();
        $this->actingAs($outsider)->get(route('schools.board', $org))->assertNotFound();
        $this->actingAs($owner)->get(route('schools.board', $org))->assertOk()->assertSee('Staff Board');
        $pending = $this->school($outsider, 'pending.example.test', 'pending');
        $this->actingAs($outsider)->get(route('schools.board', $pending))->assertForbidden();
    }

    public function test_manager_creates_and_assigns_real_staff_task_and_external_assignee_is_rejected(): void
    {
        $owner = $this->person('owner11bc@example.test');
        $teacher = $this->person('teacher11bc@example.test');
        $outsider = $this->person('outsider11bc@example.test');
        $org = $this->school($owner);
        $this->join($org, $teacher);
        $this->actingAs($owner)->post(route('schools.board.tasks.store', $org), [
            'title' => 'Arrange staff meeting', 'details' => 'Friday planning',
            'lane' => 'planned', 'priority' => 'high', 'assigned_to' => $teacher->id,
        ])->assertRedirect(route('schools.board', $org));
        $this->assertDatabaseHas('studybuddy_school_tasks', [
            'organization_id' => $org->id, 'assigned_to' => $teacher->id,
            'title' => 'Arrange staff meeting', 'priority' => 'high',
        ]);
        $this->post(route('schools.board.tasks.store', $org), [
            'title' => 'Unsafe assignment', 'lane' => 'doing', 'priority' => 'normal',
            'assigned_to' => $outsider->id,
        ])->assertSessionHasErrors('assigned_to');
    }

    public function test_teacher_may_update_only_the_lane_of_own_task(): void
    {
        $owner = $this->person('owner11bd@example.test');
        $teacher = $this->person('teacher11bd@example.test');
        $other = $this->person('other11bd@example.test');
        $org = $this->school($owner);
        $this->join($org, $teacher);
        $this->join($org, $other);
        $task = $this->task($org, $owner, $teacher);
        $url = route('schools.board.tasks.update', [$org, $task]);
        $this->actingAs($other)->put($url, ['lane' => 'done'])->assertForbidden();
        $this->actingAs($teacher)->put($url, [
            'lane' => 'review', 'title' => 'Unauthorized override',
            'details' => 'Unauthorized override', 'assigned_to' => $other->id,
        ])->assertRedirect();
        $this->assertSame('review', $task->fresh()->lane);
        $this->assertSame('QA curriculum planning', $task->fresh()->title);
        $this->assertSame($teacher->id, $task->fresh()->assigned_to);
        $this->delete(route('schools.board.tasks.archive', [$org, $task]))->assertForbidden();
    }

    public function test_cross_school_task_ids_are_not_accessible(): void
    {
        $ownerA = $this->person('owner-a11b@example.test');
        $ownerB = $this->person('owner-b11b@example.test');
        $schoolA = $this->school($ownerA, 'aa11b.example.test');
        $schoolB = $this->school($ownerB, 'bb11b.example.test');
        $taskB = $this->task($schoolB, $ownerB);
        $this->actingAs($ownerA)->put(route('schools.board.tasks.update', [$schoolA, $taskB]), [
            'title' => 'Cross-tenant', 'lane' => 'done', 'priority' => 'normal',
        ])->assertNotFound();
        $this->delete(route('schools.board.tasks.archive', [$schoolA, $taskB]))->assertNotFound();
        $this->assertNull($taskB->fresh()->archived_at);
    }

    public function test_manager_archives_tasks_instead_of_deleting_records(): void
    {
        $owner = $this->person('archive11b@example.test');
        $org = $this->school($owner);
        $task = $this->task($org, $owner);
        $this->actingAs($owner)->delete(route('schools.board.tasks.archive', [$org, $task]))->assertRedirect();
        $this->assertNotNull($task->fresh()->archived_at);
        $this->get(route('schools.board', $org))->assertDontSee('QA curriculum planning');
        $this->assertDatabaseHas('studybuddy_school_tasks', ['id' => $task->id]);
    }

    public function test_only_school_owner_may_change_verified_staff_roles_and_not_owners(): void
    {
        $owner = $this->person('owner11be@example.test');
        $schoolAdmin = $this->person('schooladmin11be@example.test');
        $teacher = $this->person('teacher11be@example.test');
        $org = $this->school($owner);
        $this->join($org, $schoolAdmin, 'school_admin');
        $staff = $this->join($org, $teacher);
        $url = route('schools.members.role', [$org, $staff]);
        $this->actingAs($schoolAdmin)->put($url, ['role' => 'school_admin'])->assertForbidden();
        $this->actingAs($owner)->put($url, ['role' => 'owner'])->assertSessionHasErrors('role');
        $this->put($url, ['role' => 'school_admin'])->assertRedirect();
        $this->assertSame('school_admin', $staff->fresh()->role);
        $ownerMember = SchoolOrganizationMember::where('organization_id', $org->id)->where('role', 'owner')->firstOrFail();
        $this->put(route('schools.members.role', [$org, $ownerMember]), ['role' => 'teacher'])->assertForbidden();
    }

    public function test_pcl_school_reviewer_sees_governance_but_not_staff_board(): void
    {
        $head = $this->person('admin@studybuddy.fun', 'admin', true);
        $reviewer = $this->person('reviewer11b@example.test', 'admin', true);
        $schoolOwner = $this->person('school-owner11b@example.test');
        $org = $this->school($schoolOwner);
        DB::table('studybuddy_admin_grants')->insert([
            'user_id' => $head->id, 'kind' => 'head', 'capabilities' => '[]',
            'is_active' => true, 'granted_by' => $head->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('studybuddy_admin_grants')->insert([
            'user_id' => $reviewer->id, 'kind' => 'staff',
            'capabilities' => json_encode(['platform.view', 'schools.review']),
            'is_active' => true, 'granted_by' => $head->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->actingAs($reviewer)->get(route('admin.control-room.schools.overview', $org))
            ->assertOk()->assertSee('Institution staff directory');
        $this->get(route('schools.board', $org))->assertNotFound();
        $this->actingAs($schoolOwner)->get(route('admin.control-room.schools.overview', $org))->assertRedirect();
    }
}
