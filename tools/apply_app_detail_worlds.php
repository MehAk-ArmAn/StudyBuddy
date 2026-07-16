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

$controller = <<'PHPCTRL'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudyBuddyAppWorldController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $apps = $this->apps();

        abort_unless(isset($apps[$slug]), 404);

        $app = $apps[$slug];
        $app['slug'] = $slug;
        $app['image_url'] = $this->findAsset($slug, $app['image_candidates'] ?? []);
        $app['alt'] = $app['alt'] ?? ($app['name'] . ' StudyBuddy app illustration');

        $related = collect($apps)
            ->except($slug)
            ->take(4)
            ->map(function ($item, $itemSlug) {
                $item['slug'] = $itemSlug;
                $item['image_url'] = $this->findAsset($itemSlug, $item['image_candidates'] ?? []);
                return $item;
            })
            ->values();

        return view('studybuddy.apps.world', [
            'app' => $app,
            'related' => $related,
        ]);
    }

    private function findAsset(string $slug, array $preferred = []): ?string
    {
        $slug = Str::slug($slug);

        $candidates = array_merge($preferred, [
            "assets/studybuddy-imgs/02_apps/{$slug}.png",
            "assets/studybuddy-imgs/02_apps/{$slug}.svg",
            "assets/studybuddy-imgs/02_apps/{$slug}.webp",
            "assets/studybuddy-imgs/apps/{$slug}.png",
            "assets/studybuddy-imgs/apps/{$slug}.svg",
            "assets/studybuddy-imgs/apps/{$slug}.webp",
            "assets/studybuddy-imgs/mini-apps/{$slug}.png",
            "assets/studybuddy-imgs/mini-apps/{$slug}.svg",
            "assets/studybuddy-imgs/mini-apps/{$slug}.webp",
            "assets/studybuddy-premium/apps/{$slug}.svg",
            "assets/studybuddy-premium/apps/{$slug}.png",
            "assets/studybuddy-brand/{$slug}.svg",
            "assets/studybuddy-brand/logo-icon.png",
            "assets/studybuddy-imgs/brand/logo-icon.png",
        ]);

        foreach ($candidates as $candidate) {
            $candidate = ltrim($candidate, '/');

            if (str_starts_with($candidate, 'http')) {
                return $candidate;
            }

            if (file_exists(public_path($candidate))) {
                return asset($candidate);
            }
        }

        $library = public_path('assets/studybuddy-imgs');

        if (is_dir($library)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($library, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile()) {
                    continue;
                }

                $extension = strtolower($file->getExtension());
                if (!in_array($extension, ['png', 'jpg', 'jpeg', 'svg', 'webp'], true)) {
                    continue;
                }

                $filename = Str::slug($file->getBasename('.' . $extension));

                if ($filename === $slug || str_contains($filename, $slug)) {
                    $relative = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    return asset(str_replace(DIRECTORY_SEPARATOR, '/', $relative));
                }
            }
        }

        return asset('assets/studybuddy-imgs/brand/logo-icon.png');
    }

    private function apps(): array
    {
        return [
            'math-quest' => [
                'name' => 'Math Quest',
                'badge' => 'Numbers • Missions • Confidence',
                'category' => 'Math Adventure',
                'audience' => 'Students and independent learners',
                'time' => '5–12 min practice rounds',
                'points' => '120 points',
                'theme' => ['#7c3cff', '#246bff', '#22d3ee', '#fff1a8'],
                'icon' => '✦',
                'image_candidates' => [
                    'assets/studybuddy-premium/apps/math-quest.svg',
                    'assets/studybuddy-imgs/apps/math-quest.svg',
                    'assets/studybuddy-imgs/02_apps/math-quest.svg',
                ],
                'alt' => 'Math Quest app illustration with a playful learning mission theme',
                'headline' => 'Turn numbers into a quest you actually want to finish.',
                'intro' => 'Math Quest helps learners build number confidence through short missions, friendly retries, progress points, and clear feedback.',
                'outcomes' => ['Mental math confidence', 'Step-by-step problem solving', 'Speed without panic', 'Healthy retry habits'],
                'missions' => [
                    ['title' => 'Warm-up Sparks', 'text' => 'Tiny starter questions to get the brain moving without pressure.'],
                    ['title' => 'Quest Rounds', 'text' => 'Skill-based challenges for arithmetic, patterns, and logic.'],
                    ['title' => 'Boss Review', 'text' => 'A final mixed round that reviews mistakes and celebrates progress.'],
                ],
                'routine' => ['Pick a skill', 'Complete 3 quick rounds', 'Review missed steps', 'Claim points'],
                'parent_note' => 'Parents can use Math Quest as a calm daily practice habit instead of stressful worksheets.',
                'teacher_note' => 'Teachers can recommend specific quest types as warm-ups, revision, or independent practice.',
            ],
            'spelling-sprint' => [
                'name' => 'Spelling Sprint',
                'badge' => 'Words • Speed • Memory',
                'category' => 'Language Sprint',
                'audience' => 'Students building spelling fluency',
                'time' => '4–10 min word rounds',
                'points' => '100 points',
                'theme' => ['#ff7aa2', '#7c3cff', '#ffd166', '#fff7fb'],
                'icon' => 'Aa',
                'image_candidates' => [
                    'assets/studybuddy-premium/apps/spelling-sprint.svg',
                    'assets/studybuddy-imgs/apps/spelling-sprint.svg',
                    'assets/studybuddy-imgs/02_apps/spelling-sprint.svg',
                ],
                'alt' => 'Spelling Sprint app illustration for playful word practice',
                'headline' => 'Make spelling practice fast, friendly, and less scary.',
                'intro' => 'Spelling Sprint turns word practice into short rounds with memory boosts, retry loops, and encouraging feedback.',
                'outcomes' => ['Word pattern recognition', 'Vocabulary confidence', 'Spelling accuracy', 'Quick recall'],
                'missions' => [
                    ['title' => 'Listen & Build', 'text' => 'Break words into parts and rebuild them with confidence.'],
                    ['title' => 'Sprint Round', 'text' => 'Practice a focused word set in a timed but gentle format.'],
                    ['title' => 'Mistake Replay', 'text' => 'Review tricky words without shame or pressure.'],
                ],
                'routine' => ['Choose a word set', 'Practice patterns', 'Sprint the list', 'Retry tricky words'],
                'parent_note' => 'Parents can use short spelling rounds as a daily confidence booster.',
                'teacher_note' => 'Teachers can match sprint lists to classroom vocabulary or weekly spellings.',
            ],
            'reading-garden' => [
                'name' => 'Reading Garden',
                'badge' => 'Stories • Vocabulary • Reflection',
                'category' => 'Reading Growth',
                'audience' => 'Readers building fluency',
                'time' => '8–15 min reading sessions',
                'points' => '110 points',
                'theme' => ['#14b87a', '#22d3ee', '#8ee6a8', '#f0fff6'],
                'icon' => '☘',
                'image_candidates' => [
                    'assets/studybuddy-premium/apps/reading-garden.svg',
                    'assets/studybuddy-imgs/apps/reading-garden.svg',
                    'assets/studybuddy-imgs/02_apps/reading-garden.svg',
                ],
                'alt' => 'Reading Garden app illustration with a calm story garden theme',
                'headline' => 'Grow reading fluency one calm story at a time.',
                'intro' => 'Reading Garden creates a gentle reading space with story moments, vocabulary, reflection prompts, and progress growth.',
                'outcomes' => ['Reading fluency', 'Vocabulary growth', 'Comprehension', 'Reflection skills'],
                'missions' => [
                    ['title' => 'Story Seed', 'text' => 'Begin with a short, clear reading goal.'],
                    ['title' => 'Vocabulary Bloom', 'text' => 'Collect useful words and understand them in context.'],
                    ['title' => 'Reflection Patch', 'text' => 'Answer simple prompts to check understanding.'],
                ],
                'routine' => ['Read a story', 'Collect words', 'Answer reflection prompts', 'Grow your garden'],
                'parent_note' => 'Parents can use Reading Garden for calm reading time and simple discussion prompts.',
                'teacher_note' => 'Teachers can use it for reading stations, fluency practice, and comprehension checks.',
            ],
            'focus-forest' => [
                'name' => 'Focus Forest',
                'badge' => 'Focus • Timer • Calm',
                'category' => 'Study Routine',
                'audience' => 'Learners who need routine support',
                'time' => '10–25 min focus sessions',
                'points' => '90 points',
                'theme' => ['#0f766e', '#22c55e', '#22d3ee', '#ecfeff'],
                'icon' => '◌',
                'image_candidates' => [
                    'assets/studybuddy-premium/apps/focus-forest.svg',
                    'assets/studybuddy-imgs/apps/focus-forest.svg',
                    'assets/studybuddy-imgs/02_apps/focus-forest.svg',
                ],
                'alt' => 'Focus Forest app illustration for calm study routines',
                'headline' => 'Build focus without making studying feel heavy.',
                'intro' => 'Focus Forest supports healthy study sessions with gentle timers, mindful breaks, streaks, and progress that feels calm.',
                'outcomes' => ['Attention habits', 'Study consistency', 'Break routines', 'Less overwhelm'],
                'missions' => [
                    ['title' => 'Plant a Focus Tree', 'text' => 'Choose a task and start a gentle focus timer.'],
                    ['title' => 'Protect the Session', 'text' => 'Stay with the task while the forest grows.'],
                    ['title' => 'Take a Mindful Break', 'text' => 'Pause, breathe, reset, and come back stronger.'],
                ],
                'routine' => ['Choose task', 'Set timer', 'Focus', 'Take a break'],
                'parent_note' => 'Parents can use Focus Forest to support homework routines without constant reminding.',
                'teacher_note' => 'Teachers can use it for independent work blocks and quiet classroom focus time.',
            ],
            'planner-city' => [
                'name' => 'Planner City',
                'badge' => 'Goals • Tasks • Routine',
                'category' => 'Planning System',
                'audience' => 'Busy learners and families',
                'time' => 'Daily planning in 3–7 min',
                'points' => '80 points',
                'theme' => ['#f59e0b', '#ef4444', '#7c3cff', '#fff7ed'],
                'icon' => '▦',
                'image_candidates' => [
                    'assets/studybuddy-premium/apps/planner-city.svg',
                    'assets/studybuddy-imgs/apps/planner-city.svg',
                    'assets/studybuddy-imgs/02_apps/planner-city.svg',
                ],
                'alt' => 'Planner City app illustration for organizing learning tasks',
                'headline' => 'Turn messy tasks into a city map you can follow.',
                'intro' => 'Planner City helps learners organize homework, revision, goals, and routines into simple steps.',
                'outcomes' => ['Task planning', 'Prioritization', 'Routine building', 'Goal tracking'],
                'missions' => [
                    ['title' => 'Build Today’s Map', 'text' => 'Turn tasks into a simple route for the day.'],
                    ['title' => 'Priority Blocks', 'text' => 'Choose what matters most and avoid overwhelm.'],
                    ['title' => 'Progress Streets', 'text' => 'Mark completed tasks and keep moving forward.'],
                ],
                'routine' => ['Add tasks', 'Pick priorities', 'Plan study blocks', 'Check off progress'],
                'parent_note' => 'Parents can use Planner City to make homework expectations clearer.',
                'teacher_note' => 'Teachers can use it to help learners break big assignments into smaller steps.',
            ],
            'quiz-galaxy' => [
                'name' => 'Quiz Galaxy',
                'badge' => 'Review • Retry • Points',
                'category' => 'Quiz Universe',
                'audience' => 'Learners reviewing any topic',
                'time' => '5–15 min quiz flights',
                'points' => '120 points',
                'theme' => ['#4f46e5', '#ec4899', '#22d3ee', '#eef2ff'],
                'icon' => '◎',
                'image_candidates' => [
                    'assets/studybuddy-premium/apps/quiz-galaxy.svg',
                    'assets/studybuddy-imgs/apps/quiz-galaxy.svg',
                    'assets/studybuddy-imgs/02_apps/quiz-galaxy.svg',
                ],
                'alt' => 'Quiz Galaxy app illustration for topic review and practice',
                'headline' => 'Launch quick quizzes across the learning galaxy.',
                'intro' => 'Quiz Galaxy makes revision feel active with short quizzes, instant feedback, smart retries, and points.',
                'outcomes' => ['Memory recall', 'Exam practice', 'Topic review', 'Confidence under questions'],
                'missions' => [
                    ['title' => 'Launch Pad', 'text' => 'Pick a topic and start with a small question set.'],
                    ['title' => 'Star Questions', 'text' => 'Answer mixed questions with clear feedback.'],
                    ['title' => 'Retry Orbit', 'text' => 'Revisit missed questions until they feel easy.'],
                ],
                'routine' => ['Pick topic', 'Answer quiz', 'Review mistakes', 'Retry and earn points'],
                'parent_note' => 'Parents can use Quiz Galaxy to check revision without turning it into pressure.',
                'teacher_note' => 'Teachers can use it for exit tickets, revision rounds, or independent review.',
            ],
            'shapes-lab' => [
                'name' => 'Shapes Lab',
                'badge' => 'Geometry • Patterns • Visual Thinking',
                'category' => 'STEM Lab',
                'audience' => 'Visual learners and young problem-solvers',
                'time' => '6–12 min shape labs',
                'points' => '80 points',
                'theme' => ['#06b6d4', '#8b5cf6', '#facc15', '#ecfeff'],
                'icon' => '△',
                'image_candidates' => [
                    'assets/studybuddy-premium/apps/shapes-lab.svg',
                    'assets/studybuddy-imgs/apps/shapes-lab.svg',
                    'assets/studybuddy-imgs/02_apps/shapes-lab.svg',
                ],
                'alt' => 'Shapes Lab app illustration for geometry and pattern learning',
                'headline' => 'Explore shapes, patterns, and visual problem solving.',
                'intro' => 'Shapes Lab helps learners think visually through shape sorting, patterns, geometry puzzles, and spatial reasoning.',
                'outcomes' => ['Geometry basics', 'Pattern recognition', 'Spatial reasoning', 'Visual confidence'],
                'missions' => [
                    ['title' => 'Shape Sort', 'text' => 'Group shapes by sides, corners, and properties.'],
                    ['title' => 'Pattern Machine', 'text' => 'Spot what comes next and explain why.'],
                    ['title' => 'Build Challenge', 'text' => 'Use shapes to solve visual puzzles.'],
                ],
                'routine' => ['Observe', 'Sort', 'Build', 'Explain'],
                'parent_note' => 'Parents can use Shapes Lab for playful STEM practice at home.',
                'teacher_note' => 'Teachers can use it for geometry centers and visual reasoning activities.',
            ],
            'flashcard-castle' => [
                'name' => 'Flashcard Castle',
                'badge' => 'Recall • Decks • Memory',
                'category' => 'Memory Castle',
                'audience' => 'Learners memorizing key facts',
                'time' => '5–10 min recall rounds',
                'points' => '90 points',
                'theme' => ['#9333ea', '#f97316', '#fde68a', '#faf5ff'],
                'icon' => '▣',
                'image_candidates' => [
                    'assets/studybuddy-premium/apps/flashcard-castle.svg',
                    'assets/studybuddy-imgs/apps/flashcard-castle.svg',
                    'assets/studybuddy-imgs/02_apps/flashcard-castle.svg',
                ],
                'alt' => 'Flashcard Castle app illustration for memory and recall practice',
                'headline' => 'Protect your knowledge inside a memory castle.',
                'intro' => 'Flashcard Castle helps learners build decks, practice recall, and review facts through short memory rounds.',
                'outcomes' => ['Active recall', 'Vocabulary memory', 'Exam facts', 'Spaced practice habits'],
                'missions' => [
                    ['title' => 'Build a Deck', 'text' => 'Create cards for words, facts, definitions, or formulas.'],
                    ['title' => 'Castle Recall', 'text' => 'Practice cards and mark what feels strong or tricky.'],
                    ['title' => 'Treasure Review', 'text' => 'Return to missed cards and lock in memory.'],
                ],
                'routine' => ['Create cards', 'Practice recall', 'Mark tricky cards', 'Review again'],
                'parent_note' => 'Parents can use Flashcard Castle for vocabulary, spellings, and exam review.',
                'teacher_note' => 'Teachers can use it for retrieval practice and topic recap decks.',
            ],
        ];
    }
}
PHPCTRL;

$view = <<'BLADE'
@extends('layouts.app')

@section('title', $app['name'] . ' | StudyBuddy')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-app-worlds.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-app-worlds.css')) ? filemtime(public_path('assets/css/studybuddy-app-worlds.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-app-worlds.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-app-worlds.js')) ? filemtime(public_path('assets/js/studybuddy-app-worlds.js')) : time() }}" defer></script>

@php
    $theme = $app['theme'];
@endphp

<main id="main-content" class="sb-app-world" style="--app-one: {{ $theme[0] }}; --app-two: {{ $theme[1] }}; --app-three: {{ $theme[2] }}; --app-soft: {{ $theme[3] }};">
    <section class="sb-app-hero">
        <div class="sb-app-orb one" aria-hidden="true"></div>
        <div class="sb-app-orb two" aria-hidden="true"></div>

        <div class="sb-app-hero-copy">
            <a class="sb-app-back" href="{{ url('/apps') }}">← Back to App Universe</a>
            <p class="sb-app-badge">{{ $app['badge'] }}</p>
            <h1>{{ $app['headline'] }}</h1>
            <p class="sb-app-intro">{{ $app['intro'] }}</p>

            <div class="sb-app-actions">
                <a href="{{ url('/register') }}">Start learning</a>
                <a class="soft" href="{{ url('/dashboard') }}">Open dashboard</a>
            </div>
        </div>

        <div class="sb-app-hero-card" data-tilt>
            <div class="sb-app-icon">{{ $app['icon'] }}</div>
            @if($app['image_url'])
                <img src="{{ $app['image_url'] }}" alt="{{ $app['alt'] }}">
            @endif
            <div>
                <strong>{{ $app['name'] }}</strong>
                <span>{{ $app['category'] }}</span>
            </div>
        </div>
    </section>

    <section class="sb-app-facts" aria-label="{{ $app['name'] }} quick facts">
        <article><span>Best for</span><strong>{{ $app['audience'] }}</strong></article>
        <article><span>Session length</span><strong>{{ $app['time'] }}</strong></article>
        <article><span>Reward</span><strong>{{ $app['points'] }}</strong></article>
    </section>

    <section class="sb-app-section split">
        <div>
            <p class="sb-app-kicker">Learning outcomes</p>
            <h2>What learners build here</h2>
            <p>Each StudyBuddy app is designed as a focused learning world. The goal is not just clicking buttons — it is building confidence through small wins.</p>
        </div>

        <div class="sb-app-outcomes">
            @foreach($app['outcomes'] as $outcome)
                <article>
                    <span>✓</span>
                    <strong>{{ $outcome }}</strong>
                </article>
            @endforeach
        </div>
    </section>

    <section class="sb-app-section">
        <div class="sb-app-section-head">
            <p class="sb-app-kicker">App journey</p>
            <h2>How {{ $app['name'] }} feels when you play</h2>
        </div>

        <div class="sb-app-mission-grid">
            @foreach($app['missions'] as $mission)
                <article class="sb-app-mission-card" data-spotlight>
                    <span>0{{ $loop->iteration }}</span>
                    <h3>{{ $mission['title'] }}</h3>
                    <p>{{ $mission['text'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="sb-app-section routine">
        <div>
            <p class="sb-app-kicker">Recommended routine</p>
            <h2>A simple flow that works</h2>
        </div>

        <ol class="sb-app-routine">
            @foreach($app['routine'] as $step)
                <li>
                    <b>{{ $loop->iteration }}</b>
                    <span>{{ $step }}</span>
                </li>
            @endforeach
        </ol>
    </section>

    <section class="sb-app-section support">
        <article>
            <p class="sb-app-kicker">For parents</p>
            <h2>Support without pressure</h2>
            <p>{{ $app['parent_note'] }}</p>
        </article>

        <article>
            <p class="sb-app-kicker">For teachers</p>
            <h2>Easy to connect with practice</h2>
            <p>{{ $app['teacher_note'] }}</p>
        </article>
    </section>

    <section class="sb-app-section related">
        <div class="sb-app-section-head">
            <p class="sb-app-kicker">Explore more</p>
            <h2>More StudyBuddy worlds</h2>
        </div>

        <div class="sb-app-related-grid">
            @foreach($related as $item)
                <a href="{{ url('/apps/' . $item['slug']) }}" class="sb-app-related-card">
                    @if($item['image_url'])
                        <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }} app illustration">
                    @endif
                    <strong>{{ $item['name'] }}</strong>
                    <span>{{ $item['category'] }}</span>
                </a>
            @endforeach
        </div>
    </section>
</main>
@endsection
BLADE;

$css = <<'CSS'
:root {
    --sb-page-bg: #f7f9ff;
    --sb-ink: #08111f;
    --sb-muted: #5d6b7d;
    --sb-line: rgba(15, 23, 42, .10);
}

.sb-app-world {
    min-height: 100vh;
    overflow: hidden;
    color: var(--sb-ink);
    background:
        radial-gradient(circle at 0% 0%, color-mix(in srgb, var(--app-three) 24%, transparent), transparent 34%),
        radial-gradient(circle at 100% 8%, color-mix(in srgb, var(--app-one) 20%, transparent), transparent 36%),
        linear-gradient(180deg, var(--app-soft), #ffffff 38%, #f7f9ff);
}

.sb-app-world *,
.sb-app-world *::before,
.sb-app-world *::after {
    box-sizing: border-box;
}

.sb-app-hero,
.sb-app-section,
.sb-app-facts {
    width: min(100%, 1180px);
    margin-inline: auto;
    padding-inline: clamp(16px, 4vw, 28px);
}

.sb-app-hero {
    position: relative;
    display: grid;
    grid-template-columns: minmax(0, 1.08fr) minmax(300px, .92fr);
    gap: clamp(28px, 5vw, 56px);
    align-items: center;
    padding-top: clamp(42px, 8vw, 92px);
    padding-bottom: clamp(30px, 6vw, 70px);
}

.sb-app-orb {
    position: absolute;
    border-radius: 999px;
    pointer-events: none;
    filter: blur(2px);
}

.sb-app-orb.one {
    width: 260px;
    height: 260px;
    left: -90px;
    top: 40px;
    background: color-mix(in srgb, var(--app-two) 18%, transparent);
}

.sb-app-orb.two {
    width: 180px;
    height: 180px;
    right: 40px;
    bottom: 40px;
    border: 1px solid color-mix(in srgb, var(--app-one) 32%, transparent);
}

.sb-app-hero-copy {
    position: relative;
    min-width: 0;
}

.sb-app-back {
    display: inline-flex;
    margin-bottom: 18px;
    border-radius: 999px;
    padding: 9px 12px;
    color: var(--sb-ink);
    background: rgba(255,255,255,.72);
    text-decoration: none;
    font-weight: 900;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
}

.sb-app-badge,
.sb-app-kicker {
    margin: 0 0 10px;
    color: var(--app-one);
    font-size: .78rem;
    font-weight: 950;
    letter-spacing: .12em;
    text-transform: uppercase;
}

.sb-app-hero h1 {
    max-width: 780px;
    margin: 0;
    font-size: clamp(2.45rem, 7vw, 5.9rem);
    line-height: .88;
    letter-spacing: -.08em;
    overflow-wrap: anywhere;
}

.sb-app-intro {
    max-width: 700px;
    margin: 22px 0 0;
    color: var(--sb-muted);
    font-size: clamp(1.02rem, 2vw, 1.2rem);
    line-height: 1.75;
    overflow-wrap: anywhere;
}

.sb-app-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 26px;
}

.sb-app-actions a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    border-radius: 999px;
    padding: 12px 18px;
    color: white;
    background: linear-gradient(135deg, var(--app-one), var(--app-two));
    text-decoration: none;
    font-weight: 950;
    box-shadow: 0 18px 36px color-mix(in srgb, var(--app-one) 22%, transparent);
    transition: transform .2s ease, box-shadow .2s ease;
}

.sb-app-actions a:hover {
    transform: translateY(-3px);
    box-shadow: 0 24px 54px color-mix(in srgb, var(--app-one) 30%, transparent);
}

.sb-app-actions a.soft {
    color: var(--sb-ink);
    background: rgba(255,255,255,.78);
}

.sb-app-hero-card {
    position: relative;
    min-width: 0;
    isolation: isolate;
    border: 1px solid rgba(255,255,255,.84);
    border-radius: clamp(30px, 5vw, 48px);
    padding: clamp(24px, 4vw, 42px);
    background:
        radial-gradient(circle at var(--mx, 70%) var(--my, 20%), color-mix(in srgb, var(--app-three) 22%, transparent), transparent 36%),
        rgba(255,255,255,.72);
    box-shadow: 0 30px 80px rgba(15, 23, 42, .14);
    backdrop-filter: blur(18px);
    transition: transform .25s ease;
}

.sb-app-hero-card:hover {
    transform: translateY(-6px) rotate(.7deg);
}

.sb-app-icon {
    position: absolute;
    top: 22px;
    right: 22px;
    width: 54px;
    height: 54px;
    display: grid;
    place-items: center;
    border-radius: 20px;
    color: white;
    background: linear-gradient(135deg, var(--app-one), var(--app-two));
    font-weight: 950;
    box-shadow: 0 18px 36px color-mix(in srgb, var(--app-one) 22%, transparent);
}

.sb-app-hero-card img {
    width: min(100%, 390px);
    height: clamp(220px, 34vw, 390px);
    display: block;
    margin: 0 auto;
    object-fit: contain;
    filter: drop-shadow(0 28px 36px rgba(15, 23, 42, .12));
}

.sb-app-hero-card strong {
    display: block;
    margin-top: 16px;
    font-size: clamp(1.4rem, 3vw, 2rem);
    letter-spacing: -.04em;
}

.sb-app-hero-card span {
    display: block;
    margin-top: 4px;
    color: var(--sb-muted);
    font-weight: 800;
}

.sb-app-facts {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: clamp(40px, 7vw, 72px);
}

.sb-app-facts article,
.sb-app-section {
    border: 1px solid rgba(15,23,42,.08);
    background: rgba(255,255,255,.78);
    box-shadow: 0 22px 60px rgba(15, 23, 42, .08);
    backdrop-filter: blur(16px);
}

.sb-app-facts article {
    min-width: 0;
    border-radius: 26px;
    padding: 18px;
}

.sb-app-facts span {
    display: block;
    color: var(--sb-muted);
    font-size: .78rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .1em;
}

.sb-app-facts strong {
    display: block;
    margin-top: 8px;
    font-size: clamp(1rem, 2vw, 1.3rem);
    overflow-wrap: anywhere;
}

.sb-app-section {
    margin-bottom: 22px;
    border-radius: 34px;
    padding: clamp(24px, 4vw, 40px);
}

.sb-app-section.split,
.sb-app-section.support,
.sb-app-section.routine {
    display: grid;
    grid-template-columns: minmax(0, .9fr) minmax(0, 1.1fr);
    gap: clamp(20px, 4vw, 38px);
    align-items: start;
}

.sb-app-section h2 {
    margin: 0;
    font-size: clamp(1.8rem, 4vw, 3.2rem);
    line-height: .95;
    letter-spacing: -.06em;
    overflow-wrap: anywhere;
}

.sb-app-section p {
    color: var(--sb-muted);
    line-height: 1.7;
    overflow-wrap: anywhere;
}

.sb-app-outcomes,
.sb-app-mission-grid,
.sb-app-related-grid {
    display: grid;
    gap: 14px;
}

.sb-app-outcomes {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.sb-app-outcomes article {
    display: flex;
    gap: 12px;
    align-items: center;
    min-width: 0;
    border-radius: 20px;
    padding: 14px;
    background: color-mix(in srgb, var(--app-three) 12%, white);
}

.sb-app-outcomes span {
    flex: 0 0 auto;
    width: 32px;
    height: 32px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    color: white;
    background: var(--app-one);
    font-weight: 950;
}

.sb-app-outcomes strong {
    min-width: 0;
    overflow-wrap: anywhere;
}

.sb-app-section-head {
    margin-bottom: 20px;
}

.sb-app-mission-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

.sb-app-mission-card {
    position: relative;
    min-width: 0;
    overflow: hidden;
    border: 1px solid rgba(15,23,42,.08);
    border-radius: 28px;
    padding: 22px;
    background:
        radial-gradient(circle at var(--mx, 50%) var(--my, 0%), color-mix(in srgb, var(--app-three) 18%, transparent), transparent 36%),
        #fff;
    transition: transform .22s ease, box-shadow .22s ease;
}

.sb-app-mission-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 24px 48px rgba(15, 23, 42, .10);
}

.sb-app-mission-card span {
    color: var(--app-one);
    font-weight: 950;
}

.sb-app-mission-card h3 {
    margin: 10px 0 8px;
    font-size: 1.35rem;
    overflow-wrap: anywhere;
}

.sb-app-routine {
    display: grid;
    gap: 12px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.sb-app-routine li {
    display: flex;
    gap: 12px;
    align-items: center;
    border-radius: 22px;
    padding: 15px;
    background: #fff;
}

.sb-app-routine b {
    width: 40px;
    height: 40px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 16px;
    color: white;
    background: linear-gradient(135deg, var(--app-one), var(--app-two));
}

.sb-app-routine span {
    overflow-wrap: anywhere;
    font-weight: 850;
}

.sb-app-section.support article {
    min-width: 0;
    border-radius: 26px;
    padding: 24px;
    background: color-mix(in srgb, var(--app-soft) 62%, white);
}

.sb-app-related-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr));
}

.sb-app-related-card {
    min-width: 0;
    border-radius: 26px;
    padding: 16px;
    color: var(--sb-ink);
    background: #fff;
    text-decoration: none;
    box-shadow: 0 18px 36px rgba(15,23,42,.06);
    transition: transform .22s ease, box-shadow .22s ease;
}

.sb-app-related-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 24px 54px rgba(15,23,42,.12);
}

.sb-app-related-card img {
    width: 100%;
    height: 130px;
    object-fit: contain;
}

.sb-app-related-card strong,
.sb-app-related-card span {
    display: block;
    overflow-wrap: anywhere;
}

.sb-app-related-card strong {
    margin-top: 10px;
}

.sb-app-related-card span {
    margin-top: 4px;
    color: var(--sb-muted);
}

@media (max-width: 960px) {
    .sb-app-hero,
    .sb-app-section.split,
    .sb-app-section.support,
    .sb-app-section.routine {
        grid-template-columns: 1fr;
    }

    .sb-app-facts,
    .sb-app-mission-grid,
    .sb-app-related-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sb-app-hero-card {
        order: -1;
    }
}

@media (max-width: 620px) {
    .sb-app-facts,
    .sb-app-outcomes,
    .sb-app-mission-grid,
    .sb-app-related-grid {
        grid-template-columns: 1fr;
    }

    .sb-app-actions a {
        width: 100%;
    }

    .sb-app-hero-card img {
        height: 240px;
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }
}
CSS;

$js = <<'JS'
(() => {
    document.querySelectorAll('[data-spotlight], [data-tilt]').forEach((card) => {
        card.addEventListener('mousemove', (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });

    document.querySelectorAll('[data-tilt]').forEach((card) => {
        card.addEventListener('mousemove', (event) => {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            const rect = card.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width - .5) * 8;
            const y = ((event.clientY - rect.top) / rect.height - .5) * -8;
            card.style.transform = `translateY(-6px) rotateX(${y}deg) rotateY(${x}deg)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });
})();
JS;

$routes = <<'PHPROUTE'
<?php

use App\Http\Controllers\StudyBuddyAppWorldController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/apps/{slug}', [StudyBuddyAppWorldController::class, 'show'])
        ->where('slug', '[A-Za-z0-9\-]+')
        ->name('studybuddy.apps.world');
});
PHPROUTE;

write_file('app/Http/Controllers/StudyBuddyAppWorldController.php', $controller);
write_file('resources/views/studybuddy/apps/world.blade.php', $view);
write_file('public/assets/css/studybuddy-app-worlds.css', $css);
write_file('public/assets/js/studybuddy-app-worlds.js', $js);
write_file('routes/studybuddy_app_worlds.php', $routes);

/**
 * Load the app-world routes before older /apps/{slug} routes.
 */
$studybuddyRoutes = $root . '/routes/studybuddy.php';
$routeInclude = "if (file_exists(__DIR__ . '/studybuddy_app_worlds.php')) { require __DIR__ . '/studybuddy_app_worlds.php'; }";

if (file_exists($studybuddyRoutes)) {
    $text = file_get_contents($studybuddyRoutes);

    if (!str_contains($text, 'studybuddy_app_worlds.php')) {
        copy($studybuddyRoutes, $studybuddyRoutes . '.bak_' . date('Ymd_His'));

        $lines = explode("\n", $text);
        $insertAt = 1;

        foreach ($lines as $i => $line) {
            if (preg_match('/^\s*use\s+/', $line)) {
                $insertAt = $i + 1;
            }
        }

        array_splice($lines, $insertAt, 0, ['', $routeInclude, '']);
        file_put_contents($studybuddyRoutes, implode("\n", $lines));
        echo "✓ patched routes/studybuddy.php to load app worlds first\n";
    }
} else {
    $webRoutes = $root . '/routes/web.php';
    if (file_exists($webRoutes)) {
        $text = file_get_contents($webRoutes);
        if (!str_contains($text, 'studybuddy_app_worlds.php')) {
            copy($webRoutes, $webRoutes . '.bak_' . date('Ymd_His'));
            file_put_contents($webRoutes, $routeInclude . "\n" . $text);
            echo "✓ patched routes/web.php to load app worlds\n";
        }
    }
}

echo "\nDONE ✅ App detail worlds applied.\n";
