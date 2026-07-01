<!doctype html>
@php
    $safeAsset = function (string $path): ?string {
        return file_exists(public_path($path)) ? asset($path).'?v='.filemtime(public_path($path)) : null;
    };
    $studyBuddyTheme = auth()->check() ? (auth()->user()->avatar_style ?: 'cosmic-dolphin') : 'cosmic-dolphin';
    $studyBuddyThemeClass = 'theme-' . \Illuminate\Support\Str::slug($studyBuddyTheme ?: 'cosmic-dolphin');
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings['seo_title'] ?? $settings['site_name'] ?? config('app.name', 'StudyBuddy') }}</title>
    @if (!empty($settings['seo_description']))<meta name="description" content="{{ $settings['seo_description'] }}">@endif
    @if (!empty($settings['seo_keywords']))<meta name="keywords" content="{{ $settings['seo_keywords'] }}">@endif
    @if (!empty($settings['favicon_path']))<link rel="icon" href="{{ preg_match('/^https?:\/\//i', $settings['favicon_path']) ? $settings['favicon_path'] : asset($settings['favicon_path']) }}">@endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#7C3AED">
    @if(file_exists(public_path('manifest.webmanifest')))<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">@endif

    @foreach([
        'assets/css/site.css',
        'assets/css/experience.css',
        'assets/css/sb-premium-fixes.css',
        'assets/css/sb-phase3-quest-vault.css',
        'assets/css/sb-phase4-command-center.css',
        'assets/css/sb-phase5-admin-experience.css',
        'assets/css/sb-phase6-final-platform.css',
        'assets/css/sb-final-ui-safety-polish.css',
    ] as $cssPath)
        @if($url = $safeAsset($cssPath))<link rel="stylesheet" href="{{ $url }}">@endif
    @endforeach
    @stack('styles')
</head>
<body id="top" class="studybuddy-site {{ $studyBuddyThemeClass }}" data-studybuddy-theme="{{ $studyBuddyTheme }}" data-sb-auth="{{ auth()->check() ? '1' : '0' }}" data-sb-theme="{{ auth()->check() ? (auth()->user()->avatar_style ?? 'cosmic-dolphin') : 'cosmic-dolphin' }}">
    <a class="sb-skip-link" href="#main-content">Skip to content</a>
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
        'assets/js/sb-final-ui-safety-polish.js',
    ] as $jsPath)
        @if($url = $safeAsset($jsPath))<script defer src="{{ $url }}"></script>@endif
    @endforeach
    @stack('scripts')
</body>
</html>
