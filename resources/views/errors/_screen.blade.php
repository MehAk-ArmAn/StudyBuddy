<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head><script src="{{ asset('assets/js/studybuddy-v5-theme-boot.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-v5-theme-boot.js')) }}"></script>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta name="robots" content="noindex, nofollow">

    <title>
        {{ $code }} · {{ config('studybuddy.brand.name') }}
    </title>

    @php
        $sbErrIcon = function (string $key): ?string {
            $path = config('studybuddy.icons.'.$key);

            return $path && file_exists(public_path($path)) ? asset($path) : null;
        };
    @endphp
    @if($u = $sbErrIcon('favicon_ico'))<link rel="icon" href="{{ $u }}" sizes="any">@endif
    @if($u = $sbErrIcon('favicon_32'))<link rel="icon" type="image/png" sizes="32x32" href="{{ $u }}">@endif
    @if($u = $sbErrIcon('apple_touch'))<link rel="apple-touch-icon" sizes="180x180" href="{{ $u }}">@endif
    <meta name="theme-color" content="{{ config('studybuddy.brand.theme_color') }}">

    <link
        rel="stylesheet"
        href="{{ asset('assets/css/studybuddy-system-screens.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-system-screens.css')) ? filemtime(public_path('assets/css/studybuddy-system-screens.css')) : time() }}"
    >
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-universe-v4.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-universe-v4.css')) }}"><link rel="stylesheet" href="{{ asset('assets/css/studybuddy-v5-atmosphere.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-v5-atmosphere.css')) }}"><link rel="stylesheet" href="{{ asset('assets/css/studybuddy-v6-reactivity.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-v6-reactivity.css')) }}"></head>
<body data-sb-universe-zone="auth" class="sb-system-body">@include('partials.studybuddy-v5-theme-switch')
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
                        src="{{ asset(config('studybuddy.icons.mark')) }}"
                        alt=""
                    >

                    <span>{{ config('studybuddy.brand.name') }}</span>
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
<script src="{{ asset('assets/js/studybuddy-universe-v4.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-universe-v4.js')) }}" defer></script><script src="{{ asset('assets/js/studybuddy-v5-atmosphere.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-v5-atmosphere.js')) }}" defer></script><script src="{{ asset('assets/js/studybuddy-v6-reactivity.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-v6-reactivity.js')) }}" defer></script></body>
</html>
