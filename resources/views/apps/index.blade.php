@extends('layouts.app')

@section('title', 'Apps')

@section('content')
<section class="apps-shell reveal-on-load">
    <aside class="app-sidebar glass-panel">
        <a class="side-logo" href="{{ route('home') }}"><span>🐬</span> StudyBuddy</a>
        <nav>
            <a class="active" href="{{ route('apps.index') }}">✦ Discover</a>
            <a href="{{ route('apps.math-quest') }}">⊕ Math</a>
            <a href="{{ route('demo.primary') }}">☁ Primary</a>
            <a href="{{ route('demo.secondary') }}">⬡ Secondary</a>
            <a href="{{ route('rewards') }}">★ Rewards</a>
        </nav>
    </aside>

    <div class="apps-content glass-panel">
        <div class="apps-topbar">
            <div>
                <p class="eyebrow">Apps Store (Playstore Style)</p>
                <h1>Choose your next learning adventure.</h1>
            </div>
            <label class="search-bar">⌕ <input type="search" placeholder="Search apps..." aria-label="Search apps"></label>
        </div>
        <div class="filter-pills">
            <button class="active">All</button><button>Popular</button><button>Primary (6-10)</button><button>Secondary (7-11)</button><button>New</button>
        </div>
        <div class="app-store-grid">
            @foreach($apps as $app)
                @include('partials.app-card', ['app' => $app])
            @endforeach
        </div>
    </div>
</section>
@endsection
