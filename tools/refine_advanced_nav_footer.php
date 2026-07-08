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
    <div class="sb-footer-glow one" aria-hidden="true"></div>
    <div class="sb-footer-glow two" aria-hidden="true"></div>

    <div class="sb-footer-inner">
        <section class="sb-footer-top-section">
            <div class="sb-footer-brand-block">
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

            <div class="sb-footer-action-card">
                <span>Ready to start?</span>
                <h2>Build your learning dashboard.</h2>
                <p>Choose your role, explore the app universe, and grow with calm progress.</p>
                <div>
                    <a href="{{ url('/register') }}">Create account</a>
                    <a class="soft" href="{{ url('/apps') }}">Explore apps</a>
                </div>
            </div>
        </section>

        <section class="sb-footer-value-strip" aria-label="StudyBuddy values">
            <article>
                <strong>{{ $pillOne }}</strong>
                <span>Discover skill-building mini apps.</span>
            </article>
            <article>
                <strong>{{ $pillTwo }}</strong>
                <span>Practice with clear feedback and routines.</span>
            </article>
            <article>
                <strong>{{ $pillThree }}</strong>
                <span>Stay motivated with quests and progress.</span>
            </article>
        </section>

        <section class="sb-footer-link-section">
            <div class="sb-footer-section-heading">
                <span>StudyBuddy Map</span>
                <h2>Everything connected in one learning universe.</h2>
            </div>

            <div class="sb-footer-grid" aria-label="StudyBuddy footer navigation">
                @foreach($footerGroups as $group => $links)
                    <div class="sb-footer-group">
                        <h3>{{ $group }}</h3>
                        <ul>
                            @foreach($links as $link)
                                @php
                                    $label = $link['label'] ?? 'Link';
                                    $url = $link['url'] ?? '#';
                                    $isExternal = str_starts_with($url, 'http') || str_starts_with($url, 'mailto:');
                                @endphp
                                <li>
                                    <a href="{{ $isExternal ? $url : url($url) }}" @if($isExternal && str_starts_with($url, 'http')) target="_blank" rel="noopener" @endif>
                                        <span>{{ $label }}</span>
                                        <i>→</i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <div class="sb-footer-group sb-footer-newsletter">
                    <h3>Updates</h3>
                    <p>Launch notes, new learning worlds, and StudyBuddy improvements.</p>

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
            </div>
        </section>

        <section class="sb-footer-bottom">
            <p>© {{ date('Y') }} {{ $brandName }}. Safe, playful learning for every role.</p>
            <p>Created by <a href="{{ $shellSettings['creator_url'] ?? 'https://pixelcraftslab.com' }}" target="_blank" rel="noopener">{{ $shellSettings['creator_name'] ?? 'PixelCraftsLab Studio' }}</a></p>
        </section>
    </div>
</footer>
BLADE;

$css = <<<'CSS'
:root {
    --sb-shell-bg: rgba(7, 12, 32, .92);
    --sb-shell-bg-2: rgba(13, 21, 48, .88);
    --sb-shell-text: #f8fbff;
    --sb-shell-muted: rgba(226, 232, 240, .72);
    --sb-shell-dark-text: #102033;
    --sb-shell-line: rgba(255, 255, 255, .14);
    --sb-shell-purple: #7c3cff;
    --sb-shell-blue: #246bff;
    --sb-shell-cyan: #22d3ee;
    --sb-shell-ink: #050816;
    --sb-shell-card: #ffffff;
    --sb-shell-shadow: 0 24px 70px rgba(15, 23, 42, .18);
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

/* NAVBAR — darker, less trapped, more premium */
.sb-advanced-shell {
    position: sticky;
    top: 0;
    z-index: 1000;
    padding: 12px clamp(10px, 2vw, 26px);
    pointer-events: none;
}

.sb-advanced-nav {
    pointer-events: auto;
    width: min(100%, 1320px);
    margin: 0 auto;
    border: 1px solid rgba(255,255,255,.16);
    border-radius: 30px;
    background:
        radial-gradient(circle at 6% 0%, rgba(34, 211, 238, .20), transparent 34%),
        radial-gradient(circle at 90% 0%, rgba(124, 60, 255, .22), transparent 36%),
        linear-gradient(135deg, rgba(5, 8, 22, .96), rgba(16, 25, 54, .92));
    box-shadow: 0 22px 70px rgba(2, 6, 23, .28);
    backdrop-filter: blur(22px);
}

.sb-nav-inner {
    display: grid;
    grid-template-columns: minmax(210px, auto) minmax(0, 1fr) minmax(170px, 250px) auto auto;
    align-items: center;
    gap: clamp(8px, 1.5vw, 14px);
    padding: 12px;
    min-width: 0;
}

.sb-nav-brand {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    color: white;
    text-decoration: none;
}

.sb-nav-brand img,
.sb-nav-brand-fallback {
    flex: 0 0 auto;
    width: 48px;
    height: 48px;
    border-radius: 18px;
    object-fit: contain;
    background: rgba(255,255,255,.08);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.12);
}

.sb-nav-brand-fallback {
    display: grid;
    place-items: center;
    color: white;
    background: linear-gradient(135deg, var(--sb-shell-purple), var(--sb-shell-blue));
    font-weight: 950;
}

.sb-nav-brand span {
    min-width: 0;
}

.sb-nav-brand strong {
    display: block;
    color: white;
    font-size: 1.05rem;
    line-height: 1;
    letter-spacing: -.03em;
}

.sb-nav-brand em {
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

.sb-nav-links {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
    min-width: 0;
    overflow: visible;
}

.sb-nav-links > a,
.sb-nav-more > button {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
    border: 0;
    border-radius: 999px;
    padding: 10px 11px;
    color: rgba(226,232,240,.76);
    background: transparent;
    text-decoration: none;
    font: inherit;
    font-size: .9rem;
    font-weight: 850;
    white-space: nowrap;
    cursor: pointer;
    transition: color .18s ease, background .18s ease, transform .18s ease;
}

.sb-nav-links > a:hover,
.sb-nav-links > a.active,
.sb-nav-more > button:hover {
    color: white;
    background: rgba(255, 255, 255, .10);
    transform: translateY(-1px);
}

.sb-nav-more {
    position: relative;
    flex: 0 0 auto;
}

.sb-nav-more-menu {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: min(260px, 92vw);
    display: grid;
    gap: 6px;
    padding: 10px;
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 22px;
    background: #091126;
    box-shadow: 0 24px 70px rgba(2,6,23,.36);
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
    color: rgba(226,232,240,.80);
    text-decoration: none;
    font-weight: 800;
    overflow-wrap: anywhere;
}

.sb-nav-more-menu a:hover,
.sb-nav-more-menu a.active {
    color: white;
    background: rgba(34, 211, 238, .12);
}

.sb-nav-search {
    display: flex;
    align-items: center;
    min-width: 0;
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 999px;
    padding: 4px;
    background: rgba(255,255,255,.08);
}

.sb-nav-search input {
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

.sb-nav-search input::placeholder {
    color: rgba(226,232,240,.56);
}

.sb-nav-search button {
    flex: 0 0 auto;
    width: 34px;
    height: 34px;
    border: 0;
    border-radius: 50%;
    color: #07101f;
    background: var(--sb-shell-cyan);
    cursor: pointer;
}

.sb-nav-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
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
    white-space: nowrap;
}

.sb-nav-actions .ghost,
.sb-mobile-actions a {
    color: white;
    background: rgba(255,255,255,.10);
}

.sb-nav-actions .solid,
.sb-mobile-actions .solid {
    color: white;
    background: linear-gradient(135deg, var(--sb-shell-purple), var(--sb-shell-blue));
    box-shadow: 0 12px 26px rgba(36,107,255,.26);
}

.sb-nav-toggle {
    display: none;
    width: 44px;
    height: 44px;
    border: 0;
    border-radius: 16px;
    background: rgba(255,255,255,.10);
    cursor: pointer;
}

.sb-nav-toggle span {
    display: block;
    width: 20px;
    height: 2px;
    margin: 4px auto;
    border-radius: 999px;
    background: white;
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
    min-width: 0;
    min-height: 44px;
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 16px;
    padding: 0 12px;
    color: white;
    background: rgba(255,255,255,.08);
    font: inherit;
}

.sb-mobile-search input::placeholder {
    color: rgba(226,232,240,.58);
}

.sb-mobile-links {
    display: grid;
    gap: 6px;
}

.sb-mobile-links a {
    border-radius: 16px;
    padding: 12px;
    color: white;
    background: rgba(255,255,255,.08);
    text-decoration: none;
    font-weight: 850;
    overflow-wrap: anywhere;
}

.sb-mobile-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* FOOTER — sectioned, spacious, no overflow */
.sb-advanced-footer {
    position: relative;
    margin-top: clamp(56px, 8vw, 110px);
    overflow: hidden;
    color: white;
    background:
        radial-gradient(circle at 18% 0%, rgba(34, 211, 238, .28), transparent 34%),
        radial-gradient(circle at 82% 22%, rgba(124, 60, 255, .36), transparent 36%),
        linear-gradient(135deg, #040716, #0e1735 52%, #07101f);
}

.sb-footer-glow {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}

.sb-footer-glow.one {
    width: 420px;
    height: 420px;
    right: -130px;
    top: -180px;
    border: 1px solid rgba(255,255,255,.13);
}

.sb-footer-glow.two {
    width: 260px;
    height: 260px;
    left: -80px;
    bottom: 70px;
    background: rgba(34,211,238,.08);
    filter: blur(8px);
}

.sb-footer-inner {
    position: relative;
    width: min(100%, 1240px);
    margin: 0 auto;
    padding: clamp(42px, 7vw, 76px) clamp(16px, 4vw, 34px) 26px;
}

.sb-footer-top-section {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(320px, 420px);
    gap: clamp(22px, 4vw, 42px);
    align-items: stretch;
    padding-bottom: 26px;
}

.sb-footer-brand-block,
.sb-footer-action-card,
.sb-footer-value-strip article,
.sb-footer-group {
    min-width: 0;
    overflow: hidden;
}

.sb-footer-brand {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    max-width: 100%;
    color: white;
    text-decoration: none;
}

.sb-footer-brand img {
    flex: 0 0 auto;
    width: 62px;
    height: 62px;
    border-radius: 22px;
    object-fit: contain;
    background: rgba(255,255,255,.10);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.12);
}

.sb-footer-brand span {
    min-width: 0;
}

.sb-footer-brand strong {
    display: block;
    font-size: clamp(1.55rem, 3vw, 2.35rem);
    letter-spacing: -.06em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sb-footer-brand em {
    display: block;
    margin-top: 4px;
    color: rgba(255,255,255,.74);
    font-style: normal;
    font-weight: 850;
    overflow-wrap: anywhere;
}

.sb-footer-brand-block p {
    max-width: 760px;
    margin: 20px 0 0;
    color: rgba(255,255,255,.72);
    line-height: 1.75;
    overflow-wrap: anywhere;
}

.sb-footer-action-card {
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 30px;
    padding: clamp(20px, 3vw, 28px);
    background:
        radial-gradient(circle at 100% 0%, rgba(34,211,238,.16), transparent 34%),
        rgba(255,255,255,.075);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.08);
}

.sb-footer-action-card span {
    color: var(--sb-shell-cyan);
    font-size: .78rem;
    font-weight: 950;
    text-transform: uppercase;
    letter-spacing: .12em;
}

.sb-footer-action-card h2 {
    margin: 10px 0 8px;
    font-size: clamp(1.55rem, 3vw, 2.2rem);
    line-height: 1;
    letter-spacing: -.05em;
    overflow-wrap: anywhere;
}

.sb-footer-action-card p {
    margin: 0 0 18px;
    color: rgba(255,255,255,.70);
    line-height: 1.6;
}

.sb-footer-action-card div {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.sb-footer-action-card a {
    display: inline-flex;
    justify-content: center;
    border-radius: 999px;
    padding: 11px 15px;
    color: #061022;
    background: var(--sb-shell-cyan);
    text-decoration: none;
    font-weight: 950;
}

.sb-footer-action-card a.soft {
    color: white;
    background: rgba(255,255,255,.12);
}

.sb-footer-value-strip {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    padding: 18px 0 28px;
    border-top: 1px solid rgba(255,255,255,.10);
    border-bottom: 1px solid rgba(255,255,255,.10);
}

.sb-footer-value-strip article {
    border: 1px solid rgba(255,255,255,.11);
    border-radius: 22px;
    padding: 16px;
    background: rgba(255,255,255,.06);
}

.sb-footer-value-strip strong {
    display: block;
    margin-bottom: 6px;
    color: white;
    font-size: 1rem;
    overflow-wrap: anywhere;
}

.sb-footer-value-strip span {
    display: block;
    color: rgba(255,255,255,.68);
    line-height: 1.55;
    overflow-wrap: anywhere;
}

.sb-footer-link-section {
    padding-top: 30px;
}

.sb-footer-section-heading {
    display: grid;
    gap: 5px;
    margin-bottom: 18px;
}

.sb-footer-section-heading span {
    color: var(--sb-shell-cyan);
    font-size: .78rem;
    font-weight: 950;
    text-transform: uppercase;
    letter-spacing: .12em;
}

.sb-footer-section-heading h2 {
    max-width: 680px;
    margin: 0;
    color: white;
    font-size: clamp(1.5rem, 3vw, 2.2rem);
    letter-spacing: -.045em;
    overflow-wrap: anywhere;
}

.sb-footer-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
}

.sb-footer-group {
    border: 1px solid rgba(255,255,255,.10);
    border-radius: 24px;
    padding: 18px;
    background: rgba(255,255,255,.055);
    min-height: 100%;
}

.sb-footer-group h3 {
    margin: 0 0 12px;
    color: white;
    font-size: .9rem;
    letter-spacing: .09em;
    text-transform: uppercase;
    overflow-wrap: anywhere;
}

.sb-footer-group ul {
    display: grid;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.sb-footer-group a {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: center;
    gap: 8px;
    border-radius: 14px;
    padding: 9px 10px;
    color: rgba(255,255,255,.72);
    background: transparent;
    text-decoration: none;
    font-weight: 760;
    line-height: 1.45;
}

.sb-footer-group a span {
    min-width: 0;
    overflow-wrap: anywhere;
}

.sb-footer-group a i {
    opacity: .5;
    font-style: normal;
    transition: transform .18s ease;
}

.sb-footer-group a:hover {
    color: white;
    background: rgba(255,255,255,.08);
}

.sb-footer-group a:hover i {
    transform: translateX(2px);
}

.sb-footer-newsletter p {
    margin: 0 0 14px;
    color: rgba(255,255,255,.70);
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.sb-footer-newsletter form {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
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
    color: white;
    background: rgba(255,255,255,.10);
}

.sb-footer-bottom {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    margin-top: 28px;
    padding-top: 22px;
    border-top: 1px solid rgba(255,255,255,.12);
    color: rgba(255,255,255,.64);
    font-size: .92rem;
}

.sb-footer-bottom p {
    margin: 0;
    overflow-wrap: anywhere;
}

.sb-footer-bottom a {
    color: white;
    font-weight: 900;
    text-decoration: none;
}

/* anti-overflow polish for public cards/boxes */
.studybuddy-site *,
.sb-site *,
.sb-section *,
.sb-card *,
.app-card *,
.preview-card *,
[class*="card"],
[class*="panel"] {
    min-width: 0;
}

.studybuddy-site p,
.studybuddy-site h1,
.studybuddy-site h2,
.studybuddy-site h3,
.studybuddy-site a,
.sb-section p,
.sb-section h1,
.sb-section h2,
.sb-section h3,
.app-card p,
.preview-card p {
    overflow-wrap: anywhere;
}

.studybuddy-site img,
.sb-site img,
.sb-section img,
.app-card img,
.preview-card img {
    max-width: 100%;
    height: auto;
    object-fit: contain;
}

@media (max-width: 1180px) {
    .sb-nav-inner {
        grid-template-columns: minmax(210px, auto) minmax(0, 1fr) auto;
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

@media (max-width: 820px) {
    .sb-advanced-shell {
        padding: 8px;
    }

    .sb-advanced-nav {
        border-radius: 22px;
    }

    .sb-nav-inner {
        gap: 8px;
    }

    .sb-nav-brand em {
        max-width: 190px;
    }

    .sb-footer-top-section {
        grid-template-columns: 1fr;
    }

    .sb-footer-value-strip {
        grid-template-columns: 1fr;
    }

    .sb-footer-grid {
        grid-template-columns: 1fr;
    }

    .sb-footer-newsletter form {
        grid-template-columns: 1fr;
    }

    .sb-footer-bottom {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .sb-nav-brand strong {
        font-size: .98rem;
    }

    .sb-nav-brand em {
        max-width: 145px;
    }

    .sb-nav-brand img,
    .sb-nav-brand-fallback {
        width: 42px;
        height: 42px;
        border-radius: 15px;
    }

    .sb-footer-brand {
        align-items: flex-start;
    }

    .sb-footer-brand strong {
        white-space: normal;
    }

    .sb-footer-action-card div {
        display: grid;
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

put_file('resources/views/layouts/partials/sb-shell-footer.blade.php', $footer);
put_file('public/assets/css/studybuddy-advanced-shell.css', $css);

echo "\nDONE ✅ Navbar darkened + footer sectioned + overflow polished.\n";
