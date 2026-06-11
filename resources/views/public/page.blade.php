@extends('layouts.studybuddy')

@section('content')
    @if($page)
        @foreach($sections as $section)
            @include('partials.cms-section', ['section' => $section])
        @endforeach

        @if(($stats ?? collect())->isNotEmpty())
            <section class="stats-grid reveal-on-load">
                @foreach($stats as $stat)
                    @include('partials.cms-stat-card', ['stat' => $stat])
                @endforeach
            </section>
        @endif
    @endif
@endsection
