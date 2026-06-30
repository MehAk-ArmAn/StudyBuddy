@if(\Illuminate\Support\Facades\Route::has('studybuddy.command-center'))
<section class="sb-command-mini-card" aria-label="StudyBuddy Command Center shortcut">
    <div>
        <span class="sb-command-mini-kicker">Phase 4</span>
        <h2>Open your StudyBuddy Command Center</h2>
        <p>Track quests, streaks, weekly goals, profile readiness, and today’s mission in one premium dashboard.</p>
    </div>
    <a href="{{ route('studybuddy.command-center') }}" class="sb-command-mini-btn">Open Command Center</a>
</section>
@endif
