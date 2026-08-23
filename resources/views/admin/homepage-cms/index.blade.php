@extends('layouts.admin')

@section('title', 'Homepage')

@push('styles-late')
<link
    rel="stylesheet"
    href="{{ asset('assets/css/studybuddy-admin-homepage-cms.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-admin-homepage-cms.css')) ? filemtime(public_path('assets/css/studybuddy-admin-homepage-cms.css')) : time() }}"
>
@endpush

@php
    // Only these section keys are read by the homepage. Anything else is an
    // old row that still exists but renders nowhere, so it is labelled rather
    // than quietly presented as editable live content.
    $liveKeys = ['hero', 'what_we_do', 'apps_preview', 'apps', 'page_paths', 'why', 'trust', 'cta'];

    // The homepage lays these out in a fixed order by key, so list them the
    // way they actually appear on the site rather than by sort_order.
    $sections = $sections->sortBy(function ($section) use ($liveKeys) {
        $position = array_search((string) $section->section_key, $liveKeys, true);

        return $position === false ? 900 + (int) $section->id : $position;
    })->values();

    $resolveImage = function (?string $path): ?string {
        if (blank($path)) {
            return null;
        }

        return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/'])
            ? $path
            : asset($path);
    };
@endphp

@section('content')
<section class="sb-homepage-cms" data-admin-skip-unified>

    <div class="cms-intro">
        <div>
            <p class="cms-kicker">Homepage</p>
            <h2>Edit what the homepage says</h2>
            <p>
                Change the wording, buttons, images and order of each section.
                Saving a section updates the live homepage straight away.
            </p>
        </div>

        <a class="cms-btn cms-btn--quiet" href="{{ url('/') }}" target="_blank" rel="noopener">
            View homepage
        </a>
    </div>

    @forelse($sections as $section)
        @php
            $isLive = in_array((string) $section->section_key, $liveKeys, true);
            $sectionImage = $resolveImage($section->image_path);
            $sectionItems = $items[$section->id] ?? collect();
        @endphp

        <article class="cms-panel">
            <div class="cms-panel__head">
                <div>
                    <p class="cms-kicker">
                        {{ $section->section_key ? \Illuminate\Support\Str::headline($section->section_key) : 'Unassigned section' }}
                    </p>
                    <h3>{{ $section->title ?: 'Untitled section' }}</h3>
                </div>

                <div>
                    @if(! $isLive)
                        <span class="cms-pill cms-pill--unused">Not used on the homepage</span>
                    @elseif($section->is_enabled)
                        <span class="cms-pill cms-pill--live">Showing</span>
                    @else
                        <span class="cms-pill cms-pill--off">Hidden</span>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('admin.control-room.homepage-cms.sections.update', $section->id) }}">
                @csrf
                @method('PATCH')

                <div class="cms-panel__body">
                    <div class="cms-fields">
                        <label class="cms-field">
                            <span>Small label above the heading</span>
                            <input type="text" name="eyebrow" value="{{ $section->eyebrow }}" maxlength="190">
                        </label>

                        <label class="cms-field">
                            <span>Heading</span>
                            <input type="text" name="title" value="{{ $section->title }}" maxlength="255" required>
                        </label>

                        <label class="cms-field cms-field--wide">
                            <span>Short line under the heading</span>
                            <textarea name="subtitle" rows="2" maxlength="2000">{{ $section->subtitle }}</textarea>
                        </label>

                        <label class="cms-field cms-field--wide">
                            <span>Longer paragraph</span>
                            <textarea name="body" rows="4" maxlength="12000">{{ $section->body ?? (property_exists($section, 'content') ? $section->content : '') }}</textarea>
                            <small>Leave empty if this section does not need one.</small>
                        </label>

                        <div class="cms-image">
                            <span class="cms-image__thumb">
                                @if($sectionImage)
                                    <img src="{{ $sectionImage }}" alt="Current image for {{ $section->title }}" loading="lazy" decoding="async">
                                @else
                                    <span>No image</span>
                                @endif
                            </span>

                            <label class="cms-field">
                                <span>Image path</span>
                                <input type="text" name="image_path" value="{{ $section->image_path }}" maxlength="1000">
                                <small>Pick one from the image library at the bottom of this page, or paste a path.</small>
                            </label>
                        </div>

                        <fieldset class="cms-group">
                            <legend>Buttons</legend>

                            <div class="cms-group__grid">
                                <label class="cms-field">
                                    <span>Main button text</span>
                                    <input type="text" name="button_label" value="{{ $section->button_label }}" maxlength="190">
                                </label>

                                <label class="cms-field">
                                    <span>Main button link</span>
                                    <input type="text" name="button_url" value="{{ $section->button_url }}" maxlength="1000">
                                </label>

                                <label class="cms-field">
                                    <span>Second button text</span>
                                    <input type="text" name="secondary_button_label" value="{{ $section->secondary_button_label }}" maxlength="190">
                                </label>

                                <label class="cms-field">
                                    <span>Second button link</span>
                                    <input type="text" name="secondary_button_url" value="{{ $section->secondary_button_url }}" maxlength="1000">
                                </label>
                            </div>
                        </fieldset>

                        <label class="cms-field">
                            <span>Order</span>
                            <input type="number" name="sort_order" value="{{ $section->sort_order }}" min="0" max="10000">
                            <small>The homepage arranges sections in a fixed layout, so this only sorts related lists.</small>
                        </label>

                        <label class="cms-toggle">
                            <input type="checkbox" name="is_enabled" value="1" @checked($section->is_enabled ?? true)>
                            <span>
                                <strong>Show this section</strong>
                                <small>Turn off to hide it without deleting anything.</small>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="cms-panel__foot">
                    <p>Changes apply to the live homepage as soon as you save.</p>
                    <button class="cms-btn cms-btn--primary" type="submit">Save section</button>
                </div>
            </form>

            @if($sectionItems->isNotEmpty())
                <div class="cms-panel__body cms-cards">
                    <h4>Cards in this section</h4>
                    <p>Each card is saved on its own.</p>

                    @foreach($sectionItems as $item)
                        <details class="cms-card">
                            <summary>
                                <span>{{ $item->title ?: 'Untitled card' }}</span>
                                @if($item->is_enabled)
                                    <span class="cms-pill cms-pill--live">Showing</span>
                                @else
                                    <span class="cms-pill cms-pill--off">Hidden</span>
                                @endif
                            </summary>

                            <div class="cms-card__body">
                                <form method="POST" action="{{ route('admin.control-room.homepage-cms.items.update', $item->id) }}">
                                    @csrf
                                    @method('PATCH')

                                    <div class="cms-fields">
                                        <label class="cms-field">
                                            <span>Badge or icon text</span>
                                            <input type="text" name="badge_text" value="{{ $item->badge_text ?: $item->icon_path }}" maxlength="120">
                                        </label>

                                        <label class="cms-field">
                                            <span>Card title</span>
                                            <input type="text" name="title" value="{{ $item->title }}" maxlength="255" required>
                                        </label>

                                        <label class="cms-field cms-field--wide">
                                            <span>Short line</span>
                                            <input type="text" name="subtitle" value="{{ $item->subtitle }}" maxlength="1200">
                                        </label>

                                        <label class="cms-field cms-field--wide">
                                            <span>Card text</span>
                                            <textarea name="body" rows="3" maxlength="6000">{{ $item->body ?? (property_exists($item, 'description') ? $item->description : '') }}</textarea>
                                        </label>

                                        <div class="cms-image">
                                            <span class="cms-image__thumb">
                                                @if($cardImage = $resolveImage($item->image_path))
                                                    <img src="{{ $cardImage }}" alt="Current image for {{ $item->title }}" loading="lazy" decoding="async">
                                                @else
                                                    <span>No image</span>
                                                @endif
                                            </span>

                                            <label class="cms-field">
                                                <span>Image path</span>
                                                <input type="text" name="image_path" value="{{ $item->image_path }}" maxlength="1000">
                                            </label>
                                        </div>

                                        <fieldset class="cms-group">
                                            <legend>Button</legend>

                                            <div class="cms-group__grid">
                                                <label class="cms-field">
                                                    <span>Button text</span>
                                                    <input type="text" name="button_label" value="{{ $item->button_label }}" maxlength="190">
                                                </label>

                                                <label class="cms-field">
                                                    <span>Button link</span>
                                                    <input type="text" name="button_url" value="{{ $item->button_url }}" maxlength="1000">
                                                </label>
                                            </div>
                                        </fieldset>

                                        <label class="cms-field">
                                            <span>Position</span>
                                            <input type="number" name="sort_order" value="{{ $item->sort_order }}" min="0" max="10000">
                                        </label>

                                        <label class="cms-toggle">
                                            <input type="checkbox" name="is_enabled" value="1" @checked($item->is_enabled ?? true)>
                                            <span>
                                                <strong>Show this card</strong>
                                                <small>Turn off to hide it without deleting anything.</small>
                                            </span>
                                        </label>
                                    </div>

                                    <div class="cms-card__actions">
                                        <button class="cms-btn cms-btn--primary" type="submit">Save card</button>
                                    </div>
                                </form>
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif
        </article>
    @empty
        <div class="cms-panel">
            <div class="cms-panel__body">
                <h3>No homepage sections yet</h3>
                <p class="cms-empty">Run the content sync script, then reload this page.</p>
            </div>
        </div>
    @endforelse

    <article class="cms-panel">
        <div class="cms-panel__head">
            <div>
                <p class="cms-kicker">Image library</p>
                <h3>Images you can use</h3>
            </div>
        </div>

        <div class="cms-panel__body">
            <p class="cms-field"><small>Select one to copy its path, then paste it into an image field above.</small></p>

            <div class="cms-library">
                @foreach($visuals as $visual)
                    <button type="button" data-copy-path="{{ $visual }}">
                        <img src="{{ $resolveImage($visual) }}" alt="" loading="lazy" decoding="async">
                        <span>{{ $visual }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </article>
</section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/studybuddy-homepage-cms.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-homepage-cms.js')) ? filemtime(public_path('assets/js/studybuddy-homepage-cms.js')) : time() }}" defer></script>
@endpush
