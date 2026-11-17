<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('studybuddy_learning_sessions')) {
            return;
        }

        Schema::create('studybuddy_learning_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('app_slug', 120);
            $table->string('app_name', 180);
            $table->string('status', 20)->default('active');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('reflection', 280)->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studybuddy_learning_sessions');
    }
};
