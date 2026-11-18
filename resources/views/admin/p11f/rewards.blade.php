@extends('layouts.admin')
@section('title', 'Rewards & Achievements Governance')
@section('content')
<main class="sb-p11f-admin" style="max-width:1140px;margin:28px auto;padding:clamp(16px,3vw,34px);color:#e8edff;background:#1a2340;border-radius:22px">
  <p style="letter-spacing:.14em;color:#84d7fc;font-size:12px;font-weight:800">STUDYBUDDY · HEAD ADMIN ONLY</p>
  <h1>Rewards integrity & platform controls</h1>
  <p>Configure verified learning rewards; updates are recorded with actor and previous values. This is not a code-execution interface.</p>
  @if(session('status'))<p role="status" style="padding:12px;background:#244e51">{{ session('status') }}</p>@endif
  @if($errors->any())<div role="alert"><strong>Fix these settings:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
  <section style="display:flex;gap:16px;flex-wrap:wrap;margin:25px 0">
    <div style="padding:16px;border:1px solid #6886a8;border-radius:12px"><strong>{{ $stats['verified_completions'] }}</strong><div>Verified rewarded games</div></div>
    <div style="padding:16px;border:1px solid #6886a8;border-radius:12px"><strong>{{ $stats['minted_points'] }}</strong><div>New system points minted</div></div>
    <div style="padding:16px;border:1px solid #6886a8;border-radius:12px"><strong>{{ $stats['achievement_unlocks'] }}</strong><div>Badge unlocks</div></div>
  </section>
  <h2>Global controls</h2>
  <form method="POST" action="{{ route('admin.control-room.rewards.settings') }}" style="display:flex;flex-wrap:wrap;gap:12px;align-items:end">@csrf @method('PUT')
    <label>Rewards <select name="enabled"><option value="1" @selected($settings?->enabled)>Enabled</option><option value="0" @selected(!$settings?->enabled)>Paused</option></select></label>
    <label>Max points / user / day <input type="number" name="global_daily_cap" value="{{ $settings->global_daily_cap ?? 60 }}" min="1" max="200" required></label>
    <button type="submit">Save global policy</button>
  </form>
  <h2 style="margin-top:30px">Verified game rules</h2>
  @foreach($rules as $rule)
  <form method="POST" action="{{ route('admin.control-room.rewards.rules.update',$rule->id) }}" style="padding:16px;margin:14px 0;background:#252f55;border-radius:14px">@csrf @method('PUT')
    <h3>{{ str_replace(':',' · ', $rule->game_key) }}</h3>
    <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:end">
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
  <div style="overflow-x:auto"><table style="width:100%;border-collapse:collapse"><thead><tr><th>When</th><th>Action</th><th>Target</th><th>Actor ID</th></tr></thead><tbody>
    @forelse($audits as $a)<tr><td>{{ $a->created_at }}</td><td>{{ $a->action }}</td><td>{{ $a->target }}</td><td>{{ $a->actor_id }}</td></tr>
    @empty<tr><td colspan="4">No reward changes yet.</td></tr>@endforelse
  </tbody></table></div>
  <p style="font-size:13px;color:#c4cbe2;margin-top:24px">Software source code is changed in Git, reviewed, tested and deployed separately—not executed from this dashboard.</p>
</main>
<style>.sb-p11f-admin input,.sb-p11f-admin select{display:block;background:#0e1734;color:white;border:1px solid #7784bd;padding:9px;border-radius:8px;width:145px;max-width:100%;box-sizing:border-box}.sb-p11f-admin label{display:grid;gap:4px;font-size:12px}.sb-p11f-admin button{background:#8661f8;border:0;color:#fff;border-radius:9px;padding:12px;font-weight:700;cursor:pointer}.sb-p11f-admin th,.sb-p11f-admin td{border-bottom:1px solid #465173;padding:8px;text-align:left}.sb-p11f-admin a{color:#8cddff}@media(max-width:600px){.sb-p11f-admin{margin:12px!important}}</style>
@endsection
