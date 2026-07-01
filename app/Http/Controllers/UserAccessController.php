<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserAccessController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login', ['pageTitle' => 'Welcome back to StudyBuddy']);
    }

    public function showRegister(): View
    {
        return view('auth.register', ['pageTitle' => 'Create your StudyBuddy account']);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('status', 'Welcome back. Your StudyBuddy space is ready.');
        }

        return back()->withErrors(['email' => 'We could not match those login details. Please check your email and password.'])->onlyInput('email');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'real_name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'role' => ['required', Rule::in(['student', 'parent', 'teacher', 'independent_learner'])],
            'date_of_birth' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'country' => ['nullable', 'string', 'max:90'],
            'guardian_email' => ['nullable', 'email', 'max:190'],
            'learning_stage' => ['nullable', 'string', 'max:120'],
            'organization_name' => ['nullable', 'string', 'max:190'],
            'organization_email' => ['nullable', 'email', 'max:190'],
            'position_title' => ['nullable', 'string', 'max:140'],
            'safeguarding_agreement' => ['required', 'accepted'],
            'truth_confirmation' => ['required', 'accepted'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $dob = Carbon::parse($data['date_of_birth']);
        $age = $dob->age;
        $role = $data['role'];

        if (in_array($role, ['parent', 'teacher', 'independent_learner'], true) && $age < 18) {
            throw ValidationException::withMessages(['date_of_birth' => 'Parent, teacher, and independent learner accounts must be 18+ because they can supervise or manage learner spaces.']);
        }

        if ($role === 'student' && $age < 13 && empty($data['guardian_email'])) {
            throw ValidationException::withMessages(['guardian_email' => 'Learners under 13 need a parent or guardian email on file.']);
        }

        if ($role === 'teacher') {
            foreach (['organization_name', 'organization_email', 'position_title'] as $field) {
                if (empty($data[$field])) {
                    throw ValidationException::withMessages([$field => 'Teacher verification needs school or organization details.']);
                }
            }
        }

        $user = User::create([
            'name' => $data['name'],
            'real_name' => $data['real_name'],
            'email' => $data['email'],
            'email_verified_at' => now(), // Email verification is intentionally disabled for StudyBuddy local/platform flow.
            'password' => Hash::make($data['password']),
            'role' => $role,
            'date_of_birth' => $dob->toDateString(),
            'country' => $data['country'] ?? null,
            'guardian_email' => $data['guardian_email'] ?? null,
            'learning_stage' => $data['learning_stage'] ?? null,
            'organization_name' => $data['organization_name'] ?? null,
            'organization_email' => $data['organization_email'] ?? null,
            'position_title' => $data['position_title'] ?? null,
            'avatar_style' => $this->defaultAvatar($role),
            'cosmic_points' => $this->startingPoints($role),
            'is_admin' => false,
            'age_verified_at' => in_array($role, ['parent', 'teacher', 'independent_learner'], true) ? now() : null,
            'role_verification_status' => $this->initialVerificationStatus($role),
            'safeguarding_agreed_at' => now(),
            'verification_submitted_at' => in_array($role, ['teacher', 'independent_learner'], true) ? now() : null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Account created. StudyBuddy is ready. Role-based controls unlock according to your account type and safety status.');
    }

    public function verificationNotice(): RedirectResponse
    {
        return redirect()->route('dashboard')->with('status', 'Email verification is disabled. StudyBuddy uses role, age, admin, and parent/student safety checks instead.');
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        return back()->with('status', 'Email verification is disabled. No inbox email is needed for StudyBuddy access.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('status', 'You have been logged out safely. Public StudyBuddy style has reset.');
    }

    private function defaultAvatar(string $role): string
    {
        return match ($role) {
            'parent' => 'parent-guide',
            'teacher' => 'teacher-mentor',
            'independent_learner' => 'cosmic-explorer',
            default => 'dolphin-cadet',
        };
    }

    private function startingPoints(string $role): int
    {
        return match ($role) {
            'parent', 'teacher' => 120,
            'independent_learner' => 80,
            default => 50,
        };
    }

    private function initialVerificationStatus(string $role): string
    {
        return match ($role) {
            'teacher', 'independent_learner' => 'pending_admin_review',
            'parent' => 'pending_child_approval',
            default => 'not_required',
        };
    }
}
