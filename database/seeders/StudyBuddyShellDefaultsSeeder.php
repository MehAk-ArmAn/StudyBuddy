<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudyBuddyShellDefaultsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('site_settings')) {
            return;
        }

        $settings = [
            'site_name' => 'StudyBuddy',
            'logo_text' => 'StudyBuddy',
            'site_tagline' => 'Learn • Play • Grow',
            'logo_image' => '',
            'brand_promise' => 'StudyBuddy is a safe, playful learning space created to help students, parents, teachers, and independent learners build confidence through apps, quests, points, and guided practice.',
            'footer_pill_one' => 'Explore apps',
            'footer_pill_two' => 'Build skills',
            'footer_pill_three' => 'Earn points',
            'creator_name' => 'PixelCraftsLab Studio',
            'creator_url' => 'https://pixelcraftslab.com',
            'shell_navigation_json' => json_encode([
                ['label' => 'Apps', 'url' => '/apps'],
                ['label' => 'Learning', 'url' => '/learning-hub'],
                ['label' => 'Parents', 'url' => '/parents-center'],
                ['label' => 'Teachers', 'url' => '/teacher-studio'],
                ['label' => 'Safety', 'url' => '/safety-support'],
                ['label' => 'Rewards', 'url' => '/rewards'],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'shell_footer_groups_json' => json_encode([
                'Explore' => [
                    ['label' => 'Apps', 'url' => '/apps'],
                    ['label' => 'Learning Hub', 'url' => '/learning-hub'],
                    ['label' => 'Quests', 'url' => '/my-quest'],
                    ['label' => 'Points Wallet', 'url' => '/points-wallet'],
                ],
                'Roles' => [
                    ['label' => 'Students', 'url' => '/apps?role=student'],
                    ['label' => 'Parents', 'url' => '/parents-center'],
                    ['label' => 'Teachers', 'url' => '/teacher-studio'],
                    ['label' => 'Independent Learners', 'url' => '/apps?role=independent_learner'],
                ],
                'Learning Worlds' => [
                ],
                'Community' => [
                    ['label' => 'About', 'url' => '/about'],
                    ['label' => 'Contact', 'url' => '/contact'],
                    ['label' => 'Privacy Policy', 'url' => '/privacy-policy'],
                    ['label' => 'Terms of Use', 'url' => '/terms'],
                ],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'shell_social_links_json' => json_encode([
                ['label' => 'Instagram', 'url' => ''],
                ['label' => 'YouTube', 'url' => ''],
                ['label' => 'LinkedIn', 'url' => ''],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ];

        foreach ($settings as $key => $value) {
            $payload = ['key' => $key, 'value' => $value];

            if (Schema::hasColumn('site_settings', 'updated_at')) {
                $payload['updated_at'] = now();
            }
            if (Schema::hasColumn('site_settings', 'created_at')) {
                $payload['created_at'] = now();
            }

            DB::table('site_settings')->updateOrInsert(['key' => $key], $payload);
        }

        $this->command?->info('StudyBuddy shell defaults saved.');
    }
}
