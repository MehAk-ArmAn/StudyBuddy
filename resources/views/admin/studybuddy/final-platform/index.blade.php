@extends('layouts.app')

@section('content')
<main class="sb-final-shell sb-final-admin sb-admin-unified-apps">
    <section class="sb-final-hero compact">
        <div>
            <p class="sb-final-kicker">Admin</p>
            <h1>Final Platform Cockpit</h1>
            <p>Control the app universe, app detail pages, web-play links, device downloads, points, launch checklist, and platform messages from one place.</p>
        </div>
        <div class="sb-final-actions">
            <a class="sb-final-btn" href="{{ route('pages.apps') }}">View Apps Page</a>
            <a class="sb-final-btn sb-final-btn-soft" href="{{ route('studybuddy.final.launch-readiness') }}">View Readiness</a>
        </div>
    </section>

    @if(session('status'))
        <div class="sb-final-alert">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="sb-final-alert danger">
            <strong>Check these fields:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="sb-final-panel">
        <h2>Platform settings</h2>
        <p>These messages appear across public pages. They are database-controlled and editable here.</p>
        <form method="POST" action="{{ route('studybuddy.admin.final.settings') }}" class="sb-final-admin-grid">
            @csrf
            @foreach($settings as $setting)
                <label>
                    <span>{{ $setting->label }}</span>
                    @if($setting->field_type === 'textarea')
                        <textarea name="settings[{{ $setting->setting_key }}]" rows="3">{{ $setting->setting_value }}</textarea>
                    @else
                        <input name="settings[{{ $setting->setting_key }}]" value="{{ $setting->setting_value }}">
                    @endif
                    <small>{{ $setting->help_text }}</small>
                </label>
            @endforeach
            <button class="sb-final-btn" type="submit">Save platform settings</button>
        </form>
    </section>

    <section class="sb-final-panel">
        <div class="sb-admin-panel-heading">
            <div>
                <h2>Unified Apps Catalog</h2>
                <p>Each app record powers /apps, /apps/slug, /play/slug, dashboard links, points, and device download slots.</p>
            </div>
            <a class="sb-final-btn sb-final-btn-soft" href="{{ route('pages.apps') }}">Preview catalog</a>
        </div>

        @foreach($apps as $app)
            <details class="sb-final-admin-card" @if($loop->first) open @endif>
                <summary>
                    <span>{{ $app->icon ?: '✨' }} {{ $app->name }}</span>
                    <small>{{ $app->slug }} · {{ ucfirst($app->status) }} · {{ $app->points_reward }} pts</small>
                </summary>
                <form method="POST" action="{{ route('studybuddy.admin.final.apps.update', $app) }}" class="sb-final-admin-form">
                    @csrf
                    @method('PATCH')
                    <div class="sb-final-admin-grid compact">
                        <label><span>Name</span><input name="name" value="{{ old('name', $app->name) }}" required></label>
                        <label><span>Slug</span><input name="slug" value="{{ old('slug', $app->slug) }}"></label>
                        <label><span>Category</span><input name="category" value="{{ old('category', $app->category) }}"></label>
                        <label><span>Status</span><select name="status"><option value="concept" @selected(old('status', $app->status)==='concept')>Concept</option><option value="planned" @selected(old('status', $app->status)==='planned')>Planned</option><option value="beta" @selected(old('status', $app->status)==='beta')>Beta</option><option value="live" @selected(old('status', $app->status)==='live')>Live</option><option value="paused" @selected(old('status', $app->status)==='paused')>Paused</option></select></label>
                        <label><span>Icon</span><input name="icon" value="{{ old('icon', $app->icon) }}"></label>
                        <label><span>Image URL</span><input name="image_url" value="{{ old('image_url', $app->image_url) }}" placeholder="/assets/studybuddy-imgs/apps/app-name.png"></label>
                        <label><span>Accent</span><input name="accent" value="{{ old('accent', $app->accent) }}"></label>
                        <label><span>Age Range</span><input name="age_range" value="{{ old('age_range', $app->age_range) }}"></label>
                        <label><span>Points</span><input type="number" name="points_reward" value="{{ old('points_reward', $app->points_reward) }}" min="0" max="500"></label>
                        <label><span>Minutes</span><input type="number" name="estimated_minutes" value="{{ old('estimated_minutes', $app->estimated_minutes) }}" min="1" max="240"></label>
                        <label><span>Sort Order</span><input type="number" name="sort_order" value="{{ old('sort_order', $app->sort_order) }}" min="0"></label>
                        <label><span>Detail Button Label</span><input name="detail_cta_label" value="{{ old('detail_cta_label', $app->detail_cta_label) }}"></label>

                        <label class="wide"><span>Hero Heading</span><input name="hero_heading" value="{{ old('hero_heading', $app->hero_heading) }}"></label>
                        <label class="wide"><span>Tagline</span><input name="tagline" value="{{ old('tagline', $app->tagline) }}"></label>
                        <label class="wide"><span>Short Description</span><textarea name="description" rows="3">{{ old('description', $app->description) }}</textarea></label>
                        <label class="wide"><span>Long Detail Description</span><textarea name="long_description" rows="5">{{ old('long_description', $app->long_description) }}</textarea></label>
                        <label class="wide"><span>Guest Preview Text</span><textarea name="preview_text" rows="3">{{ old('preview_text', $app->preview_text) }}</textarea></label>

                        <label><span>Web play URL</span><input name="web_play_url" value="{{ old('web_play_url', $app->web_play_url) }}"></label>
                        <label><span>iOS URL</span><input name="ios_url" value="{{ old('ios_url', $app->ios_url) }}"></label>
                        <label><span>Android URL</span><input name="android_url" value="{{ old('android_url', $app->android_url) }}"></label>
                        <label><span>Windows URL</span><input name="windows_url" value="{{ old('windows_url', $app->windows_url) }}"></label>
                        <label><span>Mac URL</span><input name="mac_url" value="{{ old('mac_url', $app->mac_url) }}"></label>
                        <label><span>Support URL</span><input name="support_url" value="{{ old('support_url', $app->support_url) }}"></label>

                        <fieldset class="wide sb-admin-checkset">
                            <legend>Role availability</legend>
                            @foreach($roleOptions as $value => $label)
                                <label class="check"><input type="checkbox" name="role_scope[]" value="{{ $value }}" @checked(in_array($value, $app->role_scope ?: []))> {{ $label }}</label>
                            @endforeach
                        </fieldset>

                        <label class="wide"><span>Learning Tags — one per line</span><textarea name="learning_tags_text" rows="3">{{ implode("\n", $app->learning_tags ?: []) }}</textarea></label>
                        <label class="wide"><span>Learning Outcomes — one per line</span><textarea name="learning_outcomes_text" rows="4">{{ implode("\n", $app->learning_outcomes ?: []) }}</textarea></label>
                        <label class="wide"><span>How It Works — one step per line</span><textarea name="how_it_works_text" rows="4">{{ implode("\n", $app->how_it_works ?: []) }}</textarea></label>
                        <label class="wide"><span>Screenshot URLs — one per line</span><textarea name="screenshot_urls_text" rows="3">{{ implode("\n", $app->screenshot_urls ?: []) }}</textarea></label>
                        <label class="wide"><span>Safety Note</span><textarea name="safety_note" rows="3">{{ old('safety_note', $app->safety_note) }}</textarea></label>
                        <label class="wide"><span>Locked Preview Note</span><textarea name="locked_preview_note" rows="3">{{ old('locked_preview_note', $app->locked_preview_note) }}</textarea></label>
                        <label class="wide"><span>Platform Notes</span><textarea name="platform_notes" rows="3">{{ old('platform_notes', $app->platform_notes) }}</textarea></label>

                        <label class="check"><input type="checkbox" name="is_web_enabled" value="1" @checked($app->is_web_enabled)> Web enabled</label>
                        <label class="check"><input type="checkbox" name="is_download_enabled" value="1" @checked($app->is_download_enabled)> Downloads enabled</label>
                        <label class="check"><input type="checkbox" name="is_featured" value="1" @checked($app->is_featured)> Featured</label>
                        <label class="check"><input type="checkbox" name="is_active" value="1" @checked($app->is_active)> Active</label>
                    </div>
                    <div class="sb-admin-card-actions">
                        <button class="sb-final-btn" type="submit">Save {{ $app->name }}</button>
                        <a class="sb-final-btn sb-final-btn-soft" href="{{ route('studybuddy.apps.show', $app->slug) }}">View detail page</a>
                    </div>
                </form>
            </details>
        @endforeach
    </section>

    <section class="sb-final-panel">
        <h2>Launch checklist</h2>
        @foreach($checks as $item)
            <form method="POST" action="{{ route('studybuddy.admin.final.checklist.update', $item) }}" class="sb-final-check-form">
                @csrf
                @method('PATCH')
                <strong>{{ $item->title }}</strong>
                <select name="status"><option value="todo" @selected($item->status==='todo')>Todo</option><option value="doing" @selected($item->status==='doing')>Doing</option><option value="done" @selected($item->status==='done')>Done</option><option value="blocked" @selected($item->status==='blocked')>Blocked</option></select>
                <select name="priority"><option value="low" @selected($item->priority==='low')>Low</option><option value="medium" @selected($item->priority==='medium')>Medium</option><option value="high" @selected($item->priority==='high')>High</option><option value="critical" @selected($item->priority==='critical')>Critical</option></select>
                <input name="owner_label" value="{{ $item->owner_label }}" placeholder="Owner">
                <textarea name="description" rows="2">{{ $item->description }}</textarea>
                <button class="sb-final-btn sb-final-btn-soft" type="submit">Save</button>
            </form>
        @endforeach
    </section>
</main>
@endsection
