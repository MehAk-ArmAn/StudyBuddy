@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $isVerified = $user && $user->hasVerifiedEmail();
    $roleLabels = [
        'all' => 'All learners',
        'student' => 'Students',
        'parent' => 'Parents',
        'teacher' => 'Teachers',
        'independent_learner' => 'Independent learners',
    ];
    $statusLabels = [
        'all' => 'All statuses',
        'web' => 'Playable on web',
        'download' => 'Download ready',
        'live' => 'Live',
        'beta' => 'Beta',
        'planned' => 'Coming soon',
        'concept' => 'Concepts',
    ];
@endphp

<main class="sb-apps-shell" data-sb-unified-apps>
    <section class="sb-apps-hero">
        <div class="sb-apps-hero-copy">
            <p class="sb-apps-kicker">StudyBuddy Apps</p>
            <h1>{{ $settings['apps_page_heading'] ?? 'Choose your StudyBuddy learning world.' }}</h1>
            <p>{{ $settings['apps_page_intro'] ?? 'Browse every mini-app in one place. Guests can preview each world; verified learners can save quests, play sessions, and earn points.' }}</p>
            <div class="sb-apps-actions">
                @auth
                    <a class="sb-apps-btn" href="{{ route('dashboard') }}">Go to Dashboard</a>
                    @if(Route::has('studybuddy.quests.index'))<a class="sb-apps-btn soft" href="{{ route('studybuddy.quests.index') }}">My Quest</a>@endif
                    @if(Route::has('studybuddy.final.points-wallet'))<a class="sb-apps-btn soft" href="{{ route('studybuddy.final.points-wallet') }}">Points Wallet</a>@endif
                @else
                    <a class="sb-apps-btn" href="{{ route('register') }}">Create Free Account</a>
                    <a class="sb-apps-btn soft" href="{{ route('login') }}">Login to Save</a>
                @endauth
            </div>
        </div>
        <aside class="sb-apps-orbit-card" aria-label="StudyBuddy app universe summary">
            <span class="sb-apps-orbit-icon">🚀</span>
            <strong>{{ $allApps->count() }}</strong>
            <p>mini-app worlds controlled from one admin panel</p>
        </aside>
    </section>

    <section class="sb-apps-filters" aria-label="Filter StudyBuddy apps">
        <form method="GET" action="{{ route('pages.apps') }}" class="sb-apps-filter-form">
            <label>
                <span>Search</span>
                <input type="search" name="q" placeholder="Search apps..." data-sb-app-search>
            </label>
            <label>
                <span>Role</span>
                <select name="role" onchange="this.form.submit()">
                    @foreach($roleLabels as $value => $label)
                        <option value="{{ $value }}" @selected($role === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Category</span>
                <select name="category" onchange="this.form.submit()">
                    <option value="all" @selected($category === 'all')>All categories</option>
                    @foreach($categories as $categoryName)
                        <option value="{{ $categoryName }}" @selected($category === $categoryName)>{{ $categoryName }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Status</span>
                <select name="status" onchange="this.form.submit()">
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </form>
        <div class="sb-apps-filter-pills" aria-label="Quick filters">
            <a href="{{ route('pages.apps') }}" @class(['is-active' => $role === 'all' && $status === 'all' && $category === 'all'])>All</a>
            <a href="{{ route('pages.apps', ['role' => 'student']) }}" @class(['is-active' => $role === 'student'])>Students</a>
            <a href="{{ route('pages.apps', ['role' => 'parent']) }}" @class(['is-active' => $role === 'parent'])>Parents</a>
            <a href="{{ route('pages.apps', ['role' => 'teacher']) }}" @class(['is-active' => $role === 'teacher'])>Teachers</a>
            <a href="{{ route('pages.apps', ['role' => 'independent_learner']) }}" @class(['is-active' => $role === 'independent_learner'])>Independent</a>
            <a href="{{ route('pages.apps', ['status' => 'web']) }}" @class(['is-active' => $status === 'web'])>Web Play</a>
        </div>
    </section>

    <section class="sb-apps-grid" aria-label="StudyBuddy app list">
        @forelse($apps as $app)
            @php
                $searchText = strtolower(trim($app->name.' '.$app->tagline.' '.$app->description.' '.$app->category.' '.implode(' ', $app->rolesList()).' '.implode(' ', $app->learningTagsList())));
            @endphp
            <article class="sb-app-card" data-sb-app-card data-search="{{ $searchText }}">
                <div class="sb-app-card-media">
                    @if($app->image_url)
                        <img src="{{ str_starts_with($app->image_url, 'http') ? $app->image_url : asset(ltrim($app->image_url, '/')) }}" alt="{{ $app->name }} preview">
                    @else
                        <span>{{ $app->icon ?: '✨' }}</span>
                    @endif
                    <span class="sb-app-status status-{{ $app->status }}">{{ $app->statusLabel() }}</span>
                </div>
                <div class="sb-app-card-body">
                    <p class="sb-app-category">{{ $app->category }} · {{ $app->age_range ?? 'All ages' }}</p>
                    <h2>{{ $app->name }}</h2>
                    <p class="sb-app-tagline">{{ $app->tagline }}</p>
                    <p>{{ $app->description }}</p>
                    <div class="sb-app-meta">
                        <span>⭐ {{ $app->points_reward }} pts</span>
                        <span>⏱ {{ $app->estimated_minutes }} min</span>
                    </div>
                    <div class="sb-app-role-chips">
                        @foreach($app->rolesList() as $roleLabel)
                            <span>{{ $roleLabel }}</span>
                        @endforeach
                    </div>
                    <div class="sb-app-card-actions">
                        <a class="sb-app-btn" href="{{ route('studybuddy.apps.show', $app->slug) }}">View Details</a>
                        @if($app->is_web_enabled)
                            @if($isVerified)
                                <a class="sb-app-btn soft" href="{{ route('studybuddy.final.web-play', $app->slug) }}">Play Web</a>
                            @else
                                <button class="sb-app-btn soft" type="button" data-sb-lock-trigger data-lock-kind="{{ auth()->check() ? 'verify' : 'login' }}">Preview Web</button>
                            @endif
                        @else
                            <button class="sb-app-btn ghost" type="button" data-sb-lock-trigger data-lock-kind="soon">Web Soon</button>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="sb-app-empty">
                <h2>No apps found yet.</h2>
                <p>Try clearing filters or add apps from the admin final platform panel.</p>
            </div>
        @endforelse
    </section>

    <section class="sb-apps-guest-note">
        <h2>Guest preview vs full account access</h2>
        <div class="sb-guest-grid">
            <div><strong>Guests</strong><p>Can browse and preview apps, see details, and explore the platform.</p></div>
            <div><strong>Logged-in learners</strong><p>Can save quests and personalize the dashboard.</p></div>
            <div><strong>Verified learners</strong><p>Can play web sessions, earn points, and track progress safely.</p></div>
            <div><strong>Admins</strong><p>Control every app, link, status, role, and page message from admin.</p></div>
        </div>
    </section>
</main>

@include('studybuddy.apps.partials.locked-modal')
@endsection
