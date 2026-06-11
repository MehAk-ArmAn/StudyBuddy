@if($section && $section->is_enabled)
    <section class="feature-section reveal-on-load">
        @if($section->eyebrow !== '')<p class="eyebrow">{{ $section->eyebrow }}</p>@endif
        @if($section->title !== '')<h2>{{ $section->title }}</h2>@endif
        @if(filled($section->body))<p>{{ $section->body }}</p>@endif
        @if(filled($section->media_path))
            @include('partials.cms-image', ['path' => $section->media_path, 'alt' => $section->title])
        @endif
    </section>
@endif
