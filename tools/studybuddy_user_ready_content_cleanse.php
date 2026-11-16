<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

function columnsOf(string $table): array {
    if (!Schema::hasTable($table)) return [];
    return collect(DB::select("SHOW COLUMNS FROM `{$table}`"))->keyBy('Field')->all();
}

function hasColumnSafe(string $table, string $column): bool {
    return Schema::hasTable($table) && Schema::hasColumn($table, $column);
}

function buildPayload(string $table, array $data, string $fallback = 'StudyBuddy Content'): array {
    $columns = columnsOf($table);
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

        $extra = strtolower((string) ($meta->Extra ?? ''));
        if (str_contains($extra, 'auto_increment')) continue;

        $required = (($meta->Null ?? 'YES') === 'NO') && is_null($meta->Default ?? null);
        if (!$required) continue;

        $out[$field] = match (true) {
            $field === 'slug' => Str::slug($fallback),
            $field === 'key' => Str::slug($fallback),
            str_ends_with($field, '_key') => Str::slug($fallback),
            str_contains($field, 'title') => $fallback,
            str_contains($field, 'label') => $fallback,
            str_contains($field, 'name') => $fallback,
            str_contains($field, 'status') => 'published',
            str_contains($field, 'template') => 'studybuddy',
            str_contains($field, 'type') => 'content',
            str_contains($field, 'group') => 'StudyBuddy',
            str_contains($field, 'url') => '#',
            str_starts_with($field, 'is_') => true,
            str_contains($field, 'enabled') => true,
            str_contains($field, 'order') => 10,
            str_contains($field, 'sort') => 10,
            str_contains($field, 'created_at') => now(),
            str_contains($field, 'updated_at') => now(),
            default => '',
        };
    }

    if (isset($columns['created_at']) && !isset($out['created_at'])) $out['created_at'] = now();
    if (isset($columns['updated_at'])) $out['updated_at'] = now();

    return $out;
}

function upsertSafe(string $table, array $identity, array $data, string $fallback): void {
    if (!Schema::hasTable($table)) {
        echo "skip {$table}: table missing\n";
        return;
    }

    $columns = columnsOf($table);
    $safeIdentity = [];

    foreach ($identity as $key => $value) {
        if (isset($columns[$key])) $safeIdentity[$key] = $value;
    }

    if (!$safeIdentity) {
        echo "skip {$table}: no valid identity\n";
        return;
    }

    DB::table($table)->updateOrInsert(
        $safeIdentity,
        buildPayload($table, $data, $fallback)
    );

    echo "✓ {$table}: {$fallback}\n";
}

function setting(string $key, string $label, $value, string $group = 'General', string $type = 'text', int $order = 10): void {
    upsertSafe('site_settings', ['key' => $key], [
        'key' => $key,
        'label' => $label,
        'value' => $value,
        'type' => $type,
        'group' => $group,
        'is_enabled' => true,
        'sort_order' => $order,
    ], $label);
}

function page(array $page): ?int {
    upsertSafe('pages', ['slug' => $page['slug']], [
        'slug' => $page['slug'],
        'template' => $page['template'] ?? 'studybuddy',
        'title' => $page['title'],
        'nav_label' => $page['nav_label'] ?? $page['title'],
        'meta_title' => $page['meta_title'] ?? ($page['title'].' | StudyBuddy'),
        'meta_description' => $page['meta_description'] ?? $page['subtitle'],
        'eyebrow' => $page['eyebrow'],
        'hero_title' => $page['title'],
        'hero_subtitle' => $page['subtitle'],
        'hero_body' => $page['body'],
        'body' => $page['body'],
        'content' => $page['body'],
        'button_label' => $page['button_label'] ?? 'Explore StudyBuddy',
        'button_url' => $page['button_url'] ?? '/apps',
        'secondary_button_label' => $page['secondary_button_label'] ?? 'View roles',
        'secondary_button_url' => $page['secondary_button_url'] ?? '/roles',
        'sort_order' => $page['sort_order'] ?? 10,
        'is_enabled' => true,
        'status' => 'published',
        'settings' => $page['settings'] ?? ['editable_from_admin' => true],
    ], $page['title']);

    if (!Schema::hasTable('pages')) return null;

    return (int) DB::table('pages')->where('slug', $page['slug'])->value('id');
}

function pageSection(int $pageId, string $pageSlug, array $section): void {
    if (!$pageId || !Schema::hasTable('page_sections')) return;

    $identity = hasColumnSafe('page_sections', 'page_id') && hasColumnSafe('page_sections', 'section_key')
        ? ['page_id' => $pageId, 'section_key' => $section['key']]
        : ['slug' => $pageSlug.'-'.$section['key']];

    upsertSafe('page_sections', $identity, [
        'page_id' => $pageId,
        'page_slug' => $pageSlug,
        'section_key' => $section['key'],
        'slug' => $pageSlug.'-'.$section['key'],
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
    ], $section['title']);
}

function homepageSection(array $section): ?int {
    if (!Schema::hasTable('homepage_sections')) return null;

    $identity = hasColumnSafe('homepage_sections', 'section_key')
        ? ['section_key' => $section['key']]
        : ['title' => $section['title']];

    upsertSafe('homepage_sections', $identity, [
        'section_key' => $section['key'],
        'section_type' => $section['type'] ?? 'content',
        'eyebrow' => $section['eyebrow'] ?? null,
        'title' => $section['title'],
        'subtitle' => $section['subtitle'] ?? null,
        'body' => $section['body'] ?? null,
        'content' => $section['body'] ?? null,
        'image_path' => $section['image_path'] ?? null,
        'background_image_path' => $section['background_image_path'] ?? null,
        'button_label' => $section['button_label'] ?? null,
        'button_url' => $section['button_url'] ?? null,
        'secondary_button_label' => $section['secondary_button_label'] ?? null,
        'secondary_button_url' => $section['secondary_button_url'] ?? null,
        'sort_order' => $section['sort_order'] ?? 10,
        'is_enabled' => true,
        'settings' => $section['settings'] ?? ['editable_from_admin' => true],
    ], $section['title']);

    if (hasColumnSafe('homepage_sections', 'section_key')) {
        return (int) DB::table('homepage_sections')->where('section_key', $section['key'])->value('id');
    }

    return (int) DB::table('homepage_sections')->where('title', $section['title'])->value('id');
}

function homepageItem(?int $sectionId, string $sectionKey, array $item): void {
    if (!$sectionId || !Schema::hasTable('homepage_section_items')) return;

    $identity = hasColumnSafe('homepage_section_items', 'item_key')
        ? ['homepage_section_id' => $sectionId, 'item_key' => $item['key']]
        : ['homepage_section_id' => $sectionId, 'title' => $item['title']];

    upsertSafe('homepage_section_items', $identity, [
        'homepage_section_id' => $sectionId,
        'section_key' => $sectionKey,
        'item_key' => $item['key'],
        'title' => $item['title'],
        'subtitle' => $item['subtitle'] ?? null,
        'body' => $item['body'] ?? null,
        'description' => $item['body'] ?? null,
        'icon' => $item['icon'] ?? null,
        'image_path' => $item['image_path'] ?? null,
        'button_label' => $item['button_label'] ?? null,
        'button_url' => $item['button_url'] ?? null,
        'sort_order' => $item['sort_order'] ?? 10,
        'is_enabled' => true,
        'settings' => $item['settings'] ?? [],
    ], $item['title']);
}

function navItem(string $label, string $url, int $order): void {
    upsertSafe('navigation_items', ['label' => $label], [
        'label' => $label,
        'url' => $url,
        'group' => 'main',
        'location' => 'main',
        'target' => '_self',
        'sort_order' => $order,
        'is_enabled' => true,
    ], $label);
}

function footerItem(string $group, string $label, string $url, int $order): void {
    upsertSafe('footer_items', ['label' => $label, 'url' => $url], [
        'group' => $group,
        'label' => $label,
        'url' => $url,
        'target' => '_self',
        'sort_order' => $order,
        'is_enabled' => true,
    ], $label);
}

function appItem(array $app): void {
    if (!Schema::hasTable('studybuddy_mini_app_platforms')) return;

    $existing = DB::table('studybuddy_mini_app_platforms')->where('slug', $app['slug'])->first();
    $existingImage = $existing->hero_image ?? $existing->image_path ?? null;

    $image = $existingImage ?: ($app['image_path'] ?? 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-apps.png');

    upsertSafe('studybuddy_mini_app_platforms', ['slug' => $app['slug']], [
        'slug' => $app['slug'],
        'name' => $app['name'],
        'title' => $app['name'],
        'category' => $app['category'],
        'icon' => $app['icon'],
        'tagline' => $app['tagline'],
        'description' => $app['description'],
        'body' => $app['description'],
        'image_path' => $image,
        'hero_image' => $image,
        'button_label' => 'Open app',
        'button_url' => '/apps/'.$app['slug'],
        'sort_order' => $app['sort_order'],
        'is_active' => true,
        'is_enabled' => true,
        'settings' => [
            'editable_from_admin' => true,
            'user_ready_copy' => true,
            'recommended_for' => $app['recommended_for'] ?? ['student', 'independent_learner'],
        ],
    ], $app['name']);
}

/*
|--------------------------------------------------------------------------
| Site Settings
|--------------------------------------------------------------------------
*/

setting('site_name', 'Site Name', 'StudyBuddy', 'Brand', 'text', 1);
setting('site_tagline', 'Site Tagline', 'Learn. Play. Grow. Your Way.', 'Brand', 'text', 2);
setting('brand_promise', 'Brand Promise', 'StudyBuddy is a safe learning universe where students practise through playful apps, parents support progress, teachers guide tasks, and independent learners build confidence at their own pace.', 'Brand', 'textarea', 3);
setting('homepage_primary_cta', 'Homepage Primary CTA', 'Explore learning apps', 'Homepage', 'text', 10);
setting('homepage_secondary_cta', 'Homepage Secondary CTA', 'Choose your role', 'Homepage', 'text', 11);
setting('support_email', 'Support Email', 'support@studybuddy.fun', 'Support', 'email', 20);

$cleanNav = [
    ['label' => 'Home', 'url' => '/', 'roles' => ['all']],
    ['label' => 'Apps', 'url' => '/apps', 'roles' => ['all']],
    ['label' => 'Roles', 'url' => '/roles', 'roles' => ['all']],
    ['label' => 'Community', 'url' => '/community', 'roles' => ['all']],
    ['label' => 'Search', 'url' => '/search', 'roles' => ['all']],
];

$cleanFooter = [
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

setting('shell_navigation_json', 'Shell Navigation', $cleanNav, 'Navigation', 'json', 10);
setting('shell_footer_groups_json', 'Footer Groups', $cleanFooter, 'Footer', 'json', 20);

/*
|--------------------------------------------------------------------------
| Navigation + Footer Tables
|--------------------------------------------------------------------------
*/

if (Schema::hasTable('navigation_items')) {
    if (hasColumnSafe('navigation_items', 'is_enabled')) {
        DB::table('navigation_items')->whereNotIn('label', ['Home','Apps','Roles','Community','Search'])->update(['is_enabled' => false, 'updated_at' => now()]);
    }

    navItem('Home', '/', 10);
    navItem('Apps', '/apps', 20);
    navItem('Roles', '/roles', 30);
    navItem('Community', '/community', 40);
    navItem('Search', '/search', 50);
}

if (Schema::hasTable('footer_items')) {
    foreach ($cleanFooter as $group => $items) {
        foreach ($items as $i => $item) {
            footerItem($group, $item['label'], $item['url'], ($i + 1) * 10);
        }
    }
}

/*
|--------------------------------------------------------------------------
| DB Pages
|--------------------------------------------------------------------------
*/

$pages = [
    [
        'slug' => 'about',
        'eyebrow' => 'About StudyBuddy',
        'title' => 'A playful learning universe built for real progress.',
        'subtitle' => 'StudyBuddy helps learners practise skills, build confidence, and turn small daily actions into visible growth.',
        'body' => 'StudyBuddy combines mini learning apps, role-based dashboards, safe profile showcases, points, and guided progress tools. Students get practice that feels lighter. Parents get visibility without taking over. Teachers get classroom tools for tasks and quizzes. Independent learners get a self-paced space to stay consistent.',
        'button_label' => 'Explore apps',
        'button_url' => '/apps',
        'secondary_button_label' => 'How roles work',
        'secondary_button_url' => '/roles',
        'sort_order' => 10,
        'sections' => [
            [
                'key' => 'mission',
                'eyebrow' => 'Mission',
                'title' => 'Make learning easier to start and easier to return to.',
                'body' => 'The platform is designed around tiny wins: short practice, clear feedback, friendly visuals, and dashboards that show what matters for each role.',
                'settings' => ['bullets' => ['Mini apps for practice', 'Profiles and points for motivation', 'Parent and teacher dashboards', 'Community showcase without direct messaging']],
            ],
            [
                'key' => 'values',
                'eyebrow' => 'Values',
                'title' => 'Safe, understandable, and learner-first.',
                'body' => 'StudyBuddy avoids pressure-based ranking and focuses on confidence, consistency, privacy, and positive progress.',
            ],
        ],
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
        'settings' => ['editable_from_admin' => true, 'page_role' => 'app_catalog'],
        'sections' => [
            [
                'key' => 'how-apps-work',
                'eyebrow' => 'How it works',
                'title' => 'Short sessions. Clear goals. Friendly progress.',
                'body' => 'Apps are designed to be easy to enter, easy to understand, and useful for daily practice.',
                'settings' => ['bullets' => ['Pick one app', 'Complete a small activity', 'Earn points', 'Return when ready']],
            ],
        ],
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
        'sections' => [
            [
                'key' => 'safe-connections',
                'eyebrow' => 'Consent first',
                'title' => 'Parents and teachers cannot randomly add learners.',
                'body' => 'Learners control their StudyBuddy Connect Code. A parent or teacher needs the learner’s email and current code before connecting that account.',
                'settings' => ['bullets' => ['No password sharing', 'No random child linking', 'Learners can regenerate their code', 'Connections are visible in dashboards']],
            ],
        ],
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
        'sections' => [
            [
                'key' => 'safe-showcase',
                'eyebrow' => 'Safe showcase',
                'title' => 'Community is for encouragement, not comparison pressure.',
                'body' => 'Profiles are meant to celebrate learning habits, favourite apps, and creative identity while keeping sensitive details private.',
            ],
        ],
    ],
    [
        'slug' => 'contact',
        'eyebrow' => 'Contact',
        'title' => 'Need help with StudyBuddy?',
        'subtitle' => 'Use this page for account help, safety questions, parent support, teacher setup, or platform feedback.',
        'body' => 'StudyBuddy support can help with login issues, profile settings, child connection codes, teacher classes, app content, and data requests. For urgent safety concerns, include the account email and a clear description of what happened.',
        'button_label' => 'Open dashboard',
        'button_url' => '/dashboard',
        'secondary_button_label' => 'Read safety rules',
        'secondary_button_url' => '/community-guidelines',
        'sort_order' => 50,
        'sections' => [
            [
                'key' => 'what-to-send',
                'eyebrow' => 'Support checklist',
                'title' => 'Send the right details so support can help faster.',
                'body' => 'Include your account email, the page where the issue happened, what you expected, and what appeared instead.',
                'settings' => ['bullets' => ['Account email', 'Page or feature name', 'Screenshot if possible', 'Clear description']],
            ],
        ],
    ],
    [
        'slug' => 'privacy-policy',
        'eyebrow' => 'Privacy',
        'title' => 'Privacy Policy',
        'subtitle' => 'How StudyBuddy handles account, profile, learning, and safety information.',
        'body' => 'StudyBuddy uses account information to run login, dashboards, profiles, app progress, points, safety features, and role-based tools. The platform is designed to avoid unnecessary data collection and to give users control over public profile visibility.',
        'button_label' => 'Manage profile',
        'button_url' => '/profile',
        'secondary_button_label' => 'Request deletion',
        'secondary_button_url' => '/data-deletion',
        'sort_order' => 60,
        'sections' => [
            [
                'key' => 'data-used',
                'eyebrow' => 'Information used',
                'title' => 'What StudyBuddy may store',
                'body' => 'StudyBuddy may store account details, role, profile preferences, uploaded profile picture, favourite apps, points, assignments, and connection records.',
                'settings' => ['bullets' => ['Account and role details', 'Profile and avatar choices', 'Learning activity and points', 'Parent/teacher connection records']],
            ],
            [
                'key' => 'user-control',
                'eyebrow' => 'Control',
                'title' => 'Users choose what appears publicly.',
                'body' => 'Public profiles can be turned on or off from Profile Studio. Learners control their Connect Code and can regenerate it.',
            ],
        ],
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
        'sections' => [
            [
                'key' => 'user-rules',
                'eyebrow' => 'Rules',
                'title' => 'Use StudyBuddy with honesty and care.',
                'body' => 'Do not try to access another account, add learners without consent, upload harmful content, or use profiles to harass anyone.',
                'settings' => ['bullets' => ['Use your own account', 'Respect learner privacy', 'Keep profile content safe', 'Follow community rules']],
            ],
        ],
    ],
    [
        'slug' => 'disclaimer',
        'eyebrow' => 'Disclaimer',
        'title' => 'Learning Disclaimer',
        'subtitle' => 'StudyBuddy supports learning practice but does not replace teachers, parents, or professional advice.',
        'body' => 'StudyBuddy provides practice tools, dashboards, points, and learning activities for general educational support. Scores, points, and badges are motivational signals, not official grades or professional assessments.',
        'button_label' => 'Explore roles',
        'button_url' => '/roles',
        'secondary_button_label' => 'Contact support',
        'secondary_button_url' => '/contact',
        'sort_order' => 80,
        'sections' => [
            [
                'key' => 'learning-support',
                'eyebrow' => 'Support only',
                'title' => 'Use progress signals as guidance.',
                'body' => 'Progress data can help users decide what to practise next, but learning decisions should still involve teachers, guardians, or trusted educators when needed.',
            ],
        ],
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
        'sections' => [
            [
                'key' => 'why-cookies',
                'eyebrow' => 'Purpose',
                'title' => 'Cookies help the platform remember secure sessions.',
                'body' => 'Cookies support login, CSRF protection, role dashboards, profile settings, and smoother navigation.',
            ],
        ],
    ],
    [
        'slug' => 'community-guidelines',
        'eyebrow' => 'Community',
        'title' => 'Community Guidelines',
        'subtitle' => 'A safe, positive space for learning profiles and progress.',
        'body' => 'StudyBuddy community profiles are for encouragement, not direct messaging, bullying, shaming, or pressure. Users should keep names, bios, and profile pictures appropriate and avoid sharing sensitive personal details.',
        'button_label' => 'Open community',
        'button_url' => '/community',
        'secondary_button_label' => 'Privacy policy',
        'secondary_button_url' => '/privacy-policy',
        'sort_order' => 100,
        'sections' => [
            [
                'key' => 'safe-zone',
                'eyebrow' => 'Safe zone',
                'title' => 'Keep profiles kind, simple, and learning-focused.',
                'body' => 'Public profiles should celebrate effort, favourite apps, and creative identity without exposing private information.',
                'settings' => ['bullets' => ['Be kind', 'Do not shame progress', 'Do not share private details', 'Report unsafe content']],
            ],
        ],
    ],
    [
        'slug' => 'copyright',
        'eyebrow' => 'Copyright',
        'title' => 'Copyright and Content',
        'subtitle' => 'Respect StudyBuddy content and only upload profile images you have the right to use.',
        'body' => 'StudyBuddy content, visual design, learning copy, app names, and platform materials should not be copied or reused outside the platform unless permission is given. Users should only upload profile pictures that are theirs or that they are allowed to use.',
        'button_label' => 'Contact support',
        'button_url' => '/contact',
        'secondary_button_label' => 'Terms',
        'secondary_button_url' => '/terms',
        'sort_order' => 110,
        'sections' => [
            [
                'key' => 'uploads',
                'eyebrow' => 'Uploads',
                'title' => 'Use profile pictures responsibly.',
                'body' => 'Do not upload images that expose someone’s private information, impersonate someone else, or violate another person’s rights.',
            ],
        ],
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
        'sections' => [
            [
                'key' => 'request-steps',
                'eyebrow' => 'Steps',
                'title' => 'What to include in a deletion request',
                'body' => 'Send the account email, the type of data you want removed, and whether the request is for your own account or a learner account you are responsible for.',
                'settings' => ['bullets' => ['Account email', 'Profile picture or profile data', 'Learning records if applicable', 'Guardian details for child accounts']],
            ],
        ],
    ],
];

foreach ($pages as $pageData) {
    $pageId = page($pageData);

    foreach (($pageData['sections'] ?? []) as $index => $section) {
        $section['sort_order'] = $section['sort_order'] ?? (($index + 1) * 10);
        pageSection($pageId, $pageData['slug'], $section);
    }
}

/*
|--------------------------------------------------------------------------
| Homepage Content
|--------------------------------------------------------------------------
*/

$homepageSections = [
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
            ['key' => 'student-path', 'icon' => '🎒', 'title' => 'Student path', 'body' => 'Practise with mini apps, complete tasks, earn points, and build confidence one tiny win at a time.', 'button_label' => 'Start learning', 'button_url' => '/apps'],
            ['key' => 'parent-path', 'icon' => '🛡️', 'title' => 'Parent path', 'body' => 'Connect only with a learner’s consent code, then view progress signals and support learning calmly.', 'button_label' => 'See roles', 'button_url' => '/roles'],
            ['key' => 'teacher-path', 'icon' => '🏫', 'title' => 'Teacher path', 'body' => 'Create classes, add verified students, assign tasks, and review student activity from one dashboard.', 'button_label' => 'Teacher tools', 'button_url' => '/roles'],
        ],
    ],
    [
        'key' => 'apps-universe',
        'eyebrow' => 'Apps',
        'title' => 'Mini app worlds for focused practice.',
        'subtitle' => 'Every app is designed to be short, clear, and motivating.',
        'body' => 'Students can jump into maths, reading, spelling, focus, planning, quizzes, shapes, or flashcards without feeling overwhelmed.',
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
            ['key' => 'no-random-linking', 'icon' => '🔐', 'title' => 'Consent-first linking', 'body' => 'The learner shares a code only with a trusted parent or teacher.'],
            ['key' => 'regenerate-code', 'icon' => '🔄', 'title' => 'Fresh codes anytime', 'body' => 'Learners can regenerate their code if they want to stop old connections.'],
            ['key' => 'visible-controls', 'icon' => '👀', 'title' => 'Clear dashboards', 'body' => 'Connected accounts and activity are shown in the right role dashboard.'],
        ],
    ],
    [
        'key' => 'roles',
        'eyebrow' => 'Roles',
        'title' => 'Different dashboards for different people.',
        'subtitle' => 'Students, parents, teachers, and independent learners do not need the same controls.',
        'body' => 'StudyBuddy keeps each dashboard focused: learners practise, parents monitor, teachers assign, and independent learners plan their own path.',
        'image_path' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-teachers.png',
        'button_label' => 'Explore roles',
        'button_url' => '/roles',
        'sort_order' => 40,
        'items' => [
            ['key' => 'student-dashboard', 'icon' => '🎒', 'title' => 'Student dashboard', 'body' => 'Tasks, points, favourite apps, profile controls, and a safe Connect Code.'],
            ['key' => 'parent-dashboard', 'icon' => '🛡️', 'title' => 'Parent dashboard', 'body' => 'Child progress, activity signals, connection controls, and safety links.'],
            ['key' => 'teacher-dashboard', 'icon' => '🏫', 'title' => 'Teacher dashboard', 'body' => 'Classes, verified students, assignments, quizzes, and student activity.'],
            ['key' => 'independent-dashboard', 'icon' => '🚀', 'title' => 'Independent learner', 'body' => 'Self-paced goals, focus tools, app recommendations, and profile portfolio.'],
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
        'sort_order' => 50,
        'items' => [
            ['key' => 'profile-customisation', 'icon' => '🪄', 'title' => 'Profile Studio', 'body' => 'Users can customise colours, badges, profile pictures, and favourite apps.'],
            ['key' => 'positive-progress', 'icon' => '⭐', 'title' => 'Positive progress', 'body' => 'Community is focused on encouragement and learning identity.'],
            ['key' => 'privacy-controls', 'icon' => '🔒', 'title' => 'Privacy controls', 'body' => 'Users choose whether their profile is public and what details are shown.'],
        ],
    ],
    [
        'key' => 'testimonials',
        'eyebrow' => 'Reviews',
        'title' => 'Built for learners, families, and classrooms.',
        'subtitle' => 'Clear enough for young learners, useful enough for adults guiding them.',
        'body' => 'StudyBuddy content is written to be friendly, direct, and action-ready.',
        'button_label' => 'Start with apps',
        'button_url' => '/apps',
        'sort_order' => 60,
        'items' => [
            ['key' => 'student-review', 'icon' => '🎒', 'title' => '“I know what to do next.”', 'subtitle' => 'Student experience', 'body' => 'Apps, tasks, and points make practice feel less confusing and more rewarding.'],
            ['key' => 'parent-review', 'icon' => '🛡️', 'title' => '“I can support without taking over.”', 'subtitle' => 'Parent experience', 'body' => 'Progress snapshots and activity signals make support calmer and clearer.'],
            ['key' => 'teacher-review', 'icon' => '🏫', 'title' => '“My classroom tools are in one place.”', 'subtitle' => 'Teacher experience', 'body' => 'Classes, rosters, assignments, and app-based tasks stay connected.'],
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
        'sort_order' => 70,
        'items' => [
            ['key' => 'cta-apps', 'icon' => '🎮', 'title' => 'Open apps', 'body' => 'Start a short practice session.'],
            ['key' => 'cta-profile', 'icon' => '🪄', 'title' => 'Build profile', 'body' => 'Make your StudyBuddy space feel like yours.'],
            ['key' => 'cta-community', 'icon' => '🌍', 'title' => 'Visit community', 'body' => 'See optional public learner showcases.'],
        ],
    ],
];

foreach ($homepageSections as $section) {
    $sectionId = homepageSection($section);

    foreach (($section['items'] ?? []) as $i => $item) {
        $item['sort_order'] = $item['sort_order'] ?? (($i + 1) * 10);
        homepageItem($sectionId, $section['key'], $item);
    }
}

/*
|--------------------------------------------------------------------------
| App Universe Content
|--------------------------------------------------------------------------
*/

$apps = [
    [
        'slug' => 'math-quest',
        'name' => 'Math Quest',
        'category' => 'Maths Practice',
        'icon' => '➗',
        'tagline' => 'Short maths missions for speed, accuracy, and confidence.',
        'description' => 'Math Quest helps learners practise quick calculations, number sense, and problem-solving through small challenges that feel achievable.',
        'sort_order' => 10,
        'recommended_for' => ['student', 'teacher', 'independent_learner'],
    ],
    [
        'slug' => 'spelling-sprint',
        'name' => 'Spelling Sprint',
        'category' => 'Language Practice',
        'icon' => '✍️',
        'tagline' => 'Build spelling confidence with quick word challenges.',
        'description' => 'Spelling Sprint gives learners a playful way to practise words, patterns, and memory without turning revision into pressure.',
        'sort_order' => 20,
        'recommended_for' => ['student', 'teacher'],
    ],
    [
        'slug' => 'reading-garden',
        'name' => 'Reading Garden',
        'category' => 'Reading',
        'icon' => '📚',
        'tagline' => 'Gentle reading practice that grows with the learner.',
        'description' => 'Reading Garden supports reading habits with calm prompts, simple goals, and progress that feels encouraging.',
        'sort_order' => 30,
        'recommended_for' => ['student', 'parent', 'independent_learner'],
    ],
    [
        'slug' => 'focus-forest',
        'name' => 'Focus Forest',
        'category' => 'Focus',
        'icon' => '🌲',
        'tagline' => 'Turn focus time into a calm learning routine.',
        'description' => 'Focus Forest helps learners build consistency with short sessions, gentle structure, and a clear reason to return.',
        'sort_order' => 40,
        'recommended_for' => ['student', 'parent', 'independent_learner'],
    ],
    [
        'slug' => 'planner-city',
        'name' => 'Planner City',
        'category' => 'Planning',
        'icon' => '🏙️',
        'tagline' => 'Plan homework, revision, and tiny goals in one place.',
        'description' => 'Planner City helps learners break big goals into smaller steps so studying feels more manageable.',
        'sort_order' => 50,
        'recommended_for' => ['student', 'teacher', 'independent_learner'],
    ],
    [
        'slug' => 'quiz-galaxy',
        'name' => 'Quiz Galaxy',
        'category' => 'Quizzes',
        'icon' => '🌌',
        'tagline' => 'Review knowledge through quick, friendly quizzes.',
        'description' => 'Quiz Galaxy gives teachers and learners a simple way to review topics, spot gaps, and celebrate progress.',
        'sort_order' => 60,
        'recommended_for' => ['student', 'teacher', 'independent_learner'],
    ],
    [
        'slug' => 'shape-lab',
        'name' => 'Shape Lab',
        'category' => 'Visual Learning',
        'icon' => '🔷',
        'tagline' => 'Explore shapes, patterns, and visual problem-solving.',
        'description' => 'Shape Lab supports visual learners with pattern recognition, geometry ideas, and interactive practice.',
        'sort_order' => 70,
        'recommended_for' => ['student', 'teacher'],
    ],
    [
        'slug' => 'flashcard-forge',
        'name' => 'Flashcard Forge',
        'category' => 'Memory',
        'icon' => '🃏',
        'tagline' => 'Turn revision into quick recall practice.',
        'description' => 'Flashcard Forge helps learners practise recall through bite-sized cards that can support any subject.',
        'sort_order' => 80,
        'recommended_for' => ['student', 'teacher', 'independent_learner'],
    ],
];

foreach ($apps as $app) {
    appItem($app);
}

/*
|--------------------------------------------------------------------------
| Optional StudyBuddy Content Tables
|--------------------------------------------------------------------------
*/

if (Schema::hasTable('studybuddy_content_pages')) {
    foreach ($pages as $pageData) {
        upsertSafe('studybuddy_content_pages', ['slug' => $pageData['slug']], [
            'slug' => $pageData['slug'],
            'title' => $pageData['title'],
            'subtitle' => $pageData['subtitle'],
            'body' => $pageData['body'],
            'content' => $pageData['body'],
            'is_enabled' => true,
            'sort_order' => $pageData['sort_order'] ?? 10,
            'settings' => ['synced_from' => 'studybuddy_user_ready_content_cleanse'],
        ], $pageData['title']);
    }
}

if (Schema::hasTable('studybuddy_content_items')) {
    $items = [
        ['key' => 'safe-connect-code', 'title' => 'StudyBuddy Connect Code', 'body' => 'Learners control who connects to them. Parents and teachers need the learner’s current code before adding that account.'],
        ['key' => 'profile-studio', 'title' => 'Profile Studio', 'body' => 'Users can update their profile picture, colours, badges, public visibility, favourite apps, and learning goals.'],
        ['key' => 'teacher-assignments', 'title' => 'Teacher Assignments', 'body' => 'Teachers can create classes, add verified students, and assign tasks or quizzes from their dashboard.'],
        ['key' => 'parent-progress', 'title' => 'Parent Progress', 'body' => 'Parents can review connected child progress signals and recent learning activity after a verified connection.'],
    ];

    foreach ($items as $i => $item) {
        upsertSafe('studybuddy_content_items', ['item_key' => $item['key']], [
            'item_key' => $item['key'],
            'title' => $item['title'],
            'body' => $item['body'],
            'content' => $item['body'],
            'is_enabled' => true,
            'sort_order' => ($i + 1) * 10,
            'settings' => ['editable_from_admin' => true],
        ], $item['title']);
    }
}

echo "\nDONE: StudyBuddy website content is now user-ready and DB-managed.\n";
