<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="StudyBuddy is a premium cosmic learning universe for learners, parents, teachers, and admins.">
    <title>@yield('title', 'StudyBuddy') · Cosmic Learning Universe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/studybuddy.css') }}">
</head>
<body>
    <div class="cosmic-bg" aria-hidden="true">
        <span class="planet planet-one"></span>
        <span class="planet planet-two"></span>
        <span class="comet comet-one"></span>
        <span class="comet comet-two"></span>
        <span class="starfield"></span>
    </div>

    @include('partials.navigation')

    <main class="site-main">
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="{{ asset('assets/js/studybuddy.js') }}" defer></script>
</body>
</html>
