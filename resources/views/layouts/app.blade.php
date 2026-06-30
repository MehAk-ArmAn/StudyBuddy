<!doctype html>
@php
    $studyBuddyTheme = auth()->check()
        ? (auth()->user()->avatar_style ?: 'cosmic-dolphin')
        : request()->cookie('studybuddy_theme', 'cosmic-dolphin');

    $studyBuddyTheme = $studyBuddyTheme ?: 'cosmic-dolphin';
    $studyBuddyThemeClass = 'theme-' . \Illuminate\Support\Str::slug($studyBuddyTheme);
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings['seo_title'] ?? $settings['site_name'] ?? config('app.name', 'StudyBuddy') }}</title>
    @if (!empty($settings['seo_description']))<meta name="description" content="{{ $settings['seo_description'] }}">@endif
    @if (!empty($settings['seo_keywords']))<meta name="keywords" content="{{ $settings['seo_keywords'] }}">@endif
    @if (!empty($settings['favicon_path']))<link rel="icon" href="{{ preg_match('/^https?:\/\//i', $settings['favicon_path']) ? $settings['favicon_path'] : asset($settings['favicon_path']) }}">@endif
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/experience.css') }}?v={{ file_exists(public_path('assets/css/experience.css')) ? filemtime(public_path('assets/css/experience.css')) : time() }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/sb-premium-fixes.css') }}?v={{ filemtime(public_path('assets/css/sb-premium-fixes.css')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sb-phase3-quest-vault.css') }}?v={{ filemtime(public_path('assets/css/sb-phase3-quest-vault.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/sb-phase4-command-center.css') }}?v={{ filemtime(public_path('assets/css/sb-phase4-command-center.css')) }}">
</head>
<body id="top" class="studybuddy-site {{ $studyBuddyThemeClass }}" data-studybuddy-theme="{{ $studyBuddyTheme }}" data-sb-auth="{{ auth()->check() ? '1' : '0' }}" data-sb-theme="{{ auth()->check() ? (auth()->user()->avatar_style ?? 'cosmic-dolphin') : 'cosmic-dolphin' }}">
    @include('partials.navbar', ['settings' => $settings ?? [], 'navigationItems' => $navigationItems ?? collect()])
    <main>@yield('content')</main>
    @include('partials.footer', ['settings' => $settings ?? [], 'footerGroups' => $footerGroups ?? collect()])
    <script src="{{ asset('assets/js/site.js') }}" defer></script>
    <script src="{{ asset('assets/js/experience.js') }}?v={{ file_exists(public_path('assets/js/experience.js')) ? filemtime(public_path('assets/js/experience.js')) : time() }}" defer></script>
    @stack('scripts')
    <script defer src="{{ asset('assets/js/sb-premium-fixes.js') }}?v={{ filemtime(public_path('assets/js/sb-premium-fixes.js')) }}"></script>
    <script defer src="{{ asset('assets/js/sb-phase3-quest-vault.js') }}?v={{ filemtime(public_path('assets/js/sb-phase3-quest-vault.js')) }}"></script>
    <script defer src="{{ asset('assets/js/sb-phase4-command-center.js') }}?v={{ filemtime(public_path('assets/js/sb-phase4-command-center.js')) }}"></script>
</body>
</html>
