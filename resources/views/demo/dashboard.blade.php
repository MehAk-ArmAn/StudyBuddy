@extends('layouts.app')

@section('title', $title)

@section('content')
@if($audience === 'primary')
<section class="dashboard-shell primary-dash reveal-on-load">
    <div class="student-sidebar glass-panel"><strong>🐬 StudyBuddy</strong><a class="active">Dashboard</a><a>Apps</a><a>Missions</a><a>Rewards</a><a>My Buddy</a><a>Progress</a></div>
    <div class="primary-main glass-panel">
        <div class="dash-heading"><div><p class="eyebrow">Primary Student Dashboard</p><h1>Hi Zara! 👋</h1><p>Ready for today’s adventure?</p></div><div class="mini-stats">@foreach($cards as $card) @include('partials.dashboard-card', ['card' => $card]) @endforeach</div></div>
        <div class="primary-grid">
            <article class="mission-card tilt-card"><h2>Today’s Mission</h2><ul><li>Complete 2 Math Quest lessons <span>1/2</span></li><li>Read a story in Reading Garden <span>0/1</span></li><li>Play 1 quiz in Quiz Galaxy <span>0/1</span></li></ul><a class="button" href="{{ route('apps.math-quest.play') }}">Start Mission</a></article>
            <div class="buddy-cloud tilt-card">@include('partials.image-placeholder', ['label' => 'DASHBOARD_BUDDY_IMAGE', 'src' => 'assets/studybuddy/hero-dolphin-book.png', 'variant' => 'dash-buddy', 'caption' => 'Primary Buddy cloud art'])</div>
            <div class="mini-app-row"><span>Math Quest</span><span>Spelling Sprint</span><span>Reading Garden</span><span>Shapes Lab</span></div>
            <article class="progress-card tilt-card"><h3>Your Buddy</h3><p>Baby Buddy · Level 8</p><div class="progress-track"><span style="width: 62%"></span></div></article>
            <article class="badge-card tilt-card"><h3>Recent Badges</h3><div class="badge-row"><span>⭐</span><span>🏆</span><span>💎</span><span>🌙</span></div></article>
        </div>
    </div>
</section>
@elseif($audience === 'secondary')
<section class="dashboard-shell secondary-dash reveal-on-load">
    <div class="student-sidebar glass-panel"><strong>🐬 StudyBuddy</strong><a class="active">Dashboard</a><a>My Apps</a><a>Homework</a><a>Focus Timer</a><a>Quizzes</a><a>Progress</a><a>Rewards</a></div>
    <div class="secondary-main glass-panel">
        <div class="dash-heading"><div><p class="eyebrow">Secondary Student Dashboard</p><h1>Welcome back, Mehak! 🚀</h1><p>Let’s crush your goals today.</p></div></div>
        <div class="metric-grid">@foreach($cards as $card) @include('partials.dashboard-card', ['card' => $card]) @endforeach</div>
        <div class="cockpit-grid">
            <article class="plan-card tilt-card"><h2>Today’s Plan</h2><ul><li>Math: Quadratic Equations <span>0/1</span></li><li>Science: Photosynthesis <span>0/1</span></li><li>English: Essay Writing <span>0/1</span></li><li>Focus Time — 30 min <span>0/30</span></li></ul></article>
            <article class="timer-card tilt-card"><h2>Focus Timer</h2><div class="timer-ring">25:00</div><button class="button button-compact">Start</button></article>
            <article class="chart-card tilt-card"><h2>Weekly Progress</h2><div class="chart-bars"><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div><button class="button button-ghost button-compact">View Progress</button></article>
            <article class="continue-card tilt-card"><h2>Continue Learning</h2><div class="continue-row"><span>Math Quest</span><span>Quiz Galaxy</span><span>Flashcard Castle</span><span>Planner City</span></div></article>
        </div>
    </div>
</section>
@elseif($audience === 'parent')
<section class="dashboard-shell parent-dash reveal-on-load">
    <div class="student-sidebar glass-panel"><strong>🐬 StudyBuddy</strong><a class="active">Dashboard</a><a>My Children</a><a>Progress Reports</a><a>Learning Tools</a><a>Parent Learning</a></div>
    <div class="parent-main glass-panel">
        <div class="dash-heading"><div><p class="eyebrow">Parent Dashboard</p><h1>Welcome, Mom! 💗</h1><p>Here’s how Mehak is doing this week.</p></div><button class="button button-ghost button-compact">This Week⌄</button></div>
        <div class="metric-grid">@foreach($cards as $card) @include('partials.dashboard-card', ['card' => $card]) @endforeach</div>
        <div class="parent-grid"><article class="strength-card tilt-card"><h2>Subject Strength</h2><p>Math <span>90%</span></p><div><b style="width:90%"></b></div><p>Reading <span>80%</span></p><div><b style="width:80%"></b></div><p>Science <span>70%</span></p><div><b style="width:70%"></b></div><p>Spelling <span>65%</span></p><div><b style="width:65%"></b></div></article><article class="activity-card tilt-card"><h2>Recent Activity</h2><ul><li>Completed Math Quest Lesson</li><li>Read a story in Reading Garden</li><li>Scored 90% in Quiz Galaxy</li><li>Focus session completed</li></ul></article><aside class="learning-corner tilt-card"><h2>Parent Learning Corner</h2><p>How to help your child stay focused.</p><p>Fun ways to improve math at home.</p><p>Build a better study routine.</p></aside></div>
    </div>
</section>
@elseif($audience === 'teacher')
<section class="dashboard-shell teacher-dash reveal-on-load">
    <div class="student-sidebar glass-panel"><strong>🐬 StudyBuddy</strong><a class="active">Classes</a><a>Students</a><a>Assignments</a><a>Quizzes</a><a>Reports</a><a>Resources</a></div>
    <div class="teacher-main glass-panel"><div class="dash-heading"><div><p class="eyebrow">Teacher Dashboard</p><h1>Good morning, Teacher! 👩‍🏫</h1></div><button class="button">Create Assignment</button></div><div class="metric-grid">@foreach($cards as $card) @include('partials.dashboard-card', ['card' => $card]) @endforeach</div><div class="teacher-grid"><article class="table-card tilt-card"><h2>Class Overview</h2><table><tr><th>Class</th><th>Students</th><th>Avg score</th><th>Progress</th></tr><tr><td>Class 7A</td><td>24</td><td>85%</td><td>10</td></tr><tr><td>Class 7B</td><td>26</td><td>78%</td><td>9</td></tr><tr><td>Class 8A</td><td>23</td><td>80%</td><td>11</td></tr><tr><td>Class 9A</td><td>28</td><td>82%</td><td>10</td></tr></table></article><article class="assign-card tilt-card"><h2>Recent Assignments</h2><p>Math Quiz — Fractions <span>Due in 2 days</span></p><p>Science Worksheet <span>Due in 3 days</span></p><p>Reading Comprehension <span>Due in 5 days</span></p></article></div></div>
</section>
@else
<section class="dashboard-shell admin-dash reveal-on-load">
    <div class="student-sidebar glass-panel"><strong>🐬 Admin</strong><a class="active">Dashboard</a><a>Users</a><a>Apps</a><a>Content</a><a>Lessons</a><a>Rewards</a><a>System</a></div>
    <div class="admin-main glass-panel"><div class="dash-heading"><div><p class="eyebrow">Admin Dashboard</p><h1>Control everything.</h1></div></div><div class="metric-grid">@foreach($cards as $card) @include('partials.dashboard-card', ['card' => $card]) @endforeach</div><div class="admin-grid"><article class="table-card tilt-card"><h2>Manage Apps</h2><table><tr><th>App Name</th><th>Status</th><th>Users</th><th>Action</th></tr><tr><td>Math Quest</td><td>Live</td><td>4,210</td><td>✎ 🗑</td></tr><tr><td>Spelling Sprint</td><td>Live</td><td>3,120</td><td>✎ 🗑</td></tr><tr><td>Reading Garden</td><td>Live</td><td>2,940</td><td>✎ 🗑</td></tr><tr><td>Quiz Galaxy</td><td>Live</td><td>4,790</td><td>✎ 🗑</td></tr></table></article><article class="quick-controls tilt-card"><h2>Quick Controls</h2><button>Edit Homepage Content</button><button>Manage Banners</button><button>Manage Text All Pages</button><button>Manage Images & Media</button><button>Manage Download Links</button><button>System Settings</button></article></div></div>
</section>
@endif
@endsection
