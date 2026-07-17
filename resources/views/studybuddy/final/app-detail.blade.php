@extends('layouts.app')

@section('title', $app->name)

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('assets/css/studybuddy-apps-v3.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-apps-v3.css')) ? filemtime(public_path('assets/css/studybuddy-apps-v3.css')) : time() }}"
>
<link
    rel="stylesheet"
    href="{{ asset('assets/css/studybuddy-launcher-v3.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-launcher-v3.css')) ? filemtime(public_path('assets/css/studybuddy-launcher-v3.css')) : time() }}"
>
@endpush

@php
    $imageUrl = function (?string $path): string {
        if (blank($path)) {
            return asset('assets/studybuddy-control/apps.svg');
        }

        return preg_match('/^https?:\/\//i', $path)
            ? $path
            : asset(ltrim($path, '/'));
    };

    $ready = $app->hasPublishedWebApp();

    $roles = collect(
        $app->audience_roles
        ?: [
            'student',
            'parent',
            'teacher',
            'independent_learner',
        ]
    );

    $roleLabels = [
        'student' => 'Learners',
        'parent' => 'Parents',
        'teacher' => 'Teachers',
        'independent_learner' => 'Independent learners',
    ];
@endphp

@section('content')
<div class="sb-app-detail-v3">
    <a
        class="sb-detail-back-v3"
        href="{{ route('studybuddy.apps') }}"
    >
        <svg
            viewBox="0 0 24 24"
            width="18"
            height="18"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            aria-hidden="true"
        >
            <path d="m15 18-6-6 6-6"></path>
        </svg>

        All apps
    </a>

    <section class="sb-app-detail-v3__hero">
        <div class="sb-app-detail-v3__copy">
            <div class="sb-app-detail-v3__badges">
                <span>{{ $app->category ?: 'Learning' }}</span>

                <span class="{{ $ready ? 'is-ready' : '' }}">
                    {{ $ready ? 'Available now' : ucfirst($app->status ?: 'planned') }}
                </span>
            </div>

            <h1>{{ $app->name }}</h1>

            @if($app->tagline)
                <p class="sb-app-detail-v3__tagline">
                    {{ $app->tagline }}
                </p>
            @endif

            <p class="sb-app-detail-v3__description">
                {{
                    $app->description
                    ?: $app->preview_text
                    ?: 'A focused learning experience inside StudyBuddy.'
                }}
            </p>

            <dl class="sb-app-detail-v3__facts">
                <div>
                    <dt>Session</dt>
                    <dd>{{ max(1, (int) $app->estimated_minutes) }} minutes</dd>
                </div>

                <div>
                    <dt>Reward</dt>
                    <dd>{{ max(0, (int) $app->points_reward) }} points</dd>
                </div>

                <div>
                    <dt>Age range</dt>
                    <dd>
                        @if($app->age_min && $app->age_max)
                            {{ $app->age_min }}–{{ $app->age_max }}
                        @elseif($app->age_min)
                            {{ $app->age_min }}+
                        @else
                            All ages
                        @endif
                    </dd>
                </div>
            </dl>

            <div class="sb-app-detail-v3__actions">
                @if($ready)
                    <a
                        class="sb-app-button sb-app-button--primary"
                        href="{{ route('studybuddy.final.web-play', $app->slug) }}"
                    >
                        Launch web app
                    </a>
                @else
                    <span
                        class="sb-app-button sb-app-button--disabled"
                        aria-disabled="true"
                    >
                        Package not published
                    </span>
                @endif

                @if(auth()->user()?->is_admin)
                    <a
                        class="sb-app-button sb-app-button--secondary"
                        href="{{ route('admin.control-room.final-platform') }}"
                    >
                        Manage this app
                    </a>
                @endif
            </div>
        </div>

        <div class="sb-app-detail-v3__media">
            <img
                src="{{ $imageUrl($app->safeHeroImage()) }}"
                alt="{{ $app->name }} preview"
                decoding="async"
                onerror="this.onerror=null;this.src='{{ asset('assets/studybuddy-control/apps.svg') }}'"
            >
        </div>
    </section>

    <section class="sb-app-detail-v3__content">
        <article>
            <p class="sb-app-detail-v3__eyebrow">
                Who it is for
            </p>

            <h2>Designed for the right learning context.</h2>

            <div class="sb-app-detail-v3__roles">
                @foreach($roles as $audienceRole)
                    <span>
                        {{ $roleLabels[$audienceRole] ?? \Illuminate\Support\Str::headline($audienceRole) }}
                    </span>
                @endforeach
            </div>
        </article>

        <article>
            <p class="sb-app-detail-v3__eyebrow">
                Before starting
            </p>

            <h2>What to expect</h2>

            <p>
                {{
                    $app->preview_text
                    ?: 'Open the app, complete one focused session, and return to StudyBuddy when you are finished.'
                }}
            </p>

            @if($app->safety_note)
                <div class="sb-app-detail-v3__note">
                    {{ $app->safety_note }}
                </div>
            @endif
        </article>
    </section>

    @if($related->isNotEmpty())
        <section class="sb-app-detail-v3__related">
            <header>
                <p class="sb-app-detail-v3__eyebrow">
                    More in {{ $app->category ?: 'StudyBuddy' }}
                </p>

                <h2>Continue with another focused app.</h2>
            </header>

            <div>
                @foreach($related as $relatedApp)
                    <a href="{{ route('studybuddy.apps.show', $relatedApp->slug) }}">
                        <img
                            src="{{ $imageUrl($relatedApp->safeHeroImage()) }}"
                            alt=""
                            loading="lazy"
                            onerror="this.onerror=null;this.src='{{ asset('assets/studybuddy-control/apps.svg') }}'"
                        >

                        <span>
                            <strong>{{ $relatedApp->name }}</strong>
                            <small>
                                {{ $relatedApp->tagline ?: $relatedApp->category }}
                            </small>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
