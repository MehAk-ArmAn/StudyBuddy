<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('studybuddy_learning_sessions') || ! Schema::hasColumn('studybuddy_learning_sessions', 'launch_source')) {
            throw new RuntimeException('StudyBuddy W32 requires W30 and W31 migrations first.');
        }
        if (! Schema::hasTable('studybuddy_w32_math_runs')) {
            Schema::create('studybuddy_w32_math_runs', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('learning_session_id')->nullable()->constrained('studybuddy_learning_sessions')->nullOnDelete();
                $table->string('level', 20);
                $table->string('status', 20)->default('active');
                $table->text('questions_json'); // Answers stay server-side; the Blade form receives prompts only.
                $table->unsignedTinyInteger('score')->nullable();
                $table->unsignedTinyInteger('total')->default(5);
                $table->timestamp('started_at');
                $table->timestamp('submitted_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
                $table->index(['user_id', 'submitted_at']);
            });
        }
        if (! Schema::hasTable('studybuddy_w32_achievements')) {
            Schema::create('studybuddy_w32_achievements', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('run_id')->constrained('studybuddy_w32_math_runs')->cascadeOnDelete();
                $table->string('code', 40);
                $table->timestamp('earned_at');
                $table->timestamps();
                $table->unique(['user_id', 'code']);
            });
        }
    }

    public function down(): void
    {
        // Deliberately non-destructive: source rollback must not delete private learning results.
    }
};
