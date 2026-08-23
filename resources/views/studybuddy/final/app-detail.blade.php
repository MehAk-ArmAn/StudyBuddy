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

    $ready = $app->isAvailable();

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
    @if($adminPreview ?? false)
        <aside class="sb-admin-preview-banner" role="status">
            <strong>Private preview</strong>
            <span>This is how the app page will look when published. Only administrators can open this preview.</span>
            <a href="{{ route('admin.control-room.apps.edit', $app) }}">Back to editor</a>
        </aside>
    @endif

    <a
        class="sb-detail-back-v3"
        href="{{ ($adminPreview ?? false) ? route('admin.control-room.apps.edit', $app) : route('studybuddy.apps') }}"
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

        {{ ($adminPreview ?? false) ? 'Back to editor' : 'All apps' }}
    </a>

    <section class="sb-app-detail-v3__hero">
        <div class="sb-app-detail-v3__copy">
            <div class="sb-app-detail-v3__badges">
                <span>{{ $app->category ?: 'Learning' }}</span>

                <span class="{{ $ready ? 'is-ready' : '' }}">
                    {{ $app->availabilityLabel() }}
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
                        @elseif($app->age_max)
                            Up to {{ $app->age_max }}
                        @else
                            All ages
                        @endif
                    </dd>
                </div>
            </dl>

            <div class="sb-app-detail-v3__actions">
                {{-- Only real, configured destinations appear here. --}}
                @forelse($app->availableActions() as $action)
                    <a
                        class="sb-app-button {{ $action['primary'] ? 'sb-app-button--primary' : 'sb-app-button--secondary' }}"
                        href="{{ ($adminPreview ?? false) && $action['key'] === 'browser' ? route('admin.control-room.apps.preview.play', $app) : $action['url'] }}"
                        @if($action['key'] !== 'browser') target="_blank" rel="noopener" @endif
                    >
                        @if($action['key'] === 'browser')
                            <svg viewBox="0 0 24 24" width="17" height="17" fill="currentColor" aria-hidden="true">
                                <path d="M8 5.5v13l11-6.5z"></path>
                            </svg>
                        @endif
                        {{ $action['label'] }}
                    </a>
                @empty
                    <span class="sb-app-button sb-app-button--disabled" aria-disabled="true">
                        Coming soon
                    </span>
                @endforelse

                @if($app->support_url)
                    <a
                        class="sb-app-button sb-app-button--secondary"
                        href="{{ $app->support_url }}"
                        target="_blank"
                        rel="noopener"
                    >
                        App support
                    </a>
                @endif
            </div>

            @if(auth()->user()?->is_admin)
                {{-- Kept quiet on purpose: it must never read as a learner action. --}}
                <p class="sb-app-detail-v3__admin">
                    <a href="{{ route('admin.control-room.apps.edit', $app) }}">
                        Manage this app
                    </a>
                </p>
            @endif
        </div>

        <div class="sb-app-detail-v3__media">
            <img
                src="{{ $imageUrl($app->detailImage()) }}"
                alt="{{ $app->name }} preview"
                decoding="async"
                onerror="this.onerror=null;this.src='{{ asset('assets/studybuddy-control/apps.svg') }}'"
            >
        </div>
    </section>

    @if($app->long_description || filled($app->learning_outcomes) || filled($app->learning_tags))
        <section class="sb-app-detail-v3__learning">
            @if($app->long_description)
                <article>
                    <p class="sb-app-detail-v3__eyebrow">About this app</p>
                    <h2>What this one is</h2>
                    <p class="sb-app-detail-v3__long-copy">{{ $app->long_description }}</p>
                </article>
            @endif

            @if(filled($app->learning_outcomes))
                <article>
                    <p class="sb-app-detail-v3__eyebrow">Learning goals</p>
                    <h2>What learners practise</h2>
                    <ul>
                        @foreach($app->learning_outcomes as $outcome)
                            <li>{{ $outcome }}</li>
                        @endforeach
                    </ul>
                </article>
            @endif

            @if(filled($app->learning_tags))
                <article class="sb-app-detail-v3__skills-card">
                    <p class="sb-app-detail-v3__eyebrow">Skills &amp; topics</p>
                    <h2>Covered in this app</h2>

                    <div class="sb-app-detail-v3__skills">
                        @foreach($app->learning_tags as $tag)
                            <span>{{ $tag }}</span>
                        @endforeach
                    </div>
                </article>
            @endif
        </section>
    @endif

    <section class="sb-app-detail-v3__content">
        <article>
            <p class="sb-app-detail-v3__eyebrow">
                Who it is for
            </p>

            <h2>Who it suits</h2>

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

                <h2>More to try</h2>
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
