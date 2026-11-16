@extends('layouts.app')
@php
    $sectionMap = collect($sections ?? [])->keyBy('section_key');
    $hero = $sectionMap->get('hero');
    $what = $sectionMap->get('what_we_do');
    $appsSection = $sectionMap->get('apps_preview') ?? $sectionMap->get('apps');
    $pagesSection = $sectionMap->get('page_paths');
    $whySection = $sectionMap->get('why');
    $trustSection = $sectionMap->get('trust');
    $cta = $sectionMap->get('cta');
    $featured = collect($featuredApps ?? []);
    $imageUrl = static function (?string $path): ?string {
        if (blank($path)) return null;
        return preg_match('/^https?:\/\//i', $path) ? $path : asset(ltrim($path, '/'));
    };
    $heroImage = !blank($hero?->image_path)
        ? $imageUrl($hero->image_path)
        : asset('assets/studybuddy-brand/pages/hero-dolphin-book.webp');
    $sb8CarouselItems = collect($discoveryApps ?? $featuredApps ?? [])->filter(fn ($app) => filled($app->safeHeroImage()))->take(12)->map(function ($app) use ($imageUrl) {
        $demo = str_starts_with((string) $app->slug, 'demo-next-');
        return [
            'src' => $imageUrl($app->safeHeroImage()),
            'alt' => 'Artwork for '.(string) $app->name,
            'title' => (string) $app->name,
            'subtitle' => $demo ? 'DEMO · NOT PLAYABLE' : ((string) ($app->category ?: 'Learning activity')),
            'url' => route('studybuddy.apps.show', $app->slug),
            'demo' => $demo,
        ];
    })->values()->all();
@endphp

Install the Spiral Funnel component and use it with the exact configuration below.

```bash
npx shadcn@latest add @reactbits-starter/spiral-funnel-tw
```

```tsx
<SpiralFunnel /> <!-- just added this for the spiral bg but look what appeared -->
```

Docs: https://pro.reactbits.dev/docs/components/spiral-funnel 

@push('styles')
<!-- SB_WAVE7_CINEMATIC_HOME -->
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-w7.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-w7.css')) }}">
<!-- SB_WAVE8_CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-w8.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-w8.css')) }}">
@if(file_exists(public_path('assets/css/studybuddy-wave2-hero.css')))
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-wave2-hero.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-wave2-hero.css')) }}">
@endif
@endpush
@push('scripts')
<script defer src="{{ asset('assets/js/studybuddy-w7.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-w7.js')) }}"></script>
@endpush

@push('styles')
<!-- STUDYBUDDY_IMAGEWORKS_REAL_CODE -->
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-imageworks.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-imageworks.css')) }}">
@endpush
@push('scripts')
<script defer src="{{ asset('assets/js/studybuddy-imageworks.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-imageworks.js')) }}"></script>
@endpush

@section('content')
@include('partials.studybuddy-imageworks-landing')
@endsection
