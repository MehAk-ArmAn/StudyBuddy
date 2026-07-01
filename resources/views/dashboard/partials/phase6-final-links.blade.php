<section class="sb-final-dashboard-strip" aria-label="StudyBuddy final platform shortcuts">
    <div>
        <p>Final Platform Tools</p>
        <h2>Launchpad, points, roadmap, and readiness</h2>
    </div>
    <div class="sb-final-actions">
        <a href="{{ route('studybuddy.final.app-launchpad') }}" class="sb-final-btn">App Launchpad</a>
        <a href="{{ route('studybuddy.final.points-wallet') }}" class="sb-final-btn sb-final-btn-soft">Points Wallet</a>
        <a href="{{ route('studybuddy.final.roadmap') }}" class="sb-final-btn sb-final-btn-soft">Roadmap</a>
        <a href="{{ route('studybuddy.final.launch-readiness') }}" class="sb-final-btn sb-final-btn-soft">Readiness</a>
        @if(auth()->check() && (auth()->user()->is_admin ?? false))
            <a href="{{ route('studybuddy.admin.final.index') }}" class="sb-final-btn sb-final-btn-soft">Admin Cockpit</a>
        @endif
    </div>
</section>
