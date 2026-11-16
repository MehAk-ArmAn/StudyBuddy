<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_assets', function (Blueprint $table): void {
            if (! Schema::hasColumn('media_assets', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('height');
            }
        });
    }

    public function down(): void
    {
        Schema::table('media_assets', function (Blueprint $table): void {
            if (Schema::hasColumn('media_assets', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};