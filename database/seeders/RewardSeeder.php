<?php

namespace Database\Seeders;

use App\Models\Reward;
use App\Support\DemoContent;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        DemoContent::rewards()->each(fn (object $reward) => Reward::query()->updateOrCreate(['slug' => $reward->slug], [
            'name' => $reward->name,
            'description' => $reward->description,
            'points_required' => $reward->points_required,
            'rarity' => $reward->rarity === 'locked' ? 'rare' : 'common',
            'icon' => $reward->icon,
            'glow_color' => $reward->glow_color,
        ]));
    }
}
