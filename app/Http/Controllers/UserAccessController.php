<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
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
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:190'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $email = Str::lower(trim($data['email']));
        $throttleKey = $email.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        if (!Auth::attempt(['email' => $email, 'password' => $data['password']], $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'We could not match those login details. Please check your email and access key.',
            ])->redirectTo(route('login'));
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('status', 'Welcome back. Your StudyBuddy space is ready.');
    }

    public function register(Request $request): RedirectResponse
    {
        // Accept both the old premium UI field names and the newer backend-safe aliases.
        $request->merge([
            'safe_use_confirmed' => ($request->boolean('safe_use_confirmed') || $request->boolean('safeguarding_agreement')) ? '1' : null,
            'accuracy_confirmed' => ($request->boolean('accuracy_confirmed') || $request->boolean('truth_confirmation')) ? '1' : null,
            'safeguarding_agreement' => ($request->boolean('safe_use_confirmed') || $request->boolean('safeguarding_agreement')) ? '1' : null,
            'truth_confirmation' => ($request->boolean('accuracy_confirmed') || $request->boolean('truth_confirmation')) ? '1' : null,
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'real_name' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:190', Rule::unique('users', 'email')],
            'role' => ['required', Rule::in(['student', 'parent', 'teacher', 'independent_learner'])],
            'date_of_birth' => ['required', 'date', 'before:today', 'after:1900-01-01'], // STUDYBUDDY_W22_ACCOUNTS: prevent unaged child accounts
            'country' => ['nullable', 'string', 'max:90'],
            'guardian_email' => ['nullable', 'email:rfc', 'max:190'],
            'learning_stage' => ['nullable', 'string', 'max:120'],
            'child_emails_text' => ['nullable', 'string', 'max:1200'],
            'organization_name' => ['nullable', 'string', 'max:190'],
            'organization_email' => ['nullable', 'email:rfc', 'max:190'],
            'position_title' => ['nullable', 'string', 'max:140'],
            'study_goal' => ['nullable', 'string', 'max:120'],
            'favorite_subjects' => ['nullable', 'string', 'max:190'],
            'parent_goal' => ['nullable', 'string', 'max:120'],
            'child_age_range' => ['nullable', 'string', 'max:80'],
            'class_level' => ['nullable', 'string', 'max:80'],
            'teaching_focus' => ['nullable', 'string', 'max:190'],
            'independent_goal' => ['nullable', 'string', 'max:120'],
            'daily_time' => ['nullable', 'string', 'max:80'],
            'safe_use_confirmed' => ['required', 'accepted'],
            'accuracy_confirmed' => ['required', 'accepted'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $email = Str::lower(trim($data['email']));
        $role = $data['role'];
        $dob = !empty($data['date_of_birth']) ? Carbon::parse($data['date_of_birth']) : null;
        $age = $dob?->age;

        if ($dob && in_array($role, ['parent', 'teacher'], true) && $age < 18) {
            throw ValidationException::withMessages([
                'date_of_birth' => 'Adult account roles must be 18+.',
            ]);
        }

        // Children of any age can learn through a parent-managed family profile.
        // An unverified guardian email is not parental approval. Do not create
        // direct-login under-13 accounts through the public signup form.
        if ($age !== null && $age < 13 && in_array($role, ['student', 'independent_learner'], true)) {
            throw ValidationException::withMessages([
                'date_of_birth' => 'For learners under 13, a parent or guardian first creates a family-managed learner profile from their StudyBuddy account. No separate child login is needed.',
            ]);
        }

        $childEmails = $this->parseEmailList($data['child_emails_text'] ?? '');

        // Keep preferences role-specific and expose the same learning_goal key
        // already read by DashboardController and Profile Studio.
        $roleProfile = array_filter(match ($role) {
            'student' => [
                'study_goal' => $data['study_goal'] ?? null,
                'learning_goal' => $data['study_goal'] ?? null,
                'favorite_subjects' => $data['favorite_subjects'] ?? null,
                'current_focus' => $data['learning_stage'] ?? null,
            ],
            'parent' => [
                'parent_goal' => $data['parent_goal'] ?? null,
                'child_age_range' => $data['child_age_range'] ?? null,
            ],
            'teacher' => [
                'class_level' => $data['class_level'] ?? null,
                'teaching_focus' => $data['teaching_focus'] ?? null,
            ],
            'independent_learner' => [
                'independent_goal' => $data['independent_goal'] ?? null,
                'favorite_subjects' => $data['favorite_subjects'] ?? null,
                'learning_goal' => $data['independent_goal'] ?? null,
                'daily_time' => $data['daily_time'] ?? null,
                'current_focus' => $data['learning_stage'] ?? null,
            ],
        }, fn ($value) => filled($value));

        // An independent teen remains a learner and must never gain adult-only permissions.
        $requiresAdultReview = in_array($role, ['parent', 'teacher'], true)
            || ($role === 'independent_learner' && $age >= 18);

        $payload = [
            'name' => trim($data['name']),
            'real_name' => filled($data['real_name'] ?? null) ? trim($data['real_name']) : trim($data['name']),
            'email' => $email,
            'email_verified_at' => null, 
            'password' => Hash::make($data['password']),
            'role' => $role,
            'date_of_birth' => $dob?->toDateString(),
            'country' => $data['country'] ?? null,
            'guardian_email' => null, // W24: direct signup never treats a typed address as parental approval
            'learning_stage' => $role === 'parent' ? null : ($data['learning_stage'] ?? null),
            'child_emails' => $role === 'parent' ? $childEmails : null,
            'role_profile' => $roleProfile ?: null,
            'organization_name' => $role === 'teacher' ? ($data['organization_name'] ?? null) : null,
            'organization_email' => $role === 'teacher' ? ($data['organization_email'] ?? null) : null,
            'position_title' => $role === 'teacher' ? ($data['position_title'] ?? null) : null,
            'avatar_style' => $this->defaultAvatar($role),
            'cosmic_points' => $this->startingPoints($role),
            'is_admin' => false,
            'age_verified_at' => $dob && $age >= 18 ? now() : null,
            'role_verification_status' => $role === 'independent_learner' && $age < 18 ? 'not_required' : $this->initialVerificationStatus($role),
            'adult_verification_status' => $requiresAdultReview ? 'pending_review' : 'not_required',
            'adult_verification_consent_at' => $requiresAdultReview ? now() : null,
            'safeguarding_agreed_at' => now(),
            'verification_submitted_at' => ($role === 'teacher' || ($role === 'independent_learner' && $age >= 18)) ? now() : null,
        ];

        $user = new User();

        foreach ($payload as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $user->{$column} = $value;
            }
        }

        $user->save();

        Auth::login($user);
        $request->session()->regenerate();
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with('status', 'Account created. Check your inbox to verify your email.');
    }

    public function verificationNotice(): RedirectResponse
    {
        return redirect()->route('dashboard')->with('status', 'Email verification is already handled for StudyBuddy access.');
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        return back()->with('status', 'No inbox step is needed. Your StudyBuddy account is ready to use.');
    }

    public function showLogout(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.logout-confirm');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have been logged out safely.');
    }

    private function parseEmailList(string $value): array
    {
        return collect(preg_split('/[\s,;]+/', $value) ?: [])
            ->map(fn ($email) => Str::lower(trim($email)))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
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
