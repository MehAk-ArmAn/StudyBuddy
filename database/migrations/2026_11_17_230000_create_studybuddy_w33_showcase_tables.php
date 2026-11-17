<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('studybuddy_w33_showcases')) {
            Schema::create('studybuddy_w33_showcases', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('app_id')->unique();
                $table->string('version', 40)->nullable();
                $table->text('whats_new')->nullable();
                $table->text('why_play')->nullable();
                $table->text('highlights')->nullable();
                $table->text('privacy_summary')->nullable();
                $table->string('trailer_url', 500)->nullable();
                $table->timestamps();
                $table->foreign('app_id')->references('id')->on('studybuddy_mini_app_platforms')->cascadeOnDelete();
            });
        }
        if (! Schema::hasTable('studybuddy_w33_screenshots')) {
            Schema::create('studybuddy_w33_screenshots', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('app_id')->index();
                $table->string('path', 400);
                $table->string('title', 120)->nullable();
                $table->string('alt_text', 220)->nullable();
                $table->unsignedInteger('position')->default(0);
                $table->unsignedInteger('width');
                $table->unsignedInteger('height');
                $table->timestamps();
                $table->foreign('app_id')->references('id')->on('studybuddy_mini_app_platforms')->cascadeOnDelete();
            });
        }
        if (! Schema::hasTable('studybuddy_w33_desktop_packages')) {
            Schema::create('studybuddy_w33_desktop_packages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('app_id')->index();
                $table->string('platform', 16);
                $table->string('path', 400);
                $table->string('original_name', 240);
                $table->unsignedBigInteger('bytes');
                $table->string('sha256', 64)->nullable();
                $table->string('version', 40)->nullable();
                $table->timestamps();
                $table->unique(['app_id', 'platform']);
                $table->foreign('app_id')->references('id')->on('studybuddy_mini_app_platforms')->cascadeOnDelete();
            });
        }
    }

    // Content-safe migration: do not discard uploaded files or admin listing content on rollback.
    public function down(): void {}
};
