<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudyBuddyShellAdminController extends Controller
{
    public function index()
    {
        $this->ensureAdmin();

        $settings = $this->settings();

        return view('studybuddy.admin.shell.index', [
            'settings' => $settings,
            'defaults' => $this->defaultJson(),
        ]);
    }

    public function update(Request $request)
    {
        $this->ensureAdmin();

        $fields = [
            'site_name',
            'logo_text',
            'site_tagline',
            'logo_image',
            'brand_promise',
            'footer_text',
            'footer_pill_one',
            'footer_pill_two',
            'footer_pill_three',
            'contact_email',
            'support_email',
            'creator_name',
            'creator_url',
            'shell_navigation_json',
            'shell_footer_groups_json',
            'shell_social_links_json',
        ];

        foreach ($fields as $field) {
            $value = $request->input($field);
            if (str_ends_with($field, '_json')) {
                $value = $this->prettyJson($value);
            }
            $this->putSetting($field, $value);
        }

        return redirect()
            ->route('studybuddy.admin.shell.index')
            ->with('status', 'Navbar and footer settings saved.');
    }

    private function ensureAdmin(): void
    {
        $user = auth()->user();

        $isAdmin = $user && (
            ($user->is_admin ?? false)
            || ($user->role ?? null) === 'admin'
            || ($user->email ?? null) === 'admin@studybuddy.fun'
        );

        abort_unless($isAdmin, 403);
    }

    private function settings(): array
    {
        if (!Schema::hasTable('site_settings')) {
            return [];
        }

        return DB::table('site_settings')->pluck('value', 'key')->toArray();
    }

    private function putSetting(string $key, mixed $value): void
    {
        if (!Schema::hasTable('site_settings')) {
            return;
        }

        $row = [
            'key' => $key,
            'value' => is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : (string) ($value ?? ''),
        ];

        $extra = [];
        if (Schema::hasColumn('site_settings', 'updated_at')) {
            $extra['updated_at'] = now();
        }
        if (Schema::hasColumn('site_settings', 'created_at')) {
            $extra['created_at'] = now();
        }

        DB::table('site_settings')->updateOrInsert(['key' => $key], array_merge($row, $extra));
    }

    private function prettyJson(?string $json): string
    {
        $json = trim((string) $json);
        if ($json === '') {
            return '';
        }

        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $json;
        }

        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function defaultJson(): array
    {
        return [
            'navigation' => json_encode([
                ['label' => 'Apps', 'url' => '/apps'],
                ['label' => 'Learning', 'url' => '/learning-hub'],
                ['label' => 'Parents', 'url' => '/parents-center'],
                ['label' => 'Teachers', 'url' => '/teacher-studio'],
                ['label' => 'Safety', 'url' => '/safety-support'],
                ['label' => 'Rewards', 'url' => '/rewards'],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'footer' => json_encode([
                'Explore' => [
                    ['label' => 'Apps', 'url' => '/apps'],
                    ['label' => 'Learning Hub', 'url' => '/learning-hub'],
                    ['label' => 'Quests', 'url' => '/my-quest'],
                    ['label' => 'Points Wallet', 'url' => '/points-wallet'],
                ],
                'Roles' => [
                    ['label' => 'Students', 'url' => '/apps?role=student'],
                    ['label' => 'Parents', 'url' => '/parents-center'],
                    ['label' => 'Teachers', 'url' => '/teacher-studio'],
                    ['label' => 'Independent Learners', 'url' => '/apps?role=independent_learner'],
                ],
                'Learning Worlds' => [
                    ['label' => 'Math Quest', 'url' => '/apps/math-quest'],
                    ['label' => 'Reading Garden', 'url' => '/apps/reading-garden'],
                    ['label' => 'Focus Forest', 'url' => '/apps/focus-forest'],
                    ['label' => 'Quiz Galaxy', 'url' => '/apps/quiz-galaxy'],
                ],
                'Community' => [
                    ['label' => 'About', 'url' => '/about'],
                    ['label' => 'Contact', 'url' => '/contact'],
                    ['label' => 'Privacy Policy', 'url' => '/privacy-policy'],
                    ['label' => 'Terms of Use', 'url' => '/terms'],
                ],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'socials' => json_encode([
                ['label' => 'Instagram', 'url' => ''],
                ['label' => 'YouTube', 'url' => ''],
                ['label' => 'LinkedIn', 'url' => ''],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ];
    }
}
