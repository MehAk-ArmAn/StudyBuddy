<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$patterns = [
    'lorem ipsum' => '/lorem\s+ipsum/i',
    'dummy copy' => '/\bdummy\b/i',
    'template trace' => '/template\s+(copy|content|text)|starter\s+content/i',
    'placeholder copy' => '/placeholder\s+(copy|content|text)/i',
    'todo marker' => '/\bTODO\b|\bFIXME\b/i',
    'weird uuuu text' => '/u{4,}/i',
    'old test content' => '/\btest\s*1\s*test\b/i',
    'example email' => '/example@example\.com/i',
];

$found = 0;

function scanValue(string $place, string $value, array $patterns, int &$found): void {
    foreach ($patterns as $label => $regex) {
        if (preg_match($regex, $value)) {
            $found++;
            $snippet = mb_substr(preg_replace('/\s+/', ' ', $value), 0, 160);
            echo "❌ {$label}: {$place}\n   {$snippet}\n";
        }
    }
}

echo "StudyBuddy Content Trace Audit\n";
echo "==============================\n\n";

$tables = [
    'site_settings',
    'pages',
    'page_sections',
    'page_section_items',
    'homepage_sections',
    'homepage_section_items',
    'navigation_items',
    'footer_items',
    'studybuddy_mini_app_platforms',
    'studybuddy_content_pages',
    'studybuddy_content_items',
    'studybuddy_platform_settings',
    'studybuddy_launch_checklist_items',
];

foreach ($tables as $table) {
    if (!Schema::hasTable($table)) continue;

    $columns = collect(DB::select("SHOW COLUMNS FROM `{$table}`"))
        ->filter(function ($col) {
            $type = strtolower($col->Type ?? '');
            return str_contains($type, 'char')
                || str_contains($type, 'text')
                || str_contains($type, 'json')
                || str_contains($type, 'enum')
                || str_contains($type, 'set');
        })
        ->pluck('Field')
        ->values();

    if ($columns->isEmpty()) continue;

    DB::table($table)->orderBy('id')->chunk(100, function ($rows) use ($table, $columns, $patterns, &$found) {
        foreach ($rows as $row) {
            $id = $row->id ?? '?';

            foreach ($columns as $column) {
                $value = $row->{$column} ?? null;
                if ($value === null || $value === '') continue;

                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value);
                }

                scanValue("DB {$table}.{$column} #{$id}", (string) $value, $patterns, $found);
            }
        }
    });
}

$fileRoots = [
    base_path('resources/views'),
    base_path('app/Http/Controllers'),
    base_path('database/seeders'),
];

foreach ($fileRoots as $root) {
    if (!is_dir($root)) continue;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

    foreach ($iterator as $file) {
        if (!$file->isFile()) continue;

        $path = $file->getPathname();

        if (str_contains($path, '.bak_')) continue;
        if (str_contains($path, '/vendor/')) continue;

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['php', 'blade.php'], true) && !str_ends_with($path, '.blade.php')) continue;

        $content = file_get_contents($path);
        scanValue('FILE '.str_replace(base_path().'/', '', $path), $content ?: '', $patterns, $found);
    }
}

echo "\n";
if ($found === 0) {
    echo "✅ No obvious template traces found.\n";
    exit(0);
}

echo "❌ {$found} possible trace(s) found. Review the lines above.\n";
exit(1);
