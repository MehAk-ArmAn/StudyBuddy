<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * OPT-IN, LOCAL-ONLY demonstration catalogue.
 * Never referenced from DatabaseSeeder and never allowed on a remote DB.
 * Does not modify real apps, users, roles, points or school accounts.
 */
class StudyBuddyLocalDemoAppsSeeder extends Seeder
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

        $rows = [
            ['demo-next-number-odyssey', 'Number Odyssey (Demo)', 'Mathematics', 'Count the constellations.', 'An imaginary maths adventure concept. Visual mock data only — no levels or playable builds exist.', '🪐', 'assets/studybuddy-demo/number-odyssey.svg', ['numbers','patterns','arithmetic']],
            ['demo-next-wordwhirl', 'Wordwhirl Academy (Demo)', 'English', 'A spellbinding word journey.', 'An invented literacy experience used to test catalogue layouts. Not a launched or playable app.', '📖', 'assets/studybuddy-demo/wordwhirl.svg', ['spelling','reading','vocabulary']],
            ['demo-next-planet-postcards', 'Planet Postcards (Demo)', 'Geography', 'Travel through tiny discoveries.', 'A fictional geography concept for testing image cards, filters and app detail pages.', '🌍', 'assets/studybuddy-demo/planet-postcards.svg', ['countries','maps','places']],
            ['demo-next-science-sprouts', 'Science Sprouts Lab (Demo)', 'Science', 'Little experiments. Big curiosity.', 'Concept art only for a possible science activity. No real experiments, lessons or rewards are included.', '🧪', 'assets/studybuddy-demo/science-sprouts.svg', ['observation','materials','plants']],
            ['demo-next-chromatic-castle', 'Chromatic Castle (Demo)', 'Visual Arts', 'A place for color explorers.', 'A made-up color learning world for visually testing the StudyBuddy design system.', '🎨', 'assets/studybuddy-demo/chromatic-castle.svg', ['color','art','shapes']],
            ['demo-next-brainbridge', 'Brainbridge Puzzles (Demo)', 'Critical Thinking', 'Every bridge begins with a clue.', 'A fictional logic game concept. Demo page only, with no functioning puzzle engine.', '🧩', 'assets/studybuddy-demo/brainbridge.svg', ['logic','puzzles','reasoning']],
        ];

        $added = 0;
        foreach ($rows as $index => [$slug, $name, $category, $tagline, $description, $icon, $art, $tags]) {
            // Collision-safe and repeatable: never overwrite anything already there.
            if (DB::table('studybuddy_mini_app_platforms')->where('slug', $slug)->exists()) {
                continue;
            }

            DB::table('studybuddy_mini_app_platforms')->insert([
                'slug' => $slug,
                'name' => $name,
                'category' => $category,
                'tagline' => $tagline,
                'description' => $description,
                'long_description' => 'DESIGN-ONLY DEMO. '.$description.' This entry is fictional and not part of the published app portfolio.',
                'preview_text' => 'DEMO / TEST ONLY · Concept artwork. No game to play.',
                'status' => 'concept',
                'icon' => $icon,
                'accent' => 'cosmic',
                'image_url' => $art,
                'hero_image' => $art,
                'age_range' => 'Demo preview',
                'points_reward' => 0,
                'estimated_minutes' => 0,
                'learning_tags' => json_encode($tags, JSON_UNESCAPED_UNICODE),
                'is_web_enabled' => false,
                'is_download_enabled' => false,
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 800 + $index,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $added++;
        }

        $this->command?->info("Added {$added} local demo card(s). Existing rows were preserved.");
    }
}
