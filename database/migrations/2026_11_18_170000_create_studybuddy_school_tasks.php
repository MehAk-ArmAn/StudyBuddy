<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('studybuddy_school_tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('studybuddy_organizations')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 160);
            $table->text('details')->nullable();
            $table->string('lane', 24)->default('planned');
            $table->string('priority', 12)->default('normal');
            $table->date('due_on')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'lane', 'archived_at'], 'school_task_board_lookup');
        });
    }
    public function down(): void { Schema::dropIfExists('studybuddy_school_tasks'); }
};
