@extends('layouts.app')

@section('title', \App\Support\Cms::text('apps', 'store', 'title', 'Apps'))
@section('body_class', 'page-shell page-apps premium-apps-page')

@section('content')
@php
    $asset = fn (string $file): string => asset('assets/studybuddy/' . $file);
    $storeApps = ($apps ?? \App\Support\Cms::apps())->map(fn ($app) => [
        'title' => $app->title,
        'description' => $app->description,
        'rating' => $app->hero_metric ?: '4.8',
        'img' => $app->image_path,
        'tone' => $app->card_tone ?: 'violet',
        'url' => $app->slug === 'math-quest' ? route('apps.math-quest') : ($app->launch_path ?: route('apps.index')),
        'cta' => $app->cta_text ?: \App\Support\Cms::text('apps', 'store', 'start', 'Start'),
    ]);
@endphp
<section class="apps-store-page reveal-on-load" aria-labelledby="apps-store-title">
    <div class="apps-scene" aria-hidden="true">
        <img class="apps-scene-planet apps-scene-planet-left" src="{{ $asset('planet-ringed-lg.png') }}" alt=""><img class="apps-scene-planet apps-scene-planet-right" src="{{ $asset('planet-purple-lg.png') }}" alt=""><img class="apps-scene-sparkles apps-scene-sparkles-a" src="{{ $asset('sparkles-pack.png') }}" alt=""><span class="apps-scene-comet apps-scene-comet-a"></span><span class="apps-scene-comet apps-scene-comet-b"></span><span class="apps-scene-orb apps-scene-orb-a"></span><span class="apps-scene-orb apps-scene-orb-b"></span>
    </div>
    <div class="apps-store-shell"><div class="apps-store-panel">
        <header class="apps-store-header"><div class="apps-store-heading"><span class="apps-store-badge"><span class="apps-store-badge-glow"></span><span class="apps-store-badge-shop" aria-hidden="true"><i></i><i></i><i></i></span></span><div><h1 id="apps-store-title">{{ \App\Support\Cms::text('apps', 'store', 'title', 'Apps Store') }}</h1><p>{{ \App\Support\Cms::text('apps', 'store', 'subtitle', 'Discover fun learning apps to play, practice and grow.') }}</p></div></div><label class="apps-search" aria-label="{{ \App\Support\Cms::text('apps', 'store', 'search', 'Search apps...') }}"><span aria-hidden="true"></span><input type="search" placeholder="{{ \App\Support\Cms::text('apps', 'store', 'search', 'Search apps...') }}"></label></header>
        <nav class="apps-filter-pills" aria-label="App filters"><button class="is-active" type="button">All</button><button type="button">Popular</button><button type="button">Primary (1–6)</button><button type="button">Secondary (7–11)</button><button type="button">New</button></nav>
        <div class="apps-card-grid">@foreach($storeApps as $storeApp)<article class="apps-premium-card apps-card-{{ $storeApp['tone'] }}" data-tilt-card><span class="apps-card-shine"></span><a class="apps-card-art" href="{{ $storeApp['url'] }}" aria-label="Open {{ $storeApp['title'] }}"><span class="apps-card-halo"></span>@include('partials.cms-image', ['path' => $storeApp['img'], 'alt' => $storeApp['title'].' app icon'])</a><div class="apps-card-copy"><h2>{{ $storeApp['title'] }}</h2><p>{{ $storeApp['description'] }}</p><div class="apps-card-bottom"><span class="apps-rating"><span class="css-star"></span> {{ $storeApp['rating'] }}</span><a class="apps-start-button" href="{{ $storeApp['url'] }}">{{ $storeApp['cta'] }}</a></div></div></article>@endforeach</div>
    </div></div>
</section>
@endsection
