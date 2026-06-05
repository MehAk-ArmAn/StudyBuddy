@extends('layouts.app')

@section('title', 'Showcase')
@section('body_class', 'page-shell page-showcase')

@section('content')
@php
    $miniApps = [
        ['title' => 'Math Quest', 'img' => 'app-math-quest.png', 'rating' => '4.9'],
        ['title' => 'Spelling Sprint', 'img' => 'app-spelling-sprint.png', 'rating' => '4.8'],
        ['title' => 'Reading Garden', 'img' => 'app-reading-garden.png', 'rating' => '4.9'],
        ['title' => 'Focus Forest', 'img' => 'app-focus-forest.png', 'rating' => '4.7'],
        ['title' => 'Planner City', 'img' => 'app-planner-city.png', 'rating' => '4.8'],
        ['title' => 'Quiz Galaxy', 'img' => 'app-quiz-galaxy.png', 'rating' => '4.9'],
        ['title' => 'Shapes Lab', 'img' => 'app-shapes-lab.png', 'rating' => '4.8'],
        ['title' => 'Flashcard Castle', 'img' => 'app-flashcard-castle.png', 'rating' => '4.7'],
    ];
@endphp

<section class="showcase-board reveal-on-load">
    <div class="showcase-decor" aria-hidden="true">
        <img class="decor-planet decor-planet-ringed" src="{{ asset('assets/studybuddy/planet-ringed-lg.png') }}" alt="">
        <img class="decor-planet decor-planet-purple" src="{{ asset('assets/studybuddy/planet-purple-lg.png') }}" alt="">
        <img class="decor-sparkles" src="{{ asset('assets/studybuddy/sparkles-pack.png') }}" alt="">
    </div>

    <div class="board-title">
        <h1><span>StudyBuddy</span> – The Complete Cosmic Learning Universe</h1>
        <p>Learn. Play. Grow. Your Way.</p>
        <div class="badge-cloud compact">
            <span>Interactive Animations</span>
            <span>Magical Experience</span>
            <span>Multi-Role System</span>
            <span>Web + Mobile Apps</span>
            <span>Fully Customizable</span>
        </div>
    </div>

    <div class="collage-grid">
        <article class="collage-panel panel-landing">
            <div class="panel-label"><b>01</b> Landing Page</div>
            <div class="panel-landing-inner">
                <div class="mini-landing-copy">
                    <div class="mini-nav">
                        <img class="sc-logo" src="{{ asset('assets/studybuddy/logo-icon.png') }}" alt="StudyBuddy">
                        <span>StudyBuddy</span>
                    </div>
                    <h2>Learn. Play.<br>Grow.<br><span>Your Way.</span></h2>
                    <p>A fun and safe learning universe for curious minds.</p>
                    <div class="mini-actions">
                        <button type="button">Start Learning</button>
                        <button type="button" class="ghost">Explore Apps</button>
                    </div>
                    <div class="mini-proof">
                        <span><b>50+</b> Mini Apps</span>
                        <span><b>4.9</b> Parent Rating</span>
                    </div>
                </div>
                <div class="sc-hero-wrap">
                    <img class="sc-hero-img" src="{{ asset('assets/studybuddy/hero-dolphin-book.png') }}" alt="StudyBuddy mascot">
                </div>
            </div>
            <div class="mini-icon-row">
                @foreach($miniApps as $app)
                    <div class="mini-app-chip">
                        <img src="{{ asset('assets/studybuddy/' . $app['img']) }}" alt="{{ $app['title'] }}">
                        <span>{{ $app['title'] }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="collage-panel panel-apps">
            <div class="panel-label"><b>02</b> App Store</div>
            <div class="micro-top">
                <span><img class="sc-logo-tiny" src="{{ asset('assets/studybuddy/logo-icon.png') }}" alt=""> StudyBuddy</span>
                <em>Search apps...</em>
            </div>
            <div class="filter-chips">
                <span class="active">All</span><span>Popular</span><span>Primary</span><span>Secondary</span><span>New</span>
            </div>
            <div class="micro-app-grid">
                @foreach($miniApps as $app)
                    <div class="micro-app-card">
                        <img src="{{ asset('assets/studybuddy/' . $app['img']) }}" alt="{{ $app['title'] }}">
                        <strong>{{ $app['title'] }}</strong>
                        <span>★ {{ $app['rating'] }}</span>
                        <button type="button">Start</button>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="collage-panel panel-portal">
            <div class="panel-label"><b>03</b> App Portal Page</div>
            <div class="portal-copy">
                <img class="sc-portal-icon" src="{{ asset('assets/studybuddy/app-math-quest.png') }}" alt="Math Quest">
                <h2>Math Quest</h2>
                <p>Practice math in a fun and interactive way!</p>
                <small>Ages 5–14</small>
                <div class="portal-actions">
                    <button type="button">Continue in Browser</button>
                    <button type="button" class="ghost">Download App</button>
                </div>
            </div>
            <div class="sc-portal-art">
                <img src="{{ asset('assets/studybuddy/app-math-quest.png') }}" alt="Math Quest preview">
            </div>
            <aside class="download-pop">
                <strong>Get the best experience!</strong>
                <span>Google Play</span>
                <span>App Store</span>
                <div class="pop-rewards">
                    <em>Fan Challenges</em>
                    <em>Earn Rewards</em>
                </div>
            </aside>
        </article>

        <article class="collage-panel panel-primary">
            <div class="panel-label"><b>04</b> Primary Student Dashboard</div>
            <div class="dash-split">
                <div>
                    <h3>Hi Zara! 👋</h3>
                    <p class="dash-sub">Today’s Mission</p>
                    <div class="mission-list">
                        <span>Math Quest <b>1/2</b></span>
                        <span>Reading Garden <b>0/1</b></span>
                        <span>Quiz Galaxy <b>0/1</b></span>
                    </div>
                    <div class="dash-meta">
                        <span>Buddy Coins <b>120</b></span>
                        <span>Level <b>5</b></span>
                    </div>
                    <div class="badge-row-mini">
                        <span>⭐</span><span>🏆</span><span>🎯</span><span>📚</span>
                    </div>
                </div>
                <div class="sc-buddy-wrap">
                    <img src="{{ asset('assets/studybuddy/hero-dolphin-book.png') }}" alt="Buddy mascot">
                </div>
            </div>
        </article>

        <article class="collage-panel panel-secondary">
            <div class="panel-label"><b>05</b> Secondary Student Dashboard</div>
            <h3>Welcome back, Mehak! 🚀</h3>
            <div class="mini-stats-row">
                <span>Level<br><b>12</b></span>
                <span>XP<br><b>2,350</b></span>
                <span>Coins<br><b>320</b></span>
                <span>Streak<br><b>7</b></span>
            </div>
            <div class="secondary-body">
                <div class="timer-mini"><span>25:00</span><small>Focus Timer</small></div>
                <div class="plan-mini">
                    <strong>Today’s Plan</strong>
                    <span>Math Quest</span>
                    <span>Reading Garden</span>
                    <span>Quiz Galaxy</span>
                </div>
                <div class="chart-wrap">
                    <strong>Weekly Progress</strong>
                    <div class="chart-mini"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
                </div>
            </div>
        </article>

        <article class="collage-panel panel-parent">
            <div class="panel-label"><b>06</b> Parent Dashboard</div>
            <h3>Welcome, Mom! 💗</h3>
            <p class="dash-sub">Mehak’s Progress</p>
            <div class="mini-stats-row">
                <span>Study<br><b>6h 45m</b></span>
                <span>Lessons<br><b>28</b></span>
                <span>Score<br><b>85%</b></span>
                <span>Focus<br><b>3h 20m</b></span>
            </div>
            <div class="strength-block">
                <strong>Subject Strength</strong>
                <div class="strength-lines labeled">
                    <label>Math <span style="--w:90%"></span></label>
                    <label>Reading <span style="--w:80%"></span></label>
                    <label>Science <span style="--w:70%"></span></label>
                    <label>Spelling <span style="--w:65%"></span></label>
                </div>
            </div>
        </article>

        <article class="collage-panel panel-teacher">
            <div class="panel-label"><b>07</b> Teacher Dashboard</div>
            <h3>Good morning, Teacher! 👩‍🏫</h3>
            <div class="teacher-stats">
                <span>Students <b>75</b></span>
                <span>Assignments <b>12</b></span>
                <span>Quizzes <b>8</b></span>
            </div>
            <table>
                <tr><th>Class</th><th>Students</th><th>Score</th></tr>
                <tr><td>Class 7A</td><td>24</td><td>85%</td></tr>
                <tr><td>Class 8B</td><td>23</td><td>80%</td></tr>
                <tr><td>Class 9A</td><td>28</td><td>82%</td></tr>
            </table>
        </article>

        <article class="collage-panel panel-buddy">
            <div class="panel-label"><b>08</b> Buddy Customization</div>
            <div class="buddy-shop">
                <div class="sc-buddy-shop">
                    <img src="{{ asset('assets/studybuddy/hero-dolphin-book.png') }}" alt="Customize your buddy">
                </div>
                <div class="accessory-grid">
                    <span>★<small>50</small></span>
                    <span>👑<small>120</small></span>
                    <span>🚀<small>80</small></span>
                    <span>🎩<small>60</small></span>
                    <span>👕<small>40</small></span>
                    <span>🪐<small>150</small></span>
                </div>
            </div>
        </article>

        <article class="collage-panel panel-mobile">
            <div class="panel-label"><b>09</b> Mobile App Preview</div>
            <div class="phone-stack">
                <div class="phone-frame">
                    <div class="phone-screen">
                        <img class="phone-logo" src="{{ asset('assets/studybuddy/logo-icon.png') }}" alt="">
                        <h4>Hi Mehak!</h4>
                        <p>Level 12 · 320 coins</p>
                        <img class="phone-mascot" src="{{ asset('assets/studybuddy/hero-dolphin-book.png') }}" alt="">
                    </div>
                </div>
                <div class="phone-frame">
                    <div class="phone-screen">
                        <img class="phone-app-icon" src="{{ asset('assets/studybuddy/app-math-quest.png') }}" alt="">
                        <h4>Math Quest</h4>
                        <button type="button">Continue</button>
                    </div>
                </div>
                <div class="phone-frame">
                    <div class="phone-screen">
                        <h4>Rewards</h4>
                        <p>Badges earned</p>
                        <div class="phone-badges">✦ ✦ ✦</div>
                    </div>
                </div>
            </div>
        </article>

        <article class="collage-panel panel-admin">
            <div class="panel-label"><b>10</b> Admin Dashboard</div>
            <div class="mini-stats-row admin-stats">
                <span>Users<br><b>12,450</b></span>
                <span>Active<br><b>9,230</b></span>
                <span>Revenue<br><b>$48k</b></span>
                <span>Apps<br><b>50+</b></span>
            </div>
            <table class="admin-table">
                <tr><th>App</th><th>Status</th><th>Users</th></tr>
                <tr>
                    <td><img src="{{ asset('assets/studybuddy/app-math-quest.png') }}" alt=""> Math Quest</td>
                    <td><span class="status-live">Live</span></td>
                    <td>4,210</td>
                </tr>
                <tr>
                    <td><img src="{{ asset('assets/studybuddy/app-quiz-galaxy.png') }}" alt=""> Quiz Galaxy</td>
                    <td><span class="status-live">Live</span></td>
                    <td>4,790</td>
                </tr>
                <tr>
                    <td><img src="{{ asset('assets/studybuddy/app-reading-garden.png') }}" alt=""> Reading Garden</td>
                    <td><span class="status-soon">Soon</span></td>
                    <td>—</td>
                </tr>
            </table>
        </article>
    </div>
</section>
@endsection
