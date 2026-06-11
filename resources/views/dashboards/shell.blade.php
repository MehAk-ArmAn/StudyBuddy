@extends('layouts.studybuddy')

@section('content')
    @if($page)
        <section class="feature-section reveal-on-load">
            @if($page->title !== '')<h1>{{ $page->title }}</h1>@endif
        </section>
        @if(($widgets ?? collect())->isNotEmpty())
            <section class="app-grid reveal-on-load">
                @foreach($widgets as $widget)
                    @include('partials.cms-card', ['card' => (object) ['is_enabled' => $widget->is_enabled, 'title' => $widget->title ?: $widget->label, 'body' => $widget->description ?: $widget->value, 'media_path' => $widget->icon_path]])
                @endforeach
            </section>
        @endif
    @endif
@endsection
