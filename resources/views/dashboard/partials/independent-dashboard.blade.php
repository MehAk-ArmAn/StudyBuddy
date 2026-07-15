<section class="role-grid">
    @include('dashboard.partials.learner-connect-code')

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Self-paced path</p>
                <h2>{{ $learnerData['goal'] }}</h2>
            </div>
            <a href="{{ url('/profile') }}">Update goal</a>
        </div>

        <div class="independent-track">
            <article><span>01</span><strong>Focus</strong><p>{{ $learnerData['focus'] }}</p></article>
            <article><span>02</span><strong>Practice</strong><p>Choose one app world and finish a short session.</p></article>
            <article><span>03</span><strong>Portfolio</strong><p>Showcase your profile, favourite apps, and progress.</p></article>
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Tools</p>
        <h2>Independent controls</h2>

        <div class="role-list buttons">
            <a href="{{ url('/apps/focus-forest') }}">Focus session</a>
            <a href="{{ url('/apps/planner-city') }}">Planner City</a>
            <a href="{{ url('/profile') }}">Profile portfolio</a>
            <a href="{{ url('/community') }}">Community showcase</a>
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Assigned / saved</p>
        <h2>Your tasks</h2>

        <div class="role-list">
            @forelse($assignments as $assignment)
                <article>
                    <span>{{ strtoupper(substr($assignment->type ?? 'T', 0, 1)) }}</span>
                    <div>
                        <strong>{{ $assignment->title }}</strong>
                        <small>{{ $assignment->due_at ? 'Due '.$assignment->due_at : 'Self-paced' }}</small>
                    </div>
                </article>
            @empty
                <div class="empty-role-panel">No external tasks. Build your own path from apps.</div>
            @endforelse
        </div>
    </article>

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Recommended apps</p>
                <h2>For self-directed growth</h2>
            </div>
            <a href="{{ url('/apps') }}">Explore all</a>
        </div>

        <div class="role-app-grid">
            @forelse($recommendedApps as $app)
                <a href="{{ url('/apps/'.$app->slug) }}">
                    <span>{{ $app->icon ?? '✨' }}</span>
                    <strong>{{ $app->name }}</strong>
                    <small>{{ $app->tagline ?? $app->category ?? 'Learning world' }}</small>
                </a>
            @empty
                <div class="empty-role-panel">No recommendations yet.</div>
            @endforelse
        </div>
    </article>
</section>
