@php
    // This is a personal activity summary, not a certified achievement system.
    // It never changes age permissions, teacher status, or school membership.
    $sb24Profile = is_array($user->role_profile ?? null) ? $user->role_profile : [];
    $sb24FamilyMoments = 0;
    if (in_array($role, ['student', 'independent_learner'], true) && \Illuminate\Support\Facades\Schema::hasTable('studybuddy_group_members')) {
        $sb24Related = \Illuminate\Support\Facades\DB::table('studybuddy_group_members')
            ->where('user_id', $user->id)->where('status', 'connected_with_code')->get();
        foreach ($sb24Related as $sb24Row) {
            $sb24Data = json_decode($sb24Row->metrics_json ?? '{}', true);
            $sb24FamilyMoments += max(0, (int) (is_array($sb24Data) ? ($sb24Data['moments'] ?? 0) : 0));
        }
    }
    $sb24Done = (int) $user->savedQuests()->where('status', 'completed')->count();
    $sb24Saved = (int) $user->savedQuests()->whereIn('status', ['saved', 'in_progress'])->count();
    $sb24Chapter = $sb24Done >= 25 ? 'Creating a constellation' : ($sb24Done >= 10 ? 'Finding your orbit' : ($sb24Done >= 3 ? 'Building momentum' : 'Starting your journey'));
    $sb24Goal = $sb24Profile['learning_goal'] ?? $sb24Profile['study_goal'] ?? $sb24Profile['independent_goal'] ?? $sb24Profile['parent_goal'] ?? null;
    $sb24Focus = $sb24Profile['current_focus'] ?? $user->learning_stage ?? null;
    $sb24Subjects = $sb24Profile['favorite_subjects'] ?? null;
@endphp
<section class="sb24-dashboard-journey" aria-label="Your personalized StudyBuddy journey">
    <div class="sb24-dashboard-head">
        <div><p class="sb24-eyebrow">Your personal universe</p><h2>Learning is <em>your journey.</em></h2><p>One account. Goals that evolve. Your StudyBuddy history stays with you.</p></div>
        @if($role === 'parent' && $user->isAdult() && $user->hasVerifiedEmail())
            <a class="sb24-secondary" href="{{ route('studybuddy.family.learners') }}">Manage family learner spaces ↗</a>
        @elseif($role === 'parent')
            <a class="sb24-secondary" href="{{ route('verification.notice') }}">Verify email to start family learning ↗</a>
        @else
            <a class="sb24-secondary" href="{{ route('profile') }}">Personalize your profile ↗</a>
        @endif
    </div>
    <div class="sb24-dashboard-cards">
        <article><span>My focus</span><strong>{{ $sb24Focus ?: 'Choose your next adventure' }}</strong><small>{{ $sb24Goal ?: 'Add a learning goal in Profile Studio' }}</small></article>
        <article><span>Subjects I love</span><strong>{{ $sb24Subjects ?: 'Keep exploring' }}</strong><small>Change your interests whenever you want.</small></article>
        @if(in_array($role, ['student', 'independent_learner'], true))
            <article><span>Self-tracked journey</span><strong>{{ $sb24Chapter }}</strong><small>{{ $sb24Done }} self-completed quest{{ $sb24Done === 1 ? '' : 's' }} · {{ $sb24Saved }} active or saved@if($sb24FamilyMoments) · {{ $sb24FamilyMoments }} earlier family-logged moment{{ $sb24FamilyMoments === 1 ? '' : 's' }} preserved@endif</small><a href="{{ route('studybuddy.quests.index') }}">Open My Quest →</a></article>
        @elseif($role === 'parent')
            <article><span>Family pathways</span><strong>From day one, at their pace</strong><small>Manage private profiles for younger learners; connect their own account later.</small></article>
        @else
            <article><span>Teaching journey</span><strong>Grow with your learners</strong><small>Teacher and school privileges remain subject to the existing verification process.</small></article>
        @endif
    </div>
</section>
