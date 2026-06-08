@extends('layouts.app')

@section('title', $title)
@section('body_class', 'page-shell page-student-demo page-student-' . $audience)

@section('content')
@if($audience === 'primary')
@php
    $asset = fn (string $file): string => asset('assets/studybuddy/' . $file);

    $primaryApps = [
        ['title' => 'Math Quest', 'img' => 'app-math-quest.png', 'url' => route('apps.math-quest')],
        ['title' => 'Spelling Sprint', 'img' => 'app-spelling-sprint.png', 'url' => route('apps.index')],
        ['title' => 'Reading Garden', 'img' => 'app-reading-garden.png', 'url' => route('apps.index')],
        ['title' => 'Shapes Lab', 'img' => 'app-shapes-lab.png', 'url' => route('apps.index')],
    ];

    $primaryBadges = [
        ['icon' => '📖', 'label' => 'Star Reader'],
        ['icon' => '➕', 'label' => 'Math Whiz'],
        ['icon' => '🏆', 'label' => 'Quiz Champ'],
        ['icon' => '💗', 'label' => 'Helper'],
        ['icon' => '🎯', 'label' => 'Focus Master'],
        ['icon' => '🪐', 'label' => 'Story Explorer'],
    ];
@endphp
<section class="kid-dashboard primary-student-dashboard reveal-on-load" aria-labelledby="primary-dashboard-title">
    <aside class="kid-sidebar" aria-label="Primary dashboard navigation">
        <a class="kid-brand" href="{{ route('home') }}">
            <img src="{{ $asset('logo-icon.png') }}" alt="StudyBuddy logo">
            <strong>Study<span>Buddy</span></strong>
        </a>
        <nav class="kid-side-nav">
            <a class="is-active" href="#"><span>▦</span>Dashboard</a>
            <a href="{{ route('apps.index') }}"><span>✾</span>Apps</a>
            <a href="#"><span>☑</span>Missions</a>
            <a href="{{ route('rewards') }}"><span>⚙</span>Rewards</a>
            <a href="#"><span>♡</span>My Buddy</a>
            <a href="#"><span>↺</span>Progress</a>
            <a href="#"><span>⚙</span>Settings</a>
        </nav>
        <div class="kid-weekly-goal">
            <h2>Weekly Goal! 🔥</h2>
            <p>Keep learning every day to earn big rewards!</p>
            <div class="kid-progress-line"><span style="width: 68%"></span></div>
            <small>4 / 7 Days</small>
        </div>
        <div class="kid-sidebar-star" aria-hidden="true">
            <span class="kid-orbit kid-orbit-a"></span>
            <span class="kid-orbit kid-orbit-b"></span>
            <i></i>
        </div>
    </aside>

    <main class="kid-main">
        <div class="kid-topbar">
            <span></span>
            <div class="kid-user-actions">
                <button type="button" aria-label="Notifications">🔔<em>3</em></button>
                <button class="kid-profile-chip" type="button"><span>👧🏽</span><strong>Zara<small>Age 8</small></strong><i>⌄</i></button>
            </div>
        </div>

        <section class="kid-hero-panel">
            <div class="kid-hero-stars" aria-hidden="true"></div>
            <header class="kid-welcome">
                <h1 id="primary-dashboard-title">Hi <span>Zara!</span> 👋</h1>
                <p>Ready for today’s adventure?</p>
            </header>

            <div class="kid-top-stats" aria-label="Primary stats">
                <article><span>⭐</span><strong>120</strong><small>Stars</small></article>
                <article><span>🪙</span><strong>340</strong><small>Buddy Coins</small></article>
                <article><span>🔥</span><strong>5 days</strong><small>Streak</small></article>
            </div>

            <article class="kid-mission-card">
                <h2><span>🎯</span>Today’s Mission</h2>
                <ul>
                    <li><span>🎮</span>Complete 2 Math Quest lessons <strong>1 / 2</strong><i>★</i></li>
                    <li><span>📚</span>Read a story in Reading Garden <strong>0 / 1</strong><i>★</i></li>
                    <li><span>🏆</span>Play 1 Quiz in Quiz Galaxy <strong>0 / 1</strong><i>★</i></li>
                </ul>
            </article>

            <div class="kid-buddy-zone" aria-label="StudyBuddy mascot">
                <span class="kid-buddy-star kid-buddy-star-a">★</span>
                <span class="kid-buddy-star kid-buddy-star-b">★</span>
                <img src="{{ $asset('hero-dolphin-book.png') }}" alt="StudyBuddy dolphin mascot floating on a cloud">
                <div class="kid-buddy-cloud"><span></span><span></span><span></span></div>
                <div class="kid-chat-bubble"><strong>Hi Zara! 🌟</strong><p>Let's learn, play and grow together!</p><small>Zappy 💜</small></div>
            </div>

            <aside class="kid-level-card">
                <div><span>⭐</span><strong>Level<br>12</strong><em>Star Learner</em></div>
                <p>XP Progress <b>2,350 <small>/ 2,800</small></b></p>
                <div class="kid-xp-track"><span style="width: 84%"></span></div>
            </aside>

            <section class="kid-apps-card" aria-labelledby="kid-apps-title">
                <h2 id="kid-apps-title">Your Apps</h2>
                <div class="kid-app-grid">
                    @foreach($primaryApps as $app)
                        <a href="{{ $app['url'] }}" class="kid-app-tile">
                            <img src="{{ $asset($app['img']) }}" alt="{{ $app['title'] }} app icon">
                            <strong>{{ $app['title'] }}</strong>
                            <span>Start</span>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="kid-badges-card" aria-labelledby="kid-badges-title">
                <div><h2 id="kid-badges-title">Recent Badges</h2><a href="{{ route('rewards') }}">View all</a></div>
                <div class="kid-badge-grid">
                    @foreach($primaryBadges as $badge)
                        <span><i>{{ $badge['icon'] }}</i><small>{{ $badge['label'] }}</small></span>
                    @endforeach
                </div>
            </section>

            <section class="kid-daily-card" aria-labelledby="kid-daily-title">
                <h2 id="kid-daily-title">Daily Progress</h2>
                <div class="kid-daily-grid">
                    <article><span>📖</span><strong>3</strong><small>Lessons Completed<br><em>+1 today</em></small></article>
                    <article><span>🏆</span><strong>85%</strong><small>Quiz Score<br><em>Great job!</em></small></article>
                    <article><span>⏱</span><strong>45 min</strong><small>Focus Time<br><em>+10 min</em></small></article>
                    <article><span>📋</span><strong>6 / 10</strong><small>Activities Done<br><em>Keep going!</em></small></article>
                </div>
            </section>

            <article class="kid-encourage-card"><span>⭐</span><div><h2>You’re doing amazing, Zara!</h2><p>Every small step makes big dreams come true! ✨</p></div></article>
            <article class="kid-progress-card"><h2>Your Learning Progress <span>250 / 500 XP</span></h2><div><span style="width: 62%"></span></div><p>Keep it up! You're on your way to the next level! 🚀</p></article>
            <article class="kid-streak-card"><h2>🔥 Learning Streak <span>5 days</span></h2><p>Fantastic! Keep your streak alive!</p><div><span>✓<small>Mon</small></span><span>✓<small>Tue</small></span><span>✓<small>Wed</small></span><span>✓<small>Thu</small></span><span>✓<small>Fri</small></span><span>✓<small>Sat</small></span><span>✓<small>Sun</small></span></div><img src="{{ $asset('app-spelling-sprint.png') }}" alt="Rocket app icon"></article>
            <article class="kid-fact-card"><h2>💡 Fun Fact of the Day</h2><p>Dolphins have names for each other!</p><a href="#">Cool!</a><img src="{{ $asset('hero-dolphin-book.png') }}" alt="StudyBuddy dolphin mascot"></article>
        </section>
    </main>
</section>
@elseif($audience === 'secondary')
@php
    $asset = fn (string $file): string => asset('assets/studybuddy/' . $file);

    $learning = [
        ['title' => 'Quadratic Equations', 'subject' => 'Math', 'img' => 'app-math-quest.png', 'progress' => '60%'],
        ['title' => 'Photosynthesis', 'subject' => 'Science', 'img' => 'app-reading-garden.png', 'progress' => '40%'],
        ['title' => 'Essay Writing', 'subject' => 'English', 'img' => 'app-planner-city.png', 'progress' => '30%'],
        ['title' => 'The Solar System', 'subject' => 'Science', 'img' => 'app-quiz-galaxy.png', 'progress' => '20%'],
    ];
@endphp
<section class="teen-dashboard secondary-student-dashboard reveal-on-load" aria-labelledby="secondary-dashboard-title">
    <aside class="teen-sidebar" aria-label="Secondary dashboard navigation">
        <a class="teen-brand" href="{{ route('home') }}">
            <img src="{{ $asset('logo-icon.png') }}" alt="StudyBuddy logo">
            <strong>Study<span>Buddy</span></strong>
        </a>
        <nav class="teen-side-nav">
            <a class="is-active" href="#"><span>⌂</span>Dashboard</a>
            <a href="{{ route('apps.index') }}"><span>▦</span>My Apps</a>
            <a href="#"><span>▱</span>Homework</a>
            <a href="#"><span>⏱</span>Focus Timer</a>
            <a href="#"><span>?</span>Quizzes</a>
            <a href="#"><span>□</span>Flashcards</a>
            <a href="#"><span>▥</span>Progress</a>
            <a href="{{ route('rewards') }}"><span>♕</span>Rewards</a>
            <a href="#"><span>✉</span>Messages</a>
            <a href="#"><span>⚙</span>Settings</a>
        </nav>
        <div class="teen-profile-card"><span>👩🏽</span><div><strong>Mehak</strong><small>Edit Profile ↗</small></div><em>Level 12</em></div>
        <div class="teen-streak-card"><span>🔥</span><strong>7</strong><small>Day Streak</small></div>
    </aside>

    <main class="teen-main">
        <header class="teen-header">
            <div><h1 id="secondary-dashboard-title">Welcome back, Mehak! 🚀</h1><p>Let’s crush your goals today.</p></div>
            <div class="teen-header-actions"><button aria-label="Search">⌕</button><button aria-label="Notifications">♧<em></em></button><button><span>👩🏽</span><strong>Mehak</strong><i>⌄</i></button></div>
        </header>

        <section class="teen-layout-grid">
            <div class="teen-left-column">
                <div class="teen-metric-grid" aria-label="Secondary dashboard stats">
                    <article><span class="teen-icon-badge">⭐</span><div><small>Level</small><strong>12</strong><em>Star Learner</em></div></article>
                    <article><span class="teen-icon-badge">🧪</span><div><small>XP</small><strong>2,350</strong><div class="teen-mini-track"><span style="width: 56%"></span></div><em>2,350 / 4,200</em></div></article>
                    <article><span class="teen-icon-badge">🪙</span><div><small>Buddy Coins</small><strong>320</strong></div></article>
                    <article><span class="teen-icon-badge">🔥</span><div><small>Streak</small><strong>7</strong><em>days</em></div></article>
                </div>

                <div class="teen-plan-timer-grid">
                    <article class="teen-plan-card">
                        <h2>Today’s Plan</h2>
                        <ul>
                            <li><span class="ring red"></span>Math: Quadratic Equations <strong>0 / 1</strong></li>
                            <li><span class="ring amber"></span>Science: Photosynthesis <strong>0 / 1</strong></li>
                            <li><span class="ring cyan"></span>English: Essay Writing <strong>0 / 1</strong></li>
                            <li><span class="ring done">✓</span>Focus Time — 30 min <strong>0 / 30 min</strong></li>
                        </ul>
                        <a href="#">View All Tasks <span>›</span></a>
                    </article>

                    <article class="teen-timer-card">
                        <h2>Focus Timer</h2>
                        <div class="teen-timer-ring"><strong>25:00</strong><button type="button">Start</button></div>
                        <div class="teen-timer-pills"><button class="is-active">25 min</button><button>15 min</button><button>30 min</button><button>45 min</button><button>⚙</button></div>
                    </article>
                </div>

                <section class="teen-learning-card" aria-labelledby="teen-learning-title">
                    <div><h2 id="teen-learning-title">Continue Learning</h2><a href="{{ route('apps.index') }}">View All ›</a></div>
                    <p>Pick up where you left off</p>
                    <div class="teen-learning-row">
                        @foreach($learning as $item)
                            <article>
                                <img src="{{ $asset($item['img']) }}" alt="{{ $item['title'] }} icon">
                                <div><strong>{{ $item['title'] }}</strong><small>{{ $item['subject'] }}</small><span><i style="width: {{ $item['progress'] }}"></i></span></div>
                                <em>{{ $item['progress'] }}</em>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="teen-quick-card" aria-labelledby="teen-quick-title">
                    <h2 id="teen-quick-title">Quick Actions</h2>
                    <div class="teen-quick-row">
                        <a href="#"><span>?</span><strong>Take Quiz</strong><small>Test your knowledge</small></a>
                        <a href="#"><span>▤</span><strong>Use Flashcards</strong><small>Review & memorize</small></a>
                        <a href="#"><span>☰</span><strong>Start Homework</strong><small>View assignments</small></a>
                        <a href="#"><span>🤖</span><strong>Ask Buddy AI</strong><small>Get help instantly</small></a>
                        <img src="{{ $asset('hero-dolphin-book.png') }}" alt="StudyBuddy dolphin mascot">
                    </div>
                </section>
            </div>

            <aside class="teen-right-column">
                <section class="teen-weekly-card" aria-labelledby="teen-weekly-title">
                    <div><h2 id="teen-weekly-title">Weekly Progress</h2><button>This Week⌄</button></div>
                    <div class="teen-line-chart"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><b></b><i></i></div>
                    <div class="teen-chart-labels"><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span></div>
                    <div class="teen-weekly-stats"><span>⏱<strong>6h 45m</strong><small>+1h 20m</small></span><span>📘<strong>28</strong><small>+ 8</small></span><span>❔<strong>18</strong><small>+ 5</small></span><span>🎯<strong>85%</strong><small>+12%</small></span></div>
                </section>

                <section class="teen-strength-card" aria-labelledby="teen-strength-title">
                    <div><h2 id="teen-strength-title">Subject Strength</h2><a href="#">View Details ›</a></div>
                    <ul>
                        <li><span>✥</span>Math <b><i style="width: 90%"></i></b><strong>90%</strong><em>Excellent</em></li>
                        <li><span>⚗</span>Science <b><i style="width: 80%"></i></b><strong>80%</strong><em>Good</em></li>
                        <li><span>📖</span>English <b><i style="width: 75%"></i></b><strong>75%</strong><em>Good</em></li>
                        <li><span>🌎</span>Social Studies <b><i style="width: 65%"></i></b><strong>65%</strong><em>Keep Going</em></li>
                        <li><span>▣</span>Computer <b><i style="width: 70%"></i></b><strong>70%</strong><em>Good</em></li>
                    </ul>
                </section>

                <section class="teen-activity-card" aria-labelledby="teen-activity-title">
                    <div><h2 id="teen-activity-title">Recent Activity</h2><a href="#">View All ›</a></div>
                    <ul>
                        <li><span>✓</span><div><strong>Completed Math: Quadratic Equations</strong><small>Today, 10:30 AM</small></div><em>+ 50 XP</em></li>
                        <li><span>★</span><div><strong>Scored 90% in Science Quiz</strong><small>Today, 9:15 AM</small></div><em>+ 30 XP</em></li>
                        <li><span>⏱</span><div><strong>Focus session completed (25 min)</strong><small>Yesterday, 8:45 PM</small></div><em>+ 25 XP</em></li>
                    </ul>
                </section>
            </aside>
        </section>

        <section class="teen-reward-banner">
            <span>🏆</span><div><h2>Keep it up, Mehak! 🌟</h2><p>Consistency today, success tomorrow.</p></div><a href="{{ route('rewards') }}">View Rewards</a>
        </section>
    </main>
</section>
@elseif($audience === 'parent')
@php
    $asset = fn (string $file): string => asset('assets/studybuddy/' . $file);

    $parentLessons = [
        ['icon' => '👩🏽', 'title' => 'How to help your child stay focused', 'action' => 'Read Article'],
        ['icon' => '👩🏽', 'title' => 'Fun ways to improve math at home', 'action' => 'Read Article'],
        ['icon' => '👩🏽', 'title' => 'Build a better study routine', 'action' => 'Read Article'],
    ];
@endphp
<section class="parent-pro-dashboard reveal-on-load" aria-labelledby="parent-dashboard-title">
    <aside class="parent-pro-sidebar" aria-label="Parent dashboard navigation">
        <a class="parent-pro-brand" href="{{ route('home') }}">
            <img src="{{ $asset('logo-icon.png') }}" alt="StudyBuddy logo">
            <strong>Study<span>Buddy</span></strong>
        </a>
        <nav class="parent-pro-nav">
            <a class="is-active" href="#"><span>⌂</span>Dashboard</a>
            <a href="#"><span>👥</span>My Children</a>
            <a href="#"><span>▥</span>Progress Reports</a>
            <a href="#"><span>📖</span>Learning Tools</a>
            <a href="#"><span>▱</span>Parent Learning</a>
            <a href="#"><span>⚙</span>Settings</a>
            <a href="#"><span>↪</span>Logout</a>
        </nav>
        <div class="parent-profile-card">
            <span>👩🏽</span>
            <div><strong>Mehak’s Parent</strong><small>mom@example.com</small></div>
            <i>⌄</i>
        </div>
        <a class="parent-help-card" href="#"><span>🎧</span>Need Help?</a>
        <div class="parent-book-decor" aria-hidden="true">
            <span class="parent-mini-planet"></span>
            <span class="parent-mini-star parent-mini-star-a"></span>
            <span class="parent-mini-star parent-mini-star-b"></span>
            <i></i>
        </div>
    </aside>

    <main class="parent-pro-main">
        <section class="parent-pro-shell">
            <header class="parent-pro-header">
                <div><h1 id="parent-dashboard-title">Welcome, Mom! ❤</h1><p>Here’s how Mehak is doing this week.</p></div>
                <button type="button"><span>▣</span>This Week <i>⌄</i></button>
            </header>

            <div class="parent-mascot-scene" aria-hidden="true">
                <span class="parent-scene-planet"></span>
                <img src="{{ $asset('hero-dolphin-book.png') }}" alt="">
                <span class="parent-scene-ring"></span>
            </div>

            <section class="parent-progress-summary" aria-labelledby="parent-progress-title">
                <h2 id="parent-progress-title"><span>▥</span>Mehak’s Progress</h2>
                <div class="parent-summary-grid">
                    <article><span class="parent-summary-icon">◷</span><small>Total Study Time</small><strong>6h 45m</strong><em>+ 1h 20m</em></article>
                    <article><span class="parent-summary-icon blue">☑</span><small>Lessons Completed</small><strong>28</strong><em>+ 8 ↑</em></article>
                    <article><span class="parent-summary-icon gold">🏆</span><small>Quiz Score</small><strong>85%</strong><em>+12% ↑</em></article>
                    <article><span class="parent-summary-icon violet">◎</span><small>Focus Time</small><strong>3h 20m</strong><em>+ 45m ↑</em></article>
                </div>
            </section>

            <section class="parent-strength-panel" aria-labelledby="parent-strength-title">
                <h2 id="parent-strength-title">⭐ Subject Strength</h2>
                <ul>
                    <li><span>▦</span><strong>Math</strong><b><i style="width: 90%"></i></b><em>90%</em></li>
                    <li><span>📗</span><strong>Reading</strong><b><i style="width: 80%"></i></b><em>80%</em></li>
                    <li><span>⚗</span><strong>Science</strong><b><i style="width: 70%"></i></b><em>70%</em></li>
                    <li><span>🔤</span><strong>Spelling</strong><b><i style="width: 65%"></i></b><em>65%</em></li>
                </ul>
                <div class="parent-praise-card"><span>⭐</span><p><strong>Great job!</strong> Mehak is showing consistent progress.<br>Keep encouraging!</p><i>💜</i></div>
            </section>

            <section class="parent-activity-panel" aria-labelledby="parent-activity-title">
                <h2 id="parent-activity-title">◷ Recent Activity</h2>
                <ul>
                    <li><span>✓</span><div><strong>Completed Math Quest Lessons</strong><small>Math</small></div><time>15m ago</time><i>›</i></li>
                    <li><span>✓</span><div><strong>Read a story in Reading Garden</strong><small>Reading</small></div><time>2h ago</time><i>›</i></li>
                    <li><span>★</span><div><strong>Scored 90% in Quiz Galaxy</strong><small>Science</small></div><time>Yesterday</time><i>›</i></li>
                    <li><span>◷</span><div><strong>Focus session completed</strong><small>Focus Forest</small></div><time>Yesterday</time><i>›</i></li>
                </ul>
                <a href="#">View All Activity <span>›</span></a>
            </section>

            <aside class="parent-learning-panel" aria-labelledby="parent-learning-title">
                <h2 id="parent-learning-title">💜 Parent Learning Corner</h2>
                <div class="parent-learning-list">
                    @foreach($parentLessons as $lesson)
                        <article><div><h3>{{ $lesson['title'] }}</h3><a href="#">{{ $lesson['action'] }}</a></div><span>{{ $lesson['icon'] }}</span></article>
                    @endforeach
                </div>
                <a class="parent-all-resources" href="#">View all resources <span>›</span></a>
            </aside>

            <aside class="parent-tip-card"><h2>🪐 Tip of the Week</h2><p>Short breaks help boost focus and long-term retention.</p><span></span></aside>
            <article class="parent-quote-banner"><span>“</span><p>Every small step today builds a brighter tomorrow.<br><small>Keep believing, keep encouraging! 🌟</small></p><span>”</span><i></i></article>
        </section>
    </main>
</section>
@elseif($audience === 'teacher')
@php
    $asset = fn (string $file): string => asset('assets/studybuddy/' . $file);

    $teacherAssignments = [
        ['icon' => '➗', 'title' => 'Math Quiz – Fractions', 'class' => 'Class 7A', 'due' => 'Due in 2 days', 'count' => '10 Students', 'tone' => 'purple'],
        ['icon' => '🚀', 'title' => 'Science Worksheet', 'class' => 'Class 8A', 'due' => 'Due in 3 days', 'count' => '23 Students', 'tone' => 'blue'],
        ['icon' => '📖', 'title' => 'Reading Comprehension', 'class' => 'Class 9A', 'due' => 'Due in 3 days', 'count' => '28 Students', 'tone' => 'green'],
        ['icon' => '✏️', 'title' => 'Grammar Practice', 'class' => 'Class 10A', 'due' => 'Due in 5 days', 'count' => '19 Students', 'tone' => 'orange'],
    ];
@endphp
<section class="teacher-pro-dashboard reveal-on-load" aria-labelledby="teacher-dashboard-title">
    <aside class="teacher-pro-sidebar" aria-label="Teacher dashboard navigation">
        <a class="teacher-pro-brand" href="{{ route('home') }}"><img src="{{ $asset('logo-icon.png') }}" alt="StudyBuddy logo"><strong>Study<span>Buddy</span></strong></a>
        <nav class="teacher-pro-nav">
            <a class="is-active" href="#"><span>⌂</span>Dashboard</a>
            <a href="#"><span>👥</span>Classes</a>
            <a href="#"><span>♙</span>Students</a>
            <a href="#"><span>▣</span>Assignments</a>
            <a href="#"><span>?</span>Quizzes</a>
            <a href="#"><span>▥</span>Reports</a>
            <a href="#"><span>📖</span>Resources</a>
            <a href="#"><span>⚙</span>Settings</a>
        </nav>
        <div class="teacher-tip-card"><img src="{{ $asset('hero-dolphin-book.png') }}" alt="StudyBuddy dolphin mascot"><h2>Teaching Tips ⭐</h2><p>Explore tips and best practices to engage students.</p><a href="#">Explore Now</a></div>
    </aside>

    <main class="teacher-pro-main">
        <section class="teacher-pro-shell">
            <header class="teacher-pro-header">
                <div><h1 id="teacher-dashboard-title">Good morning, Teacher! 👩🏽‍🏫</h1><p>Here’s what’s happening in your classes today.</p></div>
                <div class="teacher-top-actions"><button aria-label="Notifications">♧<em></em></button><button><span>👩🏽</span>Mrs. Arman <i>⌄</i></button></div>
            </header>

            <div class="teacher-hero-decor" aria-hidden="true">
                <span class="teacher-planet"></span>
                <img src="{{ $asset('hero-dolphin-book.png') }}" alt="">
                <i></i>
            </div>

            <section class="teacher-stat-row" aria-label="Teacher stats">
                <article><span>👥</span><div><small>Classes</small><strong>5</strong><em>Active Classes</em></div></article>
                <article><span>👥</span><div><small>Students</small><strong>120</strong><em>Total Students</em></div></article>
                <article><span>📋</span><div><small>Assignments</small><strong>12</strong><em>Active Assignments</em></div></article>
                <article><span>?</span><div><small>Quizzes</small><strong>8</strong><em>Upcoming Quizzes</em></div></article>
            </section>

            <section class="teacher-class-panel" aria-labelledby="teacher-class-title">
                <h2 id="teacher-class-title">🎓 Class Overview</h2>
                <div class="teacher-table-wrap">
                    <table>
                        <thead><tr><th>Class</th><th>Students</th><th>Avg. Score</th><th>Progress</th><th>Quizzes</th></tr></thead>
                        <tbody>
                            <tr><td><span>7A</span>Class 7A</td><td>24</td><td>85%</td><td><b><i style="width: 82%"></i></b><em>Good</em></td><td>10</td></tr>
                            <tr><td><span>7B</span>Class 7B</td><td>26</td><td>78%</td><td><b><i style="width: 72%"></i></b><em>Good</em></td><td>10</td></tr>
                            <tr><td><span>8A</span>Class 8A</td><td>23</td><td>80%</td><td><b><i style="width: 82%"></i></b><em>Good</em></td><td>10</td></tr>
                            <tr><td><span>9A</span>Class 9A</td><td>28</td><td>82%</td><td><b><i style="width: 82%"></i></b><em>Good</em></td><td>10</td></tr>
                            <tr><td><span>10A</span>Class 10A</td><td>19</td><td>75%</td><td><b class="average"><i style="width: 70%"></i></b><em class="average">Average</em></td><td>10</td></tr>
                        </tbody>
                    </table>
                </div>
                <a class="teacher-view-classes" href="#">View All Classes <span>›</span></a>
            </section>

            <section class="teacher-assign-panel" aria-labelledby="teacher-assign-title">
                <div><h2 id="teacher-assign-title">📋 Recent Assignments</h2><a href="#">View All</a></div>
                <div class="teacher-assignment-list">
                    @foreach($teacherAssignments as $assignment)
                        <article class="assignment-{{ $assignment['tone'] }}"><span>{{ $assignment['icon'] }}</span><div><h3>{{ $assignment['title'] }}</h3><small>{{ $assignment['class'] }}</small></div><time>{{ $assignment['due'] }}</time><em>{{ $assignment['count'] }}</em></article>
                    @endforeach
                </div>
                <a class="teacher-create-assignment" href="#">Create Assignment <span>＋</span></a>
            </section>
        </section>
    </main>
</section>
@else
<section class="dashboard-shell admin-dash reveal-on-load">
    <div class="student-sidebar glass-panel"><strong>🐬 Admin</strong><a class="active">Dashboard</a><a>Users</a><a>Apps</a><a>Content</a><a>Lessons</a><a>Rewards</a><a>System</a></div>
    <div class="admin-main glass-panel"><div class="dash-heading"><div><p class="eyebrow">Admin Dashboard</p><h1>Control everything.</h1></div></div><div class="metric-grid">@foreach($cards as $card) @include('partials.dashboard-card', ['card' => $card]) @endforeach</div><div class="admin-grid"><article class="table-card tilt-card"><h2>Manage Apps</h2><table><tr><th>App Name</th><th>Status</th><th>Users</th><th>Action</th></tr><tr><td>Math Quest</td><td>Live</td><td>4,210</td><td>✎ 🗑</td></tr><tr><td>Spelling Sprint</td><td>Live</td><td>3,120</td><td>✎ 🗑</td></tr><tr><td>Reading Garden</td><td>Live</td><td>2,940</td><td>✎ 🗑</td></tr><tr><td>Quiz Galaxy</td><td>Live</td><td>4,790</td><td>✎ 🗑</td></tr></table></article><article class="quick-controls tilt-card"><h2>Quick Controls</h2><button>Edit Homepage Content</button><button>Manage Banners</button><button>Manage Text All Pages</button><button>Manage Images & Media</button><button>Manage Download Links</button><button>System Settings</button></article></div></div>
</section>
@endif
@endsection
