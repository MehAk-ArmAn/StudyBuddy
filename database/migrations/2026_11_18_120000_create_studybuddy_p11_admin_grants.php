<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('studybuddy_admin_grants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $table->string('kind', 20)->index(); // head, staff
            $table->json('capabilities')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('studybuddy_test_account')->default(false)->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studybuddy_admin_grants');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('studybuddy_test_account');
        });
    }
};
