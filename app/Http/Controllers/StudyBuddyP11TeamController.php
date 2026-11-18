<?php

namespace App\Http\Controllers;

use App\Models\SchoolOrganization;
use App\Models\StudyBuddySecurityEvent;
use App\Models\User;
use App\Support\StudyBuddyAdminAuthority as Authority;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudyBuddyP11TeamController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Authority::can($request->user(), 'platform.view'), 403);
        return view('admin.p11.team', [
            'headId' => Authority::headId(),
            'isHead' => Authority::isHead($request->user()),
            'capabilities' => Authority::CAPABILITIES,
            'canReviewSchools' => Authority::can($request->user(), 'schools.review'),
            'schoolStats' => Authority::can($request->user(), 'schools.review')
                ? SchoolOrganization::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status')
                : collect(),
            'canReadAudit' => Authority::can($request->user(), 'ops.view'),
            'recentEvents' => Authority::can($request->user(), 'ops.view')
                ? StudyBuddySecurityEvent::latest('created_at')->limit(12)->get() : collect(),
            'staff' => Authority::schemaReady()
                ? DB::table('studybuddy_admin_grants as g')
                    ->join('users as u', 'u.id', '=', 'g.user_id')
                    ->select('g.*', 'u.name', 'u.email', 'u.email_verified_at')
                    ->orderByRaw("case when g.kind = 'head' then 0 else 1 end")
                    ->orderBy('u.name')->get()
                : collect(),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $this->onlyHead($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')],
            'capabilities' => ['required', 'array', 'min:1'],
            'capabilities.*' => ['required', Rule::in(array_keys(Authority::CAPABILITIES))],
        ]);
        $capabilities = array_values(array_unique(array_merge(['platform.view'], $data['capabilities'])));
        $user = DB::transaction(function () use ($data, $capabilities, $request) {
            // A random undisclosed password; staff must use reset email flow.
            $user = User::forceCreate([
                'name' => $data['name'], 'email' => Str::lower(trim($data['email'])),
                'password' => Hash::make(Str::random(80)),
                'role' => 'admin', 'is_admin' => true, 'email_verified_at' => null,
                'role_verification_status' => 'pending', 'adult_verification_status' => 'pending',
            ]);
            DB::table('studybuddy_admin_grants')->insert([
                'user_id' => $user->id, 'kind' => 'staff', 'capabilities' => json_encode($capabilities),
                'is_active' => true, 'granted_by' => $request->user()->id,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            return $user;
        });
        try {
            $delivery = Password::sendResetLink(['email' => $user->email]);
        } catch (\Throwable $error) {
            $delivery = 'mail_unavailable';
        }
        StudyBuddySecurityEvent::record('p11_staff_created', 'warning', $request->user()->id, ['target_id' => $user->id, 'caps' => $capabilities]);
        return back()->with('status', $delivery === Password::RESET_LINK_SENT
            ? 'Staff account created. A password setup link was requested; access remains blocked until email verification.'
            : 'Staff account created, but password setup mail is not confirmed. Configure mail and ask the staff member to use Forgot Password. No temporary password is disclosed.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->onlyHead($request);
        abort_if(Authority::isProtected($user), 403);
        $data = $request->validate([
            'capabilities' => ['required', 'array', 'min:1'],
            'capabilities.*' => ['required', Rule::in(array_keys(Authority::CAPABILITIES))],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $grant = DB::table('studybuddy_admin_grants')->where('user_id', $user->id)->where('kind', 'staff')->first();
        abort_unless($grant, 404);
        $capabilities = array_values(array_unique(array_merge(['platform.view'], $data['capabilities'])));
        DB::table('studybuddy_admin_grants')->where('id', $grant->id)->update([
            'capabilities' => json_encode($capabilities), 'is_active' => $request->boolean('is_active'), 'updated_at' => now(),
        ]);
        StudyBuddySecurityEvent::record('p11_staff_permissions_updated', 'warning', $request->user()->id, [
            'target_id' => $user->id, 'caps' => $capabilities, 'active' => $request->boolean('is_active'),
        ]);
        return back()->with('status', 'Staff permissions saved.');
    }

    public function revoke(Request $request, User $user): RedirectResponse
    {
        $this->onlyHead($request);
        abort_if(Authority::isProtected($user) || $user->id === $request->user()->id, 403);
        DB::transaction(function () use ($user) {
            $grant = DB::table('studybuddy_admin_grants')->where('user_id', $user->id)->where('kind', 'staff')->lockForUpdate()->first();
            abort_unless($grant, 404);
            DB::table('studybuddy_admin_grants')->where('id', $grant->id)->update(['is_active' => false, 'updated_at' => now()]);
            // Preserve the user and all associated records for audit and recovery.
            $user->forceFill(['is_admin' => false, 'role' => 'independent_learner', 'remember_token' => Str::random(60)])->save();
        });
        StudyBuddySecurityEvent::record('p11_staff_revoked', 'critical', $request->user()->id, ['target_id' => $user->id]);
        return back()->with('status', 'Staff access revoked. The user record and audit history remain intact.');
    }

    private function onlyHead(Request $request): void
    {
        abort_unless(Authority::schemaReady() && Authority::isHead($request->user()), 403, 'Head Admin authorization is required.');
    }
}
