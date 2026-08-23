@php
    $roleImage = $settings['role_image_parent'] ?? '/assets/studybuddy-brand/pages/path-parents.webp';
@endphp

<section class="role-grid parent-readable-dashboard">
    <article class="role-card wide role-art-card" data-role-card>
        <div>
            <p class="role-kicker">How connecting works</p>
            <h2>Only with your child's code</h2>
            <p>You need your child's email and their Connect Code. Nobody can link to an account any other way, and passwords are never shared.</p>
        </div>
        <img src="{{ $roleImage }}" alt="" aria-hidden="true" loading="lazy" decoding="async" onerror="this.src='{{ asset('assets/studybuddy-imgs/brand/logo-icon.png') }}'">
    </article>

    <article class="role-card wide" data-role-card>
        <div class="role-card-head">
            <div>
                <p class="role-kicker">At a glance</p>
                <h2>How things are going</h2>
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
        <p class="role-help-text">Ask your child to open their dashboard and read you the Connect Code shown there.</p>

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
                <p class="role-kicker">Your family</p>
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
                <div class="empty-role-panel">
                    <strong>No children connected yet.</strong>
                    Use the form above with your child's email and their Connect Code.
                </div>
            @endforelse
        </div>
    </article>

    <article class="role-card wide" data-role-card>
        <p class="role-kicker">Recent</p>
        <h2>What they've been doing</h2>

        <div class="role-list">
            @forelse($parentData['childActivity'] as $activity)
                <article>
                    <span>{{ ($activity->points ?? 0) >= 0 ? '+' : '' }}{{ $activity->points ?? 0 }}</span>
                    <div>
                        <strong>{{ $activity->learner_name ?? 'Learner' }}</strong>
                        <small>{{ $activity->label ?? $activity->reason ?? 'Learning activity' }} • {{ $humanDate($activity->created_at ?? null) ?: 'Recently' }}</small>
                    </div>
                </article>
            @empty
                <div class="empty-role-panel">
                    <strong>Nothing to show yet.</strong>
                    Activity appears here once a connected child finishes something.
                </div>
            @endforelse
        </div>
    </article>

    <article class="role-card" data-role-card>
        <p class="role-kicker">Your account</p>
        <h2>Settings and pages</h2>

        <div class="role-list buttons">
            <a href="{{ url('/profile') }}">Parent profile</a>
            <a href="{{ url('/community-guidelines') }}">Safety rules</a>
            <a href="{{ url('/privacy-policy') }}">Privacy policy</a>
            <a href="{{ url('/data-deletion') }}">Data deletion</a>
        </div>
    </article>
</section>
