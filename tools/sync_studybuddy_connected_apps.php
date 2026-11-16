<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$table = 'studybuddy_mini_app_platforms';

if (!Schema::hasTable($table)) {
    echo "❌ Missing table: {$table}\n";
    exit(1);
}

function has_col(string $table, string $column): bool {
    return Schema::hasColumn($table, $column);
}

function pick_asset(array $paths): ?string {
    foreach ($paths as $path) {
        if (!$path) continue;
        $clean = ltrim($path, '/');
        if (file_exists(public_path($clean))) return $clean;
    }
    return 'assets/studybuddy-imgs/brand/logo-icon.png';
}

function db_payload(string $table, array $payload): array {
    $out = [];
    foreach ($payload as $key => $value) {
        if (has_col($table, $key)) {
            $out[$key] = is_array($value) ? json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $value;
        }
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
        'theme' => ['#7c3cff', '#246bff', '#22d3ee', '#fff3b0'],
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
        'theme' => ['#ff4f9a', '#7c3cff', '#ffd166', '#fff0f8'],
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
        'theme' => ['#16a34a', '#22c55e', '#22d3ee', '#f0fff6'],
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
        'theme' => ['#0f766e', '#22c55e', '#22d3ee', '#ecfeff'],
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
        'theme' => ['#f59e0b', '#ef4444', '#7c3cff', '#fff7ed'],
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
        'theme' => ['#4f46e5', '#ec4899', '#22d3ee', '#eef2ff'],
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
        'theme' => ['#06b6d4', '#8b5cf6', '#facc15', '#ecfeff'],
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
        'theme' => ['#9333ea', '#f97316', '#fde68a', '#faf5ff'],
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
        'image_url' => $hero,
        'hero_heading' => $data['name'],
        'long_description' => $data['description'],
        'safety_note' => 'Friendly, guided practice with clear feedback and no pressure.',
        'locked_preview_note' => 'Login to save progress, earn points, and continue your learning journey.',
        'platform_notes' => 'Web preview is connected to the StudyBuddy dashboard and points wallet.',
        'detail_cta_label' => 'Open Learning World',
        'points_reward' => $data['points_reward'],
        'estimated_minutes' => $data['estimated_minutes'],
        'age_min' => $data['age_min'],
        'age_max' => 16,
        'age_range' => $data['age_min'] . '+',
        'audience_roles' => $data['roles'],
        'role_scope' => $data['roles'],
        'learning_tags' => $data['tags'],
        'learning_outcomes' => $data['outcomes'],
        'detail_sections' => $data['sections'],
        'how_it_works' => $data['sections'],
        'screenshot_urls' => [
            "assets/studybuddy-imgs/02_apps/{$slug}/01_app-icon/{$slug}_main-icon.png",
            "assets/studybuddy-imgs/02_apps/{$slug}/03_sparks/{$slug}_star-main.png",
            "assets/studybuddy-imgs/02_apps/{$slug}/05_planets-bg/{$slug}_mini-planet.png",
        ],
        'is_web_enabled' => true,
        'is_download_enabled' => false,
        'is_featured' => in_array($slug, ['math-quest', 'reading-garden', 'focus-forest'], true),
        'is_active' => true,
        'sort_order' => $sort,
    ];

    if (has_col($table, 'created_at')) {
        $payload['created_at'] = now();
    }

    DB::table($table)->updateOrInsert(
        ['slug' => $slug],
        db_payload($table, $payload)
    );

    echo "✅ {$data['name']} → {$hero}\n";
    $sort += 10;
}

$count = DB::table($table)->where('is_active', true)->count();
echo "\nDONE ✅ Active StudyBuddy apps in DB: {$count}\n";
