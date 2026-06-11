@extends('layouts.app')

@section('title', 'Rewards')

@section('content')
<section class="rewards-shell reveal-on-load" aria-labelledby="rewards-title">
    <aside class="reward-sidebar glass-panel">
        <strong>🐬 Profile</strong>
        <a href="{{ route('student.dashboard') }}">Dashboard</a>
        <a class="active" href="{{ route('rewards') }}">Buddy Rewards</a>
        <a href="{{ route('student.apps') }}">My Apps</a>
        <a href="{{ route('parent.dashboard') }}">Parent View</a>
        <a href="{{ route('teacher.dashboard') }}">Teacher View</a>
    </aside>
    <div class="buddy-customizer glass-panel">
        <div class="customizer-top">
            <div>
                <p class="eyebrow">Buddy Customization</p>
                <h1 id="rewards-title">Customize Your Buddy</h1>
                <p>Spend earned coins on costumes, badges, and magical trails.</p>
            </div>
            <div class="coin-display">🪙 320</div>
        </div>
        <div class="customizer-grid">
            <div class="buddy-preview tilt-card">
                @include('partials.image-placeholder', ['label' => 'BUDDY_CUSTOMIZATION_IMAGE', 'src' => 'assets/studybuddy/hero-dolphin-book.png', 'variant' => 'shop-buddy', 'caption' => 'Large dolphin Buddy customization render'])
                <div class="customizer-actions">
                    <a class="button button-ghost" href="{{ route('student.dashboard') }}">Back to Dashboard</a>
                    <button class="button" type="button">Save Changes</button>
                </div>
            </div>
            <div class="costume-grid" aria-label="Available rewards">
                @foreach($rewards as $reward)
                    @php
                        $isUnlocked = in_array($reward->rarity, ['unlocked', 'common'], true);
                    @endphp
                    <article class="costume-card tilt-card {{ $isUnlocked ? 'unlocked' : 'locked' }}" style="--reward-glow: {{ $reward->glow_color }}">
                        <span>{{ $reward->icon }}</span>
                        <h3>{{ $reward->name }}</h3>
                        <p>{{ $isUnlocked ? 'Unlocked' : $reward->points_required . ' coins needed' }}</p>
                        @if($isUnlocked)
                            <button class="reward-action" type="button">Equip</button>
                        @else
                            <button class="reward-action" type="button" disabled aria-disabled="true">Locked</button>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
