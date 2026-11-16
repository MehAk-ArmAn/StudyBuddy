@php
    $user = auth()->user();
    $role = $user?->role ?? 'student';

    $cards = [
        'student' => [
            ['title' => 'Learning Hub', 'body' => 'Pick a path and keep practicing.', 'url' => url('/apps?section=learning')],
            ['title' => 'Rewards', 'body' => 'See points and progress rewards.', 'url' => url('/apps?section=rewards')],
            ['title' => 'My Quest', 'body' => 'Continue saved missions.', 'url' => \Illuminate\Support\Facades\Route::has('studybuddy.quests.index') ? route('studybuddy.quests.index') : url('/my-quest')],
        ],
        'parent' => [
            ['title' => 'Parent View', 'body' => 'Preview parent support tools.', 'url' => url('/apps?role=parent')],
            ['title' => 'Safety Support', 'body' => 'Review safe learning guidance.', 'url' => url('/apps?section=safety')],
            ['title' => 'Apps', 'body' => 'Explore learning apps for your child.', 'url' => url('/apps')],
        ],
        'teacher' => [
            ['title' => 'Teacher Tools', 'body' => 'Preview classroom-ready tools.', 'url' => url('/apps?role=teacher')],
            ['title' => 'Learning Hub', 'body' => 'Find learning experiences.', 'url' => url('/apps?section=learning')],
            ['title' => 'Apps', 'body' => 'Explore app catalog.', 'url' => url('/apps')],
        ],
        'independent_learner' => [
            ['title' => 'Learning Hub', 'body' => 'Build a self-paced routine.', 'url' => url('/apps?role=independent_learner')],
            ['title' => 'Rewards', 'body' => 'Track points and momentum.', 'url' => url('/apps?section=rewards')],
            ['title' => 'Roadmap', 'body' => 'See what is coming next.', 'url' => url('/apps?section=roadmap')],
        ],
    ];

    $items = $cards[$role] ?? $cards['student'];
@endphp

<section class="sb-role-compass sbv-dashboard-card">
    <div class="sb-role-compass__head">
        <p class="eyebrow">Role compass</p>
        <h2>{{ str_replace('_', ' ', ucfirst($role)) }} options</h2>
        <p>Your dashboard shortcuts are safe direct URLs now, so they won’t crash when old route names move.</p>
    </div>
    <div class="sb-role-compass__grid">
        @foreach($items as $item)
            <a class="sb-role-compass__card" href="{{ $item['url'] }}">
                <strong>{{ $item['title'] }}</strong>
                <span>{{ $item['body'] }}</span>
            </a>
        @endforeach
    </div>
</section>
