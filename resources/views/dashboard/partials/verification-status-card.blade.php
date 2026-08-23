@php
    $user = auth()->user();
    $role = $user?->role ?? 'student';
    $adultStatus = $user?->adult_verification_status ?? 'not_required';
    $roleStatus = $user?->role_verification_status ?? 'not_required';

    $needsAdultCheck = method_exists($user, 'needsAdultVerification')
        ? $user->needsAdultVerification()
        : (in_array($role, ['parent', 'teacher', 'independent_learner'], true)
            && !in_array($adultStatus, ['approved', 'not_required'], true));

    $needsRoleCheck = method_exists($user, 'needsRoleVerification')
        ? $user->needsRoleVerification()
        : (in_array($role, ['parent', 'teacher'], true)
            && !in_array($roleStatus, ['approved', 'not_required'], true));
@endphp

@if($user && ($needsAdultCheck || $needsRoleCheck))
    <article class="auth-panel sbv-dashboard-card">
        <p class="eyebrow">Trust status</p>
        <h2>Account safety checks</h2>
        <p>Parent, teacher, and adult learner tools unlock through extra safety review. Everyday StudyBuddy features stay available.</p>
        <div class="sbv-pills compact">
            <span>Adult: {{ str_replace('_', ' ', $adultStatus) }}</span>
            <span>Role check: {{ str_replace('_', ' ', $roleStatus) }}</span>
            <span>Account: {{ str_replace('_', ' ', $role) }}</span>
        </div>
        @if(Route::has('studybuddy.verification.center'))
            <a class="btn btn-ghost" href="{{ route('studybuddy.verification.center') }}">Open Safety Center</a>
        @endif
    </article>
@endif
