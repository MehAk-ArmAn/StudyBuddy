@php
    $settings = $settings ?? [];
    $navigationItems = $navigationItems ?? collect();

    $siteName = $settings['site_name'] ?? 'StudyBuddy';
    $logoText = $settings['logo_text'] ?? $siteName;
    $tagline = $settings['site_tagline'] ?? 'Learn • Play • Grow';

    $navItems = collect($navigationItems ?? [])->filter(fn($item) => (bool)($item->is_enabled ?? true))->values();

    if ($navItems->isEmpty()) {
        $navItems = collect([
            (object)['label' => 'Apps', 'title' => 'Apps', 'url' => url('/apps')],
            (object)['label' => 'Learning', 'title' => 'Learning', 'url' => url('/learning-hub')],
            (object)['label' => 'Parents', 'title' => 'Parents', 'url' => url('/parents-center')],
            (object)['label' => 'Teachers', 'title' => 'Teachers', 'url' => url('/teacher-studio')],
            (object)['label' => 'Safety', 'title' => 'Safety', 'url' => url('/safety-support')],
        ]);
    }

    $primaryItems = $navItems->take(7);
    $moreItems = $navItems->slice(7);

    $itemUrl = function ($item) {
        $url = $item->url ?? $item->href ?? null;
        if ($url) {
            return \Illuminate\Support\Str::startsWith($url, ['http://', 'https://', '/']) ? $url : url($url);
        }

        $route = $item->route_name ?? $item->route ?? null;
        return ($route && \Illuminate\Support\Facades\Route::has($route)) ? route($route) : url('/');
    };

    $itemLabel = fn ($item) => $item->label ?? $item->title ?? $item->name ?? 'Link';
@endphp

<header class="sb-universe-nav-wrap">
    <div class="sb-universe-nav" data-sb-universe-nav>
        <a class="sb-universe-brand" href="{{ url('/') }}" aria-label="{{ $siteName }} home">
            <span class="sb-universe-logo" aria-hidden="true">
                @if(!empty($settings['logo_image']))
                    <img src="{{ $settings['logo_image'] }}" alt="">
                @else
                    <span>🐬</span>
                @endif
            </span>
            <span class="sb-universe-brand-text">
                <strong>{{ $logoText }}</strong>
                <em>{{ $tagline }}</em>
            </span>
        </a>

        <button class="sb-universe-toggle" type="button" aria-expanded="false" aria-controls="sb-universe-links">
            <span></span><span></span><span></span>
            <b>Menu</b>
        </button>

        <nav id="sb-universe-links" class="sb-universe-links" aria-label="Main navigation">
            @foreach($primaryItems as $item)
                <a href="{{ $itemUrl($item) }}"><span>{{ $itemLabel($item) }}</span></a>
            @endforeach

            @if($moreItems->isNotEmpty())
                <details class="sb-universe-more">
                    <summary><span>More</span> <i>⌄</i></summary>
                    <div>
                        @foreach($moreItems as $item)
                            <a href="{{ $itemUrl($item) }}">✦ {{ $itemLabel($item) }}</a>
                        @endforeach
                    </div>
                </details>
            @endif
        </nav>

        <div class="sb-universe-actions">
            @auth
                <a class="sb-universe-soft" href="{{ route('dashboard') }}">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            @else
                <a class="sb-universe-soft" href="{{ route('login') }}">Login</a>
                <a class="sb-universe-join" href="{{ route('register') }}">Join</a>
            @endauth
        </div>
    </div>
</header>
