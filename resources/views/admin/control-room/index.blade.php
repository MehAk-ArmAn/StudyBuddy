@extends('layouts.admin')

@section('title', 'Dashboard')

@php
    $routeUrl = function (array $names): ?string {
        foreach ($names as $name) {
            if (\Illuminate\Support\Facades\Route::has($name)) {
                return route($name);
            }
        }

        return null;
    };

    $appsUrl = $routeUrl(['admin.control-room.apps.index']);
    $addAppUrl = $routeUrl(['admin.control-room.apps.create']);
    $homepageUrl = $routeUrl(['admin.control-room.homepage-cms.index']);
    $contentUrl = $routeUrl(['admin.control-room.content-studio']);
    $usersUrl = $routeUrl(['admin.control-room.users.index', 'admin.users.index']);
    $messagesUrl = $routeUrl(['admin.control-room.contact-messages.index']);
    $shellUrl = $routeUrl(['admin.control-room.shell']);
    $settingsUrl = $routeUrl(['admin.control-room.site-settings.index', 'admin.site-settings.index']);
    $healthUrl = $routeUrl(['admin.control-room.health']);

    $dashboardStats = array_merge([
        'apps' => 0,
        'published_apps' => 0,
        'browser_apps' => 0,
        'messages' => 0,
        'new_messages' => 0,
        'users' => 0,
        'students' => 0,
        'parents' => 0,
        'teachers' => 0,
        'pages' => 0,
        'sections' => 0,
        'navigation' => 0,
        'footer' => 0,
        'quests' => 0,
        'groups' => 0,
        'assignments' => 0,
    ], $stats ?? []);

    $hour = now()->hour;
    $greeting = match (true) {
        $hour < 12 => 'Good morning',
        $hour < 18 => 'Good afternoon',
        default => 'Good evening',
    };
    $firstName = \Illuminate\Support\Str::before(auth()->user()?->name ?: 'Administrator', ' ');

    $quickActions = collect([
        [
            'title' => 'Add New App',
            'description' => 'Create a new learning experience.',
            'url' => $addAppUrl,
            'icon' => 'plus',
            'primary' => true,
        ],
        [
            'title' => 'Edit Homepage',
            'description' => 'Update the public home experience.',
            'url' => $homepageUrl,
            'icon' => 'home',
        ],
        [
            'title' => 'Manage Users',
            'description' => 'Review accounts, roles and access.',
            'url' => $usersUrl,
            'icon' => 'users',
        ],
        [
            'title' => 'View Website',
            'description' => 'Open the live learner experience.',
            'url' => url('/'),
            'icon' => 'external',
            'external' => true,
        ],
    ])->filter(fn (array $action): bool => filled($action['url']))->values();

    $workspaces = collect([
        [
            'title' => 'Apps Library',
            'description' => 'Create, preview and publish learning apps.',
            'url' => $appsUrl,
            'icon' => 'apps',
        ],
        [
            'title' => 'Content Studio',
            'description' => 'Manage shared public copy and content blocks.',
            'url' => $contentUrl,
            'icon' => 'edit',
        ],
        [
            'title' => 'Users & Roles',
            'description' => 'Manage the people who use StudyBuddy.',
            'url' => $usersUrl,
            'icon' => 'users',
        ],
        [
            'title' => 'Messages',
            'description' => 'Open messages sent through the website.',
            'url' => $messagesUrl,
            'icon' => 'message',
        ],
        [
            'title' => 'Header & Footer',
            'description' => 'Shape the navigation shared across the website.',
            'url' => $shellUrl,
            'icon' => 'palette',
        ],
        [
            'title' => 'Site Settings',
            'description' => 'Review global brand details, labels and links.',
            'url' => $settingsUrl,
            'icon' => 'settings',
        ],
    ])->filter(fn (array $workspace): bool => filled($workspace['url']))->values();
@endphp

@section('content')
<div class="sb-control-dashboard" data-admin-skip-unified>
    <section class="sb-control-welcome" aria-labelledby="control-room-title">
        <div class="sb-control-welcome__copy">
            <p class="sb-control-eyebrow">StudyBuddy Control Room</p>
            <h1 id="control-room-title">{{ $greeting }}, {{ $firstName }}.</h1>
            <p>Here’s a clear view of what’s happening across StudyBuddy.</p>
        </div>

        <div class="sb-control-welcome__actions">
            <a class="sb-control-button is-secondary" href="{{ url('/') }}" target="_blank" rel="noopener">
                <svg aria-hidden="true"><use href="#sb-admin-icon-external"></use></svg>
                View Website
            </a>
            @if($addAppUrl)
                <a class="sb-control-button is-primary" href="{{ $addAppUrl }}">
                    <svg aria-hidden="true"><use href="#sb-admin-icon-plus"></use></svg>
                    Add App
                </a>
            @endif
        </div>
    </section>

    <section class="sb-control-metrics" aria-label="StudyBuddy overview">
        <article class="sb-control-metric" data-dashboard-metric="published-apps" data-value="{{ $dashboardStats['published_apps'] }}">
            <span class="sb-control-metric__icon is-violet"><svg aria-hidden="true"><use href="#sb-admin-icon-apps"></use></svg></span>
            <div>
                <strong>{{ number_format($dashboardStats['published_apps']) }}</strong>
                <span>Published Apps</span>
                <small>{{ number_format($dashboardStats['apps']) }} total in the library</small>
            </div>
        </article>

        <article class="sb-control-metric" data-dashboard-metric="users" data-value="{{ $dashboardStats['users'] }}">
            <span class="sb-control-metric__icon is-blue"><svg aria-hidden="true"><use href="#sb-admin-icon-users"></use></svg></span>
            <div>
                <strong>{{ number_format($dashboardStats['users']) }}</strong>
                <span>Users</span>
                <small>{{ number_format($dashboardStats['students']) }} learners · {{ number_format($dashboardStats['parents']) }} parents</small>
            </div>
        </article>

        <article class="sb-control-metric" data-dashboard-metric="new-messages" data-value="{{ $dashboardStats['new_messages'] }}">
            <span class="sb-control-metric__icon is-teal"><svg aria-hidden="true"><use href="#sb-admin-icon-message"></use></svg></span>
            <div>
                <strong>{{ number_format($dashboardStats['new_messages']) }}</strong>
                <span>New Messages</span>
                <small>{{ number_format($dashboardStats['messages']) }} total in the inbox</small>
            </div>
        </article>

        <article class="sb-control-metric" data-dashboard-metric="browser-apps" data-value="{{ $dashboardStats['browser_apps'] }}">
            <span class="sb-control-metric__icon is-amber"><svg aria-hidden="true"><use href="#sb-admin-icon-external"></use></svg></span>
            <div>
                <strong>{{ number_format($dashboardStats['browser_apps']) }}</strong>
                <span>Browser Apps</span>
                <small>with a ready launch destination</small>
            </div>
        </article>
    </section>

    <section class="sb-control-section" aria-labelledby="quick-actions-title">
        <header class="sb-control-section__heading">
            <div>
                <p class="sb-control-eyebrow">Shortcuts</p>
                <h2 id="quick-actions-title">Quick actions</h2>
            </div>
            <p>Go straight to the work you do most often.</p>
        </header>

        <div class="sb-control-quick-actions">
            @foreach($quickActions as $action)
                <a
                    class="sb-control-action {{ ($action['primary'] ?? false) ? 'is-primary' : '' }}"
                    href="{{ $action['url'] }}"
                    @if($action['external'] ?? false) target="_blank" rel="noopener" @endif
                >
                    <span class="sb-control-action__icon"><svg aria-hidden="true"><use href="#sb-admin-icon-{{ $action['icon'] }}"></use></svg></span>
                    <span class="sb-control-action__copy">
                        <strong>{{ $action['title'] }}</strong>
                        <small>{{ $action['description'] }}</small>
                    </span>
                    <svg class="sb-control-action__arrow" aria-hidden="true"><use href="#sb-admin-icon-arrow"></use></svg>
                </a>
            @endforeach
        </div>
    </section>

    <div class="sb-control-dashboard__columns">
        <section class="sb-control-panel" aria-labelledby="workspaces-title">
            <header class="sb-control-panel__heading">
                <div>
                    <p class="sb-control-eyebrow">Your workspace</p>
                    <h2 id="workspaces-title">Manage StudyBuddy</h2>
                </div>
                <span>{{ $workspaces->count() }} areas</span>
            </header>

            <nav class="sb-control-workspaces" aria-label="StudyBuddy management areas">
                @foreach($workspaces as $workspace)
                    <a href="{{ $workspace['url'] }}">
                        <span class="sb-control-workspaces__icon"><svg aria-hidden="true"><use href="#sb-admin-icon-{{ $workspace['icon'] }}"></use></svg></span>
                        <span>
                            <strong>{{ $workspace['title'] }}</strong>
                            <small>{{ $workspace['description'] }}</small>
                        </span>
                        <svg aria-hidden="true"><use href="#sb-admin-icon-arrow"></use></svg>
                    </a>
                @endforeach
            </nav>
        </section>

        <aside class="sb-control-panel sb-control-snapshot" aria-labelledby="snapshot-title">
            <header class="sb-control-panel__heading">
                <div>
                    <p class="sb-control-eyebrow">Live database</p>
                    <h2 id="snapshot-title">Platform snapshot</h2>
                </div>
            </header>

            <dl>
                <div>
                    <dt>Website structure</dt>
                    <dd>{{ number_format($dashboardStats['navigation']) }} navigation · {{ number_format($dashboardStats['footer']) }} footer items</dd>
                </div>
                <div>
                    <dt>Learning activity</dt>
                    <dd>{{ number_format($dashboardStats['quests']) }} saved quests · {{ number_format($dashboardStats['assignments']) }} assignments</dd>
                </div>
                <div>
                    <dt>Community roles</dt>
                    <dd>{{ number_format($dashboardStats['teachers']) }} teachers · {{ number_format($dashboardStats['parents']) }} parents</dd>
                </div>
            </dl>

            @if($healthUrl)
                <a class="sb-control-health-link" href="{{ $healthUrl }}">
                    <span><svg aria-hidden="true"><use href="#sb-admin-icon-health"></use></svg></span>
                    <span>
                        <strong>Run a health check</strong>
                        <small>Review routes, storage and publishing readiness.</small>
                    </span>
                    <svg aria-hidden="true"><use href="#sb-admin-icon-arrow"></use></svg>
                </a>
            @endif
        </aside>
    </div>
</div>
@endsection
