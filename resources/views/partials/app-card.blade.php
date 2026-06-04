<article class="app-card tone-{{ $app->card_tone ?? 'violet' }}">
    <div class="app-icon">✦</div>
    <div class="app-copy">
        <p class="eyebrow">{{ $app->subject }} · {{ $app->age_band }}</p>
        <h3>{{ $app->title }}</h3>
        <p>{{ $app->description }}</p>
        <div class="app-meta">
            <span>{{ $app->hero_metric }}</span>
            <span class="status">{{ ucfirst($app->status) }}</span>
        </div>
    </div>
    @if($app->launch_path)
        <a class="button small" href="{{ $app->launch_path }}">Launch</a>
    @else
        <span class="button small ghost">Coming soon</span>
    @endif
</article>
