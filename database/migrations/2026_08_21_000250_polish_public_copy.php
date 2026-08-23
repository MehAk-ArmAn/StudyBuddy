<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->polishExperiencePages();
        $this->hideInternalPlatformNotes();
    }

    public function down(): void
    {
        // Public copy cleanup is intentionally non-destructive.
    }

    private function polishExperiencePages(): void
    {
        if (! Schema::hasTable('studybuddy_content_pages')) {
            return;
        }

        // Every phrase is tied to the page that originally shipped it. This
        // upgrades StudyBuddy's own legacy copy without rewriting text an
        // administrator may have deliberately used on another page.
        $replacementsBySlug = [
            'learning-hub' => [
                'Focus modes' => 'Sessions',
                'Mini missions' => 'Activities',
                'Reward paths' => 'Next steps',
                '4' => 'Short',
                '8' => 'Focused',
                'Live' => 'Clear',
                'Jump into web-play or download-ready learning games later.'
                    => 'Move from your plan into a focused StudyBuddy learning activity.',
            ],
            'parents-center' => [
                'Parent features planned' => 'Ways to support learning',
                'Future parent dashboard for safer learner support.'
                    => 'Use clear account connections and age-appropriate guidance to support safer learning.',
            ],
            'teacher-studio' => [
                'Generate learner-friendly lesson outlines, activities, and mini missions for future classroom experiences.'
                    => 'Create learner-friendly lesson outlines, activities, and mini missions for classroom practice.',
                'Teacher tools planned' => 'Classroom planning tools',
            ],
            'safety-support' => [
                'Yes. Parent-facing pages and future dashboards are part of the product direction.'
                    => 'Yes. Parents can use role-specific guidance, connect learner accounts with approval, and support calm routines.',
                'Yes. Teacher Studio is designed for classroom-friendly lesson planning and future group features.'
                    => 'Yes. Teachers can plan classroom activities and use StudyBuddy learning apps for focused practice.',
            ],
            'app-ecosystem' => [
                'Web play + downloads planned' => 'Browser play + store links',
                'Future web-hosted versions of mini apps can launch directly in browser.'
                    => 'Browser versions open directly when an app offers one.',
                'Future iOS, Android, Windows, or desktop versions can be linked from the catalog.'
                    => 'Store links appear for the devices each app supports.',
                'This app list is editable from the admin Content Studio.'
                    => 'Browse StudyBuddy apps and choose the version that works for your device.',
            ],
        ];

        DB::table('studybuddy_content_pages')
            ->select(['id', 'slug', 'subtitle', 'hero_badge', 'content_blocks'])
            ->orderBy('id')
            ->get()
            ->each(function (object $page) use ($replacementsBySlug): void {
                $pageReplacements = $replacementsBySlug[$page->slug] ?? [];

                if ($pageReplacements === []) {
                    return;
                }

                $updates = [];

                foreach (['subtitle', 'hero_badge'] as $column) {
                    $value = $page->{$column};

                    if (is_string($value) && array_key_exists($value, $pageReplacements)) {
                        $updates[$column] = $pageReplacements[$value];
                    }
                }

                $blocks = json_decode((string) $page->content_blocks, true);
                if (is_array($blocks)) {
                    $cleaned = $this->replaceLegacyStrings($blocks, $pageReplacements);

                    if ($cleaned !== $blocks) {
                        $updates['content_blocks'] = json_encode(
                            $cleaned,
                            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                        );
                    }
                }

                if ($updates !== []) {
                    $updates['updated_at'] = now();
                    DB::table('studybuddy_content_pages')->where('id', $page->id)->update($updates);
                }
            });
    }

    private function hideInternalPlatformNotes(): void
    {
        if (! Schema::hasTable('studybuddy_platform_settings')) {
            return;
        }

        $notes = [
            'final_launch_note' => [
                'old' => 'Final app hosting, store uploads, and playable builds should be connected after each mini-app is packaged and tested.',
                'new' => 'Keep hosting, store listings, and browser availability aligned with each app’s release status.',
            ],
            'default_public_theme_note' => [
                'old' => 'The public website always returns to the default Cosmic Dolphin galaxy style after logout.',
                'new' => 'The public website returns to the Cosmic Dolphin style after logout.',
            ],
        ];

        foreach ($notes as $key => $copy) {
            DB::table('studybuddy_platform_settings')
                ->where('setting_key', $key)
                ->where('setting_value', $copy['old'])
                ->update(['setting_value' => $copy['new'], 'updated_at' => now()]);

            DB::table('studybuddy_platform_settings')
                ->where('setting_key', $key)
                ->update(['is_public' => false, 'updated_at' => now()]);
        }
    }

    private function replaceLegacyStrings(mixed $value, array $replacements): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->replaceLegacyStrings($item, $replacements);
            }

            return $value;
        }

        if (is_string($value) && array_key_exists($value, $replacements)) {
            return $replacements[$value];
        }

        return $value;
    }
};
