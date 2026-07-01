<!doctype html>
@php
    $studyBuddyTheme = auth()->check()
        ? (auth()->user()->avatar_style ?: 'cosmic-dolphin')
        : 'cosmic-dolphin';
    $studyBuddyThemeClass = 'theme-'.\Illuminate\Support\Str::slug($studyBuddyTheme ?: 'cosmic-dolphin');
    $assetVersion = fn (string $path) => file_exists(public_path($path)) ? filemtime(public_path($path)) : time();
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings['seo_title'] ?? $settings['site_name'] ?? config('app.name', 'StudyBuddy') }}</title>
    @if(!empty($settings['seo_description']))<meta name="description" content="{{ $settings['seo_description'] }}">@endif
    @if(!empty($settings['seo_keywords']))<meta name="keywords" content="{{ $settings['seo_keywords'] }}">@endif
    @if(!empty($settings['favicon_path']))<link rel="icon" href="{{ preg_match('/^https?:\/\//i', $settings['favicon_path']) ? $settings['favicon_path'] : asset($settings['favicon_path']) }}">@endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v={{ $assetVersion('assets/css/site.css') }}">
    @foreach([
        'assets/css/experience.css',
        'assets/css/auth.css',
        'assets/css/sb-premium-fixes.css',
        'assets/css/sb-phase3-quest-vault.css',
        'assets/css/sb-phase4-command-center.css',
        'assets/css/sb-phase5-admin-experience.css',
        'assets/css/sb-phase6-final-platform.css',
        'assets/css/sb-final-unified-hardening.css',
        'assets/css/sb-final-app-unification.css',
    ] as $cssFile)
        @if(file_exists(public_path($cssFile)))
            <link rel="stylesheet" href="{{ asset($cssFile) }}?v={{ $assetVersion($cssFile) }}">
        @endif
    @endforeach
    @stack('styles')
    @if(file_exists(public_path('manifest.webmanifest')))<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">@endif
    <meta name="theme-color" content="#7C3AED">
</head>
<body id="top" class="studybuddy-site {{ $studyBuddyThemeClass }} sb-theme-{{ \Illuminate\Support\Str::slug($studyBuddyTheme ?: 'cosmic-dolphin') }}" data-studybuddy-theme="{{ $studyBuddyTheme }}" data-sb-auth="{{ auth()->check() ? '1' : '0' }}" data-sb-theme="{{ auth()->check() ? (auth()->user()->avatar_style ?? 'cosmic-dolphin') : 'cosmic-dolphin' }}">
    <a class="skip-to-content" href="#main-content">Skip to content</a>
    @include('partials.navbar', ['settings' => $settings ?? [], 'navigationItems' => $navigationItems ?? collect()])
    <main id="main-content" tabindex="-1">@yield('content')</main>
    @include('partials.footer', ['settings' => $settings ?? [], 'footerGroups' => $footerGroups ?? collect()])
    @foreach([
        'assets/js/site.js',
        'assets/js/experience.js',
        'assets/js/sb-premium-fixes.js',
        'assets/js/sb-phase3-quest-vault.js',
        'assets/js/sb-phase4-command-center.js',
        'assets/js/sb-phase5-admin-experience.js',
        'assets/js/sb-phase6-final-platform.js',
        'assets/js/sb-final-app-unification.js',
    ] as $jsFile)
        @if(file_exists(public_path($jsFile)))
            <script defer src="{{ asset($jsFile) }}?v={{ $assetVersion($jsFile) }}"></script>
        @endif
    @endforeach
    @stack('scripts')
</body>
</html>
