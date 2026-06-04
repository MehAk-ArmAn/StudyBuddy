@extends('layouts.app')

@section('title', 'Math Quest')

@section('content')
<section class="section-pad hero-grid compact">
    <div>
        <p class="eyebrow">Numeracy adventure</p>
        <h1>{{ $app?->title ?? 'Math Quest' }}</h1>
        <p class="lede">{{ $app?->description ?? 'Pilot Buddy through asteroid equations and collect star fragments.' }}</p>
        <a class="button" href="{{ route('apps.math-quest.play') }}">Play prototype</a>
    </div>
    <div class="glass-card quest-map">
        <span>① Number Nebula</span>
        <span>② Fraction Rings</span>
        <span>③ Algebra Asteroids</span>
        <span>④ Boss Portal</span>
    </div>
</section>
@endsection
