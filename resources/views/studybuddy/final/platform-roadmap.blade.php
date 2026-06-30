@extends('layouts.app')

@section('content')
<main class="sb-final-shell">
    <section class="sb-final-hero compact">
        <div>
            <p class="sb-final-kicker">Smart Product Plan</p>
            <h1>StudyBuddy Platform Roadmap</h1>
            <p>This roadmap keeps the main dashboard, mini-apps, points, web play, and store downloads connected without hardcoding future app links.</p>
        </div>
    </section>

    <section class="sb-final-timeline">
        <article><span>01</span><h2>Main dashboard</h2><p>Command Center, My Quest, content pages, and theme system.</p></article>
        <article><span>02</span><h2>Admin editable content</h2><p>Content Studio controls platform pages and app ecosystem copy.</p></article>
        <article><span>03</span><h2>App launchpad</h2><p>Each mini-app has web, iOS, Android, Windows, and Mac slots.</p></article>
        <article><span>04</span><h2>Points system</h2><p>Apps can post completed sessions back to StudyBuddy.</p></article>
        <article><span>05</span><h2>Final distribution</h2><p>Real app builds and web hosted games get linked from admin.</p></article>
    </section>
</main>
@endsection
