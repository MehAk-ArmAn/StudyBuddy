<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyLaunchChecklistItem;
use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\StudyBuddyPlatformSetting;
use App\Models\StudyBuddyPointTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                        ])
                    );

                    return str_contains(
                        $haystack,
                        Str::lower($search)
                    );
                }
            )
            ->values();

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
                'search'
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

        $canEarnPoints =
            ! $adminPreview
            &&
            Auth::check()
            && $canLaunch;

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

        $total = (int) StudyBuddyPointTransaction::query()
            ->where('user_id', $user?->id)
            ->sum('points');

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

        return view(
            'studybuddy.final.points-wallet',
            compact(
                'transactions',
                'total',
                'earned',
                'spent',
                'settings'
            )
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

    public function completeSession(
        Request $request
    ) {
        $data = $request->validate([
            'app_slug' => [
                'required',
                'string',
                'max:120',
            ],
        ]);

        $app = StudyBuddyMiniAppPlatform::query()
            ->active()
            ->where('slug', $data['app_slug'])
            ->firstOrFail();

        if (! $app->hasPublishedWebApp()) {
            throw ValidationException::withMessages([
                'app_slug' =>
                    'This web app is not published yet.',
            ]);
        }

        $alreadyEarnedToday =
            StudyBuddyPointTransaction::query()
                ->where(
                    'user_id',
                    $request->user()->id
                )
                ->where(
                    'source_type',
                    'mini_app_session'
                )
                ->where(
                    'source_slug',
                    $app->slug
                )
                ->whereDate(
                    'created_at',
                    today()
                )
                ->exists();

        if ($alreadyEarnedToday) {
            $message =
                'You already saved today’s points for this app.';

            return $request->expectsJson()
                ? response()->json(
                    [
                        'ok' => false,
                        'message' => $message,
                    ],
                    429
                )
                : back()->with(
                    'status',
                    $message
                );
        }

        $points = max(
            0,
            min(
                (int) $app->points_reward,
                500
            )
        );

        $transaction =
            StudyBuddyPointTransaction::create([
                'user_id' => $request->user()->id,
                'source_type' => 'mini_app_session',
                'source_slug' => $app->slug,
                'title' =>
                    'Completed '.$app->name.' session',
                'points' => $points,
                'status' => 'earned',
                'meta' => [
                    'app_name' => $app->name,
                    'completed_from' =>
                        'studybuddy_web_launcher',
                    'session_key' =>
                        Str::uuid()->toString(),
                ],
            ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'points' => $points,
                'transaction_id' => $transaction->id,
                'message' =>
                    "{$points} points added to your StudyBuddy wallet.",
            ]);
        }

        return back()->with(
            'status',
            "{$points} points added to your StudyBuddy wallet."
        );
    }
}
