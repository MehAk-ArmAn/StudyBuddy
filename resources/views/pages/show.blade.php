@extends('layouts.app')

@section('content')
    <div class="cosmic-bg" aria-hidden="true">
        <span class="glow glow-one"></span>
        <span class="glow glow-two"></span>
        <span class="glow glow-three"></span>
        <span class="starfield starfield-a"></span>
        <span class="starfield starfield-b"></span>
    </div>
    <div class="sparkle-field" data-sparkle-field aria-hidden="true"></div>

    <section id="top" class="page-hero sb-hero">
        <img class="float-planet planet-left" src="{{ asset('assets/studybuddy-imgs/decor/planets/planet-ringed-lg.png') }}" alt="" aria-hidden="true">
        <div class="hero-copy">
            @if ($page->eyebrow)<p class="eyebrow">{{ $page->eyebrow }}</p>@endif
            <h1>{!! str_replace('StudyBuddy', '<span>StudyBuddy</span>', e($page->hero_title ?: $page->title)) !!}</h1>
            @if ($page->hero_subtitle)<p class="hero-subtitle">{{ $page->hero_subtitle }}</p>@endif
            @if ($page->hero_body)<p class="hero-body">{{ $page->hero_body }}</p>@endif
            <div class="hero-actions">
                @if ($page->button_label)<a class="btn btn-primary" href="{{ $page->button_url ?: '#sections' }}">{{ $page->button_label }}</a>@endif
                @if ($page->secondary_button_label)<a class="btn btn-ghost" href="{{ $page->secondary_button_url ?: route('home') }}">{{ $page->secondary_button_label }}</a>@endif
            </div>
        </div>
        <div class="hero-art-wrap page-art-wrap">
            <span class="orbit orbit-one"></span>
            <span class="sparkle s1">✦</span><span class="sparkle s2">★</span><span class="sparkle s3">✧</span>
            @if ($page->hero_image_path)<img class="hero-art" src="{{ asset($page->hero_image_path) }}" alt="{{ $page->title }}">@endif
        </div>
    </section>

    <div id="sections" class="page-section-stack">
        @foreach ($page->sections as $section)
            @php $type = $section->section_type; @endphp

            @if ($type === 'app-grid')
                <section class="sb-section app-store-panel">
                    <div class="section-heading wide-heading">
                        <div>
                            @if ($section->eyebrow)<p class="eyebrow">{{ $section->eyebrow }}</p>@endif
                            <h2>{{ $section->title }}</h2>
                            @if ($section->subtitle)<p>{{ $section->subtitle }}</p>@endif
                        </div>
                        @if ($section->button_label)<a class="btn btn-ghost" href="{{ $section->button_url ?: '#top' }}">{{ $section->button_label }}</a>@endif
                    </div>
                    <div class="app-grid">
                        @foreach ($section->items as $item)
                            <article class="app-card lively-card">
                                @if ($item->image_path)<img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">@endif
                                <h3>{{ $item->title }}</h3>
                                @if ($item->subtitle)<p class="mini-copy">{{ $item->subtitle }}</p>@endif
                                @if ($item->body)<p>{{ $item->body }}</p>@endif
                                <div class="card-foot"><span class="rating">⭐ {{ $item->badge_text }}</span>@if ($item->button_label)<a href="{{ $item->button_url ?: '#top' }}">{{ $item->button_label }}</a>@endif</div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @elseif ($type === 'split')
                <section class="split-zone page-split-zone">
                    @foreach ($section->items as $item)
                        <article class="split-card lively-card">
                            <div>
                                @if ($item->badge_text)<p class="eyebrow">{{ $item->badge_text }}</p>@endif
                                <h2>{{ $item->title }}</h2>
                                @if ($item->subtitle)<p>{{ $item->subtitle }}</p>@endif
                                @if ($item->body)<p>{{ $item->body }}</p>@endif
                                @if ($item->button_label)<a class="btn btn-ghost" href="{{ $item->button_url ?: '#top' }}">{{ $item->button_label }}</a>@endif
                            </div>
                            @if ($item->image_path)<img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">@endif
                        </article>
                    @endforeach
                </section>
            @elseif ($type === 'legal')
                <section class="sb-section legal-panel">
                    <div class="section-heading">
                        @if ($section->eyebrow)<p class="eyebrow">{{ $section->eyebrow }}</p>@endif
                        <h2>{{ $section->title }}</h2>
                        @if ($section->subtitle)<p>{{ $section->subtitle }}</p>@endif
                    </div>
                    <div class="legal-copy">
                        @if ($section->body)<p>{!! nl2br(e($section->body)) !!}</p>@endif
                        @foreach ($section->items as $item)
                            <article>
                                <h3>{{ $item->title }}</h3>
                                @if ($item->body)<p>{!! nl2br(e($item->body)) !!}</p>@endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @else
                <section class="sb-section content-card-section">
                    <div class="section-heading">
                        @if ($section->eyebrow)<p class="eyebrow">{{ $section->eyebrow }}</p>@endif
                        <h2>{{ $section->title }}</h2>
                        @if ($section->subtitle)<p>{{ $section->subtitle }}</p>@endif
                    </div>
                    @if ($section->body)<p class="center-copy">{{ $section->body }}</p>@endif
                    <div class="feature-grid">
                        @foreach ($section->items as $item)
                            <article class="feature-card lively-card">
                                @if ($item->image_path)<img class="feature-img" src="{{ asset($item->image_path) }}" alt="{{ $item->title }}">@else<span>{{ $item->badge_text }}</span>@endif
                                <h3>{{ $item->title }}</h3>
                                @if ($item->subtitle)<p>{{ $item->subtitle }}</p>@endif
                                @if ($item->body)<p>{{ $item->body }}</p>@endif
                                @if ($item->button_label)<a href="{{ $item->button_url ?: '#top' }}">{{ $item->button_label }}</a>@endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach
    </div>
@endsection
