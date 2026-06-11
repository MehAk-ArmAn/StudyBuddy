<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/studybuddy.css') }}">
    <style>
        body { margin: 0; min-height: 100vh; background: radial-gradient(circle at top left, #312e81, #020617 50%); color: #f8fafc; font-family: Inter, system-ui, sans-serif; }
        .admin-shell { display: grid; grid-template-columns: minmax(220px, 280px) 1fr; min-height: 100vh; }
        .admin-sidebar, .admin-topbar, .admin-panel { background: rgba(15, 23, 42, .86); border: 1px solid rgba(255, 255, 255, .14); }
        .admin-sidebar { padding: 24px; }
        .admin-main { padding: 24px; }
        .admin-topbar, .admin-panel { border-radius: 24px; padding: 20px; margin-bottom: 20px; }
        .admin-nav { display: grid; gap: 10px; }
        .admin-nav a, .admin-button { color: #fff; text-decoration: none; border: 1px solid rgba(255,255,255,.14); border-radius: 999px; padding: 10px 14px; background: rgba(255,255,255,.08); display: inline-flex; }
        input, select, textarea { width: 100%; border-radius: 14px; border: 1px solid rgba(255,255,255,.16); background: rgba(2,6,23,.75); color: #fff; padding: 12px; }
    </style>
</head>
<body>
    <div class="admin-shell">
        @include('admin.partials.sidebar')
        <div class="admin-main">
            @include('admin.partials.topbar')
            @yield('content')
        </div>
    </div>
</body>
</html>
