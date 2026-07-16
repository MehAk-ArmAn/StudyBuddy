<?php

$root = dirname(__DIR__);

function write_file(string $path, string $content): void {
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

function patch_layout_asset(string $path): void {
    global $root;
    $full = $root . '/' . $path;

    if (!file_exists($full)) {
        echo "skip {$path}\n";
        return;
    }

    $text = file_get_contents($full);

    $css = <<<'BLADE'
    @if(file_exists(public_path('assets/css/studybuddy-advanced-shell.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-advanced-shell.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-advanced-shell.css')) }}">
    @endif
BLADE;

    $js = <<<'BLADE'
    @if(file_exists(public_path('assets/js/studybuddy-advanced-shell.js')))
        <script src="{{ asset('assets/js/studybuddy-advanced-shell.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-advanced-shell.js')) }}" defer></script>
    @endif
BLADE;

    if (!str_contains($text, 'studybuddy-advanced-shell.css')) {
        $text = str_replace('</head>', $css . "\n</head>", $text);
    }

    if (!str_contains($text, 'studybuddy-advanced-shell.js')) {
        $text = str_replace('</body>', $js . "\n</body>", $text);
    }

    file_put_contents($full, $text);
    echo "✓ patched assets in {$path}\n";
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

    $fallbackNav = [
        ['label' => 'Home', 'url' => '/', 'roles' => ['all']],
        ['label' => 'Apps', 'url' => '/apps', 'roles' => ['all']],
        ['label' => 'Learning', 'url' => '/apps?section=learning', 'roles' => ['all']],
        ['label' => 'Parents', 'url' => '/apps?role=parent', 'roles' => ['all']],
        ['label' => 'Teachers', 'url' => '/apps?role=teacher', 'roles' => ['all']],
        ['label' => 'Safety', 'url' => '/apps?section=safety', 'roles' => ['all']],
        ['label' => 'Rewards', 'url' => '/apps?section=rewards', 'roles' => ['all']],
    ];

    $navItems = $fallbackNav;

    if (!empty($shellSettings['shell_navigation_json'])) {
        $decoded = json_decode($shellSettings['shell_navigation_json'], true);
        if (is_array($decoded)) {
            $navItems = $decoded;
        }
    }

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
                    @php
                        $url = $item['url'] ?? '#';
                        $label = $item['label'] ?? 'Link';
                    @endphp
                    <a href="{{ url($url) }}" @class(['active' => $isActive($url)])>{{ $label }}</a>
                @endforeach

                @if($moreNav->count())
                    <div class="sb-nav-more" data-more>
                        <button type="button" aria-expanded="false" data-more-button>More <span>⌄</span></button>
                        <div class="sb-nav-more-menu">
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
                <input name="q" value="{{ request('q') }}" placeholder="Search apps..." autocomplete="off" aria-label="Search StudyBuddy apps">
                <button type="submit" aria-label="Search">⌕</button>
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

$css = <<'CSS'
:root {
    --sb-shell-purple: #7c3cff;
    --sb-shell-blue: #246bff;
    --sb-shell-cyan: #22d3ee;
    --sb-shell-ink: #050816;
    --sb-shell-white: #f8fbff;
}

*,
*::before,
*::after {
    box-sizing: border-box;
}

.sr-only {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    overflow: hidden !important;
    clip: rect(1px,1px,1px,1px) !important;
    white-space: nowrap !important;
}

.sb-skip-link {
    position: absolute;
    left: 10px;
    top: 10px;
    z-index: 99999;
    transform: translateY(-150%);
    border-radius: 999px;
    padding: 10px 14px;
    color: white;
    background: var(--sb-shell-purple);
    text-decoration: none;
    font-weight: 900;
}

.sb-skip-link:focus {
    transform: translateY(0);
}

html body .sb-advanced-shell {
    position: sticky;
    top: 0;
    z-index: 9999;
    padding: 10px clamp(10px, 2vw, 26px);
    pointer-events: none;
    transition: padding .22s ease;
}

html body .sb-advanced-shell.is-scrolled {
    padding-top: 6px;
    padding-bottom: 6px;
}

html body .sb-advanced-nav {
    pointer-events: auto;
    width: min(100%, 1320px);
    margin: 0 auto;
    border: 1px solid rgba(255,255,255,.16);
    border-radius: 30px;
    background:
        radial-gradient(circle at var(--nav-x, 10%) var(--nav-y, 0%), rgba(34,211,238,.20), transparent 32%),
        radial-gradient(circle at 90% 0%, rgba(124,60,255,.24), transparent 38%),
        linear-gradient(135deg, rgba(5,8,22,.97), rgba(15,24,54,.94));
    box-shadow: 0 22px 70px rgba(2,6,23,.35);
    backdrop-filter: blur(22px);
}

html body .sb-nav-inner {
    display: grid;
    grid-template-columns: minmax(210px, auto) minmax(0, 1fr) minmax(170px, 250px) auto auto;
    align-items: center;
    gap: clamp(8px, 1.5vw, 14px);
    min-width: 0;
    padding: 12px;
}

html body .sb-nav-brand,
html body .sb-nav-links a,
html body .sb-nav-actions a,
html body .sb-nav-more button,
html body .sb-mobile-links a,
html body .sb-mobile-actions a {
    text-decoration: none;
}

html body .sb-nav-brand {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    color: white;
    transition: transform .2s ease;
}

html body .sb-nav-brand:hover {
    transform: translateY(-2px);
}

html body .sb-nav-brand img,
html body .sb-nav-brand-fallback {
    flex: 0 0 auto;
    width: 48px;
    height: 48px;
    border-radius: 18px;
    object-fit: contain;
    background: rgba(255,255,255,.09);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.12);
    transition: transform .25s ease, filter .25s ease;
}

html body .sb-nav-brand:hover img {
    transform: rotate(-4deg) scale(1.05);
    filter: drop-shadow(0 10px 22px rgba(34,211,238,.22));
}

html body .sb-nav-brand-fallback {
    display: grid;
    place-items: center;
    color: white;
    background: linear-gradient(135deg, var(--sb-shell-purple), var(--sb-shell-blue));
    font-weight: 950;
}

html body .sb-nav-brand span {
    min-width: 0;
}

html body .sb-nav-brand strong {
    display: block;
    color: white;
    font-size: 1.05rem;
    line-height: 1;
    letter-spacing: -.03em;
}

html body .sb-nav-brand em {
    display: block;
    max-width: 260px;
    margin-top: 4px;
    color: rgba(226,232,240,.76);
    font-size: .74rem;
    font-style: normal;
    font-weight: 800;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

html body .sb-nav-links {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
    min-width: 0;
}

html body .sb-nav-links > a,
html body .sb-nav-more > button {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 0;
    border-radius: 999px;
    padding: 10px 11px;
    color: rgba(226,232,240,.78);
    background: transparent;
    font: inherit;
    font-size: .9rem;
    font-weight: 850;
    white-space: nowrap;
    cursor: pointer;
    overflow: hidden;
    transition: color .18s ease, background .18s ease, transform .18s ease;
}

html body .sb-nav-links > a:hover,
html body .sb-nav-links > a.active,
html body .sb-nav-more > button:hover {
    color: white;
    background: rgba(255,255,255,.11);
    transform: translateY(-2px);
}

html body .sb-nav-links > a::after,
html body .sb-nav-more > button::after {
    content: "";
    position: absolute;
    left: 13px;
    right: 13px;
    bottom: 6px;
    height: 2px;
    border-radius: 999px;
    background: linear-gradient(90deg, var(--sb-shell-cyan), var(--sb-shell-purple));
    transform: scaleX(0);
    transform-origin: left;
    transition: transform .2s ease;
}

html body .sb-nav-links > a:hover::after,
html body .sb-nav-links > a.active::after,
html body .sb-nav-more > button:hover::after {
    transform: scaleX(1);
}

html body .sb-nav-more {
    position: relative;
}

html body .sb-nav-more-menu,
html body .sb-account-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: min(250px, 92vw);
    display: grid;
    gap: 6px;
    padding: 10px;
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 22px;
    background: #091126;
    box-shadow: 0 24px 70px rgba(2,6,23,.40);
    opacity: 0;
    visibility: hidden;
    transform: translateY(8px) scale(.96);
    transform-origin: top right;
    transition: .18s ease;
}

html body .sb-nav-more.open .sb-nav-more-menu,
html body .sb-account-menu.open .sb-account-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

html body .sb-nav-more-menu a,
html body .sb-account-dropdown a,
html body .sb-account-dropdown button {
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

html body .sb-nav-more-menu a:hover,
html body .sb-account-dropdown a:hover,
html body .sb-account-dropdown button:hover {
    color: white;
    background: rgba(34,211,238,.13);
}

html body .sb-account-dropdown form {
    margin: 0;
}

html body .sb-account-dropdown form button,
html body .sb-mobile-logout-form button {
    color: #fecaca;
}

html body .sb-nav-search {
    display: flex;
    align-items: center;
    min-width: 0;
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 999px;
    padding: 4px;
    background: rgba(255,255,255,.08);
    transition: transform .2s ease, border-color .2s ease, background .2s ease;
}

html body .sb-nav-search:hover,
html body .sb-nav-search:focus-within {
    transform: translateY(-2px);
    border-color: rgba(34,211,238,.38);
    background: rgba(255,255,255,.13);
}

html body .sb-nav-search input {
    width: 100%;
    min-width: 0;
    border: 0;
    outline: 0;
    padding: 8px 8px 8px 12px;
    background: transparent;
    color: white;
    font: inherit;
    font-size: .88rem;
}

html body .sb-nav-search input::placeholder {
    color: rgba(226,232,240,.56);
}

html body .sb-nav-search button {
    flex: 0 0 auto;
    width: 34px;
    height: 34px;
    border: 0;
    border-radius: 50%;
    color: #07101f;
    background: var(--sb-shell-cyan);
    cursor: pointer;
    font-weight: 950;
}

html body .sb-nav-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}

html body .sb-nav-actions a,
html body .sb-mobile-actions a,
html body .sb-mobile-search button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    border-radius: 999px;
    padding: 9px 13px;
    font-weight: 900;
    white-space: nowrap;
}

html body .sb-nav-actions .ghost,
html body .sb-mobile-actions a {
    color: white;
    background: rgba(255,255,255,.10);
}

html body .sb-nav-actions .solid,
html body .sb-mobile-actions .solid {
    color: white;
    background: linear-gradient(135deg, var(--sb-shell-purple), var(--sb-shell-blue));
    box-shadow: 0 12px 26px rgba(36,107,255,.26);
}

html body .sb-account-menu {
    position: relative;
    flex: 0 0 auto;
}

html body .sb-account-trigger {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    max-width: 235px;
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 999px;
    padding: 6px 10px 6px 6px;
    color: white;
    background: rgba(255,255,255,.10);
    font: inherit;
    cursor: pointer;
    transition: transform .2s ease, background .2s ease, border-color .2s ease;
}

html body .sb-account-trigger:hover {
    transform: translateY(-2px);
    border-color: rgba(34,211,238,.30);
    background: rgba(255,255,255,.15);
}

html body .sb-account-avatar {
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    color: #061022;
    background: var(--sb-shell-cyan);
    font-weight: 950;
}

html body .sb-account-label {
    display: grid;
    min-width: 0;
    text-align: left;
}

html body .sb-account-label strong,
html body .sb-account-label em {
    min-width: 0;
    max-width: 135px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

html body .sb-account-label strong {
    font-size: .86rem;
    line-height: 1.05;
}

html body .sb-account-label em {
    color: rgba(226,232,240,.70);
    font-size: .72rem;
    font-style: normal;
    text-transform: capitalize;
}

html body .sb-account-trigger i {
    font-style: normal;
    opacity: .7;
}

html body .sb-nav-toggle {
    display: none;
    width: 44px;
    height: 44px;
    border: 0;
    border-radius: 16px;
    background: rgba(255,255,255,.10);
    cursor: pointer;
}

html body .sb-nav-toggle span {
    display: block;
    width: 20px;
    height: 2px;
    margin: 4px auto;
    border-radius: 999px;
    background: white;
}

html body .sb-mobile-panel {
    display: none;
    padding: 0 12px 12px;
}

html body .sb-mobile-panel.open {
    display: grid;
    gap: 12px;
}

html body .sb-mobile-search {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 8px;
}

html body .sb-mobile-search input {
    min-width: 0;
    min-height: 44px;
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 16px;
    padding: 0 12px;
    color: white;
    background: rgba(255,255,255,.08);
    font: inherit;
}

html body .sb-mobile-links {
    display: grid;
    gap: 6px;
}

html body .sb-mobile-links a {
    border-radius: 16px;
    padding: 12px;
    color: white;
    background: rgba(255,255,255,.08);
    font-weight: 850;
    overflow-wrap: anywhere;
}

html body .sb-mobile-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

html body .sb-mobile-logout-form {
    margin: 0;
}

html body .sb-mobile-logout-form button {
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
    html body .sb-nav-inner {
        grid-template-columns: minmax(210px, auto) minmax(0, 1fr) auto;
    }

    html body .sb-nav-links,
    html body .sb-nav-search,
    html body .sb-nav-actions {
        display: none;
    }

    html body .sb-nav-toggle {
        display: block;
        justify-self: end;
    }
}

@media (max-width: 520px) {
    html body .sb-nav-brand em {
        max-width: 145px;
    }

    html body .sb-nav-brand img,
    html body .sb-nav-brand-fallback {
        width: 42px;
        height: 42px;
        border-radius: 15px;
    }
}
CSS;

$js = <<'JS'
(() => {
    const shell = document.querySelector('[data-shell]');
    if (!shell) return;

    const mobileToggle = shell.querySelector('[data-nav-toggle]');
    const mobilePanel = shell.querySelector('[data-mobile-panel]');
    const more = shell.querySelector('[data-more]');
    const moreButton = shell.querySelector('[data-more-button]');
    const accountMenu = shell.querySelector('[data-account-menu]');
    const accountButton = shell.querySelector('[data-account-button]');
    const nav = shell.querySelector('.sb-advanced-nav');

    const closeMore = () => {
        if (!more || !moreButton) return;
        more.classList.remove('open');
        moreButton.setAttribute('aria-expanded', 'false');
    };

    const closeAccount = () => {
        if (!accountMenu || !accountButton) return;
        accountMenu.classList.remove('open');
        accountButton.setAttribute('aria-expanded', 'false');
    };

    if (mobileToggle && mobilePanel) {
        mobileToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const open = mobilePanel.classList.toggle('open');
            mobileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    if (more && moreButton) {
        moreButton.addEventListener('click', (event) => {
            event.stopPropagation();
            closeAccount();
            const open = more.classList.toggle('open');
            moreButton.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        more.addEventListener('click', (event) => event.stopPropagation());
    }

    if (accountMenu && accountButton) {
        accountButton.addEventListener('click', (event) => {
            event.stopPropagation();
            closeMore();
            const open = accountMenu.classList.toggle('open');
            accountButton.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        accountMenu.addEventListener('click', (event) => event.stopPropagation());
    }

    document.addEventListener('click', () => {
        closeMore();
        closeAccount();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMore();
            closeAccount();
        }
    });

    const setScrolled = () => {
        shell.classList.toggle('is-scrolled', window.scrollY > 18);
    };

    setScrolled();
    window.addEventListener('scroll', setScrolled, { passive: true });

    if (nav) {
        nav.addEventListener('pointermove', (event) => {
            const rect = nav.getBoundingClientRect();
            nav.style.setProperty('--nav-x', `${event.clientX - rect.left}px`);
            nav.style.setProperty('--nav-y', `${event.clientY - rect.top}px`);
        });
    }
})();
JS;

write_file('resources/views/layouts/partials/sb-shell-navbar.blade.php', $navbar);

write_file('resources/views/partials/navbar.blade.php', "@include('layouts.partials.sb-shell-navbar')\n");
write_file('resources/views/layouts/partials/studybuddy-nav.blade.php', "@include('layouts.partials.sb-shell-navbar')\n");

write_file('public/assets/css/studybuddy-advanced-shell.css', $css);
write_file('public/assets/js/studybuddy-advanced-shell.js', $js);

foreach ([
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/guest.blade.php',
    'resources/views/layouts/public.blade.php',
    'resources/views/layouts/frontend.blade.php',
    'resources/views/layouts/main.blade.php',
] as $layout) {
    patch_layout_asset($layout);
}

echo "\nDONE ✅ Hard-fixed navbar styles + account logout dropdown.\n";
