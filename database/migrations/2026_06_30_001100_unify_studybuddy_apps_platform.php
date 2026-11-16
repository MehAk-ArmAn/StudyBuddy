<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('studybuddy_mini_app_platforms')) {
            Schema::table('studybuddy_mini_app_platforms', function (Blueprint $table): void {
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'long_description')) {
                    $table->longText('long_description')->nullable()->after('description');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'hero_heading')) {
                    $table->string('hero_heading')->nullable()->after('long_description');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'preview_text')) {
                    $table->longText('preview_text')->nullable()->after('hero_heading');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'image_url')) {
                    $table->string('image_url')->nullable()->after('icon');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'age_range')) {
                    $table->string('age_range')->default('8+')->after('accent');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'role_scope')) {
                    $table->json('role_scope')->nullable()->after('age_range');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'learning_outcomes')) {
                    $table->json('learning_outcomes')->nullable()->after('learning_tags');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'how_it_works')) {
                    $table->json('how_it_works')->nullable()->after('learning_outcomes');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'screenshot_urls')) {
                    $table->json('screenshot_urls')->nullable()->after('how_it_works');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'safety_note')) {
                    $table->longText('safety_note')->nullable()->after('screenshot_urls');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'locked_preview_note')) {
                    $table->longText('locked_preview_note')->nullable()->after('safety_note');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'platform_notes')) {
                    $table->longText('platform_notes')->nullable()->after('locked_preview_note');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'detail_cta_label')) {
                    $table->string('detail_cta_label')->default('View app details')->after('platform_notes');
                }
            });

            $this->seedApps();
        }

        if (Schema::hasTable('studybuddy_platform_settings')) {
            $this->seedSettings();
        }

        if (Schema::hasTable('navigation_items')) {
            $this->seedNavigation();
        }
    }

    public function down(): void
    {
        // Non-destructive migration: keep content/admin data safe.
    }

    private function seedSettings(): void
    {
        $now = now();
        $settings = [
            ['setting_key' => 'apps_page_heading', 'label' => 'Apps Page Heading', 'group_name' => 'apps', 'setting_value' => 'Choose your StudyBuddy learning world.', 'field_type' => 'text', 'help_text' => 'Main heading for the unified Apps page.', 'is_public' => true, 'sort_order' => 101],
            ['setting_key' => 'apps_page_intro', 'label' => 'Apps Page Intro', 'group_name' => 'apps', 'setting_value' => 'Browse every StudyBuddy mini-app in one place. Guests can preview each world; verified learners can save quests, play web sessions, and earn points.', 'field_type' => 'textarea', 'help_text' => 'Intro text for /apps.', 'is_public' => true, 'sort_order' => 102],
            ['setting_key' => 'locked_guest_message', 'label' => 'Guest Lock Message', 'group_name' => 'apps', 'setting_value' => 'Create a free StudyBuddy account to save quests, play web sessions, and earn points.', 'field_type' => 'textarea', 'help_text' => 'Shown when guests click locked actions.', 'is_public' => true, 'sort_order' => 103],
            ['setting_key' => 'locked_verified_message', 'label' => 'Email Verification Lock Message', 'group_name' => 'apps', 'setting_value' => 'Verify your email to unlock saving, sessions, points, and progress tracking.', 'field_type' => 'textarea', 'help_text' => 'Shown to logged-in users before email verification.', 'is_public' => true, 'sort_order' => 104],
            ['setting_key' => 'default_public_theme_note', 'label' => 'Default Public Theme Note', 'group_name' => 'brand', 'setting_value' => 'The public website returns to the Cosmic Dolphin style after logout.', 'field_type' => 'textarea', 'help_text' => 'Internal reminder for public theme behavior.', 'is_public' => false, 'sort_order' => 105],
        ];

        foreach ($settings as $row) {
            DB::table('studybuddy_platform_settings')->updateOrInsert(
                ['setting_key' => $row['setting_key']],
                array_merge($row, ['updated_at' => $now, 'created_at' => $now])
            );
        }
    }

    private function seedNavigation(): void
    {
        $now = now();
        $items = [
            ['label' => 'Home', 'url' => '/', 'sort_order' => 1],
            ['label' => 'Apps', 'url' => '/apps', 'sort_order' => 2],
            ['label' => 'Learning Hub', 'url' => '/learning-hub', 'sort_order' => 3],
            ['label' => 'Learning Paths', 'url' => '/learning-paths', 'sort_order' => 4],
            ['label' => 'Parents', 'url' => '/parents-center', 'sort_order' => 5],
            ['label' => 'Teachers', 'url' => '/teacher-studio', 'sort_order' => 6],
            ['label' => 'Safety', 'url' => '/safety-support', 'sort_order' => 7],
        ];

        foreach ($items as $item) {
            DB::table('navigation_items')->updateOrInsert(
                ['label' => $item['label']],
                array_merge($item, ['is_enabled' => true, 'opens_new_tab' => false, 'updated_at' => $now, 'created_at' => $now])
            );
        }

        DB::table('navigation_items')
            ->whereIn('url', ['/app-launchpad', '/app-ecosystem', '/apps-launchpad', '/app-store'])
            ->update(['url' => '/apps', 'updated_at' => $now]);
    }

    private function seedApps(): void
    {
        $now = now();
        $apps = [
            [
                'slug' => 'math-quest',
                'name' => 'Math Quest',
                'category' => 'Math',
                'tagline' => 'Turn numbers into missions.',
                'description' => 'Practice arithmetic, logic, and problem solving through quest-style rounds.',
                'long_description' => 'Math Quest turns daily practice into short cosmic missions. Learners solve number challenges, unlock confidence, and build a stronger math habit without feeling like they are stuck in a worksheet.',
                'hero_heading' => 'Enter the number galaxy.',
                'preview_text' => 'Preview a short math mission and see how points, quests, and progress will connect once the app is live.',
                'status' => 'planned',
                'icon' => '🧮',
                'image_url' => '/assets/studybuddy-imgs/apps/app-math-quest.png',
                'accent' => 'cyan',
                'age_range' => '7+',
                'role_scope' => ['student', 'parent', 'teacher', 'independent_learner'],
                'learning_tags' => ['math', 'logic', 'practice'],
                'learning_outcomes' => ['Build faster mental math habits', 'Practice problem solving in small rounds', 'Turn revision into a repeatable mission'],
                'how_it_works' => ['Choose a mission level', 'Solve short math rounds', 'Save progress to your StudyBuddy dashboard'],
                'points_reward' => 40,
                'estimated_minutes' => 12,
                'is_web_enabled' => false,
                'is_download_enabled' => false,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'spelling-sprint',
                'name' => 'Spelling Sprint',
                'category' => 'Language',
                'tagline' => 'Race through words and vocabulary.',
                'description' => 'Fast spelling, vocabulary, and recall games for daily language practice.',
                'long_description' => 'Spelling Sprint helps learners practice spelling and vocabulary in tiny high-energy bursts. It is designed for quick wins, repeated attempts, and teacher-friendly word practice.',
                'hero_heading' => 'Sprint across the word track.',
                'preview_text' => 'Guests can preview the word-race concept. Verified learners will later save missions and earn points from real sessions.',
                'status' => 'planned',
                'icon' => '✍️',
                'image_url' => '/assets/studybuddy-imgs/apps/app-spelling-sprint.png',
                'accent' => 'purple',
                'age_range' => '7+',
                'role_scope' => ['student', 'teacher', 'independent_learner'],
                'learning_tags' => ['spelling', 'vocabulary', 'language'],
                'learning_outcomes' => ['Improve spelling accuracy', 'Build vocabulary confidence', 'Practice recall under light time pressure'],
                'how_it_works' => ['Pick a word pack', 'Race through spelling rounds', 'Review mistakes without shame'],
                'points_reward' => 30,
                'estimated_minutes' => 8,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'reading-garden',
                'name' => 'Reading Garden',
                'category' => 'Reading',
                'tagline' => 'Grow your reading habit.',
                'description' => 'Reading sessions, comprehension checks, and calm progress tracking.',
                'long_description' => 'Reading Garden creates a calm place for reading practice. Learners can grow a reading habit with short passages, reflection prompts, and future comprehension checks.',
                'hero_heading' => 'Grow one page at a time.',
                'preview_text' => 'Preview how reading missions will look before full reading packs are connected.',
                'status' => 'planned',
                'icon' => '🌱',
                'image_url' => '/assets/studybuddy-imgs/apps/app-reading-garden.png',
                'accent' => 'green',
                'age_range' => '8+',
                'role_scope' => ['student', 'parent', 'teacher', 'independent_learner'],
                'learning_tags' => ['reading', 'comprehension', 'habit'],
                'learning_outcomes' => ['Build consistent reading routines', 'Practice short comprehension reflection', 'Track reading confidence'],
                'how_it_works' => ['Choose a reading seed', 'Read a short passage', 'Reflect and save progress'],
                'points_reward' => 35,
                'estimated_minutes' => 15,
                'sort_order' => 3,
            ],
            [
                'slug' => 'focus-forest',
                'name' => 'Focus Forest',
                'category' => 'Focus',
                'tagline' => 'Build focus one session at a time.',
                'description' => 'Timer-based focus sessions connected with streaks, reflection, and points.',
                'long_description' => 'Focus Forest is the first playable StudyBuddy web shell. It helps learners start a timed focus session, finish it honestly, and earn server-controlled points in the StudyBuddy wallet.',
                'hero_heading' => 'Plant a calm focus session.',
                'preview_text' => 'Guests can preview the Focus Forest concept. Verified learners can play the demo web shell and earn points.',
                'status' => 'beta',
                'icon' => '🌲',
                'image_url' => '/assets/studybuddy-imgs/apps/app-focus-forest.png',
                'accent' => 'emerald',
                'age_range' => '8+',
                'role_scope' => ['student', 'parent', 'teacher', 'independent_learner'],
                'learning_tags' => ['focus', 'habits', 'timer'],
                'learning_outcomes' => ['Practice distraction-free focus', 'Build session consistency', 'Connect real effort to StudyBuddy points'],
                'how_it_works' => ['Start a focus timer', 'Complete the demo session', 'Earn server-controlled points'],
                'points_reward' => 50,
                'estimated_minutes' => 20,
                'is_web_enabled' => true,
                'web_play_url' => '/play/focus-forest',
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'slug' => 'planner-city',
                'name' => 'Planner City',
                'category' => 'Planning',
                'tagline' => 'Organize your school day.',
                'description' => 'A playful planner for homework, revision blocks, and weekly learning goals.',
                'long_description' => 'Planner City gives learners a friendly planning space for homework, revision blocks, weekly goals, and routine building.',
                'hero_heading' => 'Build your study city.',
                'preview_text' => 'Preview how planning missions can connect with your dashboard and weekly learning goals.',
                'status' => 'planned',
                'icon' => '🏙️',
                'image_url' => '/assets/studybuddy-imgs/apps/app-planner-city.png',
                'accent' => 'gold',
                'age_range' => '10+',
                'role_scope' => ['student', 'parent', 'teacher', 'independent_learner'],
                'learning_tags' => ['planning', 'routine', 'goals'],
                'learning_outcomes' => ['Plan revision blocks', 'Reduce homework overwhelm', 'Build weekly routines'],
                'how_it_works' => ['Create a plan', 'Break goals into blocks', 'Review your routine from the dashboard'],
                'points_reward' => 25,
                'estimated_minutes' => 10,
                'sort_order' => 5,
            ],
            [
                'slug' => 'quiz-galaxy',
                'name' => 'Quiz Galaxy',
                'category' => 'Quiz',
                'tagline' => 'Review topics across the stars.',
                'description' => 'Topic quizzes, quick review rounds, and future leaderboard-ready scoring.',
                'long_description' => 'Quiz Galaxy is the future home for topic quizzes, review packs, spaced practice, and safe challenge modes.',
                'hero_heading' => 'Launch into a review galaxy.',
                'preview_text' => 'Preview the quiz ecosystem before full subject packs go live.',
                'status' => 'planned',
                'icon' => '🌌',
                'image_url' => '/assets/studybuddy-imgs/apps/app-quiz-galaxy.png',
                'accent' => 'neon',
                'age_range' => '9+',
                'role_scope' => ['student', 'teacher', 'independent_learner'],
                'learning_tags' => ['quiz', 'review', 'recall'],
                'learning_outcomes' => ['Review subjects quickly', 'Practice recall', 'Spot weak topics'],
                'how_it_works' => ['Choose a quiz pack', 'Answer short rounds', 'Review results and save next steps'],
                'points_reward' => 45,
                'estimated_minutes' => 10,
                'is_featured' => true,
                'sort_order' => 6,
            ],
            [
                'slug' => 'shapes-lab',
                'name' => 'Shapes Lab',
                'category' => 'Visual',
                'tagline' => 'Explore shapes, space, and patterns.',
                'description' => 'Visual geometry practice for shapes, angles, and pattern recognition.',
                'long_description' => 'Shapes Lab helps visual learners explore geometry and patterns with a playful lab-style interface.',
                'hero_heading' => 'Experiment with shapes.',
                'preview_text' => 'Preview the visual learning lab before full activities are connected.',
                'status' => 'concept',
                'icon' => '🔷',
                'image_url' => '/assets/studybuddy-imgs/apps/app-shapes-lab.png',
                'accent' => 'blue',
                'age_range' => '7+',
                'role_scope' => ['student', 'teacher', 'independent_learner'],
                'learning_tags' => ['geometry', 'visual', 'patterns'],
                'learning_outcomes' => ['Recognize shapes and patterns', 'Practice spatial thinking', 'Connect visuals to math language'],
                'how_it_works' => ['Pick a lab activity', 'Explore visual puzzles', 'Save your progress'],
                'points_reward' => 30,
                'estimated_minutes' => 9,
                'sort_order' => 7,
            ],
            [
                'slug' => 'flashcard-castle',
                'name' => 'Flashcard Castle',
                'category' => 'Revision',
                'tagline' => 'Defend your memory castle.',
                'description' => 'Spaced flashcards, topic review, and recall practice for exams.',
                'long_description' => 'Flashcard Castle is a future revision system for subjects, memory decks, and repeated recall practice.',
                'hero_heading' => 'Defend your memory castle.',
                'preview_text' => 'Preview how flashcard revision will connect with quests and points.',
                'status' => 'planned',
                'icon' => '🏰',
                'image_url' => '/assets/studybuddy-imgs/apps/app-flashcard-castle.png',
                'accent' => 'pink',
                'age_range' => '10+',
                'role_scope' => ['student', 'teacher', 'independent_learner'],
                'learning_tags' => ['flashcards', 'revision', 'memory'],
                'learning_outcomes' => ['Practice active recall', 'Revise topics in short rounds', 'Build confidence before tests'],
                'how_it_works' => ['Choose a deck', 'Answer cards', 'Review what needs more practice'],
                'points_reward' => 35,
                'estimated_minutes' => 12,
                'sort_order' => 8,
            ],
        ];

        foreach ($apps as $app) {
            DB::table('studybuddy_mini_app_platforms')->updateOrInsert(
                ['slug' => $app['slug']],
                array_merge($this->prepareApp($app), ['updated_at' => $now, 'created_at' => $now])
            );
        }
    }

    private function prepareApp(array $app): array
    {
        foreach (['role_scope', 'learning_tags', 'learning_outcomes', 'how_it_works', 'screenshot_urls'] as $key) {
            if (array_key_exists($key, $app) && is_array($app[$key])) {
                $app[$key] = json_encode($app[$key]);
            }
        }

        $app['detail_cta_label'] = $app['detail_cta_label'] ?? 'View app details';
        $app['safety_note'] = $app['safety_note'] ?? 'StudyBuddy keeps this app connected to safe account rules and role-aware access.';
        $app['locked_preview_note'] = $app['locked_preview_note'] ?? 'Preview is open to everyone. Saving, playing, and points require a logged-in verified StudyBuddy account.';
        $app['platform_notes'] = $app['platform_notes'] ?? 'Web play and store links appear here when they are available.';
        $app['slug'] = Str::slug($app['slug']);
        $app['is_active'] = $app['is_active'] ?? true;
        $app['is_web_enabled'] = $app['is_web_enabled'] ?? false;
        $app['is_download_enabled'] = $app['is_download_enabled'] ?? false;
        $app['is_featured'] = $app['is_featured'] ?? false;

        return $app;
    }
};
