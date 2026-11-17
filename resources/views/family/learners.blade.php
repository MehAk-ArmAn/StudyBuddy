@extends('layouts.app')

@section('title', 'Family learning journeys | StudyBuddy')

@section('content')
<main class="sb24-family" id="main-content">
    <div class="sb24-family-inner">
        <header class="sb24-family-hero">
            <p class="sb24-eyebrow">StudyBuddy · Family universe</p>
            <h1>Every age. <em>One lifelong journey.</em></h1>
            <p>Help younger learners discover what they love. Keep their interests, goals and family-logged milestones together as they grow.</p>
            <div class="sb24-header-actions">
                <a class="sb24-link" href="{{ route('dashboard') }}">← Your dashboard</a>
                <a class="sb24-link" href="{{ route('profile') }}">Profile Studio</a>
            </div>
            <p class="sb24-safety">These are <strong>private parent-managed profiles</strong>, not separate child login accounts. We only request a display name and birth year; no child's email is required. A learner can later connect their own age-eligible account using a Connect Code and the existing adult-approval controls.</p>
        </header>

        @if(session('status')) <div class="sb24-alert success" role="status">{{ session('status') }}</div> @endif
        @if($errors->any()) <div class="sb24-alert error" role="alert"><strong>Please check:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

        <section class="sb24-creation" aria-labelledby="sb24-start-title">
            <div class="sb24-intro">
                <p class="sb24-eyebrow">Start a learning universe</p>
                <h2 id="sb24-start-title">A space of their own, <em>guided by you.</em></h2>
                <p>From first sounds and shapes to languages, coding and creative projects. Their interests can change, but their family learning story stays.</p>
            </div>
            <form class="sb24-form" method="POST" action="{{ route('studybuddy.family.learners.store') }}">
                @csrf
                <div class="sb24-fields">
                    <label><span>Learner's display name</span><input name="display_name" value="{{ old('display_name') }}" maxlength="100" autocomplete="off" placeholder="A nickname, not a full legal name" required></label>
                    <label><span>Birth year</span><input type="number" name="birth_year" min="1900" max="{{ now()->year }}" value="{{ old('birth_year') }}" inputmode="numeric" required></label>
                    <label><span>What would they like to explore?</span><input name="favorite_subjects" value="{{ old('favorite_subjects') }}" maxlength="190" placeholder="Space, music, numbers…"></label>
                    <label><span>Learning focus</span><input name="current_focus" value="{{ old('current_focus') }}" maxlength="120" placeholder="Explore shapes, practise reading…"></label>
                    <label class="wide"><span>A first goal (optional)</span><input name="learning_goal" value="{{ old('learning_goal') }}" maxlength="190" placeholder="Have fun discovering something new"></label>
                </div>
                <button class="sb24-primary" type="submit">Create private learning space <span aria-hidden="true">↗</span></button>
            </form>
        </section>

        <section class="sb24-learners" aria-labelledby="sb24-learners-title">
            <div class="sb24-section-title"><div><p class="sb24-eyebrow">Growing together</p><h2 id="sb24-learners-title">Your learning worlds</h2></div><p>{{ $learners->count() }} private profile{{ $learners->count() === 1 ? '' : 's' }}</p></div>
            <div class="sb24-grid">
                @forelse($learners as $learner)
                    @php
                        $journey = $learner->journey;
                        $moments = max(0, (int)($journey['moments'] ?? 0));
                        $year = (int)($journey['birth_year'] ?? 0);
                    @endphp
                    <article class="sb24-learner-card">
                        <div class="sb24-learner-top">
                            <div class="sb24-monogram" aria-hidden="true">{{ strtoupper(substr($learner->display_name ?? 'L', 0, 1)) }}</div>
                            <div><p class="sb24-eyebrow">{{ $learner->status === 'family_managed' ? 'Family-managed · private' : 'Connected learner' }}</p><h3>{{ $learner->display_name }}</h3><span>Birth year {{ $year ?: '—' }}</span></div>
                        </div>
                        <div class="sb24-progress-line"><span></span></div>
                        <p class="sb24-chapter">{{ $learner->chapter }} <span>· {{ $moments }} family-logged learning moment{{ $moments === 1 ? '' : 's' }}</span></p>
                        <p class="sb24-tiny">Journey chapter describes family-logged activity, not an academic grade, award or permission level.</p>
                        <form method="POST" action="{{ route('studybuddy.family.learners.update', $learner->id) }}" class="sb24-card-form">
                            @csrf @method('PATCH')
                            <label><span>Display name</span><input name="display_name" value="{{ $learner->display_name }}" maxlength="100" required></label>
                            <label><span>Interests</span><input name="favorite_subjects" value="{{ $journey['favorite_subjects'] ?? '' }}" maxlength="190"></label>
                            <label><span>Current focus</span><input name="current_focus" value="{{ $journey['current_focus'] ?? '' }}" maxlength="120"></label>
                            <label><span>Goal</span><input name="learning_goal" value="{{ $journey['learning_goal'] ?? '' }}" maxlength="190"></label>
                            <button type="submit" class="sb24-secondary">Save preferences</button>
                        </form>
                        <form method="POST" action="{{ route('studybuddy.family.learners.moment', $learner->id) }}" class="sb24-moment-form">
                            @csrf
                            <label><span>Record a real learning moment</span><input name="moment_note" maxlength="160" placeholder="What did they explore today?"></label>
                            <button type="submit" class="sb24-primary">Log learning moment <span aria-hidden="true">✦</span></button>
                        </form>
                        @if($learner->status === 'family_managed' && $learner->user_id === null)
                            <details class="sb24-remove">
                                <summary>Remove this private profile</summary>
                                <p>This permanently removes its family-logged notes and moments. It does not delete any StudyBuddy login account.</p>
                                <form method="POST" action="{{ route('studybuddy.family.learners.destroy', $learner->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="sb24-secondary">Confirm removal</button>
                                </form>
                            </details>
                        @endif
                        @if($learner->status === 'family_managed' && auth()->user()->canUseAdultControls())
                            <details class="sb24-connect">
                                <summary>Connect their own account (ages 13+)</summary>
                                <p>When your learner has an email-verified StudyBuddy account, ask them for their Connect Code. Their existing family learning notes stay attached to this profile.</p>
                                <form method="POST" action="{{ route('studybuddy.family.learners.link', $learner->id) }}">
                                    @csrf
                                    <label><span>Learner email</span><input type="email" name="learner_email" required></label>
                                    <label><span>Connect Code</span><input name="connect_code" maxlength="20" required></label>
                                    <button type="submit" class="sb24-secondary">Link verified account</button>
                                </form>
                            </details>
                        @endif
                    </article>
                @empty
                    <div class="sb24-empty"><span aria-hidden="true">✦</span><h3>A new universe begins here.</h3><p>Create your first private learner profile above. Nothing gets shared publicly.</p></div>
                @endforelse
            </div>
        </section>
    </div>
</main>
@endsection
