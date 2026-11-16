@extends('layouts.app')

@section('title', 'Search')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-living-platform.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-living-platform.css')) ? filemtime(public_path('assets/css/studybuddy-living-platform.css')) : time() }}">

<div class="sb-search-page">
    <section class="sb-search-hero">
        <p class="sb-living-kicker">Search StudyBuddy</p>
        <h1>Find apps, profiles, pages, and learning worlds.</h1>

        <form action="{{ route('studybuddy.search') }}" method="GET" class="sb-search-big">
            <input name="q" value="{{ $query }}" placeholder="Try math, profile, community, reading..." autofocus>
            <button type="submit">Search</button>
        </form>
    </section>

    <section class="sb-search-result-grid">
        @forelse($results as $item)
            <a href="{{ url($item['url']) }}" class="sb-search-result-card">
                @if(!empty($item['image']))
                    <img src="{{ $item['image'] }}" alt="">
                @else
                    <span>{{ $item['icon'] ?? '' }}</span>
                @endif
                <div>
                    <small>{{ $item['type'] ?? 'Result' }}</small>
                    <strong>{{ $item['title'] }}</strong>
                    <p>{{ $item['description'] ?? 'Open this StudyBuddy result.' }}</p>
                </div>
            </a>
        @empty
            <article class="sb-search-empty">
                <strong>No results yet</strong>
                <p>Try searching for apps, community, profile, dashboard, reading, math, focus, or quiz.</p>
            </article>
        @endforelse
    </section>
</div>
@endsection
