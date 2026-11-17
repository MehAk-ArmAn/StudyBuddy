@extends('layouts.app')
@section('title', 'Math Sprint Results — StudyBuddy')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-w32/math-sprint.css') }}?v=32.0">
@endpush
@section('content')
<main class="sb32-page"><div class="sb32-shell sb32-shell--result">
    <div class="sb32-back"><a href="{{ route('studybuddy.w32.index') }}">← Math Sprint</a><span>✦ PRIVATE VERIFIED ANSWERS</span></div>
    <section class="sb32-panel sb32-result">
       <span class="sb32-result-orb" aria-hidden="true">{{ $record->score === 5 ? '💫' : '⭐' }}</span>
       <p class="sb32-kicker">SERVER-GRADED RESULT</p>
       <h1>{{ $record->score }} <small>/ {{ $record->total }}</small></h1>
       <h2>{{ $record->score === 5 ? 'A perfect orbit!' : ($record->score >= 4 ? 'You earned a Math Star!' : 'Every puzzle is practice.') }}</h2>
       <p>StudyBuddy compared your submitted answers with five answers held on the server. This shows correct answers for this challenge—not general skill mastery.</p>
       <div class="sb32-pillrow"><span>{{ ucfirst($record->level) }} Orbit</span><span>{{ \Illuminate\Support\Carbon::parse($record->submitted_at)->format('j M Y · g:i A') }}</span><span>🔒 Private to you</span></div>
       @if($record->score >= 4)
         <div class="sb32-earned">⭐ Math Star {{ $record->score === 5 ? ' · 💫 Perfect Orbit' : '' }} earned</div>
       @endif
       <div class="sb32-result-actions"><a class="sb32-button" href="{{ route('studybuddy.w32.index') }}">Try another sprint →</a><a href="{{ route('studybuddy.connected.insights') }}">Back to Learning Insights</a></div>
       <p class="sb32-caption">No cosmic points were credited. Results from external games are not verified by this feature.</p>
    </section>
</div></main>
@endsection
