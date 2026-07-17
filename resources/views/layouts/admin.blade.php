<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Admin') · StudyBuddy
    </title>

    @if(file_exists(public_path('assets/css/admin.css')))
        <link
            rel="stylesheet"
            href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}"
        >
    @endif

    <link
        rel="stylesheet"
        href="{{ asset('assets/css/studybuddy-admin-simple.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-admin-simple.css')) }}"
    >

    @stack('styles')

    @if(file_exists(public_path('assets/css/studybuddy-admin-unified.css')))
        <link
            rel="stylesheet"
            href="{{ asset('assets/css/studybuddy-admin-unified.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-admin-unified.css')) }}"
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

    $routeActive = function (array $patterns): bool {
        return request()->routeIs(...$patterns);
    };

    $overviewUrl = $routeUrl([
        'admin.control-room.index',
        'admin.dashboard',
    ]);

    $homepageUrl = $routeUrl([
        'admin.control-room.homepage-cms.index',
    ]);

    $contentUrl = $routeUrl([
        'admin.control-room.content-studio',
        'studybuddy.admin.content.index',
    ]);

    $pagesUrl = $routeUrl([
        'admin.pages.index',
        'pages.index',
    ]);

    $appsUrl = $routeUrl([
        'admin.control-room.final-platform',
        'studybuddy.admin.final.index',
    ]);

    $shellUrl = $routeUrl([
        'admin.control-room.shell',
    ]);

    $settingsUrl = $routeUrl([
        'admin.control-room.site-settings.index',
        'admin.site-settings.index',
    ]);

    $usersUrl = $routeUrl([
        'admin.control-room.users.index',
        'admin.users.index',
    ]);

    $messagesUrl = $routeUrl([
        'admin.control-room.contact-messages.index',
    ]);

    $mailingUrl = $routeUrl([
        'admin.control-room.mailing-list.index',
    ]);

    $verificationUrl = $routeUrl([
        'admin.control-room.verifications.index',
    ]);

    $healthUrl = $routeUrl([
        'admin.control-room.health.index',
    ]);

    $accountUrl = $routeUrl([
        'admin.control-room.account',
    ]);

    $roleToolsUrl = $routeUrl([
        'admin.control-room.role-tools',
    ]);
@endphp

<body class="sb-simple-admin-body">
    <a
        class="sb-admin-skip-link"
        href="#admin-main-content"
    >
        Skip to main content
    </a>

    <div
        class="sb-simple-admin-shell"
        data-admin-shell
    >
        <aside
            id="studybuddy-admin-sidebar"
            class="sb-simple-admin-sidebar"
            aria-label="StudyBuddy Admin navigation"
            data-admin-sidebar
        >
            <div class="sb-simple-admin-brand">
                <a href="{{ $overviewUrl ?: url('/admin/control-room') }}">
                    <img
                        src="{{ asset('assets/studybuddy-brand/studybuddy-logo-mark.svg') }}"
                        alt=""
                    >

                    <span>
                        <strong>StudyBuddy</strong>
                        <small>Admin Control Room</small>
                    </span>
                </a>

                <button
                    type="button"
                    class="sb-admin-icon-button sb-admin-sidebar-close"
                    aria-label="Close Admin navigation"
                    data-admin-sidebar-close
                >
                    <svg aria-hidden="true">
                        <use href="#sb-admin-icon-close"></use>
                    </svg>
                </button>
            </div>

            <div class="sb-simple-admin-start">
                <span>Start here</span>

                <p>
                    Choose what you want to edit.
                    Each section controls one clear part
                    of the website.
                </p>
            </div>

            <nav class="sb-simple-admin-nav">
                @if($overviewUrl)
                    <a
                        href="{{ $overviewUrl }}"
                        class="{{ $routeActive([
                            'admin.control-room.index',
                            'admin.dashboard',
                        ]) ? 'is-active' : '' }}"
                    >
                        <svg aria-hidden="true">
                            <use href="#sb-admin-icon-dashboard"></use>
                        </svg>

                        <span>
                            <strong>Admin Home</strong>
                            <small>Choose what to manage</small>
                        </span>
                    </a>
                @endif

                <details open>
                    <summary>
                        <span>Website content</span>

                        <svg aria-hidden="true">
                            <use href="#sb-admin-icon-chevron"></use>
                        </svg>
                    </summary>

                    <div>
                        @if($homepageUrl)
                            <a
                                href="{{ $homepageUrl }}"
                                class="{{ $routeActive([
                                    'admin.control-room.homepage-cms.*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-home"></use>
                                </svg>

                                <span>
                                    <strong>Homepage</strong>
                                    <small>Edit homepage sections</small>
                                </span>
                            </a>
                        @endif

                        @if($pagesUrl)
                            <a
                                href="{{ $pagesUrl }}"
                                class="{{ $routeActive([
                                    'admin.pages.*',
                                    'pages.*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-pages"></use>
                                </svg>

                                <span>
                                    <strong>Website Pages</strong>
                                    <small>Edit About, Help and legal pages</small>
                                </span>
                            </a>
                        @endif

                        @if($contentUrl)
                            <a
                                href="{{ $contentUrl }}"
                                class="{{ $routeActive([
                                    'admin.control-room.content-*',
                                    'admin.control-room.content.*',
                                    'studybuddy.admin.content.*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-edit"></use>
                                </svg>

                                <span>
                                    <strong>Content Studio</strong>
                                    <small>Edit platform text and cards</small>
                                </span>
                            </a>
                        @endif
                    </div>
                </details>

                <details open>
                    <summary>
                        <span>Apps and appearance</span>

                        <svg aria-hidden="true">
                            <use href="#sb-admin-icon-chevron"></use>
                        </svg>
                    </summary>

                    <div>
                        @if($appsUrl)
                            <a
                                href="{{ $appsUrl }}"
                                class="{{ $routeActive([
                                    'admin.control-room.final-*',
                                    'admin.control-room.final.*',
                                    'studybuddy.admin.final.*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-apps"></use>
                                </svg>

                                <span>
                                    <strong>Apps and Launcher</strong>
                                    <small>Add apps and publish ZIP packages</small>
                                </span>
                            </a>
                        @endif

                        @if($shellUrl)
                            <a
                                href="{{ $shellUrl }}"
                                class="{{ $routeActive([
                                    'admin.control-room.shell*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-palette"></use>
                                </svg>

                                <span>
                                    <strong>Header and Footer</strong>
                                    <small>Edit navigation and shared layout</small>
                                </span>
                            </a>
                        @endif

                        @if($settingsUrl)
                            <a
                                href="{{ $settingsUrl }}"
                                class="{{ $routeActive([
                                    'admin.control-room.site-settings.*',
                                    'admin.site-settings.*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-settings"></use>
                                </svg>

                                <span>
                                    <strong>Site Settings</strong>
                                    <small>Brand, links and global options</small>
                                </span>
                            </a>
                        @endif
                    </div>
                </details>

                <details open>
                    <summary>
                        <span>People and communication</span>

                        <svg aria-hidden="true">
                            <use href="#sb-admin-icon-chevron"></use>
                        </svg>
                    </summary>

                    <div>
                        @if($usersUrl)
                            <a
                                href="{{ $usersUrl }}"
                                class="{{ $routeActive([
                                    'admin.control-room.users.*',
                                    'admin.users.*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-users"></use>
                                </svg>

                                <span>
                                    <strong>Users</strong>
                                    <small>Manage accounts and access</small>
                                </span>
                            </a>
                        @endif

                        @if($messagesUrl)
                            <a
                                href="{{ $messagesUrl }}"
                                class="{{ $routeActive([
                                    'admin.control-room.contact-messages.*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-message"></use>
                                </svg>

                                <span>
                                    <strong>Messages</strong>
                                    <small>Read website contact messages</small>
                                </span>
                            </a>
                        @endif

                        @if($mailingUrl)
                            <a
                                href="{{ $mailingUrl }}"
                                class="{{ $routeActive([
                                    'admin.control-room.mailing-list.*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-mail"></use>
                                </svg>

                                <span>
                                    <strong>Mailing List</strong>
                                    <small>Manage update subscribers</small>
                                </span>
                            </a>
                        @endif

                        @if($verificationUrl)
                            <a
                                href="{{ $verificationUrl }}"
                                class="{{ $routeActive([
                                    'admin.control-room.verifications.*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-shield"></use>
                                </svg>

                                <span>
                                    <strong>Verifications</strong>
                                    <small>Review role and safety requests</small>
                                </span>
                            </a>
                        @endif
                    </div>
                </details>

                <details open>
                    <summary>
                        <span>System</span>

                        <svg aria-hidden="true">
                            <use href="#sb-admin-icon-chevron"></use>
                        </svg>
                    </summary>

                    <div>
                        @if($healthUrl)
                            <a
                                href="{{ $healthUrl }}"
                                class="{{ $routeActive([
                                    'admin.control-room.health.*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-health"></use>
                                </svg>

                                <span>
                                    <strong>Website Health</strong>
                                    <small>Check routes, database and forms</small>
                                </span>
                            </a>
                        @endif

                        @if($roleToolsUrl)
                            <a
                                href="{{ $roleToolsUrl }}"
                                class="{{ $routeActive([
                                    'admin.control-room.role-tools*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-roles"></use>
                                </svg>

                                <span>
                                    <strong>Role Tools</strong>
                                    <small>Manage learner role experiences</small>
                                </span>
                            </a>
                        @endif

                        @if($accountUrl)
                            <a
                                href="{{ $accountUrl }}"
                                class="{{ $routeActive([
                                    'admin.control-room.account*',
                                ]) ? 'is-active' : '' }}"
                            >
                                <svg aria-hidden="true">
                                    <use href="#sb-admin-icon-account"></use>
                                </svg>

                                <span>
                                    <strong>Admin Account</strong>
                                    <small>Manage your Admin profile</small>
                                </span>
                            </a>
                        @endif
                    </div>
                </details>
            </nav>

            <div class="sb-simple-admin-sidebar-footer">
                <a
                    href="{{ url('/') }}"
                    target="_blank"
                    rel="noopener"
                >
                    <svg aria-hidden="true">
                        <use href="#sb-admin-icon-external"></use>
                    </svg>

                    View website
                </a>

                <form
                    method="POST"
                    action="{{ url('/logout') }}"
                >
                    @csrf

                    <button type="submit">
                        <svg aria-hidden="true">
                            <use href="#sb-admin-icon-logout"></use>
                        </svg>

                        Sign out
                    </button>
                </form>
            </div>
        </aside>

        <button
            type="button"
            class="sb-simple-admin-overlay"
            aria-label="Close Admin navigation"
            data-admin-overlay
            hidden
        ></button>

        <div class="sb-simple-admin-page">
            <header class="sb-simple-admin-topbar">
                <button
                    type="button"
                    class="sb-admin-icon-button sb-admin-mobile-menu"
                    aria-label="Open Admin navigation"
                    aria-controls="studybuddy-admin-sidebar"
                    aria-expanded="false"
                    data-admin-sidebar-open
                >
                    <svg aria-hidden="true">
                        <use href="#sb-admin-icon-menu"></use>
                    </svg>
                </button>

                <div class="sb-simple-admin-page-title">
                    <span>Admin Control Room</span>

                    <strong>
                        @yield('title', 'Admin Home')
                    </strong>
                </div>

                <div class="sb-simple-admin-user">
                    <span>
                        {{ mb_strtoupper(
                            mb_substr(
                                auth()->user()?->name ?: 'A',
                                0,
                                1
                            )
                        ) }}
                    </span>

                    <div>
                        <strong>
                            {{ auth()->user()?->name ?: 'Administrator' }}
                        </strong>

                        <small>Administrator</small>
                    </div>
                </div>
            </header>

            <main
                id="admin-main-content"
                class="sb-simple-admin-content"
                tabindex="-1"
            >
                @if(session('status'))
                    <div
                        class="sb-simple-admin-alert is-success"
                        role="status"
                    >
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div
                        class="sb-simple-admin-alert is-error"
                        role="alert"
                    >
                        <strong>
                            This could not be saved.
                        </strong>

                        <span>
                            {{ $errors->first() }}
                        </span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <svg
        class="sb-admin-svg-library"
        aria-hidden="true"
    >
        <symbol
            id="sb-admin-icon-dashboard"
            viewBox="0 0 24 24"
        >
            <rect x="3" y="3" width="7" height="7" rx="2"></rect>
            <rect x="14" y="3" width="7" height="7" rx="2"></rect>
            <rect x="3" y="14" width="7" height="7" rx="2"></rect>
            <rect x="14" y="14" width="7" height="7" rx="2"></rect>
        </symbol>

        <symbol
            id="sb-admin-icon-home"
            viewBox="0 0 24 24"
        >
            <path d="M3 11.5 12 4l9 7.5"></path>
            <path d="M5.5 10.5V20h13v-9.5"></path>
            <path d="M9.5 20v-6h5v6"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-pages"
            viewBox="0 0 24 24"
        >
            <path d="M7 3h8l4 4v14H7z"></path>
            <path d="M15 3v5h4"></path>
            <path d="M10 12h6M10 16h6"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-edit"
            viewBox="0 0 24 24"
        >
            <path d="M4 20h4l11-11-4-4L4 16z"></path>
            <path d="m13.5 6.5 4 4"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-apps"
            viewBox="0 0 24 24"
        >
            <rect x="3" y="3" width="8" height="8" rx="2"></rect>
            <rect x="13" y="3" width="8" height="8" rx="2"></rect>
            <rect x="3" y="13" width="8" height="8" rx="2"></rect>
            <rect x="13" y="13" width="8" height="8" rx="2"></rect>
        </symbol>

        <symbol
            id="sb-admin-icon-palette"
            viewBox="0 0 24 24"
        >
            <path d="M12 3a9 9 0 1 0 0 18h1.5a2 2 0 0 0 0-4H12a1.5 1.5 0 0 1 0-3h2a7 7 0 0 0-2-11Z"></path>
            <circle cx="7.5" cy="10" r="1"></circle>
            <circle cx="10" cy="6.8" r="1"></circle>
            <circle cx="15" cy="7.5" r="1"></circle>
        </symbol>

        <symbol
            id="sb-admin-icon-settings"
            viewBox="0 0 24 24"
        >
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19 12a7 7 0 0 0-.1-1l2-1.5-2-3.4-2.4 1a8 8 0 0 0-1.7-1L14.5 3h-5l-.4 3.1a8 8 0 0 0-1.7 1l-2.4-1-2 3.4L5.1 11a7 7 0 0 0 0 2L3 14.5l2 3.4 2.4-1a8 8 0 0 0 1.7 1l.4 3.1h5l.4-3.1a8 8 0 0 0 1.7-1l2.4 1 2-3.4-2.1-1.5a7 7 0 0 0 .1-1Z"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-users"
            viewBox="0 0 24 24"
        >
            <circle cx="9" cy="8" r="4"></circle>
            <path d="M3 21v-2a6 6 0 0 1 12 0v2"></path>
            <path d="M16 4.5a4 4 0 0 1 0 7"></path>
            <path d="M18 14a6 6 0 0 1 3 5v2"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-message"
            viewBox="0 0 24 24"
        >
            <path d="M4 5h16v11H8l-4 4z"></path>
            <path d="M8 9h8M8 12h5"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-mail"
            viewBox="0 0 24 24"
        >
            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
            <path d="m4 7 8 6 8-6"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-shield"
            viewBox="0 0 24 24"
        >
            <path d="M12 3 20 6v6c0 5-3.3 8-8 9-4.7-1-8-4-8-9V6z"></path>
            <path d="m9 12 2 2 4-5"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-health"
            viewBox="0 0 24 24"
        >
            <path d="M3 12h4l2-5 4 10 2-5h6"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-roles"
            viewBox="0 0 24 24"
        >
            <circle cx="12" cy="7" r="4"></circle>
            <path d="M5 21v-2a7 7 0 0 1 14 0v2"></path>
            <path d="M3 7h2M19 7h2"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-account"
            viewBox="0 0 24 24"
        >
            <circle cx="12" cy="8" r="4"></circle>
            <path d="M4 21a8 8 0 0 1 16 0"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-external"
            viewBox="0 0 24 24"
        >
            <path d="M14 4h6v6"></path>
            <path d="m20 4-9 9"></path>
            <path d="M18 13v7H4V6h7"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-logout"
            viewBox="0 0 24 24"
        >
            <path d="M10 4H5v16h5"></path>
            <path d="M14 8l4 4-4 4"></path>
            <path d="M18 12H9"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-menu"
            viewBox="0 0 24 24"
        >
            <path d="M4 7h16M4 12h16M4 17h16"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-close"
            viewBox="0 0 24 24"
        >
            <path d="m6 6 12 12M18 6 6 18"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-chevron"
            viewBox="0 0 24 24"
        >
            <path d="m8 10 4 4 4-4"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-search"
            viewBox="0 0 24 24"
        >
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m20 20-4-4"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-arrow"
            viewBox="0 0 24 24"
        >
            <path d="M5 12h14"></path>
            <path d="m14 7 5 5-5 5"></path>
        </symbol>

        <symbol
            id="sb-admin-icon-check"
            viewBox="0 0 24 24"
        >
            <path d="m5 12 4 4L19 6"></path>
        </symbol>
    </svg>

    <script
        src="{{ asset('assets/js/studybuddy-admin-simple.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-admin-simple.js')) }}"
        defer
    ></script>

    @stack('scripts')

    @if(file_exists(public_path('assets/js/studybuddy-admin-unified.js')))
        <script
            src="{{ asset('assets/js/studybuddy-admin-unified.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-admin-unified.js')) }}"
            defer
        ></script>
    @endif

</body>
</html>
