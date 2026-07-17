@extends('layouts.admin')

@section('title', 'Control Room')

@section('content')
<section class="sb-control-dashboard pro-dashboard">
    <div class="sb-control-welcome pro-welcome">
        <div>
            <p class="sb-control-kicker">Welcome back</p>
            <h2>StudyBuddy command center</h2>
            <p>Manage public pages, navbar, footer, apps, users, profiles, role dashboards, parent tools, teacher tools, safety, and settings from one professional admin panel.</p>

            <div class="pro-welcome-actions">
                <a href="{{ url('/admin/control-room/shell') }}">Edit navbar/footer</a>
                <a href="{{ url('/admin/control-room/pages-legal') }}">Edit pages/legal</a>
                <a href="{{ url('/admin/control-room/account') }}">Change password</a>
            </div>
        </div>

        <img src="{{ asset('assets/studybuddy-control/logo.svg') }}" alt="">
    </div>

    <div class="sb-control-stat-grid pro-stat-grid">
        <article class="pink"><span>Total Users</span><strong>{{ number_format($stats['users'] ?? 0) }}</strong><small>All registered accounts</small></article>
        <article class="blue"><span>Pages</span><strong>{{ number_format($stats['pages'] ?? 0) }}</strong><small>DB-managed pages</small></article>
        <article class="cyan"><span>Apps</span><strong>{{ number_format($stats['apps'] ?? 0) }}</strong><small>Learning worlds</small></article>
        <article class="purple"><span>Assignments</span><strong>{{ number_format($stats['assignments'] ?? 0) }}</strong><small>Teacher tasks</small></article>
    </div>

    <div class="pro-role-strip">
        <article><span></span><strong>{{ number_format($stats['students'] ?? 0) }}</strong><small>Students</small></article>
        <article><span></span><strong>{{ number_format($stats['parents'] ?? 0) }}</strong><small>Parents</small></article>
        <article><span></span><strong>{{ number_format($stats['teachers'] ?? 0) }}</strong><small>Teachers</small></article>
        <article><span></span><strong>{{ number_format($stats['groups'] ?? 0) }}</strong><small>Groups/classes</small></article>
    </div>

    <div class="sb-control-section-head">
        <div>
            <p class="sb-control-kicker">Admin categories</p>
            <h2>Choose what to manage</h2>
        </div>
        <a href="{{ url('/') }}" target="_blank" rel="noopener">Preview website</a>
    </div>

    <div class="sb-control-card-grid pro-module-grid">
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
        <section class="sb-control-panel pro-panel">
            <div class="sb-control-panel-head">
                <h2>Coverage check</h2>
                <span>Admin-managed</span>
            </div>

            <ol class="sb-control-steps">
                <li><b>1</b><span>Navbar/footer: Website Shell + site_settings JSON.</span></li>
                <li><b>2</b><span>Pages/legal: Pages & Legal DB resources.</span></li>
                <li><b>3</b><span>Apps: Apps & Platform / Content Studio.</span></li>
                <li><b>4</b><span>Roles: Role Tools + Users & Roles.</span></li>
                <li><b>5</b><span>Password: Admin Account page.</span></li>
            </ol>
        </section>

        <section class="sb-control-panel pro-panel">
            <div class="sb-control-panel-head">
                <h2>Recommended workflow</h2>
                <span>Launch-ready</span>
            </div>

            <ol class="sb-control-steps">
                <li><b>1</b><span>Edit Website Shell.</span></li>
                <li><b>2</b><span>Review Pages & Legal.</span></li>
                <li><b>3</b><span>Finalize Apps & Platform.</span></li>
                <li><b>4</b><span>Check Users, Roles, Safety.</span></li>
                <li><b>5</b><span>Run health check and preview website.</span></li>
            </ol>
        </section>
    </div>
</section>
@endsection
