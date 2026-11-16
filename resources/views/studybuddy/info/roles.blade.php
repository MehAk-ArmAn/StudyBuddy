@extends('layouts.app')

@section('title', 'Roles')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-roles-clean.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-roles-clean.css')) ? filemtime(public_path('assets/css/studybuddy-roles-clean.css')) : time() }}">
@endpush

@section('content')
@php
    $roleImages = [
        'student' => '/assets/studybuddy-brand/pages/path-apps.webp',
        'parent' => '/assets/studybuddy-brand/pages/path-parents.webp',
        'teacher' => '/assets/studybuddy-brand/pages/path-teachers.webp',
        'independent_learner' => '/assets/images/roles/independent-learner.svg',
        'independent' => '/assets/images/roles/independent-learner.svg',
    ];
@endphp

<div class="sb-roles-clean-page">
    <section class="sb-roles-clean-hero" aria-labelledby="roles-title">
        <div>
            <p class="sb-roles-eyebrow">{{ $pageData['eyebrow'] ?? 'StudyBuddy roles' }}</p>
            <h1 id="roles-title">{{ $pageData['title'] ?? 'Choose the StudyBuddy experience that fits you.' }}</h1>
            <p>{{ $pageData['subtitle'] ?? 'Students, parents, teachers, and independent learners each get tools made for their needs.' }}</p>
        </div>
        <div class="sb-roles-hero-actions">
            <a class="sb-role-primary" href="{{ route('register') }}">Create account</a>
            <a class="sb-role-secondary" href="{{ route('studybuddy.apps') }}">Explore apps</a>
        </div>
    </section>


@php
$sb10RoleItems=collect($roleCards ?? [])->map(function ($role) use ($roleImages) {
  $key=$role['key'] ?? \Illuminate\Support\Str::slug($role['title'] ?? 'role');
  $src=$roleImages[$key] ?? null;
  return $src ? ['src'=>asset(ltrim($src,'/')),'alt'=>'Illustration for '.($role['title'] ?? 'StudyBuddy role'),'title'=>(string)($role['title'] ?? 'StudyBuddy role'),'subtitle'=>(string)($role['tagline'] ?? ''),'url'=>url($role['cta_url'] ?? '/apps')] : null;
})->filter()->values()->all();
@endphp
@if(count($sb10RoleItems)>=3)
<section class="sb10-public-role" aria-labelledby="sb10-role-title" data-sb10-gallery-shell>
  <p>EXPLORE YOUR STUDYBUDDY ROLE</p><h2 id="sb10-role-title">Different paths, one place to grow.</h2>
  <div data-sb10-component="roles" data-sb10-config="sb10-role-gallery" aria-label="Interactive learning role carousel"></div>
  <script type="application/json" id="sb10-role-gallery">@json($sb10RoleItems)</script>
  <div class="sb10-gallery-fallback">@foreach($sb10RoleItems as $item)<a href="{{ $item['url'] }}"><img src="{{ $item['src'] }}" alt=""><strong>{{ $item['title'] }}</strong><small>{{ $item['subtitle'] }}</small></a>@endforeach</div>
</section>
@endif
    <section class="sb-role-card-grid" aria-label="StudyBuddy role options">
        @foreach($roleCards as $role)
            @php
                $key = $role['key'] ?? \Illuminate\Support\Str::slug($role['title'] ?? 'role');
                $roleImage = $roleImages[$key] ?? null;
                $ctaUrl = $role['cta_url'] ?? '/apps';
            @endphp

            <article class="sb-role-clean-card">
                <div class="sb-role-card-visual">
                    @if($roleImage)
                        <img src="{{ $roleImage }}" alt="" loading="lazy">
                    @else
                        <span aria-hidden="true">{{ $role['icon'] ?? '' }}</span>
                    @endif
                </div>

                <div class="sb-role-card-content">
                    <div class="sb-role-card-title">
                        <span aria-hidden="true">{{ $role['icon'] ?? '' }}</span>
                        <h2>{{ $role['title'] ?? 'StudyBuddy role' }}</h2>
                    </div>

                    <p class="sb-role-tagline">{{ $role['tagline'] ?? '' }}</p>

                    <dl class="sb-role-details">
                        @if(!empty($role['best_for']))
                            <div><dt>Best for</dt><dd>{{ $role['best_for'] }}</dd></div>
                        @endif
                        @if(!empty($role['dashboard']))
                            <div><dt>Dashboard</dt><dd>{{ $role['dashboard'] }}</dd></div>
                        @endif
                        @if(!empty($role['controls']))
                            <div><dt>Your controls</dt><dd>{{ $role['controls'] }}</dd></div>
                        @endif
                        @if(!empty($role['safety']))
                            <div><dt>Safety</dt><dd>{{ $role['safety'] }}</dd></div>
                        @endif
                    </dl>

                    <a class="sb-role-card-link" href="{{ url($ctaUrl) }}">{{ $role['cta_label'] ?? 'Learn more' }} <span aria-hidden="true">→</span></a>
                </div>
            </article>
        @endforeach
    </section>

    <section class="sb-roles-clean-explainer">
        <div>
            <p class="sb-roles-eyebrow">Simple and role-focused</p>
            <h2>Everyone sees the tools they actually need.</h2>
            <p>StudyBuddy keeps learning tools clear instead of filling every dashboard with unrelated controls.</p>
        </div>

        <ol>
            <li><span>1</span><div><strong>Choose your role</strong><p>Select the experience that matches how you use StudyBuddy.</p></div></li>
            <li><span>2</span><div><strong>Set your preferences</strong><p>Add your goals, interests, and privacy choices.</p></div></li>
            <li><span>3</span><div><strong>Use your dashboard</strong><p>Open the apps, support tools, or classroom features made for you.</p></div></li>
        </ol>
    </section>

    <section class="sb-roles-clean-safety">
        <div>
            <p class="sb-roles-eyebrow">Consent-first connections</p>
            <h2>Parent and teacher links stay controlled.</h2>
            <p>Learners use their current StudyBuddy Connect Code to approve account connections. Password sharing is never needed.</p>
        </div>
        <a class="sb-role-secondary" href="{{ url('/community-guidelines') }}">Read safety guidance</a>
    </section>
</div>
@endsection

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('assets/css/studybuddy-apps-roles-color.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-apps-roles-color.css')) ? filemtime(public_path('assets/css/studybuddy-apps-roles-color.css')) : time() }}"
>
@endpush

@push('scripts')
<script
    src="{{ asset('assets/js/studybuddy-apps-roles-color.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-apps-roles-color.js')) ? filemtime(public_path('assets/js/studybuddy-apps-roles-color.js')) : time() }}"
    defer
></script>
@endpush

@push('styles')
@if(file_exists(public_path('assets/studybuddy-reactbits/reactbits.css')))<link rel="stylesheet" href="{{ asset('assets/studybuddy-reactbits/reactbits.css') }}?v={{ filemtime(public_path('assets/studybuddy-reactbits/reactbits.css')) }}">@endif
@endpush
@push('scripts')
@if(file_exists(public_path('assets/studybuddy-reactbits/reactbits.js')))<script defer src="{{ asset('assets/studybuddy-reactbits/reactbits.js') }}?v={{ filemtime(public_path('assets/studybuddy-reactbits/reactbits.js')) }}"></script>@endif
@endpush
