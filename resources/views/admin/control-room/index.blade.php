@extends('layouts.admin')

@section('title', 'Control Room')

@section('content')
<section class="sb-control-dashboard">
    <div class="sb-control-welcome">
        <div>
            <p class="sb-control-kicker">Welcome back</p>
            <h2>Complete website command center</h2>
            <p>Manage StudyBuddy’s shell, content, apps, users, safety, and platform settings from one organized control room.</p>
        </div>
        <img src="{{ asset('assets/studybuddy-control/logo.svg') }}" alt="">
    </div>

    <div class="sb-control-stat-grid">
        <article class="pink"><span>Total Users</span><strong>{{ number_format($stats['users'] ?? 0) }}</strong><small>Registered accounts</small></article>
        <article class="blue"><span>Settings</span><strong>{{ number_format($stats['settings'] ?? 0) }}</strong><small>Editable keys</small></article>
        <article class="cyan"><span>Apps</span><strong>{{ number_format($stats['apps'] ?? 0) }}</strong><small>Mini app records</small></article>
        <article class="purple"><span>Quests</span><strong>{{ number_format($stats['quests'] ?? 0) }}</strong><small>Saved missions</small></article>
    </div>

    <div class="sb-control-section-head">
        <div><p class="sb-control-kicker">Admin Categories</p><h2>Choose what to manage</h2></div>
        <a href="{{ url('/admin/control-room/shell') }}">Edit navbar/footer</a>
    </div>

    <div class="sb-control-card-grid">
        @foreach(($sections ?? []) as $section)
            <a class="sb-control-feature {{ $section['class'] }}" href="{{ $section['url'] }}">
                <img src="{{ $section['icon'] }}" alt="">
                <span>{{ $section['title'] }}</span>
                <p>{{ $section['subtitle'] }}</p>
                <strong>{{ $section['cta'] }} →</strong>
            </a>
        @endforeach
    </div>

    <div class="sb-control-bottom-grid">
        <section class="sb-control-panel">
            <div class="sb-control-panel-head"><h2>Website health</h2><span>Live overview</span></div>
            <div class="sb-control-bars"><i style="height:44%"></i><i style="height:72%"></i><i style="height:52%"></i><i style="height:88%"></i><i style="height:62%"></i><i style="height:78%"></i><i style="height:92%"></i></div>
        </section>

        <section class="sb-control-panel">
            <div class="sb-control-panel-head"><h2>Quick workflow</h2><span>Recommended</span></div>
            <ol class="sb-control-steps">
                <li><b>1</b><span>Edit Website Shell for navbar/footer.</span></li>
                <li><b>2</b><span>Update Content Studio for public pages.</span></li>
                <li><b>3</b><span>Review Apps & Platform before publishing.</span></li>
                <li><b>4</b><span>Preview website and test key links.</span></li>
            </ol>
        </section>
    </div>
</section>
@endsection
