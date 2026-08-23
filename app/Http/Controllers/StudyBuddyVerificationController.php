<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyVerificationCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudyBuddyVerificationController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $cases = StudyBuddyVerificationCase::where('user_id', $user->id)->latest()->take(10)->get();

        return view('studybuddy.verification.center', compact('user', 'cases'));
    }

    public function submit(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'role_type' => ['required', Rule::in(['adult_account', 'parent_guardian', 'teacher', 'independent_learner'])],
            'method' => ['required', Rule::in(['face_liveness_provider', 'manual_id_review', 'school_email_domain', 'guardian_review'])],
            'provider_reference' => ['nullable', 'string', 'max:190'],
            'submitted_name' => ['required', 'string', 'max:190'],
            'submitted_country' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'adult_confirmed' => ['required', 'accepted'],
            'consent_confirmed' => ['required', 'accepted'],
        ]);

        if (! $user->isAdult()) {
            return back()->withErrors([
                'adult_confirmed' => 'Adult verification is only available for accounts whose date of birth is 18+.',
            ])->withInput();
        }

        if (StudyBuddyVerificationCase::where('user_id', $user->id)->whereIn('status', ['pending', 'needs_more_info'])->exists()) {
            return back()->withErrors([
                'role_type' => 'You already have a verification case waiting for review.',
            ])->withInput();
        }

        StudyBuddyVerificationCase::create([
            'user_id' => $user->id,
            'role_type' => $data['role_type'],
            'method' => $data['method'],
            'status' => 'pending',
            'provider_reference' => $data['provider_reference'] ?? null,
            'submitted_name' => $data['submitted_name'],
            'submitted_country' => $data['submitted_country'] ?? null,
            'adult_confirmed' => true,
            'consent_confirmed' => true,
            'notes' => $data['notes'] ?? null,
            'submitted_at' => now(),
        ]);

        $user->update([
            'adult_verification_status' => 'pending',
            'adult_verification_method' => $data['method'],
            'adult_verification_reference' => $data['provider_reference'] ?? null,
            'adult_verification_consent_at' => now(),
            'adult_verification_submitted_at' => now(),
            'role_verification_status' => in_array($user->role, ['parent', 'teacher', 'independent_learner'], true)
                ? 'pending_admin_review'
                : $user->role_verification_status,
        ]);

        return back()->with('status', 'Verification request submitted. The StudyBuddy review team will take it from here.');
    }

    public function adminIndex(): View
    {
        $cases = StudyBuddyVerificationCase::with('user')->latest()->paginate(25);

        return view('admin.studybuddy.verifications.index', compact('cases'));
    }

    public function adminUpdate(Request $request, StudyBuddyVerificationCase $case): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'verified', 'rejected', 'needs_more_info'])],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $case->update([
            'status' => $data['status'],
            'admin_notes' => $data['admin_notes'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $user = $case->user;

        if ($user && $data['status'] === 'verified') {
            $user->update([
                'adult_verification_status' => 'verified',
                'adult_verification_method' => $case->method,
                'adult_verification_reference' => $case->provider_reference,
                'adult_verification_reviewed_at' => now(),
                'age_verified_at' => now(),
                'role_verified_at' => now(),
                'role_verification_status' => 'verified',
            ]);
        } elseif ($user && $data['status'] === 'rejected') {
            $user->update([
                'adult_verification_status' => 'rejected',
                'adult_verification_reviewed_at' => now(),
                'role_verification_status' => $user->needsAdultVerification() ? 'rejected' : $user->role_verification_status,
            ]);
        } elseif ($user && $data['status'] === 'needs_more_info') {
            $user->update([
                'adult_verification_status' => 'needs_more_info',
                'role_verification_status' => $user->needsAdultVerification() ? 'needs_more_info' : $user->role_verification_status,
            ]);
        }

        return back()->with('status', 'Verification case updated.');
    }
}
