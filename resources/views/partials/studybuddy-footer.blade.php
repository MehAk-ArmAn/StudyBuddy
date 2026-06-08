@php
    $logo = asset(\App\Support\Cms::setting('favicon_path', 'assets/studybuddy/logo-icon.png'));
    $sections = \App\Support\Cms::footerSections();
@endphp
<footer class="studybuddy-footer reveal-on-load">
    <span class="studybuddy-footer__orb studybuddy-footer__orb-a" aria-hidden="true"></span>
    <span class="studybuddy-footer__orb studybuddy-footer__orb-b" aria-hidden="true"></span>
    <div class="studybuddy-footer__brand">
        <img src="{{ $logo }}" alt="{{ \App\Support\Cms::setting('site_name', 'StudyBuddy') }} logo">
        <div><strong>{{ \App\Support\Cms::setting('site_name', 'StudyBuddy') }}</strong><p>{{ \App\Support\Cms::setting('footer_tagline', 'Learn. Play. Grow. Your Way.') }}</p></div>
    </div>
    @forelse($sections as $section)
        <nav aria-label="{{ $section->title }}"><strong>{{ $section->title }}</strong>@foreach($section->links as $link)<a href="{{ $link->route_name && \Illuminate\Support\Facades\Route::has($link->route_name) ? route($link->route_name) : ($link->url ?: '#') }}">{{ $link->label }}</a>@endforeach</nav>
    @empty
        <nav aria-label="Explore"><strong>Explore</strong><a href="{{ route('home') }}">Home</a><a href="{{ route('apps.index') }}">Apps</a><a href="{{ route('apps.math-quest') }}">Math Quest</a></nav>
    @endforelse
    <section class="studybuddy-footer__apps"><strong>Get StudyBuddy</strong><span>Google Play</span><span>App Store</span></section>
    <p class="studybuddy-footer__copyright">© {{ date('Y') }} {{ \App\Support\Cms::setting('footer_copyright', 'StudyBuddy. A safe cosmic learning universe for every learner.') }}</p>
</footer>
