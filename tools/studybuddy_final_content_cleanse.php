<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

function cols(string $table): array {
    if (!Schema::hasTable($table)) return [];
    return collect(DB::select("SHOW COLUMNS FROM `{$table}`"))->keyBy('Field')->all();
}

function c(string $table, string $column): bool {
    return Schema::hasTable($table) && Schema::hasColumn($table, $column);
}

function payload(string $table, array $data, string $fallback = 'StudyBuddy'): array {
    $columns = cols($table);
    $out = [];

    foreach ($data as $key => $value) {
        if (!isset($columns[$key])) continue;
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        $out[$key] = $value;
    }

    foreach ($columns as $field => $meta) {
        if (array_key_exists($field, $out)) continue;
        if (str_contains(strtolower((string)($meta->Extra ?? '')), 'auto_increment')) continue;

        $required = (($meta->Null ?? 'YES') === 'NO') && is_null($meta->Default ?? null);
        if (!$required) continue;

        $out[$field] = match (true) {
            $field === 'slug' => Str::slug($fallback),
            $field === 'key' => Str::slug($fallback),
            str_ends_with($field, '_key') => Str::slug($fallback),
            str_contains($field, 'title') => $fallback,
            str_contains($field, 'name') => $fallback,
            str_contains($field, 'label') => $fallback,
            str_contains($field, 'status') => 'published',
            str_contains($field, 'type') => 'content',
            str_contains($field, 'group') => 'StudyBuddy',
            str_starts_with($field, 'is_') => true,
            str_contains($field, 'enabled') => true,
            str_contains($field, 'sort') || str_contains($field, 'order') => 10,
            str_contains($field, 'url') => '/',
            str_contains($field, 'created_at') => now(),
            str_contains($field, 'updated_at') => now(),
            default => '',
        };
    }

    if (isset($columns['created_at']) && !isset($out['created_at'])) $out['created_at'] = now();
    if (isset($columns['updated_at'])) $out['updated_at'] = now();

    return $out;
}

function upsert(string $table, array $identity, array $data, string $fallback): void {
    if (!Schema::hasTable($table)) {
        echo "skip {$table}: missing\n";
        return;
    }

    $safeIdentity = [];
    foreach ($identity as $key => $value) {
        if (c($table, $key)) $safeIdentity[$key] = $value;
    }

    if (!$safeIdentity) {
        echo "skip {$table}: no identity\n";
        return;
    }

    DB::table($table)->updateOrInsert($safeIdentity, payload($table, $data, $fallback));
    echo "✓ {$table}: {$fallback}\n";
}

function setting(string $key, string $label, $value, string $group = 'General', string $type = 'text', int $order = 10): void {
    upsert('site_settings', ['key' => $key], [
        'key' => $key,
        'label' => $label,
        'value' => $value,
        'group' => $group,
        'type' => $type,
        'sort_order' => $order,
        'is_enabled' => true,
    ], $label);
}

function page(array $p): ?int {
    upsert('pages', ['slug' => $p['slug']], [
        'slug' => $p['slug'],
        'template' => $p['template'] ?? 'studybuddy',
        'title' => $p['title'],
        'nav_label' => $p['nav_label'] ?? $p['title'],
        'meta_title' => $p['meta_title'] ?? $p['title'].' | StudyBuddy',
        'meta_description' => $p['subtitle'],
        'eyebrow' => $p['eyebrow'],
        'hero_title' => $p['title'],
        'hero_subtitle' => $p['subtitle'],
        'hero_body' => $p['body'],
        'body' => $p['body'],
        'content' => $p['body'],
        'button_label' => $p['button_label'] ?? 'Explore StudyBuddy',
        'button_url' => $p['button_url'] ?? '/apps',
        'secondary_button_label' => $p['secondary_button_label'] ?? 'View roles',
        'secondary_button_url' => $p['secondary_button_url'] ?? '/roles',
        'sort_order' => $p['sort_order'] ?? 10,
        'is_enabled' => true,
        'status' => 'published',
        'settings' => ['editable_from_admin' => true, 'content_status' => 'user_ready'],
    ], $p['title']);

    if (!Schema::hasTable('pages')) return null;
    return (int) DB::table('pages')->where('slug', $p['slug'])->value('id');
}

function pageSection(?int $pageId, string $slug, array $s): void {
    if (!$pageId || !Schema::hasTable('page_sections')) return;

    $identity = c('page_sections', 'section_key')
        ? ['page_id' => $pageId, 'section_key' => $s['key']]
        : ['page_id' => $pageId, 'title' => $s['title']];

    upsert('page_sections', $identity, [
        'page_id' => $pageId,
        'page_slug' => $slug,
        'section_key' => $s['key'],
        'slug' => $slug.'-'.$s['key'],
        'section_type' => $s['type'] ?? 'content',
        'eyebrow' => $s['eyebrow'] ?? null,
        'title' => $s['title'],
        'subtitle' => $s['subtitle'] ?? null,
        'body' => $s['body'],
        'content' => $s['body'],
        'button_label' => $s['button_label'] ?? null,
        'button_url' => $s['button_url'] ?? null,
        'sort_order' => $s['sort_order'] ?? 10,
        'is_enabled' => true,
        'settings' => $s['settings'] ?? ['editable_from_admin' => true],
    ], $s['title']);
}

function homepage(array $s): ?int {
    if (!Schema::hasTable('homepage_sections')) return null;

    $identity = c('homepage_sections', 'section_key')
        ? ['section_key' => $s['key']]
        : ['title' => $s['title']];

    upsert('homepage_sections', $identity, [
        'section_key' => $s['key'],
        'section_type' => $s['type'] ?? 'content',
        'eyebrow' => $s['eyebrow'],
        'title' => $s['title'],
        'subtitle' => $s['subtitle'],
        'body' => $s['body'],
        'content' => $s['body'],
        'image_path' => $s['image_path'] ?? null,
        'background_image_path' => $s['background_image_path'] ?? null,
        'button_label' => $s['button_label'] ?? null,
        'button_url' => $s['button_url'] ?? null,
        'secondary_button_label' => $s['secondary_button_label'] ?? null,
        'secondary_button_url' => $s['secondary_button_url'] ?? null,
        'sort_order' => $s['sort_order'] ?? 10,
        'is_enabled' => true,
        'settings' => ['editable_from_admin' => true, 'content_status' => 'user_ready'],
    ], $s['title']);

    return c('homepage_sections', 'section_key')
        ? (int) DB::table('homepage_sections')->where('section_key', $s['key'])->value('id')
        : (int) DB::table('homepage_sections')->where('title', $s['title'])->value('id');
}

function homepageItem(?int $sectionId, string $sectionKey, array $i): void {
    if (!$sectionId || !Schema::hasTable('homepage_section_items')) return;

    $identity = c('homepage_section_items', 'item_key')
        ? ['homepage_section_id' => $sectionId, 'item_key' => $i['key']]
        : ['homepage_section_id' => $sectionId, 'title' => $i['title']];

    upsert('homepage_section_items', $identity, [
        'homepage_section_id' => $sectionId,
        'section_key' => $sectionKey,
        'item_key' => $i['key'],
        'title' => $i['title'],
        'subtitle' => $i['subtitle'] ?? null,
        'body' => $i['body'],
        'description' => $i['body'],
        'icon' => $i['icon'] ?? null,
        'image_path' => $i['image_path'] ?? null,
        'button_label' => $i['button_label'] ?? null,
        'button_url' => $i['button_url'] ?? null,
        'sort_order' => $i['sort_order'] ?? 10,
        'is_enabled' => true,
        'settings' => ['editable_from_admin' => true],
    ], $i['title']);
}

function nav(string $label, string $url, int $order): void {
    upsert('navigation_items', ['label' => $label], [
        'label' => $label,
        'url' => $url,
        'group' => 'main',
        'location' => 'main',
        'target' => '_self',
        'sort_order' => $order,
        'is_enabled' => true,
    ], $label);
}

function footer(string $group, string $label, string $url, int $order): void {
    upsert('footer_items', ['label' => $label, 'url' => $url], [
        'group' => $group,
        'label' => $label,
        'url' => $url,
        'target' => '_self',
        'sort_order' => $order,
        'is_enabled' => true,
    ], $label);
}

function appCopy(array $a): void {
    if (!Schema::hasTable('studybuddy_mini_app_platforms')) return;

    $existing = DB::table('studybuddy_mini_app_platforms')->where('slug', $a['slug'])->first();
    $image = $existing->image_path ?? $existing->hero_image ?? $a['image_path'];

    upsert('studybuddy_mini_app_platforms', ['slug' => $a['slug']], [
        'slug' => $a['slug'],
        'name' => $a['name'],
        'title' => $a['name'],
        'category' => $a['category'],
        'icon' => $a['icon'],
        'tagline' => $a['tagline'],
        'description' => $a['description'],
        'body' => $a['description'],
        'image_path' => $image,
        'hero_image' => $image,
        'button_label' => 'Open app',
        'button_url' => '/apps/'.$a['slug'],
        'sort_order' => $a['sort_order'],
        'is_active' => true,
        'is_enabled' => true,
        'settings' => ['editable_from_admin' => true, 'content_status' => 'user_ready'],
    ], $a['name']);
}

echo "\n=== StudyBuddy final content cleanse ===\n";

setting('site_name', 'Site Name', 'StudyBuddy', 'Brand', 'text', 1);
setting('site_tagline', 'Site Tagline', 'Learn. Play. Grow. Your Way.', 'Brand', 'text', 2);
setting('brand_promise', 'Brand Promise', 'StudyBuddy helps learners practise through playful app worlds, gives parents safe progress visibility, gives teachers class tools, and lets independent learners build confidence at their own pace.', 'Brand', 'textarea', 3);
setting('support_email', 'Support Email', 'support@studybuddy.fun', 'Contact', 'email', 10);

$nav = [
    ['label' => 'Home', 'url' => '/', 'roles' => ['all']],
    ['label' => 'Apps', 'url' => '/apps', 'roles' => ['all']],
    ['label' => 'Roles', 'url' => '/roles', 'roles' => ['all']],
    ['label' => 'Community', 'url' => '/community', 'roles' => ['all']],
    ['label' => 'Search', 'url' => '/search', 'roles' => ['all']],
];

$footer = [
    'Explore' => [
        ['label' => 'Apps', 'url' => '/apps'],
        ['label' => 'Roles', 'url' => '/roles'],
        ['label' => 'Community', 'url' => '/community'],
        ['label' => 'Search', 'url' => '/search'],
    ],
    'Account' => [
        ['label' => 'Dashboard', 'url' => '/dashboard'],
        ['label' => 'Profile Studio', 'url' => '/profile'],
        ['label' => 'Connect Code Help', 'url' => '/roles'],
    ],
    'StudyBuddy' => [
        ['label' => 'About', 'url' => '/about'],
        ['label' => 'Contact', 'url' => '/contact'],
        ['label' => 'Community Guidelines', 'url' => '/community-guidelines'],
    ],
    'Legal & Safety' => [
        ['label' => 'Privacy Policy', 'url' => '/privacy-policy'],
        ['label' => 'Terms', 'url' => '/terms'],
        ['label' => 'Disclaimer', 'url' => '/disclaimer'],
        ['label' => 'Cookies', 'url' => '/cookies'],
        ['label' => 'Copyright', 'url' => '/copyright'],
        ['label' => 'Data Deletion', 'url' => '/data-deletion'],
    ],
];

setting('shell_navigation_json', 'Shell Navigation', $nav, 'Navigation', 'json', 10);
setting('shell_footer_groups_json', 'Footer Groups', $footer, 'Footer', 'json', 20);

if (Schema::hasTable('navigation_items')) {
    if (c('navigation_items', 'is_enabled')) {
        DB::table('navigation_items')->whereNotIn('label', ['Home', 'Apps', 'Roles', 'Community', 'Search'])->update(['is_enabled' => false, 'updated_at' => now()]);
    }
    nav('Home', '/', 10);
    nav('Apps', '/apps', 20);
    nav('Roles', '/roles', 30);
    nav('Community', '/community', 40);
    nav('Search', '/search', 50);
}

if (Schema::hasTable('footer_items')) {
    foreach ($footer as $group => $items) {
        foreach ($items as $index => $item) {
            footer($group, $item['label'], $item['url'], ($index + 1) * 10);
        }
    }
}

$pages = [
    [
        'slug' => 'about',
        'eyebrow' => 'About StudyBuddy',
        'title' => 'A playful learning universe built for real progress.',
        'subtitle' => 'StudyBuddy helps learners practise skills, build confidence, and turn small daily actions into visible growth.',
        'body' => 'StudyBuddy connects mini learning apps, role-based dashboards, safe profile showcases, points, and guided progress tools. Students get practice that feels lighter. Parents get visibility without taking over. Teachers get classroom tools for tasks and quizzes. Independent learners get a self-paced space to stay consistent.',
        'button_label' => 'Explore apps',
        'button_url' => '/apps',
        'secondary_button_label' => 'How roles work',
        'secondary_button_url' => '/roles',
        'sort_order' => 10,
    ],
    [
        'slug' => 'apps',
        'eyebrow' => 'Apps',
        'title' => 'Choose a learning world and start a tiny win.',
        'subtitle' => 'Explore playful app worlds for maths, reading, spelling, focus, planning, quizzes, shapes, and flashcards.',
        'body' => 'Every app is part of the StudyBuddy universe. Learners can practise one skill at a time, earn points, save favourites, and build a profile around the ways they like to learn.',
        'button_label' => 'Open app universe',
        'button_url' => '/apps',
        'secondary_button_label' => 'View roles',
        'secondary_button_url' => '/roles',
        'sort_order' => 20,
    ],
    [
        'slug' => 'roles',
        'eyebrow' => 'Roles',
        'title' => 'One platform, different dashboards for every role.',
        'subtitle' => 'Students, parents, teachers, and independent learners each get tools that match what they actually need.',
        'body' => 'Students focus on practice and points. Parents connect only with a learner’s consent code. Teachers create classes, add verified students, and assign tasks. Independent learners build their own learning routine.',
        'button_label' => 'Open dashboard',
        'button_url' => '/dashboard',
        'secondary_button_label' => 'Explore community',
        'secondary_button_url' => '/community',
        'sort_order' => 30,
    ],
    [
        'slug' => 'community',
        'eyebrow' => 'Community',
        'title' => 'Public profiles for positive learning showcases.',
        'subtitle' => 'StudyBuddy community is built around progress, favourite apps, badges, and safe profile visibility.',
        'body' => 'Users can choose whether their profile is public. Public profiles show learning style and progress without direct messaging pressure.',
        'button_label' => 'Open community',
        'button_url' => '/community',
        'secondary_button_label' => 'Community rules',
        'secondary_button_url' => '/community-guidelines',
        'sort_order' => 40,
    ],
    [
        'slug' => 'contact',
        'eyebrow' => 'Contact StudyBuddy',
        'title' => 'Need help? Send a message to the StudyBuddy team.',
        'subtitle' => 'Use this form for account help, parent or teacher setup, safety questions, app feedback, or data requests.',
        'body' => 'Your message is saved in the admin Control Room so the StudyBuddy team can review it. Choose the closest topic and include the page, account email, and what happened.',
        'button_label' => 'Send a message',
        'button_url' => '/contact',
        'secondary_button_label' => 'Read guidelines',
        'secondary_button_url' => '/community-guidelines',
        'sort_order' => 50,
    ],
    [
        'slug' => 'privacy-policy',
        'eyebrow' => 'Privacy',
        'title' => 'Privacy Policy',
        'subtitle' => 'How StudyBuddy handles account, profile, learning, and safety information.',
        'body' => 'StudyBuddy uses account information to run login, dashboards, profiles, app progress, points, safety features, and role-based tools. Public profile visibility is controlled by the user from Profile Studio.',
        'button_label' => 'Manage profile',
        'button_url' => '/profile',
        'secondary_button_label' => 'Request deletion',
        'secondary_button_url' => '/data-deletion',
        'sort_order' => 60,
    ],
    [
        'slug' => 'terms',
        'eyebrow' => 'Terms',
        'title' => 'Terms of Use',
        'subtitle' => 'The rules for using StudyBuddy safely and respectfully.',
        'body' => 'By using StudyBuddy, users agree to keep the platform learning-focused, respectful, and safe. Accounts should not be misused, profile content should stay appropriate, and connection codes should only be shared with trusted parents or teachers.',
        'button_label' => 'Read guidelines',
        'button_url' => '/community-guidelines',
        'secondary_button_label' => 'Privacy policy',
        'secondary_button_url' => '/privacy-policy',
        'sort_order' => 70,
    ],
    [
        'slug' => 'disclaimer',
        'eyebrow' => 'Disclaimer',
        'title' => 'Learning Disclaimer',
        'subtitle' => 'StudyBuddy supports practice but does not replace teachers, parents, schools, or professional advice.',
        'body' => 'StudyBuddy provides practice tools, dashboards, points, and learning activities for general educational support. Scores, points, and badges are motivational signals, not official grades or professional assessments.',
        'button_label' => 'Explore roles',
        'button_url' => '/roles',
        'secondary_button_label' => 'Contact support',
        'secondary_button_url' => '/contact',
        'sort_order' => 80,
    ],
    [
        'slug' => 'cookies',
        'eyebrow' => 'Cookies',
        'title' => 'Cookie Notice',
        'subtitle' => 'How StudyBuddy uses cookies and session technology.',
        'body' => 'StudyBuddy may use cookies and session data to keep users logged in, protect forms, remember preferences, and keep dashboards working smoothly. Some account features may not work if cookies are blocked.',
        'button_label' => 'Privacy policy',
        'button_url' => '/privacy-policy',
        'secondary_button_label' => 'Contact support',
        'secondary_button_url' => '/contact',
        'sort_order' => 90,
    ],
    [
        'slug' => 'community-guidelines',
        'eyebrow' => 'Community',
        'title' => 'Community Guidelines',
        'subtitle' => 'A safe, positive space for learning profiles and progress.',
        'body' => 'StudyBuddy community profiles are for encouragement, not bullying, pressure, or unsafe interaction. Users should keep names, bios, and profile pictures appropriate and avoid sharing sensitive personal details.',
        'button_label' => 'Open community',
        'button_url' => '/community',
        'secondary_button_label' => 'Privacy policy',
        'secondary_button_url' => '/privacy-policy',
        'sort_order' => 100,
    ],
    [
        'slug' => 'copyright',
        'eyebrow' => 'Copyright',
        'title' => 'Copyright and Content',
        'subtitle' => 'Respect StudyBuddy content and only upload images you have permission to use.',
        'body' => 'StudyBuddy content, visual design, learning copy, app names, and platform materials should not be copied or reused outside the platform unless permission is given. Users should only upload profile pictures that are theirs or that they are allowed to use.',
        'button_label' => 'Contact support',
        'button_url' => '/contact',
        'secondary_button_label' => 'Terms',
        'secondary_button_url' => '/terms',
        'sort_order' => 110,
    ],
    [
        'slug' => 'data-deletion',
        'eyebrow' => 'Data',
        'title' => 'Data Deletion',
        'subtitle' => 'How users can request removal of account, profile, and learning data.',
        'body' => 'Users or guardians can request deletion of account details, uploaded profile pictures, profile content, public showcase data, and learning records where deletion applies. Some records may need to be retained temporarily for safety, security, or legal reasons.',
        'button_label' => 'Contact support',
        'button_url' => '/contact',
        'secondary_button_label' => 'Privacy policy',
        'secondary_button_url' => '/privacy-policy',
        'sort_order' => 120,
    ],
];

$sectionsBySlug = [
    'about' => [
        ['key' => 'mission', 'eyebrow' => 'Mission', 'title' => 'Make learning easier to start and easier to return to.', 'body' => 'StudyBuddy is designed around tiny wins: short practice, clear feedback, friendly visuals, and role-based support.'],
        ['key' => 'values', 'eyebrow' => 'Values', 'title' => 'Safe, understandable, and learner-first.', 'body' => 'The platform focuses on confidence, consistency, privacy, and positive progress instead of pressure.'],
    ],
    'roles' => [
        ['key' => 'safe-connections', 'eyebrow' => 'Consent first', 'title' => 'Parents and teachers cannot randomly add learners.', 'body' => 'Learners control their StudyBuddy Connect Code. A parent or teacher needs the learner’s email and current code before connecting that account.'],
    ],
    'privacy-policy' => [
        ['key' => 'stored-data', 'eyebrow' => 'Information used', 'title' => 'What StudyBuddy may store', 'body' => 'StudyBuddy may store account details, role, profile preferences, uploaded profile picture, favourite apps, points, assignments, and connection records.'],
        ['key' => 'user-control', 'eyebrow' => 'Control', 'title' => 'Users choose what appears publicly.', 'body' => 'Public profiles can be turned on or off from Profile Studio. Learners control their Connect Code and can regenerate it.'],
    ],
    'community-guidelines' => [
        ['key' => 'safe-zone', 'eyebrow' => 'Safe zone', 'title' => 'Keep profiles kind, simple, and learning-focused.', 'body' => 'Public profiles should celebrate effort, favourite apps, and creative identity without exposing private information.'],
    ],
    'data-deletion' => [
        ['key' => 'request-steps', 'eyebrow' => 'Steps', 'title' => 'What to include in a deletion request', 'body' => 'Send the account email, the type of data you want removed, and whether the request is for your own account or a learner account you are responsible for.'],
    ],
];

foreach ($pages as $p) {
    $id = page($p);
    foreach (($sectionsBySlug[$p['slug']] ?? []) as $idx => $s) {
        $s['sort_order'] = ($idx + 1) * 10;
        pageSection($id, $p['slug'], $s);
    }
}

$home = [
    [
        'key' => 'hero',
        'eyebrow' => 'StudyBuddy Learning Universe',
        'title' => 'Learn. Play. Grow. Your Way.',
        'subtitle' => 'A safe, playful platform where students practise, parents support progress, teachers guide tasks, and independent learners build better habits.',
        'body' => 'Choose a role, enter an app world, earn points, customise your profile, and keep learning with tiny wins that feel easy to start.',
        'image_path' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-learning.png',
        'button_label' => 'Explore apps',
        'button_url' => '/apps',
        'secondary_button_label' => 'Choose your role',
        'secondary_button_url' => '/roles',
        'sort_order' => 10,
        'items' => [
            ['key' => 'student-path', 'icon' => '🎒', 'title' => 'Student path', 'body' => 'Practise with mini apps, complete tasks, earn points, and build confidence one tiny win at a time.'],
            ['key' => 'parent-path', 'icon' => '🛡️', 'title' => 'Parent path', 'body' => 'Connect only with a learner’s consent code, then view progress signals and support learning calmly.'],
            ['key' => 'teacher-path', 'icon' => '🏫', 'title' => 'Teacher path', 'body' => 'Create classes, add verified students, assign tasks, and review student activity from one dashboard.'],
        ],
    ],
    [
        'key' => 'apps-universe',
        'eyebrow' => 'Apps',
        'title' => 'Mini app worlds for focused practice.',
        'subtitle' => 'Every app is designed to be short, clear, and motivating.',
        'body' => 'Learners can jump into maths, reading, spelling, focus, planning, quizzes, shapes, or flashcards without feeling overwhelmed.',
        'image_path' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-apps.png',
        'button_label' => 'Open app universe',
        'button_url' => '/apps',
        'sort_order' => 20,
        'items' => [
            ['key' => 'math-quest', 'icon' => '➗', 'title' => 'Math Quest', 'body' => 'Quick maths missions for speed, accuracy, and confidence.'],
            ['key' => 'reading-garden', 'icon' => '📚', 'title' => 'Reading Garden', 'body' => 'Gentle reading practice with calm progress and clear next steps.'],
            ['key' => 'focus-forest', 'icon' => '🌲', 'title' => 'Focus Forest', 'body' => 'Short focus sessions that help learners stay consistent.'],
        ],
    ],
    [
        'key' => 'safe-connections',
        'eyebrow' => 'Safety',
        'title' => 'Learners control who connects to them.',
        'subtitle' => 'Parents and teachers need a learner’s StudyBuddy Connect Code before adding that learner.',
        'body' => 'No random child linking. No password sharing. A learner can regenerate their code anytime from their own dashboard.',
        'image_path' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-parents.png',
        'button_label' => 'How roles work',
        'button_url' => '/roles',
        'sort_order' => 30,
        'items' => [
            ['key' => 'consent-first', 'icon' => '🔐', 'title' => 'Consent-first linking', 'body' => 'The learner shares a code only with a trusted parent or teacher.'],
            ['key' => 'fresh-codes', 'icon' => '🔄', 'title' => 'Fresh codes anytime', 'body' => 'Learners can regenerate their code if they want to stop old access.'],
            ['key' => 'visible-controls', 'icon' => '👀', 'title' => 'Clear dashboards', 'body' => 'Connected accounts and activity are shown in the right role dashboard.'],
        ],
    ],
    [
        'key' => 'community',
        'eyebrow' => 'Community',
        'title' => 'A safe showcase space, not a pressure zone.',
        'subtitle' => 'Users can share profiles, badges, favourite apps, and progress style without direct messaging.',
        'body' => 'Public profiles are optional. Users decide what appears and can keep sensitive information private.',
        'image_path' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-learning.png',
        'button_label' => 'Open community',
        'button_url' => '/community',
        'sort_order' => 40,
        'items' => [
            ['key' => 'profile-studio', 'icon' => '🪄', 'title' => 'Profile Studio', 'body' => 'Users can customise colours, badges, profile pictures, and favourite apps.'],
            ['key' => 'positive-progress', 'icon' => '⭐', 'title' => 'Positive progress', 'body' => 'Community is focused on encouragement and learning identity.'],
            ['key' => 'privacy-controls', 'icon' => '🔒', 'title' => 'Privacy controls', 'body' => 'Users choose whether their profile is public and what details are shown.'],
        ],
    ],
    [
        'key' => 'final-cta',
        'eyebrow' => 'Start now',
        'title' => 'Choose one tiny win today.',
        'subtitle' => 'Open an app, customise your profile, or explore how your role works.',
        'body' => 'StudyBuddy is built to make learning feel easier to start and easier to continue.',
        'button_label' => 'Explore apps',
        'button_url' => '/apps',
        'secondary_button_label' => 'Open roles',
        'secondary_button_url' => '/roles',
        'sort_order' => 50,
        'items' => [
            ['key' => 'cta-apps', 'icon' => '🎮', 'title' => 'Open apps', 'body' => 'Start a short practice session.'],
            ['key' => 'cta-profile', 'icon' => '🪄', 'title' => 'Build profile', 'body' => 'Make your StudyBuddy space feel like yours.'],
            ['key' => 'cta-community', 'icon' => '🌍', 'title' => 'Visit community', 'body' => 'See optional public learner showcases.'],
        ],
    ],
];

foreach ($home as $section) {
    $sectionId = homepage($section);
    foreach (($section['items'] ?? []) as $idx => $item) {
        $item['sort_order'] = ($idx + 1) * 10;
        homepageItem($sectionId, $section['key'], $item);
    }
}

$apps = [
    ['slug'=>'math-quest','name'=>'Math Quest','category'=>'Maths Practice','icon'=>'➗','tagline'=>'Short maths missions for speed, accuracy, and confidence.','description'=>'Math Quest helps learners practise quick calculations, number sense, and problem-solving through small challenges that feel achievable.','sort_order'=>10,'image_path'=>'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-apps.png'],
    ['slug'=>'spelling-sprint','name'=>'Spelling Sprint','category'=>'Language Practice','icon'=>'✍️','tagline'=>'Build spelling confidence with quick word challenges.','description'=>'Spelling Sprint gives learners a playful way to practise words, patterns, and memory without turning revision into pressure.','sort_order'=>20,'image_path'=>'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-learning.png'],
    ['slug'=>'reading-garden','name'=>'Reading Garden','category'=>'Reading','icon'=>'📚','tagline'=>'Gentle reading practice that grows with the learner.','description'=>'Reading Garden supports reading habits with calm prompts, simple goals, and progress that feels encouraging.','sort_order'=>30,'image_path'=>'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-learning.png'],
    ['slug'=>'focus-forest','name'=>'Focus Forest','category'=>'Focus','icon'=>'🌲','tagline'=>'Turn focus time into a calm learning routine.','description'=>'Focus Forest helps learners build consistency with short sessions, gentle structure, and a clear reason to return.','sort_order'=>40,'image_path'=>'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-parents.png'],
    ['slug'=>'planner-city','name'=>'Planner City','category'=>'Planning','icon'=>'🏙️','tagline'=>'Plan homework, revision, and tiny goals in one place.','description'=>'Planner City helps learners break big goals into smaller steps so studying feels more manageable.','sort_order'=>50,'image_path'=>'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-teachers.png'],
    ['slug'=>'quiz-galaxy','name'=>'Quiz Galaxy','category'=>'Quizzes','icon'=>'🌌','tagline'=>'Review knowledge through quick, friendly quizzes.','description'=>'Quiz Galaxy gives teachers and learners a simple way to review topics, spot gaps, and celebrate progress.','sort_order'=>60,'image_path'=>'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-apps.png'],
    ['slug'=>'shape-lab','name'=>'Shape Lab','category'=>'Visual Learning','icon'=>'🔷','tagline'=>'Explore shapes, patterns, and visual problem-solving.','description'=>'Shape Lab supports visual learners with pattern recognition, geometry ideas, and interactive practice.','sort_order'=>70,'image_path'=>'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-learning.png'],
    ['slug'=>'flashcard-forge','name'=>'Flashcard Forge','category'=>'Memory','icon'=>'🃏','tagline'=>'Turn revision into quick recall practice.','description'=>'Flashcard Forge helps learners practise recall through bite-sized cards that can support any subject.','sort_order'=>80,'image_path'=>'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-apps.png'],
];

foreach ($apps as $a) appCopy($a);

if (Schema::hasTable('studybuddy_content_pages')) {
    foreach ($pages as $p) {
        upsert('studybuddy_content_pages', ['slug' => $p['slug']], [
            'slug' => $p['slug'],
            'title' => $p['title'],
            'subtitle' => $p['subtitle'],
            'body' => $p['body'],
            'content' => $p['body'],
            'is_enabled' => true,
            'sort_order' => $p['sort_order'],
            'settings' => ['editable_from_admin' => true, 'content_status' => 'user_ready'],
        ], $p['title']);
    }
}

if (Schema::hasTable('studybuddy_content_items')) {
    $items = [
        ['key'=>'safe-connect-code','title'=>'StudyBuddy Connect Code','body'=>'Learners control who connects to them. Parents and teachers need the learner’s current code before adding that account.'],
        ['key'=>'profile-studio','title'=>'Profile Studio','body'=>'Users can update their profile picture, colours, badges, public visibility, favourite apps, and learning goals.'],
        ['key'=>'teacher-assignments','title'=>'Teacher Assignments','body'=>'Teachers can create classes, add verified students, and assign tasks or quizzes from their dashboard.'],
        ['key'=>'parent-progress','title'=>'Parent Progress','body'=>'Parents can review connected child progress signals and recent learning activity after a verified connection.'],
    ];

    foreach ($items as $idx => $item) {
        upsert('studybuddy_content_items', ['item_key' => $item['key']], [
            'item_key' => $item['key'],
            'title' => $item['title'],
            'body' => $item['body'],
            'content' => $item['body'],
            'is_enabled' => true,
            'sort_order' => ($idx + 1) * 10,
            'settings' => ['editable_from_admin' => true],
        ], $item['title']);
    }
}

/*
| Clean obvious bad DB text from broad content tables.
*/
$patterns = [
    '/lorem\s+ipsum/i' => 'StudyBuddy content is ready to edit from the admin panel.',
    '/\bdummy\b/i' => 'StudyBuddy content',
    '/placeholder\s+(copy|content|text)/i' => 'StudyBuddy user-ready content',
    '/template\s+(copy|content|text)/i' => 'StudyBuddy user-ready content',
    '/u{4,}/i' => 'StudyBuddy',
];

$tables = [
    'site_settings','pages','page_sections','page_section_items','homepage_sections','homepage_section_items',
    'navigation_items','footer_items','studybuddy_mini_app_platforms','studybuddy_content_pages','studybuddy_content_items',
    'studybuddy_platform_settings','studybuddy_launch_checklist_items',
];

foreach ($tables as $table) {
    if (!Schema::hasTable($table)) continue;

    $textCols = collect(DB::select("SHOW COLUMNS FROM `{$table}`"))
        ->filter(function ($col) {
            $type = strtolower((string)($col->Type ?? ''));
            return str_contains($type, 'char') || str_contains($type, 'text') || str_contains($type, 'json');
        })
        ->pluck('Field')
        ->values();

    if ($textCols->isEmpty()) continue;

    DB::table($table)->orderBy(c($table, 'id') ? 'id' : $textCols->first())->chunk(100, function ($rows) use ($table, $textCols, $patterns) {
        foreach ($rows as $row) {
            $updates = [];
            foreach ($textCols as $col) {
                $value = $row->{$col} ?? null;
                if (!is_string($value) || $value === '') continue;

                $newValue = $value;
                foreach ($patterns as $regex => $replacement) {
                    $newValue = preg_replace($regex, $replacement, $newValue);
                }

                if ($newValue !== $value) $updates[$col] = $newValue;
            }

            if ($updates && isset($row->id) && c($table, 'id')) {
                if (c($table, 'updated_at')) $updates['updated_at'] = now();
                DB::table($table)->where('id', $row->id)->update($updates);
                echo "✓ cleaned traces in {$table} #{$row->id}\n";
            }
        }
    });
}

echo "\nDONE ✅ StudyBuddy content is now user-ready and DB-managed.\n";
