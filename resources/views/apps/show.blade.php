@extends('layouts.studybuddy')

@section('content')
    @if($app)
        <section class="hero-section reveal-on-load">
            @if($app->title !== '')<h1>{{ $app->title }}</h1>@endif
            @if(filled($app->description))<p>{{ $app->description }}</p>@endif
            @include('partials.cms-image', ['path' => $app->image_path, 'alt' => $app->title])
            @if($app->start_button_label !== '')
                <a class="button" href="{{ route('apps.play', $app->slug) }}">{{ $app->start_button_label }}</a>
            @endif
        </section>
        @if($features->isNotEmpty())
            <section class="app-grid reveal-on-load">
                @foreach($features as $feature)
                    @include('partials.cms-card', ['card' => (object) ['is_enabled' => $feature->is_enabled, 'title' => $feature->title, 'body' => $feature->body, 'media_path' => $feature->image_path]])
                @endforeach
            </section>
        @endif
    @endif
@endsection
