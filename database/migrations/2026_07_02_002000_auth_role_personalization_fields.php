<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'child_emails')) {
                $table->json('child_emails')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'role_profile')) {
                $table->json('role_profile')->nullable()->after('child_emails');
            }
        });
    }

    public function down(): void
    {
        // Keep data safe on rollback.
    }
};
