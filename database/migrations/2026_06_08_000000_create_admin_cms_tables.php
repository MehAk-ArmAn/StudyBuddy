<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general')->index();
            $table->string('type')->default('text');
            $table->timestamps();
        });

        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->boolean('is_cta')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('footer_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('handle')->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('footer_section_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->string('key');
            $table->string('title');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->unique(['page_id', 'key']);
        });

        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_section_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->string('label');
            $table->text('value')->nullable();
            $table->string('type')->default('text');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->unique(['page_section_id', 'key']);
        });

        Schema::table('mini_apps', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('hero_metric');
            $table->string('cta_text')->default('Start')->after('image_path');
        });

        Schema::create('app_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mini_app_id')->constrained('mini_apps')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::table('rewards', function (Blueprint $table) {
            $table->string('price_text')->nullable()->after('points_required');
            $table->string('category')->default('Accessories')->after('price_text');
            $table->string('image_path')->nullable()->after('category');
            $table->string('locked_text')->nullable()->after('rarity');
            $table->string('unlocked_text')->nullable()->after('locked_text');
            $table->boolean('is_active')->default(true)->after('unlocked_text');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('is_active');
        });

        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('requirement_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('audience')->index();
            $table->string('key');
            $table->string('title');
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->string('value')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->unique(['audience', 'key']);
        });

        Schema::create('showcase_panels', function (Blueprint $table) {
            $table->id();
            $table->string('number', 4);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('mobile_preview_items', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('asset_references', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->string('type')->default('image');
            $table->text('notes')->nullable();
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_references');
        Schema::dropIfExists('mobile_preview_items');
        Schema::dropIfExists('showcase_panels');
        Schema::dropIfExists('dashboard_widgets');
        Schema::dropIfExists('badges');
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn(['price_text', 'category', 'image_path', 'locked_text', 'unlocked_text', 'is_active', 'sort_order']);
        });
        Schema::dropIfExists('app_features');
        Schema::table('mini_apps', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'cta_text']);
        });
        Schema::dropIfExists('content_blocks');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('footer_links');
        Schema::dropIfExists('footer_sections');
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('admin_users');
    }
};
