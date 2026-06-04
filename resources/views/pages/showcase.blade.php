@extends('layouts.app')

@section('title', 'Showcase')

@section('content')
<section class="showcase-board reveal-on-load">
    <div class="board-title">
        <h1><span>StudyBuddy</span> – The Complete Cosmic Learning Universe</h1>
        <p>Learn. Play. Grow. Your Way.</p>
        <div class="badge-cloud compact">
            <span>✺ Interactive Animations</span><span>✦ Magical Experience</span><span>● Multi-Role System</span><span>◈ Web + Mobile Apps</span><span>☻ Fully Customizable (Admin)</span>
        </div>
    </div>

    <div class="collage-grid">
        <article class="collage-panel panel-landing tilt-card">
            <div class="panel-label"><b>01</b> Landing Page</div>
            <div class="mini-landing-copy"><h2>Learn. Play.<br>Grow.<br><span>Your Way.</span></h2><p>A fun and safe learning universe.</p><button>Start Learning</button></div>
            @include('partials.image-placeholder', ['label' => 'HERO_MASCOT_IMAGE', 'src' => 'assets/studybuddy/hero-dolphin-book.png', 'variant' => 'mini-hero', 'caption' => 'Mascot'])
            <div class="mini-icon-row">
                @foreach(\App\Support\DemoContent::miniApps()->take(8) as $app)
                    @include('partials.image-placeholder', ['label' => $app->image_label, 'src' => $app->image_path, 'variant' => 'tiny-icon', 'caption' => $app->title])
                @endforeach
            </div>
        </article>

        <article class="collage-panel panel-apps tilt-card">
            <div class="panel-label"><b>02</b> App Store</div>
            <div class="micro-top"><span>StudyBuddy</span><em>Search apps...</em></div>
            <div class="micro-app-grid">
                @foreach(\App\Support\DemoContent::miniApps() as $app)
                    <div>
                        @include('partials.image-placeholder', ['label' => $app->image_label, 'src' => $app->image_path, 'variant' => 'micro-app', 'caption' => $app->title])
                        <strong>{{ $app->title }}</strong><span>⭐ {{ $app->hero_metric }}</span>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="collage-panel panel-portal tilt-card">
            <div class="panel-label"><b>03</b> App Portal Page</div>
            <div><h2>Math Quest</h2><p>Practice math in a fun and interactive way!</p><button>Continue in Browser</button></div>
            @include('partials.image-placeholder', ['label' => 'APP_PORTAL_IMAGE_MATH_QUEST', 'src' => 'assets/studybuddy/app-math-quest.png', 'variant' => 'portal-art', 'caption' => 'Math Quest art'])
            <aside class="download-pop">Get the best experience!<br><span>Google Play</span><span>App Store</span></aside>
        </article>

        <article class="collage-panel panel-primary tilt-card">
            <div class="panel-label"><b>04</b> Primary Student Dashboard</div>
            <h3>Hi Zara! 👋</h3><p>Today’s Mission</p>
            <div class="mission-list"><span>Math Quest 1/2</span><span>Reading Garden 0/1</span><span>Quiz Galaxy 0/1</span></div>
            @include('partials.image-placeholder', ['label' => 'DASHBOARD_BUDDY_IMAGE', 'src' => 'assets/studybuddy/hero-dolphin-book.png', 'variant' => 'dash-buddy', 'caption' => 'Buddy'])
        </article>

        <article class="collage-panel panel-secondary tilt-card">
            <div class="panel-label"><b>05</b> Secondary Student Dashboard</div>
            <h3>Welcome back, Mehak! 🚀</h3>
            <div class="mini-stats-row"><span>Level<br><b>12</b></span><span>XP<br><b>2,350</b></span><span>Coins<br><b>320</b></span><span>Streak<br><b>7</b></span></div>
            <div class="timer-mini">25:00</div><div class="chart-mini"><i></i><i></i><i></i><i></i><i></i></div>
        </article>

        <article class="collage-panel panel-parent tilt-card">
            <div class="panel-label"><b>06</b> Parent Dashboard</div>
            <h3>Welcome, Mom! 💗</h3>
            <div class="mini-stats-row"><span>Study<br><b>6h45</b></span><span>Lessons<br><b>28</b></span><span>Score<br><b>85%</b></span><span>Focus<br><b>3h20</b></span></div>
            <div class="strength-lines"><span style="--w:90%"></span><span style="--w:80%"></span><span style="--w:70%"></span><span style="--w:65%"></span></div>
        </article>

        <article class="collage-panel panel-teacher tilt-card">
            <div class="panel-label"><b>07</b> Teacher Dashboard</div>
            <h3>Good morning, Teacher! 👩‍🏫</h3>
            <table><tr><th>Class</th><th>Students</th><th>Score</th></tr><tr><td>7A</td><td>24</td><td>85%</td></tr><tr><td>8A</td><td>23</td><td>80%</td></tr><tr><td>9A</td><td>28</td><td>82%</td></tr></table>
        </article>

        <article class="collage-panel panel-buddy tilt-card">
            <div class="panel-label"><b>08</b> Buddy Customization</div>
            @include('partials.image-placeholder', ['label' => 'BUDDY_CUSTOMIZATION_IMAGE', 'src' => 'assets/studybuddy/hero-dolphin-book.png', 'variant' => 'shop-buddy', 'caption' => 'Buddy shop'])
            <div class="accessory-grid"><span>★</span><span>👑</span><span>🚀</span><span>🎩</span><span>👕</span><span>🪐</span></div>
        </article>

        <article class="collage-panel panel-mobile tilt-card">
            <div class="panel-label"><b>09</b> Mobile App Preview</div>
            <div class="phone-stack"><div><h4>Hi Mehak!</h4><p>Level 12</p></div><div><h4>Math Quest</h4><button>Continue</button></div><div><h4>Rewards</h4><p>Badges ✦ ✦ ✦</p></div></div>
        </article>

        <article class="collage-panel panel-admin tilt-card">
            <div class="panel-label"><b>10</b> Admin Dashboard</div>
            <div class="mini-stats-row"><span>Users<br><b>12,450</b></span><span>Students<br><b>9,230</b></span><span>Teachers<br><b>320</b></span><span>Parents<br><b>2,900</b></span></div>
            <table><tr><th>App</th><th>Status</th><th>Users</th></tr><tr><td>Math Quest</td><td>Live</td><td>4,210</td></tr><tr><td>Quiz Galaxy</td><td>Live</td><td>4,790</td></tr></table>
        </article>
    </div>
</section>
@endsection
