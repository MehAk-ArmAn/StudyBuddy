@php
    $sbP09Notices = \App\Support\StudyBuddyDailyPulse::notices($user);
    $sbP09Unread = $sbP09Notices->where('read', false)->count();
@endphp
<section class="sbp09-dashboard-card" aria-label="Your personal planner and updates">
    <div class="sbp09-dashboard-card__left">
        <span class="sbp09-kicker">✦ YOUR NEXT CHAPTER</span>
        <h2>Plan your day in your learning universe.</h2>
        <p>Private calendar, verified assignment deadlines and honest learning activity. Nothing is shared from your personal planner.</p>
        <a href="{{ route('studybuddy.p09.pulse') }}" class="sbp09-primary-link">Open My Day <span aria-hidden="true">↗</span></a>
    </div>
    <div class="sbp09-dashboard-card__right" aria-label="Account activity">
        <img src="{{ asset('assets/studybuddy-p07/icons/achievements.webp') }}" alt="" loading="lazy" decoding="async">
        <div><strong>{{ $sbP09Unread }}</strong><small>{{ $sbP09Unread === 1 ? 'Update to review' : 'Updates to review' }}</small></div>
    </div>
</section>
