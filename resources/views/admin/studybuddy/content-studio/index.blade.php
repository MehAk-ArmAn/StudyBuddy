@extends('layouts.app')

@section('title', 'StudyBuddy Content Studio')

@section('content')
<main class="sb-admin-studio" data-sb-admin-studio>
    <section class="sb-admin-hero">
        <div>
            <p class="sbx-kicker">Admin Panel</p>
            <h1>StudyBuddy Content Studio</h1>
            <p>Edit the premium experience pages, mini-app catalog, rewards content, parent/teacher content, and support copy without touching code.</p>
        </div>
        <div class="sb-admin-hero__meta">
            <span>{{ $pages->count() }} pages</span>
            <span>{{ $items->count() }} content items</span>
            <span>{{ $apps->count() }} apps</span>
        </div>
    </section>

    @if(session('status'))
        <div class="sb-admin-alert">{{ session('status') }}</div>
    @endif

    <nav class="sb-admin-tabs" aria-label="Content Studio tabs">
        <button type="button" class="is-active" data-sb-admin-tab="pages">Pages</button>
        <button type="button" data-sb-admin-tab="items">Cards & shortcuts</button>
        <button type="button" data-sb-admin-tab="apps">Mini apps</button>
        <a href="/learning-hub" target="_blank">Preview Learning Hub ↗</a>
    </nav>

    <section class="sb-admin-panel is-active" data-sb-admin-panel="pages">
        <div class="sb-admin-panel__head">
            <h2>Editable Experience Pages</h2>
            <p>These control the public content pages. Use valid JSON in content blocks for advanced sections.</p>
        </div>

        @foreach($pages as $page)
            <form class="sb-admin-card" method="POST" action="{{ route('studybuddy.admin.content.pages.update', $page) }}">
                @csrf
                @method('PATCH')
                <div class="sb-admin-card__head">
                    <div>
                        <span class="sbx-pill">/{{ $page->slug }}</span>
                        <h3>{{ $page->title }}</h3>
                    </div>
                    <label class="sb-admin-toggle"><input type="checkbox" name="is_published" value="1" @checked($page->is_published)> Published</label>
                </div>

                <div class="sb-admin-grid">
                    <label>Title <input name="title" value="{{ old('title', $page->title) }}" required></label>
                    <label>Eyebrow <input name="eyebrow" value="{{ old('eyebrow', $page->eyebrow) }}"></label>
                    <label>Hero badge <input name="hero_badge" value="{{ old('hero_badge', $page->hero_badge) }}"></label>
                    <label>Sort order <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $page->sort_order) }}"></label>
                </div>

                <label>Subtitle <textarea name="subtitle" rows="2">{{ old('subtitle', $page->subtitle) }}</textarea></label>

                <div class="sb-admin-grid">
                    <label>Primary CTA label <input name="primary_cta_label" value="{{ old('primary_cta_label', $page->primary_cta_label) }}"></label>
                    <label>Primary CTA URL <input name="primary_cta_url" value="{{ old('primary_cta_url', $page->primary_cta_url) }}"></label>
                    <label>Secondary CTA label <input name="secondary_cta_label" value="{{ old('secondary_cta_label', $page->secondary_cta_label) }}"></label>
                    <label>Secondary CTA URL <input name="secondary_cta_url" value="{{ old('secondary_cta_url', $page->secondary_cta_url) }}"></label>
                </div>

                <details class="sb-admin-json">
                    <summary>Advanced content blocks JSON</summary>
                    <textarea name="content_blocks_json" rows="14">{{ json_encode($page->content_blocks ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</textarea>
                </details>

                <div class="sb-admin-actions">
                    <a href="/{{ $page->slug }}" target="_blank">Preview page ↗</a>
                    <button type="submit">Save page</button>
                </div>
            </form>
        @endforeach
    </section>

    <section class="sb-admin-panel" data-sb-admin-panel="items">
        <div class="sb-admin-panel__head">
            <h2>Cards, shortcuts, support notes, reward rules</h2>
            <p>These are reusable page cards and small content items shown across experience pages.</p>
        </div>

        @foreach($items as $item)
            <form class="sb-admin-card" method="POST" action="{{ route('studybuddy.admin.content.items.update', $item) }}">
                @csrf
                @method('PATCH')
                <div class="sb-admin-card__head">
                    <div><span class="sbx-pill">{{ $item->page_slug }} • {{ $item->item_type }}</span><h3>{{ $item->icon }} {{ $item->title }}</h3></div>
                    <label class="sb-admin-toggle"><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> Active</label>
                </div>
                <div class="sb-admin-grid">
                    <label>Page slug <input name="page_slug" value="{{ $item->page_slug }}"></label>
                    <label>Type <input name="item_type" value="{{ $item->item_type }}" required></label>
                    <label>Icon <input name="icon" value="{{ $item->icon }}"></label>
                    <label>Badge <input name="badge" value="{{ $item->badge }}"></label>
                    <label>Title <input name="title" value="{{ $item->title }}" required></label>
                    <label>Subtitle <input name="subtitle" value="{{ $item->subtitle }}"></label>
                    <label>Button label <input name="button_label" value="{{ $item->button_label }}"></label>
                    <label>Button URL <input name="button_url" value="{{ $item->button_url }}"></label>
                    <label>Status <input name="status" value="{{ $item->status }}"></label>
                    <label>Sort order <input type="number" min="0" name="sort_order" value="{{ $item->sort_order }}"></label>
                </div>
                <label>Description <textarea name="description" rows="3">{{ $item->description }}</textarea></label>
                <details class="sb-admin-json"><summary>Extra JSON</summary><textarea name="extra_json" rows="6">{{ json_encode($item->extra ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</textarea></details>
                <div class="sb-admin-actions"><button type="submit">Save item</button></div>
            </form>
        @endforeach
    </section>

    <section class="sb-admin-panel" data-sb-admin-panel="apps">
        <div class="sb-admin-panel__head">
            <h2>Mini-App Catalog</h2>
            <p>Edit app titles, platform availability, web play links, download links, points, and launch status. Actual app hosting comes later.</p>
        </div>

        @foreach($apps as $app)
            <form class="sb-admin-card" method="POST" action="{{ route('studybuddy.admin.content.apps.update', $app) }}">
                @csrf
                @method('PATCH')
                <div class="sb-admin-card__head">
                    <div><span class="sbx-pill">{{ $app->app_key }} • {{ $app->launch_status }}</span><h3>{{ $app->icon }} {{ $app->title }}</h3></div>
                    <label class="sb-admin-toggle"><input type="checkbox" name="is_active" value="1" @checked($app->is_active)> Active</label>
                </div>
                <div class="sb-admin-grid">
                    <label>Title <input name="title" value="{{ $app->title }}" required></label>
                    <label>Category <input name="category" value="{{ $app->category }}"></label>
                    <label>Icon <input name="icon" value="{{ $app->icon }}"></label>
                    <label>Launch status <select name="launch_status"><option @selected($app->launch_status==='planned')>planned</option><option @selected($app->launch_status==='beta')>beta</option><option @selected($app->launch_status==='live')>live</option><option @selected($app->launch_status==='paused')>paused</option></select></label>
                    <label>Points reward <input type="number" min="0" name="points_reward" value="{{ $app->points_reward }}"></label>
                    <label>Sort order <input type="number" min="0" name="sort_order" value="{{ $app->sort_order }}"></label>
                </div>
                <label>Summary <input name="summary" value="{{ $app->summary }}"></label>
                <label>Description <textarea name="description" rows="3">{{ $app->description }}</textarea></label>
                <div class="sb-admin-platforms">
                    <label><input type="checkbox" name="available_web" value="1" @checked($app->available_web)> Web play</label>
                    <label><input type="checkbox" name="available_ios" value="1" @checked($app->available_ios)> iOS</label>
                    <label><input type="checkbox" name="available_android" value="1" @checked($app->available_android)> Android</label>
                    <label><input type="checkbox" name="available_windows" value="1" @checked($app->available_windows)> Windows</label>
                </div>
                <div class="sb-admin-grid">
                    <label>Web play URL <input name="web_play_url" value="{{ $app->web_play_url }}"></label>
                    <label>iOS URL <input name="ios_url" value="{{ $app->ios_url }}"></label>
                    <label>Android URL <input name="android_url" value="{{ $app->android_url }}"></label>
                    <label>Windows URL <input name="windows_url" value="{{ $app->windows_url }}"></label>
                </div>
                <details class="sb-admin-json"><summary>Extra JSON</summary><textarea name="extra_json" rows="6">{{ json_encode($app->extra ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</textarea></details>
                <div class="sb-admin-actions"><button type="submit">Save app</button></div>
            </form>
        @endforeach
    </section>
</main>
@endsection
