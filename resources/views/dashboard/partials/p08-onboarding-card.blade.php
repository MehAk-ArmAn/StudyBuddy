{{-- Phase 08 is an optional, private setup guide. It does not gate role privileges. --}}
@php
  $p08Setup = is_array($profile['sb_p08_setup'] ?? null) ? $profile['sb_p08_setup'] : [];
  $p08Done = array_intersect(range(1, 5), array_unique(array_map('intval', is_array($p08Setup['steps'] ?? null) ? $p08Setup['steps'] : [])));
  $p08Progress = count($p08Done) * 20;
  $p08Next = 6;
  foreach (range(1, 5) as $p08Idx) { if (!in_array($p08Idx, $p08Done, true)) { $p08Next = $p08Idx; break; } }
  $p08Finished = !empty($p08Setup['finished_at']);
@endphp
<link rel="stylesheet" href="{{ asset('assets/studybuddy-p08/onboarding.css') }}?v=8.1">
<section class="sb08-dashboard-ribbon" aria-label="Optional personalized setup" data-sb08-guide="{{ $p08Setup['guide_level'] ?? 'guided' }}">
  <div class="sb08-ribbon-icon" aria-hidden="true"><img src="{{ asset('assets/studybuddy-p07/icons/reading.webp') }}" alt="" loading="lazy"></div>
  <div class="sb08-ribbon-body"><span>{{ $p08Finished ? 'YOUR PERSONAL SPACE' : 'MAKE STUDYBUDDY YOURS' }}</span>
    <strong>{{ $p08Finished ? 'Your universe, your choices.' : 'A little setup makes a big difference.' }}</strong>
    <small>{{ $p08Finished ? 'Your saved preferences are active. You can adjust them anytime.' : $p08Progress.'% of setup saved · optional · no features are locked' }}</small>
  </div>
  <a href="{{ route('studybuddy.onboarding.show', ['step' => $p08Finished ? 1 : $p08Next]) }}">{{ $p08Finished ? 'Edit my setup' : 'Continue setup' }} <span aria-hidden="true">↗</span></a>
</section>
