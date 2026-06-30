<nav class="sbx-nav" aria-label="StudyBuddy experience navigation">
    <a href="{{ route('studybuddy.learning-hub') }}" class="sbx-nav__link {{ request()->routeIs('studybuddy.learning-hub') ? 'is-active' : '' }}">Learning Hub</a>
    <a href="{{ route('studybuddy.learning-paths') }}" class="sbx-nav__link {{ request()->routeIs('studybuddy.learning-paths') ? 'is-active' : '' }}">Paths</a>
    <a href="{{ route('studybuddy.rewards') }}" class="sbx-nav__link {{ request()->routeIs('studybuddy.rewards') ? 'is-active' : '' }}">Rewards</a>
    <a href="{{ route('studybuddy.app-ecosystem') }}" class="sbx-nav__link {{ request()->routeIs('studybuddy.app-ecosystem') ? 'is-active' : '' }}">App Ecosystem</a>
    <a href="{{ route('studybuddy.parents-center') }}" class="sbx-nav__link {{ request()->routeIs('studybuddy.parents-center') ? 'is-active' : '' }}">Parents</a>
    <a href="{{ route('studybuddy.teacher-studio') }}" class="sbx-nav__link {{ request()->routeIs('studybuddy.teacher-studio') ? 'is-active' : '' }}">Teachers</a>
    <a href="{{ route('studybuddy.safety-support') }}" class="sbx-nav__link {{ request()->routeIs('studybuddy.safety-support') ? 'is-active' : '' }}">Support</a>
</nav>
