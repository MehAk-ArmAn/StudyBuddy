@extends('layouts.app')

@section('title', $app->title . ' Coming Soon')
@section('body_class', 'page-shell premium-apps-page')

@section('content')
<section class="math-quest-page reveal-on-load" aria-labelledby="coming-soon-title">
    <div class="math-page-shell">
        <div class="glass-panel auth-panel tilt-card">
            <p class="eyebrow">Coming Soon</p>
            <h1 id="coming-soon-title">{{ $app->title }} is preparing for launch</h1>
            <p>{{ $app->description }}</p>
            <div class="math-action-row">
                <a class="button" href="{{ route('apps.index') }}">Explore Apps</a>
                <a class="button button-ghost" href="{{ route('register') }}">Get Launch Updates</a>
            </div>
        </div>
    </div>
</section>
@endsection
