@extends('layouts.studybuddy')

@section('content')
    @foreach($sections as $section)
        @include('partials.cms-section', ['section' => $section])
    @endforeach

    @if($rewards->isNotEmpty())
        <section class="app-grid reveal-on-load">
            @foreach($rewards as $reward)
                @include('partials.cms-card', ['card' => (object) ['is_enabled' => true, 'title' => $reward->name, 'body' => $reward->description, 'media_path' => $reward->image_path]])
            @endforeach
        </section>
    @endif
@endsection
