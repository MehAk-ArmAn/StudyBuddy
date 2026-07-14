@php
    $adminTitle = trim($__env->yieldContent('title')) ?: ($title ?? 'StudyBuddy Admin');
    $adminUser = auth()->user();

    $adminPhotoUrl = null;
    if ($adminUser && !empty($adminUser->profile_photo_path)) {
        $adminPhotoUrl = preg_match('/^https?:\/\//i', $adminUser->profile_photo_path)
            ? $adminUser->profile_photo_path
            : asset('storage/' . ltrim($adminUser->profile_photo_path, '/'));
    }

    $adminLinks = [
        ['label' => 'Overview', 'url' => url('/admin/control-room'), 'icon' => 'dashboard.svg'],
        ['label' => 'Website Shell', 'url' => url('/admin/control-room/shell'), 'icon' => 'shell.svg'],
        ['label' => 'Pages & Legal', 'url' => url('/admin/control-room/pages-legal'), 'icon' => 'content.svg'],
        ['label' => 'Content Studio', 'url' => url('/admin/control-room/content-studio'), 'icon' => 'content.svg'],
        ['label' => 'Apps & Platform', 'url' => url('/admin/control-room/final-platform'), 'icon' => 'apps.svg'],
        ['label' => 'Role Tools', 'url' => url('/admin/control-room/role-tools'), 'icon' => 'users.svg'],
        ['label' => 'Users & Roles', 'url' => url('/admin/control-room/users'), 'icon' => 'users.svg'],
        ['label' => 'Safety Review', 'url' => url('/admin/control-room/verifications'), 'icon' => 'safety.svg'],
        ['label' => 'Site Settings', 'url' => url('/admin/control-room/site-settings'), 'icon' => 'settings.svg'],
        ['label' => 'Admin Account', 'url' => url('/admin/control-room/account'), 'icon' => 'settings.svg'],
    ];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $adminTitle }}</title>

    <link rel="stylesheet" href="{{ asset('assets/css/sb-control-room-admin.css') }}?v={{ file_exists(public_path('assets/css/sb-control-room-admin.css')) ? filemtime(public_path('assets/css/sb-control-room-admin.css')) : time() }}">

    @if(file_exists(public_path('assets/css/sb-control-room-hardening.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/sb-control-room-hardening.css') }}?v={{ filemtime(public_path('assets/css/sb-control-room-hardening.css')) }}">
    @endif

    <link rel="stylesheet" href="{{ asset('assets/css/sb-control-room-pro-v2.css') }}?v={{ file_exists(public_path('assets/css/sb-control-room-pro-v2.css')) ? filemtime(public_path('assets/css/sb-control-room-pro-v2.css')) : time() }}">
    @stack('styles')
</head>
<body class="sb-control-admin-body">
    <div class="sb-control-admin-app pro-admin-shell">
        <aside class="sb-control-sidebar pro-sidebar">
            <a class="sb-control-brand pro-brand" href="{{ url('/admin/control-room') }}">
                <img src="{{ asset('assets/studybuddy-control/logo.svg') }}" alt="StudyBuddy">
                <span>
                    <strong>StudyBuddy</strong>
                    <em>Control Room</em>
                </span>
            </a>

            <nav class="sb-control-nav pro-nav" aria-label="Admin navigation">
                @foreach($adminLinks as $link)
                    @php($path = trim(parse_url($link['url'], PHP_URL_PATH), '/'))
                    <a href="{{ $link['url'] }}" @class(['active' => request()->is($path . '*')])>
                        <img src="{{ asset('assets/studybuddy-control/' . $link['icon']) }}" alt="">
                        <span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="sb-control-upgrade pro-helper">
                <strong>Admin rule</strong>
                <p>Shell, pages, apps, roles, users, and safety are managed from this room.</p>
                <a href="{{ url('/') }}" target="_blank" rel="noopener">Preview live site</a>
            </div>
        </aside>

        <main class="sb-control-main pro-main">
            <header class="sb-control-topbar pro-topbar">
                <div>
                    <p>StudyBuddy Admin</p>
                    <h1>{{ $adminTitle }}</h1>
                </div>

                <div class="sb-control-top-actions pro-actions">
                    <a href="{{ url('/') }}" target="_blank" rel="noopener">Preview</a>
                    <a href="{{ url('/admin/control-room/account') }}">Account</a>

                    <span class="sb-control-avatar pro-avatar">
                        @if($adminPhotoUrl)
                            <img src="{{ $adminPhotoUrl }}" alt="{{ $adminUser->name ?? 'Admin' }} profile picture">
                        @else
                            {{ strtoupper(substr($adminUser->name ?? 'A', 0, 1)) }}
                        @endif
                    </span>

                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    @endauth
                </div>
            </header>

            @if(session('status'))
                <div class="sb-control-alert pro-alert">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="sb-control-alert pro-alert error">
                    <strong>Fix this first:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="{{ asset('assets/js/sb-control-room-pro-v2.js') }}?v={{ file_exists(public_path('assets/js/sb-control-room-pro-v2.js')) ? filemtime(public_path('assets/js/sb-control-room-pro-v2.js')) : time() }}" defer></script>
    @stack('scripts')
</body>
</html>
