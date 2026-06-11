@extends('layouts.studybuddy')

@section('content')
    @if($app)
        <section class="hero-section reveal-on-load">
            @if($app->title !== '')<h1>{{ $app->title }}</h1>@endif
            @if($app->web_embed_url)
                <iframe src="{{ $app->web_embed_url }}" title="{{ $app->title }}" loading="lazy"></iframe>
            @elseif(filled($app->web_embed_empty_message))
                <p>{{ $app->web_embed_empty_message }}</p>
            @endif
        </section>
    @endif
@endsection
