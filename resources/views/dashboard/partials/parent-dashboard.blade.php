@php
    $roleImage = $settings['role_image_parent'] ?? 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-parents.png';
@endphp

<section class="role-grid parent-readable-dashboard">
    <article class="role-card wide role-art-card" data-role-card>
        <div>
            <p class="role-kicker">Parent overview</p>
            <h2>Guide your child’s progress safely.</h2>
            <p>Parents can only connect a child with the child’s StudyBuddy Connect Code. No random linking. No password sharing.</p>
        </div>
        <img src="{{ $roleImage }}" alt="Parent StudyBuddy path" onerror="this.src='{{ asset('assets/studybuddy-imgs/brand/logo-icon.png') }}'">
    </article>

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">Progress snapshot</p>
                <h2>Connected child metrics</h2>
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
        <p class="role-kicker">Verified connection</p>
        <h2>Connect child account</h2>
        <p class="role-help-text">Ask the learner to open their dashboard and share their current StudyBuddy Connect Code.</p>

        <form method="POST" action="{{ route('studybuddy.parent.children.store') }}" class="role-form">
            @csrf
            <label>
                <span>Child email</span>
                <input type="email" name="child_email" required placeholder="child@example.com">
            </label>
            <label>
                <span>Child Connect Code</span>
                <input name="child_connect_code" required placeholder="Example: A1B2C3D4">
            </label>
            <button type="submit">Connect child safely</button>
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
                <div class="empty-role-panel">No child accounts connected yet. Use email + Connect Code to connect safely.</div>
            @endforelse
        </div>
    </article>

    <article class="role-card wide" data-role-card>
        <p class="role-kicker">Activity feed</p>
        <h2>Recent child activity</h2>

        <div class="role-list">
            @forelse($parentData['childActivity'] as $activity)
                <article>
                    <span>{{ ($activity->points ?? 0) >= 0 ? '+' : '' }}{{ $activity->points ?? 0 }}</span>
                    <div>
                        <strong>{{ $activity->learner_name ?? 'Learner' }}</strong>
                        <small>{{ $activity->label ?? $activity->reason ?? 'Learning activity' }} • {{ $activity->created_at ?? 'Recently' }}</small>
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
