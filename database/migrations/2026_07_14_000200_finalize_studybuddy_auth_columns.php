<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            if (!Schema::hasColumn('users', 'real_name')) {
                $table->string('real_name')->nullable()->after('name');
            }

            if (!Schema::hasColumn('users', 'child_emails')) {
                $table->json('child_emails')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'role_profile')) {
                $table->json('role_profile')->nullable()->after('child_emails');
            }

            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('role');
            }

            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country', 90)->nullable()->after('date_of_birth');
            }

            if (!Schema::hasColumn('users', 'guardian_email')) {
                $table->string('guardian_email', 190)->nullable()->after('country');
            }

            if (!Schema::hasColumn('users', 'organization_name')) {
                $table->string('organization_name', 190)->nullable()->after('guardian_email');
            }

            if (!Schema::hasColumn('users', 'organization_email')) {
                $table->string('organization_email', 190)->nullable()->after('organization_name');
            }

            if (!Schema::hasColumn('users', 'position_title')) {
                $table->string('position_title', 140)->nullable()->after('organization_email');
            }

            if (!Schema::hasColumn('users', 'role_verification_status')) {
                $table->string('role_verification_status', 40)->default('not_required')->after('is_admin');
            }

            if (!Schema::hasColumn('users', 'role_verification_notes')) {
                $table->text('role_verification_notes')->nullable()->after('role_verification_status');
            }

            if (!Schema::hasColumn('users', 'age_verified_at')) {
                $table->timestamp('age_verified_at')->nullable()->after('role_verification_notes');
            }

            if (!Schema::hasColumn('users', 'role_verified_at')) {
                $table->timestamp('role_verified_at')->nullable()->after('age_verified_at');
            }

            if (!Schema::hasColumn('users', 'safeguarding_agreed_at')) {
                $table->timestamp('safeguarding_agreed_at')->nullable()->after('role_verified_at');
            }

            if (!Schema::hasColumn('users', 'verification_submitted_at')) {
                $table->timestamp('verification_submitted_at')->nullable()->after('safeguarding_agreed_at');
            }

            if (!Schema::hasColumn('users', 'adult_verification_status')) {
                $table->string('adult_verification_status', 40)->default('not_required')->after('verification_submitted_at');
            }

            if (!Schema::hasColumn('users', 'adult_verification_consent_at')) {
                $table->timestamp('adult_verification_consent_at')->nullable()->after('adult_verification_status');
            }
        });

        if (Schema::hasColumn('users', 'email_verified_at')) {
            DB::table('users')->whereNull('email_verified_at')->update(['email_verified_at' => now()]);
        }
    }

    public function down(): void
    {
        // Kept intentionally safe. Auth/profile data should not be dropped on rollback.
    }
};
