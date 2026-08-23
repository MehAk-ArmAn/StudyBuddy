from pathlib import Path
from datetime import datetime
import re

stamp = datetime.now().strftime("%Y%m%d_%H%M%S")

files_to_write = [
    Path("resources/views/studybuddy/final/apps.blade.php"),
    Path("resources/views/studybuddy/final/app-detail.blade.php"),
    Path("resources/views/studybuddy/final/web-play.blade.php"),
    Path("public/assets/css/studybuddy-connected-apps.css"),
    Path("public/assets/js/studybuddy-connected-apps.js"),
    Path("tools/finalize_studybuddy_apps_database.php"),
]

for p in files_to_write:
    if p.exists():
        p.with_suffix(p.suffix + f".bak_{stamp}").write_text(p.read_text())
    p.parent.mkdir(parents=True, exist_ok=True)

# Remove old experimental app-world route include if it exists.
for route_file in [Path("routes/studybuddy.php"), Path("routes/web.php")]:
    if route_file.exists():
        text = route_file.read_text()
        if "studybuddy_app_worlds.php" in text:
            route_file.with_suffix(route_file.suffix + f".bak_{stamp}").write_text(text)
            text = "\n".join(line for line in text.splitlines() if "studybuddy_app_worlds.php" not in line)
            route_file.write_text(text + "\n")
            print(f"cleaned old app-world include from {route_file}")

old_route = Path("routes/studybuddy_app_worlds.php")
if old_route.exists():
    old_route.rename(old_route.with_suffix(f".php.disabled_{stamp}"))
    print("disabled old experimental app-world route file")

apps_view = r'''
@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Facades\Route;

    $currentRole = $role ?? (auth()->check() ? (auth()->user()->role ?? null) : null);

    $worlds = [
        'math-quest' => ['✦', 'Cosmic Number Quest', '#7c3cff', '#246bff', '#22d3ee', 'Math missions that feel like a galaxy adventure.'],
        'spelling-sprint' => ['Aa', 'Word Speed Arena', '#ff4f9a', '#7c3cff', '#ffd166', 'Fast, friendly word practice with memory boosts.'],
        'reading-garden' => ['☘', 'Story Growth Garden', '#16a34a', '#22c55e', '#22d3ee', 'Calm reading, vocabulary blooms, and reflection.'],
        'focus-forest' => ['◌', 'Calm Focus Forest', '#0f766e', '#22c55e', '#22d3ee', 'Gentle focus sessions with peaceful routines.'],
        'planner-city' => ['▦', 'Routine Builder City', '#f59e0b', '#ef4444', '#7c3cff', 'Turn tasks into a clear daily map.'],
        'quiz-galaxy' => ['◎', 'Review Galaxy', '#4f46e5', '#ec4899', '#22d3ee', 'Quick quizzes, smart retries, and confidence.'],
        'shapes-lab' => ['△', 'Geometry Lab', '#06b6d4', '#8b5cf6', '#facc15', 'Visual thinking, shapes, and STEM puzzles.'],
        'flashcard-castle' => ['▣', 'Memory Castle', '#9333ea', '#f97316', '#fde68a', 'Recall practice inside a magical memory world.'],
    ];

    $assetUrl = function ($path) {
        if (!$path) return asset('assets/studybuddy-imgs/brand/logo-icon.png');
        if (preg_match('/^https?:\/\//i', $path)) return $path;
        $clean = ltrim($path, '/');
        return file_exists(public_path($clean)) ? asset($clean) : asset('assets/studybuddy-imgs/brand/logo-icon.png');
    };

    $assetExists = function ($path) {
        if (!$path || preg_match('/^https?:\/\//i', $path)) return false;
        return file_exists(public_path(ltrim($path, '/')));
    };

    $questUrl = Route::has('studybuddy.quests.index') ? route('studybuddy.quests.index') : route('studybuddy.apps');
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-connected-apps.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-connected-apps.css')) ? filemtime(public_path('assets/css/studybuddy-connected-apps.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-connected-apps.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-connected-apps.js')) ? filemtime(public_path('assets/js/studybuddy-connected-apps.js')) : time() }}" defer></script>

<main id="main-content" class="sb-apps-final" data-sb-apps-page>
    <section class="sb-apps-hero-final">
        <div class="sb-apps-hero-copy">
            <p class="sb-apps-kicker">StudyBuddy App Universe</p>
            <h1>{{ $settings['launchpad_heading'] ?? 'Choose your learning world.' }}</h1>
            <p>{{ $settings['launchpad_intro'] ?? 'Pick a world, follow a tiny mission, collect progress, and make learning feel playful again.' }}</p>

            <div class="sb-apps-hero-actions">
                @auth
                    <a href="{{ route('studybuddy.final.points-wallet') }}">My Points Wallet</a>
                    <a class="soft" href="{{ $questUrl }}">My Quest</a>
                @else
                    <a href="{{ route('register') }}">Create free account</a>
                    <a class="soft" href="{{ route('login') }}">Login</a>
                @endauth
            </div>
        </div>

        <aside class="sb-apps-hero-card" data-magic-card>
            <span class="hero-orbit">🚀</span>
            <strong>{{ $apps->count() }}</strong>
            <p>learning worlds ready to explore</p>
            <div class="hero-dots" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
        </aside>
    </section>

    <section class="sb-apps-controls" aria-label="Find learning worlds">
        <label>
            <span>Search</span>
            <input type="search" placeholder="Search math, reading, focus..." data-sb-app-search>
        </label>

        <label>
            <span>Category</span>
            <select data-sb-app-filter>
                <option value="all">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ \Illuminate\Support\Str::slug($category) }}">{{ $category }}</option>
                @endforeach
            </select>
        </label>

        <label>
            <span>Role</span>
            <select data-sb-role-filter>
                <option value="all">All roles</option>
                @foreach($roles as $key => $label)
                    <option value="{{ $key }}" @selected($currentRole === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
    </section>

    <section class="sb-apps-friendly-note">
        @guest
            <strong>Preview mode:</strong> Browse every world. Login when you want to play, save progress, and earn points.
        @else
            <strong>{{ ucwords(str_replace('_', ' ', $currentRole ?? 'learner')) }} mode:</strong> Your learning worlds are ready. Pick one and start small.
        @endguest
    </section>

    @if($apps->count())
        <section class="sb-apps-grid-final" aria-label="StudyBuddy learning worlds">
            @foreach($apps as $app)
                @php
                    $rolesForApp = $app->audience_roles ?: ['student', 'parent', 'teacher', 'independent_learner'];
                    $world = $worlds[$app->slug] ?? [$app->icon ?: '✨', $app->category ?: 'Learning World', $app->accent ?: '#7c3cff', '#246bff', '#22d3ee', $app->tagline ?: 'A playful StudyBuddy learning world.'];

                    $mainImage = $assetUrl($app->safeHeroImage());

                    $galleryCandidates = [
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/01_app-icon/{$app->slug}_main-icon.png",
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/01_app-icon/{$app->slug}_icon-512.png",
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/02_orbs/{$app->slug}_orb-glow.png",
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/02_orbs/{$app->slug}_orb-small.png",
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/05_planets-bg/{$app->slug}_mini-planet.png",
                    ];

                    $gallery = collect($galleryCandidates)
                        ->filter(fn($path) => $assetExists($path))
                        ->map(fn($path) => $assetUrl($path))
                        ->unique()
                        ->take(3)
                        ->values();

                    $searchText = \Illuminate\Support\Str::lower($app->name.' '.$app->tagline.' '.$app->description.' '.$app->category.' '.implode(' ', $rolesForApp));
                @endphp

                <article
                    class="sb-app-card-final"
                    style="--app-one: {{ $world[2] }}; --app-two: {{ $world[3] }}; --app-three: {{ $world[4] }};"
                    data-app-card
                    data-magic-card
                    data-category="{{ \Illuminate\Support\Str::slug($app->category) }}"
                    data-roles="{{ implode(' ', $rolesForApp) }}"
                    data-search="{{ $searchText }}"
                >
                    <div class="sb-app-card-art">
                        <div class="generated-sparkles" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></div>
                        <img class="main-art" src="{{ $mainImage }}" alt="{{ $app->name }} artwork" loading="lazy">
                        <span class="app-symbol">{{ $world[0] }}</span>

                        @if($gallery->count())
                            <div class="mini-art-row" aria-hidden="true">
                                @foreach($gallery as $image)
                                    <img src="{{ $image }}" alt="">
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="sb-app-card-body">
                        <div class="sb-app-card-topline">
                            <span>{{ $world[1] }}</span>
                            <b>{{ ucfirst($app->status) }}</b>
                        </div>

                        <h2>{{ $app->name }}</h2>
                        <p class="tagline">{{ $app->tagline ?: $world[5] }}</p>
                        <p>{{ $app->preview_text ?: \Illuminate\Support\Str::limit($app->description, 145) }}</p>

                        <div class="role-pills">
                            @foreach($rolesForApp as $roleName)
                                <span>{{ ucwords(str_replace('_', ' ', $roleName)) }}</span>
                            @endforeach
                        </div>

                        <div class="app-mini-stats">
                            <span>⭐ {{ $app->points_reward }} pts</span>
                            <span>⏱ {{ $app->estimated_minutes }} min</span>
                            <span>{{ $app->age_min ? $app->age_min.'+' : 'All ages' }}</span>
                        </div>

                        <div class="app-card-actions">
                            <a href="{{ route('studybuddy.apps.show', $app->slug) }}">Explore World</a>
                            @if($app->is_web_enabled)
                                @auth
                                    <a class="soft" href="{{ route('studybuddy.final.web-play', $app->slug) }}">Play</a>
                                @else
                                    <a class="soft" href="{{ route('login') }}">Login to Play</a>
                                @endauth
                            @else
                                <span>Coming Soon</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <p class="sb-apps-empty" data-sb-empty hidden>No worlds match that search yet. Try clearing the filters.</p>
    @else
        <section class="sb-apps-empty">
            <h2>Learning worlds are getting ready.</h2>
            <p>Please check back soon.</p>
        </section>
    @endif
</main>
@endsection
'''

detail_view = r'''
@extends('layouts.app')

@section('content')
@php
    $worlds = [
        'math-quest' => ['✦', 'Cosmic Number Quest', '#7c3cff', '#246bff', '#22d3ee', '#fff3b0', 'Turn numbers into a glowing mission you actually want to finish.'],
        'spelling-sprint' => ['Aa', 'Word Speed Arena', '#ff4f9a', '#7c3cff', '#ffd166', '#fff0f8', 'Make spelling practice fast, friendly, and way less scary.'],
        'reading-garden' => ['☘', 'Story Growth Garden', '#16a34a', '#22c55e', '#22d3ee', '#f0fff6', 'Grow reading fluency one calm story at a time.'],
        'focus-forest' => ['◌', 'Calm Focus Forest', '#0f766e', '#22c55e', '#22d3ee', '#ecfeff', 'Build focus without making studying feel heavy.'],
        'planner-city' => ['▦', 'Routine Builder City', '#f59e0b', '#ef4444', '#7c3cff', '#fff7ed', 'Turn messy tasks into a city map you can follow.'],
        'quiz-galaxy' => ['◎', 'Review Galaxy', '#4f46e5', '#ec4899', '#22d3ee', '#eef2ff', 'Launch quick quizzes across your learning galaxy.'],
        'shapes-lab' => ['△', 'Geometry Discovery Lab', '#06b6d4', '#8b5cf6', '#facc15', '#ecfeff', 'Explore shapes, patterns, and visual problem solving.'],
        'flashcard-castle' => ['▣', 'Memory Castle', '#9333ea', '#f97316', '#fde68a', '#faf5ff', 'Protect your knowledge inside a memory castle.'],
    ];

    $world = $worlds[$app->slug] ?? [$app->icon ?: '✨', $app->category ?: 'Learning World', $app->accent ?: '#7c3cff', '#246bff', '#22d3ee', '#f7f9ff', $app->tagline ?: 'Explore this StudyBuddy learning world.'];

    $assetUrl = function ($path) {
        if (!$path) return asset('assets/studybuddy-imgs/brand/logo-icon.png');
        if (preg_match('/^https?:\/\//i', $path)) return $path;
        $clean = ltrim($path, '/');
        return file_exists(public_path($clean)) ? asset($clean) : asset('assets/studybuddy-imgs/brand/logo-icon.png');
    };

    $assetExists = function ($path) {
        if (!$path || preg_match('/^https?:\/\//i', $path)) return false;
        return file_exists(public_path(ltrim($path, '/')));
    };

    $mainImage = $assetUrl($app->safeHeroImage());

    $galleryCandidates = [
        "assets/studybuddy-imgs/02_apps/{$app->slug}/01_app-icon/{$app->slug}_main-icon.png",
        "assets/studybuddy-imgs/02_apps/{$app->slug}/01_app-icon/{$app->slug}_icon-512.png",
        "assets/studybuddy-imgs/02_apps/{$app->slug}/02_orbs/{$app->slug}_orb-glow.png",
        "assets/studybuddy-imgs/02_apps/{$app->slug}/02_orbs/{$app->slug}_orb-small.png",
        "assets/studybuddy-imgs/02_apps/{$app->slug}/05_planets-bg/{$app->slug}_mini-planet.png",
    ];

    $gallery = collect($galleryCandidates)->filter(fn($path) => $assetExists($path))->map(fn($path) => $assetUrl($path))->unique()->take(4)->values();

    $rolesForApp = $app->audience_roles ?: ['student', 'parent', 'teacher', 'independent_learner'];

    $outcomes = collect($app->learning_outcomes ?: [])
        ->map(fn($x) => is_array($x) ? ($x['text'] ?? $x['title'] ?? implode(' ', $x)) : $x)
        ->filter()
        ->values();

    if (!$outcomes->count()) {
        $outcomes = collect(['Build confidence', 'Practice safely', 'Track progress', 'Keep learning fun']);
    }

    $sections = collect($app->detail_sections ?: [])
        ->map(fn($x) => is_array($x)
            ? ['title' => $x['title'] ?? 'Learning step', 'body' => $x['body'] ?? $x['text'] ?? $x['description'] ?? 'A focused learning step.']
            : ['title' => 'Learning step', 'body' => $x])
        ->filter()
        ->values();

    if (!$sections->count()) {
        $sections = collect([
            ['title' => 'Start Small', 'body' => 'Choose a short activity and begin with a tiny win.'],
            ['title' => 'Practice Gently', 'body' => 'Complete focused rounds with friendly feedback.'],
            ['title' => 'Grow Confident', 'body' => 'Review your progress and return when ready.'],
        ]);
    }

    $relatedImage = function ($mini) use ($assetUrl) {
        return $assetUrl($mini->safeHeroImage());
    };
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-connected-apps.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-connected-apps.css')) ? filemtime(public_path('assets/css/studybuddy-connected-apps.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-connected-apps.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-connected-apps.js')) ? filemtime(public_path('assets/js/studybuddy-connected-apps.js')) : time() }}" defer></script>

<main id="main-content" class="sb-app-detail-final" data-sb-app-detail style="--app-one: {{ $world[2] }}; --app-two: {{ $world[3] }}; --app-three: {{ $world[4] }};">
    <section class="sb-detail-hero-final">
        <div class="sb-detail-copy">
            <a class="back-to-apps" href="{{ route('studybuddy.apps') }}">← Back to App Universe</a>
            <p class="sb-apps-kicker">{{ $world[1] }}</p>
            <h1>{{ $app->name }}</h1>
            <p>{{ $app->description ?: $world[6] }}</p>

            <div class="detail-stat-row">
                <span>{{ ucfirst($app->status) }}</span>
                <span>⭐ {{ $app->points_reward }} points</span>
                <span>⏱ {{ $app->estimated_minutes }} minutes</span>
                <span>{{ $app->age_min ? $app->age_min.'+' : 'All ages' }}</span>
            </div>

            <div class="sb-apps-hero-actions">
                @if($app->is_web_enabled)
                    @auth
                        <a href="{{ route('studybuddy.final.web-play', $app->slug) }}">Start Playing</a>
                    @else
                        <a href="{{ route('login') }}">Login to Play</a>
                    @endauth
                @else
                    <a href="{{ route('studybuddy.apps') }}">Explore More Apps</a>
                @endif
                <a class="soft" href="{{ route('studybuddy.final.points-wallet') }}">Points Wallet</a>
            </div>
        </div>

        <aside class="detail-art-stage" data-magic-card>
            <div class="art-glow"></div>
            <img class="main-art" src="{{ $mainImage }}" alt="{{ $app->name }} artwork">
            <span class="app-symbol big">{{ $world[0] }}</span>
            <div class="generated-sparkles detail" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>

            @if($gallery->count())
                <div class="detail-gallery" aria-hidden="true">
                    @foreach($gallery as $image)
                        <img src="{{ $image }}" alt="">
                    @endforeach
                </div>
            @endif
        </aside>
    </section>

    <section class="detail-info-strip">
        <article><span>World</span><strong>{{ $world[1] }}</strong></article>
        <article><span>Best for</span><strong>{{ collect($rolesForApp)->map(fn($r) => ucwords(str_replace('_', ' ', $r)))->join(', ') }}</strong></article>
        <article><span>Experience</span><strong>Playful, calm, and progress-friendly</strong></article>
    </section>

    <section class="detail-section-final split">
        <div>
            <p class="sb-apps-kicker">What you’ll build</p>
            <h2>Small wins that turn into real confidence.</h2>
            <p>{{ $app->safety_note ?: 'StudyBuddy keeps practice friendly, clear, and easy to return to.' }}</p>
        </div>

        <div class="outcome-grid">
            @foreach($outcomes as $outcome)
                <article data-magic-card><span>✓</span><strong>{{ $outcome }}</strong></article>
            @endforeach
        </div>
    </section>

    <section class="detail-section-final">
        <div class="section-heading">
            <p class="sb-apps-kicker">Learning journey</p>
            <h2>How this world feels when you use it.</h2>
        </div>

        <div class="mission-grid">
            @foreach($sections as $section)
                <article data-magic-card>
                    <span>0{{ $loop->iteration }}</span>
                    <h3>{{ $section['title'] }}</h3>
                    <p>{{ $section['body'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="detail-section-final split">
        <div>
            <p class="sb-apps-kicker">Play options</p>
            <h2>Start where you are.</h2>
            <p>Open a quick web session now, then come back to your points and progress anytime.</p>
        </div>

        <div class="platform-choice-grid">
            @if($app->is_web_enabled)
                @auth
                    <a href="{{ route('studybuddy.final.web-play', $app->slug) }}">Web Play <span>→</span></a>
                @else
                    <a href="{{ route('login') }}">Login to Play <span>🔒</span></a>
                @endauth
            @else
                <span>Web Play soon</span>
            @endif

            <span>iOS soon</span>
            <span>Android soon</span>
            <span>Desktop soon</span>
        </div>
    </section>

    @if($related->count())
        <section class="detail-section-final">
            <div class="section-heading">
                <p class="sb-apps-kicker">Explore more</p>
                <h2>More learning worlds</h2>
            </div>

            <div class="related-worlds">
                @foreach($related as $mini)
                    <a href="{{ route('studybuddy.apps.show', $mini->slug) }}" data-magic-card>
                        <img src="{{ $relatedImage($mini) }}" alt="{{ $mini->name }} artwork">
                        <strong>{{ $mini->name }}</strong>
                        <span>{{ $mini->category }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</main>
@endsection
'''

web_play_view = r'''
@extends('layouts.app')

@section('content')
@php
    $assetUrl = function ($path) {
        if (!$path) return asset('assets/studybuddy-imgs/brand/logo-icon.png');
        if (preg_match('/^https?:\/\//i', $path)) return $path;
        $clean = ltrim($path, '/');
        return file_exists(public_path($clean)) ? asset($clean) : asset('assets/studybuddy-imgs/brand/logo-icon.png');
    };

    $worlds = [
        'math-quest' => ['✦', '#7c3cff', '#246bff', '#22d3ee'],
        'spelling-sprint' => ['Aa', '#ff4f9a', '#7c3cff', '#ffd166'],
        'reading-garden' => ['☘', '#16a34a', '#22c55e', '#22d3ee'],
        'focus-forest' => ['◌', '#0f766e', '#22c55e', '#22d3ee'],
        'planner-city' => ['▦', '#f59e0b', '#ef4444', '#7c3cff'],
        'quiz-galaxy' => ['◎', '#4f46e5', '#ec4899', '#22d3ee'],
        'shapes-lab' => ['△', '#06b6d4', '#8b5cf6', '#facc15'],
        'flashcard-castle' => ['▣', '#9333ea', '#f97316', '#fde68a'],
    ];

    $world = $worlds[$app->slug] ?? [$app->icon ?: '✨', $app->accent ?: '#7c3cff', '#246bff', '#22d3ee'];
    $image = $assetUrl($app->safeHeroImage());
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-connected-apps.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-connected-apps.css')) ? filemtime(public_path('assets/css/studybuddy-connected-apps.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-connected-apps.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-connected-apps.js')) ? filemtime(public_path('assets/js/studybuddy-connected-apps.js')) : time() }}" defer></script>

<main id="main-content" class="sb-app-play-final" style="--app-one: {{ $world[1] }}; --app-two: {{ $world[2] }}; --app-three: {{ $world[3] }};">
    <section class="play-stage" data-magic-card>
        <div class="play-copy">
            <a class="back-to-apps" href="{{ route('studybuddy.apps.show', $app->slug) }}">← Back to {{ $app->name }}</a>
            <p class="sb-apps-kicker">Web Play</p>
            <h1>{{ $app->name }}</h1>
            <p>{{ $canPlay ? 'Complete a quick demo session and collect your StudyBuddy points.' : 'Login to start this learning session and save your progress.' }}</p>

            <div class="detail-stat-row">
                <span>⭐ {{ $app->points_reward }} points</span>
                <span>⏱ {{ $app->estimated_minutes }} minutes</span>
                <span>{{ $app->age_min ? $app->age_min.'+' : 'All ages' }}</span>
            </div>

            @if($canPlay)
                <form method="POST" action="{{ route('studybuddy.final.session.complete') }}" class="play-form">
                    @csrf
                    <input type="hidden" name="app_slug" value="{{ $app->slug }}">
                    <button type="submit">Finish demo session</button>
                </form>
            @else
                <div class="sb-apps-hero-actions">
                    <a href="{{ route('login') }}">Login to Play</a>
                    <a class="soft" href="{{ route('register') }}">Create account</a>
                </div>
            @endif
        </div>

        <aside class="play-art">
            <div class="art-glow"></div>
            <img src="{{ $image }}" alt="{{ $app->name }} artwork">
            <span>{{ $world[0] }}</span>
            <div class="generated-sparkles detail" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
        </aside>
    </section>

    <section class="play-steps">
        <article><span>1</span><strong>Start small</strong><p>Open the session and focus on one tiny win.</p></article>
        <article><span>2</span><strong>Try calmly</strong><p>Practice without pressure. StudyBuddy is built for friendly progress.</p></article>
        <article><span>3</span><strong>Collect progress</strong><p>Finish the demo and return to your points wallet.</p></article>
    </section>
</main>
@endsection
'''

css = r'''
:root {
    --sb-bg: #050816;
    --sb-panel: rgba(255,255,255,.075);
    --sb-panel-strong: rgba(255,255,255,.105);
    --sb-line: rgba(255,255,255,.13);
    --sb-text: #f8fbff;
    --sb-muted: rgba(226,232,240,.72);
    --sb-soft: rgba(226,232,240,.55);
    --sb-cyan: #22d3ee;
    --sb-purple: #7c3cff;
    --sb-blue: #246bff;
}

.sb-apps-final,
.sb-app-detail-final,
.sb-app-play-final,
.sb-apps-final *,
.sb-app-detail-final *,
.sb-app-play-final * {
    box-sizing: border-box;
}

.sb-apps-final,
.sb-app-detail-final,
.sb-app-play-final {
    position: relative;
    isolation: isolate;
    min-height: 100vh;
    overflow: hidden;
    color: var(--sb-text);
    background:
        radial-gradient(circle at 8% 0%, rgba(34,211,238,.18), transparent 32%),
        radial-gradient(circle at 92% 6%, rgba(124,60,255,.24), transparent 36%),
        linear-gradient(180deg, #050816 0%, #081026 46%, #050816 100%);
    padding-bottom: clamp(54px, 8vw, 96px);
}

.sb-app-magic-layer {
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    overflow: hidden;
    opacity: .65;
    contain: strict;
}

.sb-app-magic-layer span {
    position: absolute;
    left: var(--x);
    top: var(--y);
    width: var(--s);
    height: var(--s);
    border-radius: 999px;
    background: radial-gradient(circle, #fff 0 18%, var(--app-three, #22d3ee) 34%, transparent 72%);
    box-shadow:
        0 0 14px color-mix(in srgb, var(--app-three, #22d3ee) 60%, transparent),
        0 0 24px color-mix(in srgb, var(--app-one, #7c3cff) 35%, transparent);
    animation: sbParticleFloat var(--d) ease-in-out infinite, sbParticleTwinkle calc(var(--d) * .7) ease-in-out infinite;
    animation-delay: var(--delay);
    opacity: var(--o);
}

.sb-app-magic-layer span:nth-child(4n) {
    width: calc(var(--s) * 2.2);
    height: 2px;
    border-radius: 999px;
    background: linear-gradient(90deg, transparent, var(--app-three, #22d3ee), #fff, transparent);
}

.sb-apps-final > *,
.sb-app-detail-final > *,
.sb-app-play-final > * {
    position: relative;
    z-index: 2;
}

.sb-apps-hero-final,
.sb-apps-controls,
.sb-apps-friendly-note,
.sb-apps-grid-final,
.sb-apps-empty,
.sb-detail-hero-final,
.detail-info-strip,
.detail-section-final,
.play-stage,
.play-steps {
    width: min(100% - 24px, 1180px);
    margin-inline: auto;
}

.sb-apps-hero-final,
.sb-detail-hero-final,
.play-stage {
    width: min(100% - 24px, 1220px);
    margin-top: clamp(12px, 2vw, 24px);
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(300px, .72fr);
    gap: clamp(26px, 5vw, 58px);
    align-items: center;
    border: 1px solid rgba(255,255,255,.11);
    border-radius: clamp(30px, 4vw, 48px);
    padding: clamp(32px, 6vw, 64px);
    background:
        radial-gradient(circle at var(--hero-x, 18%) var(--hero-y, 16%), color-mix(in srgb, var(--app-three, #22d3ee) 18%, transparent), transparent 30%),
        radial-gradient(circle at 82% 18%, color-mix(in srgb, var(--app-one, #7c3cff) 16%, transparent), transparent 32%),
        rgba(255,255,255,.04);
    box-shadow: 0 34px 100px rgba(2,6,23,.34);
    backdrop-filter: blur(18px);
    overflow: clip;
}

.sb-apps-hero-copy,
.sb-detail-copy,
.play-copy {
    min-width: 0;
}

.sb-apps-kicker {
    margin: 0 0 10px;
    color: var(--app-three, var(--sb-cyan));
    font-size: .78rem;
    font-weight: 950;
    letter-spacing: .13em;
    text-transform: uppercase;
}

.sb-apps-hero-final h1,
.sb-detail-hero-final h1,
.play-stage h1 {
    max-width: 780px;
    margin: 0;
    color: var(--sb-text);
    font-size: clamp(2.45rem, 7vw, 5.75rem);
    line-height: .88;
    letter-spacing: -.08em;
    text-wrap: balance;
    overflow-wrap: anywhere;
}

.sb-apps-hero-final p,
.sb-detail-copy p,
.play-copy p,
.detail-section-final p,
.sb-app-card-final p,
.play-steps p {
    color: var(--sb-muted);
    line-height: 1.72;
    overflow-wrap: anywhere;
}

.sb-apps-hero-actions,
.app-card-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 24px;
}

.sb-apps-hero-actions a,
.app-card-actions a,
.app-card-actions span,
.back-to-apps,
.play-form button {
    position: relative;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    border: 1px solid rgba(255,255,255,.13);
    border-radius: 999px;
    padding: 11px 16px;
    color: #fff;
    background: linear-gradient(135deg, var(--app-one, var(--sb-purple)), var(--app-two, var(--sb-blue)));
    text-decoration: none;
    font: inherit;
    font-weight: 950;
    box-shadow: 0 18px 42px rgba(2,6,23,.28);
    cursor: pointer;
    transition: transform .22s ease, filter .22s ease, box-shadow .22s ease;
}

.sb-apps-hero-actions .soft,
.app-card-actions .soft,
.back-to-apps {
    color: var(--sb-text);
    background: rgba(255,255,255,.085);
}

.sb-apps-hero-actions a:hover,
.app-card-actions a:hover,
.back-to-apps:hover,
.play-form button:hover {
    transform: translateY(-3px);
    filter: brightness(1.08);
    box-shadow: 0 24px 58px color-mix(in srgb, var(--app-one, #7c3cff) 30%, transparent);
}

.sb-apps-hero-card,
.detail-art-stage,
.play-art {
    position: relative;
    min-width: 0;
    min-height: 280px;
    display: grid;
    place-items: center;
    align-content: center;
    gap: 8px;
    border: 1px solid rgba(255,255,255,.13);
    border-radius: 36px;
    padding: clamp(24px, 4vw, 38px);
    background:
        radial-gradient(circle at var(--mx, 50%) var(--my, 20%), color-mix(in srgb, var(--app-three, #22d3ee) 18%, transparent), transparent 36%),
        rgba(255,255,255,.075);
    box-shadow: 0 30px 90px rgba(2,6,23,.34);
    backdrop-filter: blur(18px);
    overflow: clip;
    transform-style: preserve-3d;
}

.sb-apps-hero-card strong {
    color: white;
    font-size: 4rem;
    line-height: .9;
}

.hero-orbit {
    font-size: 3rem;
    animation: sbFloat 5s ease-in-out infinite;
}

.hero-dots,
.generated-sparkles {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.hero-dots i,
.generated-sparkles i {
    position: absolute;
    width: 7px;
    height: 7px;
    border-radius: 999px;
    background: radial-gradient(circle, #fff 0 22%, var(--app-three, #22d3ee) 36%, transparent 74%);
    box-shadow: 0 0 14px color-mix(in srgb, var(--app-three, #22d3ee) 70%, transparent);
    animation: sbSpark 3.8s ease-in-out infinite;
}

.hero-dots i:nth-child(1), .generated-sparkles i:nth-child(1) { left: 18%; top: 20%; animation-delay: -.2s; }
.hero-dots i:nth-child(2), .generated-sparkles i:nth-child(2) { right: 18%; top: 24%; width: 10px; height: 10px; animation-delay: -.9s; }
.hero-dots i:nth-child(3), .generated-sparkles i:nth-child(3) { left: 14%; bottom: 30%; width: 5px; height: 5px; animation-delay: -1.4s; }
.hero-dots i:nth-child(4), .generated-sparkles i:nth-child(4) { right: 12%; bottom: 24%; animation-delay: -2s; }
.hero-dots i:nth-child(5), .generated-sparkles i:nth-child(5) { left: 48%; top: 12%; width: 6px; height: 6px; animation-delay: -2.8s; }
.generated-sparkles i:nth-child(6) { left: 38%; bottom: 12%; width: 9px; height: 9px; animation-delay: -3.1s; }
.generated-sparkles i:nth-child(7) { right: 42%; bottom: 16%; width: 5px; height: 5px; animation-delay: -3.6s; }

.sb-apps-controls {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(160px, .55fr) minmax(160px, .55fr);
    gap: 14px;
    align-items: end;
    margin-top: clamp(22px, 4vw, 42px);
    margin-bottom: 16px;
}

.sb-apps-controls label {
    display: grid;
    gap: 7px;
    min-width: 0;
    color: var(--sb-soft);
    font-size: .78rem;
    font-weight: 950;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.sb-apps-controls input,
.sb-apps-controls select {
    width: 100%;
    min-width: 0;
    height: 54px;
    border: 1px solid rgba(255,255,255,.13);
    border-radius: 20px;
    padding: 0 14px;
    color: var(--sb-text);
    background: rgba(255,255,255,.075);
    box-shadow: 0 18px 44px rgba(2,6,23,.22);
    font: inherit;
    font-weight: 850;
    outline: none;
}

.sb-apps-controls input::placeholder {
    color: rgba(226,232,240,.48);
}

.sb-apps-controls input:focus,
.sb-apps-controls select:focus {
    border-color: color-mix(in srgb, var(--app-three, #22d3ee) 52%, white);
    box-shadow: 0 0 0 4px rgba(34,211,238,.12), 0 18px 44px rgba(2,6,23,.22);
}

.sb-apps-controls select option {
    color: #07111f;
    background: #fff;
}

.sb-apps-friendly-note,
.sb-apps-empty {
    border: 1px solid rgba(255,255,255,.11);
    border-radius: 24px;
    padding: 16px 18px;
    margin-bottom: clamp(20px, 3vw, 30px);
    color: var(--sb-muted);
    background: rgba(255,255,255,.06);
    box-shadow: 0 18px 44px rgba(2,6,23,.22);
}

.sb-apps-friendly-note strong,
.sb-apps-empty h2 {
    color: white;
}

.sb-apps-grid-final {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: clamp(18px, 2.4vw, 26px);
    align-items: stretch;
}

.sb-app-card-final {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    min-height: 100%;
    overflow: clip;
    border: 1px solid rgba(255,255,255,.115);
    border-radius: 34px;
    background:
        radial-gradient(circle at var(--mx, 50%) var(--my, 0%), color-mix(in srgb, var(--app-three) 20%, transparent), transparent 34%),
        linear-gradient(180deg, rgba(255,255,255,.095), rgba(255,255,255,.055));
    box-shadow: 0 24px 72px rgba(2,6,23,.30);
    backdrop-filter: blur(18px);
    contain: layout paint;
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, background .22s ease;
}

.sb-app-card-final:hover {
    transform: translateY(-8px) scale(1.008);
    border-color: color-mix(in srgb, var(--app-three) 42%, white);
    box-shadow: 0 34px 92px rgba(2,6,23,.42);
}

.sb-app-card-art {
    position: relative;
    min-height: 238px;
    aspect-ratio: 16 / 10;
    display: grid;
    place-items: center;
    padding: clamp(22px, 3vw, 30px);
    overflow: hidden;
    background:
        radial-gradient(circle at 50% 46%, color-mix(in srgb, var(--app-three) 32%, transparent), transparent 45%),
        radial-gradient(circle at 15% 0%, color-mix(in srgb, var(--app-one) 18%, transparent), transparent 36%),
        linear-gradient(135deg, rgba(255,255,255,.08), rgba(255,255,255,.035));
}

.sb-app-card-art::before {
    content: "";
    position: absolute;
    inset: 18px;
    border-radius: 999px;
    background: conic-gradient(from 0deg, transparent, color-mix(in srgb, var(--app-three) 32%, transparent), transparent, color-mix(in srgb, var(--app-one) 22%, transparent), transparent);
    filter: blur(4px);
    opacity: .36;
    animation: sbSpin 11s linear infinite;
}

.sb-app-card-art::after {
    content: "";
    position: absolute;
    inset: auto 24px 18px;
    height: 16px;
    border-radius: 50%;
    background: rgba(0,0,0,.34);
    filter: blur(14px);
}

.sb-app-card-art .main-art {
    position: relative;
    z-index: 2;
    width: min(78%, 220px);
    height: 200px;
    max-height: 76%;
    object-fit: contain;
    filter: drop-shadow(0 24px 30px rgba(0,0,0,.32));
    transform-origin: center;
    animation: sbLogoHover 5.2s ease-in-out infinite;
    transition: transform .25s ease;
}

.sb-app-card-final:hover .main-art {
    transform: translateY(-7px) scale(1.045) rotate(-1deg);
}

.app-symbol {
    position: absolute;
    right: 18px;
    top: 18px;
    z-index: 3;
    min-width: 48px;
    height: 48px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(255,255,255,.16);
    border-radius: 18px;
    color: white;
    background: linear-gradient(135deg, var(--app-one), var(--app-two));
    font-weight: 950;
    box-shadow: 0 16px 34px rgba(2,6,23,.28);
}

.app-symbol.big {
    width: 58px;
    height: 58px;
    border-radius: 22px;
}

.mini-art-row {
    position: absolute;
    left: 16px;
    bottom: 16px;
    z-index: 5;
    display: flex;
    gap: 8px;
    align-items: center;
    padding: 8px;
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 999px;
    background: rgba(5,8,22,.45);
    backdrop-filter: blur(14px);
    box-shadow: 0 14px 34px rgba(2,6,23,.30);
}

.mini-art-row img {
    width: 38px;
    height: 38px;
    object-fit: contain;
    border-radius: 14px;
    padding: 4px;
    background: rgba(255,255,255,.10);
    filter: drop-shadow(0 8px 14px rgba(0,0,0,.28));
    animation: sbMiniFloat 4.8s ease-in-out infinite;
}

.mini-art-row img:nth-child(2) { animation-delay: -1.1s; }
.mini-art-row img:nth-child(3) { animation-delay: -2.2s; }

.sb-app-card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 22px;
}

.sb-app-card-topline,
.role-pills,
.app-mini-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.sb-app-card-topline {
    min-height: 32px;
    justify-content: space-between;
    align-items: center;
    color: var(--app-three);
    font-size: .76rem;
    font-weight: 950;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.sb-app-card-topline b,
.role-pills span,
.app-mini-stats span,
.detail-stat-row span {
    border: 1px solid rgba(255,255,255,.09);
    border-radius: 999px;
    padding: 7px 9px;
    background: rgba(255,255,255,.075);
    color: rgba(226,232,240,.78);
    font-size: .75rem;
    font-weight: 900;
}

.sb-app-card-final h2 {
    min-height: 1.95em;
    margin: 16px 0 8px;
    color: white;
    font-size: 1.65rem;
    line-height: .98;
    letter-spacing: -.04em;
    text-wrap: balance;
    overflow-wrap: anywhere;
}

.sb-app-card-final .tagline {
    min-height: 2.6em;
    margin: 0 0 8px;
    color: white;
    font-weight: 950;
    line-height: 1.3;
}

.sb-app-card-final p:not(.tagline) {
    min-height: 5.1em;
    margin-bottom: 0;
}

.role-pills,
.app-mini-stats {
    min-height: 34px;
    margin-top: 14px;
}

.app-card-actions {
    margin-top: auto;
    padding-top: 18px;
}

.app-card-actions a,
.app-card-actions span {
    min-width: 0;
    flex: 1 1 135px;
}

.sb-app-card-final[hidden],
.sb-apps-empty[hidden] {
    display: none !important;
}

/* Detail + play pages */
.back-to-apps {
    margin-bottom: 18px;
}

.detail-art-stage,
.play-art {
    min-height: clamp(360px, 40vw, 510px);
}

.detail-art-stage .main-art,
.play-art img {
    position: relative;
    z-index: 2;
    width: min(82%, 410px);
    height: clamp(250px, 32vw, 390px);
    object-fit: contain;
    filter: drop-shadow(0 30px 42px rgba(0,0,0,.38));
    transition: transform .28s ease, filter .28s ease;
    animation: sbLogoHover 5.2s ease-in-out infinite;
}

.detail-art-stage:hover .main-art,
.play-art:hover img {
    transform: translateY(-8px) scale(1.035);
}

.art-glow {
    position: absolute;
    inset: 12%;
    border-radius: 50%;
    background: radial-gradient(circle, color-mix(in srgb, var(--app-three) 36%, transparent), transparent 64%);
    filter: blur(28px);
    opacity: .72;
}

.detail-gallery {
    position: absolute;
    left: 18px;
    right: 18px;
    bottom: 18px;
    z-index: 6;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
    padding: 10px;
    border: 1px solid rgba(255,255,255,.13);
    border-radius: 24px;
    background: rgba(5,8,22,.48);
    backdrop-filter: blur(16px);
    box-shadow: 0 18px 44px rgba(2,6,23,.34);
}

.detail-gallery img {
    width: 100%;
    height: 58px;
    object-fit: contain;
    border-radius: 18px;
    padding: 6px;
    background: rgba(255,255,255,.09);
    filter: drop-shadow(0 10px 16px rgba(0,0,0,.30));
    animation: sbMiniFloat 5.2s ease-in-out infinite;
}

.detail-gallery img:nth-child(2) { animation-delay: -1s; }
.detail-gallery img:nth-child(3) { animation-delay: -2s; }
.detail-gallery img:nth-child(4) { animation-delay: -3s; }

.detail-stat-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 20px;
}

.detail-info-strip,
.play-steps {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: clamp(14px, 2vw, 22px);
    margin-top: clamp(22px, 3vw, 36px);
    margin-bottom: clamp(22px, 3vw, 34px);
}

.detail-info-strip article,
.detail-section-final,
.play-steps article {
    border: 1px solid rgba(255,255,255,.11);
    border-radius: 28px;
    background:
        radial-gradient(circle at 100% 0%, color-mix(in srgb, var(--app-three) 10%, transparent), transparent 32%),
        rgba(255,255,255,.065);
    box-shadow: 0 24px 72px rgba(2,6,23,.28);
    backdrop-filter: blur(18px);
}

.detail-info-strip article,
.play-steps article {
    min-width: 0;
    min-height: 118px;
    padding: 18px;
}

.detail-info-strip span,
.play-steps span {
    color: var(--sb-soft);
    font-size: .78rem;
    font-weight: 950;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.detail-info-strip strong,
.play-steps strong {
    display: block;
    margin-top: 7px;
    color: white;
    overflow-wrap: anywhere;
}

.detail-section-final {
    margin-bottom: clamp(20px, 3vw, 30px);
    padding: clamp(24px, 4vw, 42px);
    overflow: clip;
}

.detail-section-final.split {
    display: grid;
    grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr);
    gap: clamp(20px, 4vw, 40px);
    align-items: stretch;
}

.detail-section-final h2 {
    margin: 0;
    color: white;
    font-size: clamp(1.8rem, 4vw, 3.1rem);
    line-height: .95;
    letter-spacing: -.06em;
    text-wrap: balance;
}

.outcome-grid,
.mission-grid,
.platform-choice-grid,
.related-worlds {
    display: grid;
    gap: clamp(14px, 2vw, 22px);
}

.outcome-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.mission-grid,
.related-worlds {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.platform-choice-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.outcome-grid article,
.mission-grid article,
.platform-choice-grid a,
.platform-choice-grid span,
.related-worlds a {
    min-width: 0;
    border: 1px solid rgba(255,255,255,.10);
    border-radius: 24px;
    padding: 18px;
    background:
        radial-gradient(circle at var(--mx, 50%) var(--my, 0%), color-mix(in srgb, var(--app-three) 16%, transparent), transparent 36%),
        rgba(255,255,255,.07);
    color: white;
    text-decoration: none;
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, background .22s ease;
}

.outcome-grid article:hover,
.mission-grid article:hover,
.platform-choice-grid a:hover,
.related-worlds a:hover {
    transform: translateY(-6px);
    border-color: color-mix(in srgb, var(--app-three) 36%, white);
    background: rgba(255,255,255,.10);
    box-shadow: 0 24px 60px rgba(2,6,23,.34);
}

.outcome-grid article {
    display: flex;
    gap: 12px;
    align-items: center;
}

.outcome-grid article span {
    flex: 0 0 auto;
    width: 34px;
    height: 34px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    color: white;
    background: linear-gradient(135deg, var(--app-one), var(--app-two));
    font-weight: 950;
}

.mission-grid article {
    min-height: 210px;
}

.mission-grid article > span {
    color: var(--app-three);
    font-weight: 950;
}

.related-worlds a {
    display: flex;
    flex-direction: column;
    min-height: 270px;
}

.related-worlds img {
    width: 100%;
    height: 150px;
    object-fit: contain;
    margin-bottom: 10px;
    filter: drop-shadow(0 20px 28px rgba(0,0,0,.34));
}

.related-worlds span {
    color: var(--sb-muted);
    margin-top: 4px;
}

.play-stage {
    min-height: clamp(480px, 60vw, 620px);
}

.play-art {
    min-height: 420px;
}

.play-art > span {
    position: absolute;
    top: 22px;
    right: 22px;
    z-index: 3;
    min-width: 58px;
    height: 58px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(255,255,255,.16);
    border-radius: 22px;
    color: white;
    background: linear-gradient(135deg, var(--app-one), var(--app-two));
    font-weight: 950;
}

.play-form {
    margin-top: 24px;
}

.play-steps {
    margin-bottom: 0;
}

.play-steps span {
    display: grid;
    width: 40px;
    height: 40px;
    place-items: center;
    border-radius: 16px;
    color: white;
    background: linear-gradient(135deg, var(--app-one), var(--app-two));
}

/* Safety */
.sb-app-card-final,
.detail-section-final,
.detail-info-strip article,
.sb-apps-friendly-note,
.sb-apps-empty,
.sb-apps-hero-card,
.detail-art-stage,
.play-stage,
.play-steps article {
    min-width: 0;
}

.sb-app-card-final h2,
.sb-app-card-final p,
.sb-app-card-final span,
.sb-app-card-final strong,
.detail-section-final h2,
.detail-section-final h3,
.detail-section-final p,
.detail-info-strip strong,
.related-worlds strong,
.related-worlds span,
.play-stage h1,
.play-stage p {
    overflow-wrap: anywhere;
}

.sb-app-card-final img,
.detail-art-stage img,
.related-worlds img,
.play-art img {
    max-width: 100%;
}

/* Focus */
.sb-app-card-final a:focus-visible,
.sb-apps-hero-actions a:focus-visible,
.back-to-apps:focus-visible,
.sb-apps-controls input:focus-visible,
.sb-apps-controls select:focus-visible,
.related-worlds a:focus-visible,
.play-form button:focus-visible {
    outline: 3px solid color-mix(in srgb, var(--app-three, #22d3ee) 70%, white);
    outline-offset: 4px;
}

/* Animations */
@keyframes sbParticleFloat {
    0%, 100% { transform: translate3d(0,0,0) scale(1); }
    50% { transform: translate3d(var(--dx), var(--dy), 0) scale(1.25); }
}

@keyframes sbParticleTwinkle {
    0%, 100% { opacity: calc(var(--o) * .45); }
    50% { opacity: var(--o); }
}

@keyframes sbSpark {
    0%, 100% { opacity: .28; transform: translateY(0) scale(.75); }
    45% { opacity: 1; transform: translateY(-9px) scale(1.22); }
    70% { opacity: .62; transform: translateY(3px) scale(.96); }
}

@keyframes sbFloat {
    0%, 100% { transform: translateY(0) rotate(-4deg); }
    50% { transform: translateY(-10px) rotate(5deg); }
}

@keyframes sbSpin {
    to { transform: rotate(360deg); }
}

@keyframes sbLogoHover {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-8px) scale(1.025); }
}

@keyframes sbMiniFloat {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-7px) rotate(3deg); }
}

/* Responsive */
@media (max-width: 1120px) {
    .sb-apps-grid-final,
    .mission-grid,
    .related-worlds {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sb-apps-hero-final,
    .sb-detail-hero-final,
    .play-stage {
        grid-template-columns: minmax(0, 1fr);
    }

    .sb-apps-hero-card,
    .detail-art-stage,
    .play-art {
        max-width: 640px;
        width: 100%;
        justify-self: center;
    }
}

@media (max-width: 760px) {
    .sb-apps-hero-final,
    .sb-detail-hero-final,
    .play-stage {
        width: min(100% - 16px, 1220px);
        border-radius: 28px;
        padding: 30px 18px;
    }

    .sb-apps-controls,
    .sb-apps-friendly-note,
    .sb-apps-grid-final,
    .sb-apps-empty,
    .detail-info-strip,
    .detail-section-final,
    .play-steps {
        width: min(100% - 16px, 1180px);
    }

    .sb-apps-controls,
    .sb-apps-grid-final,
    .detail-info-strip,
    .outcome-grid,
    .mission-grid,
    .platform-choice-grid,
    .related-worlds,
    .play-steps {
        grid-template-columns: 1fr;
    }

    .sb-app-card-art {
        min-height: 220px;
    }

    .sb-app-card-final h2,
    .sb-app-card-final .tagline,
    .sb-app-card-final p:not(.tagline) {
        min-height: 0;
    }

    .app-card-actions,
    .sb-apps-hero-actions {
        display: grid;
    }

    .app-card-actions a,
    .app-card-actions span,
    .sb-apps-hero-actions a {
        width: 100%;
    }

    .detail-section-final.split {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 460px) {
    .sb-app-card-body {
        padding: 18px;
    }

    .sb-app-card-art {
        min-height: 190px;
    }

    .sb-app-card-art .main-art {
        height: 170px;
    }

    .mini-art-row img:nth-child(3),
    .detail-gallery img:nth-child(4) {
        display: none;
    }

    .detail-art-stage,
    .play-art {
        min-height: 310px;
    }

    .detail-art-stage .main-art,
    .play-art img {
        height: 230px;
    }

    .detail-gallery {
        left: 12px;
        right: 12px;
        bottom: 12px;
    }

    .detail-gallery img {
        height: 48px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sb-app-magic-layer,
    .generated-sparkles i,
    .hero-orbit,
    .sb-app-card-art::before,
    .sb-app-card-art .main-art,
    .mini-art-row img,
    .detail-art-stage .main-art,
    .detail-gallery img,
    .play-art img {
        animation: none !important;
    }

    .sb-app-magic-layer {
        display: none !important;
    }

    .sb-app-card-final:hover,
    .sb-app-card-final:hover .main-art,
    .detail-art-stage:hover .main-art,
    .play-art:hover img {
        transform: none !important;
    }
}
'''

js = r'''
(() => {
    const page = document.querySelector('.sb-apps-final, .sb-app-detail-final, .sb-app-play-final');
    if (!page) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('body > .sb-life-layer').forEach((el) => el.remove());

    if (!reduceMotion && !page.querySelector('.sb-app-magic-layer')) {
        const layer = document.createElement('div');
        layer.className = 'sb-app-magic-layer';
        layer.setAttribute('aria-hidden', 'true');

        const styles = getComputedStyle(page);
        const one = styles.getPropertyValue('--app-one').trim() || '#7c3cff';
        const two = styles.getPropertyValue('--app-two').trim() || '#246bff';
        const three = styles.getPropertyValue('--app-three').trim() || '#22d3ee';

        const count = window.innerWidth < 640 ? 24 : 54;

        for (let i = 0; i < count; i++) {
            const dot = document.createElement('span');
            const size = Math.random() * 6 + 2.5;
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            const dx = (Math.random() * 80 - 40).toFixed(1);
            const dy = (Math.random() * 80 - 40).toFixed(1);
            const duration = Math.random() * 8 + 7;
            const delay = Math.random() * -10;
            const opacity = Math.random() * .34 + .24;

            dot.style.setProperty('--x', `${x}%`);
            dot.style.setProperty('--y', `${y}%`);
            dot.style.setProperty('--s', `${size}px`);
            dot.style.setProperty('--dx', `${dx}px`);
            dot.style.setProperty('--dy', `${dy}px`);
            dot.style.setProperty('--d', `${duration}s`);
            dot.style.setProperty('--delay', `${delay}s`);
            dot.style.setProperty('--o', opacity.toFixed(2));
            dot.style.setProperty('--app-one', one);
            dot.style.setProperty('--app-two', two);
            dot.style.setProperty('--app-three', three);

            layer.appendChild(dot);
        }

        page.prepend(layer);
    }

    const cards = Array.from(document.querySelectorAll('[data-app-card]'));
    const search = document.querySelector('[data-sb-app-search]');
    const category = document.querySelector('[data-sb-app-filter]');
    const role = document.querySelector('[data-sb-role-filter]');
    const empty = document.querySelector('[data-sb-empty]');

    const applyFilters = () => {
        const q = (search?.value || '').trim().toLowerCase();
        const cat = category?.value || 'all';
        const selectedRole = role?.value || 'all';
        let visible = 0;

        cards.forEach((card) => {
            const matchesSearch = !q || (card.dataset.search || '').includes(q);
            const matchesCategory = cat === 'all' || card.dataset.category === cat;
            const roles = (card.dataset.roles || '').split(' ');
            const matchesRole = selectedRole === 'all' || roles.includes(selectedRole);
            const show = matchesSearch && matchesCategory && matchesRole;

            card.hidden = !show;
            if (show) visible++;
        });

        if (empty) empty.hidden = visible !== 0;
    };

    [search, category, role].forEach((el) => {
        if (!el) return;
        el.addEventListener('input', applyFilters);
        el.addEventListener('change', applyFilters);
    });

    applyFilters();

    document.querySelectorAll('[data-magic-card], .sb-apps-hero-final, .sb-detail-hero-final, .play-stage').forEach((el) => {
        el.addEventListener('pointermove', (event) => {
            const rect = el.getBoundingClientRect();
            el.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            el.style.setProperty('--my', `${event.clientY - rect.top}px`);
            el.style.setProperty('--hero-x', `${((event.clientX - rect.left) / rect.width * 100).toFixed(1)}%`);
            el.style.setProperty('--hero-y', `${((event.clientY - rect.top) / rect.height * 100).toFixed(1)}%`);
        });
    });

    document.querySelectorAll('.detail-art-stage, .play-art').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            if (reduceMotion) return;
            const rect = card.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - .5;
            const y = (event.clientY - rect.top) / rect.height - .5;
            card.style.transform = `translateY(-4px) rotateX(${(-y * 5).toFixed(2)}deg) rotateY(${(x * 5).toFixed(2)}deg)`;
        });

        card.addEventListener('pointerleave', () => {
            card.style.transform = '';
        });
    });
})();
'''

db_php = r'''<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$table = 'studybuddy_mini_app_platforms';

if (!Schema::hasTable($table)) {
    echo "Missing table: {$table}\n";
    exit(1);
}

function has_col(string $table, string $column): bool {
    return Schema::hasColumn($table, $column);
}

function pick_asset(array $paths): string {
    foreach ($paths as $path) {
        if (!$path) continue;
        $clean = ltrim($path, '/');
        if (file_exists(public_path($clean))) return $clean;
    }
    return 'assets/studybuddy-imgs/brand/logo-icon.png';
}

function payload_for(string $table, array $payload): array {
    $out = [];
    foreach ($payload as $key => $value) {
        if (!has_col($table, $key)) continue;
        $out[$key] = is_array($value) ? json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $value;
    }
    if (has_col($table, 'updated_at')) $out['updated_at'] = now();
    return $out;
}

$apps = [
    'math-quest' => [
        'name' => 'Math Quest',
        'category' => 'Math Adventure',
        'tagline' => 'Numbers become glowing missions.',
        'description' => 'Practice arithmetic, patterns, and logic through calm quest rounds, retry loops, and confidence-building feedback.',
        'preview_text' => 'Turn math practice into a cosmic mission with tiny wins and clear feedback.',
        'icon' => '✦',
        'accent' => '#7c3cff',
        'points_reward' => 120,
        'estimated_minutes' => 8,
        'age_min' => 7,
        'roles' => ['student', 'parent', 'teacher', 'independent_learner'],
        'tags' => ['math', 'mental math', 'logic', 'confidence'],
        'outcomes' => ['Mental math confidence', 'Step-by-step problem solving', 'Fast recall without panic', 'Pattern spotting and logic'],
        'sections' => [
            ['title' => 'Warm-up Sparks', 'body' => 'Start with tiny number questions that wake up your brain without pressure.'],
            ['title' => 'Quest Rounds', 'body' => 'Practice focused skills through short missions, points, and feedback.'],
            ['title' => 'Boss Review', 'body' => 'Finish with a mixed challenge that reviews mistakes and celebrates progress.'],
        ],
    ],
    'spelling-sprint' => [
        'name' => 'Spelling Sprint',
        'category' => 'Language Sprint',
        'tagline' => 'Words, speed, memory, confidence.',
        'description' => 'Build spelling fluency through short word rounds, pattern recognition, retry practice, and friendly feedback.',
        'preview_text' => 'Make spelling feel fast, friendly, and way less scary.',
        'icon' => 'Aa',
        'accent' => '#ff4f9a',
        'points_reward' => 100,
        'estimated_minutes' => 7,
        'age_min' => 6,
        'roles' => ['student', 'parent', 'teacher'],
        'tags' => ['spelling', 'words', 'vocabulary', 'memory'],
        'outcomes' => ['Word pattern recognition', 'Vocabulary confidence', 'Spelling accuracy', 'Quick recall'],
        'sections' => [
            ['title' => 'Word Warm-up', 'body' => 'Break tricky words into smaller pieces so they feel easier.'],
            ['title' => 'Sprint Round', 'body' => 'Practice a focused word list with energy, speed, and no shame.'],
            ['title' => 'Mistake Replay', 'body' => 'Retry the words that need more love until they stick.'],
        ],
    ],
    'reading-garden' => [
        'name' => 'Reading Garden',
        'category' => 'Reading Growth',
        'tagline' => 'Grow stories into confidence.',
        'description' => 'Create a calm reading space with story goals, vocabulary blooms, reflection prompts, and progress growth.',
        'preview_text' => 'Grow reading fluency one calm story at a time.',
        'icon' => '☘',
        'accent' => '#16a34a',
        'points_reward' => 110,
        'estimated_minutes' => 12,
        'age_min' => 7,
        'roles' => ['student', 'parent', 'teacher', 'independent_learner'],
        'tags' => ['reading', 'stories', 'vocabulary', 'comprehension'],
        'outcomes' => ['Reading fluency', 'Vocabulary growth', 'Comprehension', 'Reflection skills'],
        'sections' => [
            ['title' => 'Story Seed', 'body' => 'Start with a short reading goal and a calm focus moment.'],
            ['title' => 'Vocabulary Bloom', 'body' => 'Collect useful words and understand them in context.'],
            ['title' => 'Reflection Patch', 'body' => 'Answer simple prompts to check understanding and grow confidence.'],
        ],
    ],
    'focus-forest' => [
        'name' => 'Focus Forest',
        'category' => 'Study Routine',
        'tagline' => 'Calm focus, gentle routines.',
        'description' => 'Build focus habits with gentle timers, mindful breaks, streaks, and routines that reduce overwhelm.',
        'preview_text' => 'Build focus without making studying feel heavy.',
        'icon' => '◌',
        'accent' => '#0f766e',
        'points_reward' => 90,
        'estimated_minutes' => 15,
        'age_min' => 8,
        'roles' => ['student', 'parent', 'teacher', 'independent_learner'],
        'tags' => ['focus', 'timer', 'study routine', 'calm'],
        'outcomes' => ['Attention habits', 'Study consistency', 'Break routines', 'Less overwhelm'],
        'sections' => [
            ['title' => 'Plant a Focus Tree', 'body' => 'Pick a task and begin a short focus timer.'],
            ['title' => 'Protect the Session', 'body' => 'Stay with one task while your focus world grows.'],
            ['title' => 'Mindful Break', 'body' => 'Pause, breathe, reset, and come back stronger.'],
        ],
    ],
    'planner-city' => [
        'name' => 'Planner City',
        'category' => 'Planning System',
        'tagline' => 'Turn tasks into a city map.',
        'description' => 'Organize homework, revision, goals, and routines into clear tiny steps that are easier to follow.',
        'preview_text' => 'Turn messy tasks into a simple map you can actually follow.',
        'icon' => '▦',
        'accent' => '#f59e0b',
        'points_reward' => 80,
        'estimated_minutes' => 6,
        'age_min' => 9,
        'roles' => ['student', 'parent', 'teacher', 'independent_learner'],
        'tags' => ['planning', 'tasks', 'routine', 'goals'],
        'outcomes' => ['Task planning', 'Prioritization', 'Routine building', 'Goal tracking'],
        'sections' => [
            ['title' => 'Build Today’s Map', 'body' => 'Turn all your tasks into a clear route for the day.'],
            ['title' => 'Priority Blocks', 'body' => 'Choose what matters first and avoid overwhelm.'],
            ['title' => 'Progress Streets', 'body' => 'Check things off and keep moving through your plan.'],
        ],
    ],
    'quiz-galaxy' => [
        'name' => 'Quiz Galaxy',
        'category' => 'Quiz Universe',
        'tagline' => 'Review topics across the galaxy.',
        'description' => 'Make revision active with short quizzes, instant feedback, smart retry loops, and reward points.',
        'preview_text' => 'Launch quick quizzes and retry missed questions until they feel easy.',
        'icon' => '◎',
        'accent' => '#4f46e5',
        'points_reward' => 120,
        'estimated_minutes' => 10,
        'age_min' => 8,
        'roles' => ['student', 'teacher', 'independent_learner'],
        'tags' => ['quiz', 'revision', 'memory', 'exam practice'],
        'outcomes' => ['Memory recall', 'Exam practice', 'Topic review', 'Confidence under questions'],
        'sections' => [
            ['title' => 'Launch Pad', 'body' => 'Pick a topic and start with a tiny question set.'],
            ['title' => 'Star Questions', 'body' => 'Answer mixed questions with quick feedback.'],
            ['title' => 'Retry Orbit', 'body' => 'Revisit missed questions until they become easy.'],
        ],
    ],
    'shapes-lab' => [
        'name' => 'Shapes Lab',
        'category' => 'STEM Lab',
        'tagline' => 'Geometry for visual thinkers.',
        'description' => 'Build visual problem-solving through shape sorting, geometry basics, patterns, and playful STEM challenges.',
        'preview_text' => 'Explore shapes, patterns, and visual problem solving.',
        'icon' => '△',
        'accent' => '#06b6d4',
        'points_reward' => 80,
        'estimated_minutes' => 8,
        'age_min' => 6,
        'roles' => ['student', 'parent', 'teacher'],
        'tags' => ['geometry', 'patterns', 'visual thinking', 'STEM'],
        'outcomes' => ['Geometry basics', 'Pattern recognition', 'Spatial reasoning', 'Visual confidence'],
        'sections' => [
            ['title' => 'Shape Sort', 'body' => 'Group shapes by sides, corners, and properties.'],
            ['title' => 'Pattern Machine', 'body' => 'Spot what comes next and explain the rule.'],
            ['title' => 'Build Challenge', 'body' => 'Use shapes to solve visual puzzles.'],
        ],
    ],
    'flashcard-castle' => [
        'name' => 'Flashcard Castle',
        'category' => 'Memory Castle',
        'tagline' => 'Protect knowledge with recall.',
        'description' => 'Build decks, practice active recall, and review facts through short memory rounds.',
        'preview_text' => 'Protect your knowledge inside a memory castle.',
        'icon' => '▣',
        'accent' => '#9333ea',
        'points_reward' => 90,
        'estimated_minutes' => 7,
        'age_min' => 8,
        'roles' => ['student', 'teacher', 'independent_learner'],
        'tags' => ['flashcards', 'memory', 'active recall', 'review'],
        'outcomes' => ['Active recall', 'Vocabulary memory', 'Exam facts', 'Spaced practice habits'],
        'sections' => [
            ['title' => 'Build a Deck', 'body' => 'Create cards for facts, words, definitions, or formulas.'],
            ['title' => 'Castle Recall', 'body' => 'Practice cards and mark what feels strong or tricky.'],
            ['title' => 'Treasure Review', 'body' => 'Return to missed cards and lock in memory.'],
        ],
    ],
];

$sort = 10;

foreach ($apps as $slug => $data) {
    $hero = pick_asset([
        "assets/studybuddy-imgs/apps/app-{$slug}.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/01_app-icon/{$slug}_main-icon.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/01_app-icon/{$slug}_icon-512.png",
    ]);

    $payload = [
        'name' => $data['name'],
        'category' => $data['category'],
        'tagline' => $data['tagline'],
        'description' => $data['description'],
        'preview_text' => $data['preview_text'],
        'status' => 'live',
        'icon' => $data['icon'],
        'accent' => $data['accent'],
        'hero_image' => $hero,
        'safety_note' => 'Friendly, guided practice with clear feedback and no pressure.',
        'web_play_url' => null,
        'points_reward' => $data['points_reward'],
        'estimated_minutes' => $data['estimated_minutes'],
        'age_min' => $data['age_min'],
        'age_max' => 16,
        'audience_roles' => $data['roles'],
        'learning_tags' => $data['tags'],
        'learning_outcomes' => $data['outcomes'],
        'detail_sections' => $data['sections'],
        'is_web_enabled' => true,
        'is_download_enabled' => false,
        'is_featured' => in_array($slug, ['math-quest', 'reading-garden', 'focus-forest'], true),
        'is_active' => true,
        'sort_order' => $sort,
    ];

    DB::table($table)->updateOrInsert(['slug' => $slug], payload_for($table, $payload));

    echo "✓ {$data['name']} uses {$hero}\n";
    $sort += 10;
}

echo "\nDONE: StudyBuddy app database is connected and ready.\n";
'''

Path("resources/views/studybuddy/final/apps.blade.php").write_text(apps_view)
Path("resources/views/studybuddy/final/app-detail.blade.php").write_text(detail_view)
Path("resources/views/studybuddy/final/web-play.blade.php").write_text(web_play_view)
Path("public/assets/css/studybuddy-connected-apps.css").write_text(css)
Path("public/assets/js/studybuddy-connected-apps.js").write_text(js)
Path("tools/finalize_studybuddy_apps_database.php").write_text(db_php)

print("DONE: final apps system files written.")
print("Backups saved with timestamp:", stamp)
