@extends('layouts.app')

@section('content')
<div class="sb-final-shell">
    <section class="sb-final-hero compact">
        <div>
            <p class="sb-final-kicker">Launch QA</p>
            <h1>Launch Readiness Center</h1>
            <p>Track what is complete and what still needs real app builds, store assets, security hardening, and QA.</p>
        </div>
        <div class="sb-final-orb-card"><span>✅</span><strong>{{ $score }}%</strong><p>{{ $done }}/{{ $total }} items done</p></div>
    </section>

    <section class="sb-final-panel">
        @foreach($checks as $item)
            <div class="sb-final-row">
                <div>
                    <strong>{{ $item->title }}</strong>
                    <span>{{ $item->area }} • {{ ucfirst($item->priority) }} • {{ $item->owner_label }}</span>
                    <p>{{ $item->description }}</p>
                </div>
                <b class="sb-final-status sb-status-{{ $item->status }}">{{ ucfirst($item->status) }}</b>
            </div>
        @endforeach
    </section>
</div>
@endsection
