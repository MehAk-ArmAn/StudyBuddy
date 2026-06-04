@extends('layouts.app')

@section('title', 'Home')

@section('content')
<section class="universe-heading reveal-on-load">
    <h1><span>StudyBuddy</span> – The Complete Cosmic Learning Universe</h1>
    <p>Learn. Play. Grow. Your Way.</p>
    <div class="badge-cloud">
        <span>✺ Interactive Animations</span>
        <span>✦ Magical Experience</span>
        <span>● Multi-Role System</span>
        <span>◈ Web + Mobile Apps</span>
        <span>☻ Fully Customizable (Admin)</span>
    </div>
</section>

<section class="landing-panel exact-panel reveal-on-load">
    @if(file_exists(public_path('assets/studybuddy/planet-ringed-lg.png')))
        <img class="bg-planet bg-planet-ringed" src="{{ asset('assets/studybuddy/planet-ringed-lg.png') }}" alt="" aria-hidden="true">
    @else
        <span class="bg-planet bg-planet-ringed css-planet" aria-hidden="true"></span>
    @endif
    @if(file_exists(public_path('assets/studybuddy/planet-purple-lg.png')))
        <img class="bg-planet bg-planet-purple" src="{{ asset('assets/studybuddy/planet-purple-lg.png') }}" alt="" aria-hidden="true">
    @else
        <span class="bg-planet bg-planet-purple css-planet purple" aria-hidden="true"></span>
    @endif

    <div class="hero-copy">
        <p class="eyebrow">01 Landing Page</p>
        <h2>Learn. Play.<br>Grow.<br><span>Your Way.</span></h2>
        <p>A fun and safe learning universe where students can practice, play, focus, and grow with their personal study buddy.</p>
        <div class="hero-actions">
            <a class="button" href="{{ route('apps.math-quest.play') }}">Start Learning</a>
            <a class="button button-ghost" href="{{ route('apps.index') }}">Explore Apps</a>
        </div>
    </div>

    <div class="hero-mascot-zone">
        <span class="hero-chat">•••</span>
        @include('partials.image-placeholder', ['label' => 'HERO_MASCOT_IMAGE', 'src' => 'assets/studybuddy/hero-dolphin-book.png', 'variant' => 'hero', 'caption' => 'StudyBuddy dolphin and book mascot'])
    </div>

    <div class="home-app-strip">
        @foreach($featuredApps as $app)
            <a class="home-app-icon tilt-card" href="{{ $app->launch_path ?? route('apps.index') }}">
                @include('partials.image-placeholder', ['label' => $app->image_label, 'src' => $app->image_path, 'variant' => 'icon', 'caption' => $app->title])
                <span>{{ $app->title }}</span>
            </a>
        @endforeach
        @foreach(\App\Support\DemoContent::miniApps()->skip(3)->take(5) as $app)
            <a class="home-app-icon tilt-card" href="{{ route('apps.index') }}">
                @include('partials.image-placeholder', ['label' => $app->image_label, 'src' => $app->image_path, 'variant' => 'icon', 'caption' => $app->title])
                <span>{{ $app->title }}</span>
            </a>
        @endforeach
    </div>
</section>

<section class="stats-ribbon reveal-on-load">
    <div><strong>50+</strong><span>Mini Apps</span></div>
    <div><strong>10K+</strong><span>Students</span></div>
    <div><strong>100K+</strong><span>Lessons Completed</span></div>
    <div><strong>4.9</strong><span>Parent Rating ★★★★★</span></div>
</section>
@endsection
