<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('studybuddy_school_notices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('studybuddy_organizations')->cascadeOnDelete();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 140);
            $table->text('body');
            $table->string('priority', 12)->default('normal');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'archived_at', 'created_at'], 'sbp11d_notices_org_idx');
        });
        Schema::create('studybuddy_school_notice_receipts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('notice_id')->constrained('studybuddy_school_notices')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('studybuddy_organizations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->unique(['notice_id', 'user_id'], 'sbp11d_notice_user_unique');
            $table->index(['organization_id', 'user_id', 'read_at'], 'sbp11d_receipts_unread_idx');
        });
        Schema::create('studybuddy_school_safety_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('studybuddy_organizations')->cascadeOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category', 35);
            $table->string('subject', 150);
            $table->text('description');
            $table->string('status', 16)->default('open');
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_note')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'status', 'created_at'], 'sbp11d_reports_queue_idx');
        });
        Schema::create('studybuddy_school_notice_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('studybuddy_organizations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('mute_normal_badge')->default(false);
            $table->timestamps();
            $table->unique(['organization_id', 'user_id'], 'sbp11d_pref_org_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studybuddy_school_notice_preferences');
        Schema::dropIfExists('studybuddy_school_safety_reports');
        Schema::dropIfExists('studybuddy_school_notice_receipts');
        Schema::dropIfExists('studybuddy_school_notices');
    }
};
