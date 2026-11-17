<section class="sb-learning-snapshot" aria-labelledby="sb-learning-snapshot-heading">
    <div class="sb-learning-snapshot__head">
        <div>
            <p class="sb-learning-snapshot__eyebrow">YOUR PRIVATE LEARNING STORY</p>
            <h2 id="sb-learning-snapshot-heading">What you've actually explored.</h2>
            <p>App visits and graded puzzles are different things. Here's what StudyBuddy has really recorded for you.</p>
        </div>
        @if($snapshot['available'])
            <a class="sb-learning-snapshot__link" href="{{ route('studybuddy.learning.activity') }}">Activity journal <span aria-hidden="true">↗</span></a>
        @endif
    </div>

    @if(! $snapshot['available'])
        <div class="sb-learning-snapshot__empty">Learning history is not enabled on this installation yet. Your existing apps and dashboard still work.</div>
    @else
        <div class="sb-learning-snapshot__metrics" aria-label="Recorded learning summary">
            <article><strong>{{ number_format($snapshot['appVisits']) }}</strong><span>Connected app visits</span></article>
            <article><strong>{{ number_format($snapshot['savedVisits']) }}</strong><span>Saved app visits</span></article>
            <article><strong>{{ number_format($snapshot['gradedRounds']) }}</strong><span>Graded Math Sprint rounds</span></article>
            <article><strong>{{ number_format($snapshot['earnedBadges']) }}</strong><span>Earned Math Sprint badges</span></article>
        </div>
        <div class="sb-learning-snapshot__columns">
            <div class="sb-learning-snapshot__panel">
                <div class="sb-learning-snapshot__panel-head"><h3>Recent learning activity</h3><span>Private</span></div>
                @forelse($snapshot['recentActivity'] as $activity)
                    <div class="sb-learning-snapshot__entry">
                        <strong>{{ $activity->app_name }}</strong>
                        <small>{{ \Illuminate\Support\Carbon::parse($activity->started_at)->format('j M Y') }} · {{ match($activity->status) { 'finished' => 'Visit saved', 'active' => 'In progress', 'left' => 'Visit left', default => 'Recorded' } }}</small>
                    </div>
                @empty
                    <p class="sb-learning-snapshot__empty">No recorded learning activity yet. Explore an app to begin.</p>
                @endforelse
            </div>
            <div class="sb-learning-snapshot__panel">
                <div class="sb-learning-snapshot__panel-head"><h3>Real graded results</h3><span>Math Sprint</span></div>
                @forelse($snapshot['recentResults'] as $result)
                    <a class="sb-learning-snapshot__entry sb-learning-snapshot__entry--clickable" href="{{ route('studybuddy.w32.result', $result->id) }}">
                        <strong>{{ ucfirst($result->level) }} · {{ $result->score }} / {{ $result->total }} correct</strong>
                        <small>{{ \Illuminate\Support\Carbon::parse($result->submitted_at)->format('j M Y') }} · Server-graded result ↗</small>
                    </a>
                @empty
                    <p class="sb-learning-snapshot__empty">No graded Math Sprint results yet. App visits don't count as test scores.</p>
                @endforelse
            </div>
        </div>
        @if($snapshot['badges']->isNotEmpty())
            <div class="sb-learning-snapshot__badges" aria-label="Earned Math Sprint badges">
                <strong>Badges you've earned</strong>
                @foreach($snapshot['badges'] as $badge)
                    <span class="sb-learning-snapshot__badge">✦ {{ $badge->label }}</span>
                @endforeach
            </div>
        @endif
        <div class="sb-learning-snapshot__foot">
            <span>Visits are not proof of skill mastery, and these records do not award points.</span>
            <div>
                @if($snapshot['insightsReady'])<a href="{{ route('studybuddy.connected.insights') }}">More insights ↗</a>@endif
                <a href="{{ route('studybuddy.w32.index') }}">Play Math Sprint ↗</a>
            </div>
        </div>
    @endif
</section>
