@extends('layouts.admin')
@section('title', 'PCL Team & Access')
@section('content')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-p11/control.css') }}">
<section class="p11-shell">
  <div class="p11-top"><div><span class="p11-kicker">PCL · STUDYBUDDY / ACCESS</span><h1>Team & permissions</h1><p>One protected Head Admin. Everyone else gets only the capabilities their work needs.</p></div><a class="p11-link" href="{{ route('admin.control-room.index') }}">← Control Room</a></div>
  @if(session('status'))<p class="p11-success" role="status">{{ session('status') }}</p>@endif
  @if($errors->any())<div class="p11-error" role="alert"><strong>Check your submission</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
  @if(!$headId)<div class="p11-warning" role="status"><strong>Head Admin setup required.</strong> No Head Admin has been claimed yet. Back up your DB, apply the P11 migration, rotate the existing <code>admin@studybuddy.fun</code> password, and explicitly claim its user ID using the included console commands. Existing admins are not deleted automatically.</div>@endif
  <div class="p11-grid">
    <article class="p11-panel"><span class="p11-kicker">IDENTITY ROOT</span><h2>Protected Head Admin</h2><p>Account ID is the anchor—not the email or display name alone. Normal admin routes cannot edit, demote, or delete it.</p><div class="p11-pill">{{ $headId ? 'Claimed · #'.$headId : 'Not claimed' }}</div><p class="p11-note">Recovery always requires a separate, audited operator process and database backup. Never use shared short passwords.</p></article>
    <article class="p11-panel"><span class="p11-kicker">MEMBER CAPABILITIES</span><h2>Least privilege by default</h2><p>School review, users, apps, content, safeguarding, support, and operations have separate server-side checks.</p><div class="p11-pill">{{ $staff->where('kind','staff')->where('is_active',1)->count() }} active staff grants</div><p class="p11-note">A staff account needs verified email and explicit active permissions to sign in.</p></article>
  </div>
  @if($canReviewSchools)
  <article class="p11-panel"><div class="p11-section"><div><span class="p11-kicker">SCHOOL GOVERNANCE</span><h2>Institution reviews</h2></div><a class="p11-link" href="{{ route('admin.control-room.schools.index') }}">Review schools →</a></div>
    <div class="p11-school-counts"><div><strong>{{ (int) ($schoolStats['pending'] ?? 0) }}</strong><span>Pending checks</span></div><div><strong>{{ (int) ($schoolStats['approved'] ?? 0) }}</strong><span>Approved</span></div><div><strong>{{ (int) ($schoolStats['rejected'] ?? 0) }}</strong><span>Rejected</span></div></div>
    <p class="p11-note">Only independently verified institutions should be approved. School membership remains tenant-scoped.</p>
  </article>
  @endif
  @if($isHead)
  <article class="p11-panel p11-new"><h2>Create PCL team account</h2><p>A random password is generated and never displayed. The member must use their email/password-reset flow and verify their address.</p>
    <form method="POST" action="{{ route('admin.control-room.team.create') }}" class="p11-form">@csrf
      <label>Name<input name="name" required maxlength="120" value="{{ old('name') }}" autocomplete="off"></label>
      <label>Work email<input type="email" name="email" required maxlength="190" value="{{ old('email') }}" autocomplete="off"></label>
      <fieldset><legend>Permissions</legend><div class="p11-caps">@foreach($capabilities as $key=>$label) <label><input type="checkbox" name="capabilities[]" value="{{ $key }}" @checked(in_array($key,(array)old('capabilities', ['platform.view']),true))><span><strong>{{ $label }}</strong><small>{{ $key }}</small></span></label> @endforeach</div></fieldset>
      <button type="submit" class="p11-primary">Create invitation account</button>
    </form>
  </article>
  @endif
  <article class="p11-panel"><div class="p11-section"><div><span class="p11-kicker">ACTIVE DIRECTORY</span><h2>Platform admin accounts</h2></div><span>{{ $staff->count() }} total grants</span></div>
    <div class="p11-scroll"><table class="p11-table"><thead><tr><th>Person</th><th>Kind</th><th>State</th><th>Capabilities</th><th>Actions</th></tr></thead><tbody>
    @forelse($staff as $person)
      @php $permissions = json_decode($person->capabilities ?: '[]', true); $isRoot = $person->kind === 'head'; @endphp
      <tr><td><strong>{{ $person->name }}</strong><small>{{ $person->email }} · ID {{ $person->user_id }}</small></td><td><span class="p11-tag">{{ $isRoot ? 'Protected Head' : 'PCL Staff' }}</span></td><td><span class="p11-tag">{{ $person->is_active ? 'Active' : 'Revoked' }}</span><small>{{ $person->email_verified_at ? 'Email verified' : 'Email pending' }}</small></td><td class="p11-caps-cell">{{ $isRoot ? 'All permissions' : implode(' · ', is_array($permissions) ? $permissions : []) }}</td><td>
      @if($isHead && !$isRoot && $person->is_active)
        <details class="p11-details"><summary>Edit permissions</summary><form method="POST" action="{{ route('admin.control-room.team.update', $person->user_id) }}">@csrf @method('PUT')<input type="hidden" name="is_active" value="1">
        @foreach($capabilities as $key=>$label)<label><input type="checkbox" name="capabilities[]" value="{{ $key }}" @checked(in_array($key,$permissions ?? [],true))> {{ $label }}</label>@endforeach
        <button class="p11-primary" type="submit">Save access</button></form>
        <form method="POST" action="{{ route('admin.control-room.team.revoke', $person->user_id) }}" onsubmit="return confirm('Revoke this staff account? This keeps their data but ends platform access.')">@csrf @method('DELETE')<button type="submit" class="p11-danger">Revoke access</button></form></details>
      @else<span class="p11-note">{{ $isRoot ? 'Protected' : 'Read only' }}</span>@endif
      </td></tr>
    @empty<tr><td colspan="5">No grants recorded. Claim Head Admin after the migration.</td></tr>@endforelse
    </tbody></table></div>
  </article>
  @if($canReadAudit)
  <article class="p11-panel"><span class="p11-kicker">SECURITY EVENTS</span><h2>Recent recorded actions</h2><p>Restricted operational view; this shows stored events, not imaginary activity.</p>
   <div class="p11-scroll"><table class="p11-table"><thead><tr><th>When</th><th>Type</th><th>Severity</th><th>Actor ID</th></tr></thead><tbody>@forelse($recentEvents as $event)<tr><td>{{ $event->created_at?->format('j M, H:i') }}</td><td>{{ $event->event }}</td><td>{{ $event->severity }}</td><td>{{ $event->actor_id ?? 'System' }}</td></tr>@empty<tr><td colspan="4">No security events recorded.</td></tr>@endforelse</tbody></table></div>
  </article>
  @endif
</section>
@endsection
