<section class="role-grid">
    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Today</p>
                <h2>Your learning path</h2>
            </div>
            <a href="{{ url('/apps') }}">All apps</a>
        </div>

        <div class="role-app-grid">
            @forelse($recommendedApps as $app)
                <a href="{{ url('/apps/'.$app->slug) }}">
                    <span>{{ $app->icon ?? '✨' }}</span>
                    <strong>{{ $app->name }}</strong>
                    <small>{{ $app->tagline ?? $app->category ?? 'Learning world' }}</small>
                </a>
            @empty
                <div class="empty-role-panel">No apps ready yet.</div>
            @endforelse
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Assigned tasks</p>
        <h2>What to do next</h2>

        <div class="role-list">
            @forelse($assignments as $assignment)
                <article>
                    <span>{{ strtoupper(substr($assignment->type ?? 'T', 0, 1)) }}</span>
                    <div>
                        <strong>{{ $assignment->title }}</strong>
                        <small>{{ $assignment->due_at ? 'Due '.$assignment->due_at : 'No deadline' }}</small>
                    </div>
                </article>
            @empty
                <div class="empty-role-panel">No assigned tasks yet. Pick an app and start a tiny win.</div>
            @endforelse
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Progress</p>
        <h2>Recent points</h2>

        <div class="role-list">
            @forelse($recentPoints as $item)
                <article>
                    <span>{{ ($item->points ?? 0) >= 0 ? '+' : '' }}{{ $item->points ?? 0 }}</span>
                    <div>
                        <strong>{{ $item->label ?? $item->reason ?? 'StudyBuddy activity' }}</strong>
                        <small>{{ $item->created_at ?? 'Recently' }}</small>
                    </div>
                </article>
            @empty
                <div class="empty-role-panel">No point activity yet.</div>
            @endforelse
        </div>
    </article>

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Student controls</p>
                <h2>Your account tools</h2>
            </div>
        </div>

        <div class="role-control-grid">
            <a href="{{ url('/profile') }}"><span>🪄</span><strong>Edit profile</strong><small>Colors, PFP, badges, privacy</small></a>
            <a href="{{ url('/community') }}"><span>🌍</span><strong>Community</strong><small>See public profiles</small></a>
            <a href="{{ url('/search') }}"><span>⌕</span><strong>Search</strong><small>Find apps and pages</small></a>
            <a href="{{ url('/roles') }}"><span>🎭</span><strong>Roles guide</strong><small>Understand the platform</small></a>
        </div>
    </article>
</section>
