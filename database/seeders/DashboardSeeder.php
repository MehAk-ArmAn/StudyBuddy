<?php

namespace Database\Seeders;

use App\Models\DashboardCard;
use App\Support\DemoContent;
use Illuminate\Database\Seeder;

class DashboardSeeder extends Seeder
{
    public function run(): void
    {
        collect(['primary', 'secondary', 'parent', 'teacher', 'admin'])->each(function (string $audience): void {
            DemoContent::dashboardCards($audience)->each(fn (object $card) => DashboardCard::query()->updateOrCreate(
                ['audience' => $audience, 'title' => $card->title],
                ['metric' => $card->metric, 'description' => $card->description, 'accent_color' => $card->accent_color, 'sort_order' => $card->sort_order]
            ));
        });
    }
}
