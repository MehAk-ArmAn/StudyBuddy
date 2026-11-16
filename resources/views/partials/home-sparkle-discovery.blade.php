{{-- The entries below are loaded from the existing StudyBuddy apps CMS. --}}
@if(isset($discoveryApps) && $discoveryApps->isNotEmpty())
@php
    $topics = $discoveryApps->pluck('category')->filter()->unique()->sort()->values();
@endphp
<section class="sb-glow-discovery" data-sb-discovery aria-labelledby="sb-glow-title">
    <img class="sb-v6-compass-art" src="{{ asset('assets/studybuddy-v6/learning-portal.webp') }}" alt="" aria-hidden="true" loading="lazy">
    <div class="sb-glow-discovery__halo" aria-hidden="true">✦</div>
    <div class="sb-glow-discovery__heading">
        <div>
            <p class="sb-glow-eyebrow"><span aria-hidden="true">✦</span> YOUR CURIOSITY COMPASS</p>
            <h2 id="sb-glow-title">Find a little <em>wonder.</em></h2>
            <p>Pick a topic, discover what’s in the catalogue, or let the compass surprise you. Every card opens the real StudyBuddy details page.</p>
        </div>
        <div class="sb-glow-discovery__controls">
            <button type="button" class="sb-glow-surprise" data-sb-surprise>
                <span aria-hidden="true">✧</span> Surprise me
            </button>
            <a href="{{ route('studybuddy.apps') }}" class="sb-glow-all">All learning worlds <span aria-hidden="true">↗</span></a>
        </div>
    </div>
    <div class="sb-glow-topics" role="group" aria-label="Choose a learning topic">
        <button type="button" class="is-selected" data-sb-topic="all" aria-pressed="true">All worlds</button>
        @foreach($topics as $topic)
            <button type="button" data-sb-topic="{{ \Illuminate\Support\Str::lower((string) $topic) }}" aria-pressed="false">{{ $topic }}</button>
        @endforeach
    </div>
    <div class="sb-glow-discovery__grid" data-sb-grid>
        @foreach($discoveryApps as $index => $app)
            @php($isDemo = str_starts_with((string) $app->slug, 'demo-next-'))
            @php($colors = $app->accentColors())
            <a class="sb-glow-card" data-sb-discovery-card data-topic="{{ \Illuminate\Support\Str::lower((string) $app->category) }}" href="{{ route('studybuddy.apps.show', $app->slug) }}" style="--glow-a:{{ $colors[0] }};--glow-b:{{ $colors[1] }}">
                <div class="sb-glow-card__art">
                    @if($app->cardImage())
                        <img src="{{ preg_match('/^https?:\/\//i', $app->cardImage()) ? $app->cardImage() : asset(ltrim($app->cardImage(), '/')) }}" alt="" loading="lazy" decoding="async" onerror="this.remove()">
                    @else
                        <span class="sb-glow-card__fallback" aria-hidden="true">{{ $app->icon ?: $app->initials() }}</span>
                    @endif
                    <span class="sb-glow-card__glimmer" aria-hidden="true">✦</span>
                    @if($isDemo)<span class="sb-glow-card__badge">DEMO · NOT PLAYABLE</span>@elseif($app->isAvailable())<span class="sb-glow-card__badge">AVAILABLE</span>@else<span class="sb-glow-card__badge">PREVIEW</span>@endif
                </div>
                <div class="sb-glow-card__info"><small>{{ $app->category }}</small><h3>{{ $app->name }}</h3><p>{{ $app->tagline ?: 'Explore this learning world.' }}</p><span>See details <span aria-hidden="true">↗</span></span></div>
            </a>
        @endforeach
    </div>
    <p class="sb-glow-discovery__status" data-sb-discovery-status role="status" aria-live="polite">Showing {{ $discoveryApps->count() }} learning world previews.</p>
    @if($discoveryApps->contains(fn($app) => str_starts_with((string) $app->slug, 'demo-next-')))
        <p class="sb-glow-demo-note">✦ Some cards are local-only fictional design previews — no game, download or reward is available for them.</p>
    @endif
</section>
@endif
