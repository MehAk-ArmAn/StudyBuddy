<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('studybuddy_mini_app_platforms')) {
            return;
        }

        Schema::table('studybuddy_mini_app_platforms', function (Blueprint $table): void {
            if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'web_app_package_path')) {
                $table->string('web_app_package_path')->nullable()->after('web_play_url');
            }

            if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'web_app_entry_path')) {
                $table->string('web_app_entry_path')->nullable()->after('web_app_package_path');
            }

            if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'web_app_uploaded_at')) {
                $table->timestamp('web_app_uploaded_at')->nullable()->after('web_app_entry_path');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('studybuddy_mini_app_platforms')) {
            return;
        }

        $columns = collect(['web_app_package_path', 'web_app_entry_path', 'web_app_uploaded_at'])
            ->filter(fn (string $column): bool => Schema::hasColumn('studybuddy_mini_app_platforms', $column))
            ->all();

        if ($columns !== []) {
            Schema::table('studybuddy_mini_app_platforms', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }
};
