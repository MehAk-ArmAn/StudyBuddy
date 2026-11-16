@extends('layouts.app')

@section('title', $pageData['title'])

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-info-pages.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-info-pages.css')) ? filemtime(public_path('assets/css/studybuddy-info-pages.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-info-pages.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-info-pages.js')) ? filemtime(public_path('assets/js/studybuddy-info-pages.js')) : time() }}" defer></script>

<div class="sb-info-page sb-info-{{ $pageData['slug'] }}">
    <section class="sb-info-hero" data-info-card>
        <p class="sb-info-kicker">{{ $pageData['eyebrow'] }}</p>
        <h1>{{ $pageData['title'] }}</h1>
        <p>{{ $pageData['subtitle'] }}</p>

        @if($pageData['body'])
            <div class="sb-info-hero-body">{{ $pageData['body'] }}</div>
        @endif

        <div class="sb-info-actions">
            <a href="{{ url('/apps') }}">Explore apps</a>
            <a class="soft" href="{{ url('/roles') }}">How roles work</a>
        </div>
    </section>

    <section class="sb-info-section-grid">
        @foreach($sections as $section)
            <article class="sb-info-card" data-info-card>
                @if(!empty($section['eyebrow']))
                    <p class="sb-info-kicker">{{ $section['eyebrow'] }}</p>
                @endif

                <h2>{{ $section['title'] }}</h2>

                @if(!empty($section['subtitle']))
                    <p class="lead">{{ $section['subtitle'] }}</p>
                @endif

                @if(!empty($section['body']))
                    <p>{{ $section['body'] }}</p>
                @endif

                @if(!empty($section['settings']['bullets']) && is_array($section['settings']['bullets']))
                    <ul class="sb-info-list">
                        @foreach($section['settings']['bullets'] as $bullet)
                            <li>{{ $bullet }}</li>
                        @endforeach
                    </ul>
                @endif

                @if(!empty($section['button_label']) && !empty($section['button_url']))
                    <a class="sb-info-mini-link" href="{{ url($section['button_url']) }}">{{ $section['button_label'] }}</a>
                @endif
            </article>
        @endforeach
    </section>

    <section class="sb-info-final-cta" data-info-card>
        <p class="sb-info-kicker">StudyBuddy</p>
        <h2>Learn. Play. Grow. Your Way.</h2>
        <p>Small learning games that treat kids like they are clever, because they are.</p>
        <div class="sb-info-actions">
            <a href="{{ url('/community') }}">Open community</a>
            <a class="soft" href="{{ url('/contact') }}">Contact StudyBuddy</a>
        </div>
    </section>
</div>
@endsection
