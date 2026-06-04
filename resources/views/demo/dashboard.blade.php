@extends('layouts.app')

@section('title', $title)

@section('content')
<section class="section-pad dashboard-hero">
    <p class="eyebrow">{{ ucfirst($audience) }} demo</p>
    <h1>{{ $title }}</h1>
    <p class="lede">Glowing dashboard panels demonstrate the route and visual foundation before deeper backend workflows are added.</p>
</section>
<section class="section-pad dashboard-grid">
    @forelse($cards as $card)
        @include('partials.dashboard-card', ['card' => $card])
    @empty
        <article class="dashboard-card"><h3>Seed dashboard cards</h3><p>Run <code>php artisan db:seed</code> to load this demo.</p></article>
    @endforelse
</section>
@endsection
