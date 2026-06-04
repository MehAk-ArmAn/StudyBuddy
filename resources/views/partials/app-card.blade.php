<article class="app-store-card tilt-card tone-{{ $app->card_tone ?? 'violet' }}">
    @include('partials.image-placeholder', ['label' => $app->image_label ?? 'APP_CARD_IMAGE', 'variant' => 'app', 'caption' => $app->title.' artwork'])
    <div class="app-store-card-body">
        <h3>{{ $app->title }}</h3>
        <p>{{ $app->description }}</p>
        <div class="card-meta"><span>⭐ {{ $app->hero_metric }}</span><span>{{ $app->age_band }}</span></div>
        @if($app->launch_path)
            <a class="button button-compact" href="{{ $app->launch_path }}">Start</a>
        @else
            <button class="button button-compact" type="button">Start</button>
        @endif
    </div>
</article>
