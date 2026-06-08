<article class="store-app-card tilt-card tone-{{ $app->card_tone ?? 'violet' }}">
    @include('partials.image-placeholder', ['label' => $app->image_label ?? 'APP_CARD_IMAGE', 'src' => $app->image_path ?? null, 'variant' => 'app', 'caption' => $app->title.' app artwork'])
    <div class="store-app-copy">
        <h3>{{ $app->title }}</h3>
        <p>{{ $app->description }}</p>
        <div class="rating-line"><span>{{ $app->hero_metric }}</span><span>{{ $app->age_band }}</span></div>
        @if($app->launch_path)
            <a class="mini-button" href="{{ $app->launch_path }}">Start</a>
        @else
            <button class="mini-button" type="button">Start</button>
        @endif
    </div>
</article>
