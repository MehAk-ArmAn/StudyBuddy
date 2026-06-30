@extends('layouts.app')

@section('title', 'Learning Hub | StudyBuddy')

@section('content')
<main class="sbx-shell" data-sbx-page="learning-hub">
    @include('studybuddy.experience.partials.experience-nav')

    <section class="sbx-hero sbx-reveal">
        <div>
            <span class="sbx-kicker">StudyBuddy Experience Layer</span>
            <h1>Learning Hub</h1>
            <p>
                A premium content home for learners to understand what to do next,
                how quests work, how points connect, and how StudyBuddy becomes one
                central command center for learning.
            </p>
            <div class="sbx-hero__actions">
                <a href="{{ route('studybuddy.learning-paths') }}" class="sbx-btn sbx-btn--primary">Choose a path</a>
                <a href="{{ route('studybuddy.rewards') }}" class="sbx-btn sbx-btn--ghost">Explore rewards</a>
            </div>
        </div>
        <aside class="sbx-orbit-card" aria-label="Today plan">
            <span class="sbx-orbit-card__icon">🌌</span>
            <strong>Today’s Flow</strong>
            <ol>
                <li>Pick a focus</li>
                <li>Save a quest</li>
                <li>Practice in a mini-app</li>
                <li>Return to dashboard</li>
            </ol>
        </aside>
    </section>

    <section class="sbx-grid sbx-grid--3 sbx-reveal">
        <article class="sbx-card">
            <span class="sbx-card__emoji">🎯</span>
            <h2>Focus</h2>
            <p>Choose one learning goal at a time so studying feels less chaotic and more winnable.</p>
        </article>
        <article class="sbx-card">
            <span class="sbx-card__emoji">⚡</span>
            <h2>Practice</h2>
            <p>Mini-apps turn practice into quick missions that can later sync points and progress.</p>
        </article>
        <article class="sbx-card">
            <span class="sbx-card__emoji">🏆</span>
            <h2>Progress</h2>
            <p>Saved quests, streaks, badges, and rewards give learners a reason to keep returning.</p>
        </article>
    </section>

    <section class="sbx-panel sbx-reveal">
        <div class="sbx-section-head">
            <span class="sbx-kicker">Interactive planner</span>
            <h2>Build a tiny study session</h2>
            <p>Pick your mood and focus. StudyBuddy will generate a small session plan in the browser.</p>
        </div>

        <div class="sbx-builder" data-sbx-session-builder>
            <label>
                Study mood
                <select data-sbx-builder-mood>
                    <option value="calm">Calm and focused</option>
                    <option value="rush">I only have 10 minutes</option>
                    <option value="lost">I feel lost</option>
                    <option value="challenge">I want a challenge</option>
                </select>
            </label>
            <label>
                Focus area
                <select data-sbx-builder-focus>
                    <option value="math">Math</option>
                    <option value="reading">Reading</option>
                    <option value="spelling">Spelling</option>
                    <option value="planning">Planning</option>
                    <option value="revision">Revision</option>
                </select>
            </label>
            <button type="button" class="sbx-btn sbx-btn--primary" data-sbx-build-session>Generate plan</button>
            <div class="sbx-builder__result" data-sbx-builder-result>
                Choose options and generate your mini study plan.
            </div>
        </div>
    </section>

    <section class="sbx-timeline sbx-reveal" aria-label="StudyBuddy learning loop">
        <div class="sbx-section-head">
            <span class="sbx-kicker">Learning loop</span>
            <h2>How StudyBuddy should feel</h2>
        </div>
        <div class="sbx-steps">
            <article><strong>1</strong><h3>Discover</h3><p>Browse apps, paths, and learning goals.</p></article>
            <article><strong>2</strong><h3>Save</h3><p>Add missions to My Quest.</p></article>
            <article><strong>3</strong><h3>Play</h3><p>Use mini-apps when downloads or web play are ready.</p></article>
            <article><strong>4</strong><h3>Return</h3><p>Dashboard collects progress, points, and next steps.</p></article>
        </div>
    </section>
</main>
@endsection
