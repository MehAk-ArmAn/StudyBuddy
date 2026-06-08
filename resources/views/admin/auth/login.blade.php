<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin Login · StudyBuddy</title><link rel="stylesheet" href="{{ asset('assets/css/studybuddy.css') }}"></head>
<body class="studybuddy-admin-cms admin-login-page">
    <form class="admin-login-card" method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <img src="{{ asset('assets/studybuddy/logo-icon.png') }}" alt="StudyBuddy logo">
        <h1>StudyBuddy Admin</h1>
        <p>Sign in to manage content, apps, rewards, pages, and settings.</p>
        @if($errors->any())<div class="admin-cms-alert is-error">{{ $errors->first() }}</div>@endif
        <label>Email<input name="email" type="email" value="{{ old('email') }}" required autofocus></label>
        <label>Password<input name="password" type="password" required></label>
        <button type="submit">Login</button>
    </form>
</body>
</html>
