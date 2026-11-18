@auth
@php
    $sbp09BellNotices = \App\Support\StudyBuddyDailyPulse::notices(auth()->user());
    $sbp09BellUnread = $sbp09BellNotices->where('read', false)->count();
@endphp
<div class="sbp09-bell-wrap">
    <details class="sbp09-bell" data-sbp09-bell>
        <summary aria-label="Notifications, {{ $sbp09BellUnread }} unread" title="My notifications">
            <span aria-hidden="true">🔔</span>
            @if($sbp09BellUnread)<em>{{ min($sbp09BellUnread, 99) }}</em>@endif
            <span>Updates</span>
        </summary>
        <div class="sbp09-bell-menu">
            <div class="sbp09-bell-heading"><strong>My updates</strong><small>{{ $sbp09BellUnread }} unread</small></div>
            @forelse($sbp09BellNotices->where('read', false)->take(4) as $notice)
                <p class="sbp09-bell-item"><strong>{{ $notice['title'] }}</strong><span>{{ $notice['label'] }} · {{ $notice['date'] }}</span></p>
            @empty
                <p class="sbp09-bell-empty">You're all caught up. ✦</p>
            @endforelse
            <a href="{{ route('studybuddy.p09.pulse') }}#my-updates">Open My Day and notifications →</a>
        </div>
    </details>
</div>
@endauth
