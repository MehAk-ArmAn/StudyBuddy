@extends('layouts.app')

@section('title', 'Play '.$app->name)

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('assets/css/studybuddy-launcher-v3.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-launcher-v3.css')) ? filemtime(public_path('assets/css/studybuddy-launcher-v3.css')) : time() }}"
>
@endpush

@section('content')
<div
    class="sb-launcher-v3"
    data-studybuddy-launcher
    data-complete-url="{{ auth()->check() ? route('studybuddy.final.session.complete') : '' }}"
    data-app-slug="{{ $app->slug }}"
    data-points="{{ max(0, (int) $app->points_reward) }}"
>
    <header class="sb-launcher-v3__header">
        <div class="sb-launcher-v3__title">
            <a
                href="{{ route('studybuddy.apps.show', $app->slug) }}"
                aria-label="Back to {{ $app->name }}"
            >
                <svg
                    viewBox="0 0 24 24"
                    width="19"
                    height="19"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <path d="m15 18-6-6 6-6"></path>
                </svg>
            </a>

            <div>
                <p>StudyBuddy Web App</p>
                <h1>{{ $app->name }}</h1>
            </div>
        </div>

        <div class="sb-launcher-v3__controls">
            <span
                class="sb-launcher-v3__state"
                data-launcher-state
            >
                {{ $canLaunch ? 'Preparing app' : 'Not published' }}
            </span>

            @if($canLaunch)
                <button
                    type="button"
                    data-launcher-reload
                >
                    Reload
                </button>

                <button
                    type="button"
                    data-launcher-fullscreen
                >
                    Full screen
                </button>

                <a
                    href="{{ $embedUrl }}"
                    target="_blank"
                    rel="noopener"
                >
                    New tab
                </a>
            @endif

            <a href="{{ route('studybuddy.apps') }}">
                All apps
            </a>
        </div>
    </header>

    @if(session('status'))
        <div
            class="sb-launcher-v3__notice"
            role="status"
        >
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div
            class="sb-launcher-v3__notice is-error"
            role="alert"
        >
            {{ $errors->first() }}
        </div>
    @endif

    @if($canLaunch)
        <section
            class="sb-launcher-v3__stage"
            data-launcher-stage
        >
            <div
                class="sb-launcher-v3__loading"
                data-launcher-loading
            >
                <div></div>
                <strong>Opening {{ $app->name }}</strong>
                <span>Loading the published app package…</span>
            </div>

            <iframe
                src="{{ $embedUrl }}"
                title="{{ $app->name }} web app"
                loading="eager"
                data-launcher-frame
                allow="
                    autoplay;
                    fullscreen;
                    gamepad;
                    clipboard-read;
                    clipboard-write;
                    microphone;
                    camera
                "
                sandbox="
                    allow-scripts
                    allow-same-origin
                    allow-forms
                    allow-modals
                    allow-popups
                    allow-popups-to-escape-sandbox
                    allow-downloads
                    allow-pointer-lock
                    allow-presentation
                "
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
            ></iframe>
        </section>

        <section class="sb-launcher-v3__footer">
            <div>
                <strong>Session controls</strong>

                <p data-launcher-message>
                    Complete the activity inside the app, then save your
                    StudyBuddy points.
                </p>
            </div>

            @if($canEarnPoints)
                <button
                    type="button"
                    class="sb-launcher-v3__complete"
                    data-launcher-complete
                >
                    Save {{ max(0, (int) $app->points_reward) }} points
                </button>
            @else
                <a
                    class="sb-launcher-v3__complete"
                    href="{{ route('login') }}"
                >
                    Sign in to save points
                </a>
            @endif
        </section>
    @else
        <section class="sb-launcher-v3__unavailable">
            <img
                src="{{ asset('assets/studybuddy-control/apps.svg') }}"
                alt=""
            >

            <p>Web app launcher</p>

            <h2>This package has not been published yet.</h2>

            <p>
                An admin can upload a static web-app ZIP containing
                <code>index.html</code> from Control Room →
                Apps &amp; Platform.
            </p>

            <div>
                <a
                    class="sb-launcher-v3__complete"
                    href="{{ route('studybuddy.apps') }}"
                >
                    Explore available apps
                </a>

                @if(auth()->user()?->is_admin)
                    <a
                        href="{{ route('admin.control-room.final-platform') }}"
                    >
                        Open app controls
                    </a>
                @endif
            </div>
        </section>
    @endif
</div>
@endsection

@push('scripts')
<script
    src="{{ asset('assets/js/studybuddy-launcher-v3.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-launcher-v3.js')) ? filemtime(public_path('assets/js/studybuddy-launcher-v3.js')) : time() }}"
    defer
></script>
@endpush
