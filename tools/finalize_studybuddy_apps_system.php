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

function sb_has_col(string $table, string $column): bool {
    return Schema::hasColumn($table, $column);
}

function sb_public_file(?string $path): bool {
    if (!$path) return false;
    if (preg_match('/^https?:\/\//i', $path)) return true;
    return file_exists(public_path(ltrim($path, '/')));
}

function sb_find_image(string $slug): string {
    $slug = trim($slug);

    $preferred = [
        "assets/studybuddy-imgs/apps/app-{$slug}.png",
        "assets/studybuddy-imgs/apps/{$slug}.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/01_app-icon/{$slug}_main-icon.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/01_app-icon/{$slug}_icon-512.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/01_app-icon/{$slug}_icon.png",
    ];

    foreach ($preferred as $path) {
        if (file_exists(public_path($path))) return $path;
    }

    $root = public_path('assets/studybuddy-imgs');

    if (is_dir($root)) {
        $hits = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) continue;

            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'], true)) continue;

            $relative = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
            $lower = strtolower($relative);

            if (!str_contains($lower, $slug)) continue;
            if (str_contains($lower, 'spark') || str_contains($lower, 'star') || str_contains($lower, 'comet') || str_contains($lower, 'glitch')) continue;

            $score = 0;
            if (str_contains($lower, "app-{$slug}")) $score += 80;
            if (str_contains($lower, 'main-icon')) $score += 70;
            if (str_contains($lower, 'icon-512')) $score += 60;
            if (str_contains($lower, '01_app-icon')) $score += 40;
            if (str_contains($lower, 'apps/')) $score += 30;

            $hits[] = [$score, $relative];
        }

        usort($hits, fn($a, $b) => $b[0] <=> $a[0]);

        if ($hits) return $hits[0][1];
    }

    return file_exists(public_path('assets/studybuddy-imgs/brand/logo-icon.png'))
        ? 'assets/studybuddy-imgs/brand/logo-icon.png'
        : 'assets/studybuddy-brand/logo-icon.png';
}

function sb_gallery(string $slug, string $main): array {
    $candidates = [
        $main,
        "assets/studybuddy-imgs/02_apps/{$slug}/01_app-icon/{$slug}_main-icon.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/01_app-icon/{$slug}_icon-512.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/02_orbs/{$slug}_orb-glow.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/02_orbs/{$slug}_orb-small.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/05_planets-bg/{$slug}_mini-planet.png",
        "assets/studybuddy-imgs/apps/app-{$slug}.png",
    ];

    $gallery = [];

    foreach ($candidates as $path) {
        if (!$path) continue;
        $clean = ltrim($path, '/');
        if (!in_array($clean, $gallery, true) && file_exists(public_path($clean))) {
            $gallery[] = $clean;
        }
    }

    if (count($gallery) < 3 && is_dir(public_path('assets/studybuddy-imgs'))) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(public_path('assets/studybuddy-imgs'), FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) continue;

            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'], true)) continue;

            $relative = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
            $lower = strtolower($relative);

            if (!str_contains($lower, $slug)) continue;
            if (str_contains($lower, 'spark') || str_contains($lower, 'star') || str_contains($lower, 'comet') || str_contains($lower, 'glitch')) continue;

            if (!in_array($relative, $gallery, true)) $gallery[] = $relative;
            if (count($gallery) >= 5) break;
        }
    }

    return array_values(array_slice(array_unique($gallery), 0, 5));
}

function sb_payload(string $table, array $payload): array {
    $out = [];

    foreach ($payload as $key => $value) {
        if (!sb_has_col($table, $key)) continue;
        $out[$key] = is_array($value)
            ? json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            : $value;
    }

    if (sb_has_col($table, 'updated_at')) {
        $out['updated_at'] = now();
    }

    return $out;
}

$apps = [
    'math-quest' => [
        'name' => 'Math Quest',
        'category' => 'Math Adventure',
        'tagline' => 'Numbers become glowing missions.',
        'description' => 'Turn number practice into a cosmic quest with quick rounds, clear steps, calm retries, and confidence-building rewards.',
        'preview' => 'A playful math world for tiny wins, number confidence, and brave problem-solving.',
        'icon' => '✦',
        'accent' => '#7c3cff',
        'theme' => ['#7c3cff', '#246bff', '#22d3ee', '#fff3b0'],
        'points' => 120,
        'minutes' => 8,
        'age' => 7,
        'roles' => ['student', 'parent', 'teacher', 'independent_learner'],
        'tags' => ['math', 'numbers', 'logic', 'confidence'],
        'outcomes' => ['Mental math confidence', 'Step-by-step problem solving', 'Fast recall without panic', 'Pattern spotting and logic'],
        'sections' => [
            ['title' => 'Warm-up Sparks', 'body' => 'Start with tiny number questions that wake up your brain without pressure.'],
            ['title' => 'Quest Rounds', 'body' => 'Practice focused skills through short missions, points, and friendly feedback.'],
            ['title' => 'Boss Review', 'body' => 'Finish with a mixed challenge that helps mistakes turn into progress.'],
        ],
    ],
    'spelling-sprint' => [
        'name' => 'Spelling Sprint',
        'category' => 'Language Sprint',
        'tagline' => 'Words, speed, memory, confidence.',
        'description' => 'Build spelling confidence with quick word rounds, pattern practice, retries, and friendly memory boosts.',
        'preview' => 'A fast word-practice world that makes spelling feel lighter and more fun.',
        'icon' => 'Aa',
        'accent' => '#ff4f9a',
        'theme' => ['#ff4f9a', '#7c3cff', '#ffd166', '#fff0f8'],
        'points' => 100,
        'minutes' => 7,
        'age' => 6,
        'roles' => ['student', 'parent', 'teacher'],
        'tags' => ['spelling', 'words', 'vocabulary', 'memory'],
        'outcomes' => ['Word pattern recognition', 'Vocabulary confidence', 'Spelling accuracy', 'Quick recall'],
        'sections' => [
            ['title' => 'Word Warm-up', 'body' => 'Break tricky words into smaller parts so they feel easier.'],
            ['title' => 'Sprint Round', 'body' => 'Practice a focused word list with energy, rhythm, and no shame.'],
            ['title' => 'Retry Glow', 'body' => 'Revisit the words that need more love until they stick.'],
        ],
    ],
    'reading-garden' => [
        'name' => 'Reading Garden',
        'category' => 'Reading Growth',
        'tagline' => 'Grow stories into confidence.',
        'description' => 'A calm reading world with story goals, vocabulary blooms, reflection prompts, and gentle progress.',
        'preview' => 'A peaceful place to read, collect words, and grow understanding one story at a time.',
        'icon' => '☘',
        'accent' => '#16a34a',
        'theme' => ['#16a34a', '#22c55e', '#22d3ee', '#f0fff6'],
        'points' => 110,
        'minutes' => 12,
        'age' => 7,
        'roles' => ['student', 'parent', 'teacher', 'independent_learner'],
        'tags' => ['reading', 'stories', 'vocabulary', 'comprehension'],
        'outcomes' => ['Reading fluency', 'Vocabulary growth', 'Comprehension', 'Reflection skills'],
        'sections' => [
            ['title' => 'Story Seed', 'body' => 'Begin with a short reading goal and a calm focus moment.'],
            ['title' => 'Vocabulary Bloom', 'body' => 'Collect useful words and understand them in context.'],
            ['title' => 'Reflection Patch', 'body' => 'Answer simple prompts that turn reading into understanding.'],
        ],
    ],
    'focus-forest' => [
        'name' => 'Focus Forest',
        'category' => 'Study Routine',
        'tagline' => 'Calm focus, gentle routines.',
        'description' => 'Build focus habits with gentle timers, mindful breaks, streaks, and routines that reduce overwhelm.',
        'preview' => 'A calm focus world for homework, study sessions, and peaceful progress.',
        'icon' => '◌',
        'accent' => '#0f766e',
        'theme' => ['#0f766e', '#22c55e', '#22d3ee', '#ecfeff'],
        'points' => 90,
        'minutes' => 15,
        'age' => 8,
        'roles' => ['student', 'parent', 'teacher', 'independent_learner'],
        'tags' => ['focus', 'timer', 'routine', 'calm'],
        'outcomes' => ['Attention habits', 'Study consistency', 'Break routines', 'Less overwhelm'],
        'sections' => [
            ['title' => 'Plant a Focus Tree', 'body' => 'Pick one task and begin a short focus session.'],
            ['title' => 'Protect the Session', 'body' => 'Stay with the task while your focus world grows.'],
            ['title' => 'Mindful Break', 'body' => 'Pause, breathe, reset, and come back stronger.'],
        ],
    ],
    'planner-city' => [
        'name' => 'Planner City',
        'category' => 'Planning System',
        'tagline' => 'Turn tasks into a city map.',
        'description' => 'Organize homework, revision, goals, and routines into clear tiny steps that are easier to follow.',
        'preview' => 'A planning world that turns messy tasks into a path you can actually complete.',
        'icon' => '▦',
        'accent' => '#f59e0b',
        'theme' => ['#f59e0b', '#ef4444', '#7c3cff', '#fff7ed'],
        'points' => 80,
        'minutes' => 6,
        'age' => 9,
        'roles' => ['student', 'parent', 'teacher', 'independent_learner'],
        'tags' => ['planning', 'tasks', 'routine', 'goals'],
        'outcomes' => ['Task planning', 'Prioritization', 'Routine building', 'Goal tracking'],
        'sections' => [
            ['title' => 'Build Today’s Map', 'body' => 'Turn your tasks into a clear route for the day.'],
            ['title' => 'Priority Blocks', 'body' => 'Choose what matters first and avoid overwhelm.'],
            ['title' => 'Progress Streets', 'body' => 'Check things off and keep moving through your plan.'],
        ],
    ],
    'quiz-galaxy' => [
        'name' => 'Quiz Galaxy',
        'category' => 'Quiz Universe',
        'tagline' => 'Review topics across the galaxy.',
        'description' => 'Make revision active with short quizzes, instant feedback, smart retry loops, and reward points.',
        'preview' => 'A galaxy of quick quizzes that make review feel active, clear, and rewarding.',
        'icon' => '◎',
        'accent' => '#4f46e5',
        'theme' => ['#4f46e5', '#ec4899', '#22d3ee', '#eef2ff'],
        'points' => 120,
        'minutes' => 10,
        'age' => 8,
        'roles' => ['student', 'teacher', 'independent_learner'],
        'tags' => ['quiz', 'revision', 'memory', 'practice'],
        'outcomes' => ['Memory recall', 'Exam practice', 'Topic review', 'Confidence under questions'],
        'sections' => [
            ['title' => 'Launch Pad', 'body' => 'Pick a topic and start with a tiny question set.'],
            ['title' => 'Star Questions', 'body' => 'Answer mixed questions with quick feedback.'],
            ['title' => 'Retry Orbit', 'body' => 'Revisit missed questions until they feel easy.'],
        ],
    ],
    'shapes-lab' => [
        'name' => 'Shapes Lab',
        'category' => 'STEM Lab',
        'tagline' => 'Geometry for visual thinkers.',
        'description' => 'Build visual problem-solving through shape sorting, geometry basics, patterns, and playful STEM challenges.',
        'preview' => 'A bright STEM world for shapes, patterns, and visual problem-solving.',
        'icon' => '△',
        'accent' => '#06b6d4',
        'theme' => ['#06b6d4', '#8b5cf6', '#facc15', '#ecfeff'],
        'points' => 80,
        'minutes' => 8,
        'age' => 6,
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
        'preview' => 'A memory world for cards, recall, review, and stronger study habits.',
        'icon' => '▣',
        'accent' => '#9333ea',
        'theme' => ['#9333ea', '#f97316', '#fde68a', '#faf5ff'],
        'points' => 90,
        'minutes' => 7,
        'age' => 8,
        'roles' => ['student', 'teacher', 'independent_learner'],
        'tags' => ['flashcards', 'memory', 'recall', 'review'],
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
    $main = sb_find_image($slug);
    $gallery = sb_gallery($slug, $main);

    $payload = [
        'name' => $data['name'],
        'category' => $data['category'],
        'tagline' => $data['tagline'],
        'description' => $data['description'],
        'preview_text' => $data['preview'],
        'status' => 'live',
        'icon' => $data['icon'],
        'accent' => $data['accent'],
        'hero_image' => $main,
        'image_url' => $main,
        'thumbnail_image' => $main,
        'app_image' => $main,
        'logo_image' => $main,
        'background_image' => $gallery[1] ?? $main,
        'safety_note' => 'Friendly, guided practice with clear feedback and no pressure.',
        'locked_preview_note' => 'Login to save progress, earn points, and continue your learning journey.',
        'platform_notes' => 'Start with a short practice preview, then continue your progress from your StudyBuddy dashboard.',
        'detail_cta_label' => 'Start this world',
        'points_reward' => $data['points'],
        'estimated_minutes' => $data['minutes'],
        'age_min' => $data['age'],
        'age_max' => 16,
        'age_range' => $data['age'] . '+',
        'audience_roles' => $data['roles'],
        'role_scope' => $data['roles'],
        'learning_tags' => $data['tags'],
        'learning_outcomes' => $data['outcomes'],
        'detail_sections' => $data['sections'],
        'how_it_works' => $data['sections'],
        'screenshot_urls' => $gallery,
        'visual_gallery' => $gallery,
        'gallery_images' => $gallery,
        'theme' => $data['theme'],
        'theme_colors' => $data['theme'],
        'design_tokens' => [
            'primary' => $data['theme'][0],
            'secondary' => $data['theme'][1],
            'spark' => $data['theme'][2],
            'soft' => $data['theme'][3],
        ],
        'is_web_enabled' => true,
        'is_download_enabled' => false,
        'is_featured' => in_array($slug, ['math-quest', 'reading-garden', 'focus-forest'], true),
        'is_active' => true,
        'sort_order' => $sort,
    ];

    DB::table($table)->updateOrInsert(
        ['slug' => $slug],
        sb_payload($table, $payload)
    );

    echo "✅ {$data['name']} synced with {$main}\n";
    $sort += 10;
}

echo "\nDONE ✅ StudyBuddy app data is synced from one connected source.\n";
