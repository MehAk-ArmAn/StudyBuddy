<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') · StudyBuddy CMS</title>
    <link rel="stylesheet" href="{{ asset('assets/css/studybuddy.css') }}">
</head>
<body class="studybuddy-admin-cms">
@php
    $admin = request()->attributes->get('admin_user');
    $links = [
        ['Dashboard', 'admin.dashboard'], ['Page Content', 'admin.pages'], ['Navigation', 'admin.navigation'], ['Footer', 'admin.footer'],
        ['Apps Manager', 'admin.apps'], ['Rewards Manager', 'admin.rewards'], ['Badges Manager', 'admin.badges'], ['Dashboard Content', 'admin.dashboards'],
        ['Showcase Manager', 'admin.showcase'], ['Mobile Preview', 'admin.mobile-preview'], ['Assets Library', 'admin.assets'], ['Settings', 'admin.settings'], ['Admin Users', 'admin.users'],
    ];
@endphp
<div class="admin-cms-shell">
    <aside class="admin-cms-sidebar">
        <a class="admin-cms-brand" href="{{ route('admin.dashboard') }}"><img src="{{ asset('assets/studybuddy/logo-icon.png') }}" alt="StudyBuddy logo"><span>StudyBuddy CMS</span></a>
        <nav aria-label="Admin modules">
            @foreach($links as [$label, $route])
                <a @class(['is-active' => request()->routeIs($route)]) href="{{ route($route) }}">{{ $label }}</a>
            @endforeach
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit">Logout</button></form>
        </nav>
    </aside>
    <main class="admin-cms-main">
        <header class="admin-cms-topbar">
            <div><small>Control everything</small><h1>@yield('heading', 'Admin Dashboard')</h1></div>
            <div class="admin-cms-user"><span>{{ $admin?->name ?? 'Admin' }}</span><small>{{ $admin?->email ?? '' }}</small></div>
        </header>
        @if(session('status'))<div class="admin-cms-alert">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="admin-cms-alert is-error">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
</div>
</body>
</html>
