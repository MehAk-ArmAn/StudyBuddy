<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Control Room') · {{ config('studybuddy.brand.name') }} Admin</title>

    @php
        $sbAdminIcon = function (string $key): ?string {
            $path = config('studybuddy.icons.'.$key);

            if (! $path || ! file_exists(public_path($path))) {
                return null;
            }

            return asset($path).'?v='.filemtime(public_path($path));
        };
    @endphp

    @if($url = $sbAdminIcon('favicon_ico'))<link rel="icon" href="{{ $url }}" sizes="any">@endif
    @if($url = $sbAdminIcon('favicon_32'))<link rel="icon" type="image/png" sizes="32x32" href="{{ $url }}">@endif
    @if($url = $sbAdminIcon('apple_touch'))<link rel="apple-touch-icon" sizes="180x180" href="{{ $url }}">@endif
    <meta name="theme-color" content="#17203a">
    <meta name="robots" content="noindex, nofollow">

    @if(file_exists(public_path('assets/css/admin.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">
    @endif
    @if(file_exists(public_path('assets/css/studybuddy-admin-simple.css')))
        <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-admin-simple.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-admin-simple.css')) }}">
    @endif

    @stack('styles')

    <link rel="stylesheet" href="{{ asset('assets/css/studybuddy-admin-control-room.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-admin-control-room.css')) }}">


    @if(file_exists(public_path('assets/css/studybuddy-admin-targeted-pages.css')))
        <link
            rel="stylesheet"
            href="{{ asset('assets/css/studybuddy-admin-targeted-pages.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-admin-targeted-pages.css')) }}"
        >
    @endif

</head>

@php
    $routeUrl = function (array $names): ?string {
        foreach ($names as $name) {
            if (\Illuminate\Support\Facades\Route::has($name)) {
                return route($name);
            }
        }

        return null;
    };

    $routeActive = fn (array $patterns): bool => request()->routeIs(...$patterns);

    $overviewUrl = $routeUrl(['admin.control-room.index', 'admin.dashboard']);
    $appsUrl = $routeUrl(['admin.control-room.apps.index']);
    $addAppUrl = $routeUrl(['admin.control-room.apps.create']);
    $homepageUrl = $routeUrl(['admin.control-room.homepage-cms.index']);
    $pagesUrl = $routeUrl(['admin.control-room.pages-legal.index', 'admin.pages.index']);
    $contentUrl = $routeUrl(['admin.control-room.content-studio', 'studybuddy.admin.content.index']);
    $usersUrl = $routeUrl(['admin.control-room.users.index', 'admin.users.index']);
    $messagesUrl = $routeUrl(['admin.control-room.contact-messages.index']);
    $mailingUrl = $routeUrl(['admin.control-room.mailing-list.index']);
    $shellUrl = $routeUrl(['admin.control-room.shell']);
    $mediaUrl = $routeUrl(['admin.media-assets.index']);
    $settingsUrl = $routeUrl(['admin.control-room.site-settings.index', 'admin.site-settings.index']);
    $platformUrl = $routeUrl(['admin.control-room.final-platform', 'studybuddy.admin.final.index']);
    $healthUrl = $routeUrl(['admin.control-room.health']);
    $verificationUrl = $routeUrl(['admin.control-room.verifications.index']);
    $roleToolsUrl = $routeUrl(['admin.control-room.role-tools.index']);
    $accountUrl = $routeUrl(['admin.control-room.account.edit']);
    $logoutUrl = $routeUrl(['admin.logout']) ?: url('/logout');

    $currentGroup = match (true) {
        $routeActive(['admin.control-room.apps.*']) => 'Apps',
        $routeActive([
            'admin.control-room.homepage-cms.*',
            'admin.control-room.pages-legal.*',
            'admin.pages.*',
            'admin.control-room.content-*',
            'admin.control-room.content.*',
            'studybuddy.admin.content.*',
        ]) => 'Content',
        $routeActive([
            'admin.control-room.users.*',
            'admin.users.*',
            'admin.control-room.contact-messages.*',
            'admin.control-room.mailing-list.*',
        ]) => 'People',
        $routeActive([
            'admin.control-room.shell*',
            'admin.control-room.site-settings.*',
            'admin.site-settings.*',
            'admin.media-assets.*',
        ]) => 'Website',
        $routeActive([
            'admin.control-room.final-*',
            'admin.control-room.final.*',
            'studybuddy.admin.final.*',
            'admin.control-room.health*',
            'admin.control-room.verifications.*',
            'admin.control-room.role-tools.*',
            'admin.control-room.account.*',
        ]) => 'System',
        default => 'Overview',
    };

    $adminName = auth()->user()?->name ?: 'Administrator';
    $adminInitial = mb_strtoupper(mb_substr($adminName, 0, 1));
@endphp

<body data-admin-route="{{ Route::currentRouteName() }}" class="sb-simple-admin-body sb-admin-body">
    <a class="sb-admin-skip-link" href="#admin-main-content">Skip to main content</a>

    <div class="sb-simple-admin-shell sb-admin-shell" data-admin-shell>
        <aside
            id="studybuddy-admin-sidebar"
            class="sb-simple-admin-sidebar sb-admin-sidebar"
            aria-label="StudyBuddy Control Room navigation"
            data-admin-sidebar
        >
            <div class="sb-simple-admin-brand sb-admin-brand">
                <a href="{{ $overviewUrl ?: url('/admin/control-room') }}" aria-label="StudyBuddy Control Room home">
                    <span class="sb-admin-brand__mark" aria-hidden="true">
                        <img src="{{ asset('assets/studybuddy-brand/studybuddy-logo-mark.svg') }}" alt="">
                    </span>
                    <span class="sb-admin-brand__copy">
                        <strong>StudyBuddy</strong>
                        <small>Control Room</small>
                    </span>
                </a>

                <button
                    type="button"
                    class="sb-admin-icon-button sb-admin-sidebar-close"
                    aria-label="Close navigation"
                    data-admin-sidebar-close
                >
                    <svg aria-hidden="true"><use href="#sb-admin-icon-close"></use></svg>
                </button>
            </div>

            <nav class="sb-simple-admin-nav sb-admin-nav" aria-label="Admin sections">
                <div class="sb-admin-nav__group">
                    <p class="sb-admin-nav__label">Overview</p>
                    @if($overviewUrl)
                        <a
                            href="{{ $overviewUrl }}"
                            class="sb-admin-nav__item {{ $routeActive(['admin.control-room.index', 'admin.dashboard']) ? 'is-active' : '' }}"
                            @if($routeActive(['admin.control-room.index', 'admin.dashboard'])) aria-current="page" @endif
                        >
                            <svg aria-hidden="true"><use href="#sb-admin-icon-dashboard"></use></svg>
                            <span>Dashboard</span>
                        </a>
                    @endif
                </div>

                <div class="sb-admin-nav__group">
                    <p class="sb-admin-nav__label">Apps</p>
                    @if($appsUrl)
                        <a
                            href="{{ $appsUrl }}"
                            class="sb-admin-nav__item {{ $routeActive(['admin.control-room.apps.index', 'admin.control-room.apps.edit', 'admin.control-room.apps.preview*']) ? 'is-active' : '' }}"
                            @if($routeActive(['admin.control-room.apps.index', 'admin.control-room.apps.edit', 'admin.control-room.apps.preview*'])) aria-current="page" @endif
                        >
                            <svg aria-hidden="true"><use href="#sb-admin-icon-apps"></use></svg>
                            <span>All Apps</span>
                        </a>
                    @endif
                    @if($addAppUrl)
                        <a
                            href="{{ $addAppUrl }}"
                            class="sb-admin-nav__item {{ $routeActive(['admin.control-room.apps.create']) ? 'is-active' : '' }}"
                            @if($routeActive(['admin.control-room.apps.create'])) aria-current="page" @endif
                        >
                            <svg aria-hidden="true"><use href="#sb-admin-icon-plus"></use></svg>
                            <span>Add App</span>
                        </a>
                    @endif
                </div>

                <div class="sb-admin-nav__group">
                    <p class="sb-admin-nav__label">Content</p>
                    @if($homepageUrl)
                        <a href="{{ $homepageUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.control-room.homepage-cms.*']) ? 'is-active' : '' }}" @if($routeActive(['admin.control-room.homepage-cms.*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-home"></use></svg>
                            <span>Homepage</span>
                        </a>
                    @endif
                    @if($pagesUrl)
                        <a href="{{ $pagesUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.control-room.pages-legal.*', 'admin.pages.*']) ? 'is-active' : '' }}" @if($routeActive(['admin.control-room.pages-legal.*', 'admin.pages.*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-pages"></use></svg>
                            <span>Pages</span>
                        </a>
                    @endif
                    @if($contentUrl)
                        <a href="{{ $contentUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.control-room.content-*', 'admin.control-room.content.*', 'studybuddy.admin.content.*']) ? 'is-active' : '' }}" @if($routeActive(['admin.control-room.content-*', 'admin.control-room.content.*', 'studybuddy.admin.content.*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-edit"></use></svg>
                            <span>Content Studio</span>
                        </a>
                    @endif
                </div>

                <div class="sb-admin-nav__group">
                    <p class="sb-admin-nav__label">People</p>
                    @if($usersUrl)
                        <a href="{{ $usersUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.control-room.users.*', 'admin.users.*']) ? 'is-active' : '' }}" @if($routeActive(['admin.control-room.users.*', 'admin.users.*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-users"></use></svg>
                            <span>Users</span>
                        </a>
                    @endif
                    @if($messagesUrl)
                        <a href="{{ $messagesUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.control-room.contact-messages.*']) ? 'is-active' : '' }}" @if($routeActive(['admin.control-room.contact-messages.*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-message"></use></svg>
                            <span>Messages</span>
                        </a>
                    @endif
                    @if($mailingUrl)
                        <a href="{{ $mailingUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.control-room.mailing-list.*']) ? 'is-active' : '' }}" @if($routeActive(['admin.control-room.mailing-list.*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-mail"></use></svg>
                            <span>Mailing List</span>
                        </a>
                    @endif
                </div>

                <div class="sb-admin-nav__group">
                    <p class="sb-admin-nav__label">Website</p>
                    @if($shellUrl)
                        <a href="{{ $shellUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.control-room.shell*']) ? 'is-active' : '' }}" @if($routeActive(['admin.control-room.shell*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-palette"></use></svg>
                            <span>Header &amp; Footer</span>
                        </a>
                    @endif
                    @if($mediaUrl)
                        <a href="{{ $mediaUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.media-assets.*']) ? 'is-active' : '' }}" @if($routeActive(['admin.media-assets.*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-media"></use></svg>
                            <span>Media</span>
                        </a>
                    @endif
                    @if($settingsUrl)
                        <a href="{{ $settingsUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.control-room.site-settings.*', 'admin.site-settings.*']) ? 'is-active' : '' }}" @if($routeActive(['admin.control-room.site-settings.*', 'admin.site-settings.*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-settings"></use></svg>
                            <span>Site Settings</span>
                        </a>
                    @endif
                </div>

                <div class="sb-admin-nav__group">
                    <p class="sb-admin-nav__label">System</p>
                    @if($platformUrl)
                        <a href="{{ $platformUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.control-room.final-*', 'admin.control-room.final.*', 'studybuddy.admin.final.*']) ? 'is-active' : '' }}" @if($routeActive(['admin.control-room.final-*', 'admin.control-room.final.*', 'studybuddy.admin.final.*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-reports"></use></svg>
                            <span>Platform</span>
                        </a>
                    @endif
                    @if($healthUrl)
                        <a href="{{ $healthUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.control-room.health*']) ? 'is-active' : '' }}" @if($routeActive(['admin.control-room.health*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-health"></use></svg>
                            <span>Health &amp; Status</span>
                        </a>
                    @endif
                    @if($verificationUrl)
                        <a href="{{ $verificationUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.control-room.verifications.*']) ? 'is-active' : '' }}" @if($routeActive(['admin.control-room.verifications.*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-shield"></use></svg>
                            <span>Safety Review</span>
                        </a>
                    @endif
                    @if($roleToolsUrl)
                        <a href="{{ $roleToolsUrl }}" class="sb-admin-nav__item {{ $routeActive(['admin.control-room.role-tools.*']) ? 'is-active' : '' }}" @if($routeActive(['admin.control-room.role-tools.*'])) aria-current="page" @endif>
                            <svg aria-hidden="true"><use href="#sb-admin-icon-roles"></use></svg>
                            <span>Role Tools</span>
                        </a>
                    @endif
                </div>
            </nav>

            <div class="sb-simple-admin-sidebar-footer sb-admin-sidebar__footer">
                <a href="{{ url('/') }}" target="_blank" rel="noopener">
                    <svg aria-hidden="true"><use href="#sb-admin-icon-external"></use></svg>
                    <span>View Website</span>
                </a>
                <form method="POST" action="{{ $logoutUrl }}">
                    @csrf
                    <button type="submit">
                        <svg aria-hidden="true"><use href="#sb-admin-icon-logout"></use></svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <button type="button" class="sb-simple-admin-overlay sb-admin-overlay" aria-label="Close navigation" data-admin-overlay hidden></button>

        <div class="sb-simple-admin-page sb-admin-page">
            <header class="sb-simple-admin-topbar sb-admin-topbar">
                <button
                    type="button"
                    class="sb-admin-icon-button sb-admin-mobile-menu"
                    aria-label="Open navigation"
                    aria-controls="studybuddy-admin-sidebar"
                    aria-expanded="false"
                    data-admin-sidebar-open
                >
                    <svg aria-hidden="true"><use href="#sb-admin-icon-menu"></use></svg>
                </button>

                <div class="sb-simple-admin-page-title sb-admin-topbar__title">
                    <span>Control Room / {{ $currentGroup }}</span>
                    <strong>@yield('title', 'Dashboard')</strong>
                </div>

                @if($accountUrl)
                    <a class="sb-simple-admin-user sb-admin-account-link" href="{{ $accountUrl }}" aria-label="Open Admin account for {{ $adminName }}">
                        <span aria-hidden="true">{{ $adminInitial }}</span>
                        <div>
                            <strong>{{ $adminName }}</strong>
                            <small>Administrator</small>
                        </div>
                    </a>
                @else
                    <div class="sb-simple-admin-user sb-admin-account-link">
                        <span aria-hidden="true">{{ $adminInitial }}</span>
                        <div><strong>{{ $adminName }}</strong><small>Administrator</small></div>
                    </div>
                @endif
            </header>

            <main id="admin-main-content" class="sb-simple-admin-content sb-admin-content" tabindex="-1">
                @if(session('status') && ! request()->routeIs('admin.control-room.apps.*'))
                    <div class="sb-simple-admin-alert sb-admin-notice is-success" role="status">
                        <svg aria-hidden="true"><use href="#sb-admin-icon-check"></use></svg>
                        <div><strong>Done</strong><span>{{ session('status') }}</span></div>
                    </div>
                @endif

                @if($errors->any() && ! request()->routeIs('admin.control-room.apps.*'))
                    <div class="sb-simple-admin-alert sb-admin-notice is-error" role="alert">
                        <svg aria-hidden="true"><use href="#sb-admin-icon-warning"></use></svg>
                        <div><strong>We couldn't save this.</strong><span>{{ $errors->first() }}</span></div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <svg class="sb-admin-svg-library" aria-hidden="true">
        <symbol id="sb-admin-icon-dashboard" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="2"></rect><rect x="14" y="3" width="7" height="7" rx="2"></rect><rect x="3" y="14" width="7" height="7" rx="2"></rect><rect x="14" y="14" width="7" height="7" rx="2"></rect></symbol>
        <symbol id="sb-admin-icon-home" viewBox="0 0 24 24"><path d="M3 11.5 12 4l9 7.5"></path><path d="M5.5 10.5V20h13v-9.5"></path><path d="M9.5 20v-6h5v6"></path></symbol>
        <symbol id="sb-admin-icon-pages" viewBox="0 0 24 24"><path d="M7 3h8l4 4v14H7z"></path><path d="M15 3v5h4"></path><path d="M10 12h6M10 16h6"></path></symbol>
        <symbol id="sb-admin-icon-edit" viewBox="0 0 24 24"><path d="M4 20h4l11-11-4-4L4 16z"></path><path d="m13.5 6.5 4 4"></path></symbol>
        <symbol id="sb-admin-icon-apps" viewBox="0 0 24 24"><rect x="3" y="3" width="8" height="8" rx="2"></rect><rect x="13" y="3" width="8" height="8" rx="2"></rect><rect x="3" y="13" width="8" height="8" rx="2"></rect><rect x="13" y="13" width="8" height="8" rx="2"></rect></symbol>
        <symbol id="sb-admin-icon-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></symbol>
        <symbol id="sb-admin-icon-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"></circle><path d="M3 21v-2a6 6 0 0 1 12 0v2"></path><path d="M16 4.5a4 4 0 0 1 0 7M18 14a6 6 0 0 1 3 5v2"></path></symbol>
        <symbol id="sb-admin-icon-message" viewBox="0 0 24 24"><path d="M4 5h16v11H8l-4 4z"></path><path d="M8 9h8M8 12h5"></path></symbol>
        <symbol id="sb-admin-icon-mail" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m4 7 8 6 8-6"></path></symbol>
        <symbol id="sb-admin-icon-palette" viewBox="0 0 24 24"><path d="M12 3a9 9 0 1 0 0 18h1.5a2 2 0 0 0 0-4H12a1.5 1.5 0 0 1 0-3h2a7 7 0 0 0-2-11Z"></path><circle cx="7.5" cy="10" r="1"></circle><circle cx="10" cy="6.8" r="1"></circle><circle cx="15" cy="7.5" r="1"></circle></symbol>
        <symbol id="sb-admin-icon-media" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m5 18 5-5 3 3 2-2 4 4"></path></symbol>
        <symbol id="sb-admin-icon-settings" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19 12a7 7 0 0 0-.1-1l2-1.5-2-3.4-2.4 1a8 8 0 0 0-1.7-1L14.5 3h-5l-.4 3.1a8 8 0 0 0-1.7 1l-2.4-1-2 3.4L5.1 11a7 7 0 0 0 0 2L3 14.5l2 3.4 2.4-1a8 8 0 0 0 1.7 1l.4 3.1h5l.4-3.1a8 8 0 0 0 1.7-1l2.4 1 2-3.4-2.1-1.5a7 7 0 0 0 .1-1Z"></path></symbol>
        <symbol id="sb-admin-icon-reports" viewBox="0 0 24 24"><path d="M5 20V10M12 20V4M19 20v-7"></path><path d="M3 20h18"></path></symbol>
        <symbol id="sb-admin-icon-health" viewBox="0 0 24 24"><path d="M3 12h4l2-5 4 10 2-5h6"></path></symbol>
        <symbol id="sb-admin-icon-shield" viewBox="0 0 24 24"><path d="M12 3 20 6v6c0 5-3.3 8-8 9-4.7-1-8-4-8-9V6z"></path><path d="m9 12 2 2 4-5"></path></symbol>
        <symbol id="sb-admin-icon-roles" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"></circle><path d="M5 21v-2a7 7 0 0 1 14 0v2M3 7h2M19 7h2"></path></symbol>
        <symbol id="sb-admin-icon-external" viewBox="0 0 24 24"><path d="M14 4h6v6"></path><path d="m20 4-9 9"></path><path d="M18 13v7H4V6h7"></path></symbol>
        <symbol id="sb-admin-icon-logout" viewBox="0 0 24 24"><path d="M10 4H5v16h5"></path><path d="M14 8l4 4-4 4M18 12H9"></path></symbol>
        <symbol id="sb-admin-icon-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"></path></symbol>
        <symbol id="sb-admin-icon-close" viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"></path></symbol>
        <symbol id="sb-admin-icon-arrow" viewBox="0 0 24 24"><path d="M5 12h14M14 7l5 5-5 5"></path></symbol>
        <symbol id="sb-admin-icon-check" viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"></path></symbol>
        <symbol id="sb-admin-icon-warning" viewBox="0 0 24 24"><path d="M12 3 2.8 20h18.4z"></path><path d="M12 9v4M12 17h.01"></path></symbol>
    </svg>

    <script src="{{ asset('assets/js/studybuddy-admin-simple.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-admin-simple.js')) }}" defer></script>
    @stack('scripts')


</body>
</html>
