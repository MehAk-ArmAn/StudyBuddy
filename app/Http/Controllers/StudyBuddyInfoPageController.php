<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

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
            'subtitle' => $page->hero_subtitle ?? $page->subtitle ?? $page->excerpt ?? $defaults['subtitle'] ?? '',
            'body' => $page->hero_body ?? $page->body ?? $page->content ?? $defaults['body'] ?? '',
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

        // A CMS section is a heading; the paragraphs a visitor reads live in
        // its items. Rendering only the sections left legal pages showing a
        // title with nothing underneath it.
        $cards = $this->itemsForSections($rows->pluck('id'));

        if ($cards !== []) {
            return $cards;
        }

        $mapped = $rows->map(function ($row) {
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
        })->filter(fn (array $s): bool => filled($s['body']) || filled($s['subtitle']))->values()->all();

        return $mapped !== [] ? $mapped : $this->defaultSections($slug);
    }

    /**
     * Turn the items belonging to a page's sections into readable cards.
     *
     * @param  \Illuminate\Support\Collection<int, int>  $sectionIds
     * @return array<int, array<string, mixed>>
     */
    private function itemsForSections($sectionIds): array
    {
        if ($sectionIds->isEmpty() || ! Schema::hasTable('page_section_items')) {
            return [];
        }

        try {
            $items = DB::table('page_section_items')
                ->whereIn('page_section_id', $sectionIds)
                ->where('is_enabled', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        } catch (Throwable $e) {
            return [];
        }

        return $items
            ->filter(fn ($item): bool => filled($item->title) && (filled($item->body) || filled($item->subtitle)))
            ->map(fn ($item): array => [
                'key' => $item->item_key ?? $item->id,
                'eyebrow' => null,
                'title' => $item->title,
                'subtitle' => null,
                'body' => $item->body ?: $item->subtitle,
                'button_label' => $item->button_label ?? null,
                'button_url' => $item->button_url ?? null,
                'settings' => [],
            ])
            ->values()
            ->all();
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

        // Real content for the pages people actually read before trusting us
        // with an account. Plain language, describing what StudyBuddy does.
        $written = [
            'about' => [
                ['Small games, clear purpose', 'Each StudyBuddy app focuses on a learning skill and keeps sessions short enough to finish.'],
                ['Built around real learners', 'Parents, teachers, and independent learners get guidance that matches the way they support learning.'],
                ['Calm by design', 'Friendly visuals and plain language keep the experience playful without becoming noisy.'],
            ],
            'community-guidelines' => [
                ['Keep it kind', 'StudyBuddy is for learners of different ages and backgrounds. Be respectful, encouraging, and patient with one another.'],
                ['Protect personal information', 'Do not share passwords, home addresses, private contact details, or another person’s information.'],
                ['Keep it learning-focused', 'Use StudyBuddy spaces and messages for learning, account support, and constructive feedback.'],
                ['Speak up', 'If something feels unsafe or inappropriate, stop and tell a trusted adult or contact the StudyBuddy support team.'],
            ],
            'copyright' => [
                ['StudyBuddy materials', 'StudyBuddy names, illustrations, learning activities, and app content belong to their respective owners and may not be copied or resold without permission.'],
                ['Your own work', 'You keep ownership of original work you create. Only share material that you made or have permission to use.'],
                ['Reporting a concern', 'If you believe something on StudyBuddy uses your work without permission, contact us with the page address and enough detail for us to review it.'],
            ],
            'privacy-policy' => [
                ['What we collect', 'We store the account and profile details you choose to provide, including your display and real name, email, sign-in credentials, role, date of birth, country, guardian or child email connections, learning stage, profile choices and uploads. Role-specific details can include learning goals, subjects, age or class level, organization details, teaching focus and preferred study time.'],
                ['Learning and account activity', 'We store learning progress, points, quests, account connections, and any class or assignment activity you use so StudyBuddy can show the right information to you.'],
                ['Verification information', 'A verification request may include the submitted name and country, verification method or reference, confirmations and consent, notes, status, and review history.'],
                ['Support, updates and device records', 'Contact messages store the name, email, role, topic and message you submit. If you join the updates list, we store your email, subscription status and dates, a one-way code derived from your internet protocol (IP) address, and browser or device information. Site sessions and support requests can also record your IP address, browser or device information and recent activity to keep you signed in, protect forms and investigate safety concerns.'],
                ['Why we collect it', 'We use this information to run accounts and learning features, show your own progress, support the role you choose, answer messages, review verification requests, and keep younger users and the service safe.'],
                ['What we never do', 'We do not sell your information, and we do not use it to advertise to you or to your child.'],
                ['Deleting it', 'You can ask us to remove your account and everything attached to it whenever you like. The Data Deletion page walks through how.'],
                ['Still unsure?', 'Ask us through the contact page and we will explain it properly, in normal words.'],
            ],
            'data-deletion' => [
                ['1. Send the request', 'Message us from the contact page using the email address on the account you want removed.'],
                ['2. Tell us how far to go', 'Say whether you want the whole account gone, only your messages to us, or only your learning history.'],
                ['3. We confirm it is done', 'We check the request came from the account holder, delete what you asked for, and write back to confirm.'],
            ],
            'terms' => [
                ['Be decent', 'StudyBuddy is used by children. Keep what you write and share suitable for them.'],
                ['Your account is yours', 'Do not share your password, and do not sign in as somebody else.'],
                ['Our apps stay ours', 'You are welcome to play the games. Please do not copy, resell, or repackage them.'],
                ['We may change things', 'Apps get added, updated, and occasionally retired. We will not remove something you rely on without notice where we can help it.'],
            ],
            'cookies' => [
                ['What we use', 'A session cookie that keeps you signed in, and a security token that stops other sites submitting forms as you.'],
                ['What we do not use', 'No advertising cookies, and no third-party trackers following you around the internet.'],
                ['Turning them off', 'You can clear or block cookies in your browser. Signing in will stop working, because that is the part the cookie does.'],
            ],
            'disclaimer' => [
                ['What StudyBuddy is', 'Practice games that help skills stick through repetition and play.'],
                ['What it is not', 'A replacement for a teacher, a tutor, a diagnosis, or professional advice.'],
                ['If something looks wrong', 'Learning content can contain mistakes. Tell us through the contact page and we will fix it.'],
            ],
        ];

        if (isset($written[$slug])) {
            return array_map(
                static fn (array $entry): array => [
                    'key' => \Illuminate\Support\Str::slug($entry[0]),
                    'eyebrow' => null,
                    'title' => $entry[0],
                    'subtitle' => null,
                    'body' => $entry[1],
                    'settings' => [],
                ],
                $written[$slug]
            );
        }

        return [
            [
                'key' => 'overview',
                'eyebrow' => null,
                'title' => 'Here when you need it',
                'subtitle' => null,
                'body' => 'Find clear StudyBuddy guidance here, or contact our support team if your question is not covered.',
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
