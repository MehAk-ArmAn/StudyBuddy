<?php

/**
 * STUDYBUDDY-TEAM-HANDOVER-2026
 * Developer orientation: P11B School Board: check active school membership and organization scope before every task mutation. Navigation visibility is not authorization.
 *
 * Documentation-only annotation; no runtime behavior is changed here.
 */


namespace App\Http\Controllers;

use App\Models\SchoolOrganization;
use App\Models\SchoolOrganizationMember;
use App\Models\StudyBuddySchoolTask;
use App\Models\StudyBuddySecurityEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** School-only staff coordination. No learner or PCL platform overrides. */
class StudyBuddySchoolBoardController extends Controller
{
    private const LANES = ['planned', 'doing', 'review', 'done'];

    private function membership(Request $request, SchoolOrganization $organization): SchoolOrganizationMember
    {
        abort_unless($organization->status === 'approved', 403);
        return SchoolOrganizationMember::query()->where('organization_id', $organization->id)
            ->where('user_id', $request->user()->id)->where('status', 'active')->firstOrFail();
    }

    private function manager(SchoolOrganizationMember $member): void
    {
        abort_unless(in_array($member->role, ['owner', 'school_admin'], true), 403);
    }

    private function target(SchoolOrganization $organization, StudyBuddySchoolTask $task): StudyBuddySchoolTask
    {
        abort_unless($task->organization_id === $organization->id && $task->archived_at === null, 404);
        return $task;
    }

    private function assigneeRule(SchoolOrganization $organization)
    {
        return Rule::exists('studybuddy_organization_members', 'user_id')
            ->where(fn ($query) => $query->where('organization_id', $organization->id)->where('status', 'active'));
    }

    public function index(Request $request, SchoolOrganization $organization): View
    {
        $member = $this->membership($request, $organization);
        $manager = in_array($member->role, ['owner', 'school_admin'], true);
        $tasks = StudyBuddySchoolTask::query()->with(['assignee:id,name', 'creator:id,name'])
            ->where('organization_id', $organization->id)->whereNull('archived_at')
            ->orderByRaw("case lane when 'doing' then 0 when 'review' then 1 when 'planned' then 2 else 3 end")
            ->orderBy('due_on')->orderByDesc('updated_at')->limit(120)->get();
        $staff = SchoolOrganizationMember::query()->with('user:id,name')
            ->where('organization_id', $organization->id)->where('status', 'active')->get();
        return view('studybuddy.p11b.board', [
            'organization' => $organization, 'manager' => $manager, 'staff' => $staff,
            'tasks' => $tasks, 'lanes' => self::LANES,
        ]);
    }

    public function store(Request $request, SchoolOrganization $organization): RedirectResponse
    {
        $this->manager($this->membership($request, $organization));
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'details' => ['nullable', 'string', 'max:4000'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high'])],
            'lane' => ['required', Rule::in(self::LANES)],
            'due_on' => ['nullable', 'date_format:Y-m-d'],
            'assigned_to' => ['nullable', 'integer', $this->assigneeRule($organization)],
        ]);
        if (StudyBuddySchoolTask::where('organization_id', $organization->id)->whereNull('archived_at')->count() >= 120) {
            return back()->withErrors(['title' => 'This school board has reached its 120 active task limit. Archive completed tasks before adding more.'])->withInput();
        }
        $task = StudyBuddySchoolTask::create($data + [
            'organization_id' => $organization->id, 'created_by' => $request->user()->id,
        ]);
        StudyBuddySecurityEvent::record('school_task_created', 'info', $request->user()->id,
            ['organization_id' => $organization->id, 'task_id' => $task->id]);
        return redirect()->route('schools.board', $organization)->with('status', 'Staff task created.');
    }

    public function update(Request $request, SchoolOrganization $organization, StudyBuddySchoolTask $task): RedirectResponse
    {
        $member = $this->membership($request, $organization);
        $this->target($organization, $task);
        $manager = in_array($member->role, ['owner', 'school_admin'], true);
        abort_unless($manager || $task->assigned_to === $request->user()->id, 403);
        if ($manager) {
            $data = $request->validate([
                'title' => ['required', 'string', 'min:3', 'max:160'],
                'details' => ['nullable', 'string', 'max:4000'],
                'priority' => ['required', Rule::in(['low', 'normal', 'high'])],
                'lane' => ['required', Rule::in(self::LANES)],
                'due_on' => ['nullable', 'date_format:Y-m-d'],
                'assigned_to' => ['nullable', 'integer', $this->assigneeRule($organization)],
            ]);
        } else {
            // A teacher may ONLY advance or return their own task's lane.
            $data = $request->validate(['lane' => ['required', Rule::in(self::LANES)]]);
        }
        DB::transaction(function () use ($task, $organization, $request, $data, $manager): void {
            $locked = StudyBuddySchoolTask::query()->whereKey($task->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->organization_id === $organization->id && $locked->archived_at === null, 404);
            abort_unless($manager || $locked->assigned_to === $request->user()->id, 403);
            if (isset($data['assigned_to'])) {
                abort_unless(SchoolOrganizationMember::query()->where('organization_id', $organization->id)
                    ->where('user_id', $data['assigned_to'])->where('status', 'active')->exists(), 422);
            }
            $locked->update($data);
        });
        StudyBuddySecurityEvent::record('school_task_updated', 'info', $request->user()->id,
            ['organization_id' => $organization->id, 'task_id' => $task->id]);
        return redirect()->route('schools.board', $organization)->with('status', 'Staff task updated.');
    }

    public function archive(Request $request, SchoolOrganization $organization, StudyBuddySchoolTask $task): RedirectResponse
    {
        $this->manager($this->membership($request, $organization));
        $this->target($organization, $task);
        $task->update(['archived_at' => now()]); // preserve history, no hard deletion
        StudyBuddySecurityEvent::record('school_task_archived', 'warning', $request->user()->id,
            ['organization_id' => $organization->id, 'task_id' => $task->id]);
        return redirect()->route('schools.board', $organization)->with('status', 'Task archived, not deleted.');
    }
}
