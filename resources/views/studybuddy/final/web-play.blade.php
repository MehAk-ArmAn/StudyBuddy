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
    data-complete-url="{{ auth()->check() && ! ($adminPreview ?? false) ? route('studybuddy.final.session.complete') : '' }}"
    data-app-slug="{{ $app->slug }}"
    data-app-name="{{ $app->name }}"
    data-points="{{ max(0, (int) $app->points_reward) }}"
>
    <header class="sb-launcher-v3__header">
        <div class="sb-launcher-v3__title">
            <a
                class="sb-launcher-v3__back"
                href="{{ $detailUrl ?? route('studybuddy.apps.show', $app->slug) }}"
            >
                <svg
                    viewBox="0 0 24 24"
                    width="18"
                    height="18"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="m15 18-6-6 6-6"></path>
                </svg>

                <span>Back to app</span>
            </a>

            <div class="sb-launcher-v3__identity">
                <p>Playing in browser</p>
                <h1>{{ $app->name }}</h1>
            </div>
        </div>

        <div class="sb-launcher-v3__controls">
            <span
                class="sb-launcher-v3__state"
                data-launcher-state
                data-state="{{ $canLaunch ? 'loading' : 'idle' }}"
                role="status"
                aria-live="polite"
            >
                {{ $canLaunch ? 'Loading app…' : 'Not ready yet' }}
            </span>

            @if($canLaunch && $embedUrl)
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

            <a href="{{ ($adminPreview ?? false) ? route('admin.control-room.apps.edit', $app) : route('studybuddy.apps') }}">
                {{ ($adminPreview ?? false) ? 'Back to editor' : 'All apps' }}
            </a>
        </div>
    </header>

    @if($adminPreview ?? false)
        <aside class="sb-admin-preview-banner" role="status">
            <strong>Private browser test</strong>
            <span>This draft is not public. Test the launch here before publishing.</span>
            <a href="{{ route('admin.control-room.apps.edit', $app) }}">Back to editor</a>
        </aside>
    @endif

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

    @if($canLaunch && $externalUrl)
        <section class="sb-launcher-v3__handoff">
            <h2>{{ $app->name }} opens on its own site</h2>

            <p>
                This one is hosted outside StudyBuddy, so it runs in a new tab.
                Your place here stays open.
            </p>

            <div class="sb-launcher-v3__handoff-actions">
                <a
                    class="sb-app-button sb-app-button--primary"
                    href="{{ $externalUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Open {{ $app->name }}
                </a>

                <a class="sb-app-button sb-app-button--secondary" href="{{ $detailUrl ?? route('studybuddy.apps.show', $app->slug) }}">
                    Back to the app page
                </a>
            </div>
        </section>
    @elseif($canLaunch)
        <section
            class="sb-launcher-v3__stage"
            data-launcher-stage
        >
            <div
                class="sb-launcher-v3__loading"
                data-launcher-loading
            >
                <div class="sb-launcher-v3__spinner" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <strong>Getting {{ $app->name }} ready</strong>
                <span>This can take a moment the first time.</span>
            </div>

            <div
                class="sb-launcher-v3__failed"
                data-launcher-failed
                role="alert"
                hidden
            >
                <h2>We couldn't start this activity.</h2>

                <p>
                    Something got in the way while {{ $app->name }} was opening.
                    Trying again usually sorts it out.
                </p>

                <div class="sb-launcher-v3__failed-actions">
                    <button
                        type="button"
                        class="sb-launcher-v3__complete"
                        data-launcher-retry
                    >
                        Retry
                    </button>

                    <a
                        href="{{ $embedUrl }}"
                        target="_blank"
                        rel="noopener"
                    >
                        Open separately
                    </a>

                    <a href="{{ $detailUrl ?? route('studybuddy.apps.show', $app->slug) }}">
                        Back to app
                    </a>
                </div>
            </div>

            <iframe
                src="{{ $embedUrl }}"
                title="{{ $app->name }} web app"
                loading="eager"
                data-launcher-frame
                allow="autoplay; fullscreen; clipboard-write"
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
            @elseif(! ($adminPreview ?? false))
                <a
                    class="sb-launcher-v3__complete"
                    href="{{ route('login') }}"
                >
                    Sign in to save points
                </a>
            @else
                <span class="sb-launcher-v3__preview-note">Points are not recorded during an Admin preview.</span>
            @endif
        </section>
    @else
        <section class="sb-launcher-v3__unavailable">
            <img
                src="{{ asset('assets/studybuddy-control/apps.svg') }}"
                alt=""
            >

            <p>{{ $app->name }}</p>

            <h2>This one isn't playable yet.</h2>

            <p>
                It's on the shelf, but the browser version isn't ready.
                Have a look at what else there is to play.
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
                        href="{{ route('admin.control-room.apps.edit', $app) }}"
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
