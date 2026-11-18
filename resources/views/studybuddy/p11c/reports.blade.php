@extends('layouts.app')
@section('title', 'School Chat Safety Reports')
@push('styles')<link rel="stylesheet" href="{{ asset('assets/studybuddy-p11c/connect.css') }}?v=1">@endpush
@section('content')
<div class="sbp11c-shell sbp11c-reports-page">
  <header class="sbp11c-hero"><div><p class="sbp11c-kicker">SCHOOL OS · SAFETY</p><h1>Reported messages</h1><p>{{ $organization->name }} · Only the school's authorized owner and administrators can review reports. This is not a general message inbox.</p></div><a class="sbp11c-back" href="{{ route('schools.connect.index', $organization) }}">← Back to school chat</a></header>
  @if(session('status'))<p class="sbp11c-notice" role="status">{{ session('status') }}</p>@endif
  @if($errors->any())<div class="sbp11c-notice sbp11c-error" role="alert">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>@endif
  <div class="sbp11c-report-grid">
    @forelse($reports as $report)
      <article class="sbp11c-report-card">
        <div class="sbp11c-report-row"><span>{{ strtoupper($report->reason) }}</span><strong>{{ ucfirst($report->status) }}</strong></div>
        <p class="sbp11c-mini">Reported by {{ $report->reporter_name ?? 'Former staff' }} · {{ \Illuminate\Support\Carbon::parse($report->created_at)->format('j M Y') }}</p>
        <blockquote>{{ $report->hidden_at ? '[Previously hidden message]' : \Illuminate\Support\Str::limit((string)$report->reported_body, 1200) }}</blockquote>
        @if($report->status === 'pending')
          <div class="sbp11c-report-actions">
            <form method="POST" action="{{ route('schools.connect.reports.review', [$organization,$report->id]) }}">@csrf @method('PUT')<input type="hidden" name="decision" value="dismissed"><button type="submit">Dismiss report</button></form>
            <form method="POST" action="{{ route('schools.connect.reports.review', [$organization,$report->id]) }}">@csrf @method('PUT')<input type="hidden" name="decision" value="hidden"><button class="sbp11c-danger" type="submit">Hide reported message</button></form>
          </div>
        @endif
      </article>
    @empty
      <div class="sbp11c-report-card"><h2>No reports</h2><p>This school's moderation queue is clear.</p></div>
    @endforelse
  </div>
</div>
@endsection
