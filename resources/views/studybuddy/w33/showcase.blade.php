@php
    $sb33Extras = \Illuminate\Support\Facades\DB::table('studybuddy_w33_showcases')->where('app_id', $app->id)->first();
    $sb33Shots = \Illuminate\Support\Facades\DB::table('studybuddy_w33_screenshots')->where('app_id', $app->id)->orderBy('position')->orderBy('id')->get();
    $sb33Desktop = $app->is_download_enabled ? \Illuminate\Support\Facades\DB::table('studybuddy_w33_desktop_packages')->where('app_id', $app->id)->get() : collect();
    $sb33Highlights = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $sb33Extras->highlights ?? ''))));
    $sb33Hosted = $app->is_active && $app->is_web_enabled && $app->usesUploadedBuild() && $app->hasPublishedWebApp();
@endphp
<div class="sb33-showcase" data-sb33-showcase>
    @if($sb33Shots->isNotEmpty())
    <section class="sb33-showcase-section" aria-label="Real app screenshots">
      <div class="sb33-showcase-heading"><div><span class="sb33-kicker">INSIDE THE WORLD</span><h2>See the adventure before you play.</h2></div><span class="sb33-subtle">{{ $sb33Shots->count() }} real app screenshots • swipe to explore</span></div>
      <div class="sb33-gallery" role="group" aria-label="Screenshots gallery">
        @foreach($sb33Shots as $shot)
         @php $sb33ShotUrl = ($adminPreview ?? false)
             ? route('studybuddy.w33.admin.screenshots.image', ['app' => $app, 'shot' => $shot->id])
             : route('studybuddy.w33.screenshot', ['slug' => $app->slug, 'shot' => $shot->id]); @endphp
         <button type="button" class="sb33-shot" data-sb33-shot data-src="{{ $sb33ShotUrl }}" data-title="{{ $shot->title }}" data-alt="{{ $shot->alt_text }}" aria-label="Enlarge screenshot {{ $loop->iteration }}">
          <img loading="lazy" src="{{ $sb33ShotUrl }}" alt="{{ $shot->alt_text ?: $app->name.' app screenshot' }}">
          <span>{{ $shot->title ?: 'Screenshot '.$loop->iteration }}</span>
         </button>
        @endforeach
      </div>
    </section>
    @endif
    @if($sb33Extras && ($sb33Extras->why_play || count($sb33Highlights) || $sb33Extras->whats_new || $sb33Extras->privacy_summary || $sb33Extras->trailer_url))
    <section class="sb33-showcase-section"><div class="sb33-showcase-heading"><div><span class="sb33-kicker">THE STORY BEHIND THE APP</span><h2>Built for curious minds.</h2></div>@if($sb33Extras->version)<span class="sb33-version">v{{ $sb33Extras->version }}</span>@endif</div>
      <div class="sb33-story-grid">
       @if($sb33Extras->why_play || count($sb33Highlights))<article class="sb33-story"><h3>✦ Why you'll love it</h3>@if($sb33Extras->why_play)<p>{{ $sb33Extras->why_play }}</p>@endif
          @if(count($sb33Highlights))<ul>@foreach($sb33Highlights as $feature)<li>{{ $feature }}</li>@endforeach</ul>@endif
       </article>@endif
       @if($sb33Extras->whats_new)<article class="sb33-story"><h3>✧ What's new</h3><p class="sb33-preline">{{ $sb33Extras->whats_new }}</p></article>@endif
       @if($sb33Extras->privacy_summary)<article class="sb33-story"><h3>♧ Privacy, explained simply</h3><p>{{ $sb33Extras->privacy_summary }}</p></article>@endif
       @if($sb33Extras->trailer_url)<article class="sb33-story"><h3>▶ Watch a real preview</h3><p>See this app in action.</p><a class="sb33-cta" href="{{ $sb33Extras->trailer_url }}" target="_blank" rel="noopener noreferrer">Watch trailer ↗</a></article>@endif
      </div>
    </section>
    @endif
    @if(!($adminPreview ?? false) && ($sb33Hosted || $sb33Desktop->isNotEmpty()))
    <section class="sb33-showcase-section" id="desktop" aria-label="Get this app for desktop"><div class="sb33-showcase-heading"><div><span class="sb33-kicker">DESKTOP & WEB</span><h2>Take your learning world with you.</h2></div></div>
       <div class="sb33-download-grid">
        @if($sb33Hosted)<a class="sb33-download" href="{{ route('studybuddy.w33.desktop.install', $app->slug) }}" target="_blank" rel="noopener"><strong>◈ Install on desktop</strong><span>Browser-installed app (PWA) • Chrome / Edge where supported</span></a>@endif
        @foreach($sb33Desktop as $package)
         <a class="sb33-download" href="{{ route('studybuddy.w33.desktop.download', ['slug' => $app->slug, 'platform' => $package->platform]) }}"><strong>↓ Download for {{ match($package->platform) {'mac' => 'macOS', 'windows' => 'Windows', default => 'Linux'} }}</strong><span>{{ $package->version ? 'v'.$package->version.' • ' : '' }}{{ round($package->bytes / 1024 / 1024, 1) }} MB • native installer package @if($package->sha256) • SHA-256: {{ substr($package->sha256, 0, 12) }}… @endif</span></a>
        @endforeach
       </div>
    </section>
    @endif
    <dialog class="sb33-lightbox" data-sb33-lightbox aria-label="Screenshot viewer"><form method="dialog"><button aria-label="Close screenshot viewer" class="sb33-lightbox-close">×</button></form><img alt=""><p></p></dialog>
</div>
<link rel="stylesheet" href="{{ asset('assets/studybuddy-w33/showcase.css') }}?v=33.1">
<script defer src="{{ asset('assets/studybuddy-w33/showcase.js') }}?v=33.1"></script>
