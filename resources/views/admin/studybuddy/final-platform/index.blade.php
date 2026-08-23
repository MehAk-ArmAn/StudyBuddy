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
        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Learning Apps</p>
                <h2>Apps have one dedicated editor</h2>
                <p>Use the Apps area for artwork, Google Play details, browser versions, previews and publishing.</p>
            </div>
            <a href="{{ route('admin.control-room.apps.index') }}">Open Apps</a>
        </div>
        <div class="sb-admin-app-editor-grid">
            @forelse($apps as $app)
                <article class="sb-admin-app-editor">
                    <div class="sb-admin-app-preview">
                        @if($imageUrl($app->safeHeroImage()))<img src="{{ $imageUrl($app->safeHeroImage()) }}" alt="{{ $app->name }} preview">@else<img src="{{ asset('assets/studybuddy-control/apps.svg') }}" alt="{{ $app->name }} preview">@endif
                        <div><span>{{ $app->is_active ? 'Published' : 'Draft' }}</span><strong>{{ $app->name }}</strong><small>{{ $app->tagline }}</small></div>
                    </div>
                    <div class="sb-control-save-row">
                        <a href="{{ route('admin.control-room.apps.edit', $app) }}">Edit in Apps</a>
                    </div>
                </article>
            @empty
                <div class="sb-control-empty">No learning apps yet. <a href="{{ route('admin.control-room.apps.create') }}">Add the first app</a>.</div>
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
