@php($logo = asset('assets/studybuddy/logo-icon.png'))
<footer class="studybuddy-footer reveal-on-load">
    <span class="studybuddy-footer__orb studybuddy-footer__orb-a" aria-hidden="true"></span>
    <span class="studybuddy-footer__orb studybuddy-footer__orb-b" aria-hidden="true"></span>
    <div class="studybuddy-footer__brand">
        <img src="{{ $logo }}" alt="StudyBuddy logo">
        <div><strong>StudyBuddy</strong><p>Learn. Play. Grow. Your Way.</p></div>
    </div>
    <nav aria-label="Explore StudyBuddy"><strong>Explore</strong><a href="{{ route('home') }}">Home</a><a href="{{ route('apps.index') }}">Apps</a><a href="{{ route('apps.math-quest') }}">Math Quest</a><a href="{{ route('showcase') }}">Showcase</a></nav>
    <nav aria-label="Student links"><strong>Students</strong><a href="{{ route('demo.primary') }}">Primary Dashboard</a><a href="{{ route('demo.secondary') }}">Secondary Dashboard</a><a href="{{ route('rewards') }}">Rewards</a></nav>
    <nav aria-label="Parent and teacher links"><strong>Parents & Teachers</strong><a href="{{ route('demo.parent') }}">Parent Dashboard</a><a href="{{ route('demo.teacher') }}">Teacher Dashboard</a><a href="{{ route('demo.admin') }}">Admin</a></nav>
    <section class="studybuddy-footer__apps"><strong>Get StudyBuddy</strong><span>▶ Google Play</span><span> App Store</span></section>
    <p class="studybuddy-footer__copyright">© {{ date('Y') }} StudyBuddy. A safe cosmic learning universe for every learner.</p>
</footer>
