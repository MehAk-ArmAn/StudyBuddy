@extends('layouts.app')

@section('content')
<main class="sb-final-shell sb-final-admin">
    <section class="sb-final-hero compact">
        <div>
            <p class="sb-final-kicker">Admin</p>
            <h1>Final Platform Cockpit</h1>
            <p>Edit launchpad text, app platform links, points policy, readiness checklist, and final distribution settings from one place.</p>
        </div>
        <div class="sb-final-actions">
            <a class="sb-final-btn" href="{{ route('studybuddy.final.app-launchpad') }}">View Launchpad</a>
            <a class="sb-final-btn sb-final-btn-soft" href="{{ route('studybuddy.final.launch-readiness') }}">View Readiness</a>
        </div>
    </section>

    @if(session('status'))
        <div class="sb-final-alert">{{ session('status') }}</div>
    @endif

    <section class="sb-final-panel">
        <h2>Platform settings</h2>
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
        <h2>Mini-app distribution slots</h2>
        @foreach($apps as $app)
            <form method="POST" action="{{ route('studybuddy.admin.final.apps.update', $app) }}" class="sb-final-admin-card">
                @csrf
                @method('PATCH')
                <h3>{{ $app->icon }} {{ $app->name }}</h3>
                <div class="sb-final-admin-grid compact">
                    <label><span>Name</span><input name="name" value="{{ $app->name }}"></label>
                    <label><span>Category</span><input name="category" value="{{ $app->category }}"></label>
                    <label><span>Status</span><select name="status"><option value="concept" @selected($app->status==='concept')>Concept</option><option value="planned" @selected($app->status==='planned')>Planned</option><option value="beta" @selected($app->status==='beta')>Beta</option><option value="live" @selected($app->status==='live')>Live</option><option value="paused" @selected($app->status==='paused')>Paused</option></select></label>
                    <label><span>Icon</span><input name="icon" value="{{ $app->icon }}"></label>
                    <label><span>Tagline</span><input name="tagline" value="{{ $app->tagline }}"></label>
                    <label><span>Points</span><input type="number" name="points_reward" value="{{ $app->points_reward }}"></label>
                    <label><span>Minutes</span><input type="number" name="estimated_minutes" value="{{ $app->estimated_minutes }}"></label>
                    <label><span>Web play URL</span><input name="web_play_url" value="{{ $app->web_play_url }}"></label>
                    <label><span>iOS URL</span><input name="ios_url" value="{{ $app->ios_url }}"></label>
                    <label><span>Android URL</span><input name="android_url" value="{{ $app->android_url }}"></label>
                    <label><span>Windows URL</span><input name="windows_url" value="{{ $app->windows_url }}"></label>
                    <label><span>Mac URL</span><input name="mac_url" value="{{ $app->mac_url }}"></label>
                    <label class="wide"><span>Description</span><textarea name="description" rows="3">{{ $app->description }}</textarea></label>
                    <label class="check"><input type="checkbox" name="is_web_enabled" value="1" @checked($app->is_web_enabled)> Web enabled</label>
                    <label class="check"><input type="checkbox" name="is_download_enabled" value="1" @checked($app->is_download_enabled)> Downloads enabled</label>
                    <label class="check"><input type="checkbox" name="is_featured" value="1" @checked($app->is_featured)> Featured</label>
                    <label class="check"><input type="checkbox" name="is_active" value="1" @checked($app->is_active)> Active</label>
                </div>
                <button class="sb-final-btn sb-final-btn-soft" type="submit">Save {{ $app->name }}</button>
            </form>
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
