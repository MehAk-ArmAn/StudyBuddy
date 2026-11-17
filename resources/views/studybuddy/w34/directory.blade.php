@extends('layouts.app')
@section('title', 'Discover learning subjects')
@section('content')
<div class="sb34-directory" id="sb34-subjects">
  <section class="sb34-hero" aria-labelledby="sb34-title"><div class="sb34-orbit" aria-hidden="true"><span>✦</span><span>✧</span><span>✦</span></div>
    <span class="sb34-eyebrow">EXPLORE · DISCOVER · PLAY</span>
    <h1 id="sb34-title">Find your next <em>learning adventure.</em></h1>
    <p>Browse StudyBuddy games by what they actually teach. No empty subjects or made-up achievements.</p>
    <form class="sb34-search" method="GET" action="{{ $selected ? route('studybuddy.w34.subjects.show', $selected) : route('studybuddy.w34.subjects.index') }}" role="search">
      <label><span>Search games & topics</span><input type="search" name="q" maxlength="80" value="{{ $query }}" placeholder="Try multiplication, flags, spelling…" autocomplete="off"></label>
      <label><span>Learning stage</span><select name="stage"><option value="">All stages</option>
        @foreach(['early-years' => 'Early Years', 'primary' => 'Primary', 'secondary' => 'Secondary', 'all-ages' => 'All ages'] as $slug => $title)
        <option value="{{ $slug }}" @selected($stage === $slug)>{{ $title }}</option>@endforeach
      </select></label>
      <button type="submit">Explore <span aria-hidden="true">↗</span></button>
    </form>
  </section>
  <section class="sb34-section" aria-labelledby="sb34-categories"><div class="sb34-section-heading"><div><p class="sb34-eyebrow">SUBJECT ATLAS</p><h2 id="sb34-categories">Pick a subject</h2></div><p>{{ $cards->count() }} subjects with real games</p></div>
    @if($cards->isEmpty())<div class="sb34-empty">Our subject atlas is being prepared. The team will publish categories as real games are classified.</div>
    @else
      <div class="sb34-grid">
        @foreach($cards as $card)
          <a href="{{ route('studybuddy.w34.subjects.show', $card['slug']) }}" class="sb34-tile @if($selected === $card['slug']) is-active @endif">
            <span class="sb34-icon" aria-hidden="true">{{ ['Mathematics'=>'∑','Geography'=>'🌍','English'=>'✍','Science'=>'⚗','Music'=>'♫','Computing'=>'⌘','Visual Arts'=>'✦'][$card['label']] ?? '✧' }}</span>
            <span class="sb34-tile-label">{{ $card['label'] }}</span>
            <span class="sb34-tile-description">{{ $card['summary'] }}</span>
            <span class="sb34-tile-count">{{ $card['count'] }} {{ $card['count'] === 1 ? 'game' : 'games' }} <span aria-hidden="true">↗</span></span>
          </a>
        @endforeach
      </div>
    @endif
  </section>
  <section class="sb34-section" aria-labelledby="sb34-results"><div class="sb34-section-heading"><div><p class="sb34-eyebrow">READY TO EXPLORE</p><h2 id="sb34-results">{{ $selected ? $subjectData[$selected]['label'].' games' : 'All classified games' }}</h2></div>
    <a class="sb34-clear" href="{{ route('studybuddy.w34.subjects.index') }}">Clear filters</a></div>
    @if($results->isEmpty())<div class="sb34-empty">No published games match these filters yet. Try another subject or a shorter search.</div>
    @else
    <div class="sb34-results">
      @foreach($results as $result)
        <article class="sb34-game"><div><span class="sb34-game-subject">{{ $result['subject'] }}@if($result['tag']->learning_stage) · {{ str_replace('-', ' ', ucfirst($result['tag']->learning_stage)) }}@endif</span>
          <h3>{{ $result['app']->name }}</h3>
          @if($result['tag']->learning_outcome)<p>{{ $result['tag']->learning_outcome }}</p>@endif
          @if($result['tag']->topics)<p class="sb34-topics">{{ $result['tag']->topics }}</p>@endif
        </div><a class="sb34-game-action" href="{{ route('studybuddy.apps.show', $result['app']->slug) }}">View game <span aria-hidden="true">→</span></a></article>
      @endforeach
    </div>
    @endif
  </section>
  <p class="sb34-note">StudyBuddy subject labels are editorial classifications, not official curriculum certificates. Learning outcomes describe the game, not a promise of individual performance.</p>
</div>
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-w34/subjects.css') }}?v=34.1">
@endpush
