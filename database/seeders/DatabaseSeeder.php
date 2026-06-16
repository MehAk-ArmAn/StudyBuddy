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
            ['email' => 'admin'.'@'.'studybuddy.local'],
            ['name' => 'StudyBuddy Admin', 'password' => Hash::make('Change'.'Me12345!'), 'role' => 'admin', 'is_admin' => true]
        );

        $settings = [
            ['site_name','StudyBuddy','text','identity'], ['brand_name','StudyBuddy','text','identity'],
            ['logo_path','assets