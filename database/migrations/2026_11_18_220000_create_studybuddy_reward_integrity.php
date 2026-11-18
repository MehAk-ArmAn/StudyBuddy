<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('studybuddy_reward_settings')) {
            Schema::create('studybuddy_reward_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('enabled')->default(true);
                $table->unsignedInteger('global_daily_cap')->default(60);
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('studybuddy_reward_rules')) {
            Schema::create('studybuddy_reward_rules', function (Blueprint $table) {
                $table->id();
                $table->string('game_key', 100)->unique();
                $table->boolean('enabled')->default(true);
                $table->unsignedSmallInteger('min_correct')->default(3);
                $table->unsignedSmallInteger('min_seconds')->default(15);
                $table->unsignedSmallInteger('points_per_correct')->default(4);
                $table->unsignedSmallInteger('time_bonus_after_seconds')->default(60);
                $table->unsignedSmallInteger('time_bonus_points')->default(2);
                $table->unsignedSmallInteger('max_points_per_run')->default(25);
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('studybuddy_reward_events')) {
            Schema::create('studybuddy_reward_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('event_key', 120)->unique();
                $table->string('game_key', 100);
                $table->unsignedInteger('elapsed_seconds')->default(0);
                $table->unsignedSmallInteger('score')->default(0);
                $table->unsignedSmallInteger('max_score')->default(0);
                $table->unsignedSmallInteger('points_awarded')->default(0);
                $table->foreignId('point_transaction_id')->nullable()->constrained('studybuddy_point_transactions')->nullOnDelete();
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
            });
        }
        if (!Schema::hasTable('studybuddy_user_reward_badges')) {
            Schema::create('studybuddy_user_reward_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('code', 60);
                $table->foreignId('earned_from_event_id')->nullable()->constrained('studybuddy_reward_events')->nullOnDelete();
                $table->timestamp('earned_at');
                $table->timestamps();
                $table->unique(['user_id', 'code']);
            });
        }
        if (!Schema::hasTable('studybuddy_reward_rule_audits')) {
            Schema::create('studybuddy_reward_rule_audits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 100);
                $table->string('target', 120);
                $table->json('before_value')->nullable();
                $table->json('after_value')->nullable();
                $table->timestamps();
            });
        }
        $now = now();
        DB::table('studybuddy_reward_settings')->updateOrInsert(['id' => 1], [
            'enabled' => true, 'global_daily_cap' => 60, 'created_at' => $now, 'updated_at' => $now,
        ]);
        // Only replace the legacy seed sentence if it is still unchanged.
        // Head Admin custom wording must NEVER be silently overwritten.
        if (Schema::hasTable('studybuddy_platform_settings')) {
            DB::table('studybuddy_platform_settings')
                ->where('setting_key', 'points_policy')
                ->where('setting_value', 'Learners earn points by starting missions, completing sessions, finishing quests, and building consistent study habits.')
                ->update([
                    'setting_value' => 'Earn capped, non-cash virtual points from verified learning results. Opening an app does not earn points. Earned badges and profile styles are optional.',
                    'updated_at' => $now,
                ]);
        }
        foreach ([
            ['game_key'=>'math-sprint:starter', 'points_per_correct'=>4, 'max_points_per_run'=>22],
            ['game_key'=>'math-sprint:explorer', 'points_per_correct'=>6, 'max_points_per_run'=>32],
        ] as $row) {
            DB::table('studybuddy_reward_rules')->updateOrInsert(['game_key'=>$row['game_key']], array_merge([
                'enabled'=>true, 'min_correct'=>3, 'min_seconds'=>15,
                'time_bonus_after_seconds'=>60, 'time_bonus_points'=>2,
                'created_at'=>$now, 'updated_at'=>$now,
            ], $row));
        }
    }
    public function down(): void
    {
        // Preserving private earned history and balances is safer than dropping tables
        // during an accidental source rollback. Explicit data migration requires review.
    }
};
