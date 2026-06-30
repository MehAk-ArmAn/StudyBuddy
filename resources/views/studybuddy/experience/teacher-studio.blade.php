@extends('layouts.app')

@section('title', 'Teacher Studio | StudyBuddy')

@section('content')
<main class="sbx-shell" data-sbx-page="teacher-studio">
    @include('studybuddy.experience.partials.experience-nav')

    <section class="sbx-hero sbx-reveal">
        <div>
            <span class="sbx-kicker">Classroom-friendly planning</span>
            <h1>Teacher Studio</h1>
            <p>
                A future-ready space for lesson missions, class activities, mini-app recommendations,
                and simple planning tools.
            </p>
            <div class="sbx-hero__actions">
                <a href="{{ route('register') }}" class="sbx-btn sbx-btn--primary">Create teacher account</a>
                <a href="{{ route('studybuddy.learning-hub') }}" class="sbx-btn sbx-btn--ghost">View learning hub</a>
            </div>
        </div>
        <aside class="sbx-orbit-card">
            <span class="sbx-orbit-card__icon">🎓</span>
            <strong>Teacher direction</strong>
            <p>Plan → assign → practice → review.</p>
        </aside>
    </section>

    <section class="sbx-panel sbx-reveal">
        <div class="sbx-section-head">
            <span class="sbx-kicker">Interactive lesson builder</span>
            <h2>Create a mini mission outline</h2>
            <p>This is frontend-only for now, but gives the product a real interactive classroom feel.</p>
        </div>

        <div class="sbx-lesson-builder" data-sbx-lesson-builder>
            <label>Topic <input type="text" value="Fractions" data-sbx-lesson-topic></label>
            <label>Time <select data-sbx-lesson-time><option>10 minutes</option><option>20 minutes</option><option>30 minutes</option></select></label>
            <label>Style <select data-sbx-lesson-style><option>Quest challenge</option><option>Group activity</option><option>Revision sprint</option></select></label>
            <button type="button" class="sbx-btn sbx-btn--primary" data-sbx-build-lesson>Build outline</button>
            <div class="sbx-lesson-builder__result" data-sbx-lesson-result>Build a class mission outline.</div>
        </div>
    </section>

    <section class="sbx-grid sbx-grid--3 sbx-reveal">
        <article class="sbx-card"><h2>Mission cards</h2><p>Create small learning goals that feel achievable.</p></article>
        <article class="sbx-card"><h2>Mini-app pairing</h2><p>Recommend the right mini-app for the lesson focus.</p></article>
        <article class="sbx-card"><h2>Future reports</h2><p>Later, progress can turn into class summaries and insights.</p></article>
    </section>
</main>
@endsection
