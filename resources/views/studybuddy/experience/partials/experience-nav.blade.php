@php
    $experienceRoutes = [
        'learning-hub' => '/learning-hub',
        'learning-paths' => '/learning-paths',
        'rewards' => '/rewards',
        'parents-center' => '/parents-center',
        'teacher-studio' => '/teacher-studio',
        'safety-support' => '/safety-support',
        'app-ecosystem' => '/app-ecosystem',
    ];
@endphp

<nav class="sbx-nav" aria-label="StudyBuddy experience navigation">
    @foreach($navPages as $navPage)
        @php $navSlug = $navPage->slug ?? ''; @endphp
        <a class="sbx-nav__link {{ ($slug ?? '') === $navSlug ? 'is-active' : '' }}" href="{{ $experienceRoutes[$navSlug] ?? '/' }}">
            {{ $navPage->title ?? str($navSlug)->replace('-', ' ')->title() }}
        </a>
    @endforeach
</nav>
