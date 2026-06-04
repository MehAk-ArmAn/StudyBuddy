<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $content = [
            ['home.hero', 'home', 'A premium cosmic universe for confident learning', 'StudyBuddy blends playful mini apps, glowing progress dashboards, and reward loops into a dark, cinematic learning world.', ['eyebrow' => 'StudyBuddy Galaxy OS']],
            ['home.mascot', 'home', 'Meet Buddy the dolphin bookpilot', 'A friendly dolphin-and-book mascot guides learners through challenges without feeling like a generic school template.', ['mascot' => 'dolphin-book']],
            ['showcase.glass', 'showcase', '3D glass dashboards', 'Layered panels, cyan-purple glow, and cosmic motion create a premium product foundation for every audience.', ['theme' => 'cosmic-glass']],
            ['showcase.apps', 'showcase', 'Play Store style app cards', 'Mini apps are presented as polished learning products with subject signals, status badges, and direct launch paths.', ['theme' => 'app-store']],
        ];

        foreach ($content as $item) {
            SiteContent::query()->updateOrCreate(['key' => $item[0]], [
                'section' => $item[1], 'title' => $item[2], 'body' => $item[3], 'metadata' => $item[4],
            ]);
        }
    }
}
