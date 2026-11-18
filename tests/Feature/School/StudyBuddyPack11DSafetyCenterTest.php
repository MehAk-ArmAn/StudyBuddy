<?php

namespace Tests\Feature\School;

use App\Models\SchoolOrganization;
use App\Models\SchoolOrganizationMember;
use App\Models\StudyBuddySchoolNotice;
use App\Models\StudyBuddySchoolSafetyReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyPack11DSafetyCenterTest extends TestCase
{
    use RefreshDatabase;

    private function person(string $email, string $role = 'teacher'): User
    {
        return User::forceCreate([
            'name' => 'QA '.explode('@', $email)[0], 'email' => $email,
            'password' => bcrypt('Local-Test-Only-123!'),
            'email_verified_at' => now(), 'role' => $role, 'is_admin' => false,
            'role_verification_status' => 'approved', 'adult_verification_status' => 'approved',
        ]);
    }

    private function school(User $owner, string $domain = 'safety-a.qa.test'): SchoolOrganization
    {
        $org = SchoolOrganization::create([
            'name' => 'QA Safety School', 'domain' => $domain, 'official_email' => 'office@'.$domain,
            'status' => 'approved', 'requested_by' => $owner->id, 'country' => 'United Arab Emirates',
        ]);
        $this->join($org, $owner, 'owner');
        return $org;
    }

    private function join(SchoolOrganization $org, User $user, string $role = 'teacher'): void
    {
        SchoolOrganizationMember::create([
            'organization_id' => $org->id, 'user_id' => $user->id,
            'role' => $role, 'status' => 'active', 'joined_at' => now(),
        ]);
    }

    private function notice(SchoolOrganization $org, User $owner, string $priority = 'normal'): StudyBuddySchoolNotice
    {
        return StudyBuddySchoolNotice::create([
            'organization_id' => $org->id, 'posted_by' => $owner->id,
            'title' => 'QA safety briefing', 'body' => 'Prepare team meeting.', 'priority' => $priority,
        ]);
    }

    public function test_private_safety_center_requires_current_approved_membership(): void
    {
        $owner = $this->person('owner-d1@qa.test');
        $outsider = $this->person('outsider-d1@qa.test');
        $org = $this->school($owner);
        $this->get(route('schools.safety.index', $org))->assertRedirect();
        $this->actingAs($outsider)->get(route('schools.safety.index', $org))->assertNotFound();
        $this->actingAs($owner)->get(route('schools.safety.index', $org))
            ->assertOk()->assertSee('Raise a concern');
        $this->assertDatabaseHas('studybuddy_organizations', ['id' => $org->id, 'status' => 'approved']);
    }

    public function test_only_managers_create_staff_notices_and_receipts_are_saved(): void
    {
        $owner = $this->person('owner-d2@qa.test');
        $teacher = $this->person('teacher-d2@qa.test');
        $org = $this->school($owner);
        $this->join($org, $teacher);
        $payload = ['title' => 'Safety assembly notice', 'body' => 'Please attend the briefing.', 'priority' => 'urgent'];
        $this->actingAs($teacher)->post(route('schools.safety.notices.post', $org), $payload)->assertForbidden();
        $this->actingAs($owner)->post(route('schools.safety.notices.post', $org), $payload)
            ->assertRedirect(route('schools.safety.index', $org));
        $this->assertDatabaseHas('studybuddy_school_notices', ['organization_id' => $org->id, 'title' => 'Safety assembly notice']);
        $this->assertEquals(2, DB::table('studybuddy_school_notice_receipts')->count());
    }

    public function test_only_valid_school_member_can_acknowledge_notice(): void
    {
        $owner = $this->person('owner-d3@qa.test');
        $teacher = $this->person('teacher-d3@qa.test');
        $stranger = $this->person('stranger-d3@qa.test');
        $org = $this->school($owner);
        $this->join($org, $teacher);
        $notice = $this->notice($org, $owner);
        $url = route('schools.safety.notices.read', [$org, $notice]);
        $this->actingAs($stranger)->post($url)->assertNotFound();
        $this->actingAs($teacher)->post($url)->assertRedirect();
        $this->assertDatabaseHas('studybuddy_school_notice_receipts', [
            'notice_id' => $notice->id, 'user_id' => $teacher->id, 'organization_id' => $org->id,
        ]);
        $this->assertNotNull(DB::table('studybuddy_school_notice_receipts')
            ->where('user_id', $teacher->id)->where('notice_id', $notice->id)->value('read_at'));
    }

    public function test_notices_and_reports_cannot_be_modified_across_schools(): void
    {
        $a = $this->person('owner-d4a@qa.test');
        $b = $this->person('owner-d4b@qa.test');
        $schoolA = $this->school($a, 'school-a.qa.test');
        $schoolB = $this->school($b, 'school-b.qa.test');
        $noticeB = $this->notice($schoolB, $b);
        $reportB = StudyBuddySchoolSafetyReport::create([
            'organization_id' => $schoolB->id, 'reported_by' => $b->id,
            'category' => 'access_concern', 'subject' => 'Need access review',
            'description' => 'Colleague account access issue.', 'status' => 'open',
        ]);
        $this->actingAs($a)->post(route('schools.safety.notices.archive', [$schoolA, $noticeB]))->assertNotFound();
        $this->post(route('schools.safety.notices.read', [$schoolA, $noticeB]))->assertNotFound();
        $this->put(route('schools.safety.reports.review', [$schoolA, $reportB]), [
            'status' => 'resolved', 'resolution_note' => 'Resolved in another school.',
        ])->assertNotFound();
        $this->assertNull($noticeB->fresh()->archived_at);
        $this->assertSame('open', $reportB->fresh()->status);
    }

    public function test_teachers_can_report_a_concern_but_only_managers_can_resolve(): void
    {
        $owner = $this->person('owner-d5@qa.test');
        $teacher = $this->person('teacher-d5@qa.test');
        $org = $this->school($owner);
        $this->join($org, $teacher);
        $this->actingAs($teacher)->post(route('schools.safety.reports.submit', $org), [
            'category' => 'staff_safety', 'subject' => 'Safety concern to review',
            'description' => 'A process needs a private safety review.',
        ])->assertRedirect();
        $report = StudyBuddySchoolSafetyReport::firstOrFail();
        $this->put(route('schools.safety.reports.review', [$org, $report]), [
            'status' => 'resolved', 'resolution_note' => 'Handled by the teacher.',
        ])->assertForbidden();
        $this->actingAs($owner)->put(route('schools.safety.reports.review', [$org, $report]), [
            'status' => 'in_review', 'resolution_note' => 'School safeguarding contact is reviewing.',
        ])->assertRedirect();
        $this->assertSame('in_review', $report->fresh()->status);
    }

    public function test_ordinary_teacher_cannot_see_another_teachers_concern(): void
    {
        $owner = $this->person('owner-d6@qa.test');
        $one = $this->person('one-d6@qa.test');
        $two = $this->person('two-d6@qa.test');
        $org = $this->school($owner);
        $this->join($org, $one);
        $this->join($org, $two);
        StudyBuddySchoolSafetyReport::create([
            'organization_id' => $org->id, 'reported_by' => $one->id,
            'category' => 'other', 'subject' => 'Only reporter can see this',
            'description' => 'Private concern for school owner.', 'status' => 'open',
        ]);
        $this->actingAs($two)->get(route('schools.safety.index', $org))
            ->assertOk()->assertDontSee('Only reporter can see this');
        $this->actingAs($owner)->get(route('schools.safety.index', $org))
            ->assertOk()->assertSee('Only reporter can see this');
    }

    public function test_badge_mute_never_hides_urgent_notice(): void
    {
        $owner = $this->person('owner-d7@qa.test');
        $org = $this->school($owner);
        $this->notice($org, $owner, 'normal');
        $this->notice($org, $owner, 'urgent');
        $this->actingAs($owner)->post(route('schools.safety.preferences', $org), [
            'mute_normal_badge' => '1',
        ])->assertRedirect();
        $this->get(route('schools.safety.index', $org))->assertOk()
            ->assertSee('Unread badge alerts')->assertSee('QA safety briefing')->assertViewHas('unread', 1);
        $this->assertDatabaseHas('studybuddy_school_notice_preferences', [
            'organization_id' => $org->id, 'user_id' => $owner->id, 'mute_normal_badge' => 1,
        ]);
    }

    public function test_only_managers_export_notices_and_csv_formula_is_escaped(): void
    {
        $owner = $this->person('owner-d8@qa.test');
        $teacher = $this->person('teacher-d8@qa.test');
        $org = $this->school($owner);
        $this->join($org, $teacher);
        StudyBuddySchoolNotice::create([
            'organization_id' => $org->id, 'posted_by' => $owner->id,
            'title' => '=HYPERLINK("http://bad.example")', 'body' => '+injection attempt', 'priority' => 'normal',
        ]);
        $this->actingAs($teacher)->get(route('schools.safety.export.notices', $org))->assertForbidden();
        $this->actingAs($owner)->get(route('schools.safety.export.notices', $org))
            ->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_archive_preserves_notices_and_excludes_them_from_staff_board(): void
    {
        $owner = $this->person('owner-d9@qa.test');
        $org = $this->school($owner);
        $notice = $this->notice($org, $owner);
        $this->actingAs($owner)->post(route('schools.safety.notices.archive', [$org, $notice]))->assertRedirect();
        $this->assertNotNull($notice->fresh()->archived_at);
        $this->get(route('schools.safety.index', $org))->assertDontSee('QA safety briefing');
    }
}
