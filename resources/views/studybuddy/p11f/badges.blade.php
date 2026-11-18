<section class="sb-final-panel" aria-labelledby="sbp11f-title">
    <h2 id="sbp11f-title">Your earned achievements</h2>
    <p>Hidden achievements reveal themselves after verified learning milestones. Your collection is private unless you choose to show a profile item.</p>
    @forelse($earnedBadges as $badge)
        <div class="sb-final-row"><strong>✦ {{ $badgeNames[$badge->code] ?? 'Special achievement' }}</strong><span>{{ \Illuminate\Support\Carbon::parse($badge->earned_at)->format('M j, Y') }}</span></div>
    @empty
        <p>No achievements yet. Try a verified Math Sprint and finish the questions.</p>
    @endforelse
    <p>✦ {{ $hiddenBadgeCount }} secret achievement(s) still waiting to be discovered.</p>
    <a href="{{ url('/learning/math-sprint') }}">Open Math Sprint</a>
</section>
