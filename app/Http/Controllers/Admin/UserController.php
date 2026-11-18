<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudyBuddySecurityEvent;
use App\Models\User;
use App\Support\StudyBuddyAdminAuthority as Authority;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeUsers($request);
        $q = trim((string) $request->query('q', ''));
        return view('admin.users.index', [
            'users' => User::query()
                ->when($q !== '', fn($builder) => $builder->where(function($sub) use ($q) {
                    $sub->where('name','like','%'.$q.'%')->orWhere('email','like','%'.$q.'%');
                }))
                ->latest()->paginate(24)->withQueryString(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeUsers($request);
        return view('admin.users.form', ['user' => new User(), 'method' => 'POST']);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeUsers($request);
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        $data['is_admin'] = false;
        if (Schema::hasColumn('users', 'studybuddy_test_account')) {
            $data['studybuddy_test_account'] = $request->boolean('studybuddy_test_account');
        }
        $user = User::create($data);
        StudyBuddySecurityEvent::record('p11_account_created', 'info', $request->user()->id, [
            'target_id' => $user->id, 'kind' => $data['role'], 'test_account' => (bool) ($data['studybuddy_test_account'] ?? false),
        ]);
        return redirect()->route('admin.users.index')->with('status', 'Account created. Verify role and identity before connecting to a school.');
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorizeUsers($request);
        abort_if((bool) $user->is_admin || Authority::isProtected($user), 403, 'Administrators are managed in Team Access.');
        return view('admin.users.form', ['user' => $user, 'method' => 'PUT']);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeUsers($request);
        abort_if((bool) $user->is_admin || Authority::isProtected($user), 403);
        $data = $this->validated($request, $user);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        // Never accept an admin grant from this ordinary user editor.
        $data['is_admin'] = false;
        if (Schema::hasColumn('users', 'studybuddy_test_account')) {
            $data['studybuddy_test_account'] = $request->boolean('studybuddy_test_account');
        }
        $user->update($data);
        StudyBuddySecurityEvent::record('p11_account_updated', 'info', $request->user()->id, ['target_id' => $user->id]);
        return redirect()->route('admin.users.index')->with('status', 'Account updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeUsers($request);
        abort_if((bool) $user->is_admin || Authority::isProtected($user), 403);
        abort_if($user->id === $request->user()->id, 403);
        // Ordinary real-account deletion needs a dedicated privacy/export workflow.
        // Here, remove only isolated test accounts without school memberships.
        abort_unless((bool) ($user->studybuddy_test_account ?? false), 409, 'Real accounts require a dedicated privacy-review deletion process.');
        abort_if($user->schoolMemberships()->exists(), 409, 'Disconnect school memberships first.');
        $user->delete();
        StudyBuddySecurityEvent::record('p11_account_deleted', 'warning', $request->user()->id, ['target_id' => $user->id]);
        return back()->with('status', 'User deleted.');
    }

    private function authorizeUsers(Request $request): void
    {
        abort_unless(Authority::can($request->user(), 'platform.users.manage'), 403);
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user?->id)],
            'real_name' => ['nullable', 'string', 'max:160'],
            'role' => ['required', Rule::in(['student', 'parent', 'teacher', 'independent_learner'])],
            'learning_stage' => ['nullable', 'string', 'max:120'],
            'avatar_style' => ['nullable', 'string', 'max:120'],
            'profile_photo_path' => ['nullable', 'string', 'max:255'],
            'cosmic_points' => ['nullable', 'integer', 'min:0'],
            'studybuddy_test_account' => ['nullable', 'boolean'],
        ];
        $rules['password'] = $request->isMethod('post')
            ? ['required', Password::min(12)->letters()->numbers()]
            : ['nullable', Password::min(12)->letters()->numbers()];
        $data = $request->validate($rules);
        unset($data['studybuddy_test_account']); // applied only when its column exists
        return $data;
    }
}
