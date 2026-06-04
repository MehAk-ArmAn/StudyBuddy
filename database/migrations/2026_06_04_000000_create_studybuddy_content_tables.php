<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mini_apps', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subject');
            $table->string('age_band');
            $table->text('description');
            $table->string('card_tone')->default('violet');
            $table->enum('status', ['live', 'preview', 'concept'])->default('preview');
            $table->string('launch_path')->nullable();
            $table->string('hero_metric')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->unsignedInteger('points_required')->default(0);
            $table->enum('rarity', ['common', 'rare', 'epic', 'legendary'])->default('common');
            $table->string('icon')->default('✦');
            $table->string('glow_color')->default('#7dd3fc');
            $table->timestamps();
        });

        Schema::create('dashboard_cards', function (Blueprint $table) {
            $table->id();
            $table->enum('audience', ['primary', 'secondary', 'parent', 'teacher', 'admin']);
            $table->string('title');
            $table->string('metric');
            $table->text('description');
            $table->string('accent_color')->default('#a78bfa');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('site_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('section')->index();
            $table->string('title');
            $table->text('body');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_contents');
        Schema::dropIfExists('dashboard_cards');
        Schema::dropIfExists('rewards');
        Schema::dropIfExists('mini_apps');
    }
};
