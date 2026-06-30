@extends('layouts.app')

@section('title', 'StudyBuddy Command Center')

@section('content')
@php
    $appsUrl = url('/apps');
    $questUrl = \Illuminate\Support\Facades\Route::has('studybuddy.quests.index') ? route('studybuddy.quests.index') : url('/my-quest');
    $themeRouteExists = \Illuminate\Support\Facades\Route::has('studybuddy.dashboard.theme.update');
    $activeTheme = old('theme', auth()->user()->avatar_style ?? 'cosmic-dolphin');
@endphp

<main class="sb-command-center" data-command-center>
    <section class="sb-command-hero">
        <div class="sb-command-hero__copy">
            <span class="sb-command-kicker">StudyBuddy Phase 4</span>
            <h1>Your Learning Command Center</h1>
            <p>One premium home for today’s mission, saved quests, learning streaks, weekly goals, and your personalized StudyBuddy style.</p>
            <div class="sb-command-actions">
                <a href="{{ $appsUrl }}" class="sb-command-btn sb-command-btn--primary">Explore Apps</a>
                <a href="{{ $questUrl }}" class="sb-command-btn sb-command-btn--ghost">Open My Quest</a>
            </div>
        </div>
        <div class="sb-command-orb" aria-hidden="true"><div class="sb-command-orb__ring"></div><div class="sb-command-orb__core">✨</div></div>
    </section>

    <section class="sb-command-stats" aria-label="Quest statistics">
        <article class="sb-stat-card"><span>Total Quests</span><strong>{{ $totalQuests }}</strong><small>saved in your vault</small></article>
        <article class="sb-stat-card"><span>Active</span><strong>{{ $activeQuests }}</strong><small>ready to continue</small></article>
        <article class="sb-stat-card"><span>Completed</span><strong>{{ $completedQuests }}</strong><small>learning wins</small></article>
        <article class="sb-stat-card"><span>Completion</span><strong>{{ $completionRate }}%</strong><small>quest progress</small></article>
    </section>

    <section class="sb-command-grid">
        <article class="sb-panel sb-panel--mission">
            <span class="sb-panel-kicker">{{ $todayMission['tag'] }}</span>
            <h2>Today’s Mission</h2>
            <h3>{{ $todayMission['title'] }}</h3>
            <p>{{ $todayMission['focus'] }}</p>
            <a href="{{ $appsUrl }}" class="sb-command-btn sb-command-btn--primary">Start from Apps</a>
        </article>

        <article class="sb-panel sb-panel--streak">
            <span class="sb-panel-kicker">Learning Momentum</span>
            <h2>{{ $streak }} Day Streak</h2>
            <div class="sb-streak-dots" aria-hidden="true">@for($i=1;$i<=7;$i++)<span class="{{ $i <= min($streak,7) ? 'is-filled' : '' }}"></span>@endfor</div>
            <p>Keep the streak alive with one small action today: save, start, or complete a mission.</p>
        </article>

        <article class="sb-panel sb-panel--profile">
            <span class="sb-panel-kicker">Profile Readiness</span>
            <h2>{{ $profileChecklist['percent'] }}% Complete</h2>
            <div class="sb-progress"><span style="width: {{ $profileChecklist['percent'] }}%"></span></div>
            <ul class="sb-checklist">
                @foreach($profileChecklist['items'] as $item)
                    <li class="{{ $item['done'] ? 'is-done' : '' }}"><span>{{ $item['done'] ? '✓' : '•' }}</span>{{ $item['label'] }}</li>
                @endforeach
            </ul>
        </article>

        <article class="sb-panel sb-panel--role">
            <span class="sb-panel-kicker">Role Path</span>
            <h2>{{ $rolePanel['title'] }}</h2>
            <p>{{ $rolePanel['message'] }}</p>
            <div class="sb-role-chips">@foreach($rolePanel['actions'] as $action)<span>{{ $action }}</span>@endforeach</div>
        </article>
    </section>

    <section class="sb-command-row">
        <article class="sb-panel sb-panel--wide">
            <div class="sb-panel-heading"><div><span class="sb-panel-kicker">Weekly Focus</span><h2>This Week’s Learning Goals</h2></div><a href="{{ $questUrl }}" class="sb-text-link">Manage quests</a></div>
            <div class="sb-weekly-goals">
                @foreach($weeklyFocus as $goal)
                    @php $goalPercent = (int) round(($goal['current'] / max($goal['target'],1))*100); @endphp
                    <div class="sb-goal"><div class="sb-goal-top"><strong>{{ $goal['title'] }}</strong><span>{{ $goal['current'] }}/{{ $goal['target'] }}</span></div><div class="sb-progress"><span style="width: {{ min($goalPercent,100) }}%"></span></div><p>{{ $goal['hint'] }}</p></div>
                @endforeach
            </div>
        </article>

        <article class="sb-panel sb-panel--theme">
            <span class="sb-panel-kicker">Personal Style</span>
            <h2>Dashboard Mood</h2>
            <p>Choose a StudyBuddy vibe for your logged-in experience.</p>
            @if($themeRouteExists)
                <form method="POST" action="{{ route('studybuddy.dashboard.theme.update') }}" class="sb-theme-form" data-phase4-theme-form>
                    @csrf
                    <select name="theme" aria-label="Choose dashboard theme">
                        @foreach(['cosmic-dolphin'=>'Cosmic Dolphin','bts-purple-galaxy'=>'BTS Purple Galaxy','ocean-focus'=>'Ocean Focus','candy-pop'=>'Candy Pop','forest-calm'=>'Forest Calm','night-study'=>'Night Study','solar-gold'=>'Solar Gold','neon-gamer'=>'Neon Gamer'] as $value=>$label)
                            <option value="{{ $value }}" @selected($activeTheme === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="sb-command-btn sb-command-btn--primary">Save Style</button>
                    <p class="sb-theme-response" data-phase4-theme-response></p>
                </form>
            @else
                <p class="sb-muted">Theme saving route is not available yet.</p>
            @endif
        </article>
    </section>

    <section class="sb-panel sb-panel--wide">
        <div class="sb-panel-heading"><div><span class="sb-panel-kicker">Quest Vault</span><h2>Recent Saved Quests</h2></div><a href="{{ $questUrl }}" class="sb-text-link">View all</a></div>
        @if($recentQuests->isEmpty())
            <div class="sb-empty-command"><h3>No saved quests yet</h3><p>Open Apps, preview a mission, and save it to build your command center.</p><a href="{{ $appsUrl }}" class="sb-command-btn sb-command-btn--primary">Find a Mission</a></div>
        @else
            <div class="sb-recent-quests">
                @foreach($recentQuests as $quest)
                    @php $title=$quest->mission_title ?? 'StudyBuddy Mission'; $status=$quest->status ?? 'saved'; @endphp
                    <article class="sb-recent-quest">
                        <div><span>{{ ucfirst(str_replace('_',' ',$status)) }}</span><h3>{{ $title }}</h3><p>{{ $quest->mission_focus ?? $quest->mission_description ?? 'Continue this learning mission from your quest vault.' }}</p></div>
                        @if(\Illuminate\Support\Facades\Route::has('studybuddy.quests.update'))
                            <form method="POST" action="{{ route('studybuddy.quests.update', $quest) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ in_array($status,['done','completed','finished']) ? 'saved' : 'done' }}"><button type="submit" class="sb-small-action">{{ in_array($status,['done','completed','finished']) ? 'Reopen' : 'Mark Done' }}</button></form>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection
