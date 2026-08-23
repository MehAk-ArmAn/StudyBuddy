<!doctype html>
@php
    $safeAsset = function (string $path): ?string {
        return file_exists(public_path($path)) ? asset($path).'?v='.filemtime(public_path($path)) : null;
    };
    $studyBuddyTheme = auth()->check() ? (auth()->user()->avatar_style ?: 'cosmic-dolphin') : 'cosmic-dolphin';
    $studyBuddyThemeClass = 'theme-' . \Illuminate\Support\Str::slug($studyBuddyTheme ?: 'cosmic-dolphin');
    $publicPageTitle = trim($__env->yieldContent('title'));
    $currentRouteName = \Illuminate\Support\Facades\Route::currentRouteName() ?: 'page';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @include('partials.brand-head')
    @if (!empty($settings['seo_keywords']))<meta name="keywords" content="{{ $settings['seo_keywords'] }}">@endif
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    @if(file_exists(public_path('assets/css/sb-auth-role-ui.css')))<link rel="stylesheet" href="{{ asset('assets/css/sb-auth-role-ui.css') }}?v={{ filemtime(public_path('assets/css/sb-auth-role-ui.css')) }}">@endif
    @if(file_exists(public_path('assets/css/sb-structure-publish-polish.css')))<link rel="stylesheet" href="{{ asset('assets/css/sb-structure-publish-polish.css') }}?v={{ filemtime(public_path('assets/css/sb-structure-publish-polish.css')) }}">@endif
    @if(file_exists(public_path('assets/css/studybuddy-advanced-shell.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-advanced-shell.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-advanced-shell.css')) }}">
    @endif
    @if(file_exists(public_path('assets/css/studybuddy-shell-force-override.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-shell-force-override.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-shell-force-override.css')) }}">
    @endif
    @if(file_exists(public_path('assets/css/studybuddy-global-premium-polish.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-global-premium-polish.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-global-premium-polish.css')) }}">
    @endif
    @if(file_exists(public_path('assets/css/studybuddy-living-platform.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-living-platform.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-living-platform.css')) }}">
    @endif
    @if(file_exists(public_path('assets/css/studybuddy-final-no-error-polish.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-final-no-error-polish.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-final-no-error-polish.css')) }}">
    @endif
    @if(file_exists(public_path('assets/css/studybuddy-home-vibe-upgrade.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-home-vibe-upgrade.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-home-vibe-upgrade.css')) }}">
    @endif
    @stack('styles')
    @if(file_exists(public_path('assets/css/studybuddy-welcome-confetti.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-welcome-confetti.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-welcome-confetti.css')) }}">
    @endif
    @if(file_exists(public_path('assets/css/studybuddy-accessibility-hardening.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-accessibility-hardening.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-accessibility-hardening.css')) }}">
    @endif

    @if(file_exists(public_path('assets/css/studybuddy-independent-learner.css')))
        <link
            rel="stylesheet"
            href="{{ asset('assets/css/studybuddy-independent-learner.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-independent-learner.css')) }}"
        >
    @endif


    @if(file_exists(public_path('assets/css/studybuddy-mailing-list.css')))
        <link
            rel="stylesheet"
            href="{{ asset('assets/css/studybuddy-mailing-list.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-mailing-list.css')) }}"
        >
    @endif

</head>
<body id="top" class="studybuddy-site {{ $studyBuddyThemeClass }} route-{{ \Illuminate\Support\Str::slug($currentRouteName) }}" data-studybuddy-theme="{{ $studyBuddyTheme }}" data-sb-auth="{{ auth()->check() ? '1' : '0' }}" data-sb-theme="{{ auth()->check() ? (auth()->user()->avatar_style ?? 'cosmic-dolphin') : 'cosmic-dolphin' }}">
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
    @if(file_exists(public_path('assets/js/sb-auth-role-ui.js')))<script src="{{ asset('assets/js/sb-auth-role-ui.js') }}?v={{ filemtime(public_path('assets/js/sb-auth-role-ui.js')) }}" defer></script>@endif
    @if(file_exists(public_path('assets/js/studybuddy-advanced-shell.js')))
        <script src="{{ asset('assets/js/studybuddy-advanced-shell.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-advanced-shell.js')) }}" defer></script>
    @endif
    @if(file_exists(public_path('assets/js/studybuddy-living-platform.js')))
        <script src="{{ asset('assets/js/studybuddy-living-platform.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-living-platform.js')) }}" defer></script>
    @endif
    @if(file_exists(public_path('assets/js/studybuddy-home-vibe-upgrade.js')))
        <script src="{{ asset('assets/js/studybuddy-home-vibe-upgrade.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-home-vibe-upgrade.js')) }}" defer></script>
    @endif
    <script async src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.4/dist/confetti.browser.min.js"></script>
    @if(file_exists(public_path('assets/js/studybuddy-welcome-confetti.js')))
        <script src="{{ asset('assets/js/studybuddy-welcome-confetti.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-welcome-confetti.js')) }}" defer></script>
    @endif

    @if(file_exists(public_path('assets/js/studybuddy-independent-learner.js')))
        <script
            src="{{ asset('assets/js/studybuddy-independent-learner.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-independent-learner.js')) }}"
            defer
        ></script>
    @endif



    @if(file_exists(public_path('assets/js/studybuddy-remove-service-worker.js')))
        <script
            src="{{ asset('assets/js/studybuddy-remove-service-worker.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-remove-service-worker.js')) }}"
            defer
        ></script>
    @endif

</body>
</html>
