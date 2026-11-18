@extends('layouts.app')
@section('title', 'School Connect')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-p11c/connect.css') }}?v=1">
@endpush
@section('content')
<div class="sbp11c-shell" id="sbp11c-connect">
  <header class="sbp11c-hero">
    <div><p class="sbp11c-kicker">STUDYBUDDY SCHOOL OS · PRIVATE STAFF CONNECT</p>
      <h1>{{ $organization->name }} <span>Connect</span></h1>
      <p>Private, school-scoped collaboration between verified adult staff. Messages and files are not public community posts.</p>
    </div>
    <a class="sbp11c-back" href="{{ route('schools.workspace', $organization) }}">← School workspace</a>
  </header>
  <div class="sbp11c-safety"><span aria-hidden="true">🛡️</span><p><strong>Respect every learner's privacy.</strong> Never share student records, grades, medical details or other sensitive information in staff chat. School managers may review <em>reported</em> messages; PCL does not have an inbox into private conversations. Attachments download privately, not as public links.</p></div>
  @if(session('status'))<div class="sbp11c-notice" role="status">{{ session('status') }}</div>@endif
  @if($errors->any())<div class="sbp11c-notice sbp11c-error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
  <div class="sbp11c-grid">
    <aside class="sbp11c-sidebar" aria-label="School conversations">
      <div class="sbp11c-sidebar-top"><div><p class="sbp11c-mini">WORKSPACE</p><h2>Conversations</h2></div><span class="sbp11c-online" title="Private school workspace">●</span></div>
      <h3>Staff channels</h3>
      <nav aria-label="Staff channels" class="sbp11c-thread-list">
        @foreach($threads->where('kind','channel') as $thread)
        <a class="sbp11c-thread @if($selected?->id === $thread->id) active @endif" @if($selected?->id === $thread->id) aria-current="page" @endif href="{{ route('schools.connect.show', [$organization,$thread]) }}"><span class="sbp11c-thread-icon">#</span><span>{{ $thread->title }}</span></a>
        @endforeach
      </nav>
      @if($manager)
      <details class="sbp11c-add"><summary>＋ Create staff channel</summary>
        <form method="POST" action="{{ route('schools.connect.channels.create', $organization) }}">@csrf
          <label>Channel name <input name="title" maxlength="70" minlength="3" required placeholder="Curriculum team"></label>
          <button type="submit">Create channel</button>
        </form>
      </details>
      @endif
      <h3>Direct messages</h3>
      <nav aria-label="Direct messages" class="sbp11c-thread-list">
        @forelse($threads->where('kind','direct') as $thread)
          @php
            $otherId = $thread->participant_a == auth()->id() ? $thread->participant_b : $thread->participant_a;
            $otherName = $staff->firstWhere('user_id', (int) $otherId)?->user?->name ?? 'Former colleague';
          @endphp
          <a class="sbp11c-thread @if($selected?->id === $thread->id) active @endif" @if($selected?->id === $thread->id) aria-current="page" @endif href="{{ route('schools.connect.show', [$organization,$thread]) }}"><span class="sbp11c-thread-icon">✉</span><span>{{ $otherName }}</span></a>
        @empty <p class="sbp11c-muted">No private conversations yet.</p>
        @endforelse
      </nav>
      <details class="sbp11c-add"><summary>＋ New direct message</summary>
        <form method="POST" action="{{ route('schools.connect.directs.start', $organization) }}">@csrf
          <label>Verified school colleague <select name="recipient_id" required><option value="">Choose staff…</option>@foreach($staff as $person)@if($person->user_id !== auth()->id())<option value="{{ $person->user_id }}">{{ $person->user?->name }} · {{ str_replace('_',' ',$person->role) }}</option>@endif @endforeach</select></label>
          <button type="submit">Start private chat</button>
        </form>
      </details>
      @if($manager)<a class="sbp11c-reports-link" href="{{ route('schools.connect.reports', $organization) }}">⚑ Moderation reports <span>{{ $pendingReports }} pending</span></a>@endif
      <a class="sbp11c-community" href="{{ url('/community') }}">🌐 Public community profiles <span>Separate from chat ↗</span></a>
    </aside>
    <main class="sbp11c-main" aria-label="Conversation messages">
      @if($selected)
        @php
          $dmPerson = $selected->kind === 'direct' ? $staff->firstWhere('user_id', (int) ($selected->participant_a == auth()->id() ? $selected->participant_b : $selected->participant_a))?->user?->name : null;
          $heading = $dmPerson ?? $selected->title;
        @endphp
        <div class="sbp11c-chat-header"><div class="sbp11c-chat-badge">{{ $selected->kind === 'direct' ? '✉' : '#' }}</div><div><p class="sbp11c-mini">{{ $selected->kind === 'direct' ? 'PRIVATE DIRECT MESSAGE' : 'SCHOOL STAFF CHANNEL' }}</p><h2>{{ $heading }}</h2></div><a class="sbp11c-refresh" href="{{ route('schools.connect.show', [$organization,$selected]) }}">↻ Refresh</a></div>
        <div class="sbp11c-message-list" id="sbp11c-message-list" role="log" aria-label="Recent messages" aria-live="off">
          @forelse($messages as $message)
            @php
              $mine = $message->author_id === auth()->id();
              $otherRead = $selected->kind === 'direct' && $mine ? (int) ($reads[$selected->participant_a == auth()->id() ? $selected->participant_b : $selected->participant_a] ?? 0) >= $message->id : false;
            @endphp
            <article class="sbp11c-bubble-wrap {{ $mine ? 'mine' : '' }}">
              <div class="sbp11c-avatar" aria-hidden="true">{{ strtoupper(substr($message->author?->name ?? '?',0,1)) }}</div>
              <div class="sbp11c-bubble"><div class="sbp11c-message-meta"><strong>{{ $message->author?->name ?? 'Former staff member' }}</strong><time datetime="{{ $message->created_at?->toIso8601String() }}">{{ $message->created_at?->format('j M, g:i A') }}</time></div>
                @if($message->hidden_at)
                  <p class="sbp11c-redacted">Message hidden after a safety review.</p>
                @else
                  @if($message->body)<p class="sbp11c-message-text">{{ $message->body }}</p>@endif
                  @if($message->attachment_path)<a class="sbp11c-file" href="{{ route('schools.connect.attachments.download', [$organization,$selected,$message]) }}">📎 Download {{ $message->attachment_name }} <small>({{ number_format(($message->attachment_size ?? 0)/1024) }} KB)</small></a>@endif
                  <details class="sbp11c-report"><summary>Report message</summary><form method="POST" action="{{ route('schools.connect.messages.report', [$organization,$selected,$message]) }}">@csrf<label>Reason <select name="reason"><option value="safety">Safety concern</option><option value="harassment">Harassment</option><option value="privacy">Privacy concern</option><option value="other">Other</option></select></label><button type="submit">Send to school managers</button></form></details>
                @endif
                @if($otherRead)<small class="sbp11c-read">✓✓ Seen by colleague</small>@endif
              </div>
            </article>
          @empty
            <div class="sbp11c-no-messages"><span>✦</span><h3>Start the conversation</h3><p>Say hello to your verified colleagues. Keep student information out of messages.</p></div>
          @endforelse
        </div>
        <div class="sbp11c-footer">
          <form method="POST" action="{{ route('schools.connect.read', [$organization,$selected]) }}" class="sbp11c-read-form">@csrf<button type="submit">✓ Mark conversation read</button><small>Read receipts update only when you choose this action.</small></form>
          <form method="POST" enctype="multipart/form-data" action="{{ route('schools.connect.messages.send', [$organization,$selected]) }}" class="sbp11c-compose">@csrf
            <label for="sbp11c-body" class="sr-only">Your message</label>
            <textarea id="sbp11c-body" name="body" maxlength="3000" rows="3" placeholder="Message your school team…">{{ old('body') }}</textarea>
            <div class="sbp11c-compose-bottom"><label class="sbp11c-file-picker">📎 Attach PDF or image <input type="file" name="attachment" accept=".pdf,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg"></label><small>Max 4 MB · private download</small><button type="submit">Send message ↗</button></div>
          </form>
        </div>
      @else
        <div class="sbp11c-no-messages"><h2>School staff chat</h2><p>Choose a channel or start a direct conversation to connect.</p></div>
      @endif
    </main>
  </div>
</div>
<script src="{{ asset('assets/studybuddy-p11c/connect.js') }}?v=1" defer></script>
@endsection
