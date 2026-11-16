@extends('layouts.app')

@section('title', 'Verification Center')

@section('content')
@php
    $statusLabels = [
        'not_required' => 'Not required',
        'pending' => 'Pending review',
        'pending_admin_review' => 'Pending review',
        'needs_more_info' => 'More information needed',
        'verified' => 'Verified',
        'rejected' => 'Not approved',
    ];
    $methodLabels = [
        'manual_id_review' => 'Account details review',
        'face_liveness_provider' => 'Verification service reference',
        'school_email_domain' => 'School email review',
        'guardian_review' => 'Guardian review',
    ];
    $roleTypeLabels = [
        'adult_account' => 'Adult account',
        'parent_guardian' => 'Parent or guardian',
        'teacher' => 'Teacher',
        'independent_learner' => 'Independent learner',
    ];
@endphp

<section class="sbv-shell">
    <div class="sbv-hero">
        <p class="eyebrow">Trust Center</p>
        <h1>Verification Center</h1>
        <p>StudyBuddy keeps role controls safe. Parents, teachers, and independent adult learners can request an adult account review when their role requires it.</p>
        <div class="sbv-pills">
            <span>Email: {{ $user->hasVerifiedEmail() ? 'Verified' : 'Pending' }}</span>
            <span>Role: {{ $statusLabels[$user->role_verification_status ?? 'not_required'] ?? 'Pending review' }}</span>
            <span>Adult: {{ $statusLabels[$user->adult_verification_status ?? 'not_required'] ?? 'Pending review' }}</span>
        </div>
    </div>

    <div class="sbv-grid">
        <form class="sbv-card" method="POST" action="{{ route('studybuddy.verification.submit') }}">
            @csrf
            <h2>Submit adult verification</h2>
            <p class="soft-copy">StudyBuddy stores the information entered in this form for a secure account review. Do not upload identity documents or biometric images here.</p>

            @if($errors->any())
                <div class="sbv-alert">{{ $errors->first() }}</div>
            @endif

            @if(session('status'))
                <div class="sbv-alert is-good">{{ session('status') }}</div>
            @endif

            <label>
                Verification type
                <select name="role_type" required>
                    <option value="adult_account">Adult account</option>
                    <option value="parent_guardian">Parent / guardian</option>
                    <option value="teacher">Teacher</option>
                    <option value="independent_learner">Independent learner</option>
                </select>
            </label>

            <label>
                Method
                <select name="method" required>
                    <option value="manual_id_review">Account details review</option>
                    <option value="face_liveness_provider">Verification service reference</option>
                    <option value="school_email_domain">School email review</option>
                    <option value="guardian_review">Guardian review</option>
                </select>
            </label>

            <label>
                Full real name
                <input name="submitted_name" value="{{ old('submitted_name', $user->real_name ?: $user->name) }}" required maxlength="190">
            </label>

            <label>
                Country
                <input name="submitted_country" value="{{ old('submitted_country', $user->country) }}" maxlength="100">
            </label>

            <label>
                Provider/reference ID <small>optional</small>
                <input name="provider_reference" value="{{ old('provider_reference') }}" maxlength="190" placeholder="Reference from your verification service">
            </label>

            <label>
                Notes for the review team <small>optional</small>
                <textarea name="notes" rows="4" maxlength="1000">{{ old('notes') }}</textarea>
            </label>

            <label class="sbv-check">
                <input type="checkbox" name="adult_confirmed" value="1" required>
                I confirm I am 18+ and this account belongs to me.
            </label>

            <label class="sbv-check">
                <input type="checkbox" name="consent_confirmed" value="1" required>
                I consent to verification review for StudyBuddy safety and role access.
            </label>

            <button class="btn" type="submit">Submit verification request</button>
        </form>

        <aside class="sbv-card">
            <h2>Verification history</h2>
            @forelse($cases as $case)
                <article class="sbv-case">
                    <strong>{{ $roleTypeLabels[$case->role_type] ?? str_replace('_', ' ', $case->role_type) }}</strong>
                    <span>{{ $methodLabels[$case->method] ?? str_replace('_', ' ', $case->method) }}</span>
                    <em>{{ $statusLabels[$case->status] ?? str_replace('_', ' ', $case->status) }}</em>
                    @if($case->admin_notes)
                        <p>{{ $case->admin_notes }}</p>
                    @endif
                </article>
            @empty
                <p class="soft-copy">No verification requests yet.</p>
            @endforelse
        </aside>
    </div>
</section>
@endsection
