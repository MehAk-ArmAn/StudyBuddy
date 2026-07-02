<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('site_settings')) {
            return;
        }

        Schema::table('site_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('site_settings', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('group');
            }

            if (!Schema::hasColumn('site_settings', 'is_enabled')) {
                $table->boolean('is_enabled')->default(true)->after('sort_order');
            }
        });
    }

    public function down(): void
    {
        // Keep admin data safe on rollback.
    }
};
