<div class="sb-locked-modal" data-sb-lock-modal hidden>
    <div class="sb-locked-backdrop" data-sb-lock-close></div>
    <section class="sb-locked-card" role="dialog" aria-modal="true" aria-labelledby="sbLockedTitle">
        <button class="sb-locked-x" type="button" data-sb-lock-close aria-label="Close">×</button>
        <p class="sb-apps-kicker">StudyBuddy Access</p>
        <h2 id="sbLockedTitle" data-sb-lock-title>Preview mode</h2>
        <p data-sb-lock-message>{{ $settings['locked_guest_message'] ?? 'Create a free StudyBuddy account to save quests, play sessions, and earn points.' }}</p>
        <div class="sb-locked-actions">
            @guest
                <a class="sb-app-btn" href="{{ route('register') }}">Create Account</a>
                <a class="sb-app-btn soft" href="{{ route('login') }}">Login</a>
            @else
                @if(Route::has('verification.notice'))<a class="sb-app-btn" href="{{ route('verification.notice') }}">Verify Email</a>@endif
                <a class="sb-app-btn soft" href="{{ route('dashboard') }}">Dashboard</a>
            @endguest
        </div>
    </section>
</div>
