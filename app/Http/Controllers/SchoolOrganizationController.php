<?php

/**
 * STUDYBUDDY-TEAM-HANDOVER-2026
 * Developer orientation: School access boundary: invitations, membership and staff role changes must remain organization scoped. Test revoked members and cross-organization IDs.
 *
 * Documentation-only annotation; no runtime behavior is changed here.
 */


namespace App\Http\Controllers;

use App\Models\SchoolOrganization;
use App\Models\SchoolOrganizationInvite;
use App\Models\SchoolOrganizationMember;
use App\Models\StudyBuddySecurityEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SchoolOrganizationController extends Controller
{
    public function info(): View { return view('studybuddy.schools.info'); }

    public function apply(Request $request): View
    {
        $this->ensureEligible($request);
        return view('studybuddy.schools.apply', [
            'existing' => SchoolOrganization::where('requested_by', $request->user()->id)->latest()->first(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureEligible($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:190'],
            'domain' => ['required', 'string', 'max:190', 'regex:/^(?!-)[a-z0-9-]+(\.[a-z0-9-]+)+$/i'],
            'official_email' => ['required', 'email:rfc', 'max:190'],
            'city' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'purpose' => ['required', 'string', 'min:20', 'max:2500'],
            'confirm_authorized' => ['accepted'],
        ]);
        $data['domain'] = Str::lower(trim($data['domain']));
        $data['official_email'] = Str::lower(trim($data['official_email']));
        if (!str_ends_with($data['official_email'], '@'.$data['domain'])) {
            throw ValidationException::withMessages([
                'official_email' => 'Use a school email address under the official domain you entered.',
            ]);
        }
        if (SchoolOrganization::where('requested_by', $request->user()->id)->where('status', 'pending')->exists()) {
            return back()->withErrors(['name' => 'Your current school application is already awaiting review.']);
        }
        if (SchoolOrganization::where('domain', $data['domain'])->whereIn('status', ['pending', 'approved'])->exists()) {
            // Don't reveal whether an institution is already an active customer.
            return back()->withErrors(['domain' => 'This domain cannot be requested here. Contact the StudyBuddy team for assistance.']);
        }
        unset($data['confirm_authorized']);
        $organization = SchoolOrganization::create($data + ['requested_by' => $request->user()->id, 'status' => 'pending']);
        StudyBuddySecurityEvent::record('school_application_submitted', 'info', $request->user()->id, ['organization_id' => $organization->id]);
        return redirect()->route('schools.apply')->with('status', 'Request received. A real StudyBuddy administrator must verify and approve your school before any workspace becomes active.');
    }

    public function workspace(Request $request, SchoolOrganization $organization): View
    {
        $this->ensureMembership($request, $organization);
        $organization->load(['members.user', 'invitations' => fn($q) => $q->whereNull('accepted_at')->whereNull('revoked_at')->where('expires_at', '>', now())->latest()]);
        return view('studybuddy.schools.workspace', compact('organization'));
    }

    public function invite(Request $request, SchoolOrganization $organization): RedirectResponse
    {
        $this->ensureManager($request, $organization);
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:190'],
            'role' => ['required', Rule::in(['teacher', 'school_admin'])],
        ]);
        $email = Str::lower(trim($data['email']));
        if (SchoolOrganizationMember::where('organization_id', $organization->id)
            ->whereHas('user', fn($q) => $q->where('email', $email))->where('status', 'active')->exists()) {
            return back()->withErrors(['email' => 'This staff account is already in your workspace.']);
        }
        // Token is stored only as a hash; invitations are email-specific and expire.
        $plainToken = Str::random(64);
        $invitation = SchoolOrganizationInvite::create([
            'organization_id' => $organization->id,
            'invited_by' => $request->user()->id,
            'email' => $email,
            'token_hash' => hash('sha256', $plainToken),
            'role' => $data['role'],
            'expires_at' => now()->addDays(3),
        ]);
        $url = route('schools.invitations.show', $plainToken);
        try {
            Mail::raw("You have been invited to join {$organization->name} on StudyBuddy as {$data['role']}. Sign in or register with {$email}, verify your email, and open this one-time link within three days: {$url}\n\nIf you weren't expecting this invitation, ignore it.",
                fn($message) => $message->to($email)->subject('StudyBuddy school invitation'));
        } catch (\Throwable $error) {
            // Never reveal token through an error or save invitations that cannot be delivered.
            $invitation->delete();
            return back()->withErrors(['email' => 'Invitation email could not be sent. Check server mail settings and try again.']);
        }
        StudyBuddySecurityEvent::record('school_invitation_sent', 'info', $request->user()->id, ['organization_id' => $organization->id]);
        return back()->with('status', 'Invitation sent. It expires in three days and only works for the specified account email.');
    }

    public function revokeInvitation(Request $request, SchoolOrganization $organization, SchoolOrganizationInvite $invitation): RedirectResponse
    {
        $this->ensureManager($request, $organization);
        abort_unless($invitation->organization_id === $organization->id, 404);
        abort_if($invitation->accepted_at, 409, 'This invitation has already been accepted. Remove membership instead.');
        $invitation->update(['revoked_at' => now()]);
        StudyBuddySecurityEvent::record('school_invitation_revoked', 'warning', $request->user()->id, ['organization_id' => $organization->id]);
        return back()->with('status', 'Invitation revoked. Its link cannot be used now.');
    }

    public function changeStaffRole(Request $request, SchoolOrganization $organization, SchoolOrganizationMember $member): RedirectResponse
    {
        $actor = $this->ensureMembership($request, $organization);
        // Only the actual institution owner manages school administrators.
        abort_unless($actor->role === 'owner', 403);
        abort_unless($member->organization_id === $organization->id && $member->status === 'active', 404);
        abort_if($member->role === 'owner' || $member->user_id === $request->user()->id, 403);
        $data = $request->validate(['role' => ['required', Rule::in(['teacher', 'school_admin'])]]);
        $user = $member->user;
        abort_unless($user && $user->normalizedRole() === 'teacher' && $user->hasVerifiedEmail()
            && in_array($user->role_verification_status, ['verified','approved'], true), 403);
        DB::transaction(function () use ($member, $organization, $request, $data): void {
            $locked = SchoolOrganizationMember::whereKey($member->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->organization_id === $organization->id && $locked->status === 'active', 404);
            abort_if($locked->role === 'owner' || $locked->user_id === $request->user()->id, 403);
            $locked->update(['role' => $data['role']]);
        });
        StudyBuddySecurityEvent::record('school_staff_role_changed', 'warning', $request->user()->id,
            ['organization_id' => $organization->id, 'member_id' => $member->id, 'role' => $data['role']]);
        return back()->with('status', 'School staff permission updated.');
    }

    public function removeMember(Request $request, SchoolOrganization $organization, SchoolOrganizationMember $member): RedirectResponse
    {
        $this->ensureManager($request, $organization);
        abort_unless($member->organization_id === $organization->id, 404);
        abort_if($member->role === 'owner', 403, 'A school owner cannot be removed from the workspace.');
        abort_if($member->user_id === $request->user()->id, 403, 'You cannot revoke your own membership here.');
        $member->update(['status' => 'revoked']);
        StudyBuddySecurityEvent::record('school_staff_access_revoked', 'warning', $request->user()->id, ['organization_id' => $organization->id, 'member_id' => $member->id]);
        return back()->with('status', 'Staff access revoked immediately.');
    }

    public function invitation(Request $request, string $token): View
    {
        $invite = $this->findInvite($token);
        // An invitation is bound to one verified account address.
        abort_unless(Str::lower((string)$request->user()?->email) === $invite->email, 403);
        return view('studybuddy.schools.invitation', ['invite' => $invite]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invite = $this->findInvite($token);
        $user = $request->user();
        if (!$user->hasVerifiedEmail() || Str::lower($user->email) !== $invite->email) {
            return back()->withErrors(['email' => 'Sign in and verify the exact email address named on this invitation.']);
        }
        if ($user->normalizedRole() !== 'teacher' && ! $user->is_admin) {
            return back()->withErrors(['email' => 'This invitation requires a StudyBuddy teacher account.']);
        }
        if (! $user->is_admin && !in_array($user->role_verification_status, ['verified', 'approved'], true)) {
            return back()->withErrors(['email' => 'Complete your teacher/adult verification before joining a school.']);
        }
        DB::transaction(function () use ($invite, $user) {
            $locked = SchoolOrganizationInvite::whereKey($invite->id)->lockForUpdate()->firstOrFail();
            abort_if($locked->accepted_at || $locked->revoked_at || $locked->expires_at->isPast(), 410);
            abort_unless($locked->organization->status === 'approved', 403);
            SchoolOrganizationMember::updateOrCreate(
                ['organization_id' => $locked->organization_id, 'user_id' => $user->id],
                ['role' => $locked->role, 'status' => 'active', 'joined_at' => now()]
            );
            $locked->update(['accepted_at' => now()]);
        });
        StudyBuddySecurityEvent::record('school_invitation_accepted', 'info', $user->id, ['organization_id' => $invite->organization_id]);
        return redirect()->route('schools.workspace', $invite->organization_id)->with('status', 'Welcome to your school workspace.');
    }

    private function findInvite(string $token): SchoolOrganizationInvite
    {
        abort_unless(strlen($token) === 64 && ctype_alnum($token), 404);
        $invite = SchoolOrganizationInvite::with('organization')->where('token_hash', hash('sha256', $token))->firstOrFail();
        abort_if($invite->revoked_at || $invite->accepted_at || $invite->expires_at->isPast(), 410);
        abort_unless($invite->organization->status === 'approved', 403);
        return $invite;
    }

    private function ensureEligible(Request $request): void
    {
        $user = $request->user();
        abort_unless($user && in_array($user->normalizedRole(), ['teacher', 'parent', 'admin'], true), 403);
        abort_unless($user->hasVerifiedEmail(), 403, 'Verify your email address before requesting a school account.');
        abort_unless($user->is_admin || ($user->date_of_birth && $user->date_of_birth->age >= 18), 403,
            'School applications require an adult account with date of birth.');
    }

    private function ensureMembership(Request $request, SchoolOrganization $organization): SchoolOrganizationMember
    {
        abort_unless($organization->status === 'approved', 403);
        return SchoolOrganizationMember::where('organization_id', $organization->id)
            ->where('user_id', $request->user()->id)->where('status', 'active')->firstOrFail();
    }

    private function ensureManager(Request $request, SchoolOrganization $organization): void
    {
        $membership = $this->ensureMembership($request, $organization);
        abort_unless(in_array($membership->role, ['owner', 'school_admin'], true), 403);
    }
}
