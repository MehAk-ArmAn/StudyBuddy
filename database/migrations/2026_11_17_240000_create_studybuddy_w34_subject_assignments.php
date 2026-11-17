<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('studybuddy_w34_subject_assignments')) {
            Schema::create('studybuddy_w34_subject_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('app_id')->unique();
                $table->string('primary_subject', 80);
                $table->string('learning_stage', 32)->nullable();
                $table->string('topics', 400)->nullable();
                $table->text('learning_outcome')->nullable();
                $table->timestamps();
                $table->foreign('app_id')->references('id')->on('studybuddy_mini_app_platforms')->cascadeOnDelete();
                $table->index('primary_subject');
            });
        }
    }
    // Intentionally non-destructive to published metadata and learner data.
    public function down(): void {}
};
