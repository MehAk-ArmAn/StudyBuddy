@extends('layouts.admin')

@section('title', 'Admin Home')

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('assets/css/studybuddy-admin-blocks.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-admin-blocks.css')) ? filemtime(public_path('assets/css/studybuddy-admin-blocks.css')) : time() }}"
>
@endpush

@php
    $routeUrl = function (array $names): ?string {
        foreach ($names as $name) {
            if (\Illuminate\Support\Facades\Route::has($name)) {
                return route($name);
            }
        }

        return null;
    };

    $tool = function (
        string $title,
        string $description,
        string $controls,
        string $icon,
        array $routes,
        string $keywords
    ) use ($routeUrl): array {
        return [
            'title' => $title,
            'description' => $description,
            'controls' => $controls,
            'icon' => $icon,
            'url' => $routeUrl($routes),
            'keywords' => $keywords,
        ];
    };

    $zones = collect([
        [
            'id' => 'content',
            'title' => 'Website Content',
            'subtitle' => 'Edit the words, images and sections visitors see.',
            'icon' => 'edit',
            'accent' => 'purple',
            'tools' => [
                $tool(
                    'Homepage',
                    'Edit the main homepage without touching code.',
                    'Hero text, headings, paragraphs, buttons, images and homepage sections.',
                    'home',
                    [
                        'admin.control-room.homepage-cms.index',
                    ],
                    'homepage home hero headings paragraphs buttons images sections'
                ),

                $tool(
                    'Website Pages',
                    'Manage supporting pages from one place.',
                    'About, Help, Support, Privacy, Terms and other published pages.',
                    'pages',
                    [
                        'admin.pages.index',
                        'pages.index',
                    ],
                    'pages about help support privacy terms legal'
                ),

                $tool(
                    'Content Studio',
                    'Manage reusable text and content cards.',
                    'Shared cards, labels, descriptions and reusable platform content.',
                    'edit',
                    [
                        'admin.control-room.content-studio',
                        'studybuddy.admin.content.index',
                    ],
                    'content studio cards labels descriptions reusable'
                ),
            ],
        ],

        [
            'id' => 'apps',
            'title' => 'Apps and Platform',
            'subtitle' => 'Create, publish and control StudyBuddy learning apps.',
            'icon' => 'apps',
            'accent' => 'cyan',
            'tools' => [
                $tool(
                    'Apps and Launcher',
                    'Add app listings and publish real web-app packages.',
                    'App names, images, descriptions, age range, points, roles, status and ZIP packages.',
                    'apps',
                    [
                        'admin.control-room.final-platform',
                        'studybuddy.admin.final.index',
                    ],
                    'apps launcher zip package upload points roles status'
                ),

                $tool(
                    'Header and Footer',
                    'Control the shared website navigation.',
                    'Header links, footer columns, menus and platform-wide layout content.',
                    'palette',
                    [
                        'admin.control-room.shell',
                    ],
                    'header footer navigation menu links shell'
                ),

                $tool(
                    'Site Settings',
                    'Manage global StudyBuddy information.',
                    'Brand details, website links, global values and platform settings.',
                    'settings',
                    [
                        'admin.control-room.site-settings.index',
                        'admin.site-settings.index',
                    ],
                    'settings brand links global website configuration'
                ),

                $tool(
                    'Role Tools',
                    'Control role-specific platform experiences.',
                    'Learner, parent, teacher and independent learner tools and visibility.',
                    'roles',
                    [
                        'admin.control-room.role-tools',
                    ],
                    'roles learner parent teacher independent access'
                ),
            ],
        ],

        [
            'id' => 'people',
            'title' => 'People and Communication',
            'subtitle' => 'Manage users and messages from the StudyBuddy community.',
            'icon' => 'users',
            'accent' => 'pink',
            'tools' => [
                $tool(
                    'Users',
                    'Review and manage StudyBuddy accounts.',
                    'Names, emails, account roles, access and account status.',
                    'users',
                    [
                        'admin.control-room.users.index',
                        'admin.users.index',
                    ],
                    'users accounts roles access members email'
                ),

                $tool(
                    'Contact Messages',
                    'Read messages submitted through the website.',
                    'Message details, status, priority and Admin notes.',
                    'message',
                    [
                        'admin.control-room.contact-messages.index',
                    ],
                    'messages contact inbox questions support'
                ),

                $tool(
                    'Mailing List',
                    'Manage users subscribed to StudyBuddy updates.',
                    'Subscriber emails, active status, reactivation, deletion and CSV export.',
                    'mail',
                    [
                        'admin.control-room.mailing-list.index',
                    ],
                    'mailing list newsletter subscribers emails export'
                ),

                $tool(
                    'Verifications',
                    'Review account and role-verification requests.',
                    'Parent, teacher and independent learner verification status and notes.',
                    'shield',
                    [
                        'admin.control-room.verifications.index',
                    ],
                    'verification safety parent teacher independent learner'
                ),
            ],
        ],

        [
            'id' => 'system',
            'title' => 'System and Safety',
            'subtitle' => 'Check that StudyBuddy is healthy and publication-ready.',
            'icon' => 'health',
            'accent' => 'green',
            'tools' => [
                $tool(
                    'Website Health',
                    'Check whether important services are working.',
                    'Database, routes, forms, website pages and publishing-readiness checks.',
                    'health',
                    [
                        'admin.control-room.health.index',
                    ],
                    'health database routes forms errors publish system'
                ),

                $tool(
                    'Admin Account',
                    'Manage your own administrator profile.',
                    'Admin name, account information and account preferences.',
                    'account',
                    [
                        'admin.control-room.account',
                    ],
                    'admin account profile preferences'
                ),
            ],
        ],
    ])
        ->map(function (array $zone): array {
            $zone['tools'] = collect($zone['tools'])
                ->filter(
                    fn (array $item): bool => filled($item['url'])
                )
                ->values();

            return $zone;
        })
        ->filter(
            fn (array $zone): bool => $zone['tools']->isNotEmpty()
        )
        ->values();

    $homepageUrl = $routeUrl([
        'admin.control-room.homepage-cms.index',
    ]);

    $appsUrl = $routeUrl([
        'admin.control-room.final-platform',
        'studybuddy.admin.final.index',
    ]);

    $messagesUrl = $routeUrl([
        'admin.control-room.contact-messages.index',
    ]);

    $healthUrl = $routeUrl([
        'admin.control-room.health.index',
    ]);
@endphp

@section('content')
<div
    class="sb-admin-block-dashboard"
    data-admin-block-dashboard
>
    <section class="sb-admin-block-hero">
        <div class="sb-admin-block-hero__copy">
            <p class="sb-admin-block-eyebrow">
                StudyBuddy Control Room
            </p>

            <h1>
                Everything is organised by what you need to edit.
            </h1>

            <p>
                Start with a quick action or open one of the clear
                management blocks below. Every tool explains exactly
                what it controls before you open it.
            </p>
        </div>

        <div class="sb-admin-block-hero__actions">
            <a
                href="{{ url('/') }}"
                target="_blank"
                rel="noopener"
                class="sb-admin-block-button is-primary"
            >
                <svg aria-hidden="true">
                    <use href="#sb-admin-icon-external"></use>
                </svg>

                View public website
            </a>
        </div>
    </section>

    <section
        class="sb-admin-quick-section"
        aria-labelledby="admin-quick-actions-title"
    >
        <header class="sb-admin-block-heading">
            <div>
                <p class="sb-admin-block-eyebrow">
                    Start here
                </p>

                <h2 id="admin-quick-actions-title">
                    Quick actions
                </h2>
            </div>

            <p>
                The tasks you are most likely to use after publishing.
            </p>
        </header>

        <div class="sb-admin-quick-grid">
            @if($homepageUrl)
                <a
                    href="{{ $homepageUrl }}"
                    class="sb-admin-quick-card"
                    data-admin-tool-link
                    data-tool-id="homepage"
                    data-tool-title="Homepage"
                    data-tool-description="Edit homepage text, buttons, images and sections."
                    data-tool-url="{{ $homepageUrl }}"
                    data-tool-icon="home"
                    data-tool-category="Website Content"
                >
                    <span class="sb-admin-quick-card__icon">
                        <svg aria-hidden="true">
                            <use href="#sb-admin-icon-home"></use>
                        </svg>
                    </span>

                    <span>
                        <strong>Edit Homepage</strong>
                        <small>Change the main public page</small>
                    </span>

                    <svg aria-hidden="true">
                        <use href="#sb-admin-icon-arrow"></use>
                    </svg>
                </a>
            @endif

            @if($appsUrl)
                <a
                    href="{{ $appsUrl }}"
                    class="sb-admin-quick-card"
                    data-admin-tool-link
                    data-tool-id="apps-launcher"
                    data-tool-title="Apps and Launcher"
                    data-tool-description="Add apps and publish web-app ZIP packages."
                    data-tool-url="{{ $appsUrl }}"
                    data-tool-icon="apps"
                    data-tool-category="Apps and Platform"
                >
                    <span class="sb-admin-quick-card__icon">
                        <svg aria-hidden="true">
                            <use href="#sb-admin-icon-apps"></use>
                        </svg>
                    </span>

                    <span>
                        <strong>Manage Apps</strong>
                        <small>Add apps and launcher packages</small>
                    </span>

                    <svg aria-hidden="true">
                        <use href="#sb-admin-icon-arrow"></use>
                    </svg>
                </a>
            @endif

            @if($messagesUrl)
                <a
                    href="{{ $messagesUrl }}"
                    class="sb-admin-quick-card"
                    data-admin-tool-link
                    data-tool-id="contact-messages"
                    data-tool-title="Contact Messages"
                    data-tool-description="Read messages sent through the public contact form."
                    data-tool-url="{{ $messagesUrl }}"
                    data-tool-icon="message"
                    data-tool-category="People and Communication"
                >
                    <span class="sb-admin-quick-card__icon">
                        <svg aria-hidden="true">
                            <use href="#sb-admin-icon-message"></use>
                        </svg>
                    </span>

                    <span>
                        <strong>Read Messages</strong>
                        <small>Open the website inbox</small>
                    </span>

                    <svg aria-hidden="true">
                        <use href="#sb-admin-icon-arrow"></use>
                    </svg>
                </a>
            @endif

            @if($healthUrl)
                <a
                    href="{{ $healthUrl }}"
                    class="sb-admin-quick-card"
                    data-admin-tool-link
                    data-tool-id="website-health"
                    data-tool-title="Website Health"
                    data-tool-description="Check important StudyBuddy services and publishing readiness."
                    data-tool-url="{{ $healthUrl }}"
                    data-tool-icon="health"
                    data-tool-category="System and Safety"
                >
                    <span class="sb-admin-quick-card__icon">
                        <svg aria-hidden="true">
                            <use href="#sb-admin-icon-health"></use>
                        </svg>
                    </span>

                    <span>
                        <strong>Check Website</strong>
                        <small>Confirm the system is healthy</small>
                    </span>

                    <svg aria-hidden="true">
                        <use href="#sb-admin-icon-arrow"></use>
                    </svg>
                </a>
            @endif
        </div>
    </section>

    <section
        class="sb-admin-control-bar"
        aria-label="Admin tool navigation"
    >
        <nav
            class="sb-admin-jump-links"
            aria-label="Jump to an Admin section"
        >
            @foreach($zones as $zone)
                <a
                    href="#admin-zone-{{ $zone['id'] }}"
                    data-admin-zone-jump="{{ $zone['id'] }}"
                >
                    <svg aria-hidden="true">
                        <use href="#sb-admin-icon-{{ $zone['icon'] }}"></use>
                    </svg>

                    {{ $zone['title'] }}
                </a>
            @endforeach
        </nav>

        <label class="sb-admin-block-search">
            <span class="sb-admin-visually-hidden">
                Search all Admin tools
            </span>

            <svg aria-hidden="true">
                <use href="#sb-admin-icon-search"></use>
            </svg>

            <input
                type="search"
                placeholder="Search all Admin tools..."
                autocomplete="off"
                data-admin-block-search
            >

            <button
                type="button"
                aria-label="Clear Admin search"
                data-admin-block-search-clear
                hidden
            >
                <svg aria-hidden="true">
                    <use href="#sb-admin-icon-close"></use>
                </svg>
            </button>
        </label>
    </section>

    <section
        class="sb-admin-recent-section"
        data-admin-recent-section
        hidden
    >
        <header class="sb-admin-block-heading">
            <div>
                <p class="sb-admin-block-eyebrow">
                    Your shortcuts
                </p>

                <h2>Recently opened</h2>
            </div>

            <button
                type="button"
                data-admin-recent-clear
            >
                Clear recent tools
            </button>
        </header>

        <div
            class="sb-admin-recent-grid"
            data-admin-recent-grid
        ></div>
    </section>

    <div class="sb-admin-zone-list">
        @foreach($zones as $zone)
            <details
                id="admin-zone-{{ $zone['id'] }}"
                class="sb-admin-zone is-{{ $zone['accent'] }}"
                data-admin-zone="{{ $zone['id'] }}"
                open
            >
                <summary>
                    <span class="sb-admin-zone__icon">
                        <svg aria-hidden="true">
                            <use href="#sb-admin-icon-{{ $zone['icon'] }}"></use>
                        </svg>
                    </span>

                    <span class="sb-admin-zone__title">
                        <strong>{{ $zone['title'] }}</strong>
                        <small>{{ $zone['subtitle'] }}</small>
                    </span>

                    <span
                        class="sb-admin-zone__count"
                        data-admin-zone-count
                    >
                        {{ $zone['tools']->count() }}
                        {{ \Illuminate\Support\Str::plural('tool', $zone['tools']->count()) }}
                    </span>

                    <svg
                        class="sb-admin-zone__chevron"
                        aria-hidden="true"
                    >
                        <use href="#sb-admin-icon-chevron"></use>
                    </svg>
                </summary>

                <div class="sb-admin-zone__content">
                    <div class="sb-admin-zone__grid">
                        @foreach($zone['tools'] as $index => $item)
                            @php
                                $toolId = $zone['id'].'-'.$index;
                            @endphp

                            <article
                                class="sb-admin-zone-card"
                                data-admin-block-tool
                                data-search="{{ \Illuminate\Support\Str::lower(
                                    $item['title']
                                    .' '
                                    .$item['description']
                                    .' '
                                    .$item['controls']
                                    .' '
                                    .$item['keywords']
                                    .' '
                                    .$zone['title']
                                ) }}"
                            >
                                <div class="sb-admin-zone-card__top">
                                    <span class="sb-admin-zone-card__icon">
                                        <svg aria-hidden="true">
                                            <use href="#sb-admin-icon-{{ $item['icon'] }}"></use>
                                        </svg>
                                    </span>

                                    <span class="sb-admin-zone-card__category">
                                        {{ $zone['title'] }}
                                    </span>
                                </div>

                                <h3>{{ $item['title'] }}</h3>

                                <p class="sb-admin-zone-card__description">
                                    {{ $item['description'] }}
                                </p>

                                <div class="sb-admin-zone-card__controls">
                                    <strong>
                                        What you can change
                                    </strong>

                                    <p>{{ $item['controls'] }}</p>
                                </div>

                                <a
                                    href="{{ $item['url'] }}"
                                    class="sb-admin-zone-card__open"
                                    data-admin-tool-link
                                    data-tool-id="{{ $toolId }}"
                                    data-tool-title="{{ $item['title'] }}"
                                    data-tool-description="{{ $item['description'] }}"
                                    data-tool-url="{{ $item['url'] }}"
                                    data-tool-icon="{{ $item['icon'] }}"
                                    data-tool-category="{{ $zone['title'] }}"
                                >
                                    Open {{ $item['title'] }}

                                    <svg aria-hidden="true">
                                        <use href="#sb-admin-icon-arrow"></use>
                                    </svg>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </details>
        @endforeach
    </div>

    <section
        class="sb-admin-block-empty"
        data-admin-block-empty
        hidden
    >
        <svg aria-hidden="true">
            <use href="#sb-admin-icon-search"></use>
        </svg>

        <h2>No Admin tool matches that search.</h2>

        <p>
            Try searching for homepage, apps, users,
            messages, footer, settings or mailing list.
        </p>

        <button
            type="button"
            data-admin-block-reset
        >
            Show every Admin tool
        </button>
    </section>

    <section class="sb-admin-help-block">
        <div>
            <p class="sb-admin-block-eyebrow">
                Editing reminder
            </p>

            <h2>
                Save first. Then check the public website.
            </h2>

            <p>
                After editing content, use the Save or Update button
                inside that section. Open the public website in another
                tab and refresh it to confirm the result.
            </p>
        </div>

        <ol>
            <li>
                <span>1</span>
                Open the correct Admin block.
            </li>

            <li>
                <span>2</span>
                Make and save the change.
            </li>

            <li>
                <span>3</span>
                Refresh the public website.
            </li>
        </ol>
    </section>
</div>
@endsection

@push('scripts')
<script
    src="{{ asset('assets/js/studybuddy-admin-blocks.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-admin-blocks.js')) ? filemtime(public_path('assets/js/studybuddy-admin-blocks.js')) : time() }}"
    defer
></script>
@endpush
