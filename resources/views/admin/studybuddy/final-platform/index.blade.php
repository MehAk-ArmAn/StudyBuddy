@php
    use Illuminate\Support\Facades\Route;

    $appsUrl = url('/apps');
    $readinessUrl = Route::has('studybuddy.final.launch-readiness')
        ? route('studybuddy.final.launch-readiness')
        : url('/admin/control-room/final-platform');
@endphp

@extends('layouts.admin')

@section('title', 'Apps & Platform')

@section('content')
<section class="sb-control-resource">
    <div class="sb-control-panel">
        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Apps & Platform</p>
                <h2>Final Platform Cockpit</h2>
                <p>Control the app universe, app detail pages, web-play links, device downloads, points, launch checklist, and platform messages from one place.</p>
            </div>
            <div class="sb-control-row-actions">
                <a href="{{ $appsUrl }}" target="_blank" rel="noopener">View Apps Page</a>
                <a href="{{ $readinessUrl }}">View Readiness</a>
                <a href="{{ url('/admin/control-room') }}">Control Room</a>
            </div>
        </div>

        @if(session('status'))
            <div class="sb-control-alert">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="sb-control-alert" style="background:#fee2e2;color:#7f1d1d;">
                <strong>Check these fields:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="sb-control-stat-grid">
            <article class="purple"><span>Platform settings</span><strong>{{ isset($settings) ? count($settings) : 0 }}</strong><small>Editable records</small></article>
            <article class="blue"><span>Mini apps</span><strong>{{ isset($apps) ? count($apps) : 0 }}</strong><small>App records</small></article>
            <article class="cyan"><span>Checklist</span><strong>{{ isset($checklist) ? count($checklist) : 0 }}</strong><small>Launch items</small></article>
            <article class="pink"><span>Transactions</span><strong>{{ isset($transactions) ? count($transactions) : 0 }}</strong><small>Recent point logs</small></article>
        </div>

        <div class="sb-control-card-grid">
            <a class="sb-control-feature purple" href="{{ url('/admin/control-room/final-platform') }}">
                <img src="{{ asset('assets/studybuddy-control/apps.svg') }}" alt="">
                <span>App Universe</span>
                <p>Use this cockpit to manage app availability, links, and platform state.</p>
                <strong>Current page →</strong>
            </a>
            <a class="sb-control-feature blue" href="{{ url('/admin/control-room/shell') }}">
                <img src="{{ asset('assets/studybuddy-control/shell.svg') }}" alt="">
                <span>Website Shell</span>
                <p>Edit public app links in the navbar and footer.</p>
                <strong>Edit links →</strong>
            </a>
            <a class="sb-control-feature cyan" href="{{ $appsUrl }}" target="_blank" rel="noopener">
                <img src="{{ asset('assets/studybuddy-control/dashboard.svg') }}" alt="">
                <span>Public Apps Page</span>
                <p>Preview what learners and families see.</p>
                <strong>Preview →</strong>
            </a>
        </div>

        <div class="sb-control-bottom-grid">
            <section class="sb-control-panel">
                <div class="sb-control-panel-head">
                    <div>
                        <h2>Mini apps</h2>
                        <p>Current app records found by the platform controller.</p>
                    </div>
                </div>

                <div class="sb-control-table-wrap">
                    <table class="sb-control-table">
                        <thead>
                            <tr><th>Name</th><th>Status</th><th>Featured</th><th>Points</th></tr>
                        </thead>
                        <tbody>
                            @forelse(($apps ?? []) as $app)
                                <tr>
                                    <td>{{ $app->name ?? $app->title ?? 'App' }}</td>
                                    <td>{{ $app->status ?? 'active' }}</td>
                                    <td>{{ !empty($app->is_featured) ? 'Yes' : 'No' }}</td>
                                    <td>{{ $app->points_reward ?? $app->points ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><div class="sb-control-empty">No app records yet.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="sb-control-panel">
                <div class="sb-control-panel-head">
                    <div>
                        <h2>Launch checklist</h2>
                        <p>Current launch/readiness items.</p>
                    </div>
                </div>

                <ol class="sb-control-steps">
                    @forelse(($checklist ?? []) as $item)
                        <li><b>{{ $loop->iteration }}</b><span>{{ $item->title ?? $item->name ?? 'Checklist item' }}</span></li>
                    @empty
                        <li><b>1</b><span>No checklist items yet.</span></li>
                    @endforelse
                </ol>
            </section>
        </div>
    </div>
</section>
@endsection
