<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/** Opt-in cleanup. Only six unmodified demo-identity rows, never all apps. */
class StudyBuddyRemoveLocalDemoAppsSeeder extends Seeder
{
    public function run(): void
    {

        // Even if accidentally invoked on a deployed environment, fail closed.
        if (! app()->environment('local', 'testing')) {
            throw new \RuntimeException('Sample apps are LOCAL TEST DATA ONLY. Refusing to run outside local/testing.');
        }

        $connection = config('database.default');
        if ($connection !== 'sqlite') {
            $host = strtolower((string) config("database.connections.{$connection}.host", ''));
            if (! in_array($host, ['127.0.0.1', 'localhost', '::1'], true)) {
                throw new \RuntimeException('Demo data may only be written to a local database host.');
            }
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('studybuddy_mini_app_platforms')) {
            throw new \RuntimeException('Missing StudyBuddy apps table. Check the local DB configuration first; no migrations were run.');
        }

        $slugs = [
            'demo-next-number-odyssey', 'demo-next-wordwhirl',
            'demo-next-planet-postcards', 'demo-next-science-sprouts',
            'demo-next-chromatic-castle', 'demo-next-brainbridge',
        ];
        $deleted = DB::table('studybuddy_mini_app_platforms')
            ->whereIn('slug', $slugs)
            ->where('name', 'like', '%(Demo)')
            ->where('status', 'concept')
            ->where('is_web_enabled', false)
            ->where('is_download_enabled', false)
            ->delete();

        $this->command?->info("Removed {$deleted} local test card(s). Real apps and other records untouched.");
    }
}
