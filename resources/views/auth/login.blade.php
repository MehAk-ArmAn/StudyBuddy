@extends('layouts.app')

@section('title', 'Login to StudyBuddy')

@section('content')
<section class="sb-auth-stage" data-auth-page="login">
    <div class="sb-auth-card">
        <div class="sb-auth-intro">
            <p class="eyebrow">Welcome back</p>
            <h1>Open your StudyBuddy dashboard.</h1>
            <p>Jump back into your apps, quests, points, and role-based tools.</p>
            <div class="sb-auth-flip-grid small">
                <article><span>🎮</span><strong>Apps</strong><p>Pick up where you left off.</p></article>
                <article><span>✨</span><strong>Quests</strong><p>Keep your saved missions moving.</p></article>
            </div>
        </div>

        <form method="POST" action="{{ route('login') }}" class="sb-auth-form">
            @csrf

            @if ($errors->any())
                <div class="sb-auth-error-summary" role="alert" aria-live="polite">
                    <div class="sb-auth-error-icon">!</div>
                    <div>
                        <strong>Almost there — fix these first:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif


            @if(session('status'))
                <div class="sb-auth-status">{{ session('status') }}</div>
            @endif

            <label>
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com">
                @error('email')<small>{{ $message }}</small>@enderror
            </label>

            <label>
                <span>Access key</span>
                <input type="password" name="password" required autocomplete="current-password">
                @error('password')<small>{{ $message }}</small>@enderror
            </label>

            <label class="sb-check-row">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <span>Keep me logged in</span>
            </label>

            <button class="sb-auth-submit" type="submit">Open dashboard</button>

            <p class="sb-auth-switch">
                New to StudyBuddy? <a href="{{ route('register') }}">Create account</a>
            </p>
        </form>
    </div>
</section>
@endsection
