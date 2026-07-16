@extends('layouts.app')

@section('title', 'StudyBuddy Roles')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-roles-universe.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-roles-universe.css')) ? filemtime(public_path('assets/css/studybuddy-roles-universe.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-roles-universe.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-roles-universe.js')) ? filemtime(public_path('assets/js/studybuddy-roles-universe.js')) : time() }}" defer></script>

@php
    $repo = 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main';
    $hero = 'https://github.com/MehAk-ArmAn/StudyBuddy-Imgs/blob/main/hero/hero-dolphin-book.png?raw=true';

    $roles = [
        'student' => [
            'label' => 'Student',
            'kicker' => 'Practice and progress',
            'title' => 'A focused dashboard for tiny learning wins.',
            'body' => 'Students get app recommendations, assignments, points, profile controls, favourite learning worlds, and a safe Connect Code for verified parent or teacher links.',
            'image' => $repo.'/homepage-paths/path-apps.png',
            'tools' => ['App worlds', 'Points and badges', 'Assignments', 'Profile Studio', 'Connect Code'],
            'cta' => '/dashboard',
        ],
        'parent' => [
            'label' => 'Parent',
            'kicker' => 'Support without pressure',
            'title' => 'A calm progress view for connected child accounts.',
            'body' => 'Parents can connect only with a learner’s current StudyBuddy Connect Code. After that, they can see progress signals, recent activity, and safety guidance.',
            'image' => $repo.'/homepage-paths/path-parents.png',
            'tools' => ['Consent-based child linking', 'Progress snapshots', 'Recent activity', 'Safety links', 'Privacy guidance'],
            'cta' => '/dashboard',
        ],
        'teacher' => [
            'label' => 'Teacher',
            'kicker' => 'Classroom command centre',
            'title' => 'Classes, verified students, tasks, quizzes, and activity.',
            'body' => 'Teachers can create classrooms, add verified students through Connect Codes, assign tasks, build quizzes, and review student activity from one readable dashboard.',
            'image' => $repo.'/homepage-paths/path-teachers.png',
            'tools' => ['Classrooms', 'Verified rosters', 'Assignments', 'Quiz planning', 'Student activity'],
            'cta' => '/dashboard',
        ],
        'independent' => [
            'label' => 'Independent',
            'kicker' => 'Self-paced growth',
            'title' => 'A flexible space for learners building their own routine.',
            'body' => 'Independent learners can use app worlds, set goals, track progress, build a public profile, and study at their own pace without classroom pressure.',
            'image' => $repo.'/homepage-paths/path-learning.png',
            'tools' => ['Self-paced apps', 'Goals', 'Progress style', 'Profile showcase', 'Focus tools'],
            'cta' => '/apps',
        ],
    ];
@endphp

<main class="roles-universe" data-roles-universe>
    <section class="roles-hero">
        <div class="roles-hero-copy">
            <p class="roles-kicker">StudyBuddy Roles</p>
            <h1>One learning universe. Different tools for every role.</h1>
            <p>StudyBuddy keeps each dashboard focused: learners practise, parents support, teachers guide, and independent learners grow at their own pace.</p>

            <div class="roles-hero-actions">
                <a href="{{ url('/register') }}">Create account</a>
                <a class="soft" href="{{ url('/dashboard') }}">Open dashboard</a>
            </div>
        </div>

        <div class="roles-hero-visual">
            <img src="{{ $hero }}" alt="StudyBuddy dolphin book hero">
        </div>
    </section>

    <section class="roles-tabs-card">
        <div class="roles-tab-buttons">
            @foreach($roles as $key => $role)
                <button type="button" class="{{ $loop->first ? 'active' : '' }}" data-role-tab="{{ $key }}">
                    <span>{{ $role['label'] }}</span>
                    <small>{{ $role['kicker'] }}</small>
                </button>
            @endforeach
        </div>

        <div class="roles-live-panel">
            <div>
                <p data-role-kicker>{{ $roles['student']['kicker'] }}</p>
                <h2 data-role-title>{{ $roles['student']['title'] }}</h2>
                <span data-role-body>{{ $roles['student']['body'] }}</span>

                <div class="roles-tool-list" data-role-tools>
                    @foreach($roles['student']['tools'] as $tool)
                        <strong>{{ $tool }}</strong>
                    @endforeach
                </div>

                <div class="roles-hero-actions">
                    <a data-role-cta href="{{ url($roles['student']['cta']) }}">Explore this role</a>
                    <a class="soft" href="{{ url('/community-guidelines') }}">Safety rules</a>
                </div>
            </div>

            <figure>
                <img data-role-image src="{{ $roles['student']['image'] }}" alt="Student role visual">
            </figure>
        </div>
    </section>

    <section class="roles-content-grid">
        <article>
            <p class="roles-kicker">Consent-first safety</p>
            <h2>No random child linking.</h2>
            <p>Parents and teachers cannot simply add any learner. The learner must share their current StudyBuddy Connect Code, and the code can be regenerated anytime.</p>
            <ul>
                <li>No password sharing</li>
                <li>No silent parent or teacher linking</li>
                <li>Learners control their current code</li>
                <li>Connected accounts appear in dashboards</li>
            </ul>
        </article>

        <article>
            <p class="roles-kicker">Connected dashboards</p>
            <h2>Each role sees what matters.</h2>
            <p>Students do not need parent tools. Parents do not need app-building controls. Teachers need classrooms. Independent learners need goals and self-paced progress.</p>
            <ul>
                <li>Student: practice, points, tasks</li>
                <li>Parent: connected child activity</li>
                <li>Teacher: classes and assignments</li>
                <li>Independent: flexible progress</li>
            </ul>
        </article>
    </section>

    <section class="roles-journey">
        <p class="roles-kicker">How it works</p>
        <h2>From account to progress in four steps.</h2>

        <div>
            <article><span>01</span><strong>Choose a role</strong><p>Select student, parent, teacher, or independent learner during signup.</p></article>
            <article><span>02</span><strong>Set up profile</strong><p>Add your learning goals, profile picture, app interests, and public showcase settings.</p></article>
            <article><span>03</span><strong>Use your tools</strong><p>Practise apps, connect learners safely, create classes, or track self-paced goals.</p></article>
            <article><span>04</span><strong>Review progress</strong><p>Use dashboards, points, activity feeds, and community profiles to keep learning visible.</p></article>
        </div>
    </section>

    <section class="roles-final-cta">
        <h2>Ready to enter StudyBuddy?</h2>
        <p>Start with your role, then build the learning space that fits you.</p>
        <div class="roles-hero-actions">
            <a href="{{ url('/register') }}">Create account</a>
            <a class="soft" href="{{ url('/apps') }}">Explore apps</a>
            <a class="soft" href="{{ url('/community') }}">See community</a>
        </div>
    </section>
</main>

<script>
    window.studyBuddyRolesData = @json($roles);
</script>
@endsection
