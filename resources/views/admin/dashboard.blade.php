@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
    <section class="admin-card">
        <h2>Homepage CMS Status</h2>
        <p>
            Manage the StudyBuddy homepage content, navigation, footer, images, and global settings from one clean control panel.
        </p>
    </section>

    <div class="cards">
        <article>
            <strong>{{ $enabledSections }}</strong>
            <span>Enabled sections</span>
        </article>

        <article>
            <strong>{{ $navItems }}</strong>
            <span>Navigation links</span>
        </article>

        <article>
            <strong>{{ $footerItems }}</strong>
            <span>Footer links</span>
        </article>

        <article>
            <strong>{{ $mediaAssets }}</strong>
            <span>Media assets</span>
        </article>
    </div>

    <div class="quick">
        <a href="{{ route('admin.homepage-sections.index') }}">Edit Homepage</a>
        <a href="{{ route('admin.navigation-items.index') }}">Edit Navigation</a>
        <a href="{{ route('admin.footer-items.index') }}">Edit Footer</a>
        <a href="{{ route('admin.media-assets.index') }}">Media Library</a>
        <a href="{{ route('admin.site-settings.index') }}">Site Settings</a>
    </div>
@endsection
