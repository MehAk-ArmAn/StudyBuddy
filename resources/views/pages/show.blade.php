@extends('layouts.app')
@section('title', $page->meta_title ?: $page->title)
@section('content')
    @php
        $imageUrl = function (?string $path): ?string { if (blank($path)) return null; return preg_match('/^https?:\/\//i', $path) ? $path : asset($path); };
        $assetBase = $settings['asset_raw_base'] ?? 'assets/studybuddy-imgs/';
        $heroImage = $imageUrl($page->hero_image_path);
        $planet = null;
        $appSection = $page->sections->firstWhere('section_type', 'app-grid');
    @endphp
    <div class="cosmic-bg" aria-hidden="true"><span class="glow glow-one"></span><span class="glow glow-two"></span><span class="glow glow-three"></span><span class="starfield starfield-a"></span><span class="starfield starfield-b"></span></div><div class="sparkle-field" data-sparkle-field aria-hidden="true"></div>
    <section id="top" class="page-hero sb-hero">@if ($planet)<img class="float-planet planet-left" src="{{ $planet }}" alt="" aria-hidden="true">@endif<div class="hero-copy">@if ($page->eyebrow)<p class="eyebrow">{{ $page->eyebrow }}</p>@endif<h1>{!! str_replace('StudyBuddy', '<span>StudyBuddy</span>', e($page->hero_title ?: $page->title)) !!}</h1>@if ($page->hero_subtitle)<p class="hero-subtitle">{{ $page->hero_subtitle }}</p>@endif @if ($page->hero_body)<p class="hero-body">{{ $page->hero_body }}</p>@endif<div class="hero-actions">@if ($page->button_label)<a class="btn btn-primary" href="{{ $page->button_url ?: '#sections' }}">{{ $page->button_label }}</a>@endif @if ($page->secondary_button_label)<a class="btn btn-ghost" href="{{ $page->secondary_button_url ?: route('home') }}">{{ $page->secondary_button_label }}</a>@endif</div></div><div class="hero-art-wrap page-art-wrap"><span class="orbit orbit-one"></span><span class="sparkle s1"></span><span class="sparkle s2"></span><span class="sparkle s3"></span>@if ($heroImage)<img class="hero-art" src="{{ $heroImage }}" alt="" aria-hidden="true" decoding="async" width="520" height="520">@endif</div></section>
    @if ($page->slug === 'apps' && $appSection)
        @include('pages.partials.app-launcher', ['appSection' => $appSection, 'settings' => $settings, 'imageUrl' => $imageUrl])
    @else
        <div id="sections" class="page-section-stack diverse-page-stack">
        @foreach ($page->sections as $section) @php $type=$section->section_type; @endphp
            @if ($type === 'split')<section class="split-zone page-split-zone">@foreach ($section->items as $item) @php $itemImage=$imageUrl($item->image_path); @endphp <article class="split-card lively-card"><div>@if ($item->badge_text)<p class="eyebrow">{{ $item->badge_text }}</p>@endif<h2>{{ $item->title }}</h2>@if ($item->subtitle)<p>{{ $item->subtitle }}</p>@endif @if ($item->body)<p>{{ $item->body }}</p>@endif @if ($item->button_label)<a class="btn btn-ghost" href="{{ $item->button_url ?: '#top' }}">{{ $item->button_label }}</a>@endif</div>@if ($itemImage)<img src="{{ $itemImage }}" alt="{{ $item->title }}">@endif</article>@endforeach</section>
            @elseif ($type === 'legal')<section class="sb-section legal-panel reading-panel"><div class="section-heading">@if ($section->eyebrow)<p class="eyebrow">{{ $section->eyebrow }}</p>@endif<h2>{{ $section->title }}</h2>@if ($section->subtitle)<p>{{ $section->subtitle }}</p>@endif</div><div class="legal-copy">@if ($section->body)<p>{!! nl2br(e($section->body)) !!}</p>@endif @foreach ($section->items as $item)<article><h3>{{ $item->title }}</h3>@if ($item->body)<p>{!! nl2br(e($item->body)) !!}</p>@endif</article>@endforeach</div></section>
            @else<section class="sb-section content-card-section {{ $loop->even ? 'story-river' : 'feature-lab' }}"><div class="section-heading">@if ($section->eyebrow)<p class="eyebrow">{{ $section->eyebrow }}</p>@endif<h2>{{ $section->title }}</h2>@if ($section->subtitle)<p>{{ $section->subtitle }}</p>@endif</div>@if ($section->body)<p class="center-copy">{{ $section->body }}</p>@endif<div class="feature-grid diverse-feature-grid">@foreach ($section->items as $item) @php $itemImage=$imageUrl($item->image_path); @endphp <article class="feature-card lively-card">@if ($itemImage)<img class="feature-img" src="{{ $itemImage }}" alt="{{ $item->title }}">@else<span>{{ $item->badge_text ?: '' }}</span>@endif<h3>{{ $item->title }}</h3>@if ($item->subtitle)<p>{{ $item->subtitle }}</p>@endif @if ($item->body)<p>{{ $item->body }}</p>@endif @if ($item->button_label)<a href="{{ $item->button_url ?: '#top' }}">{{ $item->button_label }}</a>@endif</article>@endforeach</div></section>@endif
        @endforeach
        </div>
    @endif
@endsection
