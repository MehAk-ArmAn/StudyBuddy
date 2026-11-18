@extends('layouts.app')
@section('title', 'Resource Studio — StudyBuddy')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-p10/studio.css') }}?v=10.1">
@endpush
@section('content')
<main class="sbp10" aria-label="Private StudyBuddy resource studio">
  <div class="sbp10-shell">
    <header class="sbp10-hero">
      <div class="sbp10-hero__text">
        <p class="sbp10-eyebrow">✦ STUDYBUDDY / YOUR PRIVATE LIBRARY</p>
        <h1>Keep every great idea <em>in orbit.</em></h1>
        <p>Write learning notes, preview Markdown, and keep your files together. This space belongs to your account only.</p>
        <div class="sbp10-hero__actions">
          <a href="{{ route('studybuddy.study-space') }}">Study Space ↗</a>
          <a href="{{ route('studybuddy.p09.pulse') }}">My Day ↗</a>
          <a href="{{ route('dashboard') }}">Dashboard ↗</a>
        </div>
      </div>
      <img src="{{ asset('assets/studybuddy-p07/icons/reading.webp') }}" alt="" loading="lazy" decoding="async">
    </header>

    @if(session('status'))<div class="sbp10-message sbp10-success" role="status">{{ session('status') }}</div>@endif
    @if($errors->any())
      <div class="sbp10-message sbp10-error" role="alert"><strong>Please check:</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif

    <div class="sbp10-toolbar">
      <div><span class="sbp10-eyebrow">RESOURCE STUDIO</span><h2>Your personal library</h2></div>
      <div class="sbp10-counts"><span>{{ count($notes) }} / 25 notes</span><span>{{ count($files) }} / 30 files</span></div>
    </div>

    <div class="sbp10-columns">
      <aside class="sbp10-panel sbp10-list" aria-label="Saved notes">
        <div class="sbp10-panel-heading"><h3>Documents</h3><span>{{ count($notes) }}</span></div>
        <label class="sbp10-search"><span class="sbp10-sr">Search your notes</span>
          <input id="sbp10-note-search" type="search" placeholder="Search your titles…" autocomplete="off">
        </label>
        <nav class="sbp10-note-list" aria-label="Choose a note">
          @forelse($notes as $note)
            <a class="sbp10-note-item{{ ($selected['id'] ?? '') === $note['id'] ? ' is-selected' : '' }}" data-note-title="{{ $note['title'] }}" href="{{ route('studybuddy.p10.index', ['note' => $note['id']]) }}">
              <span aria-hidden="true">✎</span><span><strong>{{ $note['title'] }}</strong><small>{{ \Illuminate\Support\Carbon::parse($note['updated_at'])->diffForHumans() }}</small></span>
            </a>
          @empty
            <p class="sbp10-empty">Your first note is waiting. ✨</p>
          @endforelse
        </nav>
        <details class="sbp10-create"{{ $notes ? '' : ' open' }}>
          <summary>+ Create a document</summary>
          <form method="POST" action="{{ route('studybuddy.p10.notes.create') }}">
            @csrf
            <label>Title <input name="title" required maxlength="100" placeholder="Science revision" value="{{ old('title') }}"></label>
            <label>First thoughts <textarea name="body" maxlength="12000" rows="3" placeholder="# My notes…">{{ old('body') }}</textarea></label>
            <button type="submit" class="sbp10-action">Create note</button>
          </form>
        </details>
      </aside>

      <section class="sbp10-panel sbp10-editor" aria-label="Markdown document editor">
        @if($selected)
          <div class="sbp10-panel-heading"><h3>Edit document</h3><span>Private</span></div>
          <form id="sbp10-editor-form" method="POST" action="{{ route('studybuddy.p10.notes.update', ['id' => $selected['id']]) }}">
            @csrf @method('PUT')
            <label for="sbp10-title">Document title</label>
            <input id="sbp10-title" name="title" required maxlength="100" value="{{ $selected['title'] }}">
            <div class="sbp10-switch" role="group" aria-label="Editor mode"><button type="button" class="is-active" data-sbp10-mode="write" aria-pressed="true">Write</button><button type="button" data-sbp10-mode="preview" aria-pressed="false">Preview</button></div>
            <label for="sbp10-body">Markdown text</label>
            <textarea id="sbp10-body" name="body" rows="17" maxlength="12000" spellcheck="true" placeholder="# My next adventure\n- The key idea\n- My question">{{ $selected['body'] }}</textarea>
            <div class="sbp10-editor-preview" id="sbp10-live-preview" hidden aria-label="Safe Markdown preview"></div>
            <div class="sbp10-form-actions"><span>Markdown headings (#), bullets (-), and paragraphs supported</span><button type="submit" class="sbp10-action">Save changes</button></div>
          </form>
          <form class="sbp10-delete" method="POST" action="{{ route('studybuddy.p10.notes.delete', ['id' => $selected['id']]) }}" onsubmit="return confirm('Delete this note permanently?')">@csrf @method('DELETE')<button type="submit">Delete this note</button></form>
        @else
          <div class="sbp10-editor-welcome"><img src="{{ asset('assets/studybuddy-p07/icons/mascot.webp') }}" alt="" loading="lazy"><h3>Your ideas belong here.</h3><p>Choose a note on the left or create one to start writing.</p></div>
        @endif
      </section>

      <aside class="sbp10-panel sbp10-outline" aria-label="Document outline and study tips">
        <div class="sbp10-panel-heading"><h3>Outline</h3><span>Live</span></div>
        <nav id="sbp10-outline" aria-label="Headings in this document"><p class="sbp10-empty">Headings appear when you write # Section headings.</p></nav>
        <div class="sbp10-tip"><img src="{{ asset('assets/studybuddy-p07/icons/achievements.webp') }}" alt="" loading="lazy"><h4>A little progress counts.</h4><p>Save one useful idea today. No artificial productivity scores, just your real notes.</p></div>
      </aside>
    </div>

    <section class="sbp10-files sbp10-panel" aria-label="Private file manager">
      <div class="sbp10-panel-heading sbp10-files-heading"><div><span class="sbp10-eyebrow">YOUR SPACE FILES</span><h2>Files &amp; recordings</h2><p>Documents, images and audio; only your signed-in account can open these.</p></div><span>{{ count($files) }} files</span></div>
      <div class="sbp10-filter" role="group" aria-label="Filter files by type"><button type="button" class="is-active" data-sbp10-filter="all" aria-pressed="true">All</button><button type="button" data-sbp10-filter="document" aria-pressed="false">Documents</button><button type="button" data-sbp10-filter="image" aria-pressed="false">Images</button><button type="button" data-sbp10-filter="audio" aria-pressed="false">Audio</button></div>
      <form id="sbp10-upload-form" class="sbp10-upload" method="POST" action="{{ route('studybuddy.p10.files.upload') }}" enctype="multipart/form-data">
        @csrf
        <label for="sbp10-upload"><strong>Choose a resource to upload</strong><span>PDF, TXT, MD, PNG, JPG, WebP, MP3, WAV or OGG · up to 8 MB each · 60 MB total</span></label>
        <input id="sbp10-upload" name="upload" type="file" required accept=".pdf,.txt,.md,.png,.jpg,.jpeg,.webp,.mp3,.wav,.ogg">
        <button type="submit" class="sbp10-action">Upload privately</button>
        <div class="sbp10-upload-progress" hidden><progress value="0" max="100">0%</progress><span role="status">Uploading…</span></div>
      </form>
      <div class="sbp10-file-grid" id="sbp10-file-grid">
        @forelse($files as $file)
          @php
            $type = \App\Support\StudyBuddyResourceStudio::fileType($file);
          @endphp
          <article class="sbp10-file" data-sbp10-type="{{ $type }}">
            <div class="sbp10-file-top"><span class="sbp10-file-icon" aria-hidden="true">{{ $type === 'audio' ? '♫' : ($type === 'image' ? '✦' : '▤') }}</span><small>{{ strtoupper($type) }} · {{ number_format(($file['size'] ?? 0) / 1024, 1) }} KB</small></div>
            @if($type === 'image')<img class="sbp10-thumbnail" src="{{ route('studybuddy.p10.files.preview', ['id' => $file['id']]) }}" alt="Preview of {{ $file['name'] }}" loading="lazy">@endif
            <strong class="sbp10-filename" title="{{ $file['name'] }}">{{ $file['name'] }}</strong>
            @if($type === 'audio')
              <audio controls preload="none" src="{{ route('studybuddy.p10.files.preview', ['id' => $file['id']]) }}" aria-label="Play {{ $file['name'] }}"></audio>
              <div class="sbp10-audio-wave" data-audio="{{ route('studybuddy.p10.files.preview', ['id' => $file['id']]) }}"><button type="button">Load waveform</button><canvas height="42" aria-label="Waveform for {{ $file['name'] }}" hidden></canvas><input type="range" min="0" max="1000" value="0" aria-label="Scrub {{ $file['name'] }}" hidden></div>
            @endif
            <div class="sbp10-file-actions"><a href="{{ route('studybuddy.p10.files.download', ['id' => $file['id']]) }}">Download</a><form method="POST" action="{{ route('studybuddy.p10.files.delete', ['id' => $file['id']]) }}" onsubmit="return confirm('Delete this private file permanently?')">@csrf @method('DELETE')<button type="submit">Remove</button></form></div>
          </article>
        @empty
          <p class="sbp10-empty">No resources uploaded. Your library starts here. 🐬</p>
        @endforelse
      </div>
    </section>
    <p class="sbp10-footnote">Files are stored privately, not in the public web folder. Your Study Space's browser-session threads remain separate; this library does not claim to provide AI-generated answers or school-wide sharing.</p>
  </div>
</main>
@endsection
@push('scripts')<script defer src="{{ asset('assets/studybuddy-p10/studio.js') }}?v=10.1"></script>@endpush
