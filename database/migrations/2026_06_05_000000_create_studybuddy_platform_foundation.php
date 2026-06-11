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
            $table->string('name')->default('');
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
            $table->string('type')->default('text');
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('route_name')->nullable();
            $table->string('path')->unique();
            $table->string('title')->default('');
            $table->text('meta_description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_page_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key')->unique();
            $table->string('type')->default('section');
            $table->string('eyebrow')->default('');
            $table->string('title')->default('');
            $table->text('body')->nullable();
            $table->string('media_path')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_section_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key')->unique();
            $table->string('title')->default('');
            $table->text('body')->nullable();
            $table->string('media_path')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cms_block_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key')->unique();
            $table->string('label')->default('');
            $table->string('url')->default('');
            $table->string('style')->default('primary');
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_page_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cms_section_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key')->unique();
            $table->string('value')->default('');
            $table->string('label')->default('');
            $table->string('helper_text')->default('');
            $table->string('display_type')->default('text');
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_section_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key')->unique();
            $table->string('title')->default('');
            $table->text('body')->nullable();
            $table->string('media_path')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_media', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('path')->nullable();
            $table->string('alt_text')->default('');
            $table->string('media_type')->default('image');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_menus', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name')->default('');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_menu_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('');
            $table->string('url')->default('');
            $table->string('route_name')->nullable();
            $table->boolean('opens_new_tab')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_footer_columns', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title')->default('');
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_footer_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_footer_column_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('');
            $table->string('url')->default('');
            $table->string('route_name')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('mini_apps', function (Blueprint $table) {
            $table->string('subtitle')->default('')->after('title');
            $table->string('image_path')->nullable()->after('hero_metric');
            $table->string('video_path')->nullable()->after('image_path');
            $table->boolean('is_enabled')->default(true)->after('status');
        });

        Schema::create('mini_app_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mini_app_id')->constrained()->cascadeOnDelete();
            $table->string('title')->default('');
            $table->text('body')->nullable();
            $table->string('hero_image_path')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('mini_app_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mini_app_id')->constrained()->cascadeOnDelete();
            $table->string('title')->default('');
            $table->text('body')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('mini_app_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mini_app_id')->constrained()->cascadeOnDelete();
            $table->string('platform')->default('');
            $table->string('url')->default('');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('mini_app_web_embeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mini_app_id')->constrained()->cascadeOnDelete();
            $table->string('embed_url')->nullable();
            $table->text('fallback_text')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('mini_app_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('slug')->unique();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('display_name')->default('');
            $table->string('avatar_path')->nullable();
            $table->json('preferences')->nullable();
            $table->timestamps();
        });

        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('grade_level')->default('');
            $table->timestamps();
        });

        Schema::create('parent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('school_name')->default('');
            $table->timestamps();
        });

        Schema::create('dashboard_pages', function (Blueprint $table) {
            $table->id();
            $table->string('role')->index();
            $table->string('key')->unique();
            $table->string('title')->default('');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dashboard_page_id')->constrained()->cascadeOnDelete();
            $table->string('key')->unique();
            $table->string('title')->default('');
            $table->string('type')->default('card');
            $table->json('settings')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('dashboard_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dashboard_page_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('');
            $table->string('value')->default('');
            $table->string('helper_text')->default('');
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('dashboard_activity_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dashboard_page_id')->constrained()->cascadeOnDelete();
            $table->string('title')->default('');
            $table->text('body')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('dashboard_progress_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dashboard_page_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('');
            $table->unsignedSmallInteger('percent')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('reward_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('slug')->unique();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('reward_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reward_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->default('');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('coins_required')->default(0);
            $table->string('image_path')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('user_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reward_item_id')->constrained()->cascadeOnDelete();
            $table->timestamp('earned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_coins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('balance')->default(0);
            $table->timestamps();
        });

        Schema::create('legal_pages', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('path')->unique();
            $table->string('title')->default('');
            $table->longText('body')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('asset_references', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('path')->nullable();
            $table->string('alt_text')->default('');
            $table->string('asset_type')->default('image');
            $table->json('metadata')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'asset_references', 'legal_pages', 'user_coins', 'user_rewards', 'reward_items', 'reward_categories',
            'dashboard_progress_items', 'dashboard_activity_items', 'dashboard_stats', 'dashboard_widgets', 'dashboard_pages',
            'teacher_profiles', 'parent_profiles', 'student_profiles', 'user_profiles', 'mini_app_categories',
            'mini_app_web_embeds', 'mini_app_downloads', 'mini_app_features', 'mini_app_pages', 'cms_footer_links',
            'cms_footer_columns', 'cms_menu_items', 'cms_menus', 'cms_media', 'cms_cards', 'cms_stats', 'cms_buttons',
            'cms_blocks', 'cms_sections', 'cms_pages', 'site_settings', 'admin_users',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
