<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StudyBuddyPublishContentSeeder extends Seeder
{
    private string $logo = 'assets/studybuddy-imgs/brand/logo-icon.png';

    public function run(): void
    {
        $this->siteSettings();
        $this->navigation();
        $this->footer();
        $this->home();
        $this->miniApps();
        $this->platform();

        $this->command?->info('StudyBuddy publish-ready content and brand settings seeded.');
    }

    private function siteSettings(): void
    {
        $this->settings([
            'site_name' => 'StudyBuddy',
            'logo_text' => 'StudyBuddy',
            'site_tagline' => 'Learn. Play. Grow. Your Way.',
            'logo_image' => $this->logo,
            'site_logo' => $this->logo,
            'brand_promise' => 'StudyBuddy helps every learner build confidence through playful apps, guided routines, safe family tools, and progress that feels rewarding.',
            'footer_text' => 'A safe, playful learning universe for students, parents, teachers, and independent learners.',
            'footer_pill_one' => 'Explore apps',
            'footer_pill_two' => 'Build skills',
            'footer_pill_three' => 'Earn points',
            'support_email' => 'support@studybuddy.fun',
            'contact_email' => 'support@studybuddy.fun',
            'creator_name' => 'PixelCraftsLab Studio',
            'creator_url' => 'https://pixelcraftslab.com',
            'shell_navigation_json' => json_encode([
                ['label' => 'Home', 'url' => '/', 'roles' => ['all']],
                ['label' => 'Apps', 'url' => '/apps', 'roles' => ['all']],
                ['label' => 'Learning', 'url' => '/learning-hub', 'roles' => ['all']],
                ['label' => 'Parents', 'url' => '/parents-center', 'roles' => ['all']],
                ['label' => 'Teachers', 'url' => '/teacher-studio', 'roles' => ['all']],
                ['label' => 'Safety', 'url' => '/safety-support', 'roles' => ['all']],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'shell_footer_groups_json' => json_encode([
                'Explore' => [
                    ['label' => 'Apps', 'url' => '/apps'],
                    ['label' => 'Learning Hub', 'url' => '/learning-hub'],
                    ['label' => 'Rewards', 'url' => '/rewards'],
                    ['label' => 'Dashboard', 'url' => '/dashboard'],
                ],
                'Learning Worlds' => [
                    ['label' => 'Math Quest', 'url' => '/apps/math-quest'],
                    ['label' => 'Spelling Sprint', 'url' => '/apps/spelling-sprint'],
                    ['label' => 'Reading Garden', 'url' => '/apps/reading-garden'],
                    ['label' => 'Focus Forest', 'url' => '/apps/focus-forest'],
                    ['label' => 'Quiz Galaxy', 'url' => '/apps/quiz-galaxy'],
                    ['label' => 'Planner City', 'url' => '/apps/planner-city'],
                ],
                'For Every Role' => [
                    ['label' => 'Students', 'url' => '/apps?role=student'],
                    ['label' => 'Parents', 'url' => '/parents-center'],
                    ['label' => 'Teachers', 'url' => '/teacher-studio'],
                    ['label' => 'Independent Learners', 'url' => '/apps?role=independent_learner'],
                ],
                'Trust & Support' => [
                    ['label' => 'Safety Promise', 'url' => '/safety-support'],
                    ['label' => 'Contact Support', 'url' => 'mailto:support@studybuddy.fun'],
                    ['label' => 'Privacy First', 'url' => '/privacy-policy'],
                    ['label' => 'Data Deletion', 'url' => '/data-deletion'],
                ],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'shell_social_links_json' => json_encode([
                ['label' => 'Instagram', 'url' => ''],
                ['label' => 'YouTube', 'url' => ''],
                ['label' => 'LinkedIn', 'url' => ''],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);
    }

    private function navigation(): void
    {
        if (!Schema::hasTable('navigation_items')) return;
        $items = [
            ['Home', '/', 10],
            ['Apps', '/apps', 20],
            ['Learning', '/learning-hub', 30],
            ['Parents', '/parents-center', 40],
            ['Teachers', '/teacher-studio', 50],
            ['Safety', '/safety-support', 60],
        ];
        foreach ($items as [$label, $url, $sort]) {
            DB::table('navigation_items')->updateOrInsert($this->identity('navigation_items', $label), $this->cols('navigation_items', [
                'label' => $label, 'title' => $label, 'name' => $label, 'url' => $url, 'href' => $url,
                'sort_order' => $sort, 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now(),
            ]));
        }
    }

    private function footer(): void
    {
        if (!Schema::hasTable('footer_items')) return;
        $groups = [
            'Explore' => [['Apps','/apps'], ['Learning Hub','/learning-hub'], ['Rewards','/rewards'], ['Dashboard','/dashboard']],
            'Learning Worlds' => [['Math Quest','/apps/math-quest'], ['Spelling Sprint','/apps/spelling-sprint'], ['Reading Garden','/apps/reading-garden'], ['Focus Forest','/apps/focus-forest'], ['Quiz Galaxy','/apps/quiz-galaxy'], ['Planner City','/apps/planner-city']],
            'For Every Role' => [['Students','/apps?role=student'], ['Parents','/parents-center'], ['Teachers','/teacher-studio'], ['Independent Learners','/apps?role=independent_learner']],
            'Trust & Support' => [['Safety Promise','/safety-support'], ['Contact Support','mailto:support@studybuddy.fun'], ['Privacy First','/privacy-policy'], ['Data Deletion','/data-deletion']],
        ];
        $sort = 10;
        foreach ($groups as $group => $links) {
            foreach ($links as [$label, $url]) {
                DB::table('footer_items')->updateOrInsert($this->identity('footer_items', $group.' '.$label), $this->cols('footer_items', [
                    'label' => $label, 'title' => $label, 'name' => $label, 'group' => $group, 'group_name' => $group,
                    'url' => $url, 'href' => $url, 'sort_order' => $sort, 'is_enabled' => true,
                    'created_at' => now(), 'updated_at' => now(),
                ]));
                $sort += 10;
            }
        }
    }

    private function home(): void
    {
        if (!Schema::hasTable('homepage_sections')) return;
        $sections = [
            ['hero', 'StudyBuddy Learning Universe', 'Learn. Play. Grow. Your Way.', 'A safe, playful platform where students build skills through mini apps, parents support progress, teachers guide practice, and independent learners stay motivated.', 'Explore Apps', '/apps', $this->logo, 10],
            ['apps-preview', 'Mini Apps', 'One dashboard. Many learning worlds.', 'Every app has a clear purpose, friendly feedback, and progress that connects back to the StudyBuddy dashboard.', 'View App Universe', '/apps', 'assets/studybuddy-imgs/hero/hero-dolphin-book.png', 20],
            ['role-guidance', 'Personalized By Role', 'Different people need different tools.', 'StudyBuddy adapts the experience for students, parents, teachers, and independent learners without making the interface confusing.', 'Choose Your Role', '/register', 'assets/studybuddy-imgs/homepage-paths/path-apps.png', 30],
            ['safety-promise', 'Safety Promise', 'Built for confidence, care, and clarity.', 'StudyBuddy keeps actions understandable, uses role-based access, and makes support easy to find so learners and adults always know what is happening.', 'Review Safety Tools', '/safety-support', 'assets/studybuddy-imgs/homepage-paths/path-support.png', 40],
        ];
        foreach ($sections as [$slug, $eyebrow, $title, $subtitle, $button, $url, $image, $sort]) {
            DB::table('homepage_sections')->updateOrInsert($this->identity('homepage_sections', $slug), $this->cols('homepage_sections', [
                'slug' => $slug, 'key' => $slug, 'type' => $slug, 'eyebrow' => $eyebrow, 'title' => $title,
                'subtitle' => $subtitle, 'description' => $subtitle, 'button_label' => $button, 'button_url' => $url,
                'image_path' => $image, 'sort_order' => $sort, 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now(),
            ]));
        }
    }

    private function miniApps(): void
    {
        $table = Schema::hasTable('studybuddy_mini_app_platforms') ? 'studybuddy_mini_app_platforms' : (Schema::hasTable('study_buddy_mini_app_platforms') ? 'study_buddy_mini_app_platforms' : null);
        if (!$table) return;
        $apps = [
            ['Math Quest', 'math-quest', 'Numbers become missions. Practice arithmetic, problem solving, and confidence through short challenges.', 'student', 'Math', 'assets/studybuddy-imgs/02_apps/math-quest/01_app-icon/math-quest_main-icon.png', true],
            ['Spelling Sprint', 'spelling-sprint', 'Fast, friendly spelling practice with word rounds, retries, and momentum-building feedback.', 'student', 'Language', 'assets/studybuddy-imgs/02_apps/spelling-sprint/01_app-icon/spelling-sprint_main-icon.png', true],
            ['Reading Garden', 'reading-garden', 'A calm reading world for stories, vocabulary, reflection, and fluency growth.', 'student', 'Reading', 'assets/studybuddy-imgs/02_apps/reading-garden/01_app-icon/reading-garden_main-icon.png', true],
            ['Focus Forest', 'focus-forest', 'Build healthy study rhythms with timers, streaks, gentle breaks, and focus-friendly routines.', 'all', 'Focus', 'assets/studybuddy-imgs/02_apps/focus-forest/01_app-icon/focus-forest_main-icon.png', true],
            ['Planner City', 'planner-city', 'Turn homework, revision, and personal goals into a simple plan learners can actually follow.', 'independent_learner', 'Planning', 'assets/studybuddy-imgs/02_apps/planner-city/01_app-icon/planner-city_main-icon.png', false],
            ['Quiz Galaxy', 'quiz-galaxy', 'Review topics with quick quizzes, points, retry loops, and clear next steps.', 'all', 'Review', 'assets/studybuddy-imgs/02_apps/quiz-galaxy/01_app-icon/quiz-galaxy_main-icon.png', true],
            ['Shapes Lab', 'shapes-lab', 'Explore patterns, geometry, colors, and spatial thinking through visual play.', 'student', 'STEM', 'assets/studybuddy-imgs/02_apps/shapes-lab/01_app-icon/shapes-lab_main-icon.png', false],
            ['Flashcard Castle', 'flashcard-castle', 'Create study decks, practice recall, and protect knowledge like treasures in a castle.', 'all', 'Memory', 'assets/studybuddy-imgs/02_apps/flashcard-castle/01_app-icon/flashcard-castle_main-icon.png', false],
        ];
        $sort = 10;
        foreach ($apps as [$name, $slug, $description, $role, $category, $image, $featured]) {
            DB::table($table)->updateOrInsert(Schema::hasColumn($table, 'slug') ? ['slug' => $slug] : ['name' => $name], $this->cols($table, [
                'name' => $name, 'title' => $name, 'slug' => $slug, 'description' => $description,
                'short_description' => $description, 'summary' => $description, 'category' => $category,
                'role' => $role, 'target_role' => $role, 'audience' => $role, 'image_path' => $image,
                'icon_path' => $image, 'status' => 'web-ready', 'web_url' => '/apps/'.$slug, 'play_url' => '/apps/'.$slug,
                'points_reward' => $featured ? 120 : 80, 'points' => $featured ? 120 : 80,
                'is_featured' => $featured, 'is_enabled' => true, 'sort_order' => $sort,
                'created_at' => now(), 'updated_at' => now(),
            ]));
            $sort += 10;
        }
    }

    private function platform(): void
    {
        if (Schema::hasTable('studybuddy_platform_settings')) {
            $rows = [
                ['Brand', 'Promise', 'brand_promise', 'Learn. Play. Grow. Your Way.'],
                ['Experience', 'Guest Preview', 'guest_preview_message', 'Guests can explore the app universe. Dashboards, points, and saved quests unlock after signup.'],
                ['Safety', 'Role Access', 'role_access_message', 'Role-based access keeps student, parent, teacher, and independent learner tools separated.'],
                ['Launch', 'Readiness', 'launch_message', 'StudyBuddy is being prepared as a polished, accessible, family-friendly learning platform.'],
            ];
            $sort = 10;
            foreach ($rows as [$group, $label, $key, $value]) {
                DB::table('studybuddy_platform_settings')->updateOrInsert(Schema::hasColumn('studybuddy_platform_settings', 'key') ? ['key' => $key] : ['label' => $label], $this->cols('studybuddy_platform_settings', [
                    'group_name' => $group, 'label' => $label, 'key' => $key, 'value' => $value, 'sort_order' => $sort, 'is_enabled' => true, 'created_at' => now(), 'updated_at' => now(),
                ]));
                $sort += 10;
            }
        }
    }

    private function settings(array $settings): void
    {
        if (!Schema::hasTable('site_settings')) return;
        $sort = 10;
        foreach ($settings as $key => $value) {
            DB::table('site_settings')->updateOrInsert(['key' => $key], $this->cols('site_settings', [
                'key' => $key, 'value' => (string) $value, 'type' => str_ends_with($key, '_json') ? 'json' : 'text',
                'group' => str_starts_with($key, 'shell_') ? 'shell' : 'brand', 'sort_order' => $sort, 'is_enabled' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]));
            $sort += 10;
        }
    }

    private function cols(string $table, array $payload): array
    {
        return collect($payload)->filter(fn ($v, $k) => Schema::hasColumn($table, $k))->all();
    }

    private function identity(string $table, string $label): array
    {
        if (Schema::hasColumn($table, 'slug')) return ['slug' => Str::slug($label)];
        if (Schema::hasColumn($table, 'key')) return ['key' => Str::slug($label)];
        if (Schema::hasColumn($table, 'label')) return ['label' => $label];
        if (Schema::hasColumn($table, 'title')) return ['title' => $label];
        if (Schema::hasColumn($table, 'name')) return ['name' => $label];
        return ['id' => 0];
    }
}
