<?php

$root = dirname(__DIR__);

function put_file(string $path, string $content): void {
    global $root;
    $full = $root . '/' . $path;

    if (!is_dir(dirname($full))) {
        mkdir(dirname($full), 0777, true);
    }

    if (file_exists($full)) {
        copy($full, $full . '.bak_' . date('Ymd_His'));
    }

    file_put_contents($full, $content);
    echo "✓ wrote {$path}\n";
}

$navbar = <<'BLADE'
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
    $logoutUrl = Route::has('logout') ? route('logout') : url('/logout');

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
                    <a href="{{ url($url) }}" @class(['active' => $isActive($url)])>{{ $label }}</a>
                @endforeach

                @if($moreNav->count())
                    <div class="sb-nav-more" data-more>
                        <button type="button" aria-expanded="false" aria-haspopup="true" data-more-button>
                            More <span>⌄</span>
                        </button>
                        <div class="sb-nav-more-menu" data-more-menu>
                            @foreach($moreNav as $item)
                                @php
                                    $url = $item['url'] ?? '#';
                                    $label = $item['label'] ?? 'Link';
                                @endphp
                                <a href="{{ url($url) }}" @class(['active' => $isActive($url)])>{{ $label }}</a>
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
                    <div class="sb-account-menu" data-account-menu>
                        <button type="button" class="sb-account-trigger" aria-haspopup="true" aria-expanded="false" data-account-button>
                            <span class="sb-account-avatar">{{ strtoupper(substr($user->name ?? $user->email ?? 'U', 0, 1)) }}</span>
                            <span class="sb-account-label">
                                <strong>{{ $user->name ?? 'My Account' }}</strong>
                                <em>{{ $role ? str_replace('_', ' ', $role) : 'Learner' }}</em>
                            </span>
                            <i>⌄</i>
                        </button>

                        <div class="sb-account-dropdown" data-account-dropdown>
                            <a href="{{ url('/dashboard') }}">Dashboard</a>
                            @if($isAdmin)
                                <a href="{{ url('/admin/control-room') }}">Control Room</a>
                            @endif
                            <a href="{{ url('/profile') }}">Profile</a>

                            <form method="POST" action="{{ $logoutUrl }}">
                                @csrf
                                <button type="submit">Logout</button>
                            </form>
                        </div>
                    </div>
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
                    <a href="{{ url($url) }}" @class(['active' => $isActive($url)])>{{ $label }}</a>
                @endforeach
            </div>

            <div class="sb-mobile-actions">
                @guest
                    <a href="{{ url('/login') }}">Log in</a>
                    <a class="solid" href="{{ url('/register') }}">Start free</a>
                @else
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                    @if($isAdmin)
                        <a href="{{ url('/admin/control-room') }}">Control Room</a>
                    @endif
                    <form class="sb-mobile-logout-form" method="POST" action="{{ $logoutUrl }}">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>
</header>
BLADE;

put_file('resources/views/layouts/partials/sb-shell-navbar.blade.php', $navbar);

$cssPath = $root . '/public/assets/css/studybuddy-advanced-shell.css';
if (file_exists($cssPath)) {
    copy($cssPath, $cssPath . '.bak_' . date('Ymd_His'));

    $css = file_get_contents($cssPath);

    $block = <<'CSS'

/* === StudyBuddy account/logout dropdown === */
.sb-account-menu {
    position: relative;
    flex: 0 0 auto;
}

.sb-account-trigger {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    max-width: 230px;
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 999px;
    padding: 6px 10px 6px 6px;
    color: white;
    background: rgba(255,255,255,.10);
    font: inherit;
    cursor: pointer;
    transition: transform .2s ease, background .2s ease, border-color .2s ease;
}

.sb-account-trigger:hover {
    transform: translateY(-2px);
    border-color: rgba(34, 211, 238, .28);
    background: rgba(255,255,255,.14);
}

.sb-account-avatar {
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    color: #061022;
    background: var(--sb-shell-cyan);
    font-weight: 950;
}

.sb-account-label {
    display: grid;
    min-width: 0;
    text-align: left;
}

.sb-account-label strong,
.sb-account-label em {
    min-width: 0;
    max-width: 135px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.sb-account-label strong {
    font-size: .86rem;
    line-height: 1.05;
}

.sb-account-label em {
    color: rgba(226,232,240,.70);
    font-size: .72rem;
    font-style: normal;
    text-transform: capitalize;
}

.sb-account-trigger i {
    font-style: normal;
    opacity: .7;
}

.sb-account-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: 230px;
    display: grid;
    gap: 6px;
    padding: 10px;
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 22px;
    background: #091126;
    box-shadow: 0 24px 70px rgba(2,6,23,.36);
    opacity: 0;
    visibility: hidden;
    transform: translateY(8px) scale(.96);
    transform-origin: top right;
    transition: .18s ease;
}

.sb-account-menu.open .sb-account-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.sb-account-dropdown a,
.sb-account-dropdown button {
    width: 100%;
    border: 0;
    border-radius: 14px;
    padding: 11px 12px;
    color: rgba(226,232,240,.84);
    background: transparent;
    text-align: left;
    text-decoration: none;
    font: inherit;
    font-weight: 850;
    cursor: pointer;
}

.sb-account-dropdown a:hover,
.sb-account-dropdown button:hover {
    color: white;
    background: rgba(34, 211, 238, .12);
}

.sb-account-dropdown form {
    margin: 0;
}

.sb-account-dropdown form button {
    color: #fecaca;
}

.sb-mobile-logout-form {
    margin: 0;
}

.sb-mobile-logout-form button {
    min-height: 40px;
    border: 0;
    border-radius: 999px;
    padding: 9px 13px;
    color: #fecaca;
    background: rgba(255,255,255,.10);
    font: inherit;
    font-weight: 900;
    cursor: pointer;
}

@media (max-width: 1180px) {
    .sb-account-label {
        display: none;
    }

    .sb-account-trigger {
        max-width: none;
        padding-right: 9px;
    }
}
/* === End StudyBuddy account/logout dropdown === */
CSS;

    $css = preg_replace('/\/\* === StudyBuddy account\/logout dropdown === \*\/.*?\/\* === End StudyBuddy account\/logout dropdown === \*\//s', '', $css);
    file_put_contents($cssPath, trim($css) . "\n\n" . $block . "\n");
    echo "✓ patched CSS logout dropdown\n";
}

$jsPath = $root . '/public/assets/js/studybuddy-advanced-shell.js';
if (file_exists($jsPath)) {
    copy($jsPath, $jsPath . '.bak_' . date('Ymd_His'));

    $js = file_get_contents($jsPath);

    $block = <<'JS'

// === StudyBuddy account/logout dropdown ===
(() => {
    const menu = document.querySelector('[data-account-menu]');
    const button = document.querySelector('[data-account-button]');

    if (!menu || !button) return;

    const close = () => {
        menu.classList.remove('open');
        button.setAttribute('aria-expanded', 'false');
    };

    button.addEventListener('click', (event) => {
        event.stopPropagation();
        const open = menu.classList.toggle('open');
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    document.addEventListener('click', close);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') close();
    });
})();
// === End StudyBuddy account/logout dropdown ===
JS;

    $js = preg_replace('/\/\/ === StudyBuddy account\/logout dropdown ===.*?\/\/ === End StudyBuddy account\/logout dropdown ===/s', '', $js);
    file_put_contents($jsPath, trim($js) . "\n\n" . $block . "\n");
    echo "✓ patched JS logout dropdown\n";
}

echo "\nDONE ✅ Logout added to desktop account dropdown and mobile menu.\n";
