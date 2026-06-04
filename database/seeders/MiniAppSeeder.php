<?php

namespace Database\Seeders;

use App\Models\MiniApp;
use Illuminate\Database\Seeder;

class MiniAppSeeder extends Seeder
{
    public function run(): void
    {
        $apps = [
            ['Math Quest', 'math-quest', 'Numeracy', 'Ages 7-14', 'Pilot a crystal dolphin through asteroid equations, unlock streak portals, and collect star fragments for every confident answer.', 'cyan', 'live', '/apps/math-quest/play', '12 adaptive levels', 1],
            ['Word Nebula', 'word-nebula', 'Literacy', 'Ages 6-13', 'Build vocabulary constellations with glowing story prompts, grammar boosts, and reading orbit challenges.', 'violet', 'preview', null, '400+ word sparks', 2],
            ['Science Reef', 'science-reef', 'Science', 'Ages 8-15', 'Dive below an alien ocean to test hypotheses, scan habitats, and surface with experiment badges.', 'teal', 'preview', null, '24 lab missions', 3],
            ['History Hyperlane', 'history-hyperlane', 'Humanities', 'Ages 9-16', 'Jump between eras in a comet cruiser and connect timelines through evidence-based quests.', 'gold', 'concept', null, '8 time gates', 4],
        ];

        foreach ($apps as $app) {
            MiniApp::query()->updateOrCreate(['slug' => $app[1]], [
                'title' => $app[0], 'subject' => $app[2], 'age_band' => $app[3], 'description' => $app[4],
                'card_tone' => $app[5], 'status' => $app[6], 'launch_path' => $app[7], 'hero_metric' => $app[8], 'sort_order' => $app[9],
            ]);
        }
    }
}
