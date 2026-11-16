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

function patch_layout(string $layout): void {
    global $root;
    $full = $root . '/' . $layout;
    if (!file_exists($full)) {
        echo "skip {$layout}\n";
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
    echo "✓ patched {$layout}\n";
}

$navbar = <<<'BLADE'
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
BLADE;

$footer = <<<'BLADE'
@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;

    $shellSettings = [];
    if (Schema::hasTable('site_settings')) {
        $shellSettings = DB::table('site_settings')->pluck('value', 'key')->all();
    }

    $brandName = $shellSettings['site_name'] ?? 'StudyBuddy';
    $tagline = $shellSettings['site_tagline'] ?? 'Learn. Play. Grow. Your Way.';
    $promise = $shellSettings['brand_promise'] ?? $shellSettings['footer_text'] ?? 'A playful learning universe for students, parents, teachers, and independent learners.';

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

    $fallbackFooter = [
        'Explore' => [
            ['label' => 'Apps', 'url' => '/apps'],
            ['label' => 'Learning Hub', 'url' => '/apps?section=learning'],
            ['label' => 'Rewards', 'url' => '/apps?section=rewards'],
            ['label' => 'Dashboard', 'url' => '/dashboard'],
        ],
        'Learning Worlds' => [
            ['label' => 'Math Quest', 'url' => '/apps/math-quest'],
            ['label' => 'Spelling Sprint', 'url' => '/apps/spelling-sprint'],
            ['label' => 'Reading Garden', 'url' => '/apps/reading-garden'],
            ['label' => 'Focus Forest', 'url' => '/apps/focus-forest'],
            ['label' => 'Quiz Galaxy', 'url' => '/apps/quiz-galaxy'],
        ],
        'For Every Role' => [
            ['label' => 'Students', 'url' => '/apps?role=student'],
            ['label' => 'Parents', 'url' => '/apps?role=parent'],
            ['label' => 'Teachers', 'url' => '/apps?role=teacher'],
            ['label' => 'Independent Learners', 'url' => '/apps?role=independent_learner'],
        ],
        'Trust & Support' => [
            ['label' => 'Safety Promise', 'url' => '/apps?section=safety'],
            ['label' => 'Privacy First', 'url' => '/privacy-policy'],
            ['label' => 'Terms of Use', 'url' => '/terms'],
            ['label' => 'Contact Support', 'url' => 'mailto:support@studybuddy.fun'],
        ],
    ];

    $footerGroups = $safeJson($shellSettings['shell_footer_groups_json'] ?? null, $fallbackFooter);

    $socials = $safeJson($shellSettings['shell_social_links_json'] ?? null, [
        ['label' => 'Instagram', 'url' => ''],
        ['label' => 'YouTube', 'url' => ''],
        ['label' => 'LinkedIn', 'url' => ''],
    ]);

    $pillOne = $shellSettings['footer_pill_one'] ?? 'Explore apps';
    $pillTwo = $shellSettings['footer_pill_two'] ?? 'Build skills';
    $pillThree = $shellSettings['footer_pill_three'] ?? 'Earn points';
@endphp

<footer class="sb-advanced-footer">
    <div class="sb-footer-orbit" aria-hidden="true"></div>

    <div class="sb-footer-inner">
        <section class="sb-footer-hero">
            <div>
                <a class="sb-footer-brand" href="{{ url('/') }}">
                    @if($logoPath)
                        <img src="{{ str_starts_with($logoPath, 'http') ? $logoPath : asset(ltrim($logoPath, '/')) }}" alt="StudyBuddy logo">
                    @endif
                    <span>
                        <strong>{{ $brandName }}</strong>
                        <em>{{ $tagline }}</em>
                    </span>
                </a>
                <p>{{ $promise }}</p>
            </div>

            <div class="sb-footer-pills">
                <span>{{ $pillOne }}</span>
                <span>{{ $pillTwo }}</span>
                <span>{{ $pillThree }}</span>
            </div>
        </section>

        <section class="sb-footer-grid" aria-label="StudyBuddy footer navigation">
            @foreach($footerGroups as $group => $links)
                <div class="sb-footer-group">
                    <h2>{{ $group }}</h2>
                    <ul>
                        @foreach($links as $link)
                            @php
                                $label = $link['label'] ?? 'Link';
                                $url = $link['url'] ?? '#';
                                $isExternal = str_starts_with($url, 'http') || str_starts_with($url, 'mailto:');
                            @endphp
                            <li>
                                <a href="{{ $isExternal ? $url : url($url) }}" @if($isExternal && str_starts_with($url, 'http')) target="_blank" rel="noopener" @endif>
                                    {{ $label }}
                                    <span>→</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <div class="sb-footer-group sb-footer-newsletter">
                <h2>Stay in the loop</h2>
                <p>Get StudyBuddy updates, launch notes, and new learning worlds.</p>
                <form action="{{ url('/register') }}" method="GET">
                    <input name="email" type="email" placeholder="Your email">
                    <button type="submit">Join</button>
                </form>

                <div class="sb-footer-socials">
                    @foreach($socials as $social)
                        @php
                            $url = $social['url'] ?? '';
                            $label = $social['label'] ?? 'Social';
                        @endphp
                        @if($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener">{{ $label }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>

        <section class="sb-footer-bottom">
            <p>© {{ date('Y') }} {{ $brandName }}. Built for safe, playful learning.</p>
            <p>Created by <a href="{{ $shellSettings['creator_url'] ?? 'https://pixelcraftslab.com' }}" target="_blank" rel="noopener">{{ $shellSettings['creator_name'] ?? 'PixelCraftsLab Studio' }}</a></p>
        </section>
    </div>
</footer>
BLADE;

$css = <<<'CSS'
:root {
    --sb-shell-bg: rgba(255, 255, 255, .78);
    --sb-shell-text: #102033;
    --sb-shell-muted: #637083;
    --sb-shell-line: rgba(124, 60, 255, .12);
    --sb-shell-purple: #7c3cff;
    --sb-shell-blue: #246bff;
    --sb-shell-cyan: #22d3ee;
    --sb-shell-ink: #050816;
    --sb-shell-card: #ffffff;
    --sb-shell-shadow: 0 24px 70px rgba(15, 23, 42, .14);
}

.sr-only {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    overflow: hidden !important;
    clip: rect(1px, 1px, 1px, 1px) !important;
    white-space: nowrap !important;
}

.sb-skip-link {
    position: absolute;
    top: 8px;
    left: 8px;
    z-index: 9999;
    transform: translateY(-140%);
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

.sb-advanced-shell {
    position: sticky;
    top: 0;
    z-index: 1000;
    padding: 12px clamp(12px, 3vw, 30px);
    pointer-events: none;
}

.sb-advanced-nav {
    pointer-events: auto;
    max-width: 1240px;
    margin: 0 auto;
    border: 1px solid rgba(255,255,255,.7);
    border-radius: 28px;
    background:
        linear-gradient(135deg, rgba(255,255,255,.92), rgba(248, 252, 255, .78)),
        radial-gradient(circle at 0% 0%, rgba(34, 211, 238, .18), transparent 34%);
    box-shadow: var(--sb-shell-shadow);
    backdrop-filter: blur(20px);
}

.sb-nav-inner {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) minmax(180px, 260px) auto auto;
    align-items: center;
    gap: 14px;
    padding: 12px;
}

.sb-nav-brand {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    min-width: 220px;
    color: var(--sb-shell-text);
    text-decoration: none;
}

.sb-nav-brand img,
.sb-nav-brand-fallback {
    width: 48px;
    height: 48px;
    border-radius: 18px;
    object-fit: contain;
    background: linear-gradient(135deg, rgba(124,60,255,.12), rgba(34,211,238,.14));
    box-shadow: inset 0 0 0 1px rgba(124,60,255,.08);
}

.sb-nav-brand-fallback {
    display: grid;
    place-items: center;
    color: white;
    background: linear-gradient(135deg, var(--sb-shell-purple), var(--sb-shell-blue));
    font-weight: 950;
}

.sb-nav-brand strong {
    display: block;
    font-size: 1.04rem;
    line-height: 1;
    letter-spacing: -.03em;
}

.sb-nav-brand em {
    display: block;
    margin-top: 3px;
    color: var(--sb-shell-muted);
    font-size: .73rem;
    font-style: normal;
    font-weight: 800;
    white-space: nowrap;
}

.sb-nav-links {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    min-width: 0;
}

.sb-nav-links > a,
.sb-nav-more > button {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 0;
    border-radius: 999px;
    padding: 10px 12px;
    color: var(--sb-shell-muted);
    background: transparent;
    text-decoration: none;
    font: inherit;
    font-size: .92rem;
    font-weight: 850;
    cursor: pointer;
    transition: color .18s ease, background .18s ease, transform .18s ease;
}

.sb-nav-links > a:hover,
.sb-nav-links > a.active,
.sb-nav-more > button:hover {
    color: var(--sb-shell-text);
    background: rgba(124, 60, 255, .08);
    transform: translateY(-1px);
}

.sb-nav-more {
    position: relative;
}

.sb-nav-more-menu {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: 230px;
    display: grid;
    gap: 6px;
    padding: 10px;
    border: 1px solid var(--sb-shell-line);
    border-radius: 22px;
    background: white;
    box-shadow: var(--sb-shell-shadow);
    opacity: 0;
    visibility: hidden;
    transform: translateY(8px);
    transition: .18s ease;
}

.sb-nav-more.open .sb-nav-more-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.sb-nav-more-menu a {
    border-radius: 14px;
    padding: 10px 12px;
    color: var(--sb-shell-text);
    text-decoration: none;
    font-weight: 800;
}

.sb-nav-more-menu a:hover,
.sb-nav-more-menu a.active {
    background: rgba(34, 211, 238, .10);
}

.sb-nav-search {
    display: flex;
    align-items: center;
    min-width: 0;
    border: 1px solid rgba(124,60,255,.13);
    border-radius: 999px;
    padding: 4px;
    background: rgba(255,255,255,.78);
}

.sb-nav-search input {
    width: 100%;
    min-width: 0;
    border: 0;
    outline: 0;
    padding: 8px 8px 8px 12px;
    background: transparent;
    color: var(--sb-shell-text);
    font: inherit;
    font-size: .88rem;
}

.sb-nav-search button {
    width: 34px;
    height: 34px;
    border: 0;
    border-radius: 50%;
    color: white;
    background: linear-gradient(135deg, var(--sb-shell-purple), var(--sb-shell-blue));
    cursor: pointer;
}

.sb-nav-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.sb-nav-actions a,
.sb-mobile-actions a,
.sb-mobile-search button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    border-radius: 999px;
    padding: 9px 13px;
    text-decoration: none;
    font-weight: 900;
}

.sb-nav-actions .ghost,
.sb-mobile-actions a {
    color: var(--sb-shell-text);
    background: rgba(124,60,255,.08);
}

.sb-nav-actions .solid,
.sb-mobile-actions .solid {
    color: white;
    background: linear-gradient(135deg, var(--sb-shell-purple), var(--sb-shell-blue));
    box-shadow: 0 12px 26px rgba(36,107,255,.24);
}

.sb-nav-toggle {
    display: none;
    width: 44px;
    height: 44px;
    border: 0;
    border-radius: 16px;
    background: rgba(124,60,255,.10);
    cursor: pointer;
}

.sb-nav-toggle span {
    display: block;
    width: 20px;
    height: 2px;
    margin: 4px auto;
    border-radius: 999px;
    background: var(--sb-shell-text);
}

.sb-mobile-panel {
    display: none;
    padding: 0 12px 12px;
}

.sb-mobile-panel.open {
    display: grid;
    gap: 12px;
}

.sb-mobile-search {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 8px;
}

.sb-mobile-search input {
    min-height: 44px;
    border: 1px solid rgba(124,60,255,.14);
    border-radius: 16px;
    padding: 0 12px;
    font: inherit;
}

.sb-mobile-links {
    display: grid;
    gap: 6px;
}

.sb-mobile-links a {
    border-radius: 16px;
    padding: 12px;
    color: var(--sb-shell-text);
    background: rgba(124,60,255,.06);
    text-decoration: none;
    font-weight: 850;
}

.sb-mobile-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.sb-advanced-footer {
    position: relative;
    margin-top: clamp(50px, 8vw, 100px);
    overflow: hidden;
    color: white;
    background:
        radial-gradient(circle at 20% 0%, rgba(34, 211, 238, .26), transparent 34%),
        radial-gradient(circle at 80% 30%, rgba(124, 60, 255, .34), transparent 36%),
        linear-gradient(135deg, #050816, #101936 58%, #07101f);
}

.sb-footer-orbit {
    position: absolute;
    inset: -180px -80px auto auto;
    width: 420px;
    height: 420px;
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 50%;
}

.sb-footer-inner {
    position: relative;
    max-width: 1240px;
    margin: 0 auto;
    padding: clamp(42px, 7vw, 76px) clamp(18px, 4vw, 34px) 26px;
}

.sb-footer-hero {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 28px;
    align-items: center;
    padding-bottom: 34px;
    border-bottom: 1px solid rgba(255,255,255,.12);
}

.sb-footer-brand {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    color: white;
    text-decoration: none;
}

.sb-footer-brand img {
    width: 58px;
    height: 58px;
    border-radius: 20px;
    object-fit: contain;
    background: rgba(255,255,255,.10);
}

.sb-footer-brand strong {
    display: block;
    font-size: 1.45rem;
    letter-spacing: -.04em;
}

.sb-footer-brand em {
    display: block;
    margin-top: 3px;
    color: rgba(255,255,255,.72);
    font-style: normal;
    font-weight: 850;
}

.sb-footer-hero p {
    max-width: 700px;
    margin: 18px 0 0;
    color: rgba(255,255,255,.72);
    line-height: 1.75;
}

.sb-footer-pills {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 10px;
}

.sb-footer-pills span {
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 999px;
    padding: 10px 14px;
    background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.88);
    font-weight: 900;
}

.sb-footer-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: clamp(18px, 3vw, 30px);
    padding: 34px 0;
}

.sb-footer-group h2 {
    margin: 0 0 12px;
    color: white;
    font-size: .92rem;
    letter-spacing: .09em;
    text-transform: uppercase;
}

.sb-footer-group ul {
    display: grid;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.sb-footer-group a {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: rgba(255,255,255,.70);
    text-decoration: none;
    font-weight: 760;
    line-height: 1.45;
}

.sb-footer-group a:hover {
    color: white;
}

.sb-footer-group a span {
    opacity: .5;
    transition: transform .18s ease;
}

.sb-footer-group a:hover span {
    transform: translateX(2px);
}

.sb-footer-newsletter p {
    margin: 0 0 14px;
    color: rgba(255,255,255,.70);
    line-height: 1.6;
}

.sb-footer-newsletter form {
    display: flex;
    gap: 8px;
    padding: 6px;
    border-radius: 18px;
    background: rgba(255,255,255,.10);
}

.sb-footer-newsletter input {
    min-width: 0;
    width: 100%;
    border: 0;
    outline: 0;
    padding: 10px;
    color: white;
    background: transparent;
    font: inherit;
}

.sb-footer-newsletter input::placeholder {
    color: rgba(255,255,255,.54);
}

.sb-footer-newsletter button {
    border: 0;
    border-radius: 14px;
    padding: 10px 14px;
    color: #061022;
    background: var(--sb-shell-cyan);
    font-weight: 950;
    cursor: pointer;
}

.sb-footer-socials {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 12px;
}

.sb-footer-socials a {
    border-radius: 999px;
    padding: 8px 10px;
    background: rgba(255,255,255,.10);
}

.sb-footer-bottom {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,.12);
    color: rgba(255,255,255,.64);
    font-size: .92rem;
}

.sb-footer-bottom p {
    margin: 0;
}

.sb-footer-bottom a {
    color: white;
    font-weight: 900;
    text-decoration: none;
}

@media (max-width: 1120px) {
    .sb-nav-inner {
        grid-template-columns: auto minmax(0, 1fr) auto;
    }

    .sb-nav-links,
    .sb-nav-search,
    .sb-nav-actions {
        display: none;
    }

    .sb-nav-toggle {
        display: block;
        justify-self: end;
    }

    .sb-footer-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 760px) {
    .sb-advanced-shell {
        padding: 8px;
    }

    .sb-advanced-nav {
        border-radius: 22px;
    }

    .sb-nav-inner {
        gap: 8px;
    }

    .sb-nav-brand {
        min-width: 0;
    }

    .sb-nav-brand em {
        max-width: 190px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sb-footer-hero {
        grid-template-columns: 1fr;
    }

    .sb-footer-pills {
        justify-content: flex-start;
    }

    .sb-footer-grid {
        grid-template-columns: 1fr;
    }

    .sb-footer-newsletter form {
        flex-direction: column;
    }

    .sb-footer-bottom {
        flex-direction: column;
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        transition-duration: .01ms !important;
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
    }
}
CSS;

$js = <<<'JS'
(() => {
    const shell = document.querySelector('[data-shell]');
    if (!shell) return;

    const toggle = shell.querySelector('[data-nav-toggle]');
    const panel = shell.querySelector('[data-mobile-panel]');
    const more = shell.querySelector('[data-more]');
    const moreButton = shell.querySelector('[data-more-button]');

    if (toggle && panel) {
        toggle.addEventListener('click', () => {
            const open = panel.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    if (more && moreButton) {
        moreButton.addEventListener('click', (event) => {
            event.stopPropagation();
            const open = more.classList.toggle('open');
            moreButton.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        document.addEventListener('click', () => {
            more.classList.remove('open');
            moreButton.setAttribute('aria-expanded', 'false');
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                more.classList.remove('open');
                moreButton.setAttribute('aria-expanded', 'false');
            }
        });
    }
})();
JS;

put_file('resources/views/layouts/partials/sb-shell-navbar.blade.php', $navbar);
put_file('resources/views/layouts/partials/sb-shell-footer.blade.php', $footer);

put_file('resources/views/partials/navbar.blade.php', "@include('layouts.partials.sb-shell-navbar')\n");
put_file('resources/views/partials/footer.blade.php', "@include('layouts.partials.sb-shell-footer')\n");
put_file('resources/views/layouts/partials/studybuddy-nav.blade.php', "@include('layouts.partials.sb-shell-navbar')\n");
put_file('resources/views/layouts/partials/studybuddy-footer.blade.php', "@include('layouts.partials.sb-shell-footer')\n");

put_file('public/assets/css/studybuddy-advanced-shell.css', $css);
put_file('public/assets/js/studybuddy-advanced-shell.js', $js);

patch_layout('resources/views/layouts/app.blade.php');
patch_layout('resources/views/layouts/guest.blade.php');
patch_layout('resources/views/layouts/public.blade.php');
patch_layout('resources/views/layouts/frontend.blade.php');

echo "\nDONE ✅ Advanced navbar/footer applied.\n";
