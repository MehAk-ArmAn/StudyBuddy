@extends('layouts.app')
@section('title', 'Math Sprint — StudyBuddy')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-w32/math-sprint.css') }}?v=32.0">
@endpush
@section('content')
<main class="sb32-page">
    <div class="sb32-shell">
        <div class="sb32-back"><a href="{{ route('studybuddy.connected.insights') }}">← Learning Insights</a> <span>✦ FIRST-PARTY LEARNING GAME</span></div>
        <header class="sb32-hero">
            <div>
                <p class="sb32-kicker">STUDYBUDDY • MATH UNIVERSE</p>
                <h1>Math Sprint <span>✷</span></h1>
                <p>Five little number puzzles. One bright learning adventure. Your answers are checked by StudyBuddy's server, not claimed by the browser.</p>
                <div class="sb32-pillrow"><span>🔒 Private progress</span><span>🧠 Server-graded answers</span><span>✨ Earnable badges</span></div>
            </div>
            @if(file_exists(public_path('assets/studybuddy-w26/images/dolphin-math.webp')))
              <img class="sb32-mascot" src="{{ asset('assets/studybuddy-w26/images/dolphin-math.webp') }}" alt="StudyBuddy dolphin learning math" decoding="async" loading="eager">
            @else
              <div class="sb32-mascot-fallback" aria-hidden="true">✦<small>5/5</small></div>
            @endif
        </header>
        @if($errors->any())<div class="sb32-notice sb32-notice--error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        @if(session('status'))<div class="sb32-notice" role="status">{{ session('status') }}</div>@endif
        @if($run)
          <section class="sb32-panel" aria-labelledby="sb32-question-title">
            <p class="sb32-kicker">YOUR CHALLENGE IS LIVE</p>
            <h2 id="sb32-question-title">{{ $run->level === 'explorer' ? 'Explorer Orbit' : 'Starter Orbit' }} • 5 puzzles</h2>
            <p class="sb32-caption">Answer all five questions. This challenge expires 30 minutes after you started it. Your answers are graded only when you submit.</p>
            <form method="post" action="{{ route('studybuddy.w32.submit', $run->id) }}" class="sb32-questions">@csrf
                @foreach($questions as $index => $question)
                <label class="sb32-question" for="sb32-answer-{{ $index }}">
                    <span class="sb32-qnumber">{{ sprintf('%02d', $index + 1) }}</span>
                    <span class="sb32-prompt">{{ $question['prompt'] }}</span>
                    <input id="sb32-answer-{{ $index }}" type="number" inputmode="numeric" name="answers[{{ $index }}]" min="-1000" max="1000" required autocomplete="off" placeholder="Your answer" aria-label="Answer question {{ $index + 1 }}">
                </label>
                @endforeach
                <div class="sb32-actions"><button class="sb32-button" type="submit">Check my answers ✨</button><span>Scores and badges are calculated on the server. No points are added.</span></div>
            </form>
          </section>
        @else
          <section class="sb32-panel" aria-labelledby="sb32-start-title">
              <p class="sb32-kicker">CHOOSE YOUR ORBIT</p><h2 id="sb32-start-title">Ready, curious mind?</h2>
              <div class="sb32-levels">
                <form method="post" action="{{ route('studybuddy.w32.start') }}">@csrf<input type="hidden" name="level" value="starter"><div class="sb32-level-art" aria-hidden="true">🪐</div><h3>Starter Orbit</h3><p>Gentle addition and subtraction with small numbers.</p><button type="submit" class="sb32-button">Start Starter →</button></form>
                <form method="post" action="{{ route('studybuddy.w32.start') }}">@csrf<input type="hidden" name="level" value="explorer"><div class="sb32-level-art" aria-hidden="true">🌌</div><h3>Explorer Orbit</h3><p>Bigger numbers for the adventurous problem solver.</p><button type="submit" class="sb32-button sb32-button--alt">Start Explorer →</button></form>
              </div>
          </section>
        @endif
        <section class="sb32-panel" aria-labelledby="sb32-achievements-title">
            <div class="sb32-sectiontop"><div><p class="sb32-kicker">YOUR PERSONAL TROPHY CASE</p><h2 id="sb32-achievements-title">Earned with real answers</h2></div><a href="{{ route('studybuddy.learning.activity') }}">Activity journal ↗</a></div>
            <div class="sb32-badges">
                <article class="{{ in_array('math-first-pass',$achievements,true) ? 'sb32-badge--earned' : '' }}"><span>⭐</span><strong>Math Star</strong><p>{{ in_array('math-first-pass',$achievements,true) ? 'Earned!' : 'Get at least 4/5 correct to earn' }}</p></article>
                <article class="{{ in_array('math-perfect-five',$achievements,true) ? 'sb32-badge--earned' : '' }}"><span>💫</span><strong>Perfect Orbit</strong><p>{{ in_array('math-perfect-five',$achievements,true) ? 'Earned!' : 'Get all five answers right to earn' }}</p></article>
            </div>
            <p class="sb32-caption">These badges represent server-graded Math Sprint answers. They are not certificates of mastery, grades from other games, or spendable points.</p>
        </section>
        @if($recent->isNotEmpty())
          <section class="sb32-panel" aria-labelledby="sb32-recent-title"><p class="sb32-kicker">RECENT SPRINTS</p><h2 id="sb32-recent-title">Your latest results</h2><div class="sb32-history">
            @foreach($recent as $past)<a href="{{ route('studybuddy.w32.result',$past->id) }}"><strong>{{ $past->score }}/{{ $past->total }}</strong><span>{{ ucfirst($past->level) }} Orbit · {{ \Illuminate\Support\Carbon::parse($past->submitted_at)->format('j M Y') }}</span><span>View →</span></a>@endforeach
          </div></section>
        @endif
    </div>
</main>
@endsection
