@extends('layouts.app')

@section('title', 'Rewards')

@section('content')
<section class="rewards-shell reveal-on-load">
    <aside class="reward-sidebar glass-panel">
        <strong>🐬 Profile</strong>
        <a>Profile</a><a class="active">Buddy</a><a>Costumes</a><a>Themes</a><a>Badges</a><a>Settings</a>
    </aside>
    <div class="buddy-customizer glass-panel">
        <div class="customizer-top">
            <div><p class="eyebrow">Buddy Customization</p><h1>Customize Your Buddy</h1></div>
            <div class="coin-display">🪙 320</div>
        </div>
        <div class="customizer-grid">
            <div class="buddy-preview tilt-card">
                @include('partials.image-placeholder', ['label' => 'BUDDY_CUSTOMIZATION_IMAGE', 'variant' => 'mascot', 'caption' => 'Large dolphin Buddy customization render'])
                <div class="customizer-actions"><button class="button button-ghost">Reset</button><button class="button">Save Changes</button></div>
            </div>
            <div class="costume-grid">
                @foreach($rewards as $reward)
                    <article class="costume-card tilt-card {{ $reward->rarity === 'locked' ? 'locked' : 'unlocked' }}" style="--reward-glow: {{ $reward->glow_color }}">
                        <span>{{ $reward->icon }}</span>
                        <h3>{{ $reward->name }}</h3>
                        <p>{{ $reward->rarity === 'locked' ? $reward->points_required.' coins' : 'Unlocked' }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
