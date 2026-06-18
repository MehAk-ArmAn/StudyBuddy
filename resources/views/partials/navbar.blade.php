<nav class="nav" aria-label="Main navigation">
    <a class="brand" href="{{ route('home') }}">@if (!empty($settings['logo_path']))<img src="{{ asset($settings['logo_path']) }}" alt="{{ $settings['brand_name'] ?? 'StudyBuddy' }}">@endif<span>{{ $settings['brand_name'] ?? 'StudyBuddy' }}</span></a>
    <button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false">Menu</button>
    <div class="nav-links" data-nav-links>
        @foreach ($navigationItems as $item)
            <a href="{{ $item->url }}" @if($item->opens_new_tab) target="_blank" rel="noopener" @endif>{{ $item->label }}</a>
        @endforeach
        @guest
            <a href="{{ route('login') }}">{{ $settings['login_label'] ?? 'Login' }}</a>
            <a class="nav-cta" href="{{ route('register') }}">{{ $settings['register_label'] ?? 'Create Account' }}</a>
        @endguest
        @auth
            <a class="nav-cta" href="{{ route('dashboard') }}">{{ $settings['dashboard_label'] ?? 'Dashboard' }}</a>
            <form class="logout-inline" method="POST" action="{{ route('logout') }}">@csrf<button class="nav-logout" type="submit">{{ $settings['logout_label'] ?? 'Logout' }}</button></form>
        @endauth
    </div>
</nav>
