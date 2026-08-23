<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('studybuddy_learning_groups')) {
            Schema::create('studybuddy_learning_groups', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('owner_id')->index();
                $table->string('type', 40)->default('class');
                $table->string('name');
                $table->string('organization_name')->nullable();
                $table->string('invite_code', 40)->nullable()->index();
                $table->text('description')->nullable();
                $table->json('settings')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('studybuddy_group_members')) {
            Schema::create('studybuddy_group_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('group_id')->index();
                $table->unsignedBigInteger('owner_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('email')->nullable()->index();
                $table->string('display_name')->nullable();
                $table->string('member_role', 40)->default('student');
                $table->string('status', 40)->default('invited');
                $table->json('metrics_json')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('studybuddy_assignments')) {
            Schema::create('studybuddy_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('owner_id')->index();
                $table->unsignedBigInteger('group_id')->nullable()->index();
                $table->string('title');
                $table->string('type', 40)->default('task');
                $table->string('app_slug')->nullable()->index();
                $table->text('instructions')->nullable();
                $table->dateTime('due_at')->nullable();
                $table->integer('points_reward')->default(50);
                $table->string('status', 40)->default('assigned');
                $table->json('settings')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('studybuddy_assignment_recipients')) {
            Schema::create('studybuddy_assignment_recipients', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('assignment_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('email')->nullable()->index();
                $table->string('display_name')->nullable();
                $table->string('status', 40)->default('assigned');
                $table->integer('score')->nullable();
                $table->text('feedback')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Safe rollback: keep role dashboard data.
    }
};
