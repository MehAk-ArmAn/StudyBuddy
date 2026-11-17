{{-- W31: connected learner-owned visit journal; not a gameplay score or rewards system. --}}
@auth
@if(($canLaunch ?? false) && !($adminPreview ?? false))
<section class="sb31-player" data-sb31-player
    data-app-slug="{{ $app->slug }}"
    data-start-url="{{ route('studybuddy.connected.start') }}"
    data-session-prefix="{{ url('/learning/connected') }}"
    data-csrf="{{ csrf_token() }}">
    <div class="sb31-player__orb" aria-hidden="true"><span>✦</span></div>
    <div class="sb31-player__text">
        <span class="sb31-player__eyebrow">✧ CONNECTED TO YOUR PRIVATE LEARNING JOURNAL</span>
        <h2>Your learning journey, together.</h2>
        <p class="sb31-player__message" data-sb31-message role="status" aria-live="polite">
            When the app opens, StudyBuddy can record this visit. Save it when you're done.
        </p>
        <small>Visits and browser activity are estimates—not verified game scores, learning outcomes or points.</small>
    </div>
    <div class="sb31-player__actions">
        <div class="sb31-player__pulse" data-sb31-state><span class="sb31-player__pulse-dot"></span> Ready to connect</div>
        <button class="sb31-player__finish" type="button" data-sb31-finish disabled>Save visit ✦</button>
        <a href="{{ route('studybuddy.connected.insights') }}">My learning insights ↗</a>
        <button class="sb31-player__leave" type="button" data-sb31-leave hidden>Close without saving</button>
    </div>
</section>
@endif
@endauth
