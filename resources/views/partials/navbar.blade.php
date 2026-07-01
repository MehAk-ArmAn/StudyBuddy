@php
    $user = auth()->user();
    $role = $user?->normalizedRole();
    $publicLinks = [
        ['label' => 'Home', 'url' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Apps', 'url' => route('studybuddy.apps'), 'active' => request()->is('apps*')],
        ['label' => 'Learning', 'url' => route('studybuddy.experience.learning-hub'), 'active' => request()->routeIs('studybuddy.experience.learning-hub')],
        ['label' => 'Paths', 'url' => route('studybuddy.experience.learning-paths'), 'active' => request()->routeIs('studybuddy.experience.learning-paths')],
        ['label' => 'Parents', 'url' => route('studybuddy.experience.parents-center'), 'active' => request()->routeIs('studybuddy.experience.parents-center')],
        ['label' => 'Teachers', 'url' => route('studybuddy.experience.teacher-studio'), 'active' => request()->routeIs('studybuddy.experience.teacher-studio')],
        ['label' => 'Safety', 'url' => route('studybuddy.experience.safety-support'), 'active' => request()->routeIs('studybuddy.experience.safety-support')],
    ];
    $authLinks = [
        ['label' => 'Dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
        ['label' => 'Command', 'url' => route('studybuddy.command-center'), 'active' => request()->routeIs('studybuddy.command-center') || request()->routeIs('studybuddy.dashboard.command-center')],
        ['label' => 'My Quest', 'url' => route('studybuddy.quests.index'), 'active' => request()->routeIs('studybuddy.quests.*')],
        ['label' => 'Wallet', 'url' => route('studybuddy.final.points-wallet'), 'active' => request()->routeIs('studybuddy.final.points-wallet')],
    ];
    if (Route::has('studybuddy.verification.center')) {
        $authLinks[] = ['label' => 'Verify', 'url' => route('studybuddy.verification.center'), 'active' => request()->routeIs('studybuddy.verification.*')];
    }
@endphp
<nav class="sb-main-nav" aria-label="Main navigation" data-sb-polished-nav>
    <a class="sb-brand" href="{{ route('home') }}" aria-label="StudyBuddy home">
        @if (!empty($settings['logo_path']))
            <img src="{{ asset($settings['logo_path']) }}" alt="" loading="lazy">
        @else
            <span class="sb-brand-mark">✦</span>
        @endif
        <span>{{ $settings['brand_name'] ?? 'StudyBuddy' }}</span>
    </a>

    <button class="sb-nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="sb-primary-nav">
        <span>Menu</span><i aria-hidden="true">☰</i>
    </button>

    <div class="sb-nav-panel" id="sb-primary-nav" data-nav-links>
        <div class="sb-nav-scroll">
            @foreach($publicLinks as $link)
                <a class="sb-nav-link {{ $link['active'] ? 'is-active' : '' }}" href="{{ $link['url'] }}">{{ $link['label'] }}</a>
            @endforeach

            @auth
                <span class="sb-nav-divider" aria-hidden="true"></span>
                @foreach($authLinks as $link)
                    <a class="sb-nav-link {{ $link['active'] ? 'is-active' : '' }}" href="{{ $link['url'] }}">{{ $link['label'] }}</a>
                @endforeach
                @if($user?->is_admin)
                    <span class="sb-nav-divider" aria-hidden="true"></span>
                    <a class="sb-nav-link sb-nav-admin" href="{{ route('admin.dashboard') }}">Admin</a>
                    @if(Route::has('studybuddy.admin.content.index'))<a class="sb-nav-link sb-nav-admin" href="{{ route('studybuddy.admin.content.index') }}">Content</a>@endif
                    @if(Route::has('studybuddy.admin.final.index'))<a class="sb-nav-link sb-nav-admin" href="{{ route('studybuddy.admin.final.index') }}">Platform</a>@endif
                @endif
                <form class="sb-nav-logout" method="POST" action="{{ route('logout') }}" data-sb-logout-form>
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            @else
                <span class="sb-nav-divider" aria-hidden="true"></span>
                <a class="sb-nav-link" href="{{ route('login') }}">{{ $settings['login_label'] ?? 'Login' }}</a>
                <a class="sb-nav-cta" href="{{ route('register') }}">{{ $settings['register_label'] ?? 'Get Started' }}</a>
            @endauth
        </div>
    </div>
</nav>
