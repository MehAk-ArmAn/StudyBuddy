<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Creates (or repairs) the StudyBuddy administrator account.
 *
 * Idempotent: running it repeatedly updates the same row instead of adding
 * another admin. If the account from an earlier build is still present under
 * its old address, that row is moved to the current address rather than
 * leaving two administrators behind.
 *
 * Both the address and the password can be overridden per environment with
 * STUDYBUDDY_ADMIN_EMAIL and STUDYBUDDY_ADMIN_PASSWORD.
 */
class StudyBuddyAdminSeeder extends Seeder
{
    public const DEFAULT_EMAIL = 'admin@studybuddy.local';

    public const DEFAULT_PASSWORD = 'ChangeMe12345!';

    /** Addresses used by earlier builds, adopted instead of duplicated. */
    private const LEGACY_EMAILS = [
        'admin@studybuddy.fun',
    ];

    public function run(): void
    {
        $email = (string) env('STUDYBUDDY_ADMIN_EMAIL', self::DEFAULT_EMAIL);
        $password = env('STUDYBUDDY_ADMIN_PASSWORD');
        // Never install a known public password on a production server.
        if (! $password && ! app()->environment(['local', 'testing'])) {
            if (! User::where('email', $email)->exists()) {
                throw new \RuntimeException('Set STUDYBUDDY_ADMIN_PASSWORD to a strong secret before creating a production admin.');
            }
        }
        if (! $password && app()->environment(['local', 'testing'])) {
            $password = self::DEFAULT_PASSWORD; // Only suitable for isolated local development.
        }

        $user = User::query()->firstWhere('email', $email)
            ?? $this->adoptLegacyAdmin()
            ?? new User();

        $values = [
            'name' => 'StudyBuddy Admin',
            'email' => $email,
            // Always hashed by the framework — the plain password is never stored.
            // Password is only set when creating a user (or explicitly on request).
        ];

        // This schema has grown over several migrations, so only set the
        // columns that actually exist in the database being seeded.
        $optional = [
            'real_name' => 'StudyBuddy Admin',
            'role' => 'admin',
            'is_admin' => 1,
            'email_verified_at' => now(),
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

        if (! $user->exists && $password) {
            $values['password'] = Hash::make($password);
        }
        $user->forceFill($values);
        $user->save();

        $this->command?->info('StudyBuddy admin ready: '.$email);
        if (app()->environment(['local', 'testing'])) { $this->command?->warn('Use a strong private password before any public deployment.'); }
    }

    /**
     * Reuse an administrator created by an earlier build so the site ends up
     * with one admin account, not two.
     */
    private function adoptLegacyAdmin(): ?User
    {
        return User::query()->whereIn('email', self::LEGACY_EMAILS)->first();
    }
}
