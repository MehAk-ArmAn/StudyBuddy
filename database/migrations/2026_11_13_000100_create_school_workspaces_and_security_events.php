<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('studybuddy_organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 190);
            $table->string('domain', 190);
            $table->string('city', 120)->nullable();
            $table->string('country', 120)->default('United Arab Emirates');
            $table->string('official_email', 190);
            $table->string('website', 255)->nullable();
            $table->text('purpose')->nullable();
            $table->string('status', 24)->default('pending')->index();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['domain', 'status']);
        });

        Schema::create('studybuddy_organization_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('studybuddy_organizations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 32); // owner | school_admin | teacher
            $table->string('status', 24)->default('active');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['organization_id', 'user_id']);
        });

        Schema::create('studybuddy_organization_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('studybuddy_organizations')->cascadeOnDelete();
            $table->foreignId('invited_by')->constrained('users')->restrictOnDelete();
            $table->string('email', 190);
            $table->string('token_hash', 64)->unique();
            $table->string('role', 32)->default('teacher');
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'email']);
        });

        Schema::create('studybuddy_security_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 70);
            $table->string('severity', 16)->default('info');
            $table->string('ip_address', 45)->nullable();
            $table->json('context')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['event', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studybuddy_security_events');
        Schema::dropIfExists('studybuddy_organization_invites');
        Schema::dropIfExists('studybuddy_organization_members');
        Schema::dropIfExists('studybuddy_organizations');
    }
};
