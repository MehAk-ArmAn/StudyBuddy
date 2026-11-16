@extends('layouts.admin')

@section('title', 'Apps')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-admin-apps.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-admin-apps.css')) ? filemtime(public_path('assets/css/studybuddy-admin-apps.css')) : time() }}">
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-admin-apps-library.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-admin-apps-library.css')) ? filemtime(public_path('assets/css/studybuddy-admin-apps-library.css')) : time() }}">
@endpush

@section('content')
@php
    $hasActiveFilters = $search !== '' || $status !== '' || $visibility !== '';
    $catalogueIsEmpty = $totals['all'] === 0 && ! $hasActiveFilters;
@endphp

<div class="sb-apps sbal" data-admin-skip-unified>
    <header class="sbal-hero">
        <div class="sbal-hero__copy">
            <p class="sbal-eyebrow">Apps Library</p>
            <h1>Manage every learning app</h1>
            <p>Choose what learners can open in their browser or download from an app store.</p>
        </div>

        <div class="sbal-hero__actions">
            <a class="sbal-button sbal-button--quiet" href="{{ url('/apps') }}" target="_blank" rel="noopener">
                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M14 5h5v5M10 14 19 5M19 13v6H5V5h6"/></svg>
                View website
            </a>
            <a class="sbal-button sbal-button--primary" href="{{ route('admin.control-room.apps.create') }}">
                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Add app
            </a>
        </div>
    </header>

    @include('admin.apps.partials.flash')

    @unless($catalogueIsEmpty)
        <section class="sbal-summary" aria-labelledby="sbal-summary-title">
            <div class="sbal-summary__intro">
                <span class="sbal-summary__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>
                </span>
                <div>
                    <h2 id="sbal-summary-title">Your app shelf</h2>
                    <p>Live catalogue at a glance</p>
                </div>
            </div>

            <dl class="sbal-summary__metrics">
                <div><dt>Total</dt><dd>{{ $totals['all'] }}</dd></div>
                <div class="is-published"><dt>Published</dt><dd>{{ $totals['published'] }}</dd></div>
                <div><dt>Hidden</dt><dd>{{ $totals['hidden'] }}</dd></div>
                <div class="is-featured"><dt>Featured</dt><dd>{{ $totals['featured'] }}</dd></div>
            </dl>
        </section>

        <form class="sbal-toolbar" method="GET" action="{{ route('admin.control-room.apps.index') }}" role="search">
            <label class="sbal-search" for="sbal-app-search">
                <span class="sbal-field-label">Search apps</span>
                <span class="sbal-control sbal-control--search">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m16 16 4 4"/></svg>
                    <input id="sbal-app-search" type="search" name="q" value="{{ $search }}" placeholder="Name, slug or category…">
                </span>
            </label>

            <label class="sbal-filter">
                <span class="sbal-field-label">Status</span>
                <select name="status">
                    <option value="">Any status</option>
                    @foreach($statuses as $option)
                        <option value="{{ $option }}" @selected($status === $option)>{{ $statusLabels[$option] ?? ucfirst($option) }}</option>
                    @endforeach
                </select>
            </label>

            <label class="sbal-filter">
                <span class="sbal-field-label">Visibility</span>
                <select name="visibility">
                    <option value="">Any visibility</option>
                    <option value="published" @selected($visibility === 'published')>Published</option>
                    <option value="hidden" @selected($visibility === 'hidden')>Hidden</option>
                    <option value="featured" @selected($visibility === 'featured')>Featured</option>
                </select>
            </label>

            <div class="sbal-toolbar__actions">
                <button class="sbal-button sbal-button--filter" type="submit">Apply filters</button>
                @if($hasActiveFilters)
                    <a class="sbal-clear" href="{{ route('admin.control-room.apps.index') }}">Clear all</a>
                @endif
            </div>
        </form>
    @endunless

    @if($apps->isEmpty())
        <section class="sbal-empty" aria-labelledby="sbal-empty-title">
            <div class="sbal-empty__art" aria-hidden="true">
                <span class="sbal-empty__spark sbal-empty__spark--one">✦</span>
                <span class="sbal-empty__spark sbal-empty__spark--two">✦</span>
                <svg viewBox="0 0 180 150">
                    <path class="sbal-empty__shelf" d="M26 113h128M35 120h110"/>
                    <rect class="sbal-empty__tile sbal-empty__tile--one" x="37" y="55" width="46" height="46" rx="13"/>
                    <path class="sbal-empty__tile-mark sbal-empty__tile-mark--light" d="M50 68h20M50 78h13"/>
                    <rect class="sbal-empty__tile sbal-empty__tile--two" x="91" y="40" width="52" height="61" rx="15"/>
                    <circle class="sbal-empty__tile-mark sbal-empty__tile-mark--dark" cx="117" cy="63" r="9"/>
                    <path class="sbal-empty__tile-mark sbal-empty__tile-mark--dark" d="M105 84h24"/>
                </svg>
            </div>

            @if($hasActiveFilters || $totals['all'] > 0)
                <p class="sbal-eyebrow">No matches</p>
                <h2 id="sbal-empty-title">No apps match those filters</h2>
                <p>Try another search or reset the filters to return to your full app shelf.</p>
                <a class="sbal-button sbal-button--quiet-dark" href="{{ route('admin.control-room.apps.index') }}">Clear filters</a>
            @else
                <p class="sbal-eyebrow">A fresh start</p>
                <h2 id="sbal-empty-title">Your app shelf is ready <span aria-hidden="true">✨</span></h2>
                <p>Add your first real StudyBuddy app and choose where learners can open or download it.</p>
                <a class="sbal-button sbal-button--primary" href="{{ route('admin.control-room.apps.create') }}">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Add your first app
                </a>
            @endif

            <small>Browser, Google Play and App Store links can all be managed here.</small>
        </section>
    @else
        <form id="sbal-reorder-form" method="POST" action="{{ route('admin.control-room.apps.reorder') }}">@csrf</form>

        <section class="sbal-library" aria-labelledby="sbal-library-title">
            <div class="sbal-library__head">
                <div>
                    <p class="sbal-eyebrow">Catalogue</p>
                    <h2 id="sbal-library-title">Learning apps</h2>
                </div>
                <p>{{ $apps->count() }} {{ Str::plural('app', $apps->count()) }} shown</p>
            </div>

            <div class="sbal-list" role="list">
                @foreach($apps as $app)
                    <?php
                        $colors = $app->accentColors();
                        $platforms = collect();
                        $browserConfigured = filled($app->web_play_url) || filled($app->web_app_entry_path);

                        if ($app->is_web_enabled || $browserConfigured) {
                            $platforms->push([
                                'key' => 'browser',
                                'label' => ! $app->is_web_enabled
                                    ? 'Browser hidden'
                                    : ($app->hasPublishedWebApp() ? 'Browser ready' : 'Browser needs attention'),
                                'state' => ! $app->is_web_enabled
                                    ? 'disabled'
                                    : ($app->hasPublishedWebApp() ? 'ready' : 'warning'),
                            ]);
                        }

                        foreach ([
                            'android' => ['Google Play', $app->android_url],
                            'ios' => ['App Store', $app->ios_url],
                            'windows' => ['Windows', $app->windows_url],
                            'mac' => ['Mac', $app->mac_url],
                        ] as $key => [$label, $url]) {
                            if (filled($url)) {
                                $platforms->push([
                                    'key' => $key,
                                    'label' => $app->is_download_enabled ? $label : $label.' hidden',
                                    'state' => $app->is_download_enabled ? 'ready' : 'disabled',
                                ]);
                            }
                        }
                    ?>

                    <article class="sbal-app-row" role="listitem">
                        <div class="sbal-app-row__identity">
                            <span class="sbal-app-icon" style="--sbal-a:{{ $colors[0] }};--sbal-b:{{ $colors[1] }}" aria-hidden="true">
                                @if($app->cardImage())
                                    <img src="{{ $app->cardImage() }}" alt="" loading="lazy" onerror="this.remove()">
                                @endif
                                <em>{{ $app->icon ?: $app->initials() }}</em>
                            </span>

                            <div class="sbal-app-row__copy">
                                <div class="sbal-app-row__title-line">
                                    <h3><a href="{{ route('admin.control-room.apps.edit', $app) }}">{{ $app->name }}</a></h3>
                                    <span class="sbal-visibility {{ $app->is_active ? 'is-live' : 'is-hidden' }}">
                                        <i aria-hidden="true"></i>{{ $app->is_active ? 'Published' : 'Hidden' }}
                                    </span>
                                </div>
                                <p>{{ $app->tagline ?: 'No tagline added yet.' }}</p>
                                <div class="sbal-app-row__labels">
                                    <span class="sbal-chip">{{ $app->category ?: 'Uncategorized' }}</span>
                                    <span class="sbal-chip sbal-chip--{{ $app->status }}">{{ $statusLabels[$app->status] ?? ucfirst($app->status) }}</span>
                                    @if($app->is_featured)
                                        <span class="sbal-chip sbal-chip--featured"><span aria-hidden="true">★</span> Featured</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="sbal-app-row__platforms">
                            <span class="sbal-row-label">Available on</span>
                            <div class="sbal-platforms">
                                @forelse($platforms as $platform)
                                    <span class="sbal-platform sbal-platform--{{ $platform['key'] }} is-{{ $platform['state'] }}">
                                        <i aria-hidden="true"></i>{{ $platform['label'] }}
                                    </span>
                                @empty
                                    <span class="sbal-platform is-empty"><i aria-hidden="true"></i>No launch options yet</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="sbal-app-row__meta">
                            <div>
                                <span class="sbal-row-label">Last updated</span>
                                @if($app->updated_at)
                                    <time datetime="{{ $app->updated_at->toAtomString() }}">{{ $app->updated_at->diffForHumans() }}</time>
                                @else
                                    <span>Not recorded</span>
                                @endif
                            </div>
                            <label class="sbal-order">
                                <span class="sbal-row-label">Display order</span>
                                <input type="number" min="0" max="9999" name="order[{{ $app->id }}]" value="{{ $app->sort_order }}" form="sbal-reorder-form" aria-label="Display order for {{ $app->name }}">
                            </label>
                        </div>

                        <div class="sbal-app-row__actions">
                            <a class="sbal-row-action sbal-row-action--preview" href="{{ route('admin.control-room.apps.preview', $app) }}" target="_blank" rel="noopener">
                                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                Preview
                            </a>
                            <a class="sbal-row-action sbal-row-action--edit" href="{{ route('admin.control-room.apps.edit', $app) }}">
                                <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 20h4l11-11-4-4L4 16v4Z"/><path d="m13.5 6.5 4 4"/></svg>
                                Edit
                            </a>

                            <details class="sbal-menu" name="apps-library-actions">
                                <summary aria-label="More actions for {{ $app->name }}">
                                    <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg>
                                </summary>
                                <div class="sbal-menu__panel">
                                    <div class="sbal-menu__head">
                                        <strong>Manage app</strong>
                                        <span>{{ $app->name }}</span>
                                    </div>

                                    <form method="POST" action="{{ route('admin.control-room.apps.publish', $app) }}">
                                        @csrf @method('PATCH')
                                        <button class="sbal-menu__action" type="submit">
                                            <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 3v12M7 8l5-5 5 5"/><path d="M5 14v6h14v-6"/></svg>
                                            <span><strong>{{ $app->is_active ? 'Unpublish' : 'Publish' }}</strong><small>{{ $app->is_active ? 'Hide it from learners' : 'Make it visible to learners' }}</small></span>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.control-room.apps.featured', $app) }}">
                                        @csrf @method('PATCH')
                                        <button class="sbal-menu__action" type="submit">
                                            <svg aria-hidden="true" viewBox="0 0 24 24"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-2.9-5.6 2.9 1.1-6.2L3 9.6l6.2-.9L12 3Z"/></svg>
                                            <span><strong>{{ $app->is_featured ? 'Remove from featured' : 'Feature this app' }}</strong><small>{{ $app->is_featured ? 'Return it to normal ordering' : 'Place it at the front of the shelf' }}</small></span>
                                        </button>
                                    </form>

                                    <details class="sbal-menu__delete">
                                        <summary class="sbal-menu__action is-danger">
                                            <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/></svg>
                                            <span><strong>Delete app…</strong><small>Requires the app name</small></span>
                                        </summary>
                                        <form method="POST" action="{{ route('admin.control-room.apps.destroy', $app) }}">
                                            @csrf @method('DELETE')
                                            <label for="sbal-delete-{{ $app->id }}">Type <strong>{{ $app->name }}</strong> to confirm</label>
                                            <input id="sbal-delete-{{ $app->id }}" name="confirm_name" autocomplete="off" required>
                                            <button type="submit">Delete permanently</button>
                                        </form>
                                    </details>
                                </div>
                            </details>
                        </div>
                    </article>
                @endforeach
            </div>

            <footer class="sbal-library__footer">
                <div>
                    <strong>Arrange the app shelf</strong>
                    <span>Lower numbers appear first. Featured apps lead the list.</span>
                </div>
                <button class="sbal-button sbal-button--quiet-dark" type="submit" form="sbal-reorder-form">Save order</button>
            </footer>
        </section>

        @if($apps->hasPages())
            <nav class="sb-apps__pagination sbal-pagination" aria-label="Apps pages">
                @if($apps->onFirstPage())
                    <span class="sb-apps__page-link is-disabled" aria-disabled="true">Previous</span>
                @else
                    <a class="sb-apps__page-link" href="{{ $apps->previousPageUrl() }}" rel="prev">Previous</a>
                @endif

                <span class="sb-apps__page-current" aria-current="page">Page {{ $apps->currentPage() }}</span>

                @if($apps->hasMorePages())
                    <a class="sb-apps__page-link" href="{{ $apps->nextPageUrl() }}" rel="next">Next</a>
                @else
                    <span class="sb-apps__page-link is-disabled" aria-disabled="true">Next</span>
                @endif
            </nav>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/studybuddy-admin-apps.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-admin-apps.js')) ? filemtime(public_path('assets/js/studybuddy-admin-apps.js')) : time() }}" defer></script>
@endpush
