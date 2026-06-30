<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studybuddy_content_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('eyebrow')->nullable();
            $table->text('subtitle')->nullable();
            $table->string('hero_badge')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('primary_cta_label')->nullable();
            $table->string('primary_cta_url')->nullable();
            $table->string('secondary_cta_label')->nullable();
            $table->string('secondary_cta_url')->nullable();
            $table->json('content_blocks')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });

        Schema::create('studybuddy_content_items', function (Blueprint $table) {
            $table->id();
            $table->string('page_slug')->nullable()->index();
            $table->string('item_type')->default('card')->index();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('badge')->nullable();
            $table->string('image_path')->nullable();
            $table->string('button_label')->nullable();
            $table->string('button_url')->nullable();
            $table->json('extra')->nullable();
            $table->string('status')->default('active')->index();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('studybuddy_app_catalog_items', function (Blueprint $table) {
            $table->id();
            $table->string('app_key')->unique();
            $table->string('title');
            $table->string('category')->nullable();
            $table->string('summary')->nullable();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('available_web')->default(false);
            $table->boolean('available_ios')->default(false);
            $table->boolean('available_android')->default(false);
            $table->boolean('available_windows')->default(false);
            $table->string('web_play_url')->nullable();
            $table->string('ios_url')->nullable();
            $table->string('android_url')->nullable();
            $table->string('windows_url')->nullable();
            $table->unsignedInteger('points_reward')->default(10);
            $table->string('launch_status')->default('planned')->index();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('extra')->nullable();
            $table->timestamps();
        });

        $now = now();

        $pages = [
            [
                'slug' => 'learning-hub',
                'title' => 'Learning Hub',
                'eyebrow' => 'StudyBuddy Experience',
                'subtitle' => 'Build a focused study session, pick your mood, and turn learning into a guided mission.',
                'hero_badge' => 'Interactive planner',
                'primary_cta_label' => 'Build a study session',
                'primary_cta_url' => '#study-session-builder',
                'secondary_cta_label' => 'Open Command Center',
                'secondary_cta_url' => '/command-center',
                'sort_order' => 10,
                'content_blocks' => json_encode([
                    ['type'=>'stats','title'=>'Today at a glance','items'=>[
                        ['label'=>'Focus modes','value'=>'4'], ['label'=>'Mini missions','value'=>'8'], ['label'=>'Reward paths','value'=>'Live']
                    ]],
                    ['type'=>'interactive_plan','title'=>'Build your mini study session','description'=>'Choose a subject, time, and mood. StudyBuddy generates a tiny mission plan instantly.'],
                    ['type'=>'cards','title'=>'What learners can do here','items'=>[
                        ['icon'=>'🧠','title'=>'Pick a focus','description'=>'Choose the subject and difficulty for today.'],
                        ['icon'=>'🎮','title'=>'Open mini apps','description'=>'Jump into web-play or download-ready learning games later.'],
                        ['icon'=>'⭐','title'=>'Earn points','description'=>'Complete missions and send progress back to the dashboard.'],
                    ]],
                ]),
            ],
            [
                'slug' => 'learning-paths',
                'title' => 'Learning Paths',
                'eyebrow' => 'Role-aware journeys',
                'subtitle' => 'Different learners need different journeys. StudyBuddy organizes paths for students, parents, teachers, and independent learners.',
                'hero_badge' => '4 role paths',
                'primary_cta_label' => 'View paths',
                'primary_cta_url' => '#role-paths',
                'secondary_cta_label' => 'Create account',
                'secondary_cta_url' => '/register',
                'sort_order' => 20,
                'content_blocks' => json_encode([
                    ['type'=>'role_tabs','title'=>'Choose your StudyBuddy path','items'=>[
                        ['role'=>'Student','icon'=>'🚀','description'=>'Daily quests, mini games, rewards, focus streaks, and progress.'],
                        ['role'=>'Parent','icon'=>'🛡️','description'=>'Safety guidance, routine support, supervision, and learning visibility.'],
                        ['role'=>'Teacher','icon'=>'📚','description'=>'Lesson planning, class missions, quiz ideas, and student-friendly activity flows.'],
                        ['role'=>'Independent Learner','icon'=>'🌙','description'=>'Self-paced planning, progress tracking, and revision routines.'],
                    ]],
                    ['type'=>'steps','title'=>'How the journey works','items'=>[
                        ['title'=>'Pick a path','description'=>'Start with the correct role and learning goal.'],
                        ['title'=>'Save quests','description'=>'Add learning missions into My Quest.'],
                        ['title'=>'Track progress','description'=>'Use the Command Center to manage your learning universe.'],
                    ]],
                ]),
            ],
            [
                'slug' => 'rewards',
                'title' => 'Rewards & Points',
                'eyebrow' => 'Motivation system',
                'subtitle' => 'Turn progress into points, badges, streaks, and positive momentum.',
                'hero_badge' => 'Points simulator',
                'primary_cta_label' => 'Calculate points',
                'primary_cta_url' => '#points-simulator',
                'secondary_cta_label' => 'Open My Quest',
                'secondary_cta_url' => '/my-quest',
                'sort_order' => 30,
                'content_blocks' => json_encode([
                    ['type'=>'interactive_points','title'=>'Points simulator','description'=>'Estimate how many points a learner can earn from missions, quizzes, reading, and focus time.'],
                    ['type'=>'cards','title'=>'Reward ideas','items'=>[
                        ['icon'=>'⭐','title'=>'Quest points','description'=>'Earn points for each completed learning mission.'],
                        ['icon'=>'🔥','title'=>'Streaks','description'=>'Build momentum by learning several days in a row.'],
                        ['icon'=>'🏅','title'=>'Badges','description'=>'Unlock badges for subjects, consistency, and progress.'],
                    ]],
                ]),
            ],
            [
                'slug' => 'parents-center',
                'title' => 'Parents Center',
                'eyebrow' => 'Safe learning support',
                'subtitle' => 'A calm space for parents to understand safety, routines, progress, and how StudyBuddy supports learners.',
                'hero_badge' => 'Trust-first',
                'primary_cta_label' => 'View safety support',
                'primary_cta_url' => '/safety-support',
                'secondary_cta_label' => 'Register as parent',
                'secondary_cta_url' => '/register',
                'sort_order' => 40,
                'content_blocks' => json_encode([
                    ['type'=>'checklist','title'=>'Parent confidence checklist','items'=>[
                        'Know what your child is studying today.',
                        'Help them choose a realistic study goal.',
                        'Encourage breaks and balanced routines.',
                        'Review progress without pressure.',
                    ]],
                    ['type'=>'cards','title'=>'Parent features planned','items'=>[
                        ['icon'=>'🛡️','title'=>'Supervision tools','description'=>'Future parent dashboard for safer learner support.'],
                        ['icon'=>'📈','title'=>'Progress visibility','description'=>'Understand activity without overwhelming the learner.'],
                        ['icon'=>'💬','title'=>'Support templates','description'=>'Simple messages and routines for home learning.'],
                    ]],
                ]),
            ],
            [
                'slug' => 'teacher-studio',
                'title' => 'Teacher Studio',
                'eyebrow' => 'Classroom-ready planning',
                'subtitle' => 'Generate learner-friendly lesson outlines, activities, and mini missions for future classroom experiences.',
                'hero_badge' => 'Lesson builder',
                'primary_cta_label' => 'Build lesson outline',
                'primary_cta_url' => '#lesson-builder',
                'secondary_cta_label' => 'Register as teacher',
                'secondary_cta_url' => '/register',
                'sort_order' => 50,
                'content_blocks' => json_encode([
                    ['type'=>'interactive_lesson','title'=>'Mini lesson builder','description'=>'Enter a topic and generate a simple activity structure.'],
                    ['type'=>'cards','title'=>'Teacher tools planned','items'=>[
                        ['icon'=>'📝','title'=>'Mission templates','description'=>'Create class-friendly learning quests.'],
                        ['icon'=>'🧪','title'=>'Quiz ideas','description'=>'Turn topics into short checks for understanding.'],
                        ['icon'=>'📦','title'=>'Resource packs','description'=>'Prepare lesson cards and activity flows.'],
                    ]],
                ]),
            ],
            [
                'slug' => 'safety-support',
                'title' => 'Safety & Support Center',
                'eyebrow' => 'Trust and care',
                'subtitle' => 'Clear support information, safety guidance, and contact templates for learners, parents, and teachers.',
                'hero_badge' => 'Support hub',
                'primary_cta_label' => 'Copy support template',
                'primary_cta_url' => '#support-template',
                'secondary_cta_label' => 'Open Parents Center',
                'secondary_cta_url' => '/parents-center',
                'sort_order' => 60,
                'content_blocks' => json_encode([
                    ['type'=>'faq','title'=>'Common questions','items'=>[
                        ['question'=>'Is StudyBuddy only one app?','answer'=>'StudyBuddy is the main ecosystem. Mini apps can connect back to the dashboard for points and progress.'],
                        ['question'=>'Can parents use it?','answer'=>'Yes. Parent-facing pages and future dashboards are part of the product direction.'],
                        ['question'=>'Can teachers use it?','answer'=>'Yes. Teacher Studio is designed for classroom-friendly lesson planning and future group features.'],
                    ]],
                    ['type'=>'support_template','title'=>'Support message template','description'=>'Copy a clear support request template for help or feedback.'],
                ]),
            ],
            [
                'slug' => 'app-ecosystem',
                'title' => 'App Ecosystem',
                'eyebrow' => 'Mini apps connected to one dashboard',
                'subtitle' => 'StudyBuddy can grow into multiple separate games and learning apps that connect back to the same points, quests, and progress system.',
                'hero_badge' => 'Web play + downloads planned',
                'primary_cta_label' => 'Explore apps',
                'primary_cta_url' => '#app-catalog',
                'secondary_cta_label' => 'Open Apps page',
                'secondary_cta_url' => '/apps',
                'sort_order' => 70,
                'content_blocks' => json_encode([
                    ['type'=>'split','title'=>'Two ways to use apps','items'=>[
                        ['title'=>'Play on web','description'=>'Future web-hosted versions of mini apps can launch directly in browser.'],
                        ['title'=>'Download app/game','description'=>'Future iOS, Android, Windows, or desktop versions can be linked from the catalog.'],
                    ]],
                    ['type'=>'app_catalog','title'=>'Connected mini-app catalog','description'=>'This app list is editable from the admin Content Studio.'],
                ]),
            ],
        ];

        foreach ($pages as $page) {
            DB::table('studybuddy_content_pages')->insert(array_merge($page, [
                'is_published' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $items = [
            ['page_slug'=>'learning-hub','item_type'=>'shortcut','title'=>'Command Center','subtitle'=>'Dashboard','description'=>'Open your premium learning command center.','icon'=>'🌌','button_label'=>'Open','button_url'=>'/command-center','sort_order'=>10],
            ['page_slug'=>'learning-hub','item_type'=>'shortcut','title'=>'My Quest','subtitle'=>'Saved missions','description'=>'Track saved missions and mark progress.','icon'=>'⭐','button_label'=>'View quests','button_url'=>'/my-quest','sort_order'=>20],
            ['page_slug'=>'learning-hub','item_type'=>'shortcut','title'=>'Apps','subtitle'=>'Mini app launcher','description'=>'Explore learning games and app missions.','icon'=>'🎮','button_label'=>'Explore apps','button_url'=>'/apps','sort_order'=>30],
            ['page_slug'=>'parents-center','item_type'=>'trust','title'=>'Safe onboarding','description'=>'Role-aware registration and guardian fields help support safer learning.','icon'=>'🛡️','sort_order'=>10],
            ['page_slug'=>'teacher-studio','item_type'=>'teacher_tool','title'=>'Quick lesson flow','description'=>'Plan warm-up, activity, review, and follow-up mission.','icon'=>'📚','sort_order'=>10],
            ['page_slug'=>'rewards','item_type'=>'reward_rule','title'=>'Complete mission','description'=>'Suggested default reward for completing a mission.','icon'=>'✅','badge'=>'+25 pts','sort_order'=>10],
            ['page_slug'=>'rewards','item_type'=>'reward_rule','title'=>'Focus session','description'=>'Suggested reward for focused study time.','icon'=>'⏱️','badge'=>'+10 pts','sort_order'=>20],
        ];

        foreach ($items as $item) {
            DB::table('studybuddy_content_items')->insert(array_merge($item, [
                'is_active' => true,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $apps = [
            ['app_key'=>'math-quest','title'=>'Math Quest','category'=>'Math','summary'=>'Number missions and quick practice battles.','description'=>'A math mini app for challenges, speed rounds, and confidence building.','icon'=>'➗','available_web'=>false,'available_ios'=>false,'available_android'=>false,'available_windows'=>false,'points_reward'=>25,'launch_status'=>'planned','sort_order'=>10],
            ['app_key'=>'spelling-sprint','title'=>'Spelling Sprint','category'=>'Language','summary'=>'Fast spelling rounds and word missions.','description'=>'A spelling mini app for vocabulary, accuracy, and quick wins.','icon'=>'✏️','points_reward'=>20,'launch_status'=>'planned','sort_order'=>20],
            ['app_key'=>'reading-garden','title'=>'Reading Garden','category'=>'Reading','summary'=>'Grow progress through reading quests.','description'=>'A calm reading app for comprehension, streaks, and reflection.','icon'=>'🌱','points_reward'=>20,'launch_status'=>'planned','sort_order'=>30],
            ['app_key'=>'focus-forest','title'=>'Focus Forest','category'=>'Focus','summary'=>'Build focus sessions and routine streaks.','description'=>'A focus mini app for study timers, breaks, and habit growth.','icon'=>'🌳','points_reward'=>15,'launch_status'=>'planned','sort_order'=>40],
            ['app_key'=>'planner-city','title'=>'Planner City','category'=>'Planning','summary'=>'Plan tasks, homework, and revision routes.','description'=>'A planning mini app for schedules and study organization.','icon'=>'🏙️','points_reward'=>15,'launch_status'=>'planned','sort_order'=>50],
            ['app_key'=>'quiz-galaxy','title'=>'Quiz Galaxy','category'=>'Quiz','summary'=>'Review subjects with quiz missions.','description'=>'A quiz mini app for spaced revision and fast feedback.','icon'=>'🪐','points_reward'=>25,'launch_status'=>'planned','sort_order'=>60],
            ['app_key'=>'shapes-lab','title'=>'Shapes Lab','category'=>'Geometry','summary'=>'Explore shapes and visual puzzles.','description'=>'A geometry mini app for shapes, patterns, and spatial thinking.','icon'=>'🔷','points_reward'=>20,'launch_status'=>'planned','sort_order'=>70],
            ['app_key'=>'flashcard-castle','title'=>'Flashcard Castle','category'=>'Revision','summary'=>'Build and review flashcard decks.','description'=>'A flashcard mini app for memory, revision, and confidence.','icon'=>'🏰','points_reward'=>20,'launch_status'=>'planned','sort_order'=>80],
        ];

        foreach ($apps as $app) {
            DB::table('studybuddy_app_catalog_items')->insert(array_merge($app, [
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('studybuddy_app_catalog_items');
        Schema::dropIfExists('studybuddy_content_items');
        Schema::dropIfExists('studybuddy_content_pages');
    }
};
