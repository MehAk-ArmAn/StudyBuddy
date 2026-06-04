@extends('layouts.app')

@section('title', 'Apps')

@section('content')
<section class="playstore-page reveal-on-load">
    <aside class="left-rail glass-panel">
        <a class="rail-logo" href="{{ route('home') }}">@include('partials.image-placeholder', ['label' => 'LOGO_ICON', 'src' => 'assets/studybuddy/logo-icon.png', 'variant' => 'logo', 'caption' => 'Logo']) <b>StudyBuddy</b></a>
        <nav><a class="active">Apps</a><a>Popular</a><a>Primary</a><a>Secondary</a><a>New</a><a>Rewards</a></nav>
    </aside>
    <div class="playstore-panel glass-panel">
        <div class="store-topline"><div><p class="eyebrow">02 Apps Store (Playstore Style)</p><h1>StudyBuddy Apps</h1></div><label class="search-bar">⌕ <input type="search" placeholder="Search apps..." aria-label="Search apps"></label></div>
        <div class="filter-pills"><button class="active">All</button><button>Popular</button><button>Primary (6–10)</button><button>Secondary (7–11)</button><button>New</button></div>
        <div class="store-grid">
            @foreach($apps as $app)
                @include('partials.app-card', ['app' => $app])
            @endforeach
        </div>
    </div>
</section>
@endsection
