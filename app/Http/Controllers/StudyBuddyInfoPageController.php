<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudyBuddyInfoPageController extends Controller
{
    public function show(string $slug): View
    {
        $page = $this->pageBySlug($slug);
        abort_unless($page, 404);

        return view('studybuddy.info.show', [
            'pageData' => $this->normalizePage($page, $slug),
            'sections' => $this->sectionsFor($page, $slug),
        ]);
    }

    public function roles(): View
    {
        $slug = 'roles';
        $page = $this->pageBySlug($slug);
        $sections = $this->sectionsFor($page, $slug);

        $roleCards = collect($sections)
            ->flatMap(fn ($section) => $section['settings']['roles'] ?? [])
            ->values();

        if ($roleCards->isEmpty()) {
            $roleCards = collect($this->defaultRoles());
        }

        return view('studybuddy.info.roles', [
            'pageData' => $this->normalizePage($page, $slug),
            'sections' => $sections,
            'roleCards' => $roleCards,
        ]);
    }

    private function pageBySlug(string $slug): ?object
    {
        if (Schema::hasTable('pages')) {
            $page = DB::table('pages')->where('slug', $slug)->first();
            if ($page) return $page;
        }

        $defaults = $this->defaultPages();

        if (!isset($defaults[$slug])) return null;

        return (object) array_merge(['id' => null, 'slug' => $slug], $defaults[$slug]);
    }

    private function normalizePage(?object $page, string $slug): array
    {
        $defaults = $this->defaultPages()[$slug] ?? [];

        return [
            'slug' => $slug,
            'eyebrow' => $page->eyebrow ?? $defaults['eyebrow'] ?? 'StudyBuddy',
            'title' => $page->title ?? $defaults['title'] ?? 'StudyBuddy',
            'subtitle' => $page->subtitle ?? $page->excerpt ?? $defaults['subtitle'] ?? '',
            'body' => $page->body ?? $page->content ?? $defaults['body'] ?? '',
            'settings' => $this->decode($page->settings ?? null),
        ];
    }

    private function sectionsFor(?object $page, string $slug): array
    {
        $rows = collect();

        if ($page && ($page->id ?? null) && Schema::hasTable('page_sections') && Schema::hasColumn('page_sections', 'page_id')) {
            $query = DB::table('page_sections')->where('page_id', $page->id);

            if (Schema::hasColumn('page_sections', 'is_enabled')) {
                $query->where('is_enabled', true);
            }

            $query->orderBy(Schema::hasColumn('page_sections', 'sort_order') ? 'sort_order' : 'id');

            $rows = $query->get();
        }

        if ($rows->isEmpty()) {
            return $this->defaultSections($slug);
        }

        return $rows->map(function ($row) {
            return [
                'key' => $row->section_key ?? $row->slug ?? $row->id ?? null,
                'eyebrow' => $row->eyebrow ?? null,
                'title' => $row->title ?? null,
                'subtitle' => $row->subtitle ?? null,
                'body' => $row->body ?? $row->content ?? null,
                'button_label' => $row->button_label ?? null,
                'button_url' => $row->button_url ?? null,
                'settings' => $this->decode($row->settings ?? null),
            ];
        })->values()->all();
    }

    private function decode($value): array
    {
        if (is_array($value)) return $value;

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function defaultPages(): array
    {
        return [
            'roles' => [
                'eyebrow' => 'Roles',
                'title' => 'How StudyBuddy roles work',
                'subtitle' => 'One platform, four different learning experiences.',
                'body' => 'Students, parents, teachers, and independent learners each get a dashboard made for the way they use StudyBuddy.',
            ],
            'about' => [
                'eyebrow' => 'About StudyBuddy',
                'title' => 'A playful learning universe for tiny wins.',
                'subtitle' => 'StudyBuddy helps learners practice, build confidence, and return to learning without pressure.',
                'body' => 'StudyBuddy connects learning apps, profiles, points, role-based dashboards, and safe community showcases into one friendly platform.',
            ],
            'contact' => [
                'eyebrow' => 'Contact',
                'title' => 'Talk to StudyBuddy.',
                'subtitle' => 'Questions, feedback, support, and partnership messages can start here.',
                'body' => 'Use this page to guide users toward help, safety support, parent questions, school enquiries, or platform feedback.',
            ],
            'privacy-policy' => [
                'eyebrow' => 'Privacy',
                'title' => 'Privacy Policy',
                'subtitle' => 'How StudyBuddy treats account, learning, and profile information.',
                'body' => 'StudyBuddy is designed to collect only what is needed for accounts, learning progress, safety, and profile features.',
            ],
            'terms' => [
                'eyebrow' => 'Terms',
                'title' => 'Terms of Use',
                'subtitle' => 'The rules for using StudyBuddy respectfully and safely.',
                'body' => 'By using StudyBuddy, users agree to keep the platform kind, safe, and learning-focused.',
            ],
            'disclaimer' => [
                'eyebrow' => 'Disclaimer',
                'title' => 'Learning Disclaimer',
                'subtitle' => 'StudyBuddy supports learning, but it does not replace teachers, parents, or professional advice.',
                'body' => 'StudyBuddy provides practice tools, educational content, and progress experiences for general learning support.',
            ],
            'cookies' => [
                'eyebrow' => 'Cookies',
                'title' => 'Cookie Notice',
                'subtitle' => 'How StudyBuddy uses cookies and session technology.',
                'body' => 'StudyBuddy may use cookies to keep users logged in, protect sessions, remember preferences, and improve platform experience.',
            ],
            'community-guidelines' => [
                'eyebrow' => 'Community',
                'title' => 'Community Guidelines',
                'subtitle' => 'A safe, kind, progress-focused space.',
                'body' => 'StudyBuddy community profiles are designed for positive showcases, not pressure, direct messaging, or unsafe interactions.',
            ],
            'copyright' => [
                'eyebrow' => 'Copyright',
                'title' => 'Copyright and Content',
                'subtitle' => 'How content, images, and platform materials should be respected.',
                'body' => 'StudyBuddy content, visuals, and learning materials should be used respectfully and only in ways allowed by the platform.',
            ],
            'data-deletion' => [
                'eyebrow' => 'Data',
                'title' => 'Data Deletion',
                'subtitle' => 'How users can request account or profile data deletion.',
                'body' => 'Users can request deletion of profile information, uploaded profile pictures, account details, and learning data where required.',
            ],
        ];
    }

    private function defaultSections(string $slug): array
    {
        if ($slug === 'roles') {
            return [[
                'key' => 'role-cards',
                'eyebrow' => 'Interactive roles',
                'title' => 'Choose how you use StudyBuddy',
                'subtitle' => 'Every role gets a focused experience.',
                'body' => 'Tap through each role to understand dashboards, safety, learning tools, and profile options.',
                'settings' => ['roles' => $this->defaultRoles()],
            ]];
        }

        return [
            [
                'key' => 'overview',
                'eyebrow' => 'Overview',
                'title' => 'What this page means',
                'subtitle' => null,
                'body' => 'This page is part of the StudyBuddy information centre and can be edited from the admin-managed database content.',
                'settings' => [],
            ],
            [
                'key' => 'choices',
                'eyebrow' => 'User choices',
                'title' => 'Clear, friendly, and user-first',
                'subtitle' => null,
                'body' => 'StudyBuddy aims to explain important information in simple language so learners, parents, and educators understand what is happening.',
                'settings' => [],
            ],
        ];
    }

    private function defaultRoles(): array
    {
        return [
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
    }
}
