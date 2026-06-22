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
        <div class="role-preview-grid"><span>🎒 Student</span><span>💜 Parent</span><span>🎓 Teacher</span><span>🧭 Independent Learner</span></div>
        <p class="readability-note">Designed for calm reading, clear buttons, and low-clutter learning.</p>
    </article>
    <form class="auth-panel auth-form" method="POST" action="{{ route('register.store') }}">
        @csrf
        <p class="eyebrow">Register</p>
        <h2>Build your dashboard</h2>
        <p class="soft-copy">After signup, you’ll go straight to your welcome dashboard.</p>
        @if($errors->any()) <div class="auth-error" role="alert">{{ $errors->first() }}</div> @endif
        <label>Name <input name="name" value="{{ old('name') }}" autocomplete="name" required></label>
        <label>Email <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
        <label>I am a <select name="role" required>@foreach(['student'=>'Student','parent'=>'Parent','teacher'=>'Teacher','independent_learner'=>'Independent Learner'] as $value => $label)<option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>@endforeach</select></label>
        <label>Learning stage or focus <input name="learning_stage" value="{{ old('learning_stage') }}" placeholder="Example: Primary 4, GCSE, classroom, family routine"></label>
        <label>Access key <input type="password" name="password" autocomplete="new-password" required></label>
        <label>Confirm access key <input type="password" name="password_confirmation" autocomplete="new-password" required></label>
        <button class="btn" type="submit">Create dashboard</button>
        <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Login instead</a></p>
    </form>
</section>
@endsection
