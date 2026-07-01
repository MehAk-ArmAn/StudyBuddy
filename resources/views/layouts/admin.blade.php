<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudyBuddy Admin</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    @if(file_exists(public_path('assets/css/sb-final-ui-safety-polish.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/sb-final-ui-safety-polish.css') }}?v={{ filemtime(public_path('assets/css/sb-final-ui-safety-polish.css')) }}">
    @endif
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar" aria-label="Admin sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <span class="admin-brand-mark">✦</span>
                <span><strong>StudyBuddy</strong><small>Admin Control Center</small></span>
            </a>

            <nav class="admin-nav" aria-label="Admin navigation">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><span>Dashboard</span></a>
                <a class="nav-link {{ request()->routeIs('studybuddy.admin.content.*') ? 'active' : '' }}" href="{{ route('studybuddy.admin.content.index') }}"><span>Content Studio</span></a>
                @if(Route::has('studybuddy.admin.final.index'))<a class="nav-link {{ request()->routeIs('studybuddy.admin.final.*') ? 'active' : '' }}" href="{{ route('studybuddy.admin.final.index') }}"><span>App Platform</span></a>@endif
                @if(Route::has('studybuddy.admin.verifications.index'))<a class="nav-link {{ request()->routeIs('studybuddy.admin.verifications.*') ? 'active' : '' }}" href="{{ route('studybuddy.admin.verifications.index') }}"><span>Verifications</span></a>@endif
                <a class="nav-link {{ request()->routeIs('admin.site-settings.*') ? 'active' : '' }}" href="{{ route('admin.site-settings.index') }}"><span>Site Settings</span></a>
                <a class="nav-link {{ request()->routeIs('admin.navigation-items.*') ? 'active' : '' }}" href="{{ route('admin.navigation-items.index') }}"><span>Navigation</span></a>
                <a class="nav-link {{ request()->routeIs('admin.footer-items.*') ? 'active' : '' }}" href="{{ route('admin.footer-items.index') }}"><span>Footer</span></a>
                <a class="nav-link {{ request()->routeIs('admin.media-assets.*') ? 'active' : '' }}" href="{{ route('admin.media-assets.index') }}"><span>Media Library</span></a>
                <a class="nav-link {{ request()->routeIs('admin.homepage-sections.*') || request()->routeIs('admin.homepage-section-items.*') ? 'active' : '' }}" href="{{ route('admin.homepage-sections.index') }}"><span>Homepage</span></a>
                <a class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}"><span>Pages</span></a>
                <a class="nav-link" href="{{ route('studybuddy.apps') }}" target="_blank" rel="noopener"><span>Preview Apps</span></a>
            </nav>

            <div class="admin-sidebar-footer">
                <p>Logged in as</p>
                <strong>{{ auth()->user()?->name ?? 'Admin' }}</strong>
                <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="logout-button" type="submit">Logout</button></form>
            </div>
        </aside>

        <main class="admin-main" id="admin-content">
            <header class="admin-topbar">
                <div><p class="admin-kicker">StudyBuddy Control Center</p><h1>@yield('page_title', 'Dashboard')</h1></div>
                <a class="site-preview" href="{{ route('home') }}" target="_blank" rel="noopener">View Site</a>
            </header>

            @if (session('status'))<div class="notice">{{ session('status') }}</div>@endif
            @yield('content')
        </main>
    </div>
</body>
</html>
