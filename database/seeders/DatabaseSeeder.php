<?php

namespace Database\Seeders;

use App\Models\FooterItem;
use App\Models\HomepageSection;
use App\Models\MediaAsset;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@studybuddy.local'],
            [
                'name' => 'StudyBuddy Admin',
                'password' => Hash::make('ChangeMe12345!'),
                'role' => 'admin',
                'is_admin' => true,
            ]
        );

        collect([
            ['site_name', 'StudyBuddy', 'text', 'identity'],
            ['brand_name', 'StudyBuddy', 'text', 'identity'],
            ['logo_path', 'assets/studybuddy/logo-icon.png', 'image', 'identity'],
            ['favicon_path', 'assets/studybuddy/logo-icon.png', 'image', 'identity'],
            ['seo_title', 'StudyBuddy | Cosmic Learning for Curious Kids', 'text', 'seo'],
            ['seo_description', 'A premium learning homepage for families and classrooms, fully managed from the StudyBuddy admin CMS.', 'textarea', 'seo'],
            ['seo_keywords', 'kids learning, study planner, family learning, classroom support', 'text', 'seo'],
            ['homepage_meta