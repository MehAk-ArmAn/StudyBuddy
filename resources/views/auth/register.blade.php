@extends('layouts.app')

@section('title', 'Register')
@section('body_class', 'page-shell page-auth')

@section('content')
<section class="auth-shell reveal-on-load" aria-labelledby="register-title">
    <div class="glass-panel auth-panel tilt-card">
        <p class="eyebrow">Start learning</p>
        <h1 id="register-title">Create your StudyBuddy account</h1>
        <p>Join the cosmic classroom and unlock apps, dashboards, and rewards.</p>
        <form class="auth-form" method="POST" action="{{ route('register') }}">
            @csrf
            <label>Name <input type="text" name="name" autocomplete="name" required></label>
            <label>Email <input type="email" name="email" autocomplete="email" required></label>
            <label>Password <input type="password" name="password" autocomplete="new-password" required></label>
            <button class="button" type="submit">Sign Up</button>
        </form>
        <p class="auth-switch">Already registered? <a href="{{ route('login') }}">Log in</a></p>
    </div>
</section>
@endsection
