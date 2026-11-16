<?php

use App\Services\StudyBuddyWebAppPublisher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Move existing managed builds behind the authenticated/active asset routes.
     * Logical database paths stay unchanged for backwards compatibility.
     */
    public function up(): void
    {
        if (! Schema::hasTable('studybuddy_mini_app_platforms')) {
            return;
        }

        $managedApps = DB::table('studybuddy_mini_app_platforms')
            ->where(function ($query): void {
                $query->whereNotNull('web_app_entry_path')
                    ->orWhere('web_play_url', 'like', '/web-apps/%');
            })
            ->get(['slug']);

        // RefreshDatabase runs migrations before individual tests can swap in
        // isolated filesystem roots. With an empty test catalogue there is
        // nothing to migrate, so do not touch the developer's real storage/.
        if (app()->environment('testing') && $managedApps->isEmpty()) {
            return;
        }

        $privateRoot = StudyBuddyWebAppPublisher::buildRoot();
        $legacyRoot = public_path('web-apps');

        if (is_link($privateRoot)) {
            throw new RuntimeException('The private StudyBuddy web-build root must not be a symbolic link.');
        }

        File::ensureDirectoryExists($privateRoot);

        if (is_link($privateRoot)) {
            throw new RuntimeException('The private StudyBuddy web-build root must not be a symbolic link.');
        }

        if ($managedApps->isNotEmpty() && is_link($legacyRoot)) {
            throw new RuntimeException('The legacy public StudyBuddy web-build root must not be a symbolic link.');
        }

        foreach ($managedApps as $app) {
            $slug = Str::slug((string) $app->slug);

            if ($slug === '') {
                throw new RuntimeException('A managed StudyBuddy web build has no safe slug.');
            }

            $source = $legacyRoot.DIRECTORY_SEPARATOR.$slug;
            $target = StudyBuddyWebAppPublisher::buildDirectory($slug);

            if (is_link($source) || is_link($target)) {
                throw new RuntimeException('StudyBuddy refused to migrate a symbolic-link web build for '.$slug.'.');
            }

            if (file_exists($source) && ! is_dir($source)) {
                throw new RuntimeException('The legacy StudyBuddy build path is not a directory for '.$slug.'.');
            }

            if (file_exists($target) && ! is_dir($target)) {
                throw new RuntimeException('The private StudyBuddy build path is not a directory for '.$slug.'.');
            }

            if (! is_dir($source)) {
                continue;
            }

            if (is_dir($target)) {
                throw new RuntimeException('Both public and private StudyBuddy builds exist for '.$slug.'; resolve the conflict before migrating.');
            }

            if (! File::moveDirectory($source, $target)) {
                throw new RuntimeException('StudyBuddy could not move the existing web build for '.$slug.' into private storage.');
            }
        }
    }

    /** This security boundary is intentionally not reversed on rollback. */
    public function down(): void
    {
        // Existing logical paths remain compatible with the private asset route.
    }
};
