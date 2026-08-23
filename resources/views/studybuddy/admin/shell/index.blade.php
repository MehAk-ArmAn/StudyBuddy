@extends('layouts.admin')

@section('title', 'Website Shell')

@section('content')
<section class="sb-control-resource">
    <form method="POST" action="{{ route('studybuddy.admin.shell.update') }}" class="sb-control-panel sb-control-form">
        @csrf
        <div class="sb-control-panel-head wide">
            <div><p class="sb-control-kicker">Website Shell</p><h2>Navbar & Footer Studio</h2><p>Keep the navbar short. Use footer groups for the bigger link universe.</p></div>
            <div class="sb-control-row-actions"><a href="{{ url('/') }}" target="_blank" rel="noopener">Preview</a><a href="{{ url('/admin/control-room') }}">Control Room</a><button class="primary" type="submit">Save</button></div>
        </div>

        <div class="sb-control-editor-tabs"><a href="#brand">Brand</a><a href="#footer">Footer</a><a href="#links">Links JSON</a></div>

        <div class="sb-control-form-grid" id="brand">
            <label><span>Site name</span><input name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'StudyBuddy') }}"></label>
            <label><span>Logo text</span><input name="logo_text" value="{{ old('logo_text', $settings['logo_text'] ?? 'StudyBuddy') }}"></label>
            <label><span>Tagline</span><input name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? 'Learn • Play • Grow') }}"></label>
            <label><span>Logo image path / URL</span><input name="logo_image" value="{{ old('logo_image', $settings['logo_image'] ?? '') }}" placeholder="/assets/images/studybuddy-logo.png"></label>

            <label class="wide" id="footer"><span>Brand promise</span><textarea name="brand_promise" rows="4">{{ old('brand_promise', $settings['brand_promise'] ?? $settings['footer_text'] ?? 'StudyBuddy is a safe, playful learning space created to help students, parents, teachers, and independent learners build confidence through apps, quests, points, and guided practice.') }}</textarea></label>

            <label><span>Footer pill 1</span><input name="footer_pill_one" value="{{ old('footer_pill_one', $settings['footer_pill_one'] ?? 'Explore apps') }}"></label>
            <label><span>Footer pill 2</span><input name="footer_pill_two" value="{{ old('footer_pill_two', $settings['footer_pill_two'] ?? 'Build skills') }}"></label>
            <label><span>Footer pill 3</span><input name="footer_pill_three" value="{{ old('footer_pill_three', $settings['footer_pill_three'] ?? 'Earn points') }}"></label>
            <label><span>Support email</span><input name="support_email" value="{{ old('support_email', $settings['support_email'] ?? $settings['contact_email'] ?? '') }}"></label>

            <label><span>Creator name</span><input name="creator_name" value="{{ old('creator_name', $settings['creator_name'] ?? 'PixelCraftsLab Studio') }}"></label>
            <label><span>Creator URL</span><input name="creator_url" value="{{ old('creator_url', $settings['creator_url'] ?? 'https://pixelcraftslab.com') }}"></label>

            <label class="wide" id="links"><span>Navbar links JSON</span><textarea name="shell_navigation_json" rows="8">{{ old('shell_navigation_json', $settings['shell_navigation_json'] ?? $defaults['navigation']) }}</textarea></label>
            <label class="wide"><span>Footer groups JSON</span><textarea name="shell_footer_groups_json" rows="13">{{ old('shell_footer_groups_json', $settings['shell_footer_groups_json'] ?? $defaults['footer']) }}</textarea></label>
            <label class="wide"><span>Social links JSON</span><textarea name="shell_social_links_json" rows="6">{{ old('shell_social_links_json', $settings['shell_social_links_json'] ?? $defaults['socials']) }}</textarea></label>
        </div>

        <div class="sb-control-save-row"><button class="primary" type="submit">Save navbar & footer</button></div>
    </form>
</section>
@endsection
