@extends('layouts.admin')

@section('title', $title ?? 'Control Room Module')

@section('content')
<section class="sb-control-resource">
    <div class="sb-control-panel">
        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Control Room Module</p>
                <h2>{{ $title ?? 'Module' }}</h2>
                <p>{{ $subtitle ?? 'Manage this part of StudyBuddy from the control room.' }}</p>
            </div>
            <div class="sb-control-row-actions">
                <a href="{{ url('/admin/control-room') }}">Back to Control Room</a>
            </div>
        </div>

        <div class="sb-control-card-grid">
            @foreach(($cards ?? []) as $card)
                <a class="sb-control-feature blue" href="{{ $card['url'] ?? '#' }}">
                    <img src="{{ asset('assets/studybuddy-control/shell.svg') }}" alt="">
                    <span>{{ $card['title'] ?? 'Open' }}</span>
                    <p>{{ $card['body'] ?? '' }}</p>
                    <strong>Open →</strong>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
