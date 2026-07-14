<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

function tableColumns(string $table): array {
    return collect(DB::select("SHOW COLUMNS FROM `{$table}`"))->keyBy('Field')->all();
}

function makePayload(string $table, array $data, string $fallback): array {
    $columns = tableColumns($table);
    $out = [];

    foreach ($data as $key => $value) {
        if (!isset($columns[$key])) continue;
        $out[$key] = is_array($value) ? json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $value;
    }

    foreach ($columns as $field => $meta) {
        if (array_key_exists($field, $out)) continue;
        if (str_contains(strtolower((string) $meta->Extra), 'auto_increment')) continue;

        $required = (($meta->Null ?? 'YES') === 'NO') && is_null($meta->Default ?? null);
        if (!$required) continue;

        $out[$field] = match (true) {
            $field === 'slug' => Str::slug($fallback),
            str_contains($field, 'key') => Str::slug($fallback),
            str_contains($field, 'title') => $fallback,
            str_contains($field, 'label') => $fallback,
            str_contains($field, 'type') => 'content',
            str_contains($field, 'status') => 'published',
            str_starts_with($field, 'is_') => true,
            str_contains($field, 'enabled') => true,
            str_contains($field, 'order') => 10,
            str_contains($field, 'sort') => 10,
            str_contains($field, 'url') => '#',
            str_contains($field, 'created_at') => now(),
            str_contains($field, 'updated_at') => now(),
            default => '',
        };
    }

    if (isset($columns['updated_at'])) $out['updated_at'] = now();
    if (isset($columns['created_at']) && !isset($out['created_at'])) $out['created_at'] = now();

    return $out;
}

function upsertPage(array $page): ?int {
    if (!Schema::hasTable('pages')) return null;

    DB::table('pages')->updateOrInsert(
        ['slug' => $page['slug']],
        makePayload('pages', [
            'slug' => $page['slug'],
            'title' => $page['title'],
            'eyebrow' => $page['eyebrow'],
            'subtitle' => $page['subtitle'],
            'excerpt' => $page['subtitle'],
            'body' => $page['body'],
            'content' => $page['body'],
            'status' => 'published',
            'is_enabled' => true,
            'sort_order' => $page['sort_order'] ?? 10,
            'meta_title' => $page['title'],
            'meta_description' => $page['subtitle'],
            'settings' => $page['settings'] ?? [],
        ], $page['title'])
    );

    return (int) DB::table('pages')->where('slug', $page['slug'])->value('id');
}

function upsertSection(string $slug, ?int $pageId, array $section): void {
    if (!$pageId || !Schema::hasTable('page_sections')) return;

    $identity = [];

    if (Schema::hasColumn('page_sections', 'page_id')) {
        $identity['page_id'] = $pageId;
    }

    if (Schema::hasColumn('page_sections', 'section_key')) {
        $identity['section_key'] = $section['key'];
    } elseif (Schema::hasColumn('page_sections', 'slug')) {
        $identity['slug'] = $slug.'-'.$section['key'];
    } else {
        $identity['title'] = $section['title'];
    }

    DB::table('page_sections')->updateOrInsert(
        $identity,
        makePayload('page_sections', [
            'page_id' => $pageId,
            'page_slug' => $slug,
            'section_key' => $section['key'],
            'slug' => $slug.'-'.$section['key'],
            'section_type' => $section['type'] ?? 'content',
            'eyebrow' => $section['eyebrow'] ?? null,
            'title' => $section['title'],
            'subtitle' => $section['subtitle'] ?? null,
            'body' => $section['body'] ?? null,
            'content' => $section['body'] ?? null,
            'button_label' => $section['button_label'] ?? null,
            'button_url' => $section['button_url'] ?? null,
            'sort_order' => $section['sort_order'] ?? 10,
            'is_enabled' => true,
            'settings' => $section['settings'] ?? [],
        ], $section['title'])
    );
}

$roles = [
    [
        'key' => 'student',
        'icon' => '🎒',
        'title' => 'Student',
        'tagline' => 'Learn through apps, points, quests, and tiny wins.',
        'best_for' => 'Learners who want practice to feel playful and less stressful.',
        'dashboard' => 'App worlds, saved quests, profile, points wallet, recommended practice.',
        'controls' => 'Profile showcase, favorite apps, learning focus, privacy settings.',
        'safety' => 'Guided learning, respectful community profiles, no direct messaging.',
        'cta_label' => 'Explore apps',
        'cta_url' => '/apps',
    ],
    [
        'key' => 'parent',
        'icon' => '🛡️',
        'title' => 'Parent',
        'tagline' => 'Support learning while keeping the experience safe and calm.',
        'best_for' => 'Parents or guardians supporting a learner’s progress.',
        'dashboard' => 'Child-friendly guidance, progress support, safety-first profile controls.',
        'controls' => 'Child email connections, visibility choices, learning preferences.',
        'safety' => 'Privacy-first setup, parent support language, safe community showcase.',
        'cta_label' => 'Read safety guide',
        'cta_url' => '/community-guidelines',
    ],
    [
        'key' => 'teacher',
        'icon' => '🏫',
        'title' => 'Teacher',
        'tagline' => 'Use apps as simple classroom-friendly learning missions.',
        'best_for' => 'Teachers, tutors, and learning mentors.',
        'dashboard' => 'Classroom-focused shortcuts, app recommendations, activity planning.',
        'controls' => 'Teaching focus, role profile, recommended apps, professional profile.',
        'safety' => 'No direct messaging needed; public profiles stay positive and learning-focused.',
        'cta_label' => 'Explore learning worlds',
        'cta_url' => '/apps',
    ],
    [
        'key' => 'independent_learner',
        'icon' => '🚀',
        'title' => 'Independent Learner',
        'tagline' => 'Build your own routine with self-paced goals and focus tools.',
        'best_for' => 'Older learners or self-directed users building habits.',
        'dashboard' => 'Focus routines, favorite app worlds, personal profile, points progress.',
        'controls' => 'Custom colors, avatar styles, goals, favorite apps, public showcase.',
        'safety' => 'User controls decide what appears publicly.',
        'cta_label' => 'Customize profile',
        'cta_url' => '/profile',
    ],
];

$pages = [
    [
        'slug' => 'roles',
        'eyebrow' => 'Roles',
        'title' => 'How StudyBuddy roles work',
        'subtitle' => 'One platform, four different learning experiences.',
        'body' => 'Students, parents, teachers, and independent learners each get a focused experience while sharing the same safe StudyBuddy universe.',
        'sort_order' => 20,
        'sections' => [
            [
                'key' => 'role-cards',
                'eyebrow' => 'Interactive roles',
                'title' => 'Choose how you use StudyBuddy',
                'subtitle' => 'Every role gets a focused experience.',
                'body' => 'Tap through each role to understand dashboards, safety, learning tools, and profile options.',
                'sort_order' => 10,
                'settings' => ['roles' => $roles],
            ],
        ],
    ],
    [
        'slug' => 'about',
        'eyebrow' => 'About',
        'title' => 'A playful learning universe for tiny wins.',
        'subtitle' => 'StudyBuddy helps learners practice, build confidence, and return to learning without pressure.',
        'body' => 'StudyBuddy connects mini apps, role-based dashboards, points, profiles, and community showcases into one friendly learning platform.',
        'sort_order' => 30,
        'sections' => [
            ['key' => 'mission', 'eyebrow' => 'Mission', 'title' => 'Learning should feel clear, safe, and fun.', 'body' => 'StudyBuddy is designed around small steps, confidence, visual progress, and role-based support.', 'settings' => ['bullets' => ['Mini-app learning worlds', 'Profile showcases with privacy controls', 'Points and progress systems', 'Parent and teacher-friendly experiences']]],
            ['key' => 'promise', 'eyebrow' => 'Promise', 'title' => 'Built for learners, parents, and educators.', 'body' => 'The platform keeps information understandable and makes user controls easy to find.', 'sort_order' => 20],
        ],
    ],
    [
        'slug' => 'contact',
        'eyebrow' => 'Contact',
        'title' => 'Talk to StudyBuddy.',
        'subtitle' => 'Support, safety questions, parent enquiries, school requests, and feedback can start here.',
        'body' => 'StudyBuddy contact content can be updated from the admin panel so the right support information stays current.',
        'sort_order' => 40,
        'sections' => [
            ['key' => 'support', 'eyebrow' => 'Support', 'title' => 'Need help?', 'body' => 'Use this page for account questions, profile questions, learning support, safety feedback, or partnership requests.', 'settings' => ['bullets' => ['Account help', 'Profile and data questions', 'Parent or teacher support', 'Platform feedback']]],
        ],
    ],
    [
        'slug' => 'privacy-policy',
        'eyebrow' => 'Privacy',
        'title' => 'Privacy Policy',
        'subtitle' => 'How StudyBuddy treats account, learning, and profile information.',
        'body' => 'StudyBuddy is designed to collect only what is needed for accounts, learning progress, profile features, safety, and platform improvement.',
        'sort_order' => 50,
        'sections' => [
            ['key' => 'data', 'eyebrow' => 'Data', 'title' => 'Information we may use', 'body' => 'Account details, profile preferences, learning progress, profile pictures, points, app activity, and safety-related information may be used to run the platform.', 'settings' => ['bullets' => ['Account and login details', 'Profile and customization settings', 'Learning progress and points', 'Uploaded profile picture if provided']]],
            ['key' => 'choices', 'eyebrow' => 'Choices', 'title' => 'User controls', 'body' => 'Users can control profile visibility, public showcase details, favorite apps, and data deletion requests.', 'sort_order' => 20],
        ],
    ],
    [
        'slug' => 'terms',
        'eyebrow' => 'Terms',
        'title' => 'Terms of Use',
        'subtitle' => 'Rules for using StudyBuddy respectfully and safely.',
        'body' => 'StudyBuddy users agree to keep the platform kind, honest, learning-focused, and respectful of others.',
        'sort_order' => 60,
        'sections' => [
            ['key' => 'rules', 'eyebrow' => 'Rules', 'title' => 'Use StudyBuddy kindly', 'body' => 'Do not misuse accounts, upload harmful content, attempt to access other accounts, or use the platform to harass others.', 'settings' => ['bullets' => ['Use your own account', 'Keep profile content respectful', 'Do not attempt unsafe access', 'Follow community guidelines']]],
            ['key' => 'learning', 'eyebrow' => 'Learning', 'title' => 'Educational support', 'body' => 'StudyBuddy supports practice and motivation but does not replace teachers, guardians, schools, or professional advice.', 'sort_order' => 20],
        ],
    ],
    [
        'slug' => 'disclaimer',
        'eyebrow' => 'Disclaimer',
        'title' => 'Learning Disclaimer',
        'subtitle' => 'StudyBuddy supports learning but does not replace professional guidance.',
        'body' => 'StudyBuddy provides educational practice tools, motivational features, and learning activities for general support.',
        'sort_order' => 70,
        'sections' => [
            ['key' => 'education', 'eyebrow' => 'Education', 'title' => 'Practice support only', 'body' => 'Results, points, quizzes, and app activity are designed to motivate learning, not to provide official grades or professional assessment.'],
        ],
    ],
    [
        'slug' => 'cookies',
        'eyebrow' => 'Cookies',
        'title' => 'Cookie Notice',
        'subtitle' => 'How StudyBuddy uses cookies and session technology.',
        'body' => 'StudyBuddy may use cookies to keep users logged in, protect sessions, remember preferences, and improve the platform experience.',
        'sort_order' => 80,
        'sections' => [
            ['key' => 'sessions', 'eyebrow' => 'Sessions', 'title' => 'Keeping accounts working', 'body' => 'Cookies and session data help StudyBuddy remember logged-in users and protect forms from unsafe requests.'],
            ['key' => 'choices', 'eyebrow' => 'Choices', 'title' => 'Browser controls', 'body' => 'Users can manage cookies through browser settings, but some account features may stop working if cookies are blocked.', 'sort_order' => 20],
        ],
    ],
    [
        'slug' => 'community-guidelines',
        'eyebrow' => 'Community',
        'title' => 'Community Guidelines',
        'subtitle' => 'A safe, kind, progress-focused space.',
        'body' => 'StudyBuddy community profiles are for positive learning showcases, not direct messaging, pressure, or unsafe interaction.',
        'sort_order' => 90,
        'sections' => [
            ['key' => 'kindness', 'eyebrow' => 'Kindness', 'title' => 'Keep it respectful', 'body' => 'Profiles, names, bios, badges, and showcase content should be positive, safe, and learning-focused.', 'settings' => ['bullets' => ['Be kind', 'Do not bully or shame', 'Do not share private information', 'Report unsafe content']]],
            ['key' => 'profiles', 'eyebrow' => 'Profiles', 'title' => 'Public showcase rules', 'body' => 'Users control what appears publicly. Community profiles should not include sensitive personal details.', 'sort_order' => 20],
        ],
    ],
    [
        'slug' => 'copyright',
        'eyebrow' => 'Copyright',
        'title' => 'Copyright and Content',
        'subtitle' => 'Respect StudyBuddy materials and uploaded content.',
        'body' => 'StudyBuddy visuals, writing, app designs, and learning materials should be used respectfully and only in approved ways.',
        'sort_order' => 100,
        'sections' => [
            ['key' => 'content', 'eyebrow' => 'Content', 'title' => 'Platform materials', 'body' => 'StudyBuddy-created content belongs to the platform owner unless clearly stated otherwise. Users should only upload profile pictures they have permission to use.'],
        ],
    ],
    [
        'slug' => 'data-deletion',
        'eyebrow' => 'Data',
        'title' => 'Data Deletion',
        'subtitle' => 'How users can request account or profile data deletion.',
        'body' => 'Users can request deletion of profile information, uploaded profile pictures, account details, and learning data where required.',
        'sort_order' => 110,
        'sections' => [
            ['key' => 'request', 'eyebrow' => 'Request', 'title' => 'How deletion works', 'body' => 'Users or guardians can request deletion through the contact/support route. StudyBuddy can remove profile details, uploaded images, and account-related data when required.', 'settings' => ['bullets' => ['Account details', 'Profile picture', 'Public showcase content', 'Learning progress where deletion applies']]],
        ],
    ],
];

foreach ($pages as $page) {
    $pageId = upsertPage($page);
    echo "✓ page {$page['slug']}\n";

    foreach ($page['sections'] ?? [] as $index => $section) {
        $section['sort_order'] = $section['sort_order'] ?? (($index + 1) * 10);
        upsertSection($page['slug'], $pageId, $section);
        echo "  - section {$section['key']}\n";
    }
}

$nav = [
    ['label' => 'Home', 'url' => '/', 'roles' => ['all']],
    ['label' => 'Apps', 'url' => '/apps', 'roles' => ['all']],
    ['label' => 'Roles', 'url' => '/roles', 'roles' => ['all']],
    ['label' => 'Community', 'url' => '/community', 'roles' => ['all']],
    ['label' => 'Search', 'url' => '/search', 'roles' => ['all']],
    ['label' => 'Dashboard', 'url' => '/dashboard', 'roles' => ['auth']],
    ['label' => 'Profile', 'url' => '/profile', 'roles' => ['auth']],
];

$footerGroups = [
    'Explore' => [
        ['label' => 'Apps', 'url' => '/apps'],
        ['label' => 'Roles', 'url' => '/roles'],
        ['label' => 'Community', 'url' => '/community'],
        ['label' => 'Search', 'url' => '/search'],
    ],
    'StudyBuddy' => [
        ['label' => 'About', 'url' => '/about'],
        ['label' => 'Contact', 'url' => '/contact'],
        ['label' => 'Community Guidelines', 'url' => '/community-guidelines'],
    ],
    'Legal' => [
        ['label' => 'Privacy Policy', 'url' => '/privacy-policy'],
        ['label' => 'Terms', 'url' => '/terms'],
        ['label' => 'Disclaimer', 'url' => '/disclaimer'],
        ['label' => 'Cookies', 'url' => '/cookies'],
        ['label' => 'Copyright', 'url' => '/copyright'],
        ['label' => 'Data Deletion', 'url' => '/data-deletion'],
    ],
];

if (Schema::hasTable('site_settings')) {
    DB::table('site_settings')->updateOrInsert(
        ['key' => 'shell_navigation_json'],
        makePayload('site_settings', [
            'key' => 'shell_navigation_json',
            'label' => 'Shell Navigation',
            'group' => 'Navigation',
            'type' => 'json',
            'value' => $nav,
            'is_enabled' => true,
            'sort_order' => 10,
        ], 'Shell Navigation')
    );

    DB::table('site_settings')->updateOrInsert(
        ['key' => 'shell_footer_groups_json'],
        makePayload('site_settings', [
            'key' => 'shell_footer_groups_json',
            'label' => 'Footer Groups',
            'group' => 'Footer',
            'type' => 'json',
            'value' => $footerGroups,
            'is_enabled' => true,
            'sort_order' => 20,
        ], 'Footer Groups')
    );

    echo "✓ shell nav/footer settings synced\n";
}

if (Schema::hasTable('navigation_items')) {
    if (Schema::hasColumn('navigation_items', 'is_enabled')) {
        DB::table('navigation_items')
            ->whereIn('label', ['Parents', 'Teachers'])
            ->update(['is_enabled' => false, 'updated_at' => now()]);
    }

    foreach ([
        ['label' => 'Home', 'url' => '/', 'sort_order' => 10],
        ['label' => 'Apps', 'url' => '/apps', 'sort_order' => 20],
        ['label' => 'Roles', 'url' => '/roles', 'sort_order' => 30],
        ['label' => 'Community', 'url' => '/community', 'sort_order' => 40],
        ['label' => 'Search', 'url' => '/search', 'sort_order' => 50],
        ['label' => 'Profile', 'url' => '/profile', 'sort_order' => 90],
    ] as $item) {
        DB::table('navigation_items')->updateOrInsert(
            ['label' => $item['label']],
            makePayload('navigation_items', array_merge($item, [
                'is_enabled' => true,
                'group' => 'main',
                'location' => 'main',
                'target' => '_self',
            ]), $item['label'])
        );
    }

    echo "✓ navigation_items synced\n";
}

if (Schema::hasTable('footer_items')) {
    foreach ($footerGroups as $group => $items) {
        foreach ($items as $index => $item) {
            DB::table('footer_items')->updateOrInsert(
                ['label' => $item['label'], 'url' => $item['url']],
                makePayload('footer_items', [
                    'group' => $group,
                    'label' => $item['label'],
                    'url' => $item['url'],
                    'sort_order' => ($index + 1) * 10,
                    'is_enabled' => true,
                    'target' => '_self',
                ], $item['label'])
            );
        }
    }

    echo "✓ footer_items synced\n";
}

echo "\nDONE: StudyBuddy info, legal, roles, navigation, and footer content synced.\n";
