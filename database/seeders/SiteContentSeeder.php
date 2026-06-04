<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use App\Support\DemoContent;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        collect(['home', 'showcase'])->each(function (string $section): void {
            DemoContent::siteContent($section)->each(fn (object $item) => SiteContent::query()->updateOrCreate(['key' => $item->key], [
                'section' => $item->section,
                'title' => $item->title,
                'body' => $item->body,
                'metadata' => $item->metadata,
            ]));
        });
    }
}
