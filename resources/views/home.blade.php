@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-home-hero-clean.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-home-hero-clean.css')) ? filemtime(public_path('assets/css/studybuddy-home-hero-clean.css')) : time() }}">
@endpush
@section('content')
    @php
        $sectionMap = $sections->keyBy('section_key');
        $hero = $sectionMap->get('hero');
        $what = $sectionMap->get('what_we_do');
        $apps = $sectionMap->get('apps_preview') ?? $sectionMap->get('apps');
        $pages = $sectionMap->get('page_paths');
        $why = $sectionMap->get('why');
        $trust = $sectionMap->get('trust');
        $cta = $sectionMap->get('cta');
        $assetBase = $settings['asset_raw_base'] ?? 'assets/studybuddy-imgs/';
        $imageUrl = function (?string $path): ?string { if (blank($path)) return null; return preg_match('/^https?:\/\//i', $path) ? $path : asset($path); };
    @endphp
    <div class="cosmic-bg" aria-hidden="true"><span class="glow glow-one"></span><span class="glow glow-two"></span><span class="glow glow-three"></span><span class="starfield starfield-a"></span><span class="starfield starfield-b"></span></div>
    <div class="sparkle-field" data-sparkle-field aria-hidden="true"></div>
    @if ($hero)
        <section id="top" class="sb-hero home-hero journey-hero">
            <div class="hero-copy">
                @if ($hero->eyebrow)<p class="eyebrow">{{ $hero->eyebrow }}</p>@endif
                <h1>
                    @php
                        $title = $hero->title ?? '';
                        $highlight = data_get($hero->settings, 'highlight', 'Your Way');

                        // The highlight renders as its own line (span is display:block).
                        // Any punctuation straight after it has to travel with it,
                        // otherwise a lone full stop wraps onto a line of its own.
                        $heroHeading = $highlight !== '' && str_contains($title, $highlight)
                            ? preg_replace(
                                '/'.preg_quote($highlight, '/').'([^\p{L}\p{N}]*)$/u',
                                '<span>'.e($highlight).'$1</span>',
                                e($title),
                                1
                            )
                            : e($title);

                        // Not at the end of the title: highlight it where it sits.
                        if ($heroHeading === e($title) && $highlight !== '') {
                            $heroHeading = str_replace($highlight, '<span>'.e($highlight).'</span>', e($title));
                        }
                    @endphp
                    {!! $heroHeading !!}
                </h1>
                @if ($hero->subtitle)<p class="hero-subtitle">{{ $hero->subtitle }}</p>@endif
                @if ($hero->body)<p class="hero-body">{{ $hero->body }}</p>@endif
                <div class="hero-actions">@if ($hero->button_label)<a class="btn btn-primary" href="{{ $hero->button_url ?: url('/apps') }}">{{ $hero->button_label }}</a>@endif @if ($hero->secondary_button_label)<a class="btn btn-ghost" href="{{ $hero->secondary_button_url ?: route('register') }}">{{ $hero->secondary_button_label }}</a>@endif</div>
            </div>
            <div class="hero-art-wrap home-hero-visual">
                <span class="home-hero-glow" aria-hidden="true"></span>
                @if ($hero->image_path)<img class="hero-art" src="{{ $imageUrl($hero->image_path) }}" alt="The StudyBuddy dolphin leaping out of an open book" width="900" height="900" fetchpriority="high" decoding="async">@endif
            </div>
        </section>
    @endif
    <section class="personalization-lab" data-role-demo><div class="personalization-card"><p class="eyebrow">Personalized learning</p><h2>Your dashboard should not look like everyone else’s.</h2><p>StudyBuddy adapts the experience by role: learner, parent, teacher, or independent learner.</p><div class="role-choice-cloud"><button class="active" type="button" data-role-choice="student">Student</button><button type="button" data-role-choice="parent">Parent</button><button type="button" data-role-choice="teacher">Teacher</button><button type="button" data-role-choice="independent">Independent Learner</button></div></div><div class="personalization-card role-output-card"><p class="eyebrow">Live preview</p><div class="role-output" data-role-output>Your dashboard becomes a daily quest board with practice, focus, streaks, and friendly progress.</div></div></section>
    @if ($what)<section id="what-we-do" class="sb-section what-we-do-section"><div class="section-heading">@if ($what->eyebrow)<p class="eyebrow">{{ $what->eyebrow }}</p>@endif<h2>{{ $what->title }}</h2>@if ($what->subtitle)<p>{{ $what->subtitle }}</p>@endif</div><div class="what-grid">@foreach ($what->items as $item)<article class="what-card lively-card"><span class="what-icon">{{ $item->badge_text ?: '' }}</span><h3>{{ $item->title }}</h3>@if ($item->subtitle)<p>{{ $item->subtitle }}</p>@endif @if ($item->button_label)<a href="{{ $item->button_url ?: '#page-paths' }}">{{ $item->button_label }}</a>@endif</article>@endforeach</div></section>@endif
    @if ($apps && $featuredApps->isNotEmpty())
        <section id="apps" class="sb-section home-app-preview launcher-preview-strip">
            <div class="section-heading wide-heading">
                <div>
                    @if ($apps->eyebrow)<p class="eyebrow">{{ $apps->eyebrow }}</p>@endif
                    <h2>{{ $apps->title }}</h2>
                    @if ($apps->subtitle)<p>{{ $apps->subtitle }}</p>@endif
                </div>
                <a class="btn btn-ghost" href="{{ url('/apps') }}">{{ $apps->button_label ?: 'See all apps' }}</a>
            </div>
            <div class="app-preview-row">
                {{-- Real published apps, straight from the Apps CMS. --}}
                @foreach ($featuredApps as $featured)
                    @php($colors = $featured->accentColors())
                    <a class="app-card preview-card lively-card" href="{{ route('studybuddy.apps.show', $featured->slug) }}">
                        <span class="app-card__art" style="--sb-a:{{ $colors[0] }};--sb-b:{{ $colors[1] }}">
                            @if ($featured->safeHeroImage())
                                <img src="{{ $imageUrl($featured->safeHeroImage()) }}" alt="" loading="lazy" onerror="this.remove()">
                            @endif
                            <em>{{ $featured->icon ?: $featured->initials() }}</em>
                        </span>
                        <h3>{{ $featured->name }}</h3>
                        @if ($featured->tagline)<p class="mini-copy">{{ $featured->tagline }}</p>@endif
                    </a>
                @endforeach
            </div>
        </section>
    @endif
    @if ($pages)<section id="page-paths" class="sb-section page-paths-section path-orbit-section"><div class="section-heading">@if ($pages->eyebrow)<p class="eyebrow">{{ $pages->eyebrow }}</p>@endif<h2>{{ $pages->title }}</h2>@if ($pages->subtitle)<p>{{ $pages->subtitle }}</p>@endif</div><div class="path-grid">@foreach ($pages->items as $item)<a class="path-card lively-card" href="{{ $item->button_url ?: '#' }}">@if ($item->image_path)<img src="{{ $imageUrl($item->image_path) }}" alt="" aria-hidden="true" loading="lazy" decoding="async" width="520" height="520">@endif<span>{{ $item->badge_text ?: 'Path' }}</span><h3>{{ $item->title }}</h3>@if ($item->subtitle)<p>{{ $item->subtitle }}</p>@endif</a>@endforeach</div></section>@endif
    @if ($why)<section id="why" class="sb-section why-section"><div class="section-heading">@if ($why->eyebrow)<p class="eyebrow">{{ $why->eyebrow }}</p>@endif<h2>{{ $why->title }}</h2>@if ($why->subtitle)<p>{{ $why->subtitle }}</p>@endif</div><div class="feature-grid">@foreach ($why->items as $item)<article class="feature-card lively-card"><span>{{ $item->badge_text ?: '' }}</span><h3>{{ $item->title }}</h3><p>{{ $item->subtitle }}</p></article>@endforeach</div></section>@endif
    @if ($trust)<section class="sb-section trust-console"><div class="section-heading wide-heading"><div>@if ($trust->eyebrow)<p class="eyebrow">{{ $trust->eyebrow }}</p>@endif<h2>{{ $trust->title }}</h2>@if ($trust->subtitle)<p>{{ $trust->subtitle }}</p>@endif</div>@if ($trust->button_label)<a class="btn btn-ghost" href="{{ $trust->button_url ?: route('register') }}">{{ $trust->button_label }}</a>@endif</div><div class="feature-grid">@foreach ($trust->items as $item)<article class="feature-card lively-card">@if ($item->image_path)<img class="feature-img" src="{{ $imageUrl($item->image_path) }}" alt="" aria-hidden="true" loading="lazy" decoding="async">@else<span>{{ $item->badge_text ?: '' }}</span>@endif<h3>{{ $item->title }}</h3>@if ($item->subtitle)<p>{{ $item->subtitle }}</p>@endif</article>@endforeach</div></section>@endif
    @if ($cta)<section id="cta" class="sb-section cta-card"><div>@if ($cta->eyebrow)<p class="eyebrow">{{ $cta->eyebrow }}</p>@endif<h2>{{ $cta->title }}</h2>@if ($cta->subtitle)<p>{{ $cta->subtitle }}</p>@endif<div class="hero-actions">@if ($cta->button_label)<a class="btn btn-primary" href="{{ $cta->button_url ?: route('pages.contact-us') }}">{{ $cta->button_label }}</a>@endif @if ($cta->secondary_button_label)<a class="btn btn-ghost" href="{{ $cta->secondary_button_url ?: route('pages.support') }}">{{ $cta->secondary_button_label }}</a>@endif</div></div>@if ($cta->image_path)<img src="{{ $imageUrl($cta->image_path) }}" alt="" aria-hidden="true" loading="lazy" decoding="async" width="900" height="900">@endif</section>@endif
    @includeIf('partials.home-living-sections')
    @includeIf('partials.home-vibe-upgrade')
@endsection
