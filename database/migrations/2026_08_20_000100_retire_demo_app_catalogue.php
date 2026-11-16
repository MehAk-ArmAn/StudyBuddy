<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Retires the demo/test app catalogue.
 *
 * Earlier migrations seeded eight placeholder apps (Math Quest, Spelling
 * Sprint, ...) straight into the platform tables. They were only ever
 * development fixtures, so StudyBuddy ships with an empty catalogue and real
 * apps get added through the Apps CMS.
 *
 * Only the known demo slugs are touched, so any real app added through the
 * admin survives this migration untouched. It runs after the seeding
 * migrations, which means `migrate:fresh` also ends up with zero apps.
 */
return new class extends Migration
{
    /** The demo apps every historical migration/seeder created. */
    private const DEMO_SLUGS = [
        'math-quest',
        'spelling-sprint',
        'reading-garden',
        'focus-forest',
        'planner-city',
        'quiz-galaxy',
        'shapes-lab',
        'flashcard-castle',
    ];

    private const DEMO_TITLES = [
        'Math Quest',
        'Spelling Sprint',
        'Reading Garden',
        'Focus Forest',
        'Planner City',
        'Quiz Galaxy',
        'Shapes Lab',
        'Flashcard Castle',
    ];

    public function up(): void
    {
        $this->removePlatformApps();
        $this->removeLegacyCatalogueApps();
        $this->removeDemoAppCards();
        $this->removePublishedDemoBuilds();
    }

    /**
     * Deliberately irreversible: the removed rows were disposable fixtures and
     * recreating them would put fake apps back on a live catalogue.
     */
    public function down(): void
    {
        // No rollback. See the note above.
    }

    private function removePlatformApps(): void
    {
        if (! Schema::hasTable('studybuddy_mini_app_platforms')) {
            return;
        }

        DB::table('studybuddy_mini_app_platforms')
            ->whereIn('slug', self::DEMO_SLUGS)
            ->delete();
    }

    /**
     * The legacy catalogue table held a second copy of the same demo apps.
     * The table itself stays (dropping it would be a destructive schema change
     * on a live database) but the duplicate demo rows go.
     */
    private function removeLegacyCatalogueApps(): void
    {
        if (! Schema::hasTable('studybuddy_app_catalog_items')) {
            return;
        }

        DB::table('studybuddy_app_catalog_items')
            ->whereIn('title', self::DEMO_TITLES)
            ->delete();
    }

    /**
     * Remove the seeded app cards on the CMS "apps" page and the homepage
     * preview strip. Their badge_text held invented review scores (4.8, 4.7…)
     * which were never backed by real data.
     */
    private function removeDemoAppCards(): void
    {
        foreach ([
            ['page_sections', 'page_section_items', 'page_section_id'],
            ['homepage_sections', 'homepage_section_items', 'homepage_section_id'],
        ] as [$sectionTable, $itemTable, $foreignKey]) {
            if (! Schema::hasTable($sectionTable) || ! Schema::hasTable($itemTable)) {
                continue;
            }

            $sectionIds = DB::table($sectionTable)
                ->whereIn('section_key', ['all_apps', 'apps_preview'])
                ->pluck('id');

            if ($sectionIds->isEmpty()) {
                continue;
            }

            DB::table($itemTable)
                ->whereIn($foreignKey, $sectionIds)
                ->whereIn('item_key', self::DEMO_SLUGS)
                ->delete();
        }

        // Belt and braces: clear any invented rating badge left anywhere else.
        if (Schema::hasTable('page_section_items')) {
            DB::table('page_section_items')
                ->whereIn('badge_text', ['4.6', '4.7', '4.8', '4.9', '5.0'])
                ->update(['badge_text' => null]);
        }
    }

    /**
     * Delete static web builds that belonged only to the demo apps. The
     * public/web-apps directory itself is launcher infrastructure and stays.
     */
    private function removePublishedDemoBuilds(): void
    {
        // Feature tests rebuild an in-memory database but share the developer's
        // checkout. They must never delete real launcher folders from `public`.
        if (app()->environment('testing')) {
            return;
        }

        foreach (self::DEMO_SLUGS as $slug) {
            $directory = public_path('web-apps/'.$slug);

            if (is_link($directory)) {
                @unlink($directory);
            } elseif (File::isDirectory($directory)) {
                File::deleteDirectory($directory);
            }
        }
    }
};
