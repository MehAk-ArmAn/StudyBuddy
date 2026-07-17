<?php

use App\Models\HomepageSection;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

$copy = [
    'hero' => [
        'eyebrow' => 'Learning that fits real life',
        'title' => 'A calmer, smarter way to learn.',
        'subtitle' => 'Focused practice, meaningful progress, profiles, and support in one welcoming place.',
        'button_label' => 'Explore learning apps',
        'button_url' => '/apps',
        'secondary_button_label' => 'Choose your role',
        'secondary_button_url' => '/roles',
    ],

    'what_we_do' => [
        'eyebrow' => 'Made to feel manageable',
        'title' => 'Everything important, without the clutter.',
        'subtitle' => 'Clear tools for practice, progress, profiles, and support.',
    ],

    'apps_preview' => [
        'eyebrow' => 'Learning apps',
        'title' => 'Choose one useful session. Start there.',
        'subtitle' => 'Short, focused apps connected to the same StudyBuddy profile and progress.',
        'button_label' => 'View all learning apps',
        'button_url' => '/apps',
    ],

    'apps' => [
        'eyebrow' => 'Learning apps',
        'title' => 'Choose one useful session. Start there.',
        'subtitle' => 'Short, focused apps connected to the same StudyBuddy profile and progress.',
        'button_label' => 'View all learning apps',
        'button_url' => '/apps',
    ],

    'page_paths' => [
        'eyebrow' => 'Explore by goal',
        'title' => 'Go straight to what you need.',
        'subtitle' => 'Clear paths into learning, profiles, parent support, and teacher tools.',
    ],

    'trust' => [
        'eyebrow' => 'Designed with care',
        'title' => 'Welcoming does not have to mean noisy.',
        'subtitle' => 'Motion stays optional, actions stay understandable, and important information stays easy to reach.',
    ],

    'cta' => [
        'eyebrow' => 'Your next step',
        'title' => 'Start with the smallest useful action.',
        'subtitle' => 'Open one app, choose one goal, or create a profile you will want to return to.',
        'button_label' => 'Choose an app',
        'button_url' => '/apps',
        'secondary_button_label' => 'Explore roles',
        'secondary_button_url' => '/roles',
    ],
];

foreach ($copy as $sectionKey => $values) {
    HomepageSection::query()
        ->where('section_key', $sectionKey)
        ->update($values);
}

echo "Homepage content updated.\n";
