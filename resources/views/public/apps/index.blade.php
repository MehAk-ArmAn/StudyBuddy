@extends('layouts.studybuddy')

@section('content')
    @foreach($sections as $section)
        @include('partials.cms-section', ['section' => $section])
    @endforeach

    @if($apps->isNotEmpty())
        <section class="app-grid reveal-on-load">
            @foreach($apps as $app)
                @include('partials.cms-card', ['card' => (object) ['is_enabled' => true, 'title' => $app->title, 'body' => $app->description, 'media_path' => $app->image_path]])
            @endforeach
        </section>
    @endif
@endsection
