@extends('layouts.app')

@section('content')
<main class="sb-final-shell">
    <section class="sb-final-hero compact">
        <div>
            <p class="sb-final-kicker">StudyBuddy Rewards</p>
            <h1>My Points Wallet</h1>
            <p>{{ $settings['points_policy'] ?? 'Earn points from quests, apps, and learning sessions.' }}</p>
        </div>
        <div class="sb-final-orb-card"><span>⭐</span><strong>{{ $total }}</strong><p>current points</p></div>
    </section>

    <section class="sb-final-stats">
        <article><strong>{{ $earned }}</strong><span>Total earned</span></article>
        <article><strong>{{ $spent }}</strong><span>Adjusted/spent</span></article>
        <article><strong>{{ $transactions->count() }}</strong><span>Recent records</span></article>
    </section>

    <section class="sb-final-panel">
        <h2>Recent point activity</h2>
        @forelse($transactions as $transaction)
            <div class="sb-final-row">
                <div><strong>{{ $transaction->title }}</strong><span>{{ $transaction->created_at->diffForHumans() }}</span></div>
                <b class="{{ $transaction->points >= 0 ? 'positive' : 'negative' }}">{{ $transaction->points >= 0 ? '+' : '' }}{{ $transaction->points }}</b>
            </div>
        @empty
            <p>No points yet. Start a web session or complete a quest to begin.</p>
        @endforelse
    </section>
</main>
@endsection
