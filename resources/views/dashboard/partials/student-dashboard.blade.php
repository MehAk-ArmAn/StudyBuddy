<section class="role-grid">
    @include('dashboard.partials.learner-connect-code')

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Ready to play</p>
                <h2>Pick an app</h2>
            </div>
            <a href="{{ url('/apps') }}">All apps</a>
        </div>

        <div class="role-app-grid">
            @forelse($recommendedApps as $app)
                <a href="{{ url('/apps/'.$app->slug) }}">
                    <span aria-hidden="true">{{ $app->icon ?: mb_strtoupper(mb_substr($app->name, 0, 1)) }}</span>
                    <strong>{{ $app->name }}</strong>
                    <small>{{ $app->tagline ?: $app->category }}</small>
                </a>
            @empty
                <div class="empty-role-panel">
                    <strong>No apps here yet.</strong>
                    New activities will show up as soon as they're ready.
                </div>
            @endforelse
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Set by a teacher</p>
        <h2>Your tasks</h2>

        <div class="role-list">
            @forelse($assignments as $assignment)
                <article>
                    <span aria-hidden="true">{{ strtoupper(substr($assignment->type ?? 'T', 0, 1)) }}</span>
                    <div>
                        <strong>{{ $assignment->title }}</strong>
                        <small>{{ $assignment->due_at ? 'Due '.$humanDate($assignment->due_at) : 'No deadline' }}</small>
                    </div>
                </article>
            @empty
                <div class="empty-role-panel">
                    <strong>Nothing assigned right now.</strong>
                    Choose an app whenever you're ready.
                </div>
            @endforelse
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Points</p>
        <h2>What you've earned</h2>

        <div class="role-list">
            @forelse($recentPoints as $item)
                <article>
                    <span aria-hidden="true">{{ ($item->points ?? 0) >= 0 ? '+' : '' }}{{ $item->points ?? 0 }}</span>
                    <div>
                        <strong>{{ $item->label ?? $item->reason ?? 'StudyBuddy activity' }}</strong>
                        <small>{{ $humanDate($item->created_at ?? null) ?: 'Recently' }}</small>
                    </div>
                </article>
            @empty
                <div class="empty-role-panel">
                    <strong>No points yet.</strong>
                    Finish an activity and they'll show up here.
                </div>
            @endforelse
        </div>
    </article>

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Your account</p>
                <h2>Settings and pages</h2>
            </div>
        </div>

        <div class="role-control-grid">
            <a href="{{ url('/profile') }}">
                <span aria-hidden="true">P</span>
                <strong>Edit profile</strong>
                <small>Photo, colours and privacy</small>
            </a>
            <a href="{{ url('/community') }}">
                <span aria-hidden="true">C</span>
                <strong>Community</strong>
                <small>See other public profiles</small>
            </a>
            <a href="{{ url('/search') }}">
                <span aria-hidden="true">⌕</span>
                <strong>Search</strong>
                <small>Find apps and pages</small>
            </a>
            <a href="{{ url('/roles') }}">
                <span aria-hidden="true">?</span>
                <strong>How roles work</strong>
                <small>What each account can do</small>
            </a>
        </div>
    </article>
</section>
