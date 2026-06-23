<?php

namespace App\Http\Controllers;

use App\Models\FooterItem;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $role = $this->displayRole((string) $user->role);
        $profile = $this->roleProfile($role);
        $themeOptions = $this->themeOptions();
        $currentTheme = $this->validTheme((string) ($user->avatar_style ?: 'cosmic-dolphin'));

        return view('dashboard.index', [
            'settings' => SiteSetting::query()->pluck('value', 'key')->toArray(),
            'navigationItems' => NavigationItem::query()->where('is_enabled', true)->orderBy('sort_order')->get(),
            'footerGroups' => FooterItem::query()->where('is_enabled', true)->orderBy('group')->orderBy('sort_order')->get()->groupBy('group'),
            'user' => $user,
            'role' => $role,
            'roleLabel' => $profile['label'],
            'roleEyebrow' => $profile['eyebrow'],
            'dashboardIntro' => $profile['intro'],
            'heroImage' => $profile['hero_image'],
            'metrics' => $profile['metrics'],
            'missions' => $profile['missions'],
            'quickActions' => $profile['quick_actions'],
            'controlPanels' => $profile['control_panels'],
            'focusZones' => $profile['focus_zones'],
            'resourceShelf' => $profile['resource_shelf'],
            'themeOptions' => $themeOptions,
            'currentTheme' => $currentTheme,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $themeSlugs = array_column($this->themeOptions(), 'slug');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', Rule::in(['student', 'parent', 'teacher', 'independent_learner'])],
            'learning_stage' => ['nullable', 'string', 'max:120'],
            'avatar_style' => ['required', Rule::in($themeSlugs)],
        ]);

        $data['avatar_style'] = $this->validTheme($data['avatar_style']);

        $request->user()->update($data);

        return back()->with('status', 'Profile updated. Your theme now shapes the whole StudyBuddy experience.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Your current access key was not correct.']);
        }

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('status', 'Access key updated safely.');
    }

    private function displayRole(string $role): string
    {
        return match ($role) {
            'primary', 'secondary' => 'student',
            'parent' => 'parent',
            'teacher' => 'teacher',
            'independent_learner' => 'independent_learner',
            default => 'student',
        };
    }

    private function validTheme(string $theme): string
    {
        $theme = $theme ?: 'cosmic-dolphin';
        $allowed = array_column($this->themeOptions(), 'slug');

        return in_array($theme, $allowed, true) ? $theme : 'cosmic-dolphin';
    }

    private function themeOptions(): array
    {
        $base = 'assets/studybuddy-imgs/dashboard/themes/';

        return [
            [
                'slug' => 'cosmic-dolphin',
                'label' => 'Cosmic Dolphin',
                'description' => 'Classic StudyBuddy: blue, purple, sparkly, friendly, and focused.',
                'image' => $base.'cosmic-dolphin.svg',
            ],
            [
                'slug' => 'bts-purple-galaxy',
                'label' => 'BTS Purple Galaxy',
                'description' => 'Purple ARMY-inspired galaxy energy with soft stars and premium glow.',
                'image' => $base.'bts-purple-galaxy.svg',
            ],
            [
                'slug' => 'ocean-focus',
                'label' => 'Ocean Focus',
                'description' => 'Calm aqua learning mode for peaceful study sessions.',
                'image' => $base.'ocean-focus.svg',
            ],
            [
                'slug' => 'candy-pop',
                'label' => 'Candy Pop',
                'description' => 'Bright playful colors for fun, younger, high-energy learning.',
                'image' => $base.'candy-pop.svg',
            ],
            [
                'slug' => 'forest-calm',
                'label' => 'Forest Calm',
                'description' => 'Green calming theme for focus, routines, and quiet practice.',
                'image' => $base.'forest-calm.svg',
            ],
            [
                'slug' => 'night-study',
                'label' => 'Night Study',
                'description' => 'Deep midnight theme for cozy revision and focused dashboards.',
                'image' => $base.'night-study.svg',
            ],
            [
                'slug' => 'solar-gold',
                'label' => 'Solar Gold',
                'description' => 'Warm golden progress vibe for streaks, wins, and motivation.',
                'image' => $base.'solar-gold.svg',
            ],
            [
                'slug' => 'neon-gamer',
                'label' => 'Neon Gamer',
                'description' => 'Arcade-style high contrast theme for mini apps and challenges.',
                'image' => $base.'neon-gamer.svg',
            ],
        ];
    }

    private function roleProfile(string $role): array
    {
        $base = 'assets/studybuddy-imgs/';

        return match ($role) {
            'parent' => [
                'label' => 'Parent Command Center',
                'eyebrow' => 'Family guidance',
                'intro' => 'A calm parent space for routines, learner confidence, safety, and progress checks without overwhelming detail.',
                'hero_image' => $base.'dashboard/role-parent.svg',
                'metrics' => [
                    ['Weekly routine', '3h 20m', 'Calm practice time planned for this week.'],
                    ['Growth notes', '28', 'Small wins recorded across apps.'],
                    ['Confidence trend', '85%', 'A gentle signal that learning is moving well.'],
                ],
                'missions' => ['Review this week\'s calm routine', 'Choose one mini app to support at home', 'Open privacy and data controls'],
                'quick_actions' => [['Family Guide', '/for-parents'], ['Safety & Support', '/support'], ['Privacy Policy', '/privacy-policy'], ['Data Deletion', '/data-deletion']],
                'control_panels' => [
                    ['Routine Builder', 'Create simple home practice rhythms for school nights, weekends, or revision weeks.', $base.'homepage-paths/path-parents.png', '/for-parents', 'Open family guide'],
                    ['Progress Glance', 'View strengths, practice consistency, and confidence without turning learning into pressure.', $base.'apps/app-reading-garden.png', '/dashboard', 'Review progress'],
                    ['Safety Center', 'Access privacy, support, contact, and data deletion pages from one clear place.', $base.'homepage-paths/path-support.png', '/support', 'Open support'],
                ],
                'focus_zones' => ['Home routine', 'Confidence signals', 'Safe account choices', 'Reading growth'],
                'resource_shelf' => [['Parent page', '/for-parents'], ['Contact us', '/contact-us'], ['Support', '/support']],
            ],
            'teacher' => [
                'label' => 'Teacher Studio',
                'eyebrow' => 'Classroom ready',
                'intro' => 'A clearer teaching dashboard for lesson energy, class-friendly activities, assignments, and quick learning resources.',
                'hero_image' => $base.'dashboard/role-teacher.svg',
                'metrics' => [
                    ['Class groups', '5', 'Learning groups ready to organize.'],
                    ['Learners', '120', 'Students supported across classroom paths.'],
                    ['Tasks ready', '12', 'Practice activities prepared for this week.'],
                ],
                'missions' => ['Plan one focused practice block', 'Pick a mini app for today\'s class', 'Save a support page for families'],
                'quick_actions' => [['Teacher Guide', '/for-teachers'], ['Apps Library', '/apps'], ['Contact', '/contact-us'], ['Support', '/support']],
                'control_panels' => [
                    ['Lesson Launcher', 'Choose a mini app and turn it into a short classroom-friendly learning moment.', $base.'homepage-paths/path-teachers.png', '/for-teachers', 'Open teacher guide'],
                    ['Assignment Board', 'Shape simple practice tasks learners can finish with confidence.', $base.'apps/app-planner-city.png', '/apps', 'Browse apps'],
                    ['Resource Shelf', 'Keep classroom pages, support, privacy, and contact links easy to share.', $base.'apps/app-quiz-galaxy.png', '/support', 'Open resources'],
                ],
                'focus_zones' => ['Lesson flow', 'Practice tasks', 'Classroom clarity', 'Shareable resources'],
                'resource_shelf' => [['Teacher page', '/for-teachers'], ['Apps', '/apps'], ['Privacy', '/privacy-policy']],
            ],
            'independent_learner' => [
                'label' => 'Product Workspace',
                'eyebrow' => 'Independent Learner view',
                'intro' => 'A product-focused dashboard for exploring StudyBuddy pages, learning flows, trust content, and support information.',
                'hero_image' => $base.'brand/logo-icon.png',
                'metrics' => [
                    ['Product paths', '8', 'Core public pages available to review.'],
                    ['Saved resources', '16', 'Useful pages and references ready.'],
                    ['Readiness', '92%', 'Brand and learning story aligned.'],
                ],
                'missions' => ['Review the product story', 'Check privacy and data pages', 'Explore mini app positioning'],
                'quick_actions' => [['About StudyBuddy', '/about-us'], ['Apps Library', '/apps'], ['Support', '/support'], ['Contact', '/contact-us']],
                'control_panels' => [
                    ['Product Story', 'Understand the homepage, apps, family, teacher, and support journey as one product ecosystem.', $base.'homepage-paths/path-apps.png', '/about-us', 'Review story'],
                    ['Trust Review', 'Open privacy, data deletion, and support pages for operational clarity.', $base.'homepage-paths/path-support.png', '/privacy-policy', 'Review trust pages'],
                    ['Growth Map', 'Use mini apps and page insights to plan what gets improved next.', $base.'decor/planets/planet-purple-lg.png', '/apps', 'Explore apps'],
                ],
                'focus_zones' => ['Product story', 'Trust pages', 'Support flow', 'Growth roadmap'],
                'resource_shelf' => [['About us', '/about-us'], ['Data deletion', '/data-deletion'], ['Contact', '/contact-us']],
            ],
            default => [
                'label' => 'Student Learning Space',
                'eyebrow' => 'Learner dashboard',
                'intro' => 'A friendly student space for tiny wins, focus, practice, rewards, and choosing what to learn next.',
                'hero_image' => $base.'dashboard/role-student.svg',
                'metrics' => [
                    ['Level', '12', 'Star learner progress unlocked.'],
                    ['Buddy coins', '320', 'Collected from practice and focus wins.'],
                    ['Study streak', '7 days', 'Keep the learning sparkle alive.'],
                ],
                'missions' => ['Start one Math Quest challenge', 'Read a Reading Garden story', 'Try one calm Focus Forest session'],
                'quick_actions' => [['Start Apps', '/apps'], ['Get Help', '/support'], ['Contact Team', '/contact-us'], ['About StudyBuddy', '/about-us']],
                'control_panels' => [
                    ['Start Practice', 'Jump into mini apps made for quick confidence-building wins.', $base.'apps/app-math-quest.png', '/apps', 'Explore apps'],
                    ['Focus Mode', 'Use calm routines before tricky tasks so learning feels lighter.', $base.'apps/app-focus-forest.png', '/apps', 'Find focus tools'],
                    ['Rewards Path', 'Track your progress, celebrate streaks, and keep learning playful.', $base.'apps/app-flashcard-castle.png', '/dashboard', 'View progress'],
                ],
                'focus_zones' => ['Math practice', 'Reading growth', 'Calm focus', 'Quiz confidence'],
                'resource_shelf' => [['Apps', '/apps'], ['Support', '/support'], ['Privacy', '/privacy-policy']],
            ],
        };
    }
}
