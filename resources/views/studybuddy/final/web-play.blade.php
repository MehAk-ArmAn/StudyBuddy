@extends('layouts.app')

@section('content')
<main class="sb-final-shell">
    <section class="sb-final-hero compact">
        <div>
            <p class="sb-final-kicker">Web Play Shell</p>
            <h1>{{ $app->icon }} {{ $app->name }}</h1>
            <p>{{ $app->description }}</p>
            <div class="sb-final-actions">
                <a href="{{ route('studybuddy.final.app-launchpad') }}" class="sb-final-btn sb-final-btn-soft">Back to Launchpad</a>
                <a href="{{ route('studybuddy.final.points-wallet') }}" class="sb-final-btn">Points Wallet</a>
            </div>
        </div>
    </section>

    <section class="sb-webplay-frame">
        <div class="sb-webplay-screen">
            <span class="sb-webplay-icon">{{ $app->icon ?: '🎮' }}</span>
            <h2>{{ $app->name }} playable build slot</h2>
            <p>This is the smart web hosting placeholder. When the real web game is ready, paste the hosted URL in Admin → Final Platform Cockpit → app settings.</p>
            @auth
                <form method="POST" action="{{ route('studybuddy.final.session.complete') }}" class="sb-final-inline-form">
                    @csrf
                    <input type="hidden" name="app_slug" value="{{ $app->slug }}">
                    <input type="hidden" name="title" value="Completed {{ $app->name }} demo session">
                    <button class="sb-final-btn" type="submit">Complete Demo Session +{{ $app->points_reward }} pts</button>
                    <p class="sb-final-safe-note">Points are awarded from the server app catalog, not the browser.</p>
                </form>
            @else
                <a href="{{ route('login') }}" class="sb-final-btn">Login to earn points</a>
            @endauth
        </div>
    </section>
</main>
@endsection
