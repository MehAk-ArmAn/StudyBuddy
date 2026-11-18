<?php

namespace App\Http\Controllers;

use App\Models\SchoolOrganization;
use App\Models\SchoolOrganizationMember;
use App\Models\StudyBuddySchoolNotice;
use App\Models\StudyBuddySchoolSafetyReport;
use App\Models\StudyBuddySecurityEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * School-scope safety intake and in-app staff notice centre.
 * No read permission on message bodies or cross-school conversations.
 */
class StudyBuddySchoolSafetyCenterController extends Controller
{
    private function member(Request $request, SchoolOrganization $organization): SchoolOrganizationMember
    {
        abort_unless($organization->status === 'approved', 403);
        return SchoolOrganizationMember::query()
            ->where('organization_id', $organization->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'active')->firstOrFail();
    }

    private function manager(SchoolOrganizationMember $member): void
    {
        abort_unless(in_array($member->role, ['owner', 'school_admin'], true), 403);
    }

    private function scopedNotice(SchoolOrganization $organization, StudyBuddySchoolNotice $notice): StudyBuddySchoolNotice
    {
        abort_unless($notice->organization_id === $organization->id && $notice->archived_at === null, 404);
        return $notice;
    }

    private function scopedReport(SchoolOrganization $organization, StudyBuddySchoolSafetyReport $report): StudyBuddySchoolSafetyReport
    {
        abort_unless($report->organization_id === $organization->id, 404);
        return $report;
    }

    public function index(Request $request, SchoolOrganization $organization): View
    {
        $member = $this->member($request, $organization);
        $manager = in_array($member->role, ['owner', 'school_admin'], true);
        $notices = StudyBuddySchoolNotice::query()->with('poster:id,name')
            ->where('organization_id', $organization->id)->whereNull('archived_at')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->latest()->limit(80)->get();
        $read = DB::table('studybuddy_school_notice_receipts')
            ->where('organization_id', $organization->id)
            ->where('user_id', $request->user()->id)
            ->whereIn('notice_id', $notices->pluck('id')->all())
            ->whereNotNull('read_at')->pluck('read_at', 'notice_id')->all();
        $mute = (bool) DB::table('studybuddy_school_notice_preferences')
            ->where('organization_id', $organization->id)
            ->where('user_id', $request->user()->id)->value('mute_normal_badge');
        $unread = $notices->filter(fn ($notice) => !isset($read[$notice->id])
            && (!$mute || $notice->priority === 'urgent'))->count();
        $reports = StudyBuddySchoolSafetyReport::query()->with(['reporter:id,name', 'handler:id,name'])
            ->where('organization_id', $organization->id)
            ->when(!$manager, fn ($query) => $query->where('reported_by', $request->user()->id))
            ->latest()->limit(50)->get();
        return view('studybuddy.p11d.safety-center', compact(
            'organization', 'manager', 'notices', 'read', 'mute', 'unread', 'reports'
        ));
    }

    public function postNotice(Request $request, SchoolOrganization $organization): RedirectResponse
    {
        $this->manager($this->member($request, $organization));
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:140'],
            'body' => ['required', 'string', 'min:5', 'max:2500'],
            'priority' => ['required', Rule::in(['normal', 'urgent'])],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);
        $staff = SchoolOrganizationMember::query()->where('organization_id', $organization->id)
            ->where('status', 'active')->pluck('user_id')->unique()->values();
        if ($staff->count() > 1500) {
            return back()->withErrors(['title' => 'Contact platform support for schools with more than 1,500 staff members.']);
        }
        $notice = DB::transaction(function () use ($data, $request, $organization, $staff) {
            $notice = StudyBuddySchoolNotice::create($data + [
                'organization_id' => $organization->id, 'posted_by' => $request->user()->id,
            ]);
            if ($staff->isNotEmpty()) {
                $stamp = now();
                DB::table('studybuddy_school_notice_receipts')->insert(
                    $staff->map(fn ($uid) => [
                        'notice_id' => $notice->id, 'organization_id' => $organization->id,
                        'user_id' => $uid, 'read_at' => null,
                        'created_at' => $stamp, 'updated_at' => $stamp,
                    ])->all()
                );
            }
            return $notice;
        });
        StudyBuddySecurityEvent::record('school_staff_notice_posted', 'info', $request->user()->id, [
            'organization_id' => $organization->id, 'notice_id' => $notice->id, 'priority' => $notice->priority,
        ]);
        return redirect()->route('schools.safety.index', $organization)->with('status', 'Staff notice posted inside this school.');
    }

    public function markRead(Request $request, SchoolOrganization $organization, StudyBuddySchoolNotice $notice): RedirectResponse
    {
        $this->member($request, $organization);
        $this->scopedNotice($organization, $notice);
        DB::table('studybuddy_school_notice_receipts')->updateOrInsert(
            ['notice_id' => $notice->id, 'user_id' => $request->user()->id],
            ['organization_id' => $organization->id, 'read_at' => now(), 'updated_at' => now(), 'created_at' => now()]
        );
        return redirect()->route('schools.safety.index', $organization)->with('status', 'Notice acknowledged.');
    }

    public function archiveNotice(Request $request, SchoolOrganization $organization, StudyBuddySchoolNotice $notice): RedirectResponse
    {
        $this->manager($this->member($request, $organization));
        $this->scopedNotice($organization, $notice)->update(['archived_at' => now()]);
        StudyBuddySecurityEvent::record('school_staff_notice_archived', 'warning', $request->user()->id, [
            'organization_id' => $organization->id, 'notice_id' => $notice->id,
        ]);
        return redirect()->route('schools.safety.index', $organization)->with('status', 'Notice archived; history preserved.');
    }

    public function updatePreference(Request $request, SchoolOrganization $organization): RedirectResponse
    {
        $this->member($request, $organization);
        $data = $request->validate(['mute_normal_badge' => ['required', Rule::in(['0', '1'])]]);
        DB::table('studybuddy_school_notice_preferences')->updateOrInsert(
            ['organization_id' => $organization->id, 'user_id' => $request->user()->id],
            ['mute_normal_badge' => $data['mute_normal_badge'] === '1', 'updated_at' => now(), 'created_at' => now()]
        );
        return redirect()->route('schools.safety.index', $organization)->with('status', 'Unread badge preference saved. All notices remain accessible.');
    }

    public function reportIssue(Request $request, SchoolOrganization $organization): RedirectResponse
    {
        $this->member($request, $organization);
        $data = $request->validate([
            'category' => ['required', Rule::in(['inappropriate_message', 'access_concern', 'staff_safety', 'other'])],
            'subject' => ['required', 'string', 'min:5', 'max:150'],
            'description' => ['required', 'string', 'min:10', 'max:1600'],
        ]);
        $report = StudyBuddySchoolSafetyReport::create($data + [
            'organization_id' => $organization->id, 'reported_by' => $request->user()->id,
            'status' => 'open',
        ]);
        StudyBuddySecurityEvent::record('school_staff_safety_report_created', 'warning', $request->user()->id, [
            'organization_id' => $organization->id, 'report_id' => $report->id, 'category' => $report->category,
        ]);
        return redirect()->route('schools.safety.index', $organization)->with('status', 'Concern submitted to this school’s management.');
    }

    public function reviewIssue(Request $request, SchoolOrganization $organization, StudyBuddySchoolSafetyReport $report): RedirectResponse
    {
        $this->manager($this->member($request, $organization));
        $this->scopedReport($organization, $report);
        $data = $request->validate([
            'status' => ['required', Rule::in(['in_review', 'resolved', 'dismissed'])],
            'resolution_note' => ['required', 'string', 'min:5', 'max:1600'],
        ]);
        DB::transaction(function () use ($organization, $report, $request, $data): void {
            $locked = StudyBuddySchoolSafetyReport::query()->whereKey($report->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->organization_id === $organization->id, 404);
            $locked->update([
                'status' => $data['status'], 'resolution_note' => $data['resolution_note'],
                'handled_by' => $request->user()->id,
                'resolved_at' => in_array($data['status'], ['resolved', 'dismissed'], true) ? now() : null,
            ]);
        });
        StudyBuddySecurityEvent::record('school_staff_safety_report_reviewed', 'warning', $request->user()->id, [
            'organization_id' => $organization->id, 'report_id' => $report->id, 'status' => $data['status'],
        ]);
        return redirect()->route('schools.safety.index', $organization)->with('status', 'Case status updated.');
    }

    private static function csvCell(mixed $value): string
    {
        $value = (string) ($value ?? '');
        // Spreadsheet formula injection defence, including leading whitespace.
        return preg_match('/^\s*[=+\-@\t\r]/u', $value) === 1 ? "'".$value : $value;
    }

    public function exportNotices(Request $request, SchoolOrganization $organization): StreamedResponse
    {
        $this->manager($this->member($request, $organization));
        StudyBuddySecurityEvent::record('school_staff_notices_exported', 'warning', $request->user()->id, [
            'organization_id' => $organization->id,
        ]);
        return response()->streamDownload(function () use ($organization): void {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, ['notice_id', 'priority', 'title', 'body', 'posted_by_user_id', 'created_at', 'expires_at', 'archived_at']);
            StudyBuddySchoolNotice::query()->where('organization_id', $organization->id)->orderBy('id')
                ->chunkById(100, function ($notices) use ($handle): void {
                    foreach ($notices as $notice) {
                        fputcsv($handle, array_map(self::csvCell(...), [
                            $notice->id, $notice->priority, $notice->title, $notice->body,
                            $notice->posted_by, $notice->created_at, $notice->expires_at, $notice->archived_at,
                        ]));
                    }
                });
            fclose($handle);
        }, 'school-notices-'.$organization->id.'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
