<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('studybuddy_mailing_list_subscribers')) {
            return;
        }

        Schema::create('studybuddy_mailing_list_subscribers', function (Blueprint $table): void {
            $table->id();
            $table->string('email', 254)->unique();
            $table->string('status', 30)->default('active')->index();
            $table->string('source', 100)->default('website_updates');
            $table->timestamp('subscribed_at')->nullable()->index();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['status', 'subscribed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studybuddy_mailing_list_subscribers');
    }
};
