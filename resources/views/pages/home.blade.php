@extends('layouts.app')

@section('title', 'Home')

@section('content')
<section class="hero-grid section-pad">
    <div class="hero-copy">
        <p class="eyebrow">{{ $content->get('home.hero')?->metadata['eyebrow'] ?? 'StudyBuddy Galaxy OS' }}</p>
        <h1>{{ $content->get('home.hero')?->title ?? 'A premium cosmic universe for confident learning' }}</h1>
        <p class="lede">{{ $content->get('home.hero')?->body ?? 'StudyBuddy turns learning into luminous mini missions.' }}</p>
        <div class="hero-actions">
            <a class="button" href="{{ route('apps.index') }}">Explore apps</a>
            <a class="button ghost" href="{{ route('showcase') }}">View showcase</a>
        </div>
    </div>
    <div class="hero-orbit glass-card">
        <div class="orbit-ring"></div>
        <div class="buddy-orb">🐬📖</div>
        <div class="floating-chip chip-one">+240 XP</div>
        <div class="floating-chip chip-two">Math streak</div>
        <div class="floating-chip chip-three">Galaxy badge</div>
    </div>
</section>

<section class="section-pad split-section">
    @include('partials.mascot', ['title' => $content->get('home.mascot')?->title ?? 'Meet Buddy'])
    <div class="glass-card feature-panel">
        <p class="eyebrow">Premium foundation</p>
        <h2>No Bootstrap look. No generic template.</h2>
        <p>This foundation uses custom Blade partials, handcrafted CSS, cosmic UI motion, and reusable cards ready for product-specific growth.</p>
    </div>
</section>

<section class="section-pad">
    <div class="section-heading">
        <p class="eyebrow">Launch-ready mini apps</p>
        <h2>Play Store style learning products</h2>
    </div>
    <div class="app-grid">
        @forelse($featuredApps as $app)
            @include('partials.app-card', ['app' => $app])
        @empty
            <p class="empty-state">Run <code>php artisan db:seed</code> to load the demo mini apps.</p>
        @endforelse
    </div>
</section>
@endsection
