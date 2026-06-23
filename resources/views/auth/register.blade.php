@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endpush

@section('content')
<section class="auth-wrap" aria-labelledby="register-title">
    <article class="auth-panel auth-copy-panel">
        <p class="eyebrow">Start your space</p>
        <h1 id="register-title">Create your StudyBuddy account.</h1>
        <p>Pick the role that fits you. Your dashboard changes its language, cards, and next steps so the experience feels clear from the first click.</p>

        <div class="role-preview-grid">
            <span>Student</span>
            <span>Parent</span>
            <span>Teacher</span>
            <span>Independent Learner</span>
        </div>

        <p class="readability-note">Trust-first signup: learners, parents, teachers, and independent learners each get the right dashboard and safety flow.</p>
    </article>

    <form class="auth-panel auth-form" method="POST" action="{{ route('register.store') }}" data-register-form>
        @csrf

        <p class="eyebrow">Register</p>
        <h2>Build your dashboard</h2>
        <p class="soft-copy">After signup, youâ€™ll go straight to your welcome dashboard.</p>

        @if($errors->any())
            <div class="auth-error" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <label>
            Display name
            <input name="name" value="{{ old('name') }}" autocomplete="name" placeholder="Example: Mehak" required>
        </label>

        <label>
            Real name
            <input name="real_name" value="{{ old('real_name') }}" autocomplete="name" placeholder="Your real name for trust and verification" required>
        </label>

        <label>
            Email
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
        </label>

        <label>
            I am a
            <select name="role" required data-role-select>
                @foreach(['student'=>'Student','parent'=>'Parent','teacher'=>'Teacher','independent_learner'=>'Independent Learner'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', 'student') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label>
            Date of birth
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
        </label>

        <label>
            Country
            <input name="country" value="{{ old('country') }}" placeholder="Example: UAE">
        </label>

        <label>
            Learning stage or focus
            <input name="learning_stage" value="{{ old('learning_stage') }}" placeholder="Example: Year 10, GCSE, reading, math, focus">
        </label>

        <div class="role-extra" data-student-extra>
            <label>
                Parent / guardian email
                <input type="email" name="guardian_email" value="{{ old('guardian_email') }}" placeholder="Needed for younger learners">
            </label>
            <p class="soft-copy">Learners under 13 need a parent or guardian email for safety.</p>
        </div>

        <div class="role-extra" data-teacher-extra hidden>
            <label>
                School / organization name
                <input name="organization_name" value="{{ old('organization_name') }}" placeholder="Example: StudyBuddy School">
            </label>

            <label>
                School / organization email
                <input type="email" name="organization_email" value="{{ old('organization_email') }}" placeholder="Example: teacher@school.com">
            </label>

            <label>
                Position title
                <input name="position_title" value="{{ old('position_title') }}" placeholder="Example: Teacher, Tutor, Coordinator">
            </label>

            <p class="soft-copy">Teacher accounts need verification before classroom tools unlock.</p>
        </div>

        <label>
            Access key
            <input type="password" name="password" autocomplete="new-password" required>
        </label>

        <label>
            Confirm access key
            <input type="password" name="password_confirmation" autocomplete="new-password" required>
        </label>

        <label class="check-row">
            <input type="checkbox" name="safeguarding_agreement" value="1" required @checked(old('safeguarding_agreement'))>
            <span>I agree to use StudyBuddy safely and respectfully.</span>
        </label>

        <label class="check-row">
            <input type="checkbox" name="truth_confirmation" value="1" required @checked(old('truth_confirmation'))>
            <span>I confirm the information I entered is accurate.</span>
        </label>

        <button class="btn" type="submit">Create dashboard</button>

        <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Login instead</a></p>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.querySelector('[data-role-select]');
    const teacherExtra = document.querySelector('[data-teacher-extra]');
    const studentExtra = document.querySelector('[data-student-extra]');

    function syncRoleFields() {
        const role = select ? select.value : 'student';

        if (teacherExtra) {
            teacherExtra.hidden = role !== 'teacher';
            teacherExtra.querySelectorAll('input').forEach(input => {
                input.required = role === 'teacher';
            });
        }

        if (studentExtra) {
            studentExtra.hidden = role !== 'student';
        }
    }

    if (select) {
        select.addEventListener('change', syncRoleFields);
        syncRoleFields();
    }
});
</script>
@endsection