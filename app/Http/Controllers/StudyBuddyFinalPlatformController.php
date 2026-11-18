<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyLaunchChecklistItem;
use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\StudyBuddyPlatformSetting;
use App\Models\StudyBuddyPointTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Support\StudyBuddyRewardIntegrity;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudyBuddyFinalPlatformController extends Controller
{
    public function apps(Request $request): View
    {
        $settings = StudyBuddyPlatformSetting::publicMap();

        $role = trim(
            (string) $request->query('role', '')
        ) ?: null;

        $category = trim(
            (string) $request->query('category', '')
        ) ?: null;

        $search = trim(
            (string) $request->query('q', '')
        );

        $validRoles = [
            'student',
            'parent',
            'teacher',
            'independent_learner',
        ];

        if (
            $role
            && ! in_array(
                $role,
                $validRoles,
                true
            )
        ) {
            $role = null;
        }

        $allApps = StudyBuddyMiniAppPlatform::query()
            ->active()
            ->with('subjectAssignment')
            ->ordered()
            ->get();

        $categories = $allApps
            ->pluck('category')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $apps = $allApps
            ->filter(
                fn (StudyBuddyMiniAppPlatform $app): bool =>
                    ! $role
                    || $app->visibleForRole($role)
            )
            ->filter(
                fn (StudyBuddyMiniAppPlatform $app): bool =>
                    ! $category
                    || strcasecmp(
                        (string) $app->category,
                        $category
                    ) === 0
            )
            ->filter(
                function (
                    StudyBuddyMiniAppPlatform $app
                ) use ($search): bool {
                    if ($search === '') {
                        return true;
                    }

                    $haystack = Str::lower(
                        implode(' ', [
                            $app->name,
                            $app->category,
                            $app->tagline,
                            $app->description,
                            $app->preview_text,
                            $app->subjectAssignment?->topics,
                            $app->subjectAssignment?->learning_outcome,
                            config('studybuddy_w34_subjects.subjects.'.($app->subjectAssignment?->primary_subject ?? '').'.label', ''),
                        ])
                    );

                    return str_contains(
                        $haystack,
                        Str::lower($search)
                    );
                }
            )
            ->values();

        // Only academic subjects with actual published apps visible in this catalogue.
        // Reuse W34's canonical 28-subject taxonomy and assignment relation.
        $subjectCounts = $apps
            ->filter(fn (StudyBuddyMiniAppPlatform $app): bool =>
                $app->subjectAssignment !== null
                && ! str_starts_with((string) $app->slug, 'demo-next-'))
            ->countBy(fn (StudyBuddyMiniAppPlatform $app) => $app->subjectAssignment->primary_subject);
        $subjectLinks = collect(config('studybuddy_w34_subjects.subjects', []))
            ->filter(fn ($item, $slug) => ($subjectCounts[$slug] ?? 0) > 0)
            ->map(fn ($item, $slug) => [
                'slug' => $slug,
                'label' => $item['label'],
                'count' => $subjectCounts[$slug],
            ])->values();

        $roles = [
            'student' => 'Learners',
            'parent' => 'Parents',
            'teacher' => 'Teachers',
            'independent_learner' => 'Independent',
        ];

        return view(
            'studybuddy.final.apps',
            compact(
                'settings',
                'apps',
                'categories',
                'roles',
                'role',
                'category',
                'search',
                'subjectLinks'
            )
        );
    }

    public function appDetail(
        Request $request,
        string $slug
    ): View {
        $app = StudyBuddyMiniAppPlatform::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->renderAppDetail($app);
    }

    /** Admin-only preview route: drafts stay private while they are reviewed. */
    public function adminAppPreview(StudyBuddyMiniAppPlatform $app): View
    {
        return $this->renderAppDetail($app, true);
    }

    private function renderAppDetail(
        StudyBuddyMiniAppPlatform $app,
        bool $adminPreview = false
    ): View {
        $settings = StudyBuddyPlatformSetting::publicMap();

        $related = StudyBuddyMiniAppPlatform::query()
            ->active()
            ->where('id', '!=', $app->id)
            ->where('category', $app->category)
            ->ordered()
            ->take(3)
            ->get();

        return view(
            'studybuddy.final.app-detail',
            compact(
                'settings',
                'app',
                'related',
                'adminPreview'
            )
        );
    }

    public function appLaunchpadRedirect(): RedirectResponse
    {
        return redirect()->route(
            'studybuddy.apps',
            status: 301
        );
    }

    public function webPlay(
        Request $request,
        string $slug
    ): View {
        $app = StudyBuddyMiniAppPlatform::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->renderWebPlay($app);
    }

    /** Admin-only browser test for an app that has not been published yet. */
    public function adminWebPlayPreview(StudyBuddyMiniAppPlatform $app): View
    {
        return $this->renderWebPlay($app, true);
    }

    private function renderWebPlay(
        StudyBuddyMiniAppPlatform $app,
        bool $adminPreview = false
    ): View {

        $settings = StudyBuddyPlatformSetting::publicMap();

        $canLaunch = $app->hasPublishedWebApp();

        // A browser iframe or "Save points" click is not verifiable completion.
        // Only trusted server-graded game adapters may mint new points.
        $canEarnPoints = false;

        $embedUrl = null;
        $externalUrl = null;

        if ($canLaunch) {
            if ($app->usesExternalBrowserUrl()) {
                // Never frame a site we do not host: its own security headers
                // may forbid framing, which would leave the learner staring at
                // a blank box. Send them there directly instead.
                $externalUrl = $app->web_play_url;
            } else {
                $embedUrl = $adminPreview
                    ? route('admin.control-room.apps.preview.asset', [
                        'app' => $app,
                        'path' => 'index.html',
                    ])
                    : route('studybuddy.web-app.asset', [
                        'slug' => $app->slug,
                        'path' => 'index.html',
                    ]);
            }
        }

        $detailUrl = $adminPreview
            ? route('admin.control-room.apps.preview', $app)
            : route('studybuddy.apps.show', $app->slug);

        return view(
            'studybuddy.final.web-play',
            compact(
                'app',
                'settings',
                'canLaunch',
                'canEarnPoints',
                'embedUrl',
                'externalUrl',
                'adminPreview',
                'detailUrl'
            )
        );
    }

    public function pointsWallet(
        Request $request
    ): View {
        $user = $request->user();

        $transactions = StudyBuddyPointTransaction::query()
            ->where('user_id', $user?->id)
            ->latest()
            ->take(50)
            ->get();

        // The spendable balance is the canonical users.cosmic_points field.
        // Legacy journal records may not include signup credits or old cosmetics.
        $total = (int) ($user?->cosmic_points ?? 0);

        $earned = (int) StudyBuddyPointTransaction::query()
            ->where('user_id', $user?->id)
            ->where('points', '>', 0)
            ->sum('points');

        $spent = abs(
            (int) StudyBuddyPointTransaction::query()
                ->where('user_id', $user?->id)
                ->where('points', '<', 0)
                ->sum('points')
        );

        $settings = StudyBuddyPlatformSetting::publicMap();
        $earnedBadges = StudyBuddyRewardIntegrity::installed()
            ? DB::table('studybuddy_user_reward_badges')->where('user_id',$user->id)
                ->orderBy('earned_at')->get()
            : collect();
        $badgeNames = StudyBuddyRewardIntegrity::badgeNames();
        $hiddenBadgeCount = max(0,count($badgeNames)-$earnedBadges->count());

        return view(
            'studybuddy.final.points-wallet',
            compact('transactions','total','earned','spent','settings','earnedBadges','badgeNames','hiddenBadgeCount')
        );
    }

    public function platformRoadmap(): View
    {
        $settings = StudyBuddyPlatformSetting::publicMap();

        $apps = StudyBuddyMiniAppPlatform::query()
            ->active()
            ->ordered()
            ->get();

        $checks = StudyBuddyLaunchChecklistItem::query()
            ->orderBy('sort_order')
            ->get();

        return view(
            'studybuddy.final.platform-roadmap',
            compact(
                'settings',
                'apps',
                'checks'
            )
        );
    }

    public function launchReadiness(): View
    {
        $settings = StudyBuddyPlatformSetting::publicMap();

        $checks = StudyBuddyLaunchChecklistItem::query()
            ->orderBy('sort_order')
            ->get();

        $total = max(
            $checks->count(),
            1
        );

        $done = $checks
            ->where('status', 'done')
            ->count();

        $score = (int) round(
            ($done / $total) * 100
        );

        return view(
            'studybuddy.final.launch-readiness',
            compact(
                'settings',
                'checks',
                'score',
                'done',
                'total'
            )
        );
    }

    public function completeSession(Request $request)
    {
        // Fail closed: the old app-launcher Save button awarded unverified points.
        // Do not revive it; connect each game to an approved server-side result.
        $message = 'Points now require a verified game result. Complete a Math Sprint to earn.';
        return $request->expectsJson()
            ? response()->json(['ok'=>false,'message'=>$message], 409)
            : back()->withErrors(['session'=>$message]);
    }
}
