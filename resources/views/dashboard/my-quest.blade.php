@extends('layouts.app')

@section('content')
<div class="sb-quest-page">
    <section class="sb-quest-hero">
        <div>
            <p class="sb-eyebrow">StudyBuddy Quest Vault</p>
            <h1>My Quest</h1>
            <p>
                Your saved missions live here. Start small, keep going, and turn every study session into a little win.
            </p>
        </div>

        <div class="sb-quest-orb" aria-hidden="true">
            <span></span>
        </div>
    </section>

    @if (session('status'))
        <div class="sb-quest-alert">
            {{ session('status') }}
        </div>
    @endif

    <section class="sb-quest-stats" aria-label="Quest statistics">
        <article>
            <strong>{{ $stats['total'] ?? 0 }}</strong>
            <span>Total Missions</span>
        </article>
        <article>
            <strong>{{ $stats['saved'] ?? 0 }}</strong>
            <span>Saved</span>
        </article>
        <article>
            <strong>{{ $stats['in_progress'] ?? 0 }}</strong>
            <span>In Progress</span>
        </article>
        <article>
            <strong>{{ $stats['completed'] ?? 0 }}</strong>
            <span>Completed</span>
        </article>
    </section>

    @if ($quests->count())
        <section class="sb-quest-grid">
            @foreach ($quests as $quest)
                <article class="sb-quest-card" data-quest-card>
                    <div class="sb-quest-card-top">
                        <span class="sb-quest-pill">{{ $quest->app_title ?: Str::headline($quest->app_slug) }}</span>
                        <span class="sb-quest-status sb-status-{{ str_replace('_', '-', $quest->status) }}">
                            {{ $quest->status_label }}
                        </span>
                    </div>

                    <h2>{{ $quest->mission_title }}</h2>

                    @if ($quest->mission_description)
                        <p>{{ $quest->mission_description }}</p>
                    @else
                        <p>Saved from the StudyBuddy apps launcher. Open this when you are ready to continue.</p>
                    @endif

                    <div class="sb-quest-meta">
                        @if ($quest->difficulty)
                            <span>{{ $quest->difficulty }}</span>
                        @endif

                        @if ($quest->estimated_minutes)
                            <span>{{ $quest->estimated_minutes }} min</span>
                        @endif

                        <span>{{ $quest->created_at?->diffForHumans() }}</span>
                    </div>

                    <div class="sb-progress-track" aria-label="Quest progress">
                        <span style="width: {{ max(0, min(100, (int) $quest->progress)) }}%"></span>
                    </div>

                    <form method="POST" action="{{ route('studybuddy.quests.update', $quest) }}" class="sb-quest-actions">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="progress" value="{{ $quest->status === 'completed' ? 100 : max(10, (int) $quest->progress) }}">

                        @if ($quest->status !== 'in_progress')
                            <button type="submit" name="status" value="in_progress">Start</button>
                        @endif

                        @if ($quest->status !== 'completed')
                            <button type="submit" name="status" value="completed">Mark Done</button>
                        @endif
                    </form>

                    <form method="POST" action="{{ route('studybuddy.quests.destroy', $quest) }}" class="sb-quest-remove" onsubmit="return confirm('Remove this quest?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Remove</button>
                    </form>
                </article>
            @endforeach
        </section>

        <div class="sb-quest-pagination">
            {{ $quests->links() }}
        </div>
    @else
        <section class="sb-empty-quest">
            <div class="sb-empty-icon"></div>
            <h2>No missions saved yet</h2>
            <p>Go to the Apps page, open a mission preview, and save your first mission to My Quest.</p>
            <a href="{{ url('/apps') }}">Explore Apps</a>
        </section>
    @endif
</div>
@endsection
