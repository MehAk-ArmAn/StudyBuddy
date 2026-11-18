<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('studybuddy_school_conversations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('studybuddy_organizations')->cascadeOnDelete();
            $table->string('conversation_key', 110);
            $table->string('kind', 12); // channel | direct
            $table->string('title', 90);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('participant_a')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('participant_b')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['organization_id', 'conversation_key'], 'sb_p11c_unique_school_conversation');
        });
        Schema::create('studybuddy_school_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('studybuddy_organizations')->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained('studybuddy_school_conversations')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body')->nullable();
            $table->string('attachment_path', 255)->nullable();
            $table->string('attachment_name', 190)->nullable();
            $table->string('attachment_mime', 90)->nullable();
            $table->unsignedInteger('attachment_size')->nullable();
            $table->timestamp('hidden_at')->nullable();
            $table->foreignId('hidden_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['conversation_id', 'id']);
        });
        Schema::create('studybuddy_school_message_reads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->constrained('studybuddy_school_conversations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('last_message_id')->default(0);
            $table->timestamp('read_at')->nullable();
            $table->unique(['conversation_id', 'user_id'], 'sb_p11c_read_unique');
        });
        Schema::create('studybuddy_school_message_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('studybuddy_organizations')->cascadeOnDelete();
            $table->foreignId('message_id')->constrained('studybuddy_school_messages')->cascadeOnDelete();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->string('reason', 24);
            $table->string('status', 16)->default('pending'); // pending | dismissed | hidden
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->unique(['message_id', 'reported_by'], 'sb_p11c_report_unique');
            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studybuddy_school_message_reports');
        Schema::dropIfExists('studybuddy_school_message_reads');
        Schema::dropIfExists('studybuddy_school_messages');
        Schema::dropIfExists('studybuddy_school_conversations');
    }
};
