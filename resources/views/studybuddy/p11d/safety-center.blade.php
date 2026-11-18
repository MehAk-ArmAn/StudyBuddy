@extends('layouts.app')
@section('title', 'School Safety Center')
@push('styles')<link rel="stylesheet" href="{{ asset('assets/studybuddy-p11d/safety.css') }}?v=1">@endpush
@section('content')
<div class="sbd11-shell">
  <header class="sbd11-hero">
    <div><p class="sbd11-eyebrow">STUDYBUDDY · SCHOOL SAFETY</p><h1>Care, clarity, <em>connection.</em></h1><p>{{ $organization->name }} · A private safety and staff updates space for verified school members.</p><div class="sbd11-links"><a href="{{ route('schools.workspace', $organization) }}">← School workspace</a><a href="{{ route('schools.connect.index', $organization) }}">Staff Connect ↗</a>@if($manager)<a href="{{ route('schools.connect.reports', $organization) }}">Message reports ↗</a>@endif</div></div>
    <div class="sbd11-stat"><strong>{{ $unread }}</strong><span>Unread badge alerts</span></div>
  </header>
  @if(session('status'))<p class="sbd11-flash" role="status">{{ session('status') }}</p>@endif
  @if($errors->any())<div class="sbd11-flash sbd11-error" role="alert"><strong>Couldn’t save your changes</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
  <nav class="sbd11-tabs" aria-label="Safety Center sections"><a href="#sbd11-notices">Staff notices</a><a href="#sbd11-reports">Raise a concern</a><a href="#sbd11-preferences">Preferences</a></nav>
  <div class="sbd11-layout">
    <main>
      <section id="sbd11-notices" class="sbd11-surface"><div class="sbd11-sectionhead"><div><span class="sbd11-overline">TEAM ANNOUNCEMENTS</span><h2>Staff noticeboard</h2><p>In-app notices. No emails, push alerts or automatic message surveillance.</p></div>@if($manager)<a class="sbd11-link" href="{{ route('schools.safety.export.notices', $organization) }}">Export CSV</a>@endif</div>
        @forelse($notices as $notice)
        <article class="sbd11-notice {{ $notice->priority === 'urgent' ? 'sbd11-urgent' : '' }}">
          <div class="sbd11-noticehead"><span class="sbd11-tag">{{ $notice->priority === 'urgent' ? 'Important' : 'Team update' }}</span><span class="sbd11-time">{{ $notice->created_at?->format('j M Y · H:i') }}</span></div>
          <h3>{{ $notice->title }}</h3><p class="sbd11-content">{{ $notice->body }}</p>
          <p class="sbd11-byline">By {{ $notice->poster?->name ?? 'Former staff member' }} @if($notice->expires_at)· Expires {{ $notice->expires_at->format('j M Y') }}@endif</p>
          <div class="sbd11-actions">
            @if(isset($read[$notice->id]))<span class="sbd11-read"><span aria-hidden="true">✓</span> Acknowledged</span>@else<form method="POST" action="{{ route('schools.safety.notices.read', [$organization, $notice]) }}">@csrf<button type="submit" class="sbd11-button sbd11-button-light">Mark read</button></form>@endif
            @if($manager)<form method="POST" action="{{ route('schools.safety.notices.archive', [$organization, $notice]) }}" onsubmit="return confirm('Archive this notice for the whole school?')">@csrf<button type="submit" class="sbd11-danger">Archive notice</button></form>@endif
          </div>
        </article>
        @empty<div class="sbd11-empty"><span>✦</span><h3>All clear for now</h3><p>Your school has no active notices. When a school manager posts one, it will appear here.</p></div>@endforelse
      </section>
      <section id="sbd11-reports" class="sbd11-surface"><div class="sbd11-sectionhead"><div><span class="sbd11-overline">SAFE REPORTING</span><h2>Raise a concern</h2><p>Only this school's management can review reports. This form does not contact emergency services or PCL moderators.</p></div></div>
        <div class="sbd11-advisory">Do not paste identifiable student information, passwords, private attachments or sensitive message contents here. For immediate danger, use your school's emergency safeguarding process.</div>
        <form class="sbd11-form" method="POST" action="{{ route('schools.safety.reports.submit', $organization) }}">
          @csrf
          <label>Concern category <select name="category" required><option value="inappropriate_message">Inappropriate message or conduct</option><option value="access_concern">Access or account concern</option><option value="staff_safety">Staff safety concern</option><option value="other">Other school concern</option></select></label>
          <label>Short subject <input name="subject" maxlength="150" minlength="5" required value="{{ old('subject') }}" placeholder="Describe the issue briefly"></label>
          <label>What happened? <textarea name="description" rows="4" maxlength="1600" minlength="10" required placeholder="Describe the concern without sharing private student details">{{ old('description') }}</textarea></label>
          <button class="sbd11-button" type="submit">Submit privately to school management</button>
        </form>
      </section>
      <section class="sbd11-surface"><div class="sbd11-sectionhead"><div><span class="sbd11-overline">CASE TRACKING</span><h2>{{ $manager ? 'School safety inbox' : 'My submitted concerns' }}</h2><p>School-scoped reports with clear review status. This is separate from the Staff Connect message report queue.</p></div></div>
        @forelse($reports as $report)
          <details class="sbd11-case"><summary><span><strong>{{ $report->subject }}</strong><small>{{ str_replace('_',' ',ucfirst($report->category)) }} · {{ $report->created_at?->format('j M Y') }}</small></span><span class="sbd11-status">{{ str_replace('_',' ',ucfirst($report->status)) }}</span></summary>
            <p class="sbd11-content">{{ $report->description }}</p><p class="sbd11-byline">Submitted by {{ $report->reporter?->name ?? 'Former staff member' }}</p>
            @if($report->resolution_note)<div class="sbd11-advisory">Manager update: {{ $report->resolution_note }} <small>— {{ $report->handler?->name ?? 'School management' }}</small></div>@endif
            @if($manager)<form class="sbd11-form" method="POST" action="{{ route('schools.safety.reports.review', [$organization, $report]) }}">@csrf @method('PUT')<label>Review status <select name="status"><option value="in_review">In review</option><option value="resolved">Resolved</option><option value="dismissed">Dismissed</option></select></label><label>Manager update <textarea name="resolution_note" required maxlength="1600" minlength="5" rows="3" placeholder="Record the action and next steps for the reporter"></textarea></label><button class="sbd11-button sbd11-button-light" type="submit">Save review</button></form>@endif
          </details>
        @empty<p class="sbd11-no-reports">No safety reports to display.</p>@endforelse
      </section>
    </main>
    <aside>
      @if($manager)<section class="sbd11-surface sbd11-panel"><span class="sbd11-overline">MANAGER TOOLS</span><h2>Post a staff notice</h2><p>Visible only within this verified school. Important notices always count toward unread alerts.</p><form class="sbd11-form" method="POST" action="{{ route('schools.safety.notices.post', $organization) }}">@csrf<label>Notice title <input name="title" required minlength="3" maxlength="140" value="{{ old('title') }}" placeholder="Staff briefing update"></label><label>Message <textarea name="body" required minlength="5" maxlength="2500" rows="5" placeholder="Keep it useful, respectful and free of student data">{{ old('body') }}</textarea></label><label>Priority <select name="priority"><option value="normal">Normal update</option><option value="urgent">Important notice</option></select></label><label>Expires (optional) <input name="expires_at" type="datetime-local"></label><button class="sbd11-button" type="submit">Publish to school staff</button></form></section>@endif
      <section id="sbd11-preferences" class="sbd11-surface sbd11-panel"><span class="sbd11-overline">MY SETTINGS</span><h2>Notification preference</h2><p>Control the unread badge count for normal notices. It does not hide notices, disable important alerts, or change email/push delivery.</p><form class="sbd11-form" method="POST" action="{{ route('schools.safety.preferences', $organization) }}">@csrf<label>Normal notice badge <select name="mute_normal_badge"><option value="0" @selected(!$mute)>Count normal unread notices</option><option value="1" @selected($mute)>Mute normal unread badge</option></select></label><button class="sbd11-button sbd11-button-light" type="submit">Save preference</button></form></section>
      <section class="sbd11-surface sbd11-panel sbd11-guide"><span class="sbd11-overline">SAFEGUARDING</span><h2>School boundaries matter.</h2><p>Teachers and school administrators only see the information their membership permits. PCL platform administrators do not receive automatic access to internal notices or reports.</p><p>Staff Connect has its own message reporting workflow. School managers can open that queue through the link above.</p></section>
    </aside>
  </div>
</div>
@endsection
