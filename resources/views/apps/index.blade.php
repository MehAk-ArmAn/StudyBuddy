@extends('layouts.app')

@section('title', 'Apps')

@section('content')
<section class="section-pad">
    <div class="section-heading wide">
        <p class="eyebrow">StudyBuddy app constellation</p>
        <h1>Choose a mission from a polished app store for learning.</h1>
        <p>Each card is ready for richer backend logic later while all routes load immediately today.</p>
    </div>
    <div class="app-grid app-grid-large">
        @forelse($apps as $app)
            @include('partials.app-card', ['app' => $app])
        @empty
            <p class="empty-state">Run <code>php artisan db:seed</code> to load demo apps.</p>
        @endforelse
    </div>
</section>
@endsection
