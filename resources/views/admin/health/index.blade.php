@extends('layouts.admin')

@section('title', 'Health Check')

@section('content')
<section class="sb-admin-health-page">
    <div class="sb-control-panel health-hero">
        <div>
            <p class="sb-control-kicker">Launch health</p>
            <h2>StudyBuddy system check</h2>
            <p>This page checks the database, routes, writable storage, and the web-app publishing engine without exposing private configuration values.</p>
        </div>
        <div class="health-summary" aria-label="Health check summary">
            <article class="pass"><strong>{{ $summary['pass'] }}</strong><span>Passed</span></article>
            <article class="warn"><strong>{{ $summary['warn'] }}</strong><span>Warnings</span></article>
            <article class="fail"><strong>{{ $summary['fail'] }}</strong><span>Failed</span></article>
        </div>
    </div>

    @foreach($checks->groupBy('group') as $group => $groupChecks)
        <section class="sb-control-panel health-group">
            <div class="sb-control-panel-head wide">
                <div><p class="sb-control-kicker">{{ $group }}</p><h2>{{ $group }} checks</h2></div>
                <a href="{{ url('/admin/control-room/health') }}">Run again</a>
            </div>

            <div class="health-check-list">
                @foreach($groupChecks as $check)
                    <article class="health-check is-{{ $check['status'] }}">
                        <span aria-hidden="true">{{ $check['status'] === 'pass' ? '' : ($check['status'] === 'warn' ? '!' : '×') }}</span>
                        <div>
                            <strong>{{ $check['label'] }}</strong>
                            <p>{{ $check['detail'] }}</p>
                        </div>
                        <em>{{ strtoupper($check['status']) }}</em>
                    </article>
                @endforeach
            </div>
        </section>
    @endforeach
</section>
@endsection
