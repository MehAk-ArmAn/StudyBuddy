<?php

$root = dirname(__DIR__);

function backup(string $file): void {
    if (file_exists($file)) {
        copy($file, $file . '.bak_' . date('Ymd_His'));
    }
}

function write_file(string $path, string $content): void {
    global $root;
    $full = $root . '/' . $path;
    if (!is_dir(dirname($full))) {
        mkdir(dirname($full), 0777, true);
    }
    backup($full);
    file_put_contents($full, $content);
    echo "✓ wrote {$path}\n";
}

function patch_layout(string $path): void {
    global $root;

    $full = $root . '/' . $path;
    if (!file_exists($full)) {
        return;
    }

    backup($full);
    $text = file_get_contents($full);

    $oldAssets = [
        'sb-bangtan-nav-footer.css',
        'sb-bangtan-hover-footer-upgrade.css',
        'sb-shell-nav-footer-fullwidth.css',
        'sb-consistent-shell.css',
        'sb-shell-safe-links-fix.css',
        'sb-bangtan-nav-footer.js',
        'sb-bangtan-hover-footer-upgrade.js',
        'sb-shell-nav-footer.js',
        'sb-consistent-shell.js',
    ];

    $lines = explode("\n", $text);
    $clean = [];

    foreach ($lines as $line) {
        $remove = false;
        foreach ($oldAssets as $asset) {
            if (str_contains($line, $asset)) {
                $remove = true;
                break;
            }
        }
        if (!$remove) {
            $clean[] = $line;
        }
    }

    $text = implode("\n", $clean);

    $css = <<<'BLADE'
    {{-- StudyBuddy ONE TRUE SHELL --}}
    @if(file_exists(public_path('assets/css/studybuddy-advanced-shell.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-advanced-shell.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-advanced-shell.css')) }}">
    @endif
    @if(file_exists(public_path('assets/css/studybuddy-shell-force-override.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-shell-force-override.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-shell-force-override.css')) }}">
    @endif
BLADE;

    $js = <<<'BLADE'
    @if(file_exists(public_path('assets/js/studybuddy-advanced-shell.js')))
        <script src="{{ asset('assets/js/studybuddy-advanced-shell.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-advanced-shell.js')) }}" defer></script>
    @endif
BLADE;

    // Remove old duplicate advanced includes first.
    $text = preg_replace('/\s*@if\(file_exists\(public_path\(\'assets\/css\/studybuddy-advanced-shell\.css\'\)\)\).*?@endif/s', '', $text);
    $text = preg_replace('/\s*@if\(file_exists\(public_path\(\'assets\/css\/studybuddy-shell-force-override\.css\'\)\)\).*?@endif/s', '', $text);
    $text = preg_replace('/\s*@if\(file_exists\(public_path\(\'assets\/js\/studybuddy-advanced-shell\.js\'\)\)\).*?@endif/s', '', $text);

    if (str_contains($text, '</head>')) {
        $text = str_replace('</head>', $css . "\n</head>", $text);
    }

    if (str_contains($text, '</body>')) {
        $text = str_replace('</body>', $js . "\n</body>", $text);
    }

    // Force navbar right after <body> if this layout does not already include any canonical shell/nav.
    if (
        !str_contains($text, "layouts.partials.sb-shell-navbar")
        && !str_contains($text, "partials.navbar")
        && !str_contains($text, "studybuddy-nav")
        && str_contains($text, '<body')
        && !str_contains($path, 'admin')
    ) {
        $text = preg_replace('/(<body[^>]*>)/i', "$1\n@include('layouts.partials.sb-shell-navbar')", $text, 1);
    }

    // Force footer before </body> if layout does not include any canonical shell/footer.
    if (
        !str_contains($text, "layouts.partials.sb-shell-footer")
        && !str_contains($text, "partials.footer")
        && !str_contains($text, "studybuddy-footer")
        && str_contains($text, '</body>')
        && !str_contains($path, 'admin')
    ) {
        $text = str_replace('</body>', "@include('layouts.partials.sb-shell-footer')\n</body>", $text);
    }

    file_put_contents($full, $text);
    echo "✓ patched layout {$path}\n";
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
        if (is_array($decoded)) $navItems = $decoded;
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
/* StudyBuddy final forced shell override */
html body .sb-advanced-shell {
    position: sticky !important;
    top: 0 !important;
    z-index: 99999 !important;
    padding: 10px clamp(10px, 2vw, 26px) !important;
    pointer-events: none !important;
}

html body .sb-advanced-nav {
    pointer-events: auto !important;
    width: min(100%, 1320px) !important;
    margin: 0 auto !important;
    border: 1px solid rgba(255,255,255,.16) !important;
    border-radius: 30px !important;
    background:
        radial-gradient(circle at var(--nav-x, 12%) var(--nav-y, 0%), rgba(34,211,238,.20), transparent 32%),
        radial-gradient(circle at 90% 0%, rgba(124,60,255,.24), transparent 38%),
        linear-gradient(135deg, rgba(5,8,22,.98), rgba(15,24,54,.95)) !important;
    box-shadow: 0 22px 70px rgba(2,6,23,.38) !important;
    backdrop-filter: blur(22px) !important;
}

html body .sb-nav-inner {
    display: grid !important;
    grid-template-columns: minmax(210px, auto) minmax(0, 1fr) minmax(170px, 250px) auto auto !important;
    align-items: center !important;
    gap: clamp(8px, 1.5vw, 14px) !important;
    min-width: 0 !important;
    padding: 12px !important;
}

html body .sb-nav-brand,
html body .sb-nav-links a,
html body .sb-nav-actions a,
html body .sb-nav-more button,
html body .sb-mobile-links a,
html body .sb-mobile-actions a {
    text-decoration: none !important;
}

html body .sb-nav-brand {
    display: inline-flex !important;
    align-items: center !important;
    gap: 12px !important;
    min-width: 0 !important;
    color: white !important;
}

html body .sb-nav-brand img,
html body .sb-nav-brand-fallback {
    flex: 0 0 auto !important;
    width: 48px !important;
    height: 48px !important;
    border-radius: 18px !important;
    object-fit: contain !important;
    background: rgba(255,255,255,.09) !important;
}

html body .sb-nav-brand strong {
    display: block !important;
    color: white !important;
    font-size: 1.05rem !important;
    line-height: 1 !important;
}

html body .sb-nav-brand em {
    display: block !important;
    max-width: 260px !important;
    margin-top: 4px !important;
    color: rgba(226,232,240,.76) !important;
    font-size: .74rem !important;
    font-style: normal !important;
    font-weight: 800 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

html body .sb-nav-links {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 5px !important;
    min-width: 0 !important;
}

html body .sb-nav-links > a,
html body .sb-nav-more > button {
    position: relative !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    border: 0 !important;
    border-radius: 999px !important;
    padding: 10px 11px !important;
    color: rgba(226,232,240,.78) !important;
    background: transparent !important;
    font: inherit !important;
    font-size: .9rem !important;
    font-weight: 850 !important;
    white-space: nowrap !important;
    cursor: pointer !important;
    transition: color .18s ease, background .18s ease, transform .18s ease !important;
}

html body .sb-nav-links > a:hover,
html body .sb-nav-links > a.active,
html body .sb-nav-more > button:hover {
    color: white !important;
    background: rgba(255,255,255,.11) !important;
    transform: translateY(-2px) !important;
}

html body .sb-nav-more,
html body .sb-account-menu {
    position: relative !important;
}

html body .sb-nav-more-menu,
html body .sb-account-dropdown {
    position: absolute !important;
    top: calc(100% + 12px) !important;
    right: 0 !important;
    width: min(250px, 92vw) !important;
    display: grid !important;
    gap: 6px !important;
    padding: 10px !important;
    border: 1px solid rgba(255,255,255,.14) !important;
    border-radius: 22px !important;
    background: #091126 !important;
    box-shadow: 0 24px 70px rgba(2,6,23,.40) !important;
    opacity: 0 !important;
    visibility: hidden !important;
    transform: translateY(8px) scale(.96) !important;
    transform-origin: top right !important;
    transition: .18s ease !important;
}

html body .sb-nav-more.open .sb-nav-more-menu,
html body .sb-account-menu.open .sb-account-dropdown {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) scale(1) !important;
}

html body .sb-nav-more-menu a,
html body .sb-account-dropdown a,
html body .sb-account-dropdown button {
    width: 100% !important;
    border: 0 !important;
    border-radius: 14px !important;
    padding: 11px 12px !important;
    color: rgba(226,232,240,.84) !important;
    background: transparent !important;
    text-align: left !important;
    text-decoration: none !important;
    font: inherit !important;
    font-weight: 850 !important;
    cursor: pointer !important;
}

html body .sb-nav-more-menu a:hover,
html body .sb-account-dropdown a:hover,
html body .sb-account-dropdown button:hover {
    color: white !important;
    background: rgba(34,211,238,.13) !important;
}

html body .sb-nav-search {
    display: flex !important;
    align-items: center !important;
    min-width: 0 !important;
    border: 1px solid rgba(255,255,255,.12) !important;
    border-radius: 999px !important;
    padding: 4px !important;
    background: rgba(255,255,255,.08) !important;
}

html body .sb-nav-search input {
    width: 100% !important;
    min-width: 0 !important;
    border: 0 !important;
    outline: 0 !important;
    padding: 8px 8px 8px 12px !important;
    background: transparent !important;
    color: white !important;
    font: inherit !important;
    font-size: .88rem !important;
}

html body .sb-nav-search input::placeholder {
    color: rgba(226,232,240,.56) !important;
}

html body .sb-nav-search button {
    flex: 0 0 auto !important;
    width: 34px !important;
    height: 34px !important;
    border: 0 !important;
    border-radius: 50% !important;
    color: #07101f !important;
    background: #22d3ee !important;
    cursor: pointer !important;
    font-weight: 950 !important;
}

html body .sb-nav-actions {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    min-width: 0 !important;
}

html body .sb-nav-actions a,
html body .sb-mobile-actions a,
html body .sb-mobile-search button {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 40px !important;
    border-radius: 999px !important;
    padding: 9px 13px !important;
    font-weight: 900 !important;
    white-space: nowrap !important;
}

html body .sb-nav-actions .ghost,
html body .sb-mobile-actions a {
    color: white !important;
    background: rgba(255,255,255,.10) !important;
}

html body .sb-nav-actions .solid,
html body .sb-mobile-actions .solid {
    color: white !important;
    background: linear-gradient(135deg, #7c3cff, #246bff) !important;
}

html body .sb-account-trigger {
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
    max-width: 235px !important;
    border: 1px solid rgba(255,255,255,.14) !important;
    border-radius: 999px !important;
    padding: 6px 10px 6px 6px !important;
    color: white !important;
    background: rgba(255,255,255,.10) !important;
    font: inherit !important;
    cursor: pointer !important;
}

html body .sb-account-avatar {
    width: 36px !important;
    height: 36px !important;
    display: grid !important;
    place-items: center !important;
    border-radius: 50% !important;
    color: #061022 !important;
    background: #22d3ee !important;
    font-weight: 950 !important;
}

html body .sb-account-label {
    display: grid !important;
    min-width: 0 !important;
    text-align: left !important;
}

html body .sb-account-label strong,
html body .sb-account-label em {
    min-width: 0 !important;
    max-width: 135px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

html body .sb-account-label strong {
    font-size: .86rem !important;
    line-height: 1.05 !important;
    color: white !important;
}

html body .sb-account-label em {
    color: rgba(226,232,240,.70) !important;
    font-size: .72rem !important;
    font-style: normal !important;
    text-transform: capitalize !important;
}

html body .sb-account-dropdown form,
html body .sb-mobile-logout-form {
    margin: 0 !important;
}

html body .sb-account-dropdown form button,
html body .sb-mobile-logout-form button {
    color: #fecaca !important;
}

html body .sb-nav-toggle {
    display: none !important;
    width: 44px !important;
    height: 44px !important;
    border: 0 !important;
    border-radius: 16px !important;
    background: rgba(255,255,255,.10) !important;
    cursor: pointer !important;
}

html body .sb-nav-toggle span {
    display: block !important;
    width: 20px !important;
    height: 2px !important;
    margin: 4px auto !important;
    border-radius: 999px !important;
    background: white !important;
}

html body .sb-mobile-panel {
    display: none !important;
    padding: 0 12px 12px !important;
}

html body .sb-mobile-panel.open {
    display: grid !important;
    gap: 12px !important;
}

@media (max-width: 1180px) {
    html body .sb-nav-inner {
        grid-template-columns: minmax(210px, auto) minmax(0, 1fr) auto !important;
    }

    html body .sb-nav-links,
    html body .sb-nav-search,
    html body .sb-nav-actions {
        display: none !important;
    }

    html body .sb-nav-toggle {
        display: block !important;
        justify-self: end !important;
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

write_file('public/assets/css/studybuddy-shell-force-override.css', $css);
write_file('public/assets/js/studybuddy-advanced-shell.js', $js);

foreach ([
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/guest.blade.php',
    'resources/views/layouts/public.blade.php',
    'resources/views/layouts/frontend.blade.php',
    'resources/views/layouts/main.blade.php',
    'resources/views/layouts/master.blade.php',
] as $layout) {
    patch_layout($layout);
}

echo "\nDONE ✅ One real shell forced. Old shell assets removed from layouts.\n";
