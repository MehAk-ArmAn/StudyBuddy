<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

function nullableVarchar(string $table, string $column): void
{
    if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
        echo "skip: {$table}.{$column} not found\n";
        return;
    }

    try {
        DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` VARCHAR(255) NULL");
        echo "fixed: {$table}.{$column} can be NULL now\n";
    } catch (Throwable $e) {
        echo "notice: could not alter {$table}.{$column}: {$e->getMessage()}\n";
    }
}

function fillMissingKeys(string $table, string $keyColumn, string $prefix): void
{
    if (!Schema::hasTable($table) || !Schema::hasColumn($table, $keyColumn)) {
        return;
    }

    $rows = DB::table($table)
        ->whereNull($keyColumn)
        ->orWhere($keyColumn, '')
        ->get();

    foreach ($rows as $row) {
        $source = $row->slug ?? $row->key ?? $row->title ?? $row->label ?? $row->name ?? "{$prefix}-{$row->id}";
        $value = Str::slug($source) ?: "{$prefix}-{$row->id}";

        DB::table($table)->where('id', $row->id)->update([
            $keyColumn => $value,
        ]);
    }

    echo "filled: {$table}.{$keyColumn} missing values\n";
}

echo "\n=== StudyBuddy DB schema hotfix ===\n";

nullableVarchar('homepage_sections', 'section_key');
nullableVarchar('homepage_section_items', 'item_key');
nullableVarchar('homepage_section_items', 'section_key');

echo "\n=== Running migrations ===\n";
Artisan::call('migrate', ['--force' => true]);
echo Artisan::output();

echo "\n=== Running StudyBuddy content seeder ===\n";

if (!file_exists(database_path('seeders/StudyBuddyPublishContentSeeder.php'))) {
    echo "ERROR: database/seeders/StudyBuddyPublishContentSeeder.php not found.\n";
    exit(1);
}

try {
    Artisan::call('db:seed', [
        '--class' => 'StudyBuddyPublishContentSeeder',
        '--force' => true,
    ]);

    echo Artisan::output();
} catch (Throwable $e) {
    echo "\nSEEDER ERROR:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Filling any missing keys after seed ===\n";
fillMissingKeys('homepage_sections', 'section_key', 'section');
fillMissingKeys('homepage_section_items', 'item_key', 'item');
fillMissingKeys('homepage_section_items', 'section_key', 'section');

echo "\n=== Clearing Laravel cache ===\n";
Artisan::call('optimize:clear');
echo Artisan::output();

echo "\nDONE ✅\n";
