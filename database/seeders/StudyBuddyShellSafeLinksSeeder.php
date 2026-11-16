<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudyBuddyShellSafeLinksSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('site_settings')) {
            return;
        }

        $settings = [
            'shell_navigation_json' => json_encode([
                ['label' => 'Home', 'url' => '/', 'roles' => ['all']],
                ['label' => 'Apps', 'url' => '/apps', 'roles' => ['all']],
                ['label' => 'Learning', 'url' => '/apps?section=learning', 'roles' => ['all']],
                ['label' => 'Parents', 'url' => '/apps?role=parent', 'roles' => ['all']],
                ['label' => 'Teachers', 'url' => '/apps?role=teacher', 'roles' => ['all']],
                ['label' => 'Safety', 'url' => '/apps?section=safety', 'roles' => ['all']],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'shell_footer_groups_json' => json_encode([
                'Explore' => [
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'Apps', 'url' => '/apps'],
                    ['label' => 'Learning Hub', 'url' => '/apps?section=learning'],
                    ['label' => 'Rewards', 'url' => '/apps?section=rewards'],
                ],
                'Roles' => [
                    ['label' => 'Students', 'url' => '/apps?role=student'],
                    ['label' => 'Parents', 'url' => '/apps?role=parent'],
                    ['label' => 'Teachers', 'url' => '/apps?role=teacher'],
                    ['label' => 'Independent Learners', 'url' => '/apps?role=independent_learner'],
                ],
                'Learning Worlds' => [
                ],
                'Account' => [
                    ['label' => 'Login', 'url' => '/login'],
                    ['label' => 'Create Account', 'url' => '/register'],
                    ['label' => 'Dashboard', 'url' => '/dashboard'],
                ],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ];

        foreach ($settings as $key => $value) {
            $payload = ['key' => $key, 'value' => $value];
            foreach (['type' => 'json', 'group' => 'shell', 'sort_order' => 10, 'is_enabled' => true] as $column => $default) {
                if (Schema::hasColumn('site_settings', $column)) {
                    $payload[$column] = $default;
                }
            }
            if (Schema::hasColumn('site_settings', 'updated_at')) $payload['updated_at'] = now();
            if (Schema::hasColumn('site_settings', 'created_at')) $payload['created_at'] = now();
            DB::table('site_settings')->updateOrInsert(['key' => $key], $payload);
        }

        $this->command?->info('StudyBuddy shell links reset to safe working URLs.');
    }
}
