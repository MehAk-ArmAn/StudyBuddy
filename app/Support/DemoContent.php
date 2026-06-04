<?php

namespace App\Support;

use Illuminate\Support\Collection;

class DemoContent
{
    public static function miniApps(): Collection
    {
        return collect([
            (object) ['title' => 'Math Quest', 'slug' => 'math-quest', 'subject' => 'Numeracy', 'age_band' => 'Ages 7-14', 'description' => 'Pilot a crystal dolphin through asteroid equations, unlock streak portals, and collect star fragments for every confident answer.', 'card_tone' => 'cyan', 'status' => 'live', 'launch_path' => '/apps/math-quest/play', 'hero_metric' => '12 adaptive levels', 'sort_order' => 1],
            (object) ['title' => 'Word Nebula', 'slug' => 'word-nebula', 'subject' => 'Literacy', 'age_band' => 'Ages 6-13', 'description' => 'Build vocabulary constellations with glowing story prompts, grammar boosts, and reading orbit challenges.', 'card_tone' => 'violet', 'status' => 'preview', 'launch_path' => null, 'hero_metric' => '400+ word sparks', 'sort_order' => 2],
            (object) ['title' => 'Science Reef', 'slug' => 'science-reef', 'subject' => 'Science', 'age_band' => 'Ages 8-15', 'description' => 'Dive below an alien ocean to test hypotheses, scan habitats, and surface with experiment badges.', 'card_tone' => 'teal', 'status' => 'preview', 'launch_path' => null, 'hero_metric' => '24 lab missions', 'sort_order' => 3],
            (object) ['title' => 'History Hyperlane', 'slug' => 'history-hyperlane', 'subject' => 'Humanities', 'age_band' => 'Ages 9-16', 'description' => 'Jump between eras in a comet cruiser and connect timelines through evidence-based quests.', 'card_tone' => 'gold', 'status' => 'concept', 'launch_path' => null, 'hero_metric' => '8 time gates', 'sort_order' => 4],
        ]);
    }

    public static function rewards(): Collection
    {
        return collect([
            (object) ['name' => 'Moon Pebble', 'slug' => 'moon-pebble', 'description' => 'Awarded for completing a first StudyBuddy mission.', 'points_required' => 100, 'rarity' => 'common', 'icon' => '☾', 'glow_color' => '#67e8f9'],
            (object) ['name' => 'Dolphin Trail', 'slug' => 'dolphin-trail', 'description' => 'A luminous mascot trail for three-day learning streaks.', 'points_required' => 450, 'rarity' => 'rare', 'icon' => '🐬', 'glow_color' => '#38bdf8'],
            (object) ['name' => 'Nebula Crown', 'slug' => 'nebula-crown', 'description' => 'Unlocked by mastering a topic cluster with 90% confidence.', 'points_required' => 1200, 'rarity' => 'epic', 'icon' => '♛', 'glow_color' => '#c084fc'],
            (object) ['name' => 'Galaxy Key', 'slug' => 'galaxy-key', 'description' => 'A legendary pass for learners who complete every weekly quest.', 'points_required' => 2500, 'rarity' => 'legendary', 'icon' => '✦', 'glow_color' => '#f0abfc'],
        ]);
    }

    public static function dashboardCards(string $audience): Collection
    {
        return collect([
            ['primary', 'Star Path', '4 quests ready', 'Friendly next steps with mascot encouragement and bite-sized goals.', '#67e8f9', 1],
            ['primary', 'Power Meter', '82% focus', 'A glowing learner confidence panel for today’s activities.', '#a78bfa', 2],
            ['secondary', 'Exam Orbit', '6 topics mapped', 'Revision planets grouped by confidence, deadline, and momentum.', '#22d3ee', 1],
            ['secondary', 'Challenge League', 'Top 12%', 'A premium competitive panel without noisy classroom clutter.', '#f0abfc', 2],
            ['parent', 'Family Signals', '3 wins today', 'Clear progress highlights, effort trends, and reward moments.', '#38bdf8', 1],
            ['parent', 'Wellbeing Glow', 'Balanced', 'Study rhythm, breaks, and positive nudges in one calm view.', '#c084fc', 2],
            ['teacher', 'Class Galaxy', '28 learners', 'At-a-glance mastery clusters with intervention priorities.', '#67e8f9', 1],
            ['teacher', 'Assignment Beam', '5 drafts', 'Launch differentiated missions and review completion signals.', '#a78bfa', 2],
            ['admin', 'Platform Pulse', '99.9% ready', 'Operational health, content status, and audience demo shortcuts.', '#22d3ee', 1],
            ['admin', 'Content Forge', '18 modules', 'Manage mini apps, reward economies, and site copy foundations.', '#f0abfc', 2],
        ])->where(0, $audience)->map(fn (array $card) => (object) [
            'audience' => $card[0], 'title' => $card[1], 'metric' => $card[2], 'description' => $card[3], 'accent_color' => $card[4], 'sort_order' => $card[5],
        ])->values();
    }

    public static function siteContent(string $section): Collection
    {
        return collect([
            (object) ['key' => 'home.hero', 'section' => 'home', 'title' => 'A premium cosmic universe for confident learning', 'body' => 'StudyBuddy blends playful mini apps, glowing progress dashboards, and reward loops into a dark, cinematic learning world.', 'metadata' => ['eyebrow' => 'StudyBuddy Galaxy OS']],
            (object) ['key' => 'home.mascot', 'section' => 'home', 'title' => 'Meet Buddy the dolphin bookpilot', 'body' => 'A friendly dolphin-and-book mascot guides learners through challenges without feeling like a generic school template.', 'metadata' => ['mascot' => 'dolphin-book']],
            (object) ['key' => 'showcase.glass', 'section' => 'showcase', 'title' => '3D glass dashboards', 'body' => 'Layered panels, cyan-purple glow, and cosmic motion create a premium product foundation for every audience.', 'metadata' => ['theme' => 'cosmic-glass']],
            (object) ['key' => 'showcase.apps', 'section' => 'showcase', 'title' => 'Play Store style app cards', 'body' => 'Mini apps are presented as polished learning products with subject signals, status badges, and direct launch paths.', 'metadata' => ['theme' => 'app-store']],
        ])->where('section', $section)->values();
    }
}
