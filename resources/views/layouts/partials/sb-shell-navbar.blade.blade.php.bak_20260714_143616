@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Schema;

    $shellSettings = [];
    if (Schema::hasTable('site_settings')) {
        $shellSettings = DB::table('site_settings')->pluck('value', 'key')->all();
    }

    $brandName = $shellSettings['site_name'] ?? 'StudyBuddy';
    $tagline = $shellSettings['site_tagline'] ?? 'Learn. Play. Grow. Your Way.';

    $logoCandidates = [
        $shellSettings['logo_image'] ?? null,
        'assets/studybuddy-imgs/brand/logo-icon.png',
        'assets/studybuddy-brand/logo-icon.png',
        'assets/studybuddy-control/logo.svg',
    ];

    $logoPath = null;
    foreach ($logoCandidates as $candidate) {
        if (!$candidate) continue;
        $clean = ltrim($candidate, '/');
        if (str_starts_with($clean, 'http') || file_exists(public_path($clean))) {
            $logoPath = $candidate;
            break;
        }
    }

    $fallbackNav = [
        ['label' => 'Home', 'url' => '/', 'roles' => ['all']],
        ['label' => 'Apps', 'url' => '/apps', 'roles' => ['all']],
        ['label' => 'Community', 'url' => '/community', 'roles' => ['all']],
        ['label' => 'Profile', 'url' => '/profile', 'roles' => ['auth']],
        ['label' => 'Dashboard', 'url' => '/dashboard', 'roles' => ['auth']],
        ['label' => 'Parents', 'url' => '/apps?role=parent', 'roles' => ['all']],
        ['label' => 'Teachers', 'url' => '/apps?role=teacher', 'roles' => ['all']],
        ['label' => 'Safety', 'url' => '/apps?section=safety', 'roles' => ['all']],
    ];

    $navItems = $fallbackNav;
    if (!empty($shellSettings['shell_navigation_json'])) {
        $decoded = json_decode($shellSettings['shell_navigation_json'], true);
        if (is_array($decoded)) $navItems = $decoded;
    }

    $user = Auth::user();
    $role = $user->role ?? null;
    $isAdmin = $user && (($user->is_admin ?? false) || $role === 'admin' || ($user->email ?? null) === 'admin@studybuddy.fun');

    $visibleNav = collect($navItems)->filter(function ($item) use ($role, $user) {
        $roles = $item['roles'] ?? ['all'];
        if (in_array('all', $roles, true)) return true;
        if (!$user && in_array('guest', $roles, true)) return true;
        if ($user && in_array('auth', $roles, true)) return true;
        return $role && in_array($role, $roles, true);
    })->values();

    $primaryNav = $visibleNav->take(5);
    $moreNav = $visibleNav->slice(5);

    $isActive = function ($url) {
        $path = trim(parse_url($url, PHP_URL_PATH) ?: '/', '/');
        if ($path === '') return request()->is('/');
        return request()->is($path) || request()->is($path . '/*');
    };
@endphp

<header class="sb-advanced-shell" data-shell>
    <nav class="sb-advanced-nav" aria-label="StudyBuddy navigation">
        <div class="sb-nav-inner">
            <a class="sb-nav-brand" href="{{ url('/') }}">
                @if($logoPath)
                    <img src="{{ str_starts_with($logoPath, 'http') ? $logoPath : asset(ltrim($logoPath, '/')) }}" alt="StudyBuddy logo">
                @else
                    <span class="sb-nav-brand-fallback">SB</span>
                @endif
                <span>
                    <strong>{{ $brandName }}</strong>
                    <em>{{ $tagline }}</em>
                </span>
            </a>

            <div class="sb-nav-links">
                @foreach($primaryNav as $item)
                    @php($url = $item['url'] ?? '#')
                    @php($label = $item['label'] ?? 'Link')
                    <a href="{{ url($url) }}" @class(['active' => $isActive($url)])>{{ $label }}</a>
                @endforeach

                @if($moreNav->count())
                    <div class="sb-nav-more" data-more>
                        <button type="button" aria-expanded="false" data-more-button>More <span>⌄</span></button>
                        <div class="sb-nav-more-menu">
                            @foreach($moreNav as $item)
                                @php($url = $item['url'] ?? '#')
                                @php($label = $item['label'] ?? 'Link')
                                <a href="{{ url($url) }}" @class(['active' => $isActive($url)])>{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <form class="sb-nav-search sb-live-search" action="{{ route('studybuddy.search') }}" method="GET" role="search" data-search-root data-search-endpoint="{{ route('studybuddy.search.suggest') }}">
                <input name="q" value="{{ request('q') }}" placeholder="Search apps, profiles, pages..." autocomplete="off" aria-label="Search StudyBuddy" data-search-input>
                <button type="submit" aria-label="Search">⌕</button>
                <div class="sb-search-popover" data-search-results hidden></div>
            </form>

            <div class="sb-nav-actions">
                @guest
                    <a class="ghost" href="{{ url('/login') }}">Log in</a>
                    <a class="solid" href="{{ url('/register') }}">Start free</a>
                @else
                    <div class="sb-account-menu" data-account-menu>
                        <button type="button" class="sb-account-trigger" data-account-button aria-expanded="false">
                            <span class="sb-account-avatar">{{ strtoupper(substr($user->name ?? $user->email ?? 'U', 0, 1)) }}</span>
                            <span class="sb-account-label">
                                <strong>{{ $user->name ?? 'My Account' }}</strong>
                                <em>{{ $role ? str_replace('_', ' ', $role) : 'Learner' }}</em>
                            </span>
                            <i>⌄</i>
                        </button>
                        <div class="sb-account-dropdown">
                            <a href="{{ url('/dashboard') }}">Dashboard</a>
                            <a href="{{ url('/profile') }}">Profile Studio</a>
                            <a href="{{ url('/community') }}">Community</a>
                            @if($isAdmin)<a href="{{ url('/admin/control-room') }}">Control Room</a>@endif
                            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Logout</button></form>
                        </div>
                    </div>
                @endguest
            </div>

            <button class="sb-nav-toggle" type="button" aria-label="Open navigation" aria-expanded="false" data-nav-toggle>
                <span></span><span></span><span></span>
            </button>
        </div>

        <div class="sb-mobile-panel" data-mobile-panel>
            <form class="sb-mobile-search sb-live-search" action="{{ route('studybuddy.search') }}" method="GET" role="search" data-search-root data-search-endpoint="{{ route('studybuddy.search.suggest') }}">
                <input name="q" value="{{ request('q') }}" placeholder="Search StudyBuddy..." autocomplete="off" data-search-input>
                <button type="submit">Search</button>
                <div class="sb-search-popover mobile" data-search-results hidden></div>
            </form>

            <div class="sb-mobile-links">
                @foreach($visibleNav as $item)
                    @php($url = $item['url'] ?? '#')
                    @php($label = $item['label'] ?? 'Link')
                    <a href="{{ url($url) }}" @class(['active' => $isActive($url)])>{{ $label }}</a>
                @endforeach
            </div>

            <div class="sb-mobile-actions">
                @guest
                    <a href="{{ url('/login') }}">Log in</a>
                    <a class="solid" href="{{ url('/register') }}">Start free</a>
                @else
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    <a href="{{ url('/profile') }}">Profile Studio</a>
                    <a href="{{ url('/community') }}">Community</a>
                    @if($isAdmin)<a href="{{ url('/admin/control-room') }}">Control Room</a>@endif
                    <form class="sb-mobile-logout-form" method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Logout</button></form>
                @endguest
            </div>
        </div>
    </nav>
</header>
