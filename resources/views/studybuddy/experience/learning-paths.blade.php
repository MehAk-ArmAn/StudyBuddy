@extends('layouts.app')

@section('title', 'Learning Paths | StudyBuddy')

@section('content')
<main class="sbx-shell" data-sbx-page="learning-paths">
    @include('studybuddy.experience.partials.experience-nav')

    <section class="sbx-hero sbx-reveal">
        <div>
            <span class="sbx-kicker">Role-aware onboarding</span>
            <h1>Choose your StudyBuddy path</h1>
            <p>
                StudyBuddy should not feel the same for everyone. A learner needs motivation,
                a parent needs trust, a teacher needs clarity, and an independent learner needs control.
            </p>
        </div>
        <aside class="sbx-orbit-card">
            <span class="sbx-orbit-card__icon">🧭</span>
            <strong>Current paths</strong>
            <p>Student, Parent, Teacher, Independent Learner.</p>
        </aside>
    </section>

    <section class="sbx-role-picker sbx-reveal" data-sbx-role-picker>
        <div class="sbx-role-tabs">
            @foreach($roles as $role)
                <button type="button" data-sbx-role-tab="{{ $role['key'] }}" class="{{ $loop->first ? 'is-active' : '' }}">
                    <span>{{ $role['emoji'] }}</span> {{ $role['title'] }}
                </button>
            @endforeach
        </div>

        <div class="sbx-role-panels">
            @foreach($roles as $role)
                <article data-sbx-role-panel="{{ $role['key'] }}" class="{{ $loop->first ? 'is-active' : '' }}">
                    <span class="sbx-card__emoji">{{ $role['emoji'] }}</span>
                    <h2>{{ $role['title'] }} path</h2>
                    <p>{{ $role['summary'] }}</p>
                    <ul class="sbx-checklist">
                        <li>Personalized dashboard direction</li>
                        <li>Clear next-step guidance</li>
                        <li>Connected quests and platform content</li>
                        <li>Future app progress sync foundation</li>
                    </ul>
                    <a href="{{ route('register') }}" class="sbx-btn sbx-btn--primary">{{ $role['cta'] }}</a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="sbx-grid sbx-grid--2 sbx-reveal">
        <article class="sbx-card">
            <h2>What this phase completes</h2>
            <p>Content pages now explain the product clearly, so visitors understand the experience before app hosting/download systems are built.</p>
        </article>
        <article class="sbx-card">
            <h2>What comes later</h2>
            <p>Separate mini-apps can later connect to this platform through accounts, points, rewards, web play, downloads, and shared progress APIs.</p>
        </article>
    </section>
</main>
@endsection
