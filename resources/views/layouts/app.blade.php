<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $settings['seo_title'] ?? $settings['site_name'] ?? config('app.name', 'StudyBuddy') }}</title>

    @if (!empty($settings['seo_description']))
        <meta name="description" content="{{ $settings['seo_description'] }}">
    @endif

    @if (!empty($settings['seo_keywords']))
        <meta name="keywords" content="{{ $settings['seo_keywords'] }}">
    @endif

    @if (!empty($settings['favicon_path']))
        <link rel="icon" href="{{ asset($settings['favicon_path']) }}">
    @endif

    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}">
    @stack('styles')
</head>
<body id="top" class="studybuddy-site">
    @include('partials.navbar', [
        'settings' => $settings ?? [],
        'navigationItems' => $navigationItems ?? collect(),
    ])

    <main>
        @yield('content')
    </main>

    @include('partials.footer', [
        'settings' => $settings ?? [],
        'footerGroups' => $footerGroups ?? collect(),
    ])

    <script src="{{ asset('assets/js/site.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
