@extends('layouts.studybuddy')

@section('content')
    @if($page)
        <section class="feature-section reveal-on-load">
            @if($page->title !== '')<h1>{{ $page->title }}</h1>@endif
            @if(filled($page->body))<div>{!! nl2br(e($page->body)) !!}</div>@endif
        </section>
    @endif
@endsection
