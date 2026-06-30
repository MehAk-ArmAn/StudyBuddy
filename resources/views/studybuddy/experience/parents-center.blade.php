@extends('layouts.app')

@section('title', 'Parents Center | StudyBuddy')

@section('content')
<main class="sbx-shell" data-sbx-page="parents-center">
    @include('studybuddy.experience.partials.experience-nav')

    <section class="sbx-hero sbx-reveal">
        <div>
            <span class="sbx-kicker">Parent trust layer</span>
            <h1>Parents Center</h1>
            <p>
                A clean space explaining how StudyBuddy supports safer learning,
                clearer routines, and less stressful study habits at home.
            </p>
            <div class="sbx-hero__actions">
                <a href="{{ route('register') }}" class="sbx-btn sbx-btn--primary">Create parent account</a>
                <a href="{{ route('studybuddy.safety-support') }}" class="sbx-btn sbx-btn--ghost">Safety guide</a>
            </div>
        </div>
        <aside class="sbx-orbit-card">
            <span class="sbx-orbit-card__icon">💜</span>
            <strong>Parent promise</strong>
            <p>Simple guidance, safe structure, and child-friendly motivation.</p>
        </aside>
    </section>

    <section class="sbx-grid sbx-grid--3 sbx-reveal">
        <article class="sbx-card"><h2>Know the routine</h2><p>See what the learner is trying to practice and what the next goal should be.</p></article>
        <article class="sbx-card"><h2>Support without pressure</h2><p>Use small missions, streaks, and rewards to make progress feel positive.</p></article>
        <article class="sbx-card"><h2>Prepare for app sync</h2><p>Future apps can feed progress into one family-friendly dashboard.</p></article>
    </section>

    <section class="sbx-panel sbx-reveal">
        <div class="sbx-section-head">
            <span class="sbx-kicker">Home routine</span>
            <h2>Suggested weekly learning rhythm</h2>
        </div>
        <div class="sbx-week">
            <button type="button">Mon <span>Math Quest</span></button>
            <button type="button">Tue <span>Reading Garden</span></button>
            <button type="button">Wed <span>Focus Forest</span></button>
            <button type="button">Thu <span>Quiz Galaxy</span></button>
            <button type="button">Fri <span>Reward review</span></button>
        </div>
    </section>
</main>
@endsection
