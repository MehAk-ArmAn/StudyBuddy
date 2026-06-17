@extends('layouts.app')

@section('content')
    <div class="cosmic-bg" aria-hidden="true">
        <span class="glow glow-one"></span>
        <span class="glow glow-two"></span>
        <span class="glow glow-three"></span>
        <span class="starfield starfield-a"></span>
        <span class="starfield starfield-b"></span>
    </div>

    @php
        $sectionMap = $sections->keyBy('section_key');
        $hero = $sectionMap->get('hero');
        $apps = $sectionMap->get('apps');
        $why = $sectionMap->get('why');
        $parents = $sectionMap->get('parents');
        $teachers = $sectionMap->get('teachers');
        $stats = $sectionMap->get('stats');
        $cta = $sectionMap->get('cta');
        $imgBase = 'assets/studybuddy-imgs/';
    @endphp

    @if ($hero)
        <section id="top" class="sb-hero">
            <img class="float-planet planet-left" src="{{ asset($imgBase . 'decor/planets/planet-ringed-lg.png') }}" alt="" aria-hidden="true">
            <img class="float-planet planet-right" src="{{ asset($imgBase . 'decor/planets/planet-purple-lg.png') }}" alt="" aria-hidden="true">

            <div class="hero-copy">
                @if ($hero->eyebrow)<p class="eyebrow">{{ $hero->eyebrow }}</p>@endif
                <h1>
                    @php
                        $title = $hero->title ?? '';
                        $highlight = data_get($hero->settings, 'highlight', 'Your Way');
                    @endphp
                    {!! str_replace($highlight, '<span>' . e($highlight) . '</span>', e($title)) !!}
                </h1>
                @if ($hero->subtitle)<p class="hero-subtitle">{{ $hero->subtitle }}</p>@endif
                @if ($hero->body)<p class="hero-body">{{ $hero->body }}</p>@endif
                <div class="hero-actions">
                    @if ($hero->button_label)<a class="btn btn-primary" href="{{ $hero->button_url ?: '#apps' }}">{{ $hero->button_label }}</a>@endif
                    @if ($hero->secondary_button_label)<a class="btn btn-ghost" href="{{ $hero->secondary_button_url ?: '#why' }}">{{ $hero->secondary_button_label }}</a>@endif
                </div>
            </div>

            <div class="hero-art-wrap">
                <span class="orbit orbit-one"></span>
                <span class="orbit orbit-two"></span>
                <span class="sparkle s1">✦</span><span class="sparkle s2">✦</span><span class="sparkle s3">★</span><span class="sparkle s4">✧</span>
                @if ($hero->image_path)<img class="hero-art" src="{{ asset($hero->image_path) }}" alt="{{ $hero->title }}">@endif
            </div>
        </section>
    @endif

    @if ($apps)
        <section id="apps" class="sb-section app-store-panel">
            <div class="section-heading wide-heading">
                <div>
                    @if ($apps->eyebrow)<p class="eyebrow">{{ $apps->eyebrow }}</p>@endif
                    <h2>{{ $apps->title }}</h2>
                    @if ($apps->subtitle)<p>{{ $apps->subtitle }}</p>@endif
                </div>
                <div class="search-pill">⌕ Search apps...</div>
            </div>
            <div class="app-grid">
                @foreach ($apps->items as $item)
                    <article class="app-card">
                        @if ($item->image_path)<img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">@endif
                        <h3>{{ $item->title }}</h3>
                        @if ($item->subtitle)<p class="mini-copy">{{ $item->subtitle }}</p>@endif
                        <div class="card-foot"><span class="rating">⭐ {{ $item->badge_text }}</span>@if ($item->button_label)<a href="{{ $item->button_url ?: '#cta' }}">{{ $item->button_label }}</a>@endif</div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if ($why)
        <section id="why" class="sb-section why-section">
            <div class="section-heading">
                @if ($why->eyebrow)<p class="eyebrow">{{ $why->eyebrow }}</p>@endif
                <h2>{{ $why->title }}</h2>
                @if ($why->subtitle)<p>{{ $why->subtitle }}</p>@endif
            </div>
            <div class="feature-grid">
                @foreach ($why->items as $item)
                    <article class="feature-card"><span>{{ $item->badge_text }}</span><h3>{{ $item->title }}</h3><p>{{ $item->subtitle }}</p></article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="split-zone">
        @foreach ([$parents, $teachers] as $split)
            @if ($split)
                <article id="{{ $split->section_key }}" class="split-card">
                    <div>
                        @if ($split->eyebrow)<p class="eyebrow">{{ $split->eyebrow }}</p>@endif
                        <h2>{{ $split->title }}</h2>
                        @if ($split->subtitle)<p>{{ $split->subtitle }}</p>@endif
                        @if ($split->button_label)<a class="btn btn-ghost" href="{{ $split->button_url ?: '#cta' }}">{{ $split->button_label }}</a>@endif
                    </div>
                    @if ($split->image_path)<img src="{{ asset($split->image_path) }}" alt="{{ $split->title }}">@endif
                </article>
            @endif
        @endforeach
    </section>

    @if ($stats)
        <section id="stats" class="stats-strip">
            @foreach ($stats->items as $item)
                <article><span>{{ $item->badge_text }}</span><strong>{{ $item->title }}</strong><p>{{ $item->subtitle }}</p></article>
            @endforeach
        </section>
    @endif

    @if ($cta)
        <section id="cta" class="sb-section cta-card">
            <div>
                @if ($cta->eyebrow)<p class="eyebrow">{{ $cta->eyebrow }}</p>@endif
                <h2>{{ $cta->title }}</h2>
                @if ($cta->subtitle)<p>{{ $cta->subtitle }}</p>@endif
                <div class="hero-actions">
                    @if ($cta->button_label)<a class="btn btn-primary" href="{{ $cta->button_url ?: '#top' }}">{{ $cta->button_label }}</a>@endif
                    @if ($cta->secondary_button_label)<a class="btn btn-ghost" href="{{ $cta->secondary_button_url ?: '#top' }}">{{ $cta->secondary_button_label }}</a>@endif
                </div>
            </div>
            @if ($cta->image_path)<img src="{{ asset($cta->image_path) }}" alt="{{ $cta->title }}">@endif
        </section>
    @endif
@endsection
