@extends('layouts.admin')
@section('title', 'Showcase & Desktop · '.$app->name)
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-w33/showcase.css') }}?v=33.1">
@endpush
@section('content')
<main class="sb33-admin" data-admin-skip-unified>
  <nav class="sb33-crumbs"><a href="{{ route('admin.control-room.apps.index') }}">Apps Library</a> <span>›</span> <a href="{{ route('admin.control-room.apps.edit', $app) }}">{{ $app->name }}</a> <span>›</span> Showcase</nav>
  <div class="sb33-admin-head"><div><span class="sb33-kicker">STUDYBUDDY • APP STORE STUDIO</span><h1>Showcase & Desktop</h1><p>Manage the live gallery, learning highlights, release notes and desktop distribution for <strong>{{ $app->name }}</strong>. Your existing Flutter Web ZIP is managed in the main app editor.</p></div>
    <a class="sb33-button secondary" href="{{ route('admin.control-room.apps.edit', $app) }}">← Back to app editor</a>
  </div>
  @if(session('status'))<div class="sb33-notice">{{ session('status') }}</div>@endif
  @if($errors->any())<div class="sb33-error" role="alert"><strong>Please review:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
  <section class="sb33-panel"><div class="sb33-panel-head"><div><span class="sb33-kicker">01 • THE STORY</span><h2>More than a store listing</h2></div><span class="sb33-subtle">Visible on the published app detail page</span></div>
    <form method="POST" action="{{ route('studybuddy.w33.admin.update', $app) }}" class="sb33-admin-form">@csrf
      <div class="sb33-fieldgrid"><label>Release version<input type="text" name="version" maxlength="40" placeholder="1.2.0" value="{{ old('version', $showcase->version ?? '') }}"></label>
      <label>Trailer link (HTTPS)<input type="url" name="trailer_url" placeholder="https://..." value="{{ old('trailer_url', $showcase->trailer_url ?? '') }}"></label></div>
      <label>Why learners will love it<textarea name="why_play" rows="3" maxlength="4000" placeholder="What makes this learning experience unique?">{{ old('why_play', $showcase->why_play ?? '') }}</textarea></label>
      <label>Highlights <small>(one per line)</small><textarea name="highlights" rows="5" maxlength="4000" placeholder="Adaptive challenges&#10;Short, playable sessions&#10;Designed for curious minds">{{ old('highlights', $showcase->highlights ?? '') }}</textarea></label>
      <label>What's new in this release<textarea name="whats_new" rows="4" maxlength="8000" placeholder="Improvements, new topics, fixes...">{{ old('whats_new', $showcase->whats_new ?? '') }}</textarea></label>
      <label>Privacy & safety in plain language<textarea name="privacy_summary" rows="3" maxlength="4000" placeholder="What data is collected, whether the app needs an internet connection, and how learner progress works.">{{ old('privacy_summary', $showcase->privacy_summary ?? '') }}</textarea></label>
      <button class="sb33-button" type="submit">Save showcase</button>
    </form>
  </section>
  <section class="sb33-panel"><div class="sb33-panel-head"><div><span class="sb33-kicker">02 • REAL SCREENSHOTS</span><h2>Visual story gallery</h2></div><span class="sb33-subtle">{{ $shots->count() }} / 24 images</span></div>
    <p class="sb33-subtle">Upload up to 8 screenshots at once (PNG, JPG or WebP, 5 MB each). Use genuine app captures—landscape or portrait both work. Draft galleries remain private.</p>
    <form method="POST" enctype="multipart/form-data" action="{{ route('studybuddy.w33.admin.screenshots.upload', $app) }}" class="sb33-upload-row">@csrf
       <input aria-label="Select app screenshots" name="screenshots[]" type="file" accept="image/png,image/jpeg,image/webp" multiple required>
       <button class="sb33-button" type="submit">Upload screenshots</button>
    </form>
    <div class="sb33-edit-gallery">
     @forelse($shots as $shot)
      <div class="sb33-edit-shot"><img src="{{ route('studybuddy.w33.admin.screenshots.image', ['app' => $app, 'shot' => $shot->id]) }}" alt="{{ $shot->alt_text ?: $app->name.' screenshot' }}" loading="lazy">
        <form method="POST" action="{{ route('studybuddy.w33.admin.screenshots.update', ['app' => $app, 'shot' => $shot->id]) }}">@csrf
          <label>Caption<input name="title" maxlength="120" value="{{ $shot->title }}" placeholder="Example: Play the numbers world"></label>
          <label>Alt text<input name="alt_text" maxlength="220" value="{{ $shot->alt_text }}" placeholder="Describe the screen"></label>
          <label>Order<input type="number" name="position" min="0" max="1000" value="{{ $shot->position }}"></label>
          <button class="sb33-button small" type="submit">Save image</button>
        </form>
        <form method="POST" action="{{ route('studybuddy.w33.admin.screenshots.destroy', ['app' => $app, 'shot' => $shot->id]) }}" onsubmit="return confirm('Remove this screenshot?')">@csrf @method('DELETE')<button type="submit" class="sb33-remove">Remove image</button></form>
      </div>
     @empty <p class="sb33-subtle">No screenshots uploaded yet. Add real game captures to bring this listing to life.</p>
     @endforelse
    </div>
  </section>
  <section class="sb33-panel"><div class="sb33-panel-head"><div><span class="sb33-kicker">03 • ONE ZIP, MULTIPLE SURFACES</span><h2>Desktop downloads</h2></div></div>
    <div class="sb33-methods"><div><strong>🌐 Browser + Desktop install</strong><p>Your uploaded Flutter Web ZIP becomes the existing browser launcher. The desktop-install option opens the app's installable browser experience (HTTPS or localhost needed; support depends on browser).</p>
      @if($app->usesUploadedBuild() && $app->hasPublishedWebApp() && $app->is_active)
       <a class="sb33-button secondary" href="{{ route('studybuddy.w33.desktop.install', $app->slug) }}" target="_blank" rel="noopener">Test installable web app ↗</a>
      @else <span class="sb33-subtle">Publish a hosted web build to enable desktop installation.</span> @endif
    </div><div><strong>🧰 Generate Electron desktop build kit</strong><p>Export the <em>actual published web files</em> in an Electron project. Build on macOS/Windows/Linux using Node.js, then upload the tested installer below. The kit itself is not a signed installer.</p>
      @if($app->usesUploadedBuild() && $app->hasPublishedWebApp())
       <a class="sb33-button secondary" href="{{ route('studybuddy.w33.admin.desktop.kit', $app) }}">Download desktop builder ZIP ↓</a>
      @else <span class="sb33-subtle">Upload a Flutter Web build in the main editor first.</span> @endif
    </div></div>
    <div class="sb33-native-grid">
     @foreach(['windows' => 'Windows (.exe / .msix / .msi / .zip)', 'mac' => 'macOS (.dmg / .pkg / .zip)', 'linux' => 'Linux (.AppImage / .deb / .rpm / .zip)'] as $platform => $label)
      <article class="sb33-native"><h3>{{ $label }}</h3>
        @if(isset($packages[$platform]))<p class="sb33-ok">✓ Uploaded: {{ $packages[$platform]->original_name }} ({{ round($packages[$platform]->bytes/1024/1024, 1) }} MB)</p><p class="sb33-subtle">SHA-256: <code style="word-break:break-all">{{ $packages[$platform]->sha256 }}</code></p>@endif
        <form method="POST" enctype="multipart/form-data" action="{{ route('studybuddy.w33.admin.desktop.upload', ['app' => $app, 'platform' => $platform]) }}">@csrf
          <label>Release version<input name="release_version" maxlength="40" value="{{ $packages[$platform]->version ?? $showcase->version ?? '' }}" placeholder="1.0.0"></label>
          <label>Actual tested desktop installer<input type="file" name="desktop_file" required></label>
          <button type="submit" class="sb33-button small">{{ isset($packages[$platform]) ? 'Replace installer' : 'Upload installer' }}</button>
        </form>
        @if(isset($packages[$platform]))<form method="POST" action="{{ route('studybuddy.w33.admin.desktop.destroy', ['app' => $app, 'platform' => $platform]) }}" onsubmit="return confirm('Remove this downloadable installer?')">@csrf @method('DELETE')<button type="submit" class="sb33-remove">Remove installer</button></form>@endif
      </article>
     @endforeach
    </div><p class="sb33-subtle">Native releases require an OS-specific build and, for public distribution, usually code signing / notarization. Upload limits are controlled by PHP/server settings; the app validates at 200 MB.</p>
  </section>
</main>
@endsection
