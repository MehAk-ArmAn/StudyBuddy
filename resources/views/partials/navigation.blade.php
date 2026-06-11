@php($logoExists = file_exists(public_path('assets/studybuddy/logo-icon.png')))
<header class="nav-shell reveal-on-load">
    <a class="brand" href="{{ route('home') }}" aria-label="StudyBuddy home">
        <span class="brand-mark">
            @if($logoExists)
                <img src="{{ asset('assets/studybuddy/logo-icon.png') }}" alt="StudyBuddy logo">
            @else
                🐬
            @endif
        </span>
        <span class="brand-copy"><strong>StudyBuddy</strong></span>
    </a>
    <nav class="nav-links" aria-label="Main navigation">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('apps.index') }}">Apps</a>
        <a href="{{ route('demo.parent') }}">For Parents</a>
        <a href="{{ route('demo.teacher') }}">For Teachers</a>
        <a href="{{ route('rewards') }}">Pricing</a>
        <a href="{{ route('showcase') }}">Support</a>
    </nav>
    <div class="nav-actions">
        <a class="button button-compact" href="{{ route('register') }}">Sign Up</a>
    </div>
</header>
