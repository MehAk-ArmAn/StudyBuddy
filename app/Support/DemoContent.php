<?php

namespace App\Support;

use Illuminate\Support\Collection;

class DemoContent
{
    public static function miniApps(): Collection
    {
        return collect([
            (object) ['title' => 'Math Quest', 'slug' => 'math-quest', 'subject' => 'Math', 'age_band' => 'Primary & Secondary', 'description' => 'Practice math in a fun and interactive way with glowing challenge planets.', 'card_tone' => 'violet', 'status' => 'live', 'launch_path' => '/apps/math-quest/play', 'hero_metric' => '4.9', 'image_label' => 'APP_CARD_IMAGE_MATH', 'sort_order' => 1],
            (object) ['title' => 'Spelling Sprint', 'slug' => 'spelling-sprint', 'subject' => 'English', 'age_band' => 'Primary', 'description' => 'Improve spelling and vocabulary with rocket-fast word missions.', 'card_tone' => 'blue', 'status' => 'live', 'launch_path' => null, 'hero_metric' => '4.7', 'image_label' => 'APP_CARD_IMAGE_SPELLING', 'sort_order' => 2],
            (object) ['title' => 'Reading Garden', 'slug' => 'reading-garden', 'subject' => 'Reading', 'age_band' => 'Primary', 'description' => 'Read stories and grow vocabulary inside a magical floating garden.', 'card_tone' => 'green', 'status' => 'live', 'launch_path' => null, 'hero_metric' => '4.8', 'image_label' => 'APP_CARD_IMAGE_READING', 'sort_order' => 3],
            (object) ['title' => 'Focus Forest', 'slug' => 'focus-forest', 'subject' => 'Focus', 'age_band' => 'All ages', 'description' => 'Stay focused and calm while friendly forest spirits keep time.', 'card_tone' => 'teal', 'status' => 'live', 'launch_path' => null, 'hero_metric' => '4.8', 'image_label' => 'APP_CARD_IMAGE_FOCUS', 'sort_order' => 4],
            (object) ['title' => 'Planner City', 'slug' => 'planner-city', 'subject' => 'Planning', 'age_band' => 'Secondary', 'description' => 'Organize tasks and homework with a neon city mission board.', 'card_tone' => 'cyan', 'status' => 'live', 'launch_path' => null, 'hero_metric' => '4.6', 'image_label' => 'APP_CARD_IMAGE_PLANNER', 'sort_order' => 5],
            (object) ['title' => 'Quiz Galaxy', 'slug' => 'quiz-galaxy', 'subject' => 'Quizzes', 'age_band' => 'All ages', 'description' => 'Test knowledge and earn stars with fast-paced quiz rounds.', 'card_tone' => 'gold', 'status' => 'live', 'launch_path' => null, 'hero_metric' => '4.7', 'image_label' => 'APP_CARD_IMAGE_QUIZ', 'sort_order' => 6],
            (object) ['title' => 'Shapes Lab', 'slug' => 'shapes-lab', 'subject' => 'Geometry', 'age_band' => 'Primary', 'description' => 'Learn shapes through playful geometry experiments and puzzles.', 'card_tone' => 'orange', 'status' => 'live', 'launch_path' => null, 'hero_metric' => '4.6', 'image_label' => 'APP_CARD_IMAGE_SHAPES', 'sort_order' => 7],
            (object) ['title' => 'Flashcard Castle', 'slug' => 'flashcard-castle', 'subject' => 'Memory', 'age_band' => 'All ages', 'description' => 'Create, study, and master flashcards inside a magic castle.', 'card_tone' => 'pink', 'status' => 'live', 'launch_path' => null, 'hero_metric' => '4.8', 'image_label' => 'APP_CARD_IMAGE_FLASHCARDS', 'sort_order' => 8],
        ]);
    }

    public static function rewards(): Collection
    {
        return collect([
            (object) ['name' => 'Star Cape', 'slug' => 'star-cape', 'description' => 'A sparkling cape for Buddy after your first week streak.', 'points_required' => 100, 'rarity' => 'unlocked', 'icon' => '★', 'glow_color' => '#8b5cf6'],
            (object) ['name' => 'Gold Crown', 'slug' => 'gold-crown', 'description' => 'A legendary crown for quiz champions.', 'points_required' => 150, 'rarity' => 'unlocked', 'icon' => '♛', 'glow_color' => '#f59e0b'],
            (object) ['name' => 'Rocket Pack', 'slug' => 'rocket-pack', 'description' => 'Zoom between lessons with a rocket boost.', 'points_required' => 250, 'rarity' => 'locked', 'icon' => '🚀', 'glow_color' => '#38bdf8'],
            (object) ['name' => 'Wizard Hat', 'slug' => 'wizard-hat', 'description' => 'Unlock when spelling streaks reach level ten.', 'points_required' => 300, 'rarity' => 'locked', 'icon' => '✦', 'glow_color' => '#ec4899'],
            (object) ['name' => 'Neon Hoodie', 'slug' => 'neon-hoodie', 'description' => 'A cozy cosmic hoodie for focused learners.', 'points_required' => 350, 'rarity' => 'locked', 'icon' => '◆', 'glow_color' => '#22c55e'],
            (object) ['name' => 'Planet Trail', 'slug' => 'planet-trail', 'description' => 'A tiny solar system that follows Buddy.', 'points_required' => 500, 'rarity' => 'locked', 'icon' => '●', 'glow_color' => '#a855f7'],
        ]);
    }

    public static function dashboardCards(string $audience): Collection
    {
        return collect([
            ['primary', 'Stars', '120', 'Today’s sparkle points.', '#fbbf24', 1],
            ['primary', 'Buddy Coins', '340', 'Spend coins on costumes.', '#fb923c', 2],
            ['primary', 'Streak', '6 days', 'Keep your mission streak alive.', '#f43f5e', 3],
            ['secondary', 'Level', '12', 'Star Learner rank.', '#8b5cf6', 1],
            ['secondary', 'XP', '2,350', '650 XP until next level.', '#22d3ee', 2],
            ['secondary', 'Coins', '320', 'Buddy shop balance.', '#f59e0b', 3],
            ['secondary', 'Streak', '7 days', 'Focus streak active.', '#ef4444', 4],
            ['parent', 'Study Time', '6h 45m', '+1h 20m this week.', '#22d3ee', 1],
            ['parent', 'Lessons', '28', '+8 completed.', '#8b5cf6', 2],
            ['parent', 'Quiz Score', '85%', '+12% improvement.', '#22c55e', 3],
            ['parent', 'Focus Time', '3h 20m', '+45m focused.', '#f59e0b', 4],
            ['teacher', 'Classes', '5', 'Active learning groups.', '#22d3ee', 1],
            ['teacher', 'Students', '120', 'Learners in this term.', '#8b5cf6', 2],
            ['teacher', 'Assignments', '12', 'Open missions.', '#f59e0b', 3],
            ['teacher', 'Quizzes', '8', 'Ready to review.', '#ec4899', 4],
            ['admin', 'Total Users', '12,450', '+18% this week.', '#22d3ee', 1],
            ['admin', 'Active Students', '9,230', '+9% this week.', '#22c55e', 2],
            ['admin', 'Teachers', '320', '+5% this week.', '#8b5cf6', 3],
            ['admin', 'Parents', '2,900', '+7% this week.', '#f59e0b', 4],
        ])->where(0, $audience)->map(fn (array $card) => (object) [
            'audience' => $card[0], 'title' => $card[1], 'metric' => $card[2], 'description' => $card[3], 'accent_color' => $card[4], 'sort_order' => $card[5],
        ])->values();
    }

    public static function siteContent(string $section): Collection
    {
        return collect([
            (object) ['key' => 'home.hero', 'section' => 'home', 'title' => 'Learn. Play. Grow. Your Way.', 'body' => 'A fun and safe cosmic learning universe where students can practice, play, focus, and grow with their personal study buddy.', 'metadata' => ['eyebrow' => 'StudyBuddy Universe']],
            (object) ['key' => 'showcase.landing', 'section' => 'showcase', 'title' => 'Landing preview', 'body' => 'Cinematic hero panel with mascot, stats, shortcuts, and glowing CTAs.', 'metadata' => ['label' => 'LANDING_PREVIEW_IMAGE']],
            (object) ['key' => 'showcase.apps', 'section' => 'showcase', 'title' => 'App Store preview', 'body' => 'Play Store inspired grid with search, filters, glowing covers, and start actions.', 'metadata' => ['label' => 'APP_STORE_PREVIEW_IMAGE']],
            (object) ['key' => 'showcase.mobile', 'section' => 'showcase', 'title' => 'Mobile preview', 'body' => 'Narrow responsive mockups for app, reward, and learner journeys.', 'metadata' => ['label' => 'MOBILE_PREVIEW_IMAGE']],
        ])->where('section', $section)->values();
    }
}
