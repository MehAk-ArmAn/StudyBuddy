@extends('layouts.app')

@section('title', 'Admin Login')
@section('body_class', 'page-shell page-auth')

@section('content')
<section class="auth-shell reveal-on-load" aria-labelledby="admin-login-title">
    <div class="glass-panel auth-panel tilt-card">
        <p class="eyebrow">CMS access</p>
        <h1 id="admin-login-title">Admin Login</h1>
        <p>Access the StudyBuddy CMS dashboard and resource management tools.</p>
        <form class="auth-form" method="POST" action="{{ route('admin.login') }}">
            @csrf
            <label>Email <input type="email" name="email" autocomplete="email" required></label>
            <label>Password <input type="password" name="password" autocomplete="current-password" required></label>
            <button class="button" type="submit">Open Admin</button>
        </form>
        <p class="auth-switch"><a href="{{ route('login') }}">Student login</a></p>
    </div>
</section>
@endsection
