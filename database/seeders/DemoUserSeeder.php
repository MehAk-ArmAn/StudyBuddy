<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            ['Nova Primary', 'primary@studybuddy.test', 'primary', 'dolphin-cadet', 'KS2 Explorer', 1280],
            ['Orion Secondary', 'secondary@studybuddy.test', 'secondary', 'astro-scholar', 'GCSE Voyager', 2460],
            ['Mira Parent', 'parent@studybuddy.test', 'parent', 'guardian-orbit', 'Family Mission Control', 820],
            ['Sol Teacher', 'teacher@studybuddy.test', 'teacher', 'mentor-comet', 'Classroom Captain', 5120],
            ['Astra Admin', 'admin@studybuddy.test', 'admin', 'galaxy-architect', 'Platform Operator', 9900],
        ])->each(fn (array $user) => User::query()->updateOrCreate(
            ['email' => $user[1]],
            [
                'name' => $user[0],
                'password' => Hash::make('password'),
                'role' => $user[2],
                'avatar_style' => $user[3],
                'learning_stage' => $user[4],
                'cosmic_points' => $user[5],
            ]
        ));
    }
}
