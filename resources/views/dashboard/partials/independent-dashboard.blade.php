<section class="role-grid">
    @include('dashboard.partials.learner-connect-code')

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Your focus</p>
                <h2>{{ $learnerData['goal'] ?: 'No goal set yet' }}</h2>
            </div>
            <a href="{{ url('/profile') }}">{{ $learnerData['goal'] ? 'Change goal' : 'Set a goal' }}</a>
        </div>

        <div class="independent-track">
            <article>
                <span aria-hidden="true">01</span>
                <strong>Working on</strong>
                <p>{{ $learnerData['focus'] ?: 'Add this in your profile.' }}</p>
            </article>
            <article>
                <span aria-hidden="true">02</span>
                <strong>Practise</strong>
                <p>Pick one app and finish a short session.</p>
            </article>
            <article>
                <span aria-hidden="true">03</span>
                <strong>Keep it</strong>
                <p>Your points and profile build up as you go.</p>
            </article>
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Shortcuts</p>
        <h2>Where to go</h2>

        {{-- These point at pages that exist. The old list linked to two app
             slugs that were never in the catalogue, so both 404'd. --}}
        <div class="role-list buttons">
            <a href="{{ url('/apps') }}">Browse apps</a>
            <a href="{{ url('/profile') }}">Your profile</a>
            <a href="{{ url('/points-wallet') }}">Points</a>
            <a href="{{ url('/community') }}">Community</a>
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Saved and assigned</p>
        <h2>Your tasks</h2>

        <div class="role-list">
            @forelse($assignments as $assignment)
                <article>
                    <span aria-hidden="true">{{ strtoupper(substr($assignment->type ?? 'T', 0, 1)) }}</span>
                    <div>
                        <strong>{{ $assignment->title }}</strong>
                        <small>{{ $assignment->due_at ? 'Due '.$humanDate($assignment->due_at) : 'Self-paced' }}</small>
                    </div>
                </article>
            @empty
                <div class="empty-role-panel">
                    <strong>Nothing here yet.</strong>
                    You set your own pace — start with any app.
                </div>
            @endforelse
        </div>
    </article>

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
</section>
