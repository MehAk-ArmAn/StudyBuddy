@extends('layouts.app')

@section('title', 'Play Math Quest')

@section('content')
<section class="game-shell reveal-on-load">
    <div class="game-hud glass-panel">
        <span>XP <strong>2,350</strong></span>
        <span>Coins <strong>320</strong></span>
        <span>Streak <strong>7 🔥</strong></span>
    </div>
    <div class="game-board glass-panel">
        <div class="question-card tilt-card">
            <p class="eyebrow">Math Quest · Asteroid Gate</p>
            <h1>8 × 7 = ?</h1>
            <p>Choose the correct answer to power Buddy’s rocket portal.</p>
            <div class="answer-grid">
                <button data-correct="false">54</button>
                <button data-correct="true">56</button>
                <button data-correct="false">64</button>
                <button data-correct="false">72</button>
            </div>
            <p class="answer-feedback" aria-live="polite">Pick an answer to begin.</p>
        </div>
        <aside class="buddy-game-card tilt-card">
            @include('partials.image-placeholder', ['label' => 'MATH_QUEST_BUDDY_IMAGE', 'variant' => 'mascot', 'caption' => 'Buddy game helper'])
            <h2>Buddy Boost</h2>
            <p>Tip: multiplication is repeated groups. Eight groups of seven stars makes fifty-six.</p>
        </aside>
    </div>
</section>
@endsection
