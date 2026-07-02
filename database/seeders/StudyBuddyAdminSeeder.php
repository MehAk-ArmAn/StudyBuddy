<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class StudyBuddyAdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('STUDYBUDDY_ADMIN_PASSWORD', 'StudyBuddyAdmin@2026!');

        $values = [
            'name' => 'StudyBuddy Admin',
            'email' => 'admin@studybuddy.fun',
            'password' => Hash::make($password),
        ];

        $optional = [
            'real_name' => 'StudyBuddy Admin',
            'role' => 'admin',
            'is_admin' => 1,
            'email_verified_at' => now(),
            'country' => 'UAE',
            'learning_stage' => 'Platform admin',
            'safe_use_confirmed' => 1,
            'accuracy_confirmed' => 1,
            'adult_verification_status' => 'approved',
            'role_verification_status' => 'approved',
        ];

        foreach ($optional as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $values[$column] = $value;
            }
        }

        $user = User::firstOrNew(['email' => 'admin@studybuddy.fun']);
        $user->forceFill($values);
        $user->save();

        $this->command?->info('StudyBuddy admin ready: admin@studybuddy.fun / ' . $password);
    }
}
