@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Str;

    $settings = $settings ?? (Schema::hasTable('site_settings') ? DB::table('site_settings')->pluck('value', 'key')->toArray() : []);
    $siteName = $settings['site_name'] ?? 'StudyBuddy';
    $logoText = $settings['logo_text'] ?? $siteName;
    $tagline = $settings['site_tagline'] ?? 'Learn • Play • Grow';

    $logoSetting = $settings['logo_image'] ?? $settings['logo_path'] ?? $settings['site_logo'] ?? null;
    $logoSrc = $logoSetting
        ? (Str::startsWith($logoSetting, ['http://', 'https://', '/']) ? $logoSetting : asset($logoSetting))
        : asset('assets/studybuddy-brand/studybuddy-logo-mark.svg');

    $decode = function ($json, $fallback = []) {
        $decoded = json_decode((string) $json, true);
        return is_array($decoded) ? $decoded : $fallback;
    };

    $safePublicNav = [
        ['label' => 'Home', 'url' => '/', 'roles' => ['all']],
        ['label' => 'Apps', 'url' => '/apps', 'roles' => ['all']],
        ['label' => 'Learning', 'url' => '/apps?section=learning', 'roles' => ['all']],
        ['label' => 'Parents', 'url' => '/apps?role=parent', 'roles' => ['all']],
        ['label' => 'Teachers', 'url' => '/apps?role=teacher', 'roles' => ['all']],
        ['label' => 'Safety', 'url' => '/apps?section=safety', 'roles' => ['all']],
    ];

    $navItems = $decode($settings['shell_navigation_json'] ?? '', []);

    if (!$navItems && isset($navigationItems)) {
        $navItems = collect($navigationItems)->filter(fn($item) => (bool)($item->is_enabled ?? true))->map(fn($item) => [
            'label' => $item->label ?? $item->title ?? $item->name ?? 'Link',
            'url' => $item->url ?? $item->href ?? '/',
            'roles' => ['all'],
        ])->values()->all();
    }

    $navItems = $navItems ?: $safePublicNav;
    $role = auth()->check() ? (auth()->user()->role ?? 'student') : 'guest';

    $visibleNav = collect($navItems)->filter(function ($item) use ($role) {
        $roles = $item['roles'] ?? ['all'];
        if (in_array('all', (array) $roles, true)) {
            return true;
        }
        return in_array($role, (array) $roles, true);
    })->values();

    $linkUrl = function ($url) {
        $url = trim((string) ($url ?: '/'));
        if ($url === '') return url('/');
        return Str::startsWith($url, ['http://', 'https://']) ? $url : url(Str::startsWith($url, '/') ? $url : '/' . $url);
    };

    $primaryItems = $visibleNav->take(5);
    $moreItems = $visibleNav->slice(5);
    $roleLabel = str_replace('_', ' ', $role);
@endphp

<header class="sb-consistent-nav-wrap">
    <div class="sb-consistent-nav">
        <a class="sb-consistent-brand" href="{{ url('/') }}" aria-label="{{ $siteName }} home">
            <img src="{{ $logoSrc }}" alt="{{ $siteName }} logo">
            <span><strong>{{ $logoText }}</strong><em>{{ $tagline }}</em></span>
        </a>

        <button class="sb-consistent-menu" type="button" aria-expanded="false" aria-controls="sb-consistent-links">
            <span></span><span></span><span></span><b>Menu</b>
        </button>

        <nav id="sb-consistent-links" class="sb-consistent-links" aria-label="Main navigation">
            @foreach($primaryItems as $item)
                <a href="{{ $linkUrl($item['url'] ?? '/') }}">{{ $item['label'] ?? 'Link' }}</a>
            @endforeach

            @if($moreItems->isNotEmpty())
                <details class="sb-consistent-more">
                    <summary>More</summary>
                    <div>
                        @foreach($moreItems as $item)
                            <a href="{{ $linkUrl($item['url'] ?? '/') }}">{{ $item['label'] ?? 'Link' }}</a>
                        @endforeach
                    </div>
                </details>
            @endif
        </nav>

        <div class="sb-consistent-actions">
            @auth
                <span class="sb-role-chip">{{ $roleLabel }}</span>
                <a class="sb-consistent-ghost" href="{{ route('dashboard') }}">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Logout</button></form>
            @else
                <a class="sb-consistent-ghost" href="{{ route('login') }}">Login</a>
                <a class="sb-consistent-primary" href="{{ route('register') }}">Join</a>
            @endauth
        </div>
    </div>
</header>
