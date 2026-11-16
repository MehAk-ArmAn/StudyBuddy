<section class="sb-final-links-card">
    <div>
        <p class="sb-final-kicker">StudyBuddy Platform</p>
        <h2>Apps, points, downloads, and web play</h2>
        <p>Explore available learning apps, open browser activities, and keep your points and quests together.</p>
    </div>
    <div class="sb-final-actions">
        <a class="sb-final-btn" href="{{ url('/apps') }}">Open Apps</a>
        @if(Route::has('studybuddy.final.points-wallet'))<a class="sb-final-btn sb-final-btn-soft" href="{{ route('studybuddy.final.points-wallet') }}">Points Wallet</a>@endif
    </div>
</section>
