<section class="role-grid">
    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Parent overview</p>
                <h2>Child progress snapshot</h2>
            </div>
            <a href="{{ url('/community-guidelines') }}">Safety guide</a>
        </div>

        <div class="metric-grid">
            <article><span>Children</span><strong>{{ $parentData['metrics']['children'] }}</strong></article>
            <article><span>Total points</span><strong>{{ number_format($parentData['metrics']['total_points']) }}</strong></article>
            <article><span>Average points</span><strong>{{ number_format($parentData['metrics']['avg_points']) }}</strong></article>
            <article><span>Recent events</span><strong>{{ $parentData['metrics']['recent_events'] }}</strong></article>
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Connect child</p>
        <h2>Add child account</h2>

        <form method="POST" action="{{ route('studybuddy.parent.children.store') }}" class="role-form">
            @csrf
            <label>
                <span>Child name</span>
                <input name="child_name" placeholder="Example: Ayaan">
            </label>
            <label>
                <span>Child email</span>
                <input type="email" name="child_email" required placeholder="child@example.com">
            </label>
            <button type="submit">Connect child</button>
        </form>
    </article>

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Family learning hub</p>
                <h2>Connected children</h2>
            </div>
        </div>

        <div class="child-grid">
            @forelse($parentData['children'] as $child)
                <article>
                    <span>{{ strtoupper(substr($child->display_name ?? $child->email ?? 'C', 0, 1)) }}</span>
                    <strong>{{ $child->display_name ?? $child->email }}</strong>
                    <small>{{ $child->email ?? 'No email' }}</small>
                    <em>{{ $child->status ?? 'connected' }}</em>

                    @if(!empty($child->id))
                        <form method="POST" action="{{ route('studybuddy.parent.children.destroy', $child->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Remove</button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="empty-role-panel">No child accounts connected yet. Add a child email to begin.</div>
            @endforelse
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Activity feed</p>
        <h2>Recent child activity</h2>

        <div class="role-list">
            @forelse($parentData['childActivity'] as $activity)
                <article>
                    <span>{{ ($activity->points ?? 0) >= 0 ? '+' : '' }}{{ $activity->points ?? 0 }}</span>
                    <div>
                        <strong>{{ $activity->label ?? $activity->reason ?? 'Learning activity' }}</strong>
                        <small>{{ $activity->created_at ?? 'Recently' }}</small>
                    </div>
                </article>
            @empty
                <div class="empty-role-panel">Child activity will appear after connected learners earn points.</div>
            @endforelse
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Parent tools</p>
        <h2>Controls</h2>

        <div class="role-list buttons">
            <a href="{{ url('/profile') }}">Parent profile</a>
            <a href="{{ url('/community-guidelines') }}">Safety rules</a>
            <a href="{{ url('/privacy-policy') }}">Privacy policy</a>
            <a href="{{ url('/data-deletion') }}">Data deletion</a>
        </div>
    </article>
</section>
