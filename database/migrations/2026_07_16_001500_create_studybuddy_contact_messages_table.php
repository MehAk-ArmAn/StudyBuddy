<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('studybuddy_contact_messages')) {
            Schema::create('studybuddy_contact_messages', function (Blueprint $table) {
                $table->id();
                $table->string('name', 160);
                $table->string('email', 190);
                $table->string('role', 80)->nullable();
                $table->string('category', 120)->default('general');
                $table->string('subject', 190);
                $table->longText('message');
                $table->string('status', 40)->default('new');
                $table->string('priority', 40)->default('normal');
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('ip_address', 80)->nullable();
                $table->string('user_agent', 500)->nullable();
                $table->json('meta')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Keep support messages safe on rollback.
    }
};
