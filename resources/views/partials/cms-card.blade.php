@if(($card->is_enabled ?? true) && (($card->title ?? '') !== '' || ($card->body ?? '') !== '' || ($card->media_path ?? '') !== ''))
    <article class="app-card">
        @if(($card->media_path ?? '') !== '')
            @include('partials.cms-image', ['path' => $card->media_path, 'alt' => $card->title ?? ''])
        @endif
        @if(($card->title ?? '') !== '')<h3>{{ $card->title }}</h3>@endif
        @if(($card->body ?? '') !== '')<p>{{ $card->body }}</p>@endif
    </article>
@endif
