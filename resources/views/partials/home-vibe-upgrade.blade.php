<section class="sb-vibe-upgrade" data-vibe-upgrade>
    <div class="sb-vibe-shell">
        <div class="sb-vibe-head">
            <div>
                <p class="sb-vibe-kicker">Choose your vibe</p>
                <h2>Profiles that feel like your learning personality.</h2>
                <p>Users can build a StudyBuddy profile that shows favourite app worlds, colours, badges, progress style, and safe public showcase settings.</p>
                <div class="sb-vibe-actions">
                    <a href="{{ url('/profile') }}">Customize profile</a>
                    <a class="soft" href="{{ url('/community') }}">See community</a>
                </div>
            </div>
            <div class="sb-vibe-visual">
                <img data-vibe-image src="https://github.com/MehAk-ArmAn/StudyBuddy-Imgs/blob/main/hero/hero-dolphin-book.png?raw=true" alt="StudyBuddy profile visual">
            </div>
        </div>

        <div class="sb-vibe-tabs" aria-label="Profile vibe options">
            <button type="button" class="sb-vibe-tab is-active" data-vibe-tab="learner"><span></span><strong>Learner</strong><small>Profile, goals, app worlds</small></button>
            <button type="button" class="sb-vibe-tab" data-vibe-tab="parent"><span></span><strong>Parent</strong><small>Safe support dashboard</small></button>
            <button type="button" class="sb-vibe-tab" data-vibe-tab="teacher"><span></span><strong>Teacher</strong><small>Classes and assignments</small></button>
            <button type="button" class="sb-vibe-tab" data-vibe-tab="independent"><span></span><strong>Independent</strong><small>Self-paced growth</small></button>
            <button type="button" class="sb-vibe-tab" data-vibe-tab="rewards"><span></span><strong>Rewards</strong><small>Badges and points</small></button>
        </div>

        <div class="sb-vibe-panel">
            <div>
                <h3 data-vibe-title>Learner Profile</h3>
                <p data-vibe-text>Build a profile around favourite app worlds, learning goals, progress style, badges, colours, and public showcase settings.</p>
            </div>
            <div class="sb-vibe-actions">
                <a href="{{ url('/profile') }}">Open Profile Studio</a>
                <a class="soft" href="{{ url('/roles') }}">How roles work</a>
            </div>
        </div>
    </div>
</section>
