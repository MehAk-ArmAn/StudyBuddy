@php
    $navDefaults = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Apps', 'url' => '/apps'],
        ['label' => 'Learning', 'url' => '/learning-hub'],
        ['label' => 'Paths', 'url' => '/learning-paths'],
        ['label' => 'Parents', 'url' => '/parents-center'],
        ['label' => 'Teachers', 'url' => '/teacher-studio'],
        ['label' => 'Safety', 'url' => '/safety-support'],
    ];
    $items = ($navigationItems ?? collect())->isNotEmpty()
        ? ($navigationItems ?? collect())->map(fn($item) => ['label' => $item->label, 'url' => $item->url, 'opens_new_tab' => $item->opens_new_tab ?? false])->toArray()
        : $navDefaults;
    $isAdminUser = auth()->check() && ((auth()->user()->role ?? null) === 'admin' || (bool) (auth()->user()->is_admin ?? false));
@endphp

<nav class="nav sb-pro-nav" aria-label="Main navigation" data-sb-pro-nav>
    <a class="brand" href="{{ route('home') }}" aria-label="StudyBuddy home">
        @if (!empty($settings['logo_path']))
            <img src="{{ asset($settings['logo_path']) }}" alt="{{ $settings['brand_name'] ?? 'StudyBuddy' }}">
        @endif
        <span>{{ $settings['brand_name'] ?? 'StudyBuddy' }}</span>
    </a>

    <button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="studybuddy-nav-links">Menu</button>

    <div class="nav-links" id="studybuddy-nav-links" data-nav-links>
        @foreach ($items as $item)
            @php($url = $item['url'] ?? '#')
            <a href="{{ $url }}" @class(['is-active' => request()->is(trim($url, '/')) || ($url === '/' && request()->is('/'))]) @if(!empty($item['opens_new_tab'])) target="_blank" rel="noopener" @endif>{{ $item['label'] }}</a>
        @endforeach

        @auth
            <span class="sb-nav-divider" aria-hidden="true"></span>
            <a href="{{ route('dashboard') }}" @class(['is-active' => request()->is('dashboard')])>Dashboard</a>
            @if(Route::has('studybuddy.dashboard.command-center'))<a href="{{ route('studybuddy.dashboard.command-center') }}" @class(['is-active' => request()->is('command-center') || request()->is('dashboard/command-center')])>Command</a>@endif
            @if(Route::has('studybuddy.quests.index'))<a href="{{ route('studybuddy.quests.index') }}" @class(['is-active' => request()->is('my-quest*')])>My Quest</a>@endif
            @if(Route::has('studybuddy.final.points-wallet'))<a href="{{ route('studybuddy.final.points-wallet') }}" @class(['is-active' => request()->is('points-wallet')])>Wallet</a>@endif
            @if(Route::has('studybuddy.verification.center'))<a href="{{ route('studybuddy.verification.center') }}" @class(['is-active' => request()->is('verification-center')])>Verify</a>@endif
            @if($isAdminUser)
                <span class="sb-nav-divider" aria-hidden="true"></span>
                @if(Route::has('studybuddy.admin.content.index'))<a href="{{ route('studybuddy.admin.content.index') }}" @class(['is-active' => request()->is('admin/studybuddy/content-studio*')])>Content Studio</a>@endif
                @if(Route::has('studybuddy.admin.final.index'))<a href="{{ route('studybuddy.admin.final.index') }}" @class(['is-active' => request()->is('admin/studybuddy/final-platform*')])>Platform</a>@endif
            @endif
            <form class="logout-inline" method="POST" action="{{ route('logout') }}" data-sb-logout-form>
                @csrf
                <button class="nav-logout" type="submit">{{ $settings['logout_label'] ?? 'Logout' }}</button>
            </form>
        @else
            <span class="sb-nav-divider" aria-hidden="true"></span>
            <a href="{{ route('login') }}">{{ $settings['login_label'] ?? 'Login' }}</a>
            <a class="nav-cta" href="{{ route('register') }}">{{ $settings['register_label'] ?? 'Create Account' }}</a>
        @endauth
    </div>
</nav>
