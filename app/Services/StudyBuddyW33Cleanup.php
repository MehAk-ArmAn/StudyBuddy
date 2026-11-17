<?php

namespace App\Services;

use App\Models\StudyBuddyMiniAppPlatform;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Only deletes the app's own W33 uploaded files when its CMS listing is deleted. */
class StudyBuddyW33Cleanup
{
    public static function forApp(StudyBuddyMiniAppPlatform $app): void
    {
        $disk = Storage::disk('local');
        $tables = [
            'studybuddy_w33_screenshots' => 'studybuddy-w33/screenshots/'.$app->id.'/',
            'studybuddy_w33_desktop_packages' => 'studybuddy-w33/desktop/'.$app->id.'/',
        ];
        foreach ($tables as $table => $prefix) {
            if (! Schema::hasTable($table)) continue;
            foreach (DB::table($table)->where('app_id', $app->id)->pluck('path') as $path) {
                if (is_string($path) && str_starts_with($path, $prefix)
                    && ! str_contains($path, '..') && ! str_contains($path, "\0")) {
                    $disk->delete($path);
                }
            }
        }
    }
}
