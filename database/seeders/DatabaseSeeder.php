<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DemoUserSeeder::class,
            MiniAppSeeder::class,
            RewardSeeder::class,
            DashboardSeeder::class,
            SiteContentSeeder::class,
        ]);
    }
}
