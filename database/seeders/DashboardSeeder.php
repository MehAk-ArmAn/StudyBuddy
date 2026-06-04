<?php

namespace Database\Seeders;

use App\Models\DashboardCard;
use Illuminate\Database\Seeder;

class DashboardSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [
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
        ];

        foreach ($cards as $card) {
            DashboardCard::query()->updateOrCreate(
                ['audience' => $card[0], 'title' => $card[1]],
                ['metric' => $card[2], 'description' => $card[3], 'accent_color' => $card[4], 'sort_order' => $card[5]]
            );
        }
    }
}
