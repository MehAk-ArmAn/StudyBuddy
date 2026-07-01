@php
    $roleKey = strtolower((string) (auth()->user()->role ?? 'student'));
    $roleCards = [
        'student' => [
            'title' => 'Student path',
            'copy' => 'Practice in short quests, collect points, and keep your study streak gentle but consistent.',
            'links' => [['Apps', route('studybuddy.final.app-launchpad')], ['My Quest', route('studybuddy.quests.index')], ['Rewards', route('studybuddy.experience.rewards')]],
        ],
        'parent' => [
            'title' => 'Parent path',
            'copy' => 'Guide routines, check safety information, and help learners choose calm, age-aware practice.',
            'links' => [['Parents Center', route('studybuddy.experience.parents-center')], ['Safety', route('studybuddy.experience.safety-support')], ['Launchpad', route('studybuddy.final.app-launchpad')]],
        ],
        'teacher' => [
            'title' => 'Teacher path',
            'copy' => 'Turn mini-apps into classroom activities, save useful quests, and prepare simple learning flows.',
            'links' => [['Teacher Studio', route('studybuddy.experience.teacher-studio')], ['Learning Paths', route('studybuddy.experience.learning-paths')], ['Apps', route('studybuddy.final.app-launchpad')]],
        ],
        'independent_learner' => [
            'title' => 'Independent learner path',
            'copy' => 'Build your own routine with quests, focused sessions, points, and a clear personal roadmap.',
            'links' => [['Learning Hub', route('studybuddy.experience.learning-hub')], ['Wallet', route('studybuddy.final.points-wallet')], ['Roadmap', route('studybuddy.final.roadmap')]],
        ],
    ];
    $card = $roleCards[$roleKey] ?? $roleCards['student'];
@endphp
<section class="sb-role-compass auth-panel" aria-label="Role-aware StudyBuddy shortcuts">
    <div>
        <p class="eyebrow">Role compass</p>
        <h2>{{ $card['title'] }}</h2>
        <p>{{ $card['copy'] }}</p>
    </div>
    <div class="sb-role-compass__links">
        @foreach($card['links'] as [$label, $href])
            <a href="{{ $href }}">{{ $label }} <span>→</span></a>
        @endforeach
    </div>
</section>
