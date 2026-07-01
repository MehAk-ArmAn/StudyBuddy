<section class="sb-final-links-card">
    <div>
        <p class="sb-final-kicker">StudyBuddy Platform</p>
        <h2>Apps, points, downloads, and web play</h2>
        <p>The unified Apps page is now the single source for every mini-app. Preview as guest, play when verified, and manage everything from admin.</p>
    </div>
    <div class="sb-final-actions">
        <a class="sb-final-btn" href="{{ route('pages.apps') }}">Open Apps</a>
        @if(Route::has('studybuddy.final.points-wallet'))<a class="sb-final-btn sb-final-btn-soft" href="{{ route('studybuddy.final.points-wallet') }}">Points Wallet</a>@endif
        @if(Route::has('studybuddy.final.roadmap'))<a class="sb-final-btn sb-final-btn-soft" href="{{ route('studybuddy.final.roadmap') }}">Roadmap</a>@endif
    </div>
</section>
