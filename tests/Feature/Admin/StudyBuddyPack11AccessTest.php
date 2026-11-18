<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Support\StudyBuddyAdminAuthority as Authority;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyPack11AccessTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $email, bool $admin = false, string $role = 'student'): User
    {
        return User::forceCreate([
            'name' => 'Test Account', 'email' => $email,
            'password' => bcrypt('ARealStrongPassword123!'),
            'email_verified_at' => now(),
            'role' => $role, 'is_admin' => $admin,
        ]);
    }

    private function claim(User $head): void
    {
        DB::table('studybuddy_admin_grants')->insert([
            'user_id' => $head->id, 'kind' => 'head', 'capabilities' => '[]',
            'is_active' => true, 'granted_by' => $head->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function test_email_string_and_role_field_do_not_grant_platform_access(): void
    {
        $fake = $this->account('admin@studybuddy.fun', false, 'admin');
        $this->actingAs($fake)->get('/admin/control-room')->assertRedirect();
        $this->get('/admin/control-room/team')->assertRedirect();
    }

    public function test_claimed_head_can_access_and_cannot_be_deleted_or_demoted_through_ordinary_routes(): void
    {
        $head = $this->account('admin@studybuddy.fun', true, 'admin');
        $this->claim($head);
        $this->actingAs($head)->get('/admin/control-room/team')->assertOk()->assertSee('Protected Head Admin');
        $this->delete('/admin/control-room/users/'.$head->id)->assertForbidden();
        $this->delete('/admin/users/'.$head->id)->assertForbidden();
        $this->put('/admin/control-room/users/'.$head->id, [
            'name'=>'Changed', 'email'=>'replaced@example.test', 'role'=>'student',
            'is_admin'=>false,
        ])->assertForbidden();
        $this->patch('/admin/control-room/account/profile', [
            'name' => 'Renamed Head', 'email' => 'moved@example.test',
        ])->assertSessionHasErrors('email');
        $this->assertSame('admin@studybuddy.fun', $head->fresh()->email);
        $this->assertTrue($head->fresh()->is_admin);
    }

    public function test_legacy_admin_without_scoped_grant_is_denied_after_head_claim(): void
    {
        $head = $this->account('admin@studybuddy.fun', true, 'admin');
        $legacy = $this->account('admin@studybuddy.local', true, 'admin');
        $this->claim($head);
        $this->actingAs($legacy)->get('/admin/control-room')->assertForbidden();
        $this->get('/admin/control-room/users')->assertForbidden();
        $this->assertTrue($legacy->fresh()->is_admin); // no destructive data mutation
    }

    public function test_staff_only_receives_specific_school_permission_and_cannot_promote_others(): void
    {
        $head = $this->account('admin@studybuddy.fun', true, 'admin');
        $staff = $this->account('school-reviewer@example.test', true, 'admin');
        $this->claim($head);
        DB::table('studybuddy_admin_grants')->insert([
            'user_id'=>$staff->id, 'kind'=>'staff',
            'capabilities'=>json_encode(['platform.view','schools.review']),
            'is_active'=>true, 'granted_by'=>$head->id,
            'created_at'=>now(), 'updated_at'=>now(),
        ]);
        $this->actingAs($staff)->get('/admin/control-room')->assertOk();
        $this->get('/admin/control-room/schools')->assertOk();
        $this->get('/admin/control-room/users')->assertForbidden();
        $this->post('/admin/control-room/team', [
            'name'=>'New Admin', 'email'=>'new-admin@example.test', 'capabilities'=>['platform.users.manage'],
        ])->assertForbidden();
    }

    public function test_standard_user_management_does_not_accept_admin_promotion_and_marks_test_accounts(): void
    {
        $head = $this->account('admin@studybuddy.fun', true, 'admin');
        $this->claim($head);
        $this->actingAs($head)->post('/admin/users', [
            'name'=>'Fake Admin', 'email'=>'fake-admin@example.test',
            'role'=>'admin', 'is_admin'=>1,
            'password'=>'SomeStrongPassword123!',
        ])->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email'=>'fake-admin@example.test']);
        $this->post('/admin/users', [
            'name'=>'Test Learner', 'email'=>'p11-test-learner@example.test',
            'role'=>'student', 'is_admin'=>1, 'studybuddy_test_account'=>1,
            'password'=>'SomeStrongPassword123!',
        ])->assertRedirect();
        $user = User::where('email','p11-test-learner@example.test')->firstOrFail();
        $this->assertFalse((bool)$user->is_admin);
        $this->assertTrue((bool)$user->studybuddy_test_account);
    }

    public function test_head_claim_command_anchors_existing_row_without_deleting_legacy_account(): void
    {
        $head = $this->account('admin@studybuddy.fun', true, 'admin');
        $old = $this->account('admin@studybuddy.local', true, 'admin');
        $this->artisan('studybuddy:p11:claim-head', ['id' => $head->id])->assertExitCode(0);
        $this->assertSame($head->id, Authority::headId());
        $this->assertDatabaseHas('users', ['id' => $old->id, 'email' => 'admin@studybuddy.local']);
        $this->actingAs($old)->get('/admin/control-room')->assertForbidden();
    }

    public function test_real_accounts_are_not_deleted_by_ordinary_admin_directory(): void
    {
        $head = $this->account('admin@studybuddy.fun', true, 'admin');
        $learner = $this->account('real-learner@example.test');
        $this->claim($head);
        $this->actingAs($head)->delete('/admin/users/'.$learner->id)->assertStatus(409);
        $this->assertDatabaseHas('users', ['id' => $learner->id]);
    }

    public function test_only_head_can_revoke_staff_and_head_remains_protected(): void
    {
        $head = $this->account('admin@studybuddy.fun', true, 'admin');
        $staff = $this->account('member@example.test', true, 'admin');
        $this->claim($head);
        DB::table('studybuddy_admin_grants')->insert([
            'user_id'=>$staff->id, 'kind'=>'staff',
            'capabilities'=>json_encode(['platform.view','platform.users.manage']),
            'is_active'=>true, 'granted_by'=>$head->id,
            'created_at'=>now(), 'updated_at'=>now(),
        ]);
        $this->actingAs($staff)->delete('/admin/control-room/team/'.$head->id)->assertForbidden();
        $this->actingAs($head)->delete('/admin/control-room/team/'.$head->id)->assertForbidden();
        $this->delete('/admin/control-room/team/'.$staff->id)->assertRedirect();
        $this->assertFalse((bool)$staff->fresh()->is_admin);
        $this->assertTrue((bool)$head->fresh()->is_admin);
    }
}
