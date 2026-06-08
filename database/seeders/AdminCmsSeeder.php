<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\AssetReference;
use App\Models\Badge;
use App\Models\CmsPage;
use App\Models\DashboardWidget;
use App\Models\FooterSection;
use App\Models\MiniApp;
use App\Models\MobilePreviewItem;
use App\Models\NavigationItem;
use App\Models\Reward;
use App\Models\ShowcasePanel;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminCmsSeeder extends Seeder
{
    public function run(): void
    {
        $this->settings();
        $this->navigation();
        $this->footer();
        $this->pages();
        $this->apps();
        $this->rewards();
        $this->badges();
        $this->dashboardWidgets();
        $this->showcasePanels();
        $this->mobilePreview();
        $this->assets();
        $this->adminUser();
    }

    private function settings(): void
    {
        collect([
            ['site_name', 'StudyBuddy', 'general'],
            ['tagline', 'Learn. Play. Grow. Your Way.', 'general'],
            ['meta_title', 'StudyBuddy · The Complete Cosmic Learning Universe', 'seo'],
            ['meta_description', 'StudyBuddy is a premium cosmic learning universe for learners, parents, teachers, and admins.', 'seo'],
            ['favicon_path', 'assets/studybuddy/logo-icon.png', 'assets'],
            ['contact_email', 'hello@studybuddy.test', 'contact'],
            ['footer_tagline', 'Learn. Play. Grow. Your Way.', 'footer'],
            ['footer_copyright', 'StudyBuddy. A safe cosmic learning universe for every learner.', 'footer'],
        ])->each(fn ($row) => SiteSetting::query()->updateOrCreate(['key' => $row[0]], ['value' => $row[1], 'group' => $row[2], 'type' => 'text']));
    }

    private function navigation(): void
    {
        collect([
            ['Home', 'home', false, 10], ['Apps', 'apps.index', false, 20], ['Math Quest', 'apps.math-quest', false, 30],
            ['Rewards', 'rewards', false, 40], ['Dashboards', 'demo.primary', false, 50], ['Showcase', 'showcase', false, 60],
            ['Start Learning', 'apps.math-quest.play', true, 70],
        ])->each(fn ($row) => NavigationItem::query()->updateOrCreate(['route_name' => $row[1], 'is_cta' => $row[2]], ['label' => $row[0], 'url' => null, 'is_enabled' => true, 'sort_order' => $row[3]]));
    }

    private function footer(): void
    {
        $sections = [
            'explore' => ['Explore', [['Home','home'], ['Apps','apps.index'], ['Math Quest','apps.math-quest'], ['Showcase','showcase']]],
            'students' => ['Students', [['Primary Dashboard','demo.primary'], ['Secondary Dashboard','demo.secondary'], ['Rewards','rewards']]],
            'parents_teachers' => ['Parents & Teachers', [['Parent Dashboard','demo.parent'], ['Teacher Dashboard','demo.teacher']]],
        ];
        $order = 10;
        foreach ($sections as $handle => [$title, $links]) {
            $section = FooterSection::query()->updateOrCreate(['handle' => $handle], ['title' => $title, 'sort_order' => $order, 'is_enabled' => true]);
            foreach ($links as $i => [$label, $route]) {
                $section->links()->updateOrCreate(['route_name' => $route], ['label' => $label, 'url' => null, 'sort_order' => ($i + 1) * 10, 'is_enabled' => true]);
            }
            $order += 10;
        }
    }

    private function pages(): void
    {
        $pages = [
            'home' => ['Home', ['hero' => ['Hero', ['headline' => 'Learn. Play. Grow. Your Way.', 'subtitle' => 'A fun and safe learning universe where students can practice, play, focus, and grow with their personal study buddy.', 'primary_cta' => 'Start Learning', 'secondary_cta' => 'Explore Apps']], 'apps_preview' => ['Apps Preview', ['heading' => 'Mini Apps', 'helper' => 'Practice, read, plan, focus, and quiz in one connected universe.']]]],
            'apps' => ['Apps', ['store' => ['Apps Store', ['title' => 'Apps Store', 'subtitle' => 'Discover fun learning apps to play, practice and grow.', 'search' => 'Search apps...', 'start' => 'Start']]]],
            'math_quest' => ['Math Quest', ['hero' => ['Hero', ['title' => 'Math Quest', 'description' => 'Practice math in a fun and interactive way!', 'browser_cta' => 'Continue in Browser', 'download_cta' => 'Download App']]]],
            'math_quest_play' => ['Math Quest Play', ['hero' => ['Hero', ['headline' => 'Learn. Play. Grow. Anywhere.', 'subtitle' => 'Take StudyBuddy with you on every learning adventure.', 'app_store' => 'App Store', 'google_play' => 'Google Play']]]],
            'primary_dashboard' => ['Primary Dashboard', ['welcome' => ['Welcome', ['title' => 'Hi Zara!', 'subtitle' => 'Ready for today’s adventure?']]]],
            'secondary_dashboard' => ['Secondary Dashboard', ['welcome' => ['Welcome', ['title' => 'Welcome back, Mehak!', 'subtitle' => 'Let’s crush your goals today.']]]],
            'parent_dashboard' => ['Parent Dashboard', ['welcome' => ['Welcome', ['title' => 'Welcome, Mom!', 'subtitle' => 'Here’s how Mehak is doing this week.']]]],
            'teacher_dashboard' => ['Teacher Dashboard', ['welcome' => ['Welcome', ['title' => 'Good morning, Teacher!', 'subtitle' => 'Here’s what’s happening in your classes today.']]]],
            'rewards' => ['Rewards', ['hero' => ['Hero', ['title' => 'Customize Your Buddy', 'subtitle' => 'Make your study buddy uniquely yours! Earn coins, unlock awesome items, and show off your style.', 'save' => 'Save Changes', 'reset' => 'Reset']]]],
            'admin_demo' => ['Admin Demo', ['hero' => ['Hero', ['title' => 'Admin Dashboard (Control Everything)', 'subtitle' => 'Monitor, manage and scale your learning universe with full control.']]]],
            'showcase' => ['Showcase', ['hero' => ['Hero', ['title' => 'StudyBuddy – The Complete Cosmic Learning Universe', 'subtitle' => 'Learn. Play. Grow. Your Way.']]]],
        ];

        foreach ($pages as $key => [$title, $sections]) {
            $page = CmsPage::query()->updateOrCreate(['key' => $key], ['title' => $title, 'slug' => str_replace('_', '-', $key), 'description' => $title.' content', 'is_enabled' => true]);
            $sectionOrder = 10;
            foreach ($sections as $sectionKey => [$sectionTitle, $blocks]) {
                $section = $page->sections()->updateOrCreate(['key' => $sectionKey], ['title' => $sectionTitle, 'sort_order' => $sectionOrder, 'is_enabled' => true]);
                $blockOrder = 10;
                foreach ($blocks as $blockKey => $value) {
                    $section->blocks()->updateOrCreate(['key' => $blockKey], ['label' => Str::headline($blockKey), 'value' => $value, 'type' => strlen($value) > 90 ? 'textarea' : 'text', 'sort_order' => $blockOrder, 'is_enabled' => true]);
                    $blockOrder += 10;
                }
                $sectionOrder += 10;
            }
        }
    }

    private function apps(): void
    {
        $apps = [
            ['Math Quest','math-quest','Math','Ages 6–14','Practice math in a fun way.','app-math-quest.png','4.8',10,'/apps/math-quest'],
            ['Spelling Sprint','spelling-sprint','English','Ages 6–14','Improve spelling and vocabulary.','app-spelling-sprint.png','4.7',20,'/apps'],
            ['Reading Garden','reading-garden','Reading','Ages 6–14','Read stories and build reading skills.','app-reading-garden.png','4.8',30,'/apps'],
            ['Focus Forest','focus-forest','Mindfulness','Ages 6–14','Stay focused and calm.','app-focus-forest.png','4.8',40,'/apps'],
            ['Planner City','planner-city','Planning','Ages 8–16','Organize tasks and homework.','app-planner-city.png','4.6',50,'/apps'],
            ['Quiz Galaxy','quiz-galaxy','General','Ages 8–16','Test knowledge and earn stars.','app-quiz-galaxy.png','4.7',60,'/apps'],
            ['Shapes Lab','shapes-lab','Math','Ages 6–10','Learn shapes and their world.','app-shapes-lab.png','4.6',70,'/apps'],
            ['Flashcard Castle','flashcard-castle','General','Ages 8–16','Study anywhere with flashcards.','app-flashcard-castle.png','4.8',80,'/apps'],
        ];
        foreach ($apps as $app) {
            MiniApp::query()->updateOrCreate(['slug' => $app[1]], ['title' => $app[0], 'subject' => $app[2], 'age_band' => $app[3], 'description' => $app[4], 'card_tone' => 'violet', 'status' => 'live', 'launch_path' => $app[8], 'hero_metric' => $app[6], 'image_path' => 'assets/studybuddy/'.$app[5], 'cta_text' => 'Start', 'sort_order' => $app[7]]);
        }
    }

    private function rewards(): void
    {
        collect(['Star Cap','Wizard Hat','Astronaut Helmet','Galaxy Headband','Cool Shades','Round Glasses','Bow Tie','Neck Scarf','Hoodie','Space Jacket','Star Aura','Comet Trail','Mini Planet Pet','Rocket Pet','Floating Book','Stardust Wings'])->each(function ($name, $i) {
            Reward::query()->updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => 'Premium buddy customization item.', 'points_required' => [100,150,200,120,80,70,60,90,180,220,150,200,250,250,200,300][$i], 'price_text' => 'Buddy Coins', 'category' => ['Hats','Hats','Hats','Accessories','Glasses','Glasses','Accessories','Accessories','Outfits','Outfits','Stars','Themed','Themed','Themed','Accessories','Accessories'][$i], 'image_path' => null, 'rarity' => 'rare', 'locked_text' => 'Locked', 'unlocked_text' => 'Unlocked', 'is_active' => true, 'sort_order' => ($i + 1) * 10, 'icon' => 'item', 'glow_color' => '#8b5cf6']);
        });
    }

    private function badges(): void
    {
        collect(['Star Reader','Math Whiz','Quiz Champ','Helper','Focus Master','Story Explorer'])->each(fn ($name, $i) => Badge::query()->updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => 'Achievement badge for consistent learning.', 'image_path' => null, 'requirement_text' => 'Complete learning goals', 'is_active' => true, 'sort_order' => ($i + 1) * 10]));
    }

    private function dashboardWidgets(): void
    {
        foreach (['primary','secondary','parent','teacher','admin'] as $audience) {
            foreach (['Level','XP','Coins','Streak','Recent Activity','Weekly Progress'] as $i => $title) {
                DashboardWidget::query()->updateOrCreate(['audience' => $audience, 'key' => Str::slug($title, '_')], ['title' => $title, 'label' => $title, 'description' => 'CMS-controlled dashboard label.', 'value' => ['12','2,350','320','5 days','Latest updates','This week'][$i], 'image_path' => null, 'sort_order' => ($i + 1) * 10, 'is_enabled' => true]);
            }
        }
    }

    private function showcasePanels(): void
    {
        collect(['Landing Page','App Store','App Portal','Primary Dashboard','Secondary Dashboard','Parent Dashboard','Teacher Dashboard','Buddy Customization','Mobile App Preview','Admin Dashboard'])->each(fn ($title, $i) => ShowcasePanel::query()->updateOrCreate(['number' => str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)], ['title' => $title, 'description' => 'Compact CMS-controlled product panel.', 'image_path' => null, 'sort_order' => ($i + 1) * 10, 'is_enabled' => true]));
    }

    private function mobilePreview(): void
    {
        foreach (['features' => ['Daily mission','Rewards','Progress'], 'phone_screens' => ['Home screen','Quest screen','Rewards screen'], 'bottom_strip' => ['Safe learning','Works anywhere','Made for growth']] as $group => $items) {
            foreach ($items as $i => $title) {
                MobilePreviewItem::query()->updateOrCreate(['group' => $group, 'title' => $title], ['description' => 'CMS-controlled mobile preview text.', 'image_path' => null, 'sort_order' => ($i + 1) * 10, 'is_enabled' => true]);
            }
        }
    }

    private function assets(): void
    {
        collect(['logo-icon.png','hero-dolphin-book.png','app-math-quest.png','app-spelling-sprint.png','app-reading-garden.png','app-focus-forest.png','app-planner-city.png','app-quiz-galaxy.png','app-shapes-lab.png','app-flashcard-castle.png','planet-ringed-lg.png','planet-purple-lg.png','sparkles-pack.png'])->each(fn ($file) => AssetReference::query()->updateOrCreate(['path' => 'assets/studybuddy/'.$file], ['name' => Str::headline(pathinfo($file, PATHINFO_FILENAME)), 'type' => 'image', 'notes' => 'Raw StudyBuddy PNG asset path.', 'is_required' => true]));
    }

    private function adminUser(): void
    {
        $email = env('STUDYBUDDY_ADMIN_EMAIL');
        $password = env('STUDYBUDDY_ADMIN_PASSWORD');
        if ($email && $password) {
            AdminUser::query()->updateOrCreate(['email' => $email], ['name' => env('STUDYBUDDY_ADMIN_NAME', 'StudyBuddy Admin'), 'password' => Hash::make($password), 'is_active' => true]);
        }
    }
}
