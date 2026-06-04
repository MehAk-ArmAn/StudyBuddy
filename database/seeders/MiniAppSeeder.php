<?php

namespace Database\Seeders;

use App\Models\MiniApp;
use App\Support\DemoContent;
use Illuminate\Database\Seeder;

class MiniAppSeeder extends Seeder
{
    public function run(): void
    {
        DemoContent::miniApps()->each(fn (object $app) => MiniApp::query()->updateOrCreate(['slug' => $app->slug], [
            'title' => $app->title,
            'subject' => $app->subject,
            'age_band' => $app->age_band,
            'description' => $app->description,
            'card_tone' => $app->card_tone,
            'status' => $app->status,
            'launch_path' => $app->launch_path,
            'hero_metric' => $app->hero_metric,
            'sort_order' => $app->sort_order,
        ]));
    }
}
