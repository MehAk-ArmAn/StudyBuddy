{{-- Safe in ANY Blade layout: requires a successful production Vite build before loading. --}}
@if (is_file(public_path('build/studybuddy-react/manifest.json')))
    @viteReactRefresh
    @vite('resources/js/studybuddy-react/entry.jsx', 'build/studybuddy-react')
@endif
