@extends('layouts.app')

@section('title', 'Rewards')

@section('content')
<section class="section-pad rewards-hero">
    <p class="eyebrow">Cosmic reward economy</p>
    <h1>Badges, trails, crowns, and keys that feel collectible.</h1>
</section>
<section class="section-pad reward-grid">
    @forelse($rewards as $reward)
        <article class="reward-card" style="--reward-glow: {{ $reward->glow_color }}">
            <span class="reward-icon">{{ $reward->icon }}</span>
            <p class="eyebrow">{{ ucfirst($reward->rarity) }} · {{ $reward->points_required }} points</p>
            <h2>{{ $reward->name }}</h2>
            <p>{{ $reward->description }}</p>
        </article>
    @empty
        <p class="empty-state">Run <code>php artisan db:seed</code> to load demo rewards.</p>
    @endforelse
</section>
@endsection
