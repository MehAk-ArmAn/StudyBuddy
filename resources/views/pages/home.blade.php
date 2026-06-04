@extends('layouts.app')

@section('title', 'Home')

@section('content')
<section class="landing-stage reveal-on-load">
    <div class="landing-panel glass-panel">
        <div class="hero-copy">
            <div class="pill-row">
                <span class="cosmic-pill">✺ Interactive Animations</span>
                <span class="cosmic-pill">✦ Magical Experience</span>
                <span class="cosmic-pill">● Multi-Role System</span>
            </div>
            <h1>Learn. Play. Grow. <span>Your Way.</span></h1>
            <p>A fun and safe learning universe where students can practice, play, focus, and grow with their personal study buddy.</p>
            <div class="hero-actions">
                <a class="button" href="{{ route('apps.math-quest.play') }}">Start Learning</a>
                <a class="button button-ghost" href="{{ route('apps.index') }}">Explore Apps</a>
            </div>
        </div>

        <div class="hero-visual-wrap">
            <span class="hero-planet mini-planet-a"></span>
            <span class="hero-planet mini-planet-b"></span>
            <span class="hero-star hero-star-a">★</span>
            <span class="hero-star hero-star-b">✦</span>
            @include('partials.image-placeholder', ['label' => 'HERO_MASCOT_IMAGE', 'variant' => 'mascot', 'caption' => 'Dolphin + open book mascot art'])
        </div>

        <div class="shortcut-strip">
            @foreach($featuredApps as $app)
                <a class="shortcut-card tilt-card" href="{{ $app->launch_path ?? route('apps.index') }}">
                    @include('partials.image-placeholder', ['label' => $app->image_label, 'variant' => 'shortcut', 'caption' => $app->title])
                    <span>{{ $app->title }}</span>
                </a>
            @endforeach
            <a class="shortcut-card tilt-card" href="{{ route('apps.index') }}">
                @include('partials.image-placeholder', ['label' => 'APP_SHORTCUT_MORE_IMAGE', 'variant' => 'shortcut', 'caption' => 'More apps'])
                <span>More Apps</span>
            </a>
        </div>
    </div>

    <div class="stats-row glass-panel">
        <div><strong>50+</strong><span>Mini Apps</span></div>
        <div><strong>10K+</strong><span>Students</span></div>
        <div><strong>100K+</strong><span>Lessons Completed</span></div>
        <div><strong>4.9</strong><span>Parent Rating ★★★★★</span></div>
    </div>
</section>
@endsection
