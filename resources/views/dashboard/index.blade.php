@extends('layouts.app')

@section('title', 'My StudyBuddy Dashboard')

@section('content')
@php
    use Illuminate\Support\Facades\Route;

    $role = method_exists($user, 'normalizedRole') ? $user->normalizedRole() : ($user->role ?? 'student');

    $dashboardPhotoUrl = null;
    if (!empty($user->profile_photo_path)) {
        $dashboardPhotoUrl = preg_match('/^https?:\/\//i', $user->profile_photo_path)
            ? $user->profile_photo_path
            : asset('storage/'.ltrim($user->profile_photo_path, '/'));
    }
    $displayRole = ucwords(str_replace('_', ' ', $role));

    $assetUrl = function ($path) {
        if (!$path) return asset('assets/studybuddy-imgs/brand/logo-icon.png');
        if (preg_match('/^https?:\/\//i', $path)) return $path;
        $clean = ltrim($path, '/');
        return file_exists(public_path($clean)) ? asset($clean) : asset('assets/studybuddy-imgs/brand/logo-icon.png');
    };

    $profileUrl = route('profile');
    $publicProfileUrl = route('studybuddy.profile.public', $user->id);
    $communityUrl = route('studybuddy.community');
    $pointsUrl = Route::has('studybuddy.final.points-wallet') ? route('studybuddy.final.points-wallet') : url('/points-wallet');
    $appsUrl = Route::has('studybuddy.apps') ? route('studybuddy.apps') : url('/apps');
    $questsUrl = Route::has('studybuddy.quests.index') ? route('studybuddy.quests.index') : $appsUrl;
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-dashboard-system.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-dashboard-system.css')) ? filemtime(public_path('assets/css/studybuddy-dashboard-system.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-dashboard-system.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-dashboard-system.js')) ? filemtime(public_path('assets/js/studybuddy-dashboard-system.js')) : time() }}" defer></script>

<main id="main-content" class="sb-user-hub">
    <section class="sb-hub-hero">
        <div>
            <p class="sb-hub-kicker">Your StudyBuddy Space</p>
            <h1>{{ $settings['dashboard_heading'] ?? 'Welcome back, '.$user->name.'.' }}</h1>
            <p>{{ $settings['dashboard_intro'] ?? 'Control your profile, apps, quests, points, and learning preferences from one clean dashboard.' }}</p>

            <div class="sb-hub-actions">
                <a href="{{ $appsUrl }}">Explore apps</a>
                <a class="soft" href="{{ $profileUrl }}">Edit profile</a>
                <a class="soft" href="{{ $communityUrl }}">Community profiles</a>
            </div>
        </div>

        <aside class="sb-profile-passport" data-hub-card>
            <div class="avatar-ring">
                @if($dashboardPhotoUrl)
                    <img src="{{ $dashboardPhotoUrl }}" alt="{{ $user->name }} profile picture">
                @else
                    <span>{{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}</span>
                @endif
            </div>
            <strong>{{ $user->name }}</strong>
            <p>{{ $profile['headline'] ?? $displayRole.' learning dashboard' }}</p>

            <div class="passport-stats">
                <article><span>Points</span><b>{{ number_format((int) ($user->cosmic_points ?? 0)) }}</b></article>
                <article><span>Rank</span><b>{{ $rank ? '#'.$rank : 'New' }}</b></article>
                <article><span>Profile</span><b>{{ $completion }}%</b></article>
            </div>

            <div class="profile-progress"><i style="width: {{ $completion }}%"></i></div>
        </aside>
    </section>

    <section class="sb-hub-grid">
        <article class="sb-hub-card wide" data-hub-card>
            <div class="card-head">
                <div>
                    <p class="sb-hub-kicker">Continue learning</p>
                    <h2>Your app worlds</h2>
                </div>
                <a href="{{ $appsUrl }}">View all</a>
            </div>

            <div class="mini-app-grid">
                @forelse($recommendedApps as $app)
                    @php($image = $assetUrl($app->hero_image ?? $app->image_path ?? null))
                    <a href="{{ url('/apps/'.$app->slug) }}" class="mini-app-card">
                        <img src="{{ $image }}" alt="{{ $app->name }} artwork">
                        <span>{{ $app->icon ?? '✨' }}</span>
                        <strong>{{ $app->name }}</strong>
                        <small>{{ $app->tagline ?? $app->category ?? 'Learning world' }}</small>
                    </a>
                @empty
                    <div class="empty-panel">
                        <strong>No apps yet</strong>
                        <p>Your app universe will appear here once apps are added.</p>
                    </div>
                @endforelse
            </div>
        </article>

        <article class="sb-hub-card" data-hub-card>
            <div class="card-head">
                <div>
                    <p class="sb-hub-kicker">Profile control</p>
                    <h2>Public profile</h2>
                </div>
            </div>

            <p>Your profile can showcase your learning style, favourite apps, goals, and points.</p>

            <div class="status-row">
                <span>{{ ($profile['public_profile_enabled'] ?? false) ? 'Public profile is ON' : 'Public profile is private' }}</span>
                <b>{{ ($profile['show_points'] ?? false) ? 'Points visible' : 'Points hidden' }}</b>
            </div>

            <div class="stack-actions">
                <a href="{{ $profileUrl }}">Edit my profile</a>
                <a class="soft" href="{{ $publicProfileUrl }}">Preview profile</a>
            </div>
        </article>

        <article class="sb-hub-card" data-hub-card>
            <div class="card-head">
                <div>
                    <p class="sb-hub-kicker">Quick access</p>
                    <h2>Controls</h2>
                </div>
            </div>

            <div class="control-list">
                <a href="{{ $pointsUrl }}"><span>⭐</span><strong>Points wallet</strong><small>Rewards and activity</small></a>
                <a href="{{ $questsUrl }}"><span>✨</span><strong>My quest</strong><small>Saved learning missions</small></a>
                <a href="{{ $communityUrl }}"><span>🌍</span><strong>Community</strong><small>Public learner profiles</small></a>
                @if(($user->is_admin ?? false) || ($user->role ?? null) === 'admin')
                    <a href="{{ url('/admin/control-room') }}"><span>⚙️</span><strong>Control Room</strong><small>Admin settings</small></a>
                @endif
            </div>
        </article>

        <article class="sb-hub-card wide" data-hub-card>
            <div class="card-head">
                <div>
                    <p class="sb-hub-kicker">Progress</p>
                    <h2>Recent activity</h2>
                </div>
                <a href="{{ $pointsUrl }}">Open wallet</a>
            </div>

            <div class="activity-list">
                @forelse($recentPoints as $item)
                    <article>
                        <span>{{ ($item->points ?? 0) >= 0 ? '+' : '' }}{{ $item->points ?? 0 }}</span>
                        <div>
                            <strong>{{ $item->label ?? $item->reason ?? 'StudyBuddy activity' }}</strong>
                            <small>{{ $item->created_at ?? 'Recently' }}</small>
                        </div>
                    </article>
                @empty
                    <div class="empty-panel">
                        <strong>No activity yet</strong>
                        <p>Open an app, complete a session, and your progress will show here.</p>
                    </div>
                @endforelse
            </div>
        </article>

        <article class="sb-hub-card" data-hub-card>
            <div class="card-head">
                <div>
                    <p class="sb-hub-kicker">Leaderboard</p>
                    <h2>Community rank</h2>
                </div>
            </div>

            <div class="leaderboard-mini">
                @forelse($leaderboard->take(5) as $index => $member)
                    <div>
                        <span>#{{ $index + 1 }}</span>
                        <strong>{{ $member->name }}</strong>
                        <small>{{ number_format((int) ($member->cosmic_points ?? 0)) }} pts</small>
                    </div>
                @empty
                    <div class="empty-panel"><strong>No rankings yet</strong></div>
                @endforelse
            </div>
        </article>
    </section>
</main>
@endsection
