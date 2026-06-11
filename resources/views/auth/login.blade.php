@extends('layouts.app')

@section('title', 'Login')
@section('body_class', 'page-shell page-auth')

@section('content')
<section class="auth-shell reveal-on-load" aria-labelledby="login-title">
    <div class="glass-panel auth-panel tilt-card">
        <p class="eyebrow">Welcome back</p>
        <h1 id="login-title">Log in to StudyBuddy</h1>
        <p>Continue to your learning dashboard, rewards, and app missions.</p>
        <form class="auth-form" method="POST" action="{{ route('login') }}">
            @csrf
            <label>Email <input type="email" name="email" autocomplete="email" required></label>
            <label>Password <input type="password" name="password" autocomplete="current-password" required></label>
            <button class="button" type="submit">Log In</button>
        </form>
        <p class="auth-switch">New here? <a href="{{ route('register') }}">Create an account</a></p>
    </div>
</section>
@endsection
