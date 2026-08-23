<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'email_verified_at')) {
            DB::table('users')->whereNull('email_verified_at')->update(['email_verified_at' => now()]);
        }

        if (Schema::hasTable('studybuddy_mini_app_platforms')) {
            Schema::table('studybuddy_mini_app_platforms', function (Blueprint $table): void {
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'hero_image')) {
                    $table->string('hero_image')->nullable()->after('accent');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'preview_text')) {
                    $table->text('preview_text')->nullable()->after('description');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'safety_note')) {
                    $table->text('safety_note')->nullable()->after('preview_text');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'age_min')) {
                    $table->unsignedTinyInteger('age_min')->nullable()->after('estimated_minutes');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'age_max')) {
                    $table->unsignedTinyInteger('age_max')->nullable()->after('age_min');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'audience_roles')) {
                    $table->json('audience_roles')->nullable()->after('age_max');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'learning_outcomes')) {
                    $table->json('learning_outcomes')->nullable()->after('learning_tags');
                }
                if (! Schema::hasColumn('studybuddy_mini_app_platforms', 'detail_sections')) {
                    $table->json('detail_sections')->nullable()->after('learning_outcomes');
                }
            });

            $defaults = [
                'math-quest' => ['age_min' => 7, 'age_max' => 15, 'audience_roles' => ['student', 'parent', 'teacher', 'independent_learner'], 'preview_text' => 'Preview number missions, confidence-building practice, and mini challenges before logging in.', 'safety_note' => 'Designed for low-pressure practice and no open chat.'],
                'spelling-sprint' => ['age_min' => 6, 'age_max' => 15, 'audience_roles' => ['student', 'parent', 'teacher', 'independent_learner'], 'preview_text' => 'Preview word races, spelling rounds, and daily vocabulary goals.', 'safety_note' => 'Word content should be reviewed by admins before launch.'],
                'reading-garden' => ['age_min' => 6, 'age_max' => 16, 'audience_roles' => ['student', 'parent', 'teacher', 'independent_learner'], 'preview_text' => 'Preview calm reading goals and comprehension missions.', 'safety_note' => 'Reading content should remain age-aware and parent-friendly.'],
                'focus-forest' => ['age_min' => 8, 'age_max' => 18, 'audience_roles' => ['student', 'parent', 'teacher', 'independent_learner'], 'preview_text' => 'Preview focus sessions, streak habits, and calm study timers.', 'safety_note' => 'Focus tools encourage breaks and balanced routines.'],
                'planner-city' => ['age_min' => 10, 'age_max' => 18, 'audience_roles' => ['student', 'parent', 'teacher', 'independent_learner'], 'preview_text' => 'Preview routines, weekly plans, and simple study blocks.', 'safety_note' => 'Planning features should support learners without pressure.'],
                'quiz-galaxy' => ['age_min' => 8, 'age_max' => 18, 'audience_roles' => ['student', 'teacher', 'independent_learner'], 'preview_text' => 'Preview review quizzes, topic galaxies, and practice scores.', 'safety_note' => 'Scores are for progress, not public ranking.'],
                'shapes-lab' => ['age_min' => 6, 'age_max' => 14, 'audience_roles' => ['student', 'teacher', 'independent_learner'], 'preview_text' => 'Preview visual geometry, patterns, shapes, and spatial thinking.', 'safety_note' => 'Visual learning only; no sensitive data needed.'],
                'flashcard-castle' => ['age_min' => 9, 'age_max' => 18, 'audience_roles' => ['student', 'teacher', 'independent_learner'], 'preview_text' => 'Preview recall rounds and flashcard-style study quests.', 'safety_note' => 'Future personal flashcards should stay private to the learner.'],
            ];

            foreach ($defaults as $slug => $data) {
                DB::table('studybuddy_mini_app_platforms')->where('slug', $slug)->update([
                    'preview_text' => $data['preview_text'],
                    'safety_note' => $data['safety_note'],
                    'age_min' => $data['age_min'],
                    'age_max' => $data['age_max'],
                    'audience_roles' => json_encode($data['audience_roles']),
                    'learning_outcomes' => json_encode(['Practice with confidence', 'Build a small habit', 'Earn points through safe progress']),
                    'detail_sections' => json_encode([
                        ['title' => 'How it works', 'body' => 'Choose a short activity, complete a focused task, and return to your StudyBuddy dashboard for progress.'],
                        ['title' => 'Why it helps', 'body' => 'Small missions make learning less scary and easier to repeat.'],
                    ]),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Keep content-safe columns and verified timestamps. This migration is intentionally non-destructive.
    }
};
