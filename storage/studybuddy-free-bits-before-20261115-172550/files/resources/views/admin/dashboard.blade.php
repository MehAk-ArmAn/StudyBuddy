@extends('layouts.admin')

@section('title', 'Website Overview')

@php
    $controlRoomUrl = \Illuminate\Support\Facades\Route::has('admin.control-room.index')
        ? route('admin.control-room.index')
        : url('/admin/control-room');
@endphp

@section('content')
<div class="sb-control-dashboard sb-control-dashboard--bridge" data-admin-skip-unified>
    <section class="sb-control-welcome" aria-labelledby="website-overview-title">
        <div class="sb-control-welcome__copy">
            <p class="sb-control-eyebrow">Website overview</p>
            <h1 id="website-overview-title">Welcome back, {{ \Illuminate\Support\Str::before(auth()->user()?->name ?: 'Administrator', ' ') }}.</h1>
            <p>Your main tools now live together in the StudyBuddy Control Room.</p>
        </div>

        <div class="sb-control-welcome__actions">
            <a class="sb-control-button is-secondary" href="{{ url('/') }}" target="_blank" rel="noopener">
                <svg aria-hidden="true"><use href="#sb-admin-icon-external"></use></svg>
                View Website
            </a>
            <a class="sb-control-button is-primary" href="{{ $controlRoomUrl }}">
                Open Control Room
                <svg aria-hidden="true"><use href="#sb-admin-icon-arrow"></use></svg>
            </a>
        </div>
    </section>

    <section class="sb-control-metrics" aria-label="Website content overview">
        <article class="sb-control-metric">
            <span class="sb-control-metric__icon is-violet"><svg aria-hidden="true"><use href="#sb-admin-icon-home"></use></svg></span>
            <div>
                <strong>{{ number_format($enabledSections ?? 0) }}</strong>
                <span>Homepage Sections</span>
                <small>currently enabled</small>
            </div>
        </article>

        <article class="sb-control-metric">
            <span class="sb-control-metric__icon is-blue"><svg aria-hidden="true"><use href="#sb-admin-icon-pages"></use></svg></span>
            <div>
                <strong>{{ number_format($navItems ?? 0) }}</strong>
                <span>Navigation Items</span>
                <small>in the website header</small>
            </div>
        </article>

        <article class="sb-control-metric">
            <span class="sb-control-metric__icon is-teal"><svg aria-hidden="true"><use href="#sb-admin-icon-palette"></use></svg></span>
            <div>
                <strong>{{ number_format($footerItems ?? 0) }}</strong>
                <span>Footer Items</span>
                <small>across the website footer</small>
            </div>
        </article>

        <article class="sb-control-metric">
            <span class="sb-control-metric__icon is-amber"><svg aria-hidden="true"><use href="#sb-admin-icon-media"></use></svg></span>
            <div>
                <strong>{{ number_format($mediaAssets ?? 0) }}</strong>
                <span>Media Assets</span>
                <small>in the media library</small>
            </div>
        </article>
    </section>

    <section class="sb-control-bridge-panel" aria-labelledby="control-room-entry-title">
        <span class="sb-control-bridge-panel__icon"><svg aria-hidden="true"><use href="#sb-admin-icon-dashboard"></use></svg></span>
        <div>
            <p class="sb-control-eyebrow">One organized workspace</p>
            <h2 id="control-room-entry-title">Continue in the Control Room</h2>
            <p>Manage apps, content, users, messages, website settings and system health from the grouped navigation.</p>
        </div>
        <a class="sb-control-button is-primary" href="{{ $controlRoomUrl }}">
            Go to Dashboard
            <svg aria-hidden="true"><use href="#sb-admin-icon-arrow"></use></svg>
        </a>
    </section>
</div>
@endsection
