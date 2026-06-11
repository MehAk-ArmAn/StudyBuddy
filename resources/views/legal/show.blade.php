@extends('layouts.studybuddy')

@section('content')
    @if($legalPage)
        <section class="feature-section reveal-on-load">
            @if($legalPage->title !== '')<h1>{{ $legalPage->title }}</h1>@endif
            @if(filled($legalPage->body))<div>{!! nl2br(e($legalPage->body)) !!}</div>@endif
        </section>
    @endif
@endsection
