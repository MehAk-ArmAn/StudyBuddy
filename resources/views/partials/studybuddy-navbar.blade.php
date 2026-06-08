@php
    $logo = asset(\App\Support\Cms::setting('favicon_path', 'assets/studybuddy/logo-icon.png'));
    $navItems = \App\Support\Cms::navigation();
    $navLinks = $navItems->where('is_cta', false);
    $cta = $navItems->firstWhere('is_cta', true);
@endphp
<header class="studybuddy-navbar reveal-on-load" data-studybuddy-navbar>
    <a class="studybuddy-navbar__brand" href="{{ route('home') }}" aria-label="StudyBuddy home">
        <span class="studybuddy-navbar__mark"><img src="{{ $logo }}" alt="StudyBuddy logo"></span>
        <span class="studybuddy-navbar__word">Study<span>Buddy</span></span>
    </a>

    <button class="studybuddy-navbar__toggle" type="button" aria-expanded="false" aria-controls="studybuddy-main-nav" data-studybuddy-nav-toggle>
        <span></span><span></span><span></span>
        <em>Menu</em>
    </button>

    <nav class="studybuddy-navbar__links" id="studybuddy-main-nav" aria-label="Main navigation" data-studybuddy-nav-links>
        @foreach($navLinks as $link)
            <a href="{{ $link['url'] }}" @class(['is-active' => $link['route_name'] ? request()->routeIs($link['route_name']) : false])>{{ $link['label'] }}</a>
        @endforeach
    </nav>

    <a class="studybuddy-navbar__cta" href="{{ $cta['url'] ?? route('apps.math-quest.play') }}">{{ $cta['label'] ?? 'Start Learning' }}</a>
</header>
