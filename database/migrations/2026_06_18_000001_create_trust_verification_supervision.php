<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'real_name')) {
                $table->string('real_name')->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('email_verified_at');
            }
            if (! Schema::hasColumn('users', 'country')) {
                $table->string('country', 90)->nullable()->after('date_of_birth');
            }
            if (! Schema::hasColumn('users', 'guardian_email')) {
                $table->string('guardian_email')->nullable()->after('country');
            }
            if (! Schema::hasColumn('users', 'organization_name')) {
                $table->string('organization_name')->nullable()->after('learning_stage');
            }
            if (! Schema::hasColumn('users', 'organization_email')) {
                $table->string('organization_email')->nullable()->after('organization_name');
            }
            if (! Schema::hasColumn('users', 'position_title')) {
                $table->string('position_title')->nullable()->after('organization_email');
            }
            if (! Schema::hasColumn('users', 'role_verification_status')) {
                $table->string('role_verification_status', 40)->default('not_required')->after('is_admin');
            }
            if (! Schema::hasColumn('users', 'role_verification_notes')) {
                $table->text('role_verification_notes')->nullable()->after('role_verification_status');
            }
            if (! Schema::hasColumn('users', 'age_verified_at')) {
                $table->timestamp('age_verified_at')->nullable()->after('role_verification_notes');
            }
            if (! Schema::hasColumn('users', 'role_verified_at')) {
                $table->timestamp('role_verified_at')->nullable()->after('age_verified_at');
            }
            if (! Schema::hasColumn('users', 'safeguarding_agreed_at')) {
                $table->timestamp('safeguarding_agreed_at')->nullable()->after('role_verified_at');
            }
            if (! Schema::hasColumn('users', 'verification_submitted_at')) {
                $table->timestamp('verification_submitted_at')->nullable()->after('safeguarding_agreed_at');
            }
        });

        if (! Schema::hasTable('account_connections')) {
            Schema::create('account_connections', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('target_id')->constrained('users')->cascadeOnDelete();
                $table->string('type', 40);
                $table->string('status', 40)->default('pending');
                $table->string('requested_by_role', 40)->nullable();
                $table->json('permissions')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->timestamps();

                $table->unique(['requester_id', 'target_id', 'type'], 'account_connection_unique');
                $table->index(['target_id', 'status']);
                $table->index(['requester_id', 'type', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('account_connections');

        Schema::table('users', function (Blueprint $table): void {
            foreach ([
                'verification_submitted_at',
                'safeguarding_agreed_at',
                'role_verified_at',
                'age_verified_at',
                'role_verification_notes',
                'role_verification_status',
                'position_title',
                'organization_email',
                'organization_name',
                'guardian_email',
                'country',
                'date_of_birth',
                'real_name',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
