@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
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

    $safeJson = function ($value, $fallback) {
        if (!is_string($value) || trim($value) === '') return $fallback;
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : $fallback;
    };

    $fallbackNav = [
        ['label' => 'Home', 'url' => '/', 'roles' => ['all']],
        ['label' => 'Apps', 'url' => '/apps', 'roles' => ['all']],
        ['label' => 'Learning', 'url' => '/apps?section=learning', 'roles' => ['all']],
        ['label' => 'Parents', 'url' => '/apps?role=parent', 'roles' => ['all']],
        ['label' => 'Teachers', 'url' => '/apps?role=teacher', 'roles' => ['all']],
        ['label' => 'Safety', 'url' => '/apps?section=safety', 'roles' => ['all']],
        ['label' => 'Rewards', 'url' => '/apps?section=rewards', 'roles' => ['all']],
        ['label' => 'Roadmap', 'url' => '/apps?section=roadmap', 'roles' => ['all']],
    ];

    $navItems = $safeJson($shellSettings['shell_navigation_json'] ?? null, $fallbackNav);

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

    $currentUrl = request()->path();
    $isActive = function ($url) use ($currentUrl) {
        $path = trim(parse_url($url, PHP_URL_PATH) ?: '/', '/');
        if ($path === '') return request()->is('/');
        return request()->is($path) || request()->is($path . '/*');
    };
@endphp

<header class="sb-advanced-shell" data-shell>
    <a class="sb-skip-link" href="#main-content">Skip to content</a>

    <nav class="sb-advanced-nav" aria-label="StudyBuddy primary navigation">
        <div class="sb-nav-inner">
            <a class="sb-nav-brand" href="{{ url('/') }}" aria-label="StudyBuddy home">
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

            <div class="sb-nav-links" data-nav-links>
                @foreach($primaryNav as $item)
                    @php
                        $url = $item['url'] ?? '#';
                        $label = $item['label'] ?? 'Link';
                    @endphp
                    <a href="{{ url($url) }}" @class(['active' => $isActive($url)])>
                        {{ $label }}
                    </a>
                @endforeach

                @if($moreNav->count())
                    <div class="sb-nav-more" data-more>
                        <button type="button" aria-expanded="false" aria-haspopup="true" data-more-button>
                            More
                            <span>⌄</span>
                        </button>
                        <div class="sb-nav-more-menu" data-more-menu>
                            @foreach($moreNav as $item)
                                @php
                                    $url = $item['url'] ?? '#';
                                    $label = $item['label'] ?? 'Link';
                                @endphp
                                <a href="{{ url($url) }}" @class(['active' => $isActive($url)])>
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <form class="sb-nav-search" action="{{ url('/apps') }}" method="GET" role="search">
                <label class="sr-only" for="sb-shell-search">Search StudyBuddy apps</label>
                <input id="sb-shell-search" name="q" value="{{ request('q') }}" placeholder="Search apps..." autocomplete="off">
                <button type="submit" aria-label="Search">⌕</button>
            </form>

            <div class="sb-nav-actions">
                @guest
                    <a class="ghost" href="{{ url('/login') }}">Log in</a>
                    <a class="solid" href="{{ url('/register') }}">Start free</a>
                @else
                    @if($isAdmin)
                        <a class="ghost" href="{{ url('/admin/control-room') }}">Control Room</a>
                    @endif
                    <a class="solid" href="{{ url('/dashboard') }}">Dashboard</a>
                @endguest
            </div>

            <button class="sb-nav-toggle" type="button" aria-label="Open navigation" aria-expanded="false" data-nav-toggle>
                <span></span><span></span><span></span>
            </button>
        </div>

        <div class="sb-mobile-panel" data-mobile-panel>
            <form class="sb-mobile-search" action="{{ url('/apps') }}" method="GET" role="search">
                <input name="q" value="{{ request('q') }}" placeholder="Search apps, skills, quests...">
                <button type="submit">Search</button>
            </form>

            <div class="sb-mobile-links">
                @foreach($visibleNav as $item)
                    @php
                        $url = $item['url'] ?? '#';
                        $label = $item['label'] ?? 'Link';
                    @endphp
                    <a href="{{ url($url) }}" @class(['active' => $isActive($url)])>
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="sb-mobile-actions">
                @guest
                    <a href="{{ url('/login') }}">Log in</a>
                    <a class="solid" href="{{ url('/register') }}">Start free</a>
                @else
                    @if($isAdmin)
                        <a href="{{ url('/admin/control-room') }}">Control Room</a>
                    @endif
                    <a class="solid" href="{{ url('/dashboard') }}">Dashboard</a>
                @endguest
            </div>
        </div>
    </nav>
</header>