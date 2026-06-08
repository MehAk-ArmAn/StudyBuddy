@php($logoExists = file_exists(public_path('assets/studybuddy/logo-icon.png')))
<footer class="cosmic-footer reveal-on-load">
    <div class="footer-brand">
        <span class="brand-mark footer-mark">
            @if($logoExists)
                <img src="{{ asset('assets/studybuddy/logo-icon.png') }}" alt="StudyBuddy logo">
            @else

            @endif
        </span>
        <div>
            <h2>StudyBuddy</h2>
            <p>A safe and fun learning universe for every student.</p>
        </div>
    </div>
    <div class="footer-columns">
        <div><h3>Explore</h3><a href="{{ route('home') }}">Home</a><a href="{{ route('apps.index') }}">Apps</a><a href="{{ route('rewards') }}">Rewards</a></div>
        <div><h3>For Parents</h3><a href="{{ route('demo.parent') }}">Parent Dashboard</a><a href="{{ route('demo.primary') }}">Primary Dashboard</a><a href="{{ route('demo.secondary') }}">Secondary Dashboard</a></div>
        <div><h3>For Teachers</h3><a href="{{ route('demo.teacher') }}">Teacher Dashboard</a><a href="{{ route('showcase') }}">Showcase</a><a href="{{ route('showcase') }}">Showcase</a></div>
    </div>
    <div class="footer-apps">
        <p class="eyebrow">Get StudyBuddy Apps</p>
        <div class="store-badge">▶ Google Play</div>
        <div class="store-badge"> App Store</div>
        @include('partials.image-placeholder', ['label' => 'FOOTER_QR_IMAGE', 'variant' => 'qr', 'caption' => 'Footer app QR'])
    </div>
</footer>
