@php
    $logo = asset('assets/studybuddy/logo-icon.png');
    $navLinks = [
        ['label' => 'Home', 'url' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Apps', 'url' => route('apps.index'), 'active' => request()->routeIs('apps.index')],
        ['label' => 'Math Quest', 'url' => route('apps.math-quest'), 'active' => request()->routeIs('apps.math-quest') || request()->routeIs('apps.math-quest.play')],
        ['label' => 'Rewards', 'url' => route('rewards'), 'active' => request()->routeIs('rewards')],
        ['label' => 'Dashboards', 'url' => route('demo.primary'), 'active' => request()->routeIs('demo.*')],
        ['label' => 'Showcase', 'url' => route('showcase'), 'active' => request()->routeIs('showcase')],
    ];
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
            <a href="{{ $link['url'] }}" @class(['is-active' => $link['active']])>{{ $link['label'] }}</a>
        @endforeach
    </nav>

    <a class="studybuddy-navbar__cta" href="{{ route('apps.math-quest.play') }}">Start Learning</a>
</header>
