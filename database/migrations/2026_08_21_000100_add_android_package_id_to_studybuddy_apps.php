<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('studybuddy_mini_app_platforms')
            && ! Schema::hasColumn('studybuddy_mini_app_platforms', 'android_package_id')
        ) {
            Schema::table('studybuddy_mini_app_platforms', function (Blueprint $table): void {
                $table->string('android_package_id')->nullable()->unique()->after('android_url');
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('studybuddy_mini_app_platforms')
            && Schema::hasColumn('studybuddy_mini_app_platforms', 'android_package_id')
        ) {
            Schema::table('studybuddy_mini_app_platforms', function (Blueprint $table): void {
                $table->dropColumn('android_package_id');
            });
        }
    }
};
