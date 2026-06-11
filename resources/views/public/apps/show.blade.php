@extends('layouts.studybuddy')

@section('content')
    @if($app)
        <section class="hero-section reveal-on-load">
            @if($app->title !== '')<h1>{{ $app->title }}</h1>@endif
            @if($app->description !== '')<p>{{ $app->description }}</p>@endif
            @include('partials.cms-image', ['path' => $app->image_path, 'alt' => $app->title])
        </section>
    @endif
@endsection
