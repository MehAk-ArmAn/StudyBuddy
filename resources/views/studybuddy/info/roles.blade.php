@extends('layouts.app')

@section('title', 'Roles')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-roles-clean.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-roles-clean.css')) ? filemtime(public_path('assets/css/studybuddy-roles-clean.css')) : time() }}">
@endpush

@section('content')
@php
    $roleImages = [
        'student' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-apps.png',
        'parent' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-parents.png',
        'teacher' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-teachers.png',
        'independent_learner' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-learning.png',
        'independent' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-learning.png',
    ];
@endphp

<div class="sb-roles-clean-page">
    <section class="sb-roles-clean-hero" aria-labelledby="roles-title">
        <div>
            <p class="sb-roles-eyebrow">{{ $pageData['eyebrow'] ?? 'StudyBuddy roles' }}</p>
            <h1 id="roles-title">{{ $pageData['title'] ?? 'Choose the StudyBuddy experience that fits you.' }}</h1>
            <p>{{ $pageData['subtitle'] ?? 'Students, parents, teachers, and independent learners each get tools made for their needs.' }}</p>
        </div>
        <div class="sb-roles-hero-actions">
            <a class="sb-role-primary" href="{{ route('register') }}">Create account</a>
            <a class="sb-role-secondary" href="{{ route('studybuddy.apps') }}">Explore apps</a>
        </div>
    </section>

    <section class="sb-role-card-grid" aria-label="StudyBuddy role options">
        @foreach($roleCards as $role)
            @php
                $key = $role['key'] ?? \Illuminate\Support\Str::slug($role['title'] ?? 'role');
                $roleImage = $roleImages[$key] ?? null;
                $ctaUrl = $role['cta_url'] ?? '/apps';
            @endphp

            <article class="sb-role-clean-card">
                <div class="sb-role-card-visual">
                    @if($roleImage)
                        <img src="{{ $roleImage }}" alt="" loading="lazy">
                    @else
                        <span aria-hidden="true">{{ $role['icon'] ?? '✦' }}</span>
                    @endif
                </div>

                <div class="sb-role-card-content">
                    <div class="sb-role-card-title">
                        <span aria-hidden="true">{{ $role['icon'] ?? '✦' }}</span>
                        <h2>{{ $role['title'] ?? 'StudyBuddy role' }}</h2>
                    </div>

                    <p class="sb-role-tagline">{{ $role['tagline'] ?? '' }}</p>

                    <dl class="sb-role-details">
                        @if(!empty($role['best_for']))
                            <div><dt>Best for</dt><dd>{{ $role['best_for'] }}</dd></div>
                        @endif
                        @if(!empty($role['dashboard']))
                            <div><dt>Dashboard</dt><dd>{{ $role['dashboard'] }}</dd></div>
                        @endif
                        @if(!empty($role['controls']))
                            <div><dt>Your controls</dt><dd>{{ $role['controls'] }}</dd></div>
                        @endif
                        @if(!empty($role['safety']))
                            <div><dt>Safety</dt><dd>{{ $role['safety'] }}</dd></div>
                        @endif
                    </dl>

                    <a class="sb-role-card-link" href="{{ url($ctaUrl) }}">{{ $role['cta_label'] ?? 'Learn more' }} <span aria-hidden="true">→</span></a>
                </div>
            </article>
        @endforeach
    </section>

    <section class="sb-roles-clean-explainer">
        <div>
            <p class="sb-roles-eyebrow">Simple and role-focused</p>
            <h2>Everyone sees the tools they actually need.</h2>
            <p>StudyBuddy keeps learning tools clear instead of filling every dashboard with unrelated controls.</p>
        </div>

        <ol>
            <li><span>1</span><div><strong>Choose your role</strong><p>Select the experience that matches how you use StudyBuddy.</p></div></li>
            <li><span>2</span><div><strong>Set your preferences</strong><p>Add your goals, interests, and privacy choices.</p></div></li>
            <li><span>3</span><div><strong>Use your dashboard</strong><p>Open the apps, support tools, or classroom features made for you.</p></div></li>
        </ol>
    </section>

    <section class="sb-roles-clean-safety">
        <div>
            <p class="sb-roles-eyebrow">Consent-first connections</p>
            <h2>Parent and teacher links stay controlled.</h2>
            <p>Learners use their current StudyBuddy Connect Code to approve account connections. Password sharing is never needed.</p>
        </div>
        <a class="sb-role-secondary" href="{{ url('/community-guidelines') }}">Read safety guidance</a>
    </section>
</div>
@endsection

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('assets/css/studybuddy-apps-roles-color.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-apps-roles-color.css')) ? filemtime(public_path('assets/css/studybuddy-apps-roles-color.css')) : time() }}"
>
@endpush

@push('scripts')
<script
    src="{{ asset('assets/js/studybuddy-apps-roles-color.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-apps-roles-color.js')) ? filemtime(public_path('assets/js/studybuddy-apps-roles-color.js')) : time() }}"
    defer
></script>
@endpush
