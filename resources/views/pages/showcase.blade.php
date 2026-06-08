@extends('layouts.app')

@section('title', 'Showcase')
@section('body_class', 'page-shell page-product-board')

@section('content')
@php
    $asset = fn (string $file): string => asset('assets/studybuddy/' . $file);
    $apps = [
        ['Math Quest', 'app-math-quest.png'], ['Spelling Sprint', 'app-spelling-sprint.png'], ['Reading Garden', 'app-reading-garden.png'], ['Focus Forest', 'app-focus-forest.png'],
        ['Planner City', 'app-planner-city.png'], ['Quiz Galaxy', 'app-quiz-galaxy.png'], ['Shapes Lab', 'app-shapes-lab.png'], ['Flashcard Castle', 'app-flashcard-castle.png'],
    ];
@endphp

<section class="product-board reveal-on-load" aria-labelledby="product-board-title">
    <div class="product-board-bg" aria-hidden="true"><img class="pb-planet-left" src="{{ $asset('planet-ringed-lg.png') }}" alt=""><img class="pb-planet-right" src="{{ $asset('planet-purple-lg.png') }}" alt=""><img class="pb-sparkles" src="{{ $asset('sparkles-pack.png') }}" alt=""><span></span></div>

    <header class="product-board-header">
        <h1 id="product-board-title"><span>StudyBuddy</span> – The Complete Cosmic Learning Universe</h1>
        <p>Learn. Play. Grow. Your Way.</p>
        <div class="product-badges"><span>◉ Interactive Animations</span><span>◇ Magical Experience</span><span>◎ Multi-Role System</span><span>◈ Web + Mobile Apps</span><span>✦ Fully Customizable (Admin)</span></div>
    </header>

    <main class="product-collage">
        <article class="pb-panel pb-landing"><b>01</b><small>Landing Page</small><div class="pb-mini-nav"><img src="{{ $asset('logo-icon.png') }}" alt=""><span>StudyBuddy</span><i>Home Apps Parents Teachers Pricing Support</i></div><div class="pb-landing-body"><div><h2>Learn. Play.<br>Grow.<br><span>Your Way.</span></h2><p>A fun and safe learning universe where students can practice, play, focus, and grow.</p><button>Start Learning</button><button class="ghost">Explore Apps</button></div><img src="{{ $asset('hero-dolphin-book.png') }}" alt="StudyBuddy mascot"></div><div class="pb-app-ribbon">@foreach($apps as $app)<span><img src="{{ $asset($app[1]) }}" alt="">{{ $app[0] }}</span>@endforeach</div></article>

        <article class="pb-panel pb-store"><b>02</b><small>Apps Store (Playstore Style)</small><div class="pb-store-top"><span><img src="{{ $asset('logo-icon.png') }}" alt="">StudyBuddy</span><em>Search apps...</em></div><div class="pb-tabs"><i>All</i><i>Popular</i><i>Primary</i><i>Secondary</i><i>New</i></div><div class="pb-store-grid">@foreach($apps as $app)<span><img src="{{ $asset($app[1]) }}" alt=""><strong>{{ $app[0] }}</strong><em>★ 4.8</em><button>Start</button></span>@endforeach</div></article>

        <article class="pb-panel pb-portal"><b>03</b><small>App Portal Page</small><div class="pb-portal-copy"><h2>Math Quest</h2><p>Practice math in a fun and interactive way!</p><span>Ages 6–14</span><span>Primary & Secondary</span><button>Continue in Browser</button></div><img src="{{ $asset('app-math-quest.png') }}" alt="Math Quest"><aside>Get the best experience!<button>Google Play</button><button>App Store</button></aside></article>

        <article class="pb-panel pb-primary"><b>04</b><small>Primary Student Dashboard (Age 6–10)</small><h3>Hi Zara! 👋</h3><div class="pb-mission">Today’s Mission<span>Complete 2 Math Quest lessons 1/2</span><span>Read a story 0/1</span><span>Play 1 quiz 0/1</span></div><img src="{{ $asset('hero-dolphin-book.png') }}" alt=""><div class="pb-mini-apps">@foreach(array_slice($apps,0,4) as $app)<span><img src="{{ $asset($app[1]) }}" alt="">{{ $app[0] }}</span>@endforeach</div></article>

        <article class="pb-panel pb-secondary"><b>05</b><small>Secondary Student Dashboard (Age 11–16)</small><h3>Welcome back, Mehak! 🚀</h3><div class="pb-statline"><span>Level<br>12</span><span>XP<br>2,350</span><span>Coins<br>320</span><span>Streak<br>7</span></div><div class="pb-plan"><strong>Today’s Plan</strong><span>Math: Quadratic Equations</span><span>Science: Photosynthesis</span><span>Focus Time – 30 min</span></div><div class="pb-chart"><i></i><i></i><i></i><i></i><i></i><i></i></div></article>

        <article class="pb-panel pb-parent"><b>06</b><small>Parent Dashboard</small><h3>Welcome, Mom! ❤</h3><div class="pb-statline"><span>Study<br>6h 45m</span><span>Lessons<br>28</span><span>Score<br>85%</span><span>Focus<br>3h 20m</span></div><div class="pb-bars"><label>Math<i style="width:90%"></i></label><label>Reading<i style="width:80%"></i></label><label>Science<i style="width:70%"></i></label><label>Spelling<i style="width:65%"></i></label></div><aside>Parent Learning Corner<span>Read Article</span><span>Read Article</span><span>Read Article</span></aside></article>

        <article class="pb-panel pb-teacher"><b>07</b><small>Teacher Dashboard</small><h3>Good morning, Teacher! 👩🏽‍🏫</h3><div class="pb-statline"><span>Classes<br>5</span><span>Students<br>120</span><span>Assignments<br>12</span><span>Quizzes<br>8</span></div><table><tr><th>Class</th><th>Students</th><th>Score</th></tr><tr><td>7A</td><td>24</td><td>85%</td></tr><tr><td>8A</td><td>23</td><td>80%</td></tr><tr><td>9A</td><td>28</td><td>82%</td></tr></table><button>Create Assignment</button></article>

        <article class="pb-panel pb-buddy"><b>08</b><small>Buddy Customization (Like Blooket)</small><div><img src="{{ $asset('hero-dolphin-book.png') }}" alt=""><section>@foreach(['🧢','🧙‍♂️','🧑‍🚀','🕶️','🎀','🧥','💫','🚀','📘'] as $icon)<span>{{ $icon }}<small>100</small></span>@endforeach</section></div><button>Save Changes</button></article>

        <article class="pb-panel pb-mobile"><b>09</b><small>Mobile App Preview</small><div class="pb-phone-row">@foreach([0,1,2] as $phone)<div class="pb-phone"><span></span><h4>{{ $phone === 0 ? 'Hi Mehak!' : ($phone === 1 ? 'Math Quest' : 'Rewards') }}</h4><img src="{{ $asset($phone === 1 ? 'app-math-quest.png' : 'hero-dolphin-book.png') }}" alt=""><p>{{ $phone === 0 ? 'Level 12 · 320 coins' : ($phone === 1 ? 'Choose Mode' : 'Badges earned') }}</p></div>@endforeach</div></article>

        <article class="pb-panel pb-admin"><b>10</b><small>Admin Dashboard (Control Everything)</small><div class="pb-statline"><span>Users<br>12,450</span><span>Active<br>9,230</span><span>Teachers<br>320</span><span>Parents<br>2,900</span></div><table><tr><th>App</th><th>Status</th><th>Users</th></tr>@foreach(array_slice($apps,0,5) as $app)<tr><td><img src="{{ $asset($app[1]) }}" alt="">{{ $app[0] }}</td><td>Live</td><td>4,210</td></tr>@endforeach</table></article>
    </main>

    <footer class="product-board-footer"><div><img src="{{ $asset('logo-icon.png') }}" alt=""><strong>StudyBuddy</strong><p>A safe and fun learning universe for every student.</p></div><nav><strong>Explore</strong><a>Home</a><a>Apps</a><a>How It Works</a><a>Pricing</a></nav><nav><strong>For Parents</strong><a>Parent Dashboard</a><a>Parent Learning</a><a>Help Center</a></nav><nav><strong>For Teachers</strong><a>Teacher Dashboard</a><a>Resources</a><a>Lesson Plans</a></nav><section><strong>Stay Updated ✨</strong><label><input placeholder="Enter your email"><button>Subscribe</button></label></section><aside><strong>Get StudyBuddy Apps</strong><span>Google Play</span><span>App Store</span><i></i></aside></footer>
</section>
@endsection
