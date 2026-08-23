@extends('layouts.admin')

@section('title', 'Homepage CMS')

@section('content')
<section class="sb-homepage-cms">
    <div class="sb-control-panel health-hero">
        <div>
            <p class="sb-control-kicker">Homepage CMS</p>
            <h2>Edit the homepage visually</h2>
            <p>Update the headings, wording, buttons, order and images. Everything you save here appears on the homepage straight away.</p>
        </div>
        <div class="cms-preview-stack">
            @foreach($visuals as $visual)
                <img src="{{ \Illuminate\Support\Str::startsWith($visual, ['http://','https://','/']) ? $visual : asset($visual) }}" alt="" aria-hidden="true" loading="lazy">
            @endforeach
        </div>
    </div>

    <div class="cms-visual-bank sb-control-panel">
        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Visual Bank</p>
                <h2>Working homepage image paths</h2>
            </div>
        </div>

        <div class="cms-visual-grid">
            @foreach($visuals as $visual)
                <button type="button" data-copy-path="{{ $visual }}">
                    <img src="{{ \Illuminate\Support\Str::startsWith($visual, ['http://','https://','/']) ? $visual : asset($visual) }}" alt="" aria-hidden="true" loading="lazy">
                    <span>{{ $visual }}</span>
                </button>
            @endforeach
        </div>
    </div>

    @forelse($sections as $section)
        <div class="sb-control-panel cms-section-card">
            <form method="POST" action="{{ route('admin.control-room.homepage-cms.sections.update', $section->id) }}">
                @csrf
                @method('PATCH')

                <div class="sb-control-panel-head wide">
                    <div>
                        <p class="sb-control-kicker">Section {{ $section->sort_order ?? $loop->iteration }}</p>
                        <h2>{{ $section->title }}</h2>
                    </div>
                    <div class="sb-control-row-actions">
                        <a href="{{ url('/') }}" target="_blank" rel="noopener">Preview</a>
                        <button class="primary" type="submit">Save Section</button>
                    </div>
                </div>

                <div class="cms-editor-grid">
                    <label><span>Eyebrow</span><input name="eyebrow" value="{{ $section->eyebrow }}"></label>
                    <label><span>Title</span><input name="title" value="{{ $section->title }}" required></label>
                    <label class="wide"><span>Subtitle</span><textarea name="subtitle" rows="3">{{ $section->subtitle }}</textarea></label>
                    <label class="wide"><span>Body</span><textarea name="body" rows="5">{{ $section->body ?? (property_exists($section, 'content') ? $section->content : '') }}</textarea></label>
                    <label class="wide"><span>Image</span><input name="image_path" value="{{ $section->image_path }}"></label>
                    <label><span>Primary Button</span><input name="button_label" value="{{ $section->button_label }}"></label>
                    <label><span>Primary URL</span><input name="button_url" value="{{ $section->button_url }}"></label>
                    <label><span>Secondary Button</span><input name="secondary_button_label" value="{{ $section->secondary_button_label }}"></label>
                    <label><span>Secondary URL</span><input name="secondary_button_url" value="{{ $section->secondary_button_url }}"></label>
                    <label><span>Sort Order</span><input type="number" name="sort_order" value="{{ $section->sort_order }}"></label>
                    <label class="cms-check"><input type="checkbox" name="is_enabled" value="1" @checked($section->is_enabled ?? true)> <span>Enabled</span></label>
                </div>
            </form>

            @if(($section->image_path ?? null))
                <div class="cms-image-preview">
                    <img src="{{ \Illuminate\Support\Str::startsWith($section->image_path, ['http://','https://','/']) ? $section->image_path : asset($section->image_path) }}" alt="Current image for {{ $section->title }}" loading="lazy">
                </div>
            @endif

            <div class="cms-items">
                <h3>Cards inside this section</h3>

                @foreach(($items[$section->id] ?? collect()) as $item)
                    <form method="POST" action="{{ route('admin.control-room.homepage-cms.items.update', $item->id) }}" class="cms-item-form">
                        @csrf
                        @method('PATCH')

                        <label><span>Icon / Badge Text</span><input name="badge_text" value="{{ $item->badge_text ?: $item->icon_path }}"></label>
                        <label><span>Title</span><input name="title" value="{{ $item->title }}" required></label>
                        <label class="wide"><span>Subtitle</span><input name="subtitle" value="{{ $item->subtitle }}"></label>
                        <label class="wide"><span>Body</span><textarea name="body" rows="3">{{ $item->body ?? (property_exists($item, 'description') ? $item->description : '') }}</textarea></label>
                        <label class="wide"><span>Image</span><input name="image_path" value="{{ $item->image_path }}"></label>
                        <label><span>Button</span><input name="button_label" value="{{ $item->button_label }}"></label>
                        <label><span>URL</span><input name="button_url" value="{{ $item->button_url }}"></label>
                        <label><span>Sort</span><input type="number" name="sort_order" value="{{ $item->sort_order }}"></label>
                        <label class="cms-check"><input type="checkbox" name="is_enabled" value="1" @checked($item->is_enabled ?? true)> <span>Enabled</span></label>
                        <button type="submit">Save Card</button>
                    </form>
                @endforeach
            </div>
        </div>
    @empty
        <div class="sb-control-panel">
            <h2>No homepage sections found</h2>
            <p>Run the content sync script first, then refresh this page.</p>
        </div>
    @endforelse
</section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/studybuddy-homepage-cms.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-homepage-cms.js')) ? filemtime(public_path('assets/js/studybuddy-homepage-cms.js')) : time() }}" defer></script>
@endpush
