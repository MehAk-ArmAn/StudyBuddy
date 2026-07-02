@extends('layouts.admin')

@section('title', 'StudyBuddy Shell Studio')

@section('content')
<section class="sb-shell-admin">
    <header class="sb-shell-admin-hero">
        <div>
            <p class="eyebrow">StudyBuddy Admin</p>
            <h1>Navbar & Footer Studio</h1>
            <p>
                Control the site head and foot from one place. These settings stay consistent across pages;
                only logged-in account actions and role badges change by user role.
            </p>
        </div>
        <a href="{{ url('/') }}" target="_blank" rel="noopener">Preview site</a>
    </header>

    @if(session('status'))
        <div class="sb-shell-admin-alert">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('studybuddy.admin.shell.update') }}" class="sb-shell-admin-form">
        @csrf

        <section class="sb-shell-admin-card">
            <h2>Brand</h2>
            <div class="sb-shell-admin-grid">
                <label>Site name<input name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'StudyBuddy') }}"></label>
                <label>Logo text<input name="logo_text" value="{{ old('logo_text', $settings['logo_text'] ?? 'StudyBuddy') }}"></label>
                <label>Tagline<input name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? 'Learn • Play • Grow') }}"></label>
                <label>Logo image path / URL<input name="logo_image" value="{{ old('logo_image', $settings['logo_image'] ?? '') }}" placeholder="/assets/your-logo.png or https://..."></label>
            </div>
            <p class="sb-shell-admin-help">
                Use your actual StudyBuddy logo path here. If empty, the site uses a clean StudyBuddy SVG mark, not emojis.
            </p>
        </section>

        <section class="sb-shell-admin-card">
            <h2>Footer promise</h2>
            <label>Brand promise
                <textarea name="brand_promise" rows="4">{{ old('brand_promise', $settings['brand_promise'] ?? $settings['footer_text'] ?? 'StudyBuddy is a safe, playful learning space created to help students, parents, teachers, and independent learners build confidence through apps, quests, points, and guided practice.') }}</textarea>
            </label>

            <div class="sb-shell-admin-grid">
                <label>Pill 1<input name="footer_pill_one" value="{{ old('footer_pill_one', $settings['footer_pill_one'] ?? 'Explore apps') }}"></label>
                <label>Pill 2<input name="footer_pill_two" value="{{ old('footer_pill_two', $settings['footer_pill_two'] ?? 'Build skills') }}"></label>
                <label>Pill 3<input name="footer_pill_three" value="{{ old('footer_pill_three', $settings['footer_pill_three'] ?? 'Earn points') }}"></label>
                <label>Support email<input name="support_email" value="{{ old('support_email', $settings['support_email'] ?? $settings['contact_email'] ?? '') }}"></label>
            </div>
        </section>

        <section class="sb-shell-admin-card">
            <h2>Creator credit</h2>
            <div class="sb-shell-admin-grid">
                <label>Creator name<input name="creator_name" value="{{ old('creator_name', $settings['creator_name'] ?? 'PixelCraftsLab Studio') }}"></label>
                <label>Creator URL<input name="creator_url" value="{{ old('creator_url', $settings['creator_url'] ?? 'https://pixelcraftslab.com') }}"></label>
            </div>
        </section>

        <section class="sb-shell-admin-card">
            <h2>Navbar JSON</h2>
            <p class="sb-shell-admin-help">Edit labels and URLs here. This is the same on every page.</p>
            <textarea name="shell_navigation_json" rows="10">{{ old('shell_navigation_json', $settings['shell_navigation_json'] ?? $defaults['navigation']) }}</textarea>
        </section>

        <section class="sb-shell-admin-card">
            <h2>Footer groups JSON</h2>
            <p class="sb-shell-admin-help">Create footer columns like Explore, Roles, Learning Worlds, Community.</p>
            <textarea name="shell_footer_groups_json" rows="16">{{ old('shell_footer_groups_json', $settings['shell_footer_groups_json'] ?? $defaults['footer']) }}</textarea>
        </section>

        <section class="sb-shell-admin-card">
            <h2>Social links JSON</h2>
            <p class="sb-shell-admin-help">Leave URLs blank until social pages are ready.</p>
            <textarea name="shell_social_links_json" rows="7">{{ old('shell_social_links_json', $settings['shell_social_links_json'] ?? $defaults['socials']) }}</textarea>
        </section>

        <button class="sb-shell-admin-save" type="submit">Save navbar & footer</button>
    </form>
</section>
@endsection
