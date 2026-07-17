@extends('layouts.admin')

@section('title', 'Apps & Platform')

@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;

    $settings = collect($settings ?? []);
    $apps = collect($apps ?? []);
    $checks = collect($checks ?? $checklist ?? []);
    $recentPoints = collect($recentPoints ?? $transactions ?? []);
    $imageUrl = function ($path) {
        if (! filled($path)) return null;
        return Str::startsWith($path, ['http://', 'https://', '/']) ? $path : asset($path);
    };
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-admin-app-publisher.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-admin-app-publisher.css')) ? filemtime(public_path('assets/css/studybuddy-admin-app-publisher.css')) : time() }}">
@endpush

@section('content')
<section class="sb-control-resource">
    <div class="sb-control-panel">
        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Apps & Platform</p>
                <h2>Final Platform Cockpit</h2>
                <p>Edit app detail pages, web-play/download links, launch checklist, public settings, and points adjustments. Everything here saves through the control-room routes.</p>
            </div>
            <div class="sb-control-row-actions">
                <a href="{{ url('/apps') }}" target="_blank" rel="noopener">View Apps Page</a>
                <a href="{{ Route::has('studybuddy.final.launch-readiness') ? route('studybuddy.final.launch-readiness') : url('/apps?section=roadmap') }}" target="_blank" rel="noopener">View Readiness</a>
                <a href="{{ url('/admin/control-room') }}">Control Room</a>
            </div>
        </div>

        @if($errors->any())
            <div class="sb-control-alert danger"><strong>Check these fields:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <div class="sb-control-stat-grid">
            <article class="purple"><span>Platform settings</span><strong>{{ number_format($settings->count()) }}</strong><small>Editable records</small></article>
            <article class="blue"><span>Mini apps</span><strong>{{ number_format($apps->count()) }}</strong><small>App records</small></article>
            <article class="cyan"><span>Checklist</span><strong>{{ number_format($checks->count()) }}</strong><small>Launch items</small></article>
            <article class="pink"><span>Recent point logs</span><strong>{{ number_format($recentPoints->count()) }}</strong><small>Latest activity</small></article>
        </div>
    </div>

    <form class="sb-control-panel sb-control-form" method="POST" action="{{ route('admin.control-room.final.settings') }}">
        @csrf
        <div class="sb-control-panel-head wide"><div><p class="sb-control-kicker">Public Settings</p><h2>Platform messages & labels</h2><p>These values are used by public StudyBuddy pages and platform sections.</p></div><button class="primary" type="submit">Save settings</button></div>
        <div class="sb-control-form-grid">
            @forelse($settings as $setting)
                <label class="{{ ($setting->field_type ?? '') === 'textarea' || strlen((string) $setting->setting_value) > 130 ? 'wide' : '' }}">
                    <span>{{ $setting->label ?? $setting->setting_key }}</span>
                    @if(($setting->field_type ?? '') === 'textarea' || strlen((string) $setting->setting_value) > 130)
                        <textarea name="settings[{{ $setting->setting_key }}]" rows="4">{{ $setting->setting_value }}</textarea>
                    @else
                        <input name="settings[{{ $setting->setting_key }}]" value="{{ $setting->setting_value }}">
                    @endif
                    @if(!empty($setting->help_text))<small>{{ $setting->help_text }}</small>@endif
                </label>
            @empty
                <div class="sb-control-empty">No platform settings found.</div>
            @endforelse
        </div>
    </form>

    <div class="sb-control-panel">
        <div class="sb-control-panel-head wide"><div><p class="sb-control-kicker">Mini Apps</p><h2>Editable app records</h2><p>Use image paths from <code>public/assets/studybuddy-imgs</code> / the StudyBuddy-Imgs repo when possible.</p></div></div>
        <div class="sb-admin-app-editor-grid">
            @forelse($apps as $app)
                <form class="sb-admin-app-editor" method="POST" enctype="multipart/form-data" action="{{ route('admin.control-room.final.apps.update', $app) }}">
                    @csrf
                    @method('PATCH')
                    <div class="sb-admin-app-preview">
                        @if($imageUrl($app->hero_image))<img src="{{ $imageUrl($app->hero_image) }}" alt="{{ $app->name }} preview">@else<img src="{{ asset('assets/studybuddy-control/apps.svg') }}" alt="{{ $app->name }} preview">@endif
                        <div><span>{{ $app->status }}</span><strong>{{ $app->name }}</strong><small>{{ $app->tagline }}</small></div>
                    </div>

                    <div class="sb-control-form-grid compact">
                        <label><span>Name</span><input name="name" value="{{ $app->name }}" required></label>
                        <label><span>Category</span><input name="category" value="{{ $app->category }}"></label>
                        <label class="wide"><span>Tagline</span><input name="tagline" value="{{ $app->tagline }}"></label>
                        <label class="wide"><span>Description</span><textarea name="description" rows="3">{{ $app->description }}</textarea></label>
                        <label class="wide"><span>Preview text</span><textarea name="preview_text" rows="2">{{ $app->preview_text }}</textarea></label>
                        <label class="wide"><span>Safety note</span><textarea name="safety_note" rows="2">{{ $app->safety_note }}</textarea></label>
                        <label><span>Status</span><select name="status">@foreach(['concept','planned','beta','live','paused'] as $status)<option value="{{ $status }}" @selected($app->status === $status)>{{ $status }}</option>@endforeach</select></label>
                        <label><span>Icon</span><input name="icon" value="{{ $app->icon }}"></label>
                        <label class="wide"><span>Hero image path</span><input name="hero_image" value="{{ $app->hero_image }}"></label>
                        <label class="wide"><span>Web play URL</span><input name="web_play_url" value="{{ $app->web_play_url }}" placeholder="Auto-filled after ZIP upload, or paste a trusted hosted URL"><small>Use a full trusted URL, or upload a static web-app ZIP below.</small></label>

                        <div class="wide sb-web-app-publisher">
                            <div>
                                <span class="sb-publisher-label">Web app launcher</span>
                                @if($app->hasPublishedWebApp())
                                    <strong>Published and ready</strong>
                                    <small>Last upload: {{ optional($app->web_app_uploaded_at)->format('d M Y, H:i') ?: 'URL-managed build' }}</small>
                                    <a href="{{ route('studybuddy.final.web-play', $app->slug) }}" target="_blank" rel="noopener">Open launcher</a>
                                @else
                                    <strong>Not published yet</strong>
                                    <small>Upload a ZIP containing index.html. StudyBuddy will publish it to this app automatically.</small>
                                @endif
                            </div>
                            <label class="sb-publisher-upload">
                                <span>Upload static web-app ZIP</span>
                                <input type="file" name="web_app_zip" accept=".zip,application/zip">
                                <small>Maximum 30 MB upload / 120 MB extracted. HTML, CSS, JS, images, audio, JSON, and other static assets are supported.</small>
                            </label>
                            @if($app->web_app_package_path || $app->web_play_url)
                                <label class="sb-publisher-remove"><input type="checkbox" name="remove_web_app" value="1"> Remove the current published web app</label>
                            @endif
                        </div>
                        <label><span>iOS URL</span><input name="ios_url" value="{{ $app->ios_url }}"></label>
                        <label><span>Android URL</span><input name="android_url" value="{{ $app->android_url }}"></label>
                        <label><span>Windows URL</span><input name="windows_url" value="{{ $app->windows_url }}"></label>
                        <label><span>Mac URL</span><input name="mac_url" value="{{ $app->mac_url }}"></label>
                        <label><span>Points</span><input type="number" min="0" max="500" name="points_reward" value="{{ $app->points_reward }}" required></label>
                        <label><span>Minutes</span><input type="number" min="1" max="240" name="estimated_minutes" value="{{ $app->estimated_minutes }}" required></label>
                        <label><span>Age min</span><input type="number" min="3" max="120" name="age_min" value="{{ $app->age_min }}"></label>
                        <label><span>Age max</span><input type="number" min="3" max="120" name="age_max" value="{{ $app->age_max }}"></label>
                    </div>

                    <div class="sb-control-check-grid">
                        @foreach(['student'=>'Student','parent'=>'Parent','teacher'=>'Teacher','independent_learner'=>'Independent'] as $role => $label)
                            <label><input type="checkbox" name="audience_roles[]" value="{{ $role }}" @checked(in_array($role, $app->audience_roles ?? []))> {{ $label }}</label>
                        @endforeach
                        <label><input type="checkbox" name="is_web_enabled" value="1" @checked($app->is_web_enabled)> Web enabled</label>
                        <label><input type="checkbox" name="is_download_enabled" value="1" @checked($app->is_download_enabled)> Downloads</label>
                        <label><input type="checkbox" name="is_featured" value="1" @checked($app->is_featured)> Featured</label>
                        <label><input type="checkbox" name="is_active" value="1" @checked($app->is_active)> Active</label>
                    </div>

                    <div class="sb-control-save-row"><button class="primary" type="submit">Save {{ $app->name }}</button></div>
                </form>
            @empty
                <div class="sb-control-empty">No mini apps found.</div>
            @endforelse
        </div>
    </div>

    <div class="sb-control-bottom-grid">
        <section class="sb-control-panel">
            <div class="sb-control-panel-head"><div><h2>Launch checklist</h2><p>Update status, priority, owner, and details.</p></div></div>
            <div class="sb-control-stack">
                @forelse($checks as $item)
                    <form class="sb-control-inline-editor" method="POST" action="{{ route('admin.control-room.final.checklist.update', $item) }}">
                        @csrf
                        @method('PATCH')
                        <h3>{{ $item->title }}</h3>
                        <div class="sb-control-form-grid compact">
                            <label><span>Status</span><select name="status">@foreach(['todo','doing','done','blocked'] as $status)<option value="{{ $status }}" @selected($item->status === $status)>{{ $status }}</option>@endforeach</select></label>
                            <label><span>Priority</span><select name="priority">@foreach(['low','medium','high','critical'] as $priority)<option value="{{ $priority }}" @selected($item->priority === $priority)>{{ $priority }}</option>@endforeach</select></label>
                            <label><span>Owner</span><input name="owner_label" value="{{ $item->owner_label }}"></label>
                            <label class="wide"><span>Description</span><textarea name="description" rows="2">{{ $item->description }}</textarea></label>
                        </div>
                        <button type="submit">Save checklist item</button>
                    </form>
                @empty
                    <div class="sb-control-empty">No checklist items found.</div>
                @endforelse
            </div>
        </section>

        <section class="sb-control-panel">
            <div class="sb-control-panel-head"><div><h2>Points adjustment</h2><p>Manual admin point awards or corrections.</p></div></div>
            <form class="sb-control-form" method="POST" action="{{ route('admin.control-room.final.points.award') }}">
                @csrf
                <div class="sb-control-form-grid compact">
                    <label><span>User ID</span><input type="number" min="1" name="user_id" required></label>
                    <label><span>Points</span><input type="number" name="points" required></label>
                    <label class="wide"><span>Title</span><input name="title" value="Admin adjustment" required></label>
                </div>
                <div class="sb-control-save-row"><button class="primary" type="submit">Save points</button></div>
            </form>

            <div class="sb-control-table-wrap">
                <table class="sb-control-table">
                    <thead><tr><th>User</th><th>Title</th><th>Points</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($recentPoints as $point)
                            <tr><td>{{ $point->user_id }}</td><td>{{ $point->title }}</td><td>{{ $point->points }}</td><td>{{ $point->status }}</td></tr>
                        @empty
                            <tr><td colspan="4"><div class="sb-control-empty">No recent point records.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</section>
@endsection
