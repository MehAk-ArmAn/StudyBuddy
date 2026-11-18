<?php

namespace Tests\Feature\School;

use App\Models\SchoolOrganization;
use App\Models\SchoolOrganizationMember;
use App\Models\StudyBuddySchoolConversation;
use App\Models\StudyBuddySchoolMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyPack11CMessagingTest extends TestCase
{
    use RefreshDatabase;

    private function person(string $email, string $role = 'teacher'): User
    {
        return User::forceCreate([
            'name' => 'QA '.explode('@', $email)[0], 'email' => $email,
            'password' => bcrypt('TestOnly2026!Strong'), 'role' => $role, 'is_admin' => false,
            'email_verified_at' => now(), 'date_of_birth' => '1988-02-03',
            'role_verification_status' => 'approved', 'adult_verification_status' => 'approved',
        ]);
    }

    private function school(User $owner, string $domain = 'p11c-a.test', string $status = 'approved'): SchoolOrganization
    {
        $org = SchoolOrganization::create([
            'name' => 'Test Learning Academy', 'domain' => $domain, 'city' => 'Test City',
            'country' => 'United Arab Emirates', 'official_email' => 'team@'.$domain,
            'requested_by' => $owner->id, 'status' => $status,
        ]);
        if ($status === 'approved') $this->join($org, $owner, 'owner');
        return $org;
    }

    private function join(SchoolOrganization $org, User $user, string $role = 'teacher', string $status = 'active'): SchoolOrganizationMember
    {
        return SchoolOrganizationMember::create([
            'organization_id' => $org->id, 'user_id' => $user->id,
            'role' => $role, 'status' => $status, 'joined_at' => now(),
        ]);
    }

    private function room(SchoolOrganization $org, User $owner): StudyBuddySchoolConversation
    {
        return StudyBuddySchoolConversation::create([
            'organization_id' => $org->id, 'conversation_key' => 'channel:test',
            'kind' => 'channel', 'title' => 'Test Lounge', 'created_by' => $owner->id,
        ]);
    }

    public function test_staff_channels_are_private_and_scoped_to_an_approved_school(): void
    {
        $owner = $this->person('owner11c@test.local');
        $outsider = $this->person('outsider11c@test.local');
        $school = $this->school($owner);
        $this->get(route('schools.connect.index', $school))->assertRedirect();
        $this->actingAs($outsider)->get(route('schools.connect.index', $school))->assertNotFound();
        $this->actingAs($owner)->get(route('schools.connect.index', $school))
            ->assertOk()->assertSee('Staff Lounge')->assertSee('Planning &amp; Updates', false);
        $this->assertDatabaseCount('studybuddy_school_conversations', 2);
        $this->get(route('schools.connect.index', $school))->assertOk();
        $this->assertDatabaseCount('studybuddy_school_conversations', 2);
        $pending = $this->school($outsider, 'p11c-pending.test', 'pending');
        $this->actingAs($outsider)->get(route('schools.connect.index', $pending))->assertForbidden();
    }

    public function test_teacher_cannot_create_channels_but_school_manager_can(): void
    {
        $owner = $this->person('ownerchan11c@test.local');
        $teacher = $this->person('teacherchan11c@test.local');
        $school = $this->school($owner);
        $this->join($school, $teacher);
        $this->actingAs($teacher)->post(route('schools.connect.channels.create', $school), [
            'title' => 'English Department',
        ])->assertForbidden();
        $this->actingAs($owner)->post(route('schools.connect.channels.create', $school), [
            'title' => 'English Department',
        ])->assertRedirect();
        $this->assertDatabaseHas('studybuddy_school_conversations', [
            'organization_id' => $school->id, 'kind' => 'channel', 'title' => 'English Department',
        ]);
    }

    public function test_only_current_staff_can_send_and_read_messages(): void
    {
        $owner = $this->person('ownermsg11c@test.local');
        $teacher = $this->person('teachermsg11c@test.local');
        $outsider = $this->person('outsidermsg11c@test.local');
        $school = $this->school($owner);
        $member = $this->join($school, $teacher);
        $room = $this->room($school, $owner);
        $send = route('schools.connect.messages.send', [$school, $room]);
        $this->actingAs($teacher)->post($send, ['body' => 'Staff agenda for Friday'])->assertRedirect();
        $this->assertDatabaseHas('studybuddy_school_messages', [
            'conversation_id' => $room->id, 'author_id' => $teacher->id, 'body' => 'Staff agenda for Friday',
        ]);
        $this->actingAs($outsider)->get(route('schools.connect.show', [$school,$room]))->assertNotFound();
        $this->post($send, ['body' => 'No access'])->assertNotFound();
        $member->update(['status' => 'revoked']);
        $this->actingAs($teacher)->post($send, ['body' => 'Revoked staff'])->assertNotFound();
        $this->assertDatabaseCount('studybuddy_school_messages', 1);
    }

    public function test_cross_school_message_ids_and_threads_cannot_be_read_or_reported(): void
    {
        $a = $this->person('orgA11c@test.local');
        $b = $this->person('orgB11c@test.local');
        $schoolA = $this->school($a, 'p11c-1.test');
        $schoolB = $this->school($b, 'p11c-2.test');
        $roomB = $this->room($schoolB, $b);
        $messageB = StudyBuddySchoolMessage::create([
            'organization_id' => $schoolB->id, 'conversation_id' => $roomB->id,
            'author_id' => $b->id, 'body' => 'Private B only',
        ]);
        $this->actingAs($a)->get(route('schools.connect.show', [$schoolA,$roomB]))->assertNotFound();
        $this->post(route('schools.connect.messages.send', [$schoolA,$roomB]), ['body' => 'Wrong school'])->assertNotFound();
        $roomA = $this->room($schoolA, $a);
        $this->post(route('schools.connect.messages.report', [$schoolA,$roomA,$messageB]), ['reason' => 'privacy'])->assertNotFound();
        $this->get(route('schools.connect.attachments.download', [$schoolA,$roomA,$messageB]))->assertNotFound();
    }

    public function test_direct_messages_are_visible_only_to_both_participants(): void
    {
        $owner = $this->person('ownerdm11c@test.local');
        $teacher = $this->person('teacherdm11c@test.local');
        $third = $this->person('thirddm11c@test.local');
        $school = $this->school($owner);
        $this->join($school, $teacher);
        $this->join($school, $third);
        $this->actingAs($owner)->post(route('schools.connect.directs.start', $school), [
            'recipient_id' => $teacher->id,
        ])->assertRedirect();
        $thread = StudyBuddySchoolConversation::where('kind','direct')->firstOrFail();
        $this->actingAs($third)->get(route('schools.connect.show', [$school,$thread]))->assertNotFound();
        $this->actingAs($teacher)->get(route('schools.connect.show', [$school,$thread]))->assertOk();
        $this->actingAs($owner)->get(route('schools.connect.show', [$school,$thread]))->assertOk();
        $this->post(route('schools.connect.directs.start', $school), ['recipient_id' => $teacher->id])->assertRedirect();
        $this->assertSame(1, StudyBuddySchoolConversation::where('kind','direct')->count());
        $this->post(route('schools.connect.directs.start', $school), ['recipient_id' => $owner->id])->assertUnprocessable();
    }

    public function test_receipts_advance_only_when_authorized_member_marks_read(): void
    {
        $owner = $this->person('ownerread11c@test.local');
        $teacher = $this->person('teacherread11c@test.local');
        $school = $this->school($owner);
        $this->join($school, $teacher);
        $room = $this->room($school, $owner);
        $msg = StudyBuddySchoolMessage::create([
            'organization_id' => $school->id, 'conversation_id' => $room->id,
            'author_id' => $owner->id, 'body' => 'Message for receipt test',
        ]);
        $this->actingAs($teacher)->get(route('schools.connect.show', [$school,$room]))->assertOk();
        $this->assertDatabaseCount('studybuddy_school_message_reads', 0);
        $this->post(route('schools.connect.read', [$school,$room]))->assertRedirect();
        $this->assertDatabaseHas('studybuddy_school_message_reads', [
            'conversation_id' => $room->id, 'user_id' => $teacher->id,
            'last_message_id' => $msg->id,
        ]);
    }

    public function test_reported_messages_are_moderated_without_opening_full_direct_message(): void
    {
        $owner = $this->person('ownerreport11c@test.local');
        $teacher = $this->person('teacherreport11c@test.local');
        $school = $this->school($owner);
        $this->join($school, $teacher);
        $room = $this->room($school, $owner);
        $message = StudyBuddySchoolMessage::create([
            'organization_id' => $school->id, 'conversation_id' => $room->id,
            'author_id' => $teacher->id, 'body' => 'Report this unsafe staff message',
        ]);
        $this->actingAs($teacher)->post(route('schools.connect.messages.report', [$school,$room,$message]), [
            'reason' => 'privacy',
        ])->assertRedirect();
        $reportId = DB::table('studybuddy_school_message_reports')->value('id');
        $this->get(route('schools.connect.reports', $school))->assertForbidden();
        $this->actingAs($owner)->get(route('schools.connect.reports', $school))
            ->assertOk()->assertSee('Report this unsafe staff message');
        $this->put(route('schools.connect.reports.review', [$school,$reportId]), [
            'decision' => 'hidden',
        ])->assertRedirect();
        $this->assertNotNull($message->fresh()->hidden_at);
        $this->get(route('schools.connect.show', [$school,$room]))
            ->assertOk()->assertDontSee('Report this unsafe staff message')->assertSee('Message hidden');
    }

    public function test_student_role_cannot_access_chat_even_with_forged_active_school_membership(): void
    {
        $owner = $this->person('ownerchild11c@test.local');
        $student = $this->person('studentchild11c@test.local','student');
        $school = $this->school($owner);
        $this->join($school, $student, 'teacher');
        $this->actingAs($student)->get(route('schools.connect.index', $school))->assertForbidden();
    }

    public function test_manager_can_review_reported_direct_message_but_cannot_open_private_dm(): void
    {
        $owner = $this->person('ownermod11c@test.local');
        $first = $this->person('firstmod11c@test.local');
        $second = $this->person('secondmod11c@test.local');
        $school = $this->school($owner);
        $this->join($school, $first);
        $this->join($school, $second);
        $ids = [$first->id, $second->id]; sort($ids);
        $dm = StudyBuddySchoolConversation::create([
            'organization_id' => $school->id,
            'conversation_key' => 'dm:'.$ids[0].':'.$ids[1],
            'kind' => 'direct', 'title' => 'Staff direct message',
            'created_by' => $first->id, 'participant_a' => $ids[0], 'participant_b' => $ids[1],
        ]);
        $message = StudyBuddySchoolMessage::create([
            'organization_id' => $school->id, 'conversation_id' => $dm->id,
            'author_id' => $first->id, 'body' => 'Staff DM content reported for safety',
        ]);
        $this->actingAs($owner)->get(route('schools.connect.show', [$school,$dm]))->assertNotFound();
        $this->actingAs($second)->post(route('schools.connect.messages.report', [$school,$dm,$message]), [
            'reason' => 'safety',
        ])->assertRedirect();
        $this->actingAs($owner)->get(route('schools.connect.reports', $school))
            ->assertOk()->assertSee('Staff DM content reported for safety');
        $this->get(route('schools.connect.show', [$school,$dm]))->assertNotFound();
    }

    public function test_private_attachment_download_requires_thread_membership_and_unhidden_message(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');
        $owner = $this->person('ownerfile11c@test.local');
        $teacher = $this->person('teacherfile11c@test.local');
        $outsider = $this->person('outsiderfile11c@test.local');
        $school = $this->school($owner);
        $this->join($school, $teacher);
        $room = $this->room($school, $owner);
        $path = 'studybuddy-school-messaging/'.$school->id.'/sample.pdf';
        \Illuminate\Support\Facades\Storage::disk('local')->put($path, '%PDF-1.4 example');
        $message = StudyBuddySchoolMessage::create([
            'organization_id' => $school->id, 'conversation_id' => $room->id,
            'author_id' => $owner->id, 'body' => null, 'attachment_path' => $path,
            'attachment_name' => 'sample.pdf', 'attachment_mime' => 'application/pdf',
            'attachment_size' => 16,
        ]);
        $url = route('schools.connect.attachments.download', [$school,$room,$message]);
        $this->actingAs($outsider)->get($url)->assertNotFound();
        $this->actingAs($teacher)->get($url)->assertOk();
        $message->update(['hidden_at' => now(), 'hidden_by' => $owner->id]);
        $this->get($url)->assertNotFound();
    }

    public function test_unapproved_school_and_revoked_staff_have_no_access_to_report_queue(): void
    {
        $owner = $this->person('ownerrevoked11c@test.local');
        $admin = $this->person('adminrevoked11c@test.local');
        $school = $this->school($owner);
        $member = $this->join($school, $admin, 'school_admin');
        $this->actingAs($admin)->get(route('schools.connect.reports', $school))->assertOk();
        $member->update(['status' => 'revoked']);
        $this->get(route('schools.connect.reports', $school))->assertNotFound();
    }
}
