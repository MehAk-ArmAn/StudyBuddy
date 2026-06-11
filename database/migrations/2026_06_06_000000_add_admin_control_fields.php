<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mini_apps', function (Blueprint $table) {
            $table->text('short_description')->nullable()->after('slug');
            $table->string('category')->default('')->after('description');
            $table->string('grade_level')->default('')->after('age_band');
            $table->string('icon_path')->nullable()->after('grade_level');
            $table->boolean('is_featured')->default(false)->after('is_enabled');
            $table->string('start_button_label')->default('')->after('is_featured');
            $table->string('download_button_label')->default('')->after('start_button_label');
            $table->string('web_embed_url')->nullable()->after('download_button_label');
            $table->text('web_embed_empty_message')->nullable()->after('web_embed_url');
            $table->text('web_embed_code_placeholder')->nullable()->after('web_embed_empty_message');
            $table->string('google_play_url')->nullable()->after('web_embed_empty_message');
            $table->string('app_store_url')->nullable()->after('google_play_url');
            $table->string('seo_title')->default('')->after('app_store_url');
            $table->text('seo_description')->nullable()->after('seo_title');
        });

        Schema::table('mini_app_features', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('body');
            $table->boolean('is_enabled')->default(true)->after('image_path');
        });

        Schema::table('reward_items', function (Blueprint $table) {
            $table->string('rarity')->default('')->after('coins_required');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('is_enabled');
        });

        Schema::table('dashboard_widgets', function (Blueprint $table) {
            $table->string('audience_role')->default('student')->after('dashboard_page_id');
            $table->string('label')->default('')->after('title');
            $table->string('value')->default('')->after('label');
            $table->text('description')->nullable()->after('value');
            $table->string('icon_path')->nullable()->after('description');
        });

        Schema::table('legal_pages', function (Blueprint $table) {
            $table->string('slug')->default('')->after('key');
            $table->string('meta_title')->default('')->after('body');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->boolean('is_published')->default(true)->after('is_enabled');
            $table->timestamp('published_at')->nullable()->after('is_published');
        });

        Schema::table('asset_references', function (Blueprint $table) {
            $table->string('name')->default('')->after('key');
            $table->string('asset_group')->default('')->after('asset_type');
            $table->string('used_on')->default('')->after('asset_group');
        });
    }

    public function down(): void
    {
        Schema::table('asset_references', function (Blueprint $table) {
            $table->dropColumn(['name', 'asset_group', 'used_on']);
        });
        Schema::table('legal_pages', function (Blueprint $table) {
            $table->dropColumn(['slug', 'meta_title', 'meta_description', 'is_published', 'published_at']);
        });
        Schema::table('dashboard_widgets', function (Blueprint $table) {
            $table->dropColumn(['audience_role', 'label', 'value', 'description', 'icon_path']);
        });
        Schema::table('reward_items', function (Blueprint $table) {
            $table->dropColumn(['rarity', 'sort_order']);
        });
        Schema::table('mini_app_features', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'is_enabled']);
        });
        Schema::table('mini_apps', function (Blueprint $table) {
            $table->dropColumn([
                'short_description', 'category', 'grade_level', 'icon_path', 'is_featured', 'start_button_label',
                'download_button_label', 'web_embed_url', 'web_embed_empty_message', 'web_embed_code_placeholder', 'google_play_url', 'app_store_url',
                'seo_title', 'seo_description',
            ]);
        });
    }
};
