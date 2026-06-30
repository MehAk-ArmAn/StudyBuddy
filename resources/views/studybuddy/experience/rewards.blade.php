@extends('layouts.app')

@section('title', 'Rewards & Points | StudyBuddy')

@section('content')
<main class="sbx-shell" data-sbx-page="rewards">
    @include('studybuddy.experience.partials.experience-nav')

    <section class="sbx-hero sbx-reveal">
        <div>
            <span class="sbx-kicker">Motivation system</span>
            <h1>Points, streaks, badges, and quests</h1>
            <p>
                This page explains the reward logic that can later connect all StudyBuddy mini-apps
                into one dashboard.
            </p>
        </div>
        <aside class="sbx-orbit-card">
            <span class="sbx-orbit-card__icon">🏆</span>
            <strong>Reward idea</strong>
            <p>Finish missions → earn points → unlock badges → build streaks.</p>
        </aside>
    </section>

    <section class="sbx-grid sbx-grid--4 sbx-reveal">
        <article class="sbx-stat"><strong data-sbx-count="120">0</strong><span>Quest Points</span></article>
        <article class="sbx-stat"><strong data-sbx-count="7">0</strong><span>Day Streak</span></article>
        <article class="sbx-stat"><strong data-sbx-count="8">0</strong><span>Mini Apps</span></article>
        <article class="sbx-stat"><strong data-sbx-count="4">0</strong><span>Badges Ready</span></article>
    </section>

    <section class="sbx-panel sbx-reveal">
        <div class="sbx-section-head">
            <span class="sbx-kicker">Interactive points simulator</span>
            <h2>Estimate reward points</h2>
            <p>Use this as content/UI foundation. Real points can later come from the database and apps.</p>
        </div>

        <div class="sbx-points-lab" data-sbx-points-lab>
            <label>Mini-app missions completed <input type="number" min="0" max="50" value="3" data-sbx-points-missions></label>
            <label>Focus sessions completed <input type="number" min="0" max="50" value="2" data-sbx-points-focus></label>
            <label>Reading tasks completed <input type="number" min="0" max="50" value="1" data-sbx-points-reading></label>
            <button type="button" class="sbx-btn sbx-btn--primary" data-sbx-calc-points>Calculate</button>
            <div class="sbx-points-lab__result" data-sbx-points-result>Estimated points will appear here.</div>
        </div>
    </section>

    <section class="sbx-badges sbx-reveal">
        <article><span>🌟</span><h3>First Quest</h3><p>Save your first mission.</p></article>
        <article><span>🔥</span><h3>Streak Starter</h3><p>Return for three learning days.</p></article>
        <article><span>📚</span><h3>Reading Ranger</h3><p>Complete reading practice.</p></article>
        <article><span>🧠</span><h3>Quiz Galaxy</h3><p>Win a review challenge.</p></article>
    </section>
</main>
@endsection
