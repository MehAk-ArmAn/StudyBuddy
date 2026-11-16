@extends('layouts.admin')

@php
    $isNew = ! $app->exists;
    $colors = $app->accentColors();
    $selectedRoles = old('audience_roles', $app->audience_roles ?: array_keys($roles));
    $cardImage = old('image_url', $app->image_url);
    $coverImage = old('hero_image', $app->hero_image);
    $externalLaunchUrl = old('web_play_url', $app->usesExternalBrowserUrl() ? $app->web_play_url : '');
    $advancedHasErrors = $errors->hasAny(['slug', 'status', 'sort_order', 'is_featured']);
    $browserHasErrors = $errors->hasAny(['is_web_enabled', 'web_app_zip', 'web_play_url', 'remove_web_app']);
    $storeHasErrors = $errors->hasAny(['is_download_enabled', 'android_url', 'android_package_id', 'ios_url', 'windows_url', 'mac_url', 'support_url']);
    $googleHasErrors = $errors->hasAny(['android_url', 'android_package_id']);
    $appStoreHasErrors = $errors->has('ios_url');
    $downloadsHaveErrors = $errors->hasAny(['windows_url', 'mac_url', 'support_url']);
    $webEnabled = (bool) old('is_web_enabled', $app->is_web_enabled);
    $storeEnabled = (bool) old('is_download_enabled', $app->is_download_enabled);
    $googleConfigured = filled(old('android_url', $app->android_url));
    $appStoreConfigured = filled(old('ios_url', $app->ios_url));
    $otherDownloadsConfigured = filled(old('windows_url', $app->windows_url)) || filled(old('mac_url', $app->mac_url)) || filled(old('support_url', $app->support_url));
    $hasSavedDestination = $app->exists && $app->isAvailable();
    $editorStatus = $isNew ? 'New draft' : ($app->is_active ? 'Published' : 'Private draft');
    $platformSummary = collect([
        $webEnabled ? 'Browser' : null,
        $googleConfigured ? 'Google Play' : null,
        $appStoreConfigured ? 'App Store' : null,
    ])->filter()->values();
    $readinessItems = [
        ['label' => 'Name and category', 'ready' => filled(old('name', $app->name)) && filled(old('category', $app->category))],
        ['label' => 'A clear app description', 'ready' => filled(old('preview_text', $app->preview_text)) || filled(old('description', $app->description))],
        ['label' => 'Card artwork', 'ready' => filled($cardImage)],
        ['label' => 'A browser or store destination', 'ready' => $hasSavedDestination || filled($externalLaunchUrl) || filled(old('android_url', $app->android_url)) || filled(old('ios_url', $app->ios_url))],
    ];
@endphp

@section('title', $isNew ? 'Add app' : 'Edit '.$app->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-admin-apps.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-admin-apps.css')) ? filemtime(public_path('assets/css/studybuddy-admin-apps.css')) : time() }}">
@endpush

@section('content')
<div class="sb-apps sb-app-editor" data-admin-skip-unified data-app-editor data-editor-mode="{{ $isNew ? 'create' : 'edit' }}" data-has-validation-errors="{{ $errors->any() ? 'true' : 'false' }}">
    <nav class="sb-apps__crumbs" aria-label="Breadcrumb">
        <a href="{{ route('admin.control-room.index') }}">Control Room</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('admin.control-room.apps.index') }}">Apps Library</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">{{ $isNew ? 'Add app' : $app->name }}</span>
    </nav>

    <header class="sb-product-header">
        <div class="sb-product-header__identity">
            <a class="sb-product-header__back" href="{{ route('admin.control-room.apps.index') }}" aria-label="Back to Apps Library">
                <span aria-hidden="true">←</span>
            </a>
            <div>
                <div class="sb-product-header__meta">
                    <span class="sb-status-pill {{ $app->is_active ? 'is-live' : 'is-draft' }}">{{ $editorStatus }}</span>
                    @foreach($platformSummary as $platform)
                        <span class="sb-platform-pill">{{ $platform }}</span>
                    @endforeach
                </div>
                <h1>{{ $isNew ? 'Add a learning app' : $app->name }}</h1>
                <p>
                @if($isNew)
                    Create a polished StudyBuddy experience, then preview it before publishing.
                @elseif($app->is_active)
                    Live at <a href="{{ route('studybuddy.apps.show', $app->slug) }}" target="_blank" rel="noopener">/apps/{{ $app->slug }}</a>
                @else
                    This app is private and only visible in Admin preview.
                @endif
                </p>
            </div>
        </div>

    </header>

    @include('admin.apps.partials.flash')

    <form id="app-editor-form" class="sb-form sb-app-editor__form" method="POST" enctype="multipart/form-data"
          action="{{ $isNew ? route('admin.control-room.apps.store') : route('admin.control-room.apps.update', $app) }}"
          data-app-form>
        @csrf
        @unless($isNew) @method('PUT') @endunless
        <button class="sb-visually-hidden" type="submit" name="save_action" value="{{ $app->is_active ? 'publish' : 'draft' }}" tabindex="-1" aria-hidden="true">
            {{ $app->is_active ? 'Save changes' : 'Save draft' }}
        </button>

        <div class="sb-app-editor__workspace">
            <aside class="sb-editor-rail" aria-label="App editor progress" data-editor-nav>
                <p class="sb-editor-rail__eyebrow">App editor</p>
                <nav aria-label="App editor sections">
                    @foreach([
                        ['01', 'Basics', 'Identity and summary', 'basic-info'],
                        ['02', 'Branding', 'Artwork and fallback', 'branding'],
                        ['03', 'Learning', 'Experience and audience', 'learning-details'],
                        ['04', 'Platforms', 'Browser and stores', 'availability'],
                        ['05', 'Review', 'Preview and publish', 'preview-publish'],
                    ] as [$number, $label, $hint, $target])
                        <a href="#{{ $target }}" data-section-link data-section-target="{{ $target }}">
                            <span>{{ $number }}</span>
                            <span><strong>{{ $label }}</strong><small>{{ $hint }}</small></span>
                            <i aria-hidden="true"></i>
                        </a>
                    @endforeach
                </nav>
                <div class="sb-editor-rail__help">
                    <strong>Safe to take your time</strong>
                    <p>Save a draft at any point. Nothing becomes public until you publish it.</p>
                </div>
            </aside>

            <div class="sb-app-editor__main">
                <div class="sb-save-bar" role="region" aria-label="Save app" data-save-bar>
                    <div class="sb-save-bar__state">
                        <span class="sb-save-bar__dot {{ $app->is_active ? 'is-live' : '' }}" aria-hidden="true"></span>
                        <span>
                            <strong>{{ $editorStatus }}</strong>
                            <small data-save-state>{{ $errors->any() ? 'Review the highlighted fields' : 'No unsaved changes' }}</small>
                        </span>
                    </div>
                    <div class="sb-save-bar__actions">
                        @unless($isNew)
                            <a class="sb-btn sb-btn--ghost sb-save-bar__preview" href="{{ route('admin.control-room.apps.preview', $app) }}" target="_blank" rel="noopener">Preview</a>
                        @endunless
                        <button class="sb-btn {{ $app->is_active ? 'sb-btn--warning' : 'sb-btn--secondary' }}" type="submit" name="save_action" value="draft">
                            {{ $app->is_active ? 'Save & unpublish' : 'Save draft' }}
                        </button>
                        <button class="sb-btn sb-btn--primary" type="submit" name="save_action" value="publish">
                            {{ $app->is_active ? 'Save changes' : ($isNew ? 'Create & publish' : 'Save & publish') }}
                        </button>
                    </div>
                </div>

        <section class="sb-card" id="basic-info" data-editor-section="basics">
            <div class="sb-card__head sb-card__head--numbered">
                <span>1</span>
                <div><h2>Basic info</h2><p>The name and short introduction people see first.</p></div>
            </div>

            <div class="sb-grid">
                <label class="sb-field">
                    <span>App name <em>required</em></span>
                    <input name="name" value="{{ old('name', $app->name) }}" required maxlength="160" autocomplete="off" data-app-name @error('name') aria-invalid="true" @enderror>
                    <small>Shown on the app card and detail page.</small>
                    @error('name')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>

                <label class="sb-field">
                    <span>Category <em>required</em></span>
                    <input name="category" list="sb-categories" value="{{ old('category', $app->category) }}" required maxlength="120"
                           placeholder="For example, Maths or Reading" @error('category') aria-invalid="true" @enderror>
                    <datalist id="sb-categories">
                        @foreach($categorySuggestions as $suggestion)<option value="{{ $suggestion }}"></option>@endforeach
                    </datalist>
                    <small>Helps families filter the Apps page.</small>
                    @error('category')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>

                <label class="sb-field sb-field--wide">
                    <span>One-line summary</span>
                    <input name="tagline" value="{{ old('tagline', $app->tagline) }}" maxlength="240"
                           placeholder="A short, specific reason to try the app" @error('tagline') aria-invalid="true" @enderror>
                    <small>Appears directly under the app name.</small>
                    @error('tagline')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>
            </div>
        </section>

        <section class="sb-card" id="branding" data-editor-section="branding">
            <div class="sb-card__head sb-card__head--numbered">
                <span>2</span>
                <div><h2>Branding &amp; artwork</h2><p>Upload polished artwork and preview it before saving. JPG, PNG, WEBP or GIF; up to 4 MB.</p></div>
            </div>

            <div class="sb-branding-intro">
                <label class="sb-field sb-field--compact">
                    <span>Fallback icon</span>
                    <input name="icon" value="{{ old('icon', $app->icon) }}" maxlength="24" placeholder="🧮" @error('icon') aria-invalid="true" @enderror>
                    <small>One emoji, used only when artwork is unavailable.</small>
                    @error('icon')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>
                <p><strong>Artwork tip</strong> Keep titles and important characters away from the edges so both desktop and mobile crops feel balanced.</p>
            </div>

            <div class="sb-upload-grid">
                @foreach([
                    ['image_url', 'Card artwork', $cardImage, '4:3', '800 × 600 or larger', 'Used in the Apps Library and homepage app shelf.'],
                    ['hero_image', 'Feature artwork', $coverImage, '4:3', '1200 × 900 or larger', 'Used on the app detail page and falls back to card artwork.'],
                ] as [$field, $label, $current, $ratio, $sizeHint, $usageHint])
                    <article class="sb-upload-card {{ $errors->has($field.'_file') || $errors->has($field) ? 'has-error' : '' }}" data-upload-card data-upload-dropzone data-artwork-card="{{ $field }}">
                        <div class="sb-upload-card__preview {{ $field === 'hero_image' ? 'is-feature' : '' }}"
                             style="--sb-a:{{ $colors[0] }};--sb-b:{{ $colors[1] }}" data-artwork-preview>
                            <img src="{{ $current ?: '' }}" alt="{{ $label }} preview" data-artwork-preview-image @if(! $current) hidden @endif>
                            <span class="sb-upload-card__fallback" data-artwork-fallback @if($current) hidden @endif>
                                <em>{{ $app->icon ?: $app->initials() }}</em>
                                <small>Preview</small>
                            </span>
                            <span class="sb-upload-card__ratio">{{ $ratio }}</span>
                        </div>

                        <div class="sb-upload-card__body">
                            <div>
                                <h3>{{ $label }}</h3>
                                <p>{{ $usageHint }}</p>
                            </div>

                            <label class="sb-upload-card__button">
                                <input type="file" name="{{ $field }}_file" accept="image/png,image/jpeg,image/webp,image/gif"
                                       data-artwork-input @error($field.'_file') aria-invalid="true" @enderror>
                                <span>{{ $current ? 'Replace image' : 'Upload image' }}</span>
                            </label>
                            <button class="sb-upload-card__clear" type="button" data-upload-clear hidden>Clear new selection</button>
                            <small class="sb-upload-card__file" data-upload-file-name>PNG, JPG, WEBP or GIF · {{ $sizeHint }} · max 4 MB</small>
                            @error($field.'_file')<strong class="sb-error">{{ $message }}</strong>@enderror

                            <details class="sb-inline-advanced" @if($errors->has($field)) open @endif>
                                <summary>Use an existing image address</summary>
                                <label class="sb-media__path">
                                    <span>Root-relative path or secure URL</span>
                                    <input name="{{ $field }}" value="{{ $current }}" maxlength="500" placeholder="/assets/images/apps/example.webp"
                                           @error($field) aria-invalid="true" @enderror>
                                </label>
                                @error($field)<strong class="sb-error">{{ $message }}</strong>@enderror
                            </details>

                            @if($current)
                                <label class="sb-remove-media">
                                    <input type="checkbox" name="remove_{{ $field }}" value="1" @checked(old('remove_'.$field))>
                                    <span>Remove current {{ strtolower($label) }}</span>
                                </label>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="sb-card" id="learning-details" data-editor-section="learning">
            <div class="sb-card__head sb-card__head--numbered">
                <span>3</span>
                <div><h2>Learning details</h2><p>Explain what the app teaches, what a session feels like and who it is for.</p></div>
            </div>

            <div class="sb-editor-subsection">
                <header><h3>Public page copy</h3><p>Start concise, then add depth only where it helps families understand the app.</p></header>
                <div class="sb-grid">
                <label class="sb-field sb-field--wide">
                    <span>Short description</span>
                    <textarea name="description" rows="3" maxlength="4000" @error('description') aria-invalid="true" @enderror>{{ old('description', $app->description) }}</textarea>
                    <small>Shown near the top of the detail page and used on cards when the introduction below is blank.</small>
                    @error('description')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>

                <label class="sb-field sb-field--wide">
                    <span>Card intro &amp; what to expect</span>
                    <textarea name="preview_text" rows="3" maxlength="1200" @error('preview_text') aria-invalid="true" @enderror>{{ old('preview_text', $app->preview_text) }}</textarea>
                    <small>A concise introduction used on the card and in the “What to expect” section.</small>
                    @error('preview_text')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>

                <label class="sb-field sb-field--wide">
                    <span>Full description</span>
                    <textarea name="long_description" rows="6" maxlength="8000" @error('long_description') aria-invalid="true" @enderror>{{ old('long_description', $app->long_description) }}</textarea>
                    <small>Optional longer copy for the “About this app” section.</small>
                    @error('long_description')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>
                </div>
            </div>

            <div class="sb-editor-subsection">
                <header><h3>Learning profile</h3><p>Describe the outcomes, topics and any guidance adults should know.</p></header>
                <div class="sb-grid">
                <label class="sb-field sb-field--wide">
                    <span>Learning outcomes</span>
                    <textarea name="learning_outcomes_text" rows="4" maxlength="2000" placeholder="One outcome per line"
                              @error('learning_outcomes_text') aria-invalid="true" @enderror>{{ old('learning_outcomes_text', collect($app->learning_outcomes ?? [])->implode("\n")) }}</textarea>
                    <small>One per line. They appear as the learning-goals list.</small>
                    @error('learning_outcomes_text')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>

                <label class="sb-field sb-field--wide">
                    <span>Skills &amp; topics</span>
                    <input name="learning_tags_text" maxlength="600" value="{{ old('learning_tags_text', collect($app->learning_tags ?? [])->implode(', ')) }}"
                           placeholder="addition, times tables, mental maths" @error('learning_tags_text') aria-invalid="true" @enderror>
                    <small>Separate with commas. These become topic labels on the detail page.</small>
                    @error('learning_tags_text')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>

                <label class="sb-field sb-field--wide">
                    <span>Safety or supervision note</span>
                    <textarea name="safety_note" rows="2" maxlength="1200" @error('safety_note') aria-invalid="true" @enderror>{{ old('safety_note', $app->safety_note) }}</textarea>
                    <small>Optional guidance for parents and teachers.</small>
                    @error('safety_note')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>
                </div>
            </div>

            <div class="sb-editor-subsection">
                <header><h3>Session details</h3><p>Set the age guidance, typical play time and points reward.</p></header>
                <div class="sb-grid">
                <label class="sb-field">
                    <span>Youngest age</span>
                    <input type="number" name="age_min" min="3" max="120" value="{{ old('age_min', $app->age_min) }}" @error('age_min') aria-invalid="true" @enderror>
                    @error('age_min')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>

                <label class="sb-field">
                    <span>Oldest age</span>
                    <input type="number" name="age_max" min="3" max="120" value="{{ old('age_max', $app->age_max) }}" @error('age_max') aria-invalid="true" @enderror>
                    @error('age_max')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>

                <label class="sb-field">
                    <span>Typical session <em>required</em></span>
                    <input type="number" name="estimated_minutes" min="1" max="240" required value="{{ old('estimated_minutes', $app->estimated_minutes ?? 10) }}" @error('estimated_minutes') aria-invalid="true" @enderror>
                    <small>Approximate length in minutes.</small>
                    @error('estimated_minutes')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>

                <label class="sb-field">
                    <span>Points per session <em>required</em></span>
                    <input type="number" name="points_reward" min="0" max="500" required value="{{ old('points_reward', $app->points_reward ?? 25) }}" @error('points_reward') aria-invalid="true" @enderror>
                    <small>Use 0 when the app does not award points.</small>
                    @error('points_reward')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>
                </div>
            </div>

            <fieldset class="sb-fieldset">
                <legend>Designed for</legend>
                <p class="sb-fieldset__hint">Keep every group selected unless the app is intended for a specific audience.</p>
                <div class="sb-checks">
                    @foreach($roles as $value => $label)
                        <label class="sb-check">
                            <input type="checkbox" name="audience_roles[]" value="{{ $value }}" @checked(in_array($value, (array) $selectedRoles, true))>
                            <span><strong>{{ $label }}</strong></span>
                        </label>
                    @endforeach
                </div>
                @error('audience_roles')<strong class="sb-error">{{ $message }}</strong>@enderror
                @error('audience_roles.*')<strong class="sb-error">{{ $message }}</strong>@enderror
            </fieldset>
        </section>

        <section class="sb-card sb-platforms-section" id="availability" data-editor-section="platforms">
            <div class="sb-card__head sb-card__head--numbered">
                <span>4</span>
                <div><h2>Platforms</h2><p>Choose where learners can open or download this app. Empty destinations never create empty public buttons.</p></div>
            </div>

            <article class="sb-platform-card {{ $webEnabled ? 'is-enabled' : '' }} {{ $browserHasErrors ? 'has-error' : '' }}" data-platform-card="browser">
                <header class="sb-platform-card__head">
                    <span class="sb-platform-card__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18"></path></svg>
                    </span>
                    <span class="sb-platform-card__title">
                        <small>Browser</small>
                        <strong>Playable on the web</strong>
                    </span>
                    <span class="sb-platform-card__status {{ $app->hasPublishedWebApp() ? 'is-ready' : '' }}">
                        {{ $app->hasPublishedWebApp() ? 'Ready' : ($webEnabled ? 'Needs setup' : 'Off') }}
                    </span>
                    <label class="sb-switch">
                        <input type="checkbox" name="is_web_enabled" value="1" data-web-enabled data-platform-toggle aria-controls="browser-platform-body" @checked($webEnabled)>
                        <span aria-hidden="true"></span>
                        <span class="sb-visually-hidden">Offer a browser version</span>
                    </label>
                </header>

                <div id="browser-platform-body" class="sb-platform-card__body" data-platform-body @if(! $webEnabled && ! $browserHasErrors) hidden @endif>
                    @unless($isNew)
                        @if($app->hasPublishedWebApp())
                            <div class="sb-inline-status is-success">
                                <strong>Browser version ready</strong>
                                <span>{{ $app->usesUploadedBuild() ? 'StudyBuddy is serving the uploaded build.' : 'The secure external app is connected.' }}</span>
                                <a href="{{ route('admin.control-room.apps.preview.play', $app) }}" target="_blank" rel="noopener">Test launch</a>
                            </div>
                        @elseif($webEnabled)
                            <div class="sb-inline-status is-warning"><strong>Choose a launch method</strong><span>Upload a build or add a secure external address.</span></div>
                        @endif
                    @endunless

                    <div class="sb-launch-choices">
                        <section class="sb-launch-choice">
                            <span class="sb-launch-choice__label">Upload</span>
                            <h3>Static web build</h3>
                            <p>Choose a ZIP containing <code>index.html</code>. StudyBuddy validates and publishes it automatically.</p>
                            <label class="sb-field sb-file-field">
                                <span>Web build (.zip)</span>
                                <input type="file" name="web_app_zip" accept=".zip,application/zip" data-web-zip @error('web_app_zip') aria-invalid="true" @enderror>
                                <small>Up to 60 MB zipped and 120 MB extracted. Static files only.</small>
                                @error('web_app_zip')<strong class="sb-error">{{ $message }}</strong>@enderror
                            </label>
                        </section>

                        <section class="sb-launch-choice">
                            <span class="sb-launch-choice__label">Connect</span>
                            <h3>External web app</h3>
                            <p>Use a secure app hosted elsewhere. It opens in a new tab with its own security settings.</p>
                            <label class="sb-field">
                                <span>Secure launch address</span>
                                <input type="url" name="web_play_url" value="{{ $externalLaunchUrl }}" maxlength="500"
                                       placeholder="https://games.example.com/flag-frenzy/" data-web-url @error('web_play_url') aria-invalid="true" @enderror>
                                <small>Must start with <code>https://</code>. Leave blank when uploading a ZIP.</small>
                                @error('web_play_url')<strong class="sb-error">{{ $message }}</strong>@enderror
                            </label>
                        </section>
                    </div>

                    @if(! $isNew && ($app->web_app_package_path || $app->web_play_url))
                        <label class="sb-check sb-check--danger sb-check--spaced">
                            <input type="checkbox" name="remove_web_app" value="1" @checked(old('remove_web_app'))>
                            <span><strong>Remove the current browser version</strong><small>Deletes hosted build files and hides browser play. Store listings are not affected.</small></span>
                        </label>
                        @error('remove_web_app')<strong class="sb-error">{{ $message }}</strong>@enderror
                    @endif
                </div>
            </article>

            <div class="sb-platforms-divider" id="store-listing">
                <div>
                    <p class="sb-subhead">Store listings</p>
                    <h3>Downloads and support</h3>
                    <p>Add only the destinations where this app is genuinely available.</p>
                </div>
                <label class="sb-store-visibility">
                    <span><strong>Show store links</strong><small>Saved links stay here when hidden</small></span>
                    <span class="sb-switch">
                        <input type="checkbox" name="is_download_enabled" value="1" data-store-enabled data-platform-toggle aria-controls="store-platforms" @checked($storeEnabled)>
                        <span aria-hidden="true"></span>
                    </span>
                </label>
            </div>

            <div id="store-platforms" class="sb-platform-grid" data-platform-body @if(! $storeEnabled && ! $storeHasErrors) hidden @endif>
                <details class="sb-platform-card sb-platform-disclosure {{ $googleHasErrors ? 'has-error' : '' }}" data-platform-card="google-play" @if($googleConfigured || $googleHasErrors) open @endif>
                    <summary>
                        <span class="sb-platform-card__icon is-google" aria-hidden="true">▶</span>
                        <span class="sb-platform-card__title"><small>Android</small><strong>Google Play</strong></span>
                        <span class="sb-platform-card__status {{ $googleConfigured ? 'is-ready' : '' }}">{{ $googleConfigured ? 'Configured' : 'Not set up' }}</span>
                        <span class="sb-platform-card__chevron" aria-hidden="true">⌄</span>
                    </summary>
                    <div class="sb-platform-card__body">
                        <label class="sb-field">
                            <span>Google Play URL</span>
                            <input type="url" name="android_url" value="{{ old('android_url', $app->android_url) }}" maxlength="500"
                                   placeholder="https://play.google.com/store/apps/details?id=com.example.app" inputmode="url" data-google-play-url
                                   @error('android_url') aria-invalid="true" @enderror>
                            <small>Paste the full public listing link.</small>
                            @error('android_url')<strong class="sb-error">{{ $message }}</strong>@enderror
                        </label>

                        <label class="sb-field">
                            <span>Android package ID</span>
                            <input name="android_package_id" value="{{ old('android_package_id', $app->android_package_id) }}" maxlength="255"
                                   placeholder="com.example.app" autocapitalize="none" spellcheck="false" data-android-package
                                   @error('android_package_id') aria-invalid="true" @enderror>
                            <small data-package-status>Filled automatically from the Play link when empty. It must match the listing.</small>
                            @error('android_package_id')<strong class="sb-error">{{ $message }}</strong>@enderror
                        </label>
                    </div>
                </details>

                <details class="sb-platform-card sb-platform-disclosure {{ $appStoreHasErrors ? 'has-error' : '' }}" data-platform-card="app-store" @if($appStoreConfigured || $appStoreHasErrors) open @endif>
                    <summary>
                        <span class="sb-platform-card__icon is-apple" aria-hidden="true"></span>
                        <span class="sb-platform-card__title"><small>iPhone &amp; iPad</small><strong>App Store</strong></span>
                        <span class="sb-platform-card__status {{ $appStoreConfigured ? 'is-ready' : '' }}">{{ $appStoreConfigured ? 'Configured' : 'Not set up' }}</span>
                        <span class="sb-platform-card__chevron" aria-hidden="true">⌄</span>
                    </summary>
                    <div class="sb-platform-card__body">
                        <label class="sb-field">
                            <span>App Store URL</span>
                            <input type="url" name="ios_url" value="{{ old('ios_url', $app->ios_url) }}" maxlength="500"
                                   placeholder="https://apps.apple.com/app/…" inputmode="url" data-store-url @error('ios_url') aria-invalid="true" @enderror>
                            @error('ios_url')<strong class="sb-error">{{ $message }}</strong>@enderror
                        </label>
                    </div>
                </details>

                <details class="sb-platform-card sb-platform-disclosure sb-platform-disclosure--wide {{ $downloadsHaveErrors ? 'has-error' : '' }}" data-platform-card="downloads" @if($otherDownloadsConfigured || $downloadsHaveErrors) open @endif>
                    <summary>
                        <span class="sb-platform-card__icon" aria-hidden="true">↓</span>
                        <span class="sb-platform-card__title"><small>Optional</small><strong>Other downloads &amp; support</strong></span>
                        <span class="sb-platform-card__status {{ $otherDownloadsConfigured ? 'is-ready' : '' }}">{{ $otherDownloadsConfigured ? 'Configured' : 'Not set up' }}</span>
                        <span class="sb-platform-card__chevron" aria-hidden="true">⌄</span>
                    </summary>
                    <div class="sb-platform-card__body sb-grid">
                        @foreach([
                            ['windows_url', 'Windows download URL', 'https://'],
                            ['mac_url', 'Mac download URL', 'https://'],
                            ['support_url', 'App support page', 'https://'],
                        ] as [$field, $label, $placeholder])
                            <label class="sb-field">
                                <span>{{ $label }}</span>
                                <input type="url" name="{{ $field }}" value="{{ old($field, $app->{$field}) }}" maxlength="500"
                                       placeholder="{{ $placeholder }}" inputmode="url" data-store-url @error($field) aria-invalid="true" @enderror>
                                @error($field)<strong class="sb-error">{{ $message }}</strong>@enderror
                            </label>
                        @endforeach
                    </div>
                </details>
            </div>
        </section>

        <section class="sb-card sb-publish-card" id="preview-publish" data-editor-section="review">
            <div class="sb-card__head sb-card__head--numbered">
                <span>5</span>
                <div><h2>Preview &amp; publish</h2><p>Saving a draft keeps the app private. Publishing makes it visible on the public Apps page.</p></div>
            </div>

            <div class="sb-publish-grid">
                <div class="sb-publish-status {{ $app->is_active ? 'is-live' : 'is-draft' }}">
                    <span>{{ $app->is_active ? 'Published' : 'Private draft' }}</span>
                    <strong>{{ $app->is_active ? 'Learners can see this app.' : 'Only administrators can preview this app.' }}</strong>
                    <p>{{ $app->is_active ? 'Saving changes keeps it published unless you choose Save & unpublish.' : 'Choose Save & publish only when the preview is ready.' }}</p>
                </div>

                <div class="sb-readiness">
                    <h3>Recommended before publishing</h3>
                    <ul>
                        @foreach($readinessItems as $item)
                            <li class="{{ $item['ready'] ? 'is-ready' : '' }}"><span aria-hidden="true">{{ $item['ready'] ? '✓' : '○' }}</span>{{ $item['label'] }}</li>
                        @endforeach
                    </ul>
                    <small>These are helpful checks, not blockers. Save first to update the exact preview.</small>
                </div>
            </div>

            <details class="sb-advanced-panel" id="advanced" @if($advancedHasErrors) open @endif>
                <summary>
                    <span class="sb-advanced-panel__icon" aria-hidden="true">•••</span>
                    <span><strong>Advanced publishing details</strong><small>Page address, release stage, display order and featuring</small></span>
                    <span class="sb-card__chevron" aria-hidden="true">⌄</span>
                </summary>

                <div class="sb-card__details-body">
                    <div class="sb-grid">
                        <label class="sb-field sb-field--wide">
                            <span>App page address</span>
                            <input name="slug" value="{{ old('slug', $app->slug) }}" maxlength="160" data-app-slug
                                   placeholder="Built automatically from the app name" @error('slug') aria-invalid="true" @enderror>
                            <small>The page will use <code>/apps/<b data-slug-preview>{{ old('slug', $app->slug) ?: 'your-app-name' }}</b></code>. @unless($isNew) Changing this also moves an uploaded browser build. @endunless</small>
                            @error('slug')<strong class="sb-error">{{ $message }}</strong>@enderror
                        </label>

                        <label class="sb-field">
                            <span>Release stage</span>
                            <select name="status" @error('status') aria-invalid="true" @enderror>
                                @foreach($statuses as $option)
                                    <option value="{{ $option }}" @selected(old('status', $app->status) === $option)>{{ $statusLabels[$option] ?? ucfirst($option) }}</option>
                                @endforeach
                            </select>
                            <small>This describes the app itself; publishing is controlled by the save bar.</small>
                            @error('status')<strong class="sb-error">{{ $message }}</strong>@enderror
                        </label>

                        <label class="sb-field">
                            <span>Display order</span>
                            <input type="number" name="sort_order" min="0" max="9999" value="{{ old('sort_order', $app->sort_order ?? 0) }}" @error('sort_order') aria-invalid="true" @enderror>
                            <small>Lower numbers appear first.</small>
                            @error('sort_order')<strong class="sb-error">{{ $message }}</strong>@enderror
                        </label>
                    </div>

                    <label class="sb-check sb-check--spaced">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $app->is_featured))>
                        <span><strong>Feature this app</strong><small>Places it before non-featured apps in the catalogue</small></span>
                    </label>
                    @error('is_featured')<strong class="sb-error">{{ $message }}</strong>@enderror
                </div>
            </details>

            @unless($isNew)
                <div class="sb-preview-links">
                    @if($app->hasPublishedWebApp())
                        <a class="sb-btn sb-btn--ghost" href="{{ route('admin.control-room.apps.preview.play', $app) }}" target="_blank" rel="noopener">Test browser launch</a>
                    @endif
                </div>
            @endunless

        </section>
            </div>
        </div>
    </form>

    @unless($isNew)
        <section class="sb-card sb-card--danger">
            <div class="sb-card__head"><h2>Delete this app</h2><p>This permanently removes the listing, uploaded artwork, stored ZIP and hosted browser build. It cannot be undone.</p></div>
            <form class="sb-danger-form" method="POST" action="{{ route('admin.control-room.apps.destroy', $app) }}" data-delete-form data-confirm-value="{{ $app->name }}">
                @csrf @method('DELETE')
                <label class="sb-field">
                    <span>Type <b>{{ $app->name }}</b> to confirm</span>
                    <input name="confirm_name" autocomplete="off" placeholder="{{ $app->name }}" data-delete-confirm @error('confirm_name') aria-invalid="true" @enderror>
                    @error('confirm_name')<strong class="sb-error">{{ $message }}</strong>@enderror
                </label>
                <button class="sb-btn sb-btn--danger" type="submit" data-delete-button>Delete {{ $app->name }}</button>
            </form>
        </section>
    @endunless
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/studybuddy-admin-apps.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-admin-apps.js')) ? filemtime(public_path('assets/js/studybuddy-admin-apps.js')) : time() }}" defer></script>
@endpush
