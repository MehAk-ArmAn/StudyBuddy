{{-- Existing Laravel homepage: same Apps CMS, no fabricated app cards or duplicate page. --}}
<section class="sb-live-catalogue" aria-labelledby="sb-live-catalogue-heading" data-studybuddy-live-catalogue>
    <div class="sb-live-catalogue__top">
        <div>
            <p class="sb-live-catalogue__eyebrow">FROM THE LIVE CATALOGUE</p>
            <h2 id="sb-live-catalogue-heading">Learning apps, <em>from our collection.</em></h2>
            <p>These are real listings from our app collection, not the illustrated world previews below.</p>
        </div>
        <div class="sb-live-catalogue__links">
            <a href="{{ route('studybuddy.apps') }}">All apps <span aria-hidden="true">↗</span></a>
            <a href="{{ route('studybuddy.w34.subjects.index') }}">Learning subjects <span aria-hidden="true">↗</span></a>
        </div>
    </div>
    @if($featuredApps->isNotEmpty())
        <div class="sb-live-catalogue__grid">
            @foreach($featuredApps as $app)
                <article class="sb-live-catalogue__card">
                    <a class="sb-live-catalogue__image" href="{{ route('studybuddy.apps.show', $app->slug) }}" aria-label="View {{ $app->name }}">
                        @php($artwork = $app->cardImage())
                        <img src="{{ $artwork && preg_match('~^https?://~i', $artwork) ? $artwork : ($artwork ? asset(ltrim($artwork, '/')) : asset('assets/studybuddy-control/apps.svg')) }}"
                             alt="" loading="lazy" decoding="async">
                    </a>
                    <div class="sb-live-catalogue__card-body">
                        <span class="sb-live-catalogue__category">{{ $app->category ?: 'Learning app' }}</span>
                        <h3><a href="{{ route('studybuddy.apps.show', $app->slug) }}">{{ $app->name }}</a></h3>
                        <p>{{ \Illuminate\Support\Str::limit($app->preview_text ?: $app->description ?: 'Explore the app details and learning goals.', 105) }}</p>
                        <div class="sb-live-catalogue__card-bottom">
                            <span>{{ $app->isAvailable() ? 'Available now' : 'Details available' }}</span>
                            <a href="{{ route('studybuddy.apps.show', $app->slug) }}">View app ↗</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <p class="sb-live-catalogue__empty">No published learning apps just yet. The illustrated worlds below are previews; real games will appear here when they are published.</p>
    @endif
</section>
