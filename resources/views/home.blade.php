{{-- W12: Exact user-supplied Base44 HTML snapshot, served through the existing Laravel home route. --}}
{{-- Laravel routes/auth/database/controllers and React bridge are not replaced. --}}
@php
    $sbExactFile = public_path('assets/studybuddy-base44-exact/home.html');
@endphp
{!! is_file($sbExactFile) ? file_get_contents($sbExactFile) : '<h1>StudyBuddy homepage assets missing</h1>' !!}
