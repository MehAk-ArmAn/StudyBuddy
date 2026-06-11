@extends('layouts.studybuddy')

@section('content')
    @if($page)
        <section class="feature-section reveal-on-load">
            @if($page->title !== '')<h1>{{ $page->title }}</h1>@endif
        </section>
    @endif
@endsection
