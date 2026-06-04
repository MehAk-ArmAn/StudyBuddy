@extends('layouts.app')

@section('title', 'Showcase')

@section('content')
<section class="showcase-page reveal-on-load">
    <div class="showcase-title">
        <p class="eyebrow">StudyBuddy — The Complete Cosmic Learning Universe</p>
        <h1>All core product surfaces in one premium visual collage.</h1>
        <p>Landing, app store, app portal, dashboards, rewards, mobile, and admin previews are composed from real Blade panels with image placeholders.</p>
    </div>

    <div class="mockup-collage">
        @php
            $panels = [
                ['02', 'Landing preview', 'LANDING_PREVIEW_IMAGE', 'wide', route('home')],
                ['03', 'App Store preview', 'APP_STORE_PREVIEW_IMAGE', 'large', route('apps.index')],
                ['04', 'App Portal preview', 'APP_PORTAL_PREVIEW_IMAGE', 'medium', route('apps.math-quest')],
                ['05', 'Primary dashboard preview', 'PRIMARY_DASHBOARD_PREVIEW_IMAGE', 'medium', route('demo.primary')],
                ['06', 'Secondary dashboard preview', 'SECONDARY_DASHBOARD_PREVIEW_IMAGE', 'medium', route('demo.secondary')],
                ['07', 'Parent dashboard preview', 'PARENT_DASHBOARD_PREVIEW_IMAGE', 'medium', route('demo.parent')],
                ['08', 'Teacher dashboard preview', 'TEACHER_DASHBOARD_PREVIEW_IMAGE', 'medium', route('demo.teacher')],
                ['09', 'Rewards / Buddy customization preview', 'BUDDY_CUSTOMIZATION_IMAGE', 'medium', route('rewards')],
                ['10', 'Mobile preview', 'MOBILE_PREVIEW_IMAGE', 'phone', route('apps.math-quest.play')],
                ['11', 'Admin dashboard preview', 'ADMIN_PREVIEW_IMAGE', 'wide', route('demo.admin')],
            ];
        @endphp
        @foreach($panels as [$number, $title, $label, $size, $url])
            <a class="preview-panel tilt-card preview-{{ $size }}" href="{{ $url }}">
                <div class="panel-top"><span>{{ $number }}</span><strong>{{ $title }}</strong></div>
                @include('partials.image-placeholder', ['label' => $label, 'variant' => $size === 'phone' ? 'phone' : 'preview', 'caption' => $title])
                <p>{{ $title }} built as a real route preview surface.</p>
            </a>
        @endforeach
    </div>
</section>
@endsection
