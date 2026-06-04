<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="StudyBuddy is a premium cosmic learning universe for learners, parents, teachers, and admins.">
    <title>@yield('title', 'StudyBuddy') · The Complete Cosmic Learning Universe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/studybuddy.css') }}">
</head>
<body class="@yield('body_class', 'page-shell')">
    <div class="cosmic-system" aria-hidden="true">
        <span class="stars stars-a"></span>
        <span class="stars stars-b"></span>
        <span class="gradient-blob blob-a"></span>
        <span class="gradient-blob blob-b"></span>
        <span class="gradient-blob blob-c"></span>
        <span class="planet planet-left"></span>
        <span class="planet planet-right"></span>
        <span class="comet comet-a"></span>
        <span class="comet comet-b"></span>
    </div>

    @include('partials.navigation')

    <main class="site-main">
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="{{ asset('assets/js/studybuddy.js') }}" defer></script>
</body>
</html>
