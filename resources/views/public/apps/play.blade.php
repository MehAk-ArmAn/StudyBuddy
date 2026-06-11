@extends('layouts.studybuddy')

@section('content')
    @if($app)
        <section class="hero-section reveal-on-load">
            @if($app->title !== '')<h1>{{ $app->title }}</h1>@endif
            @if($app->launch_path)
                <iframe src="{{ $app->launch_path }}" title="{{ $app->title }}" loading="lazy"></iframe>
            @endif
        </section>
    @endif
@endsection
