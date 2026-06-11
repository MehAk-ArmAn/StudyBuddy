@php
    $pageTitle = $title ?? trim($__env->yieldContent('title', config('app.name', 'StudyBuddy')));
    $pageDescription = $metaDescription ?? trim($__env->yieldContent('meta_description', ''));
    $bodyClass = trim('sb-page ' . ($bodyClass ?? $__env->yieldContent('body_class', '')));
    $logoPath = 'assets/StudyBuddy-Imgs/01_brand/logo/studybuddy-logo-icon.png';
    $logoExists = file_exists(public_path($logoPath));
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if($pageDescription !== '')
        <meta name="description" content="{{ $pageDescription }}">
    @endif
    <title>{{ $pageTitle }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-frontend.css') }}">
    @stack('styles')
</head>
<body class="{{ $bodyClass }}" data-sb-page>
    @include('partials.frontend-page-loader')
    @include('partials.frontend-cosmic-background')
    @include('partials.frontend-floating-assets')

    <header class="sb-navbar sb-glass" data-sb-nav>
        <a class="sb-brand" href="{{ route('home') }}" aria-label="{{ config('app.name', 'StudyBuddy') }} home">
            <span class="sb-brand-mark">
                @if($logoExists)
                    <img src="{{ asset($logoPath) }}" alt="{{ config('app.name', 'StudyBuddy') }} logo">
                @else
                    <span aria-hidden="true">✦</span>
                @endif
            </span>
            <span class="sb-brand-text">{{ config('app.name', 'StudyBuddy') }}</span>
        </a>

        <button class="sb-menu-toggle" type="button" data-sb-menu-toggle aria-controls="sb-primary-nav" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav id="sb-primary-nav" class="sb-nav-links" data-sb-menu aria-label="Primary navigation">
            @foreach(($navItems ?? []) as $navItem)
                <a href="{{ $navItem['url'] ?? '#' }}" @class(['is-active' => $navItem['active'] ?? false])>{{ $navItem['label'] ?? '' }}</a>
            @endforeach
            @if(empty($navItems ?? []))
                @yield('navigation')
            @endif
        </nav>

        @hasSection('nav_actions')
            <div class="sb-nav-actions">
                @yield('nav_actions')
            </div>
        @endif
    </header>

    <main class="sb-shell" id="main-content">
        @yield('content')
    </main>

    <footer class="sb-footer sb-glass">
        @hasSection('footer')
            @yield('footer')
        @else
            <div class="sb-footer-brand">
                <strong>{{ config('app.name', 'StudyBuddy') }}</strong>
                @hasSection('footer_tagline')
                    <span>@yield('footer_tagline')</span>
                @endif
            </div>
            @if(!empty($footerLinks ?? []))
                <nav class="sb-footer-links" aria-label="Footer navigation">
                    @foreach($footerLinks as $footerLink)
                        <a href="{{ $footerLink['url'] ?? '#' }}">{{ $footerLink['label'] ?? '' }}</a>
                    @endforeach
                </nav>
            @endif
        @endif
    </footer>

    <script src="{{ asset('assets/js/studybuddy-frontend.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
