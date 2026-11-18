@extends('layouts.admin')
@section('title', 'Rewards & Achievements Governance')
@push('styles-late')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-p11f/rewards.css') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/studybuddy-p11f/rewards.css') }}">
@endpush
@section('content')
<main class="sb-p11f-admin">
  <p class="sb-p11f-admin__eyebrow">STUDYBUDDY · HEAD ADMIN ONLY</p>
  <h1>Rewards integrity & platform controls</h1>
  <p>Configure verified learning rewards; updates are recorded with actor and previous values. This is not a code-execution interface.</p>
  @if(session('status'))<p role="status" class="sb-p11f-admin__status">{{ session('status') }}</p>@endif
  @if($errors->any())<div role="alert"><strong>Fix these settings:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
  <section class="sb-p11f-admin__metrics" aria-label="Reward statistics">
    <div class="sb-p11f-admin__metric"><strong>{{ $stats['verified_completions'] }}</strong><div>Verified rewarded games</div></div>
    <div class="sb-p11f-admin__metric"><strong>{{ $stats['minted_points'] }}</strong><div>New system points minted</div></div>
    <div class="sb-p11f-admin__metric"><strong>{{ $stats['achievement_unlocks'] }}</strong><div>Badge unlocks</div></div>
  </section>
  <h2>Global controls</h2>
  <form method="POST" action="{{ route('admin.control-room.rewards.settings') }}" class="sb-p11f-admin__settings">@csrf @method('PUT')
    <label>Rewards <select name="enabled"><option value="1" @selected($settings?->enabled)>Enabled</option><option value="0" @selected(!$settings?->enabled)>Paused</option></select></label>
    <label>Max points / user / day <input type="number" name="global_daily_cap" value="{{ $settings->global_daily_cap ?? 60 }}" min="1" max="200" required></label>
    <button type="submit">Save global policy</button>
  </form>
  <h2 class="sb-p11f-admin__section-title">Verified game rules</h2>
  @foreach($rules as $rule)
  <form method="POST" action="{{ route('admin.control-room.rewards.rules.update',$rule->id) }}" class="sb-p11f-admin__rule">@csrf @method('PUT')
    <h3>{{ str_replace(':',' · ', $rule->game_key) }}</h3>
    <div class="sb-p11f-admin__fields">
      <label>Enabled <select name="enabled"><option value="1" @selected($rule->enabled)>Yes</option><option value="0" @selected(!$rule->enabled)>No</option></select></label>
      <label>Correct answers required <input type="number" name="min_correct" min="3" max="5" value="{{ $rule->min_correct }}" required></label>
      <label>Minimum elapsed seconds <input type="number" name="min_seconds" min="10" max="180" value="{{ $rule->min_seconds }}" required></label>
      <label>Points per correct answer <input type="number" name="points_per_correct" min="1" max="12" value="{{ $rule->points_per_correct }}" required></label>
      <label>Small bonus after (seconds) <input type="number" name="time_bonus_after_seconds" min="45" max="300" value="{{ $rule->time_bonus_after_seconds }}" required></label>
      <label>Time bonus points <input type="number" name="time_bonus_points" min="0" max="3" value="{{ $rule->time_bonus_points }}" required></label>
      <label>Max points per run <input type="number" name="max_points_per_run" min="1" max="50" value="{{ $rule->max_points_per_run }}" required></label>
      <button type="submit">Save this game</button>
    </div>
  </form>
  @endforeach
  <p>Only server-graded Math Sprint currently issues points. External or embedded games must be individually connected to a verified result adapter. A learner cannot award points by pressing a generic Save button.</p>
  <h2>Head Admin control map</h2>
  <p><a href="{{ url('/admin/control-room') }}">Control Room</a> · <a href="{{ url('/admin/control-room/apps') }}">Apps / publishing</a> · <a href="{{ url('/admin/control-room/site-settings') }}">Site settings</a> · <a href="{{ url('/admin/control-room/schools') }}">Schools</a> · <a href="{{ url('/admin/control-room/team') }}">Team / permissions</a></p>
  <h2>Change history (latest 25)</h2>
  <div class="sb-p11f-admin__table-scroll" role="region" aria-label="Reward change history" tabindex="0"><table class="sb-p11f-admin__table"><thead><tr><th>When</th><th>Action</th><th>Target</th><th>Actor ID</th></tr></thead><tbody>
    @forelse($audits as $a)<tr><td>{{ $a->created_at }}</td><td>{{ $a->action }}</td><td>{{ $a->target }}</td><td>{{ $a->actor_id }}</td></tr>
    @empty<tr><td colspan="4">No reward changes yet.</td></tr>@endforelse
  </tbody></table></div>
  <p class="sb-p11f-admin__footnote">Software source code is changed in Git, reviewed, tested and deployed separately—not executed from this dashboard.</p>
</main>

@endsection
