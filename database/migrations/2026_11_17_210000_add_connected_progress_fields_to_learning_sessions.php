<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('studybuddy_learning_sessions')) {
            throw new RuntimeException('Install and migrate StudyBuddy W30 before W31.');
        }
        // W30's private session table stays the source of truth. These are
        // additive and deliberately NOT scores, mastery or rewarded points.
        if (! Schema::hasColumn('studybuddy_learning_sessions', 'launch_source')) {
            Schema::table('studybuddy_learning_sessions', function (Blueprint $table): void {
                $table->string('launch_source', 24)->nullable();
            });
        }
        if (! Schema::hasColumn('studybuddy_learning_sessions', 'observed_seconds')) {
            Schema::table('studybuddy_learning_sessions', function (Blueprint $table): void {
                $table->unsignedInteger('observed_seconds')->default(0);
            });
        }
        if (! Schema::hasColumn('studybuddy_learning_sessions', 'last_observed_at')) {
            Schema::table('studybuddy_learning_sessions', function (Blueprint $table): void {
                $table->timestamp('last_observed_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive: W31 source rollback must not erase
        // learning history or observation data already recorded for learners.
    }
};
