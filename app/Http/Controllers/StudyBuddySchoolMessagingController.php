<?php

namespace App\Http\Controllers;

use App\Models\SchoolOrganization;
use App\Models\SchoolOrganizationMember;
use App\Models\StudyBuddySchoolConversation;
use App\Models\StudyBuddySchoolMessage;
use App\Models\StudyBuddySecurityEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** P11C: explicit, staff-only private communication with no platform content override. */
class StudyBuddySchoolMessagingController extends Controller
{
    private function eligibleStaff(?User $user): bool
    {
        if (! $user || ! $user->hasVerifiedEmail()
            || ! in_array($user->normalizedRole(), ['teacher', 'parent', 'admin'], true)) return false;
        if (! $user->is_admin && $user->adult_verification_status !== 'approved'
            && ! ($user->date_of_birth && $user->date_of_birth->age >= 18)) return false;
        if ($user->normalizedRole() === 'teacher' && ! $user->is_admin
            && ! in_array($user->role_verification_status, ['verified', 'approved'], true)) return false;
        return true;
    }

    private function member(Request $request, SchoolOrganization $school): SchoolOrganizationMember
    {
        abort_unless($school->status === 'approved', 403);
        // Membership plus adult/verified account status are both necessary.
        abort_unless($this->eligibleStaff($request->user()), 403);
        return SchoolOrganizationMember::where('organization_id', $school->id)
            ->where('user_id', $request->user()->id)->where('status', 'active')->firstOrFail();
    }

    private function manager(SchoolOrganizationMember $member): void
    {
        abort_unless(in_array($member->role, ['owner', 'school_admin'], true), 403);
    }

    private function conversation(SchoolOrganization $school, StudyBuddySchoolConversation $conversation, int $userId): StudyBuddySchoolConversation
    {
        abort_unless($conversation->organization_id === $school->id, 404);
        if ($conversation->kind === 'direct') {
            abort_unless(in_array($userId, [(int) $conversation->participant_a, (int) $conversation->participant_b], true), 404);
            // A DM cannot be entered if the other participant has left the school.
            $other = $conversation->participant_a == $userId ? $conversation->participant_b : $conversation->participant_a;
            abort_unless(SchoolOrganizationMember::where('organization_id', $school->id)
                ->where('user_id', $other)->where('status', 'active')->exists()
                && $this->eligibleStaff(User::find($other)), 404);
        } else {
            abort_unless($conversation->kind === 'channel', 404);
        }
        return $conversation;
    }

    private function rooms(SchoolOrganization $school, int $userId): void
    {
        foreach (['general' => 'Staff Lounge', 'planning' => 'Planning & Updates'] as $key => $title) {
            StudyBuddySchoolConversation::firstOrCreate(
                ['organization_id' => $school->id, 'conversation_key' => 'channel:'.$key],
                ['kind' => 'channel', 'title' => $title, 'created_by' => $userId]
            );
        }
    }

    public function index(Request $request, SchoolOrganization $organization): View
    {
        $member = $this->member($request, $organization);
        $this->rooms($organization, $request->user()->id);
        return $this->render($organization, $member, $request->user()->id, null);
    }

    public function show(Request $request, SchoolOrganization $organization, StudyBuddySchoolConversation $conversation): View
    {
        $member = $this->member($request, $organization);
        $this->conversation($organization, $conversation, $request->user()->id);
        $this->rooms($organization, $request->user()->id);
        return $this->render($organization, $member, $request->user()->id, $conversation);
    }

    private function render(SchoolOrganization $school, SchoolOrganizationMember $member, int $userId, ?StudyBuddySchoolConversation $selected): View
    {
        $staff = SchoolOrganizationMember::with('user:id,name,email')
            ->where('organization_id', $school->id)->where('status', 'active')->get()
            ->filter(fn ($m) => $m->user !== null)->values();
        $threads = StudyBuddySchoolConversation::where('organization_id', $school->id)
            ->where(function ($query) use ($userId): void {
                $query->where('kind', 'channel')
                    ->orWhere(fn ($q) => $q->where('kind', 'direct')
                        ->where(fn ($p) => $p->where('participant_a', $userId)->orWhere('participant_b', $userId)));
            })->orderBy('kind')->orderBy('id')->get()
            ->filter(fn ($thread) => $thread->kind === 'channel' || $staff->contains('user_id', (int) ($thread->participant_a == $userId ? $thread->participant_b : $thread->participant_a)))
            ->values();
        $selected = $selected ?: $threads->first(fn ($thread) => $thread->conversation_key === 'channel:general') ?: $threads->first();
        $messages = $selected ? StudyBuddySchoolMessage::with('author:id,name')
            ->where('conversation_id', $selected->id)->orderByDesc('id')->limit(80)->get()->reverse()->values() : collect();
        $reads = $selected ? DB::table('studybuddy_school_message_reads')->where('conversation_id', $selected->id)
            ->pluck('last_message_id', 'user_id')->all() : [];
        $manager = in_array($member->role, ['owner', 'school_admin'], true);
        $pendingReports = $manager ? DB::table('studybuddy_school_message_reports')
            ->where('organization_id', $school->id)->where('status', 'pending')->count() : 0;
        return view('studybuddy.p11c.connect', [
            'organization' => $school, 'member' => $member, 'manager' => $manager,
            'staff' => $staff, 'threads' => $threads, 'selected' => $selected,
            'messages' => $messages, 'reads' => $reads, 'pendingReports' => $pendingReports,
        ]);
    }

    public function createChannel(Request $request, SchoolOrganization $organization): RedirectResponse
    {
        $this->manager($this->member($request, $organization));
        $data = $request->validate(['title' => ['required', 'string', 'min:3', 'max:70']]);
        abort_if(StudyBuddySchoolConversation::where('organization_id', $organization->id)->where('kind', 'channel')->count() >= 15,
            422, 'This school has reached its 15-channel limit.');
        $thread = StudyBuddySchoolConversation::create([
            'organization_id' => $organization->id, 'conversation_key' => 'channel:'.Str::lower(Str::random(20)),
            'kind' => 'channel', 'title' => trim($data['title']), 'created_by' => $request->user()->id,
        ]);
        return redirect()->route('schools.connect.show', [$organization, $thread])->with('status', 'Private staff channel created.');
    }

    public function startDirect(Request $request, SchoolOrganization $organization): RedirectResponse
    {
        $this->member($request, $organization);
        $data = $request->validate(['recipient_id' => ['required', 'integer', Rule::exists('studybuddy_organization_members', 'user_id')
            ->where(fn ($q) => $q->where('organization_id', $organization->id)->where('status', 'active'))]]);
        $self = (int) $request->user()->id;
        $recipientId = (int) $data['recipient_id'];
        abort_if($recipientId === $self, 422, 'You cannot message yourself.');
        $recipient = User::findOrFail($recipientId);
        abort_unless($this->eligibleStaff($recipient), 403);
        $pair = [$self, $recipientId]; sort($pair, SORT_NUMERIC);
        $thread = StudyBuddySchoolConversation::firstOrCreate(
            ['organization_id' => $organization->id, 'conversation_key' => 'dm:'.$pair[0].':'.$pair[1]],
            ['kind' => 'direct', 'title' => 'Staff direct message', 'created_by' => $self,
                'participant_a' => $pair[0], 'participant_b' => $pair[1]]
        );
        return redirect()->route('schools.connect.show', [$organization, $thread]);
    }

    public function send(Request $request, SchoolOrganization $organization, StudyBuddySchoolConversation $conversation): RedirectResponse
    {
        $this->member($request, $organization);
        $this->conversation($organization, $conversation, $request->user()->id);
        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:3000'],
            'attachment' => ['nullable', 'file', 'max:4096', 'mimes:png,jpg,jpeg,pdf', 'mimetypes:image/png,image/jpeg,application/pdf'],
        ]);
        $body = trim((string) ($data['body'] ?? ''));
        $file = $request->file('attachment');
        if ($body === '' && ! $file) return back()->withErrors(['body' => 'Write a message or attach one supported file.']);
        if (StudyBuddySchoolMessage::where('conversation_id', $conversation->id)->count() >= 25000) {
            return back()->withErrors(['body' => 'This conversation has reached its storage limit. Contact your school administrator.']);
        }
        $stored = null;
        try {
            $attributes = [
                'organization_id' => $organization->id, 'conversation_id' => $conversation->id,
                'author_id' => $request->user()->id, 'body' => $body ?: null,
            ];
            if ($file) {
                // Private local storage only. File URLs are NEVER public.
                $stored = $file->store('studybuddy-school-messaging/'.$organization->id, 'local');
                if (! $stored) throw new \RuntimeException('Private storage unavailable');
                $attributes += [
                    'attachment_path' => $stored,
                    'attachment_name' => Str::limit(preg_replace('/[^A-Za-z0-9._-]/', '_', basename($file->getClientOriginalName())), 180, ''),
                    'attachment_mime' => $file->getMimeType(), 'attachment_size' => $file->getSize(),
                ];
            }
            StudyBuddySchoolMessage::create($attributes);
        } catch (\Throwable $e) {
            if ($stored) Storage::disk('local')->delete($stored);
            return back()->withErrors(['attachment' => 'Unable to safely save this message. Please retry.']);
        }
        return redirect()->route('schools.connect.show', [$organization, $conversation])->with('status', 'Message sent.');
    }

    public function markRead(Request $request, SchoolOrganization $organization, StudyBuddySchoolConversation $conversation): RedirectResponse
    {
        $this->member($request, $organization);
        $this->conversation($organization, $conversation, $request->user()->id);
        DB::transaction(function () use ($conversation, $request): void {
            $last = (int) (StudyBuddySchoolMessage::where('conversation_id', $conversation->id)->max('id') ?? 0);
            $query = DB::table('studybuddy_school_message_reads')
                ->where('conversation_id', $conversation->id)->where('user_id', $request->user()->id);
            $existing = $query->lockForUpdate()->first();
            if ($existing) {
                $query->update(['last_message_id' => max($last, (int) $existing->last_message_id), 'read_at' => now()]);
            } else {
                DB::table('studybuddy_school_message_reads')->insert([
                    'conversation_id' => $conversation->id, 'user_id' => $request->user()->id,
                    'last_message_id' => $last, 'read_at' => now(),
                ]);
            }
        });
        return back()->with('status', 'Read receipt updated.');
    }

    private function messageFor(SchoolOrganization $school, StudyBuddySchoolConversation $conversation, StudyBuddySchoolMessage $message): void
    {
        abort_unless($message->organization_id === $school->id && $message->conversation_id === $conversation->id, 404);
    }

    public function download(Request $request, SchoolOrganization $organization, StudyBuddySchoolConversation $conversation, StudyBuddySchoolMessage $message): StreamedResponse
    {
        $this->member($request, $organization);
        $this->conversation($organization, $conversation, $request->user()->id);
        $this->messageFor($organization, $conversation, $message);
        abort_if($message->hidden_at || ! $message->attachment_path, 404);
        $path = $message->attachment_path;
        abort_unless(Str::startsWith($path, 'studybuddy-school-messaging/'.$organization->id.'/')
            && Storage::disk('local')->exists($path), 404);
        return Storage::disk('local')->download($path, $message->attachment_name ?: 'school-attachment',
            ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store']);
    }

    public function report(Request $request, SchoolOrganization $organization, StudyBuddySchoolConversation $conversation, StudyBuddySchoolMessage $message): RedirectResponse
    {
        $this->member($request, $organization);
        $this->conversation($organization, $conversation, $request->user()->id);
        $this->messageFor($organization, $conversation, $message);
        abort_if($message->hidden_at, 404);
        $data = $request->validate(['reason' => ['required', Rule::in(['safety', 'harassment', 'privacy', 'other'])]]);
        DB::table('studybuddy_school_message_reports')->updateOrInsert(
            ['message_id' => $message->id, 'reported_by' => $request->user()->id],
            ['organization_id' => $organization->id, 'reason' => $data['reason'],
                'status' => 'pending', 'reviewed_by' => null, 'reviewed_at' => null, 'updated_at' => now(), 'created_at' => now()]
        );
        StudyBuddySecurityEvent::record('school_message_reported', 'warning', $request->user()->id,
            ['organization_id' => $organization->id, 'message_id' => $message->id]);
        return back()->with('status', 'Reported to authorized school managers for review.');
    }

    public function reports(Request $request, SchoolOrganization $organization): View
    {
        $member = $this->member($request, $organization);
        $this->manager($member);
        $reports = DB::table('studybuddy_school_message_reports as r')
            ->join('studybuddy_school_messages as m', 'm.id', '=', 'r.message_id')
            ->leftJoin('users as u', 'u.id', '=', 'r.reported_by')
            ->where('r.organization_id', $organization->id)
            ->where('m.organization_id', $organization->id)
            ->select('r.*', 'm.body as reported_body', 'm.hidden_at', 'u.name as reporter_name')
            ->orderByRaw("case when r.status = 'pending' then 0 else 1 end")
            ->orderByDesc('r.id')->limit(100)->get();
        return view('studybuddy.p11c.reports', compact('organization', 'reports'));
    }

    public function reviewReport(Request $request, SchoolOrganization $organization, int $report): RedirectResponse
    {
        $this->manager($this->member($request, $organization));
        $data = $request->validate(['decision' => ['required', Rule::in(['dismissed', 'hidden'])]]);
        DB::transaction(function () use ($organization, $report, $data, $request): void {
            $row = DB::table('studybuddy_school_message_reports')->where('id', $report)
                ->where('organization_id', $organization->id)->lockForUpdate()->first();
            abort_unless($row, 404);
            abort_unless($row->status === 'pending', 409);
            $msg = StudyBuddySchoolMessage::where('organization_id', $organization->id)->findOrFail($row->message_id);
            if ($data['decision'] === 'hidden') {
                $msg->update(['hidden_at' => now(), 'hidden_by' => $request->user()->id]);
            }
            DB::table('studybuddy_school_message_reports')->where('id', $row->id)->update([
                'status' => $data['decision'], 'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(), 'updated_at' => now(),
            ]);
        });
        StudyBuddySecurityEvent::record('school_message_report_reviewed', 'warning', $request->user()->id,
            ['organization_id' => $organization->id, 'report_id' => $report, 'decision' => $data['decision']]);
        return back()->with('status', 'Report reviewed.');
    }
}
