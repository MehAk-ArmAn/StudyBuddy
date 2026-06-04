@extends('layouts.app')

@section('title', 'Showcase')

@section('content')
<section class="section-pad showcase-hero">
    <p class="eyebrow">Design target</p>
    <h1>Dark navy galaxy, purple/cyan glow, 3D glass, planets, comets, and dashboards.</h1>
    <p class="lede">The showcase proves the visual language is product-specific: polished learning interfaces, mascot charm, premium motion, and cosmic atmosphere.</p>
</section>

<section class="section-pad showcase-grid">
    @forelse($contentBlocks as $block)
        <article class="glass-card showcase-card">
            <p class="eyebrow">{{ $block->metadata['theme'] ?? 'StudyBuddy' }}</p>
            <h2>{{ $block->title }}</h2>
            <p>{{ $block->body }}</p>
        </article>
    @empty
        <article class="glass-card showcase-card"><h2>Seed showcase content</h2><p>Run the demo seeders to populate this orbit.</p></article>
    @endforelse
    <article class="glass-card planet-lab">
        <span class="planet-preview"></span>
        <h2>Floating planets and globs</h2>
        <p>Atmospheric objects are layered in CSS so the brand can evolve without relying on stock templates.</p>
    </article>
</section>
@endsection
