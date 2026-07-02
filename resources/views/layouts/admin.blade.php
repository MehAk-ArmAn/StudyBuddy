@php
    $adminTitle = trim($__env->yieldContent('title')) ?: ($title ?? 'StudyBuddy Admin');
    $adminUser = auth()->user();
    $adminLinks = [
        ['label' => 'Overview', 'url' => url('/admin/control-room'), 'icon' => 'dashboard.svg'],
        ['label' => 'Website Shell', 'url' => url('/admin/control-room/shell'), 'icon' => 'shell.svg'],
        ['label' => 'Content Studio', 'url' => url('/admin/control-room/content-studio'), 'icon' => 'content.svg'],
        ['label' => 'Apps & Platform', 'url' => url('/admin/control-room/final-platform'), 'icon' => 'apps.svg'],
        ['label' => 'Users & Roles', 'url' => url('/admin/control-room/users'), 'icon' => 'users.svg'],
        ['label' => 'Safety Review', 'url' => url('/admin/control-room/verifications'), 'icon' => 'safety.svg'],
        ['label' => 'Site Settings', 'url' => url('/admin/control-room/site-settings'), 'icon' => 'settings.svg'],
    ];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $adminTitle }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/sb-control-room-admin.css') }}?v={{ file_exists(public_path('assets/css/sb-control-room-admin.css')) ? filemtime(public_path('assets/css/sb-control-room-admin.css')) : time() }}">
    @stack('styles')
</head>
<body class="sb-control-admin-body">
    <div class="sb-control-admin-app">
        <aside class="sb-control-sidebar">
            <a class="sb-control-brand" href="{{ url('/admin/control-room') }}">
                <img src="{{ asset('assets/studybuddy-control/logo.svg') }}" alt="StudyBuddy">
                <span><strong>StudyBuddy</strong><em>Control Room</em></span>
            </a>

            <nav class="sb-control-nav" aria-label="Admin navigation">
                @foreach($adminLinks as $link)
                    @php $path = trim(parse_url($link['url'], PHP_URL_PATH), '/'); @endphp
                    <a href="{{ $link['url'] }}" @class(['active' => request()->is($path . '*')])>
                        <img src="{{ asset('assets/studybuddy-control/' . $link['icon']) }}" alt="">
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="sb-control-upgrade">
                <img src="{{ asset('assets/studybuddy-control/reports.svg') }}" alt="">
                <strong>Professional Mode</strong>
                <p>Everything stays inside the control room.</p>
            </div>
        </aside>

        <main class="sb-control-main">
            <header class="sb-control-topbar">
                <div>
                    <p>StudyBuddy Admin</p>
                    <h1>{{ $adminTitle }}</h1>
                </div>

                <div class="sb-control-top-actions">
                    <a href="{{ url('/') }}" target="_blank" rel="noopener">Preview website</a>
                    <span class="sb-control-avatar">{{ strtoupper(substr($adminUser->name ?? 'A', 0, 1)) }}</span>
                    @auth
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Logout</button></form>
                    @endauth
                </div>
            </header>

            @if(session('status'))
                <div class="sb-control-alert">{{ session('status') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
