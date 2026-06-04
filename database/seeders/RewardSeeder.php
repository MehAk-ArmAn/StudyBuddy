<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        $rewards = [
            ['Moon Pebble', 'moon-pebble', 'Awarded for completing a first StudyBuddy mission.', 100, 'common', '☾', '#67e8f9'],
            ['Dolphin Trail', 'dolphin-trail', 'A luminous mascot trail for three-day learning streaks.', 450, 'rare', '🐬', '#38bdf8'],
            ['Nebula Crown', 'nebula-crown', 'Unlocked by mastering a topic cluster with 90% confidence.', 1200, 'epic', '♛', '#c084fc'],
            ['Galaxy Key', 'galaxy-key', 'A legendary pass for learners who complete every weekly quest.', 2500, 'legendary', '✦', '#f0abfc'],
        ];

        foreach ($rewards as $reward) {
            Reward::query()->updateOrCreate(['slug' => $reward[1]], [
                'name' => $reward[0], 'description' => $reward[2], 'points_required' => $reward[3],
                'rarity' => $reward[4], 'icon' => $reward[5], 'glow_color' => $reward[6],
            ]);
        }
    }
}
