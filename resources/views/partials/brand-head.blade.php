{{--
    StudyBuddy identity for the document head.

    Shared by the public site, the auth screens, the admin shell and the error
    pages so the brand can never drift between them. Values come from
    config/studybuddy.php, overridable by site settings from the admin.
--}}
@php
    $sbSettings = $settings ?? [];

    $sbBrandName = $sbSettings['brand_name'] ?? $sbSettings['site_name'] ?? config('studybuddy.brand.name');
    $sbSlogan = $sbSettings['brand_slogan'] ?? config('studybuddy.brand.slogan');
    $sbDescription = $sbSettings['seo_description'] ?? config('studybuddy.brand.description');
    $sbThemeColor = config('studybuddy.brand.theme_color');

    // Resolve a configured path, preferring an admin override, and only if the
    // file is actually present so a broken setting cannot blank the identity.
    $sbIcon = function (string $key, ?string $override = null): ?string {
        if ($override && preg_match('#^https?://#i', $override)) {
            return $override;
        }

        foreach ([$override, config('studybuddy.icons.'.$key)] as $candidate) {
            if ($candidate && file_exists(public_path(ltrim($candidate, '/')))) {
                return asset(ltrim($candidate, '/')).'?v='.filemtime(public_path(ltrim($candidate, '/')));
            }
        }

        return null;
    };

    $sbFaviconIco = $sbIcon('favicon_ico');
    $sbFavicon32 = $sbIcon('favicon_32', $sbSettings['favicon_path'] ?? null);
    $sbFavicon16 = $sbIcon('favicon_16');
    $sbAppleTouch = $sbIcon('apple_touch');
    $sbSocial = $sbIcon('social', $sbSettings['logo_path'] ?? null);

    // Section content arrives already escaped, so decode once before this
    // gets escaped again on output — otherwise "Rewards & Points" reached the
    // browser as "Rewards &amp;amp; Points".
    $sbTitle = trim(html_entity_decode(
        $__env->yieldContent('title'),
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    ));

    // The layout owns the brand suffix. A CMS-supplied title that already
    // carries it (e.g. "Support | StudyBuddy") must not print it twice.
    $sbTitle = trim((string) preg_replace(
        '/\s*[|·\x{2014}\x{2013}-]\s*'.preg_quote($sbBrandName, '/').'\s*$/iu',
        '',
        $sbTitle
    ));

    $sbFullTitle = $sbTitle !== '' ? $sbTitle.' · '.$sbBrandName : $sbBrandName.' — '.$sbSlogan;
@endphp

<title>{{ $sbFullTitle }}</title>
<meta name="description" content="{{ $sbDescription }}">
<meta name="application-name" content="{{ $sbBrandName }}">
<meta name="theme-color" content="{{ $sbThemeColor }}">
<link rel="canonical" href="{{ url()->current() }}">

@if($sbFaviconIco)<link rel="icon" href="{{ $sbFaviconIco }}" sizes="any">@endif
@if($sbFavicon32)<link rel="icon" type="image/png" sizes="32x32" href="{{ $sbFavicon32 }}">@endif
@if($sbFavicon16)<link rel="icon" type="image/png" sizes="16x16" href="{{ $sbFavicon16 }}">@endif
@if($sbAppleTouch)<link rel="apple-touch-icon" sizes="180x180" href="{{ $sbAppleTouch }}">@endif
<meta name="apple-mobile-web-app-title" content="{{ $sbBrandName }}">

@if(file_exists(public_path('manifest.webmanifest')))
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
@endif

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $sbBrandName }}">
<meta property="og:title" content="{{ $sbFullTitle }}">
<meta property="og:description" content="{{ $sbDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
@if($sbSocial)<meta property="og:image" content="{{ $sbSocial }}">@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $sbFullTitle }}">
<meta name="twitter:description" content="{{ $sbDescription }}">
@if($sbSocial)<meta name="twitter:image" content="{{ $sbSocial }}">@endif
