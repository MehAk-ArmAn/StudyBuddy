@extends('layouts.app')

@section('title', 'My Quest · StudyBuddy')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-w16/quests.css') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/studybuddy-w16/quests.css') }}">
@endpush

@push('scripts')
<script defer src="{{ asset('assets/studybuddy-w16/quests.js') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/studybuddy-w16/quests.js') }}"></script>
@endpush

@section('content')
@php
    $total = (int) ($stats['total'] ?? 0);
    $saved = (int) ($stats['saved'] ?? 0);
    $active = (int) ($stats['in_progress'] ?? 0);
    $done = (int) ($stats['completed'] ?? 0);
    $completion = $total ? (int) round($done * 100 / $total) : 0;
@endphp
<div class="sb16" data-sb16-quests data-sb16-errors="{{ $errors->any() ? '1' : '0' }}">
  <header class="sb16-hero">
    <div class="sb16-hero__ambient" aria-hidden="true"></div>
    <div class="sb16-hero__copy">
      <span class="sb16-kicker"><span class="sb16-star" aria-hidden="true">✦</span> YOUR PRIVATE LEARNING SPACE</span>
      <h1>Every little step <em>adds up.</em></h1>
      <p>Choose a goal, enjoy the process, and make progress at your own pace. These missions belong to your account—not someone else's dashboard.</p>
      <div class="sb16-actions"><button type="button" class="sb16-btn sb16-btn--main" data-sb16-open>+ Create a quest</button><a class="sb16-btn sb16-btn--soft" href="{{ route('studybuddy.apps') }}">Explore learning worlds <span aria-hidden="true">↗</span></a></div>
    </div>
    <div class="sb16-hero__visual" aria-hidden="true">
      <div class="sb16-orbit sb16-orbit--one"></div><div class="sb16-orbit sb16-orbit--two"></div>
      <img src="{{ asset('assets/studybuddy-brand/pages/hero-dolphin-book.webp') }}" alt="" loading="eager" decoding="async">
      <span class="sb16-float sb16-float--one">✦</span><span class="sb16-float sb16-float--two">✧</span>
    </div>
  </header>

  @if(session('status'))<p class="sb16-notice" role="status">{{ session('status') }}</p>@endif
  @if($errors->any())<div class="sb16-error" role="alert"><strong>Couldn't save that quest.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

  <section class="sb16-summary" aria-label="Your real quest totals">
    <div><span>YOUR MISSIONS</span><strong>{{ $total }}</strong><small>All quests saved to your account</small></div>
    <div><span>IN MOTION</span><strong>{{ $active }}</strong><small>Quests you're currently working on</small></div>
    <div><span>COMPLETED</span><strong>{{ $done }}</strong><small>Your finished quests</small></div>
    <div class="sb16-summary__ring" style="--value: {{ $completion }}%;"><span>{{ $completion }}%</span><small>of all quests complete</small></div>
  </section>

  <section class="sb16-create" id="sb16-create" data-sb16-composer hidden aria-labelledby="sb16-create-title">
    <div class="sb16-create__head"><div><span class="sb16-kicker">A NEW ADVENTURE</span><h2 id="sb16-create-title">What will you learn next?</h2></div><button type="button" class="sb16-dismiss" data-sb16-close aria-label="Close create quest form">×</button></div>
    <form action="{{ route('studybuddy.quests.store') }}" method="POST" class="sb16-create__form">
      @csrf
      <label class="sb16-field sb16-field--wide"><span>Mission title <b>*</b></span><input name="mission_title" value="{{ old('mission_title') }}" required maxlength="220" placeholder="e.g. Practise fractions for 15 minutes" autocomplete="off" data-sb16-title></label>
      <label class="sb16-field"><span>Learning world (optional)</span><input name="app_title" value="{{ old('app_title') }}" maxlength="100" placeholder="e.g. Number Odyssey"></label>
      <label class="sb16-field"><span>Minutes (optional)</span><input type="number" name="estimated_minutes" value="{{ old('estimated_minutes') }}" min="1" max="1440" placeholder="15"></label>
      <label class="sb16-field"><span>Difficulty (optional)</span><select name="difficulty"><option value="">Choose your pace</option>@foreach(['Easy','Medium','Challenge'] as $difficulty)<option value="{{ $difficulty }}" @selected(old('difficulty') === $difficulty)>{{ $difficulty }}</option>@endforeach</select></label>
      <label class="sb16-field sb16-field--wide"><span>A little more detail (optional)</span><textarea name="mission_description" rows="3" maxlength="3000" placeholder="What would you like to explore or achieve?">{{ old('mission_description') }}</textarea></label>
      <div class="sb16-form__footer"><p>This saves a real mission to your StudyBuddy account.</p><button type="submit" class="sb16-btn sb16-btn--main">Save my quest <span aria-hidden="true">↗</span></button></div>
    </form>
  </section>

  <section class="sb16-list" aria-labelledby="sb16-list-heading">
    <div class="sb16-list__heading"><div><span class="sb16-kicker">YOUR PERSONAL COLLECTION</span><h2 id="sb16-list-heading">Your learning <em>journey.</em></h2></div><p>Filter the {{ $quests->count() }} {{ \Illuminate\Support\Str::plural('mission', $quests->count()) }} shown on this page.</p></div>
    @if($quests->count())
      <div class="sb16-filterbar" aria-label="Filter quests on this page">
        <label class="sb16-search"><span class="sb16-visually-hidden">Search the quests on this page</span><input type="search" data-sb16-search placeholder="Search your quests…" autocomplete="off"></label>
        <div class="sb16-filterbar__chips" role="group" aria-label="Quest status">
          <button type="button" data-sb16-filter="all" aria-pressed="true">All</button>
          <button type="button" data-sb16-filter="saved" aria-pressed="false">Saved</button>
          <button type="button" data-sb16-filter="in_progress" aria-pressed="false">In progress</button>
          <button type="button" data-sb16-filter="completed" aria-pressed="false">Completed</button>
          <button type="button" data-sb16-filter="archived" aria-pressed="false">Archived</button>
        </div>
      </div>
      <p class="sb16-filter-count" aria-live="polite" data-sb16-count></p>
      <div class="sb16-quest-grid">
      @foreach($quests as $quest)
        @php $pct = max(0, min(100, (int) $quest->progress)); @endphp
        <article class="sb16-quest" data-sb16-quest data-status="{{ $quest->status }}" data-search="{{ \Illuminate\Support\Str::lower(trim($quest->mission_title.' '.($quest->app_title ?? '').' '.($quest->mission_description ?? ''))) }}">
          <div class="sb16-quest__top"><span class="sb16-world">✦ {{ $quest->app_title ?: \Illuminate\Support\Str::headline($quest->app_slug) }}</span><span class="sb16-status sb16-status--{{ str_replace('_','-', $quest->status) }}">{{ $quest->status_label }}</span></div>
          <h3>{{ $quest->mission_title }}</h3><p class="sb16-quest__description">{{ $quest->mission_description ?: 'A little challenge, created by you.' }}</p>
          <div class="sb16-meta">@if($quest->difficulty)<span>{{ $quest->difficulty }}</span>@endif @if($quest->estimated_minutes)<span>{{ $quest->estimated_minutes }} min</span>@endif <span>Added {{ $quest->created_at?->diffForHumans() }}</span></div>
          <div class="sb16-progress__head"><span>Mission progress</span><strong>{{ $pct }}%</strong></div>
          <div class="sb16-progress__bar" role="meter" aria-label="Progress for {{ $quest->mission_title }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $pct }}"><span style="width: {{ $pct }}%"></span></div>
          <form class="sb16-progress-form" method="POST" action="{{ route('studybuddy.quests.update', $quest) }}">
            @csrf @method('PATCH')
            @if($quest->status !== 'in_progress')<input type="hidden" name="status" value="in_progress">@endif
            <label><span class="sb16-visually-hidden">Adjust progress for {{ $quest->mission_title }}</span><input type="range" name="progress" min="0" max="100" step="5" value="{{ $pct }}" data-sb16-range aria-label="Progress percentage for {{ $quest->mission_title }}"></label>
            <output data-sb16-output>{{ $pct }}%</output><button type="submit" class="sb16-small-btn">Save progress</button>
          </form>
          <details class="sb16-notes"><summary>Personal notes <span aria-hidden="true">↗</span></summary>
            <form method="POST" action="{{ route('studybuddy.quests.update', $quest) }}">@csrf @method('PATCH')<label for="sb16-notes-{{ $quest->id }}" class="sb16-visually-hidden">Notes for {{ $quest->mission_title }}</label><textarea id="sb16-notes-{{ $quest->id }}" name="notes" rows="3" maxlength="3000" placeholder="What did you discover?">{{ $quest->notes }}</textarea><button class="sb16-small-btn" type="submit">Save notes</button></form>
          </details>
          <div class="sb16-quest__actions">
            @if($quest->status !== 'completed')<form method="POST" action="{{ route('studybuddy.quests.update', $quest) }}">@csrf @method('PATCH')<button type="submit" name="status" value="completed" class="sb16-small-btn sb16-small-btn--done">✓ Mark complete</button></form>@else<form method="POST" action="{{ route('studybuddy.quests.update', $quest) }}">@csrf @method('PATCH')<button type="submit" name="status" value="in_progress" class="sb16-small-btn">↺ Continue learning</button></form>@endif
            @if($quest->status !== 'archived')<form method="POST" action="{{ route('studybuddy.quests.update', $quest) }}">@csrf @method('PATCH')<button type="submit" name="status" value="archived" class="sb16-small-btn sb16-small-btn--quiet">Archive</button></form>@else<form method="POST" action="{{ route('studybuddy.quests.update', $quest) }}">@csrf @method('PATCH')<button type="submit" name="status" value="saved" class="sb16-small-btn sb16-small-btn--quiet">Restore</button></form>@endif
            <form method="POST" action="{{ route('studybuddy.quests.destroy', $quest) }}" data-sb16-delete>@csrf @method('DELETE')<button type="submit" class="sb16-small-btn sb16-small-btn--remove">Delete</button></form>
          </div>
        </article>
      @endforeach
      </div>
      <p class="sb16-noresults" data-sb16-empty hidden>Nothing matches that filter on this page. Try another status or search.</p>
      @if($quests->hasPages())<div class="sb16-pagination">{{ $quests->links() }}</div>@endif
    @else
      <div class="sb16-empty"><span aria-hidden="true">✧</span><h3>Every journey begins with a little spark.</h3><p>No quests saved yet. Create your first goal or explore the learning apps.</p><button type="button" data-sb16-open class="sb16-btn sb16-btn--main">Make my first quest</button></div>
    @endif
  </section>
  <div class="sb16-foot"><a href="{{ route('dashboard') }}">← Return to my dashboard</a><a href="{{ route('studybuddy.apps') }}">Find another learning world ↗</a></div>
</div>
@endsection
