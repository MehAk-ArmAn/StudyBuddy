@extends('layouts.app')

@section('content')
<main class="sb-final-shell sb-webplay-page">
    <section class="sb-final-hero compact">
        <div>
            <p class="sb-final-kicker">Web Play Shell</p>
            <h1>{{ $app->icon }} {{ $app->name }}</h1>
            <p>{{ $app->description }}</p>
            <div class="sb-final-actions">
                <a href="{{ route('studybuddy.apps.show', $app->slug) }}" class="sb-final-btn sb-final-btn-soft">App Details</a>
                <a href="{{ route('studybuddy.apps') }}" class="sb-final-btn sb-final-btn-soft">All Apps</a>
                @auth<a href="{{ route('studybuddy.final.points-wallet') }}" class="sb-final-btn">Points Wallet</a>@endauth
            </div>
        </div>
    </section>

    <section class="sb-webplay-frame">
        <div class="sb-webplay-screen">
            <span class="sb-webplay-icon">{{ $app->icon ?: '🎮' }}</span>
            <h2>{{ $app->name }} web build slot</h2>
            @guest
                <p>You are viewing preview mode. Login to start web sessions, save quests, and earn StudyBuddy points.</p>
                <a href="{{ route('login') }}" class="sb-final-btn">Login to unlock</a>
            @else
                @if($app->is_web_enabled)
                    <p>This shell is ready for the real hosted web game. Paste the real hosted URL in Admin → Final Platform when the mini-app build is ready.</p>
                    <form method="POST" action="{{ route('studybuddy.final.session.complete') }}" class="sb-final-inline-form">
                        @csrf
                        <input type="hidden" name="app_slug" value="{{ $app->slug }}">
                        <button class="sb-final-btn" type="submit">Complete Demo Session +{{ $app->points_reward }} pts</button>
                    </form>
                @else
                    <p>This app is preview-only right now. Admin can enable Web Play when the hosted build is ready.</p>
                    <span class="sb-final-btn sb-final-btn-disabled">Coming Soon</span>
                @endif
            @endguest
        </div>
    </section>
</main>
@endsection
