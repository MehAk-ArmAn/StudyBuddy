<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('saved_quests')) {
            Schema::create('saved_quests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();

                $table->string('app_slug')->default('studybuddy')->index();
                $table->string('app_title')->nullable();

                $table->string('mission_title');
                $table->text('mission_description')->nullable();

                $table->string('difficulty')->nullable();
                $table->unsignedSmallInteger('estimated_minutes')->nullable();

                $table->string('status')->default('saved')->index();
                $table->unsignedTinyInteger('progress')->default(0);
                $table->text('notes')->nullable();

                $table->string('source_url')->nullable();
                $table->json('metadata')->nullable();

                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();

                $table->timestamps();

                $table->unique(
                    ['user_id', 'app_slug', 'mission_title'],
                    'saved_quests_unique_user_app_mission'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_quests');
    }
};
