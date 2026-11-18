@extends('layouts.app')
@section('title', 'My Day · StudyBuddy')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-p09/pulse.css') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/studybuddy-p09/pulse.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/studybuddy-p09/pulse.js') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/studybuddy-p09/pulse.js') }}" defer></script>
@endpush
@section('content')
@php
    $gridStart = $month->copy()->startOfMonth()->startOfWeek(\Illuminate\Support\Carbon::MONDAY);
    $gridEnd = $month->copy()->endOfMonth()->endOfWeek(\Illuminate\Support\Carbon::SUNDAY);
    $gridDays = collect();
    for ($day = $gridStart->copy(); $day->lte($gridEnd); $day->addDay()) $gridDays->push($day->copy());
    $eventsByDay = $entries->groupBy('date');
    $visitTotal = array_sum(array_column($weekly, 'visits'));
    $roundTotal = array_sum(array_column($weekly, 'rounds'));
    $weeklyTotal = $visitTotal + $roundTotal;
    $visitPercent = $weeklyTotal ? round($visitTotal * 100 / $weeklyTotal) : 0;
    $points = [];
    foreach ($weekly as $i => $dayRow) {
        $x = 32 + $i * 85;
        $y = 157 - (int) round(($dayRow['visits'] + $dayRow['rounds']) / $chartMax * 120);
        $points[] = $x.','.$y;
    }
@endphp
<div class="sbp09" id="my-day">
    <header class="sbp09-hero">
        <div class="sbp09-hero-copy">
            <span class="sbp09-kicker">✦ YOUR PRIVATE LEARNING COMPASS</span>
            <h1>Make room for your <em>next bright idea.</em></h1>
            <p>One calm place for your plans, upcoming assignments and the learning you actually recorded.</p>
            <div class="sbp09-hero-links"><a href="{{ route('dashboard') }}">← Dashboard</a><a href="{{ route('studybuddy.study-space') }}">My Study Space ↗</a></div>
        </div>
        <img src="{{ asset('assets/studybuddy-p07/banners/progress.webp') }}" alt="" aria-hidden="true" decoding="async" fetchpriority="high">
    </header>

    @if(session('p09_status'))
        <div class="sbp09-toast" role="status" data-p09-toast><strong>Saved!</strong> {{ session('p09_status') }} <button type="button" data-p09-toast-close aria-label="Dismiss notification">×</button></div>
    @endif
    @if($errors->any())
        <div class="sbp09-errors" role="alert"><strong>Please check your details:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <nav class="sbp09-section-nav" aria-label="My Day sections">
        <a href="#calendar">Calendar</a><a href="#my-updates">Updates @if($unread)<span>({{ $unread }})</span>@endif</a>
        <a href="#my-activity">Activity</a><a href="#my-preferences">Preferences</a>
    </nav>

    <section id="calendar" class="sbp09-panel" aria-labelledby="sbp09-calendar-heading">
        <div class="sbp09-heading"><div><span class="sbp09-kicker">01 / THE PLANETARIUM</span><h2 id="sbp09-calendar-heading">Your month, at a glance.</h2><p>Personal plans are private. School deadlines appear only from verified connections. Times follow the StudyBuddy app timezone.</p></div><a class="sbp09-mini-link" href="#sbp09-add-plan">+ Add a plan</a></div>
        <div class="sbp09-calendar-layout">
            <div class="sbp09-calendar">
                <div class="sbp09-month-nav">
                    <a href="{{ route('studybuddy.p09.pulse', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}" aria-label="Previous month">‹</a>
                    <h3>{{ $month->format('F Y') }}</h3>
                    <a href="{{ route('studybuddy.p09.pulse', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}" aria-label="Next month">›</a>
                </div>
                <div class="sbp09-calendar-grid" role="group" aria-label="Calendar for {{ $month->format('F Y') }}">
                    @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dayName)<span class="sbp09-weekday">{{ $dayName }}</span>@endforeach
                    @foreach($gridDays as $date)
                        @php $dateKey = $date->toDateString(); $dateCount = $eventsByDay->get($dateKey, collect())->count(); @endphp
                        <a class="sbp09-day {{ $date->month === $month->month ? '' : 'sbp09-day-muted' }} {{ $dateKey === $selected ? 'sbp09-day-selected' : '' }} {{ $dateKey === now()->toDateString() ? 'sbp09-day-today' : '' }}"
                           href="{{ route('studybuddy.p09.pulse', ['month' => $date->format('Y-m'), 'day' => $dateKey]) }}#calendar"
                           @if($dateKey === $selected) aria-current="date" @endif
                           aria-label="{{ $date->format('l j F Y') }}{{ $dateCount ? ', '.$dateCount.' scheduled items' : '' }}">
                            <span>{{ $date->day }}</span>@if($dateCount)<i aria-hidden="true"></i>@endif
                        </a>
                    @endforeach
                </div>
                <div class="sbp09-legend"><span><i></i> Day with plans or deadlines</span><span><i></i> Selected day</span></div>
            </div>
            <aside class="sbp09-agenda" aria-labelledby="sbp09-agenda-heading">
                <div class="sbp09-agenda-heading"><span class="sbp09-kicker">DAY AGENDA</span><h3 id="sbp09-agenda-heading">{{ \Illuminate\Support\Carbon::parse($selected)->format('l, j F') }}</h3></div>
                @forelse($selectedEntries as $entry)
                    <article class="sbp09-agenda-item">
                        <span class="sbp09-entry-icon">{{ $entry['kind'] === 'plan' ? '✦' : '◷' }}</span>
                        <div><strong>{{ $entry['title'] }}</strong><small>{{ $entry['time'] ?? 'Any time' }} · {{ $entry['kind'] === 'plan' ? 'Personal plan' : ($entry['kind'] === 'class' ? 'Your class' : 'Verified assignment') }}</small>
                        @if($entry['kind'] === 'plan' && !empty($entry['notes']))<p>{{ $entry['notes'] }}</p>@endif
                        @if($entry['kind'] === 'plan')
                            <form method="POST" action="{{ route('studybuddy.p09.plans.destroy', $entry['id']) }}">
                                @csrf @method('DELETE')<button type="submit">Remove plan</button>
                            </form>
                        @endif</div>
                    </article>
                @empty
                    <div class="sbp09-empty"><img src="{{ asset('assets/studybuddy-p07/icons/reading.webp') }}" alt="" loading="lazy"><strong>No plans on this day.</strong><p>Give this day a purpose, or enjoy the breathing room.</p></div>
                @endforelse
            </aside>
        </div>
        <form id="sbp09-add-plan" class="sbp09-plan-form" method="POST" action="{{ route('studybuddy.p09.plans.store') }}">
            @csrf
            <div><span class="sbp09-kicker">ADD A PERSONAL PLAN</span><h3>Your next little milestone.</h3><small>Up to 40 saved plans per account, never shared with schools or parents.</small></div>
            <label>Title <input type="text" name="title" maxlength="110" required minlength="2" placeholder="Read a chapter" value="{{ old('title') }}"></label>
            <label>Date <input type="date" name="date" required value="{{ old('date', $selected >= now()->toDateString() ? $selected : now()->toDateString()) }}" min="{{ now()->toDateString() }}"></label>
            <label>Time (optional) <input type="time" name="time" value="{{ old('time') }}"></label>
            <label class="sbp09-notes-label">Notes (optional) <input name="notes" type="text" maxlength="240" placeholder="Just a short reminder" value="{{ old('notes') }}"></label>
            <button type="submit">Save my plan <span aria-hidden="true">↗</span></button>
        </form>
    </section>

    <section id="my-updates" class="sbp09-panel" aria-labelledby="sbp09-updates-heading">
        <div class="sbp09-heading"><div><span class="sbp09-kicker">02 / NOTIFICATIONS</span><h2 id="sbp09-updates-heading">Updates worth your attention.</h2><p>Personal, in-app reminders for due work and saved plans—not email or push notifications.</p></div>
        @if($unread)<form method="POST" action="{{ route('studybuddy.p09.notifications.read') }}">@csrf<input type="hidden" name="notice" value="all"><button class="sbp09-quiet-button" type="submit">Mark all read</button></form>@endif
        </div>
        <div class="sbp09-notice-grid">
            @forelse($notices as $notice)
                <article class="sbp09-notice {{ $notice['read'] ? 'is-read' : '' }}">
                    <span class="sbp09-notice-icon" aria-hidden="true">{{ $notice['kind'] === 'plan' ? '✧' : '◉' }}</span>
                    <div><small>{{ $notice['label'] }} · {{ $notice['date'] }}</small><strong>{{ $notice['title'] }}</strong>
                        @if(!$notice['read'])<form method="POST" action="{{ route('studybuddy.p09.notifications.read') }}">@csrf<input name="notice" type="hidden" value="{{ $notice['id'] }}"><button type="submit">Mark as read</button></form>@else <span class="sbp09-read-label">Seen</span> @endif
                    </div>
                </article>
            @empty
                <div class="sbp09-empty sbp09-empty-wide"><strong>All clear, cosmic explorer ✨</strong><p>Nothing upcoming within seven days or overdue within fourteen days.</p></div>
            @endforelse
        </div>
    </section>

    <section id="my-activity" class="sbp09-panel" aria-labelledby="sbp09-analytics-heading">
        <div class="sbp09-heading"><div><span class="sbp09-kicker">03 / REAL ACTIVITY</span><h2 id="sbp09-analytics-heading">Your learning, not a made-up score.</h2><p>Past 7 days · logged app launches and finished Math Sprint rounds on this account.</p></div></div>
        <div class="sbp09-chart-layout">
            <div class="sbp09-chart-card"><h3>Daily recorded activity</h3><p>Counts represent recorded events—not study time or grades.</p>
                <svg viewBox="0 0 580 184" role="img" aria-label="Daily recorded app launches and completed math rounds over the past seven days">
                    <defs><linearGradient id="sbp09-chart-fill" x1="0" x2="0" y1="0" y2="1"><stop offset="0%" stop-color="#48dcff" stop-opacity=".24"/><stop offset="100%" stop-color="#8c54ff" stop-opacity="0"/></linearGradient></defs>
                    <line x1="32" y1="157" x2="548" y2="157" stroke="#d8d3f4" stroke-opacity=".35"/>
                    <line x1="32" y1="97" x2="548" y2="97" stroke="#d8d3f4" stroke-opacity=".12"/>
                    <polygon points="32,157 {{ implode(' ', $points) }} 542,157" fill="url(#sbp09-chart-fill)"/>
                    <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="#42dfff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    @foreach($weekly as $i => $dayRow)
                        <circle cx="{{ 32 + $i * 85 }}" cy="{{ 157 - (int) round(($dayRow['visits'] + $dayRow['rounds']) / $chartMax * 120) }}" r="5" fill="#af74ff" stroke="#fff" stroke-width="2"/>
                        <text x="{{ 32 + $i * 85 }}" y="179" text-anchor="middle" fill="#bfc7e9" font-size="13">{{ $dayRow['day'] }}</text>
                    @endforeach
                </svg>
                <details class="sbp09-chart-details"><summary>View exact activity counts</summary><table><caption>Recorded activity in the last seven days</caption><thead><tr><th>Date</th><th>App visits</th><th>Math rounds</th></tr></thead><tbody>@foreach($weekly as $row)<tr><th scope="row">{{ $row['date'] }}</th><td>{{ $row['visits'] }}</td><td>{{ $row['rounds'] }}</td></tr>@endforeach</tbody></table></details>
            </div>
            <div class="sbp09-chart-card sbp09-breakdown"><h3>Your recorded mix</h3><div class="sbp09-donut" style="--p09-split: {{ $visitPercent }}%;"><div><strong>{{ $weeklyTotal }}</strong><small>events</small></div></div>
                <div class="sbp09-breakdown-legend"><span><i></i> App visits <strong>{{ $visitTotal }}</strong></span><span><i></i> Finished math rounds <strong>{{ $roundTotal }}</strong></span></div>
                @if($weeklyTotal === 0)<p class="sbp09-muted-note">Once you launch an app or finish a Math Sprint round, your chart will begin to fill.</p>@endif
            </div>
        </div>
        @if($teacherProgress !== null && ($teacherProgress['state'] ?? '') === 'ready')
            <div class="sbp09-teacher-strip"><strong>Verified classroom assignment progress</strong><span>{{ $teacherProgress['totals']['completed'] }} learner-reported completions of {{ $teacherProgress['totals']['assigned'] }} verified recipient assignments.</span><small>Not grades, scores or proof of mastery. Private learner journals are excluded.</small></div>
        @endif
    </section>

    <section id="my-preferences" class="sbp09-panel" aria-labelledby="sbp09-preferences-heading">
        <div class="sbp09-heading"><div><span class="sbp09-kicker">04 / YOUR CONTROLS</span><h2 id="sbp09-preferences-heading">Only the updates you want.</h2><p>These preferences affect this in-app feed, not email/push delivery. Quiet hours are saved for future delivery support and do not hide reminders from the page.</p></div></div>
        <form class="sbp09-preferences" method="POST" action="{{ route('studybuddy.p09.preferences.update') }}">
            @csrf @method('PUT')
            <label><input name="assignment_alerts" type="checkbox" value="1" @checked($settingsP09['assignment_alerts'])><span>Show class assignment deadlines <small>Only your own verified connections</small></span></label>
            <label><input name="plan_alerts" type="checkbox" value="1" @checked($settingsP09['plan_alerts'])><span>Show personal plan reminders <small>Saved only in your account</small></span></label>
            <label><input name="quiet_enabled" type="checkbox" value="1" @checked($settingsP09['quiet_enabled'])><span>Save preferred quiet hours <small>For future scheduled delivery; the in-app feed remains visible</small></span></label>
            <div class="sbp09-time-prefs"><label>From <input type="time" name="quiet_start" value="{{ $settingsP09['quiet_start'] }}" required></label><label>To <input type="time" name="quiet_end" value="{{ $settingsP09['quiet_end'] }}" required></label></div>
            <button type="submit">Save preferences</button>
        </form>
    </section>
</div>
@endsection
