<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta name="robots" content="noindex, nofollow">

    <title>
        {{ $code }} · {{ $title }} · StudyBuddy
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('assets/css/studybuddy-system-screens.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-system-screens.css')) ? filemtime(public_path('assets/css/studybuddy-system-screens.css')) : time() }}"
    >
</head>
<body class="sb-system-body">
    <main class="sb-system-screen">
        <section class="sb-system-screen__panel">
            <div class="sb-system-screen__visual">
                <span
                    class="sb-system-screen__glow"
                    aria-hidden="true"
                ></span>

                <img
                    src="{{ asset($image) }}"
                    alt="{{ $imageAlt ?? '' }}"
                >
            </div>

            <div class="sb-system-screen__content">
                <div class="sb-system-screen__brand">
                    <img
                        src="{{ asset('assets/studybuddy-brand/studybuddy-logo-mark.svg') }}"
                        alt=""
                    >

                    <span>StudyBuddy system</span>
                </div>

                <p class="sb-system-screen__eyebrow">
                    Error {{ $code }}
                </p>

                <h1>{{ $title }}</h1>

                <p class="sb-system-screen__lead">
                    {{ $message }}
                </p>

                <div class="sb-system-screen__actions">
                    <a
                        class="sb-system-button sb-system-button--primary"
                        href="{{ url('/') }}"
                    >
                        Return home
                    </a>

                    <a
                        class="sb-system-button sb-system-button--secondary"
                        href="{{ url('/apps') }}"
                    >
                        Explore apps
                    </a>

                    <button
                        type="button"
                        class="sb-system-button sb-system-button--secondary"
                        onclick="window.location.reload()"
                    >
                        Try again
                    </button>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
