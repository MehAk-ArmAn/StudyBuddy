<?php

/**
 * StudyBuddy brand identity — the single source of truth.
 *
 * Change a value here (or the matching site setting in the admin) and every
 * template picks it up. Nothing about the brand should be hard-coded into an
 * individual Blade file.
 *
 * Site settings edited in the admin win over these values where a setting
 * exists; these are the defaults a fresh install ships with, so the product is
 * correctly branded before anyone touches the CMS.
 */
return [

    'brand' => [
        'name' => 'StudyBuddy',

        // The canonical slogan. Do not write variations of it.
        'slogan' => 'Learn. Play. Grow. Your Way.',

        // Used only where a domain genuinely belongs (legal text, contact copy).
        'domain' => 'StudyBuddy.fun',

        'description' => 'Short learning games for maths, spelling, reading and focus. Built for kids, and sane for the parents watching over their shoulder.',

        'theme_color' => '#7C3AED',
        'background_color' => '#080B2D',
    ],

    /*
     | Identity artwork is kept local on purpose: a site should not lose its
     | favicon because a third-party host is unreachable. Content imagery may
     | still live in the shared image library.
     */
    'icons' => [
        'logo' => 'assets/studybuddy-brand/studybuddy-logo.png',
        'mark' => 'assets/studybuddy-brand/studybuddy-logo-mark.svg',
        'favicon_ico' => 'favicon.ico',
        'favicon_16' => 'assets/studybuddy-brand/studybuddy-icon-16.png',
        'favicon_32' => 'assets/studybuddy-brand/studybuddy-icon-32.png',
        'apple_touch' => 'assets/studybuddy-brand/studybuddy-apple-touch-icon.png',
        'pwa_192' => 'assets/studybuddy-brand/studybuddy-icon-192.png',
        'pwa_512' => 'assets/studybuddy-brand/studybuddy-icon-512.png',
        'maskable_512' => 'assets/studybuddy-brand/studybuddy-maskable-512.png',

        // Social preview: a 500x500 mark reads better than a stretched favicon.
        'social' => 'assets/studybuddy-brand/studybuddy-logo.png',
    ],
];
