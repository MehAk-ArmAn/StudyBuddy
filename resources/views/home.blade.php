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
        $what = $sectionMap->get('what_we_do');
        $apps = $sectionMap->get('apps_preview') ?? $sectionMap->get('apps');
        $pages = $sectionMap->get('page_paths');
        $why = $sectionMap->get('why');
        $cta = $sectionMap->get('cta');
        $imgBase = 'assets/studybuddy-imgs/';
    @endphp

    <div class="sparkle-field" data-sparkle-field aria-hidden="true"></div>

    @if ($hero)
        <section id="top" class="sb-hero home-hero">
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
                    @if ($hero->button_label)<a class="btn btn-primary" href="{{ $hero->button_url ?: '#what-we-do' }}">{{ $hero->button_label }}</a>@endif
                    @if ($hero->secondary_button_label)<a class="btn btn-ghost" href="{{ $hero->secondary_button_url ?: route('pages.apps') }}">{{ $hero->secondary_button_label }}</a>@endif
                </div>
            </div>

            <div class="hero-art-wrap fun-orbit-card">
                <span class="orbit orbit-one"></span>
                <span class="orbit orbit-two"></span>
                <span class="sparkle s1">✦</span><span class="sparkle s2">✦</span><span class="sparkle s3">★</span><span class="sparkle s4">✧</span>
                <div class="popup-bubble bubble-one">{{ data_get($hero->settings, 'bubble_one', 'Daily wins ✨') }}</div>
                <div class="popup-bubble bubble-two">{{ data_get($hero->settings, 'bubble_two', 'Focus mode 🌙') }}</div>
                <div class="popup-bubble bubble-three">{{ data_get($hero->settings, 'bubble_three', 'Tiny quests 🚀') }}</div>
                @if ($hero->image_path)<img class="hero-art" src="{{ asset($hero->image_path) }}" alt="{{ $hero->title }}">@endif
            </div>
        </section>
    @endif

    @if ($what)
        <section id="what-we-do" class="sb-section what-we-do-section">
            <div class="section-heading">
                @if ($what->eyebrow)<p class="eyebrow">{{ $what->eyebrow }}</p>@endif
                <h2>{{ $what->title }}</h2>
                @if ($what->subtitle)<p>{{ $what->subtitle }}</p>@endif
            </div>
            <div class="what-grid">
                @foreach ($what->items as $item)
                    <article class="what-card lively-card">
                        <span class="what-icon">{{ $item->badge_text }}</span>
                        <h3>{{ $item->title }}</h3>
                        @if ($item->subtitle)<p>{{ $item->subtitle }}</p>@endif
                        @if ($item->button_label)<a href="{{ $item->button_url ?: '#page-paths' }}">{{ $item->button_label }}</a>@endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if ($apps)
        <section id="apps" class="sb-section home-app-preview">
            <div class="section-heading wide-heading">
                <div>
                    @if ($apps->eyebrow)<p class="eyebrow">{{ $apps->eyebrow }}</p>@endif
                    <h2>{{ $apps->title }}</h2>
                    @if ($apps->subtitle)<p>{{ $apps->subtitle }}</p>@endif
                </div>
                @if ($apps->button_label)<a class="btn btn-ghost" href="{{ $apps->button_url ?: route('pages.apps') }}">{{ $apps->button_label }}</a>@endif
            </div>
            <div class="app-preview-row">
                @foreach ($apps->items->take(4) as $item)
                    <article class="app-card preview-card">
                        @if ($item->image_path)<img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">@endif
                        <h3>{{ $item->title }}</h3>
                        @if ($item->subtitle)<p class="mini-copy">{{ $item->subtitle }}</p>@endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if ($pages)
        <section id="page-paths" class="sb-section page-paths-section">
            <div class="section-heading">
                @if ($pages->eyebrow)<p class="eyebrow">{{ $pages->eyebrow }}</p>@endif
                <h2>{{ $pages->title }}</h2>
                @if ($pages->subtitle)<p>{{ $pages->subtitle }}</p>@endif
            </div>
            <div class="path-grid">
                @foreach ($pages->items as $item)
                    <a class="path-card lively-card" href="{{ $item->button_url ?: '#' }}">
                        @if ($item->image_path)<img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">@endif
                        <span>{{ $item->badge_text }}</span>
                        <h3>{{ $item->title }}</h3>
                        @if ($item->subtitle)<p>{{ $item->subtitle }}</p>@endif
                    </a>
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
                    <article class="feature-card lively-card"><span>{{ $item->badge_text }}</span><h3>{{ $item->title }}</h3><p>{{ $item->subtitle }}</p></article>
                @endforeach
            </div>
        </section>
    @endif

    @if ($cta)
        <section id="cta" class="sb-section cta-card">
            <div>
                @if ($cta->eyebrow)<p class="eyebrow">{{ $cta->eyebrow }}</p>@endif
                <h2>{{ $cta->title }}</h2>
                @if ($cta->subtitle)<p>{{ $cta->subtitle }}</p>@endif
                <div class="hero-actions">
                    @if ($cta->button_label)<a class="btn btn-primary" href="{{ $cta->button_url ?: route('pages.contact-us') }}">{{ $cta->button_label }}</a>@endif
                    @if ($cta->secondary_button_label)<a class="btn btn-ghost" href="{{ $cta->secondary_button_url ?: route('pages.support') }}">{{ $cta->secondary_button_label }}</a>@endif
                </div>
            </div>
            @if ($cta->image_path)<img src="{{ asset($cta->image_path) }}" alt="{{ $cta->title }}">@endif
        </section>
    @endif
@endsection
