<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('studybuddy_platform_settings')) {
            Schema::create('studybuddy_platform_settings', function (Blueprint $table) {
                $table->id();
                $table->string('setting_key')->unique();
                $table->string('label');
                $table->string('group_name')->default('general');
                $table->longText('setting_value')->nullable();
                $table->string('field_type')->default('text');
                $table->string('help_text')->nullable();
                $table->boolean('is_public')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('studybuddy_mini_app_platforms')) {
            Schema::create('studybuddy_mini_app_platforms', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->string('category')->default('core');
                $table->string('tagline')->nullable();
                $table->longText('description')->nullable();
                $table->string('status')->default('planned');
                $table->string('icon')->nullable();
                $table->string('accent')->default('cosmic');
                $table->string('web_play_url')->nullable();
                $table->string('ios_url')->nullable();
                $table->string('android_url')->nullable();
                $table->string('windows_url')->nullable();
                $table->string('mac_url')->nullable();
                $table->string('support_url')->nullable();
                $table->unsignedInteger('points_reward')->default(25);
                $table->unsignedInteger('estimated_minutes')->default(10);
                $table->json('learning_tags')->nullable();
                $table->boolean('is_web_enabled')->default(false);
                $table->boolean('is_download_enabled')->default(false);
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('studybuddy_point_transactions')) {
            Schema::create('studybuddy_point_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('source_type')->default('manual');
                $table->string('source_slug')->nullable();
                $table->string('title');
                $table->integer('points')->default(0);
                $table->string('status')->default('earned');
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('studybuddy_launch_checklist_items')) {
            Schema::create('studybuddy_launch_checklist_items', function (Blueprint $table) {
                $table->id();
                $table->string('area')->default('platform');
                $table->string('title');
                $table->longText('description')->nullable();
                $table->string('status')->default('todo');
                $table->string('priority')->default('medium');
                $table->string('owner_label')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        $now = now();

        $settings = [
            ['setting_key' => 'platform_name', 'label' => 'Platform Name', 'group_name' => 'brand', 'setting_value' => 'StudyBuddy', 'field_type' => 'text', 'help_text' => 'Main product name.', 'sort_order' => 1],
            ['setting_key' => 'platform_tagline', 'label' => 'Platform Tagline', 'group_name' => 'brand', 'setting_value' => 'One dashboard for learning apps, quests, points, and safe study routines.', 'field_type' => 'textarea', 'help_text' => 'Used across launch pages.', 'sort_order' => 2],
            ['setting_key' => 'launchpad_heading', 'label' => 'Launchpad Heading', 'group_name' => 'launchpad', 'setting_value' => 'Choose your learning world.', 'field_type' => 'text', 'help_text' => 'Heading for app launchpad.', 'sort_order' => 10],
            ['setting_key' => 'launchpad_intro', 'label' => 'Launchpad Intro', 'group_name' => 'launchpad', 'setting_value' => 'Play on web when available or download the right version for your device. Every app connects back to StudyBuddy points, quests, and your main dashboard.', 'field_type' => 'textarea', 'help_text' => 'Intro text for the app ecosystem page.', 'sort_order' => 11],
            ['setting_key' => 'points_policy', 'label' => 'Points Policy', 'group_name' => 'points', 'setting_value' => 'Learners earn points by starting missions, completing sessions, finishing quests, and building consistent study habits.', 'field_type' => 'textarea', 'help_text' => 'Visible points explanation.', 'sort_order' => 20],
            ['setting_key' => 'support_email', 'label' => 'Support Email', 'group_name' => 'support', 'setting_value' => 'support@studybuddy.fun', 'field_type' => 'text', 'help_text' => 'Support contact shown in templates.', 'sort_order' => 30],
            ['setting_key' => 'final_launch_note', 'label' => 'Final Launch Note', 'group_name' => 'readiness', 'setting_value' => 'Keep hosting, store listings, and browser availability aligned with each app’s release status.', 'field_type' => 'textarea', 'help_text' => 'Internal release note.', 'is_public' => false, 'sort_order' => 40],
        ];
        foreach ($settings as $row) {
            DB::table('studybuddy_platform_settings')->updateOrInsert(
                ['setting_key' => $row['setting_key']],
                array_merge($row, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        $apps = [
            ['slug'=>'math-quest','name'=>'Math Quest','category'=>'Math','tagline'=>'Turn numbers into missions.','description'=>'Practice arithmetic, logic, and problem solving through short quest-style rounds.','status'=>'planned','icon'=>'🧮','accent'=>'cyan','points_reward'=>40,'estimated_minutes'=>12,'is_web_enabled'=>false,'is_download_enabled'=>false,'is_featured'=>true,'sort_order'=>1,'learning_tags'=>json_encode(['math','practice','missions'])],
            ['slug'=>'spelling-sprint','name'=>'Spelling Sprint','category'=>'Language','tagline'=>'Race through words and vocabulary.','description'=>'Fast spelling, vocabulary, and recall games for daily language practice.','status'=>'planned','icon'=>'✍️','accent'=>'purple','points_reward'=>30,'estimated_minutes'=>8,'is_featured'=>true,'sort_order'=>2,'learning_tags'=>json_encode(['spelling','vocabulary'])],
            ['slug'=>'reading-garden','name'=>'Reading Garden','category'=>'Reading','tagline'=>'Grow your reading habit.','description'=>'Reading sessions, comprehension checks, and calm progress tracking.','status'=>'planned','icon'=>'🌱','accent'=>'green','points_reward'=>35,'estimated_minutes'=>15,'sort_order'=>3,'learning_tags'=>json_encode(['reading','comprehension'])],
            ['slug'=>'focus-forest','name'=>'Focus Forest','category'=>'Focus','tagline'=>'Build focus one session at a time.','description'=>'Timer-based focus sessions connected with streaks, reflection, and points.','status'=>'beta','icon'=>'🌲','accent'=>'emerald','points_reward'=>50,'estimated_minutes'=>20,'is_web_enabled'=>true,'web_play_url'=>'/play/focus-forest','sort_order'=>4,'learning_tags'=>json_encode(['focus','habits','timer'])],
            ['slug'=>'planner-city','name'=>'Planner City','category'=>'Planning','tagline'=>'Organize your school day.','description'=>'A playful planner for homework, revision blocks, and weekly learning goals.','status'=>'planned','icon'=>'🏙️','accent'=>'gold','points_reward'=>25,'estimated_minutes'=>10,'sort_order'=>5,'learning_tags'=>json_encode(['planning','routine'])],
            ['slug'=>'quiz-galaxy','name'=>'Quiz Galaxy','category'=>'Quiz','tagline'=>'Review topics across the stars.','description'=>'Topic quizzes, quick review rounds, and future leaderboard-ready scoring.','status'=>'planned','icon'=>'🌌','accent'=>'neon','points_reward'=>45,'estimated_minutes'=>10,'is_featured'=>true,'sort_order'=>6,'learning_tags'=>json_encode(['quiz','review'])],
            ['slug'=>'shapes-lab','name'=>'Shapes Lab','category'=>'Visual','tagline'=>'Explore shapes, space, and patterns.','description'=>'Visual geometry practice for shapes, angles, and pattern recognition.','status'=>'concept','icon'=>'🔷','accent'=>'blue','points_reward'=>30,'estimated_minutes'=>9,'sort_order'=>7,'learning_tags'=>json_encode(['geometry','visual'])],
            ['slug'=>'flashcard-castle','name'=>'Flashcard Castle','category'=>'Revision','tagline'=>'Defend your memory castle.','description'=>'Spaced flashcards, topic review, and recall practice for exams.','status'=>'planned','icon'=>'🏰','accent'=>'pink','points_reward'=>35,'estimated_minutes'=>12,'sort_order'=>8,'learning_tags'=>json_encode(['flashcards','revision'])],
        ];
        foreach ($apps as $row) {
            DB::table('studybuddy_mini_app_platforms')->updateOrInsert(
                ['slug' => $row['slug']],
                array_merge($row, ['created_at' => $now, 'updated_at' => $now])
            );
        }

        $checks = [
            ['area'=>'Content','title'=>'All public content is admin editable','description'=>'Learning Hub, paths, rewards, app ecosystem, parent/teacher/safety pages should load from admin content studio.','status'=>'done','priority'=>'high','owner_label'=>'Admin CMS','sort_order'=>1],
            ['area'=>'Apps','title'=>'Mini-app catalog is editable','description'=>'Each app has status, points, web play URL, store/download links, and visibility controls.','status'=>'done','priority'=>'high','owner_label'=>'App Launchpad','sort_order'=>2],
            ['area'=>'Rewards','title'=>'Points ledger exists','description'=>'StudyBuddy can store earned point transactions per logged-in learner.','status'=>'done','priority'=>'high','owner_label'=>'Points Wallet','sort_order'=>3],
            ['area'=>'PWA','title'=>'Installable web shell added','description'=>'Manifest and service worker are available for browser install experiments.','status'=>'doing','priority'=>'medium','owner_label'=>'Web App','sort_order'=>4],
            ['area'=>'Stores','title'=>'App store assets pending','description'=>'iOS, Android, Windows, and macOS builds need real packaged apps before links go live.','status'=>'todo','priority'=>'high','owner_label'=>'Final Distribution','sort_order'=>5],
            ['area'=>'Security','title'=>'Admin authorization hardening','description'=>'Before production, restrict admin routes to verified admin users only.','status'=>'todo','priority'=>'high','owner_label'=>'Security','sort_order'=>6],
            ['area'=>'QA','title'=>'Mobile and browser regression test','description'=>'Test dashboard, quest vault, content pages, launchpad, and logout theme reset on mobile/desktop.','status'=>'todo','priority'=>'medium','owner_label'=>'QA','sort_order'=>7],
        ];
        foreach ($checks as $row) {
            DB::table('studybuddy_launch_checklist_items')->updateOrInsert(
                ['area' => $row['area'], 'title' => $row['title']],
                array_merge($row, ['created_at' => $now, 'updated_at' => $now])
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('studybuddy_point_transactions');
        Schema::dropIfExists('studybuddy_launch_checklist_items');
        Schema::dropIfExists('studybuddy_mini_app_platforms');
        Schema::dropIfExists('studybuddy_platform_settings');
    }
};
