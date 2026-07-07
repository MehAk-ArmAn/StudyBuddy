@extends('layouts.admin')

@section('title', 'Content Studio')

@php
    use Illuminate\Support\Str;
    $imageUrl = function ($path) {
        if (! filled($path)) return null;
        return Str::startsWith($path, ['http://', 'https://', '/']) ? $path : asset($path);
    };
@endphp

@section('content')
<section class="sb-control-resource" data-sb-admin-studio>
    <div class="sb-control-panel">
        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Content Studio</p>
                <h2>Editable public content</h2>
                <p>Edit premium experience pages, mini-app catalog entries, rewards copy, parent/teacher content, and support text without touching code.</p>
            </div>
            <div class="sb-control-row-actions">
                <a href="{{ url('/apps') }}" target="_blank" rel="noopener">Preview Apps</a>
                <a href="{{ url('/admin/control-room') }}">Control Room</a>
            </div>
        </div>
        <div class="sb-control-stat-grid">
            <article class="purple"><span>Pages</span><strong>{{ $pages->count() }}</strong><small>Editable pages</small></article>
            <article class="blue"><span>Content items</span><strong>{{ $items->count() }}</strong><small>Cards and copy</small></article>
            <article class="cyan"><span>Apps</span><strong>{{ $apps->count() }}</strong><small>Catalog records</small></article>
            <article class="pink"><span>Images</span><strong>Repo</strong><small>StudyBuddy-Imgs paths</small></article>
        </div>
    </div>

    <nav class="sb-control-editor-tabs" aria-label="Content Studio tabs">
        <button type="button" class="is-active" data-sb-admin-tab="pages">Pages</button>
        <button type="button" data-sb-admin-tab="items">Cards & shortcuts</button>
        <button type="button" data-sb-admin-tab="apps">Mini apps</button>
    </nav>

    <section class="sb-control-panel is-active" data-sb-admin-panel="pages">
        <div class="sb-control-panel-head"><div><h2>Editable Experience Pages</h2><p>Use valid JSON in content blocks for advanced sections.</p></div></div>
        <div class="sb-control-stack">
            @foreach($pages as $page)
                <form class="sb-control-inline-editor" method="POST" action="{{ route('admin.control-room.content.pages.update', $page) }}">
                    @csrf
                    @method('PATCH')
                    <div class="sb-control-panel-head small"><div><span class="sb-control-pill">/{{ $page->slug }}</span><h3>{{ $page->title }}</h3></div><label><input type="checkbox" name="is_published" value="1" @checked($page->is_published)> Published</label></div>
                    <div class="sb-control-form-grid">
                        <label><span>Title</span><input name="title" value="{{ old('title', $page->title) }}" required></label>
                        <label><span>Eyebrow</span><input name="eyebrow" value="{{ old('eyebrow', $page->eyebrow) }}"></label>
                        <label><span>Hero badge</span><input name="hero_badge" value="{{ old('hero_badge', $page->hero_badge) }}"></label>
                        <label><span>Sort order</span><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $page->sort_order) }}"></label>
                        <label class="wide"><span>Subtitle</span><textarea name="subtitle" rows="2">{{ old('subtitle', $page->subtitle) }}</textarea></label>
                        <label><span>Primary CTA label</span><input name="primary_cta_label" value="{{ old('primary_cta_label', $page->primary_cta_label) }}"></label>
                        <label><span>Primary CTA URL</span><input name="primary_cta_url" value="{{ old('primary_cta_url', $page->primary_cta_url) }}"></label>
                        <label><span>Secondary CTA label</span><input name="secondary_cta_label" value="{{ old('secondary_cta_label', $page->secondary_cta_label) }}"></label>
                        <label><span>Secondary CTA URL</span><input name="secondary_cta_url" value="{{ old('secondary_cta_url', $page->secondary_cta_url) }}"></label>
                        <label class="wide"><span>Content blocks JSON</span><textarea name="content_blocks_json" rows="8">{{ json_encode($page->content_blocks ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</textarea></label>
                    </div>
                    <div class="sb-control-save-row"><a href="/{{ $page->slug }}" target="_blank">Preview</a><button class="primary" type="submit">Save page</button></div>
                </form>
            @endforeach
        </div>
    </section>

    <section class="sb-control-panel" data-sb-admin-panel="items">
        <div class="sb-control-panel-head"><div><h2>Cards, shortcuts, support notes, reward rules</h2><p>Reusable cards and content items shown across StudyBuddy.</p></div></div>
        <div class="sb-control-stack">
            @foreach($items as $item)
                <form class="sb-control-inline-editor" method="POST" action="{{ route('admin.control-room.content.items.update', $item) }}">
                    @csrf @method('PATCH')
                    <div class="sb-control-panel-head small"><div><span class="sb-control-pill">{{ $item->page_slug }} • {{ $item->item_type }}</span><h3>{{ $item->title }}</h3></div><label><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> Active</label></div>
                    @if($imageUrl($item->image_path))<div class="sb-control-image-preview"><img src="{{ $imageUrl($item->image_path) }}" alt="{{ $item->title }} preview"></div>@endif
                    <div class="sb-control-form-grid">
                        <label><span>Page slug</span><input name="page_slug" value="{{ $item->page_slug }}"></label>
                        <label><span>Type</span><input name="item_type" value="{{ $item->item_type }}" required></label>
                        <label><span>Icon</span><input name="icon" value="{{ $item->icon }}"></label>
                        <label><span>Badge</span><input name="badge" value="{{ $item->badge }}"></label>
                        <label><span>Title</span><input name="title" value="{{ $item->title }}" required></label>
                        <label><span>Subtitle</span><input name="subtitle" value="{{ $item->subtitle }}"></label>
                        <label><span>Image path</span><input name="image_path" value="{{ $item->image_path }}"></label>
                        <label><span>Button label</span><input name="button_label" value="{{ $item->button_label }}"></label>
                        <label><span>Button URL</span><input name="button_url" value="{{ $item->button_url }}"></label>
                        <label><span>Status</span><input name="status" value="{{ $item->status }}"></label>
                        <label><span>Sort order</span><input type="number" min="0" name="sort_order" value="{{ $item->sort_order }}"></label>
                        <label class="wide"><span>Description</span><textarea name="description" rows="3">{{ $item->description }}</textarea></label>
                        <label class="wide"><span>Extra JSON</span><textarea name="extra_json" rows="5">{{ json_encode($item->extra ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</textarea></label>
                    </div>
                    <div class="sb-control-save-row"><button class="primary" type="submit">Save item</button></div>
                </form>
            @endforeach
        </div>
    </section>

    <section class="sb-control-panel" data-sb-admin-panel="apps">
        <div class="sb-control-panel-head"><div><h2>Mini-App Catalog</h2><p>Edit app titles, images, platform availability, web play links, download links, points, and launch status.</p></div></div>
        <div class="sb-admin-app-editor-grid">
            @foreach($apps as $app)
                <form class="sb-admin-app-editor" method="POST" action="{{ route('admin.control-room.content.apps.update', $app) }}">
                    @csrf @method('PATCH')
                    <div class="sb-admin-app-preview">
                        @if($imageUrl($app->image_path))<img src="{{ $imageUrl($app->image_path) }}" alt="{{ $app->title }} preview">@else<img src="{{ asset('assets/studybuddy-control/apps.svg') }}" alt="{{ $app->title }} preview">@endif
                        <div><span>{{ $app->app_key }} • {{ $app->launch_status }}</span><strong>{{ $app->title }}</strong><small>{{ $app->summary }}</small></div>
                    </div>
                    <div class="sb-control-form-grid compact">
                        <label><span>Title</span><input name="title" value="{{ $app->title }}" required></label>
                        <label><span>Category</span><input name="category" value="{{ $app->category }}"></label>
                        <label><span>Icon</span><input name="icon" value="{{ $app->icon }}"></label>
                        <label><span>Launch status</span><select name="launch_status">@foreach(['planned','beta','live','paused'] as $status)<option value="{{ $status }}" @selected($app->launch_status === $status)>{{ $status }}</option>@endforeach</select></label>
                        <label><span>Points reward</span><input type="number" min="0" name="points_reward" value="{{ $app->points_reward }}"></label>
                        <label><span>Sort order</span><input type="number" min="0" name="sort_order" value="{{ $app->sort_order }}"></label>
                        <label class="wide"><span>Image path</span><input name="image_path" value="{{ $app->image_path }}"></label>
                        <label class="wide"><span>Summary</span><input name="summary" value="{{ $app->summary }}"></label>
                        <label class="wide"><span>Description</span><textarea name="description" rows="3">{{ $app->description }}</textarea></label>
                        <label><span>Web play URL</span><input name="web_play_url" value="{{ $app->web_play_url }}"></label>
                        <label><span>iOS URL</span><input name="ios_url" value="{{ $app->ios_url }}"></label>
                        <label><span>Android URL</span><input name="android_url" value="{{ $app->android_url }}"></label>
                        <label><span>Windows URL</span><input name="windows_url" value="{{ $app->windows_url }}"></label>
                        <label class="wide"><span>Extra JSON</span><textarea name="extra_json" rows="5">{{ json_encode($app->extra ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</textarea></label>
                    </div>
                    <div class="sb-control-check-grid"><label><input type="checkbox" name="available_web" value="1" @checked($app->available_web)> Web play</label><label><input type="checkbox" name="available_ios" value="1" @checked($app->available_ios)> iOS</label><label><input type="checkbox" name="available_android" value="1" @checked($app->available_android)> Android</label><label><input type="checkbox" name="available_windows" value="1" @checked($app->available_windows)> Windows</label><label><input type="checkbox" name="is_active" value="1" @checked($app->is_active)> Active</label></div>
                    <div class="sb-control-save-row"><button class="primary" type="submit">Save app</button></div>
                </form>
            @endforeach
        </div>
    </section>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function (event) {
    const tab = event.target.closest('[data-sb-admin-tab]');
    if (!tab) return;
    const key = tab.dataset.sbAdminTab;
    document.querySelectorAll('[data-sb-admin-tab]').forEach(el => el.classList.toggle('is-active', el === tab));
    document.querySelectorAll('[data-sb-admin-panel]').forEach(el => el.classList.toggle('is-active', el.dataset.sbAdminPanel === key));
});
</script>
@endpush
