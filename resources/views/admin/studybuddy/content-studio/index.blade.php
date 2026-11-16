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
                <p>Edit experience pages, reusable cards, rewards copy, parent and teacher content, and support text.</p>
            </div>
            <div class="sb-control-row-actions">
                <a href="{{ route('admin.control-room.apps.index') }}">Manage Apps</a>
                <a href="{{ url('/admin/control-room') }}">Control Room</a>
            </div>
        </div>
        <div class="sb-control-stat-grid">
            <article class="purple"><span>Pages</span><strong>{{ $pages->count() }}</strong><small>Editable pages</small></article>
            <article class="blue"><span>Content items</span><strong>{{ $items->count() }}</strong><small>Cards and copy</small></article>
            <article class="cyan"><span>Workspace</span><strong>Live</strong><small>Public content</small></article>
        </div>
    </div>

    <nav class="sb-control-editor-tabs" aria-label="Content Studio tabs">
        <button type="button" class="is-active" data-sb-admin-tab="pages">Pages</button>
        <button type="button" data-sb-admin-tab="items">Cards & shortcuts</button>
    </nav>

    <section class="sb-control-panel is-active" data-sb-admin-panel="pages">
        <div class="sb-control-panel-head"><div><h2>Experience pages</h2><p>Open a page to edit its wording. Changes go live as soon as you save.</p></div></div>
        <div class="sb-control-stack">
            @foreach($pages as $page)
                <details class="sb-studio-row">
                    <summary>
                        <span class="sb-studio-row__title">{{ $page->title }}</span>
                        <span class="sb-studio-row__path">/{{ $page->slug }}</span>
                        <span class="sb-studio-row__state {{ $page->is_published ? 'is-on' : 'is-off' }}">{{ $page->is_published ? 'Live' : 'Hidden' }}</span>
                    </summary>
                <form class="sb-control-inline-editor" method="POST" action="{{ route('admin.control-room.content.pages.update', $page) }}">
                    @csrf
                    @method('PATCH')
                    <label class="sb-studio-publish"><input type="checkbox" name="is_published" value="1" @checked($page->is_published)> <span>Show this page on the website</span></label>
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
                        <details class="sb-studio-advanced wide"><summary>Advanced layout</summary><p class="sb-studio-advanced__hint">Structured page sections. Keep this valid JSON.</p><label><span class="sb-visually-hidden">Content blocks</span><textarea name="content_blocks_json" rows="8" spellcheck="false">{{ json_encode($page->content_blocks ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</textarea></label></details>
                    </div>
                    <div class="sb-control-save-row"><button class="primary" type="submit">Save page</button><a href="/{{ $page->slug }}" target="_blank" rel="noopener">Preview page</a></div>
                </form>
                </details>
            @endforeach
        </div>
    </section>

    <section class="sb-control-panel" data-sb-admin-panel="items">
        <div class="sb-control-panel-head"><div><h2>Cards and shortcuts</h2><p>Reusable blocks that appear across the website.</p></div></div>
        <div class="sb-control-stack">
            @foreach($items as $item)
                <details class="sb-studio-row">
                    <summary>
                        <span class="sb-studio-row__title">{{ $item->title }}</span>
                        <span class="sb-studio-row__path">{{ $item->page_slug }} &middot; {{ $item->item_type }}</span>
                        <span class="sb-studio-row__state {{ $item->is_active ? 'is-on' : 'is-off' }}">{{ $item->is_active ? 'Live' : 'Hidden' }}</span>
                    </summary>
                <form class="sb-control-inline-editor" method="POST" action="{{ route('admin.control-room.content.items.update', $item) }}">
                    @csrf @method('PATCH')
                    <label class="sb-studio-publish"><input type="checkbox" name="is_active" value="1" @checked($item->is_active)> <span>Show this block on the website</span></label>
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
                </details>
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
