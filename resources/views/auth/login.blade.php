@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endpush

@section('content')
<section class="auth-wrap auth-wrap-centered" aria-labelledby="login-title">
    <article class="auth-panel auth-copy-panel">
        <p class="eyebrow">StudyBuddy Access</p>
        <h1 id="login-title">Welcome back.</h1>
        <p>Login to reach your calm learning dashboard. Students, families, teachers, and independent learners all get a dashboard shaped around their role.</p>
        <div class="auth-mini-grid"><span>Readable dashboard</span><span>Gentle progress</span><span>Role-based tools</span></div>
    </article>
    <form class="auth-panel auth-form" method="POST" action="{{ route('login.store') }}">
        @csrf
        <p class="eyebrow">Login</p>
        <h2>Open your dashboard</h2>
        <p class="soft-copy">Use your email and access key to continue.</p>
        @if(session('status')) <div class="auth-success" role="status">{{ session('status') }}</div> @endif
        @if($errors->any()) <div class="auth-error" role="alert">{{ $errors->first() }}</div> @endif
        <label>Email <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
        <label>Access key <input type="password" name="password" autocomplete="current-password" required></label>
        <label class="check-row"><input type="checkbox" name="remember" value="1"><span>Keep me signed in on this device</span></label>
        <button class="btn" type="submit">Enter dashboard</button>
        <p class="auth-switch">New here? <a href="{{ route('register') }}">Create a StudyBuddy account</a></p>
    </form>
</section>
@endsection
