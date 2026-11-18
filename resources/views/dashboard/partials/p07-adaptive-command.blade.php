{{-- Pack 07: the first view is a functional command center, not an extra decorative hero. --}}
@php
    $p07IsLearner = in_array($role, ['student', 'independent_learner'], true);
    $p07Role = match ($role) {
        'teacher' => 'teacher',
        'parent' => 'parent',
        'independent_learner' => 'independent',
        default => 'student',
    };
    $p07Hero = match ($p07Role) {
        'teacher' => 'school',
        'parent' => 'family',
        'independent' => 'worlds',
        default => 'learner',
    };
    $p07Title = match ($p07Role) {
        'teacher' => 'Your teaching day, made clearer.',
        'parent' => 'Your family learning space.',
        'independent' => 'Your learning, your pace.',
        default => 'A little progress, a whole universe.',
    };
    $p07Description = match ($p07Role) {
        'teacher' => 'Find your classes, create assignments and follow work from learners you have connected.',
        'parent' => 'Support your connected children, see their recent activity and keep family learning simple.',
        'independent' => 'Continue a goal, explore an app or pick up your private learning notes.',
        default => 'Choose an app, finish a task and see the progress StudyBuddy has actually recorded.',
    };
    $p07Stats = match ($p07Role) {
        'teacher' => [
            ['label' => 'Your classes', 'value' => $teacherData['metrics']['classes'] ?? 0, 'hint' => 'Classes you created'],
            ['label' => 'Connected learners', 'value' => $teacherData['metrics']['students'] ?? 0, 'hint' => 'Connected roster entries'],
            ['label' => 'Assignments created', 'value' => $teacherData['metrics']['assignments'] ?? 0, 'hint' => 'Your own tasks and drafts'],
            ['label' => 'Recent learner events', 'value' => $teacherData['metrics']['activity'] ?? 0, 'hint' => 'Latest recorded activity'],
        ],
        'parent' => [
            ['label' => 'Connected children', 'value' => $parentData['metrics']['children'] ?? 0, 'hint' => 'Linked with a Connect Code'],
            ['label' => 'Recorded points', 'value' => $parentData['metrics']['total_points'] ?? 0, 'hint' => 'Across connected profiles'],
            ['label' => 'Recent activities', 'value' => $parentData['metrics']['recent_events'] ?? 0, 'hint' => 'Latest recorded events'],
            ['label' => 'Your profile', 'value' => $completion.'%', 'hint' => 'Profile completeness'],
        ],
        default => [
            ['label' => 'Your points', 'value' => $user->cosmic_points ?? 0, 'hint' => 'Recorded in your account'],
            ['label' => 'Open assignments', 'value' => $learnerData['metrics']['assignments'] ?? 0, 'hint' => 'Assigned to you'],
            ['label' => 'App visits', 'value' => $learningSnapshot['appVisits'] ?? 0, 'hint' => 'Saved learning sessions'],
            ['label' => 'Your profile', 'value' => $completion.'%', 'hint' => 'Personalisation progress'],
        ],
    };
    $p07Primary = match ($p07Role) {
        'teacher' => ['href' => '#sb36-teacher-actions', 'label' => 'Create a class'],
        'parent' => ['href' => '#sbp07-workspace', 'label' => 'Connect a child'],
        'independent' => ['href' => url('/my-quest'), 'label' => 'Open My Quest'],
        default => ['href' => url('/apps'), 'label' => 'Find a learning app'],
    };
    $p07Shortcuts = match ($p07Role) {
        'teacher' => [
            ['title' => 'Classroom tools', 'description' => 'Classes, rosters and assignments', 'href' => '#sbp07-workspace', 'image' => 'reading'],
            ['title' => 'Explore activities', 'description' => 'Find learning apps for a class', 'href' => url('/apps'), 'image' => 'math'],
            ['title' => 'Study Space', 'description' => 'Keep private planning notes', 'href' => route('studybuddy.study-space'), 'image' => 'science'],
        ],
        'parent' => [
            ['title' => 'Family connections', 'description' => 'Manage linked learner accounts', 'href' => '#sbp07-workspace', 'image' => 'reading'],
            ['title' => 'Discover apps', 'description' => 'Explore activities together', 'href' => url('/apps'), 'image' => 'math'],
            ['title' => 'Privacy and safety', 'description' => 'Understand family controls', 'href' => url('/privacy-policy'), 'image' => 'geography'],
        ],
        default => [
            ['title' => 'Number Galaxy', 'description' => 'Play a quick maths activity', 'href' => url('/learning/math-sprint'), 'image' => 'math'],
            ['title' => 'Explore worlds', 'description' => 'Find a subject that excites you', 'href' => url('/subjects'), 'image' => 'science'],
            ['title' => 'Study Space', 'description' => 'Keep your private study notes', 'href' => route('studybuddy.study-space'), 'image' => 'reading'],
        ],
    };
@endphp
<section id="sbp07-dashboard" class="sbp07" data-role="{{ $p07Role }}" aria-labelledby="sbp07-title">
    <header class="sbp07-hero">
        <img src="{{ asset('assets/studybuddy-p07/banners/'.$p07Hero.'.webp') }}" alt="" aria-hidden="true" fetchpriority="high" decoding="async" class="sbp07-hero-art">
        <div class="sbp07-hero-copy">
            <p class="sbp07-eyebrow">✦ Your {{ $p07Role === 'teacher' ? 'teaching' : ($p07Role === 'parent' ? 'family' : 'learning') }} universe</p>
            <h1 id="sbp07-title">{{ $p07Title }}</h1>
            <p>{{ $p07Description }}</p>
            <div class="sbp07-actions">
                <a class="sbp07-button sbp07-button-primary" href="{{ $p07Primary['href'] }}">{{ $p07Primary['label'] }} <span aria-hidden="true">↗</span></a>
                <a class="sbp07-button sbp07-button-ghost" href="{{ route('studybuddy.study-space') }}">My Study Space</a>
            </div>
        </div>
        <div class="sbp07-identity" aria-label="Signed-in account overview">
            <div class="sbp07-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name ?? 'S', 0, 1)) }}</div>
            <div class="sbp07-identity-text"><strong>{{ $user->name }}</strong><span>{{ $displayRole }}</span></div>
            <div class="sbp07-identity-progress"><span>Profile {{ $completion }}%</span><progress value="{{ $completion }}" max="100">{{ $completion }}%</progress></div>
        </div>
    </header>

    <nav class="sbp07-nav" aria-label="Dashboard sections">
        <a href="#sbp07-dashboard" aria-current="page">Overview</a>
        <a href="#sbp07-next">Your next step</a>
        <a href="#sbp07-workspace">{{ $p07Role === 'teacher' ? 'Classroom tools' : ($p07Role === 'parent' ? 'Family tools' : 'Your workspace') }}</a>
        <a href="{{ url('/profile') }}">Personalise</a>
    </nav>

    <div class="sbp07-metrics" aria-label="Real account statistics">
        @foreach($p07Stats as $stat)
            <article class="sbp07-metric"><span>{{ $stat['label'] }}</span><strong>{{ is_numeric($stat['value']) ? number_format((int) $stat['value']) : $stat['value'] }}</strong><small>{{ $stat['hint'] }}</small></article>
        @endforeach
    </div>

    <div id="sbp07-next" class="sbp07-grid">
        <section class="sbp07-panel sbp07-panel-focus" aria-labelledby="sbp07-focus-title">
            <div class="sbp07-panel-heading"><span class="sbp07-eyebrow">UP NEXT</span><h2 id="sbp07-focus-title">{{ $p07Role === 'teacher' ? 'Your classrooms' : ($p07Role === 'parent' ? 'Your connected family' : 'Your next move') }}</h2></div>
            @if($p07Role === 'teacher')
                <div class="sbp07-list">
                    @forelse($teacherData['groups']->take(3) as $group)
                    <a href="#sbp07-workspace"><span class="sbp07-list-icon">✎</span><span><strong>{{ $group->name }}</strong><small>{{ $teacherData['members']->where('group_id', $group->id)->count() }} connected roster entries</small></span><span aria-hidden="true">↗</span></a>
                    @empty
                    <div class="sbp07-empty"><strong>Your first class starts here.</strong><p>Create a class, then invite verified learners using their Connect Codes.</p><a href="#sb36-teacher-actions">Create a class ↗</a></div>
                    @endforelse
                </div>
            @elseif($p07Role === 'parent')
                <div class="sbp07-list">
                    @forelse($parentData['children']->where('status', 'connected_with_code')->take(3) as $child)
                    <a href="#sbp07-workspace"><span class="sbp07-list-icon">♡</span><span><strong>{{ $child->display_name ?? 'Connected learner' }}</strong><small>Connected with a learner code</small></span><span aria-hidden="true">↗</span></a>
                    @empty
                    <div class="sbp07-empty"><strong>No children connected yet.</strong><p>Ask your child for their Connect Code and link their account safely.</p><a href="#sbp07-workspace">Open family tools ↗</a></div>
                    @endforelse
                </div>
            @else
                <div class="sbp07-list">
                    @forelse($assignments->where('status', 'assigned')->take(3) as $task)
                    <a href="#sbp07-workspace"><span class="sbp07-list-icon">✦</span><span><strong>{{ $task->title }}</strong><small>{{ $task->due_at ? 'Due '.$humanDate($task->due_at) : 'No deadline' }}</small></span><span aria-hidden="true">↗</span></a>
                    @empty
                    <div class="sbp07-empty"><strong>Nothing assigned right now.</strong><p>Choose a learning world, make a personal goal or explore a new app.</p><a href="{{ url('/apps') }}">Browse apps ↗</a></div>
                    @endforelse
                </div>
            @endif
            <a class="sbp07-text-link" href="#sbp07-workspace">See your full workspace <span aria-hidden="true">↗</span></a>
        </section>
        <section class="sbp07-panel sbp07-panel-worlds" aria-labelledby="sbp07-worlds-title">
            <div class="sbp07-panel-heading"><span class="sbp07-eyebrow">EXPLORE</span><h2 id="sbp07-worlds-title">Places to go next</h2></div>
            <div class="sbp07-world-grid">
                @foreach($p07Shortcuts as $card)
                <a href="{{ $card['href'] }}" class="sbp07-world">
                    <img src="{{ asset('assets/studybuddy-p07/icons/'.$card['image'].'.webp') }}" alt="" aria-hidden="true" loading="lazy" decoding="async">
                    <strong>{{ $card['title'] }}</strong><small>{{ $card['description'] }}</small>
                </a>
                @endforeach
            </div>
        </section>
    </div>
</section>
