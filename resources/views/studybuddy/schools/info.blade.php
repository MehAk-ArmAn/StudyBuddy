@extends('layouts.app')
@section('title', 'For Schools — StudyBuddy')
@push('styles')<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-next-chapter.css') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/css/studybuddy-next-chapter.css') }}">@endpush
@section('content')
<div class="sb-schools" id="school-top">
    <section class="sb-schools-hero">
        <img class="sb-v6-school-wave" src="{{ asset('assets/studybuddy-v6/glass-wave.webp') }}" alt="" aria-hidden="true" loading="lazy">
        <div class="sb-schools-copy"><p class="sb-next-eyebrow">STUDYBUDDY FOR SCHOOLS · PRIVATE PILOT WORKSPACES</p><h1>Where the whole school <em>grows together.</em></h1><p>Bring playful practice, teacher tools and learning adventures into one thoughtfully designed place. A school request is reviewed by our team before any organization workspace opens.</p><div class="sb-next-actions"><a href="{{ auth()->check() ? route('schools.apply') : route('register') }}" class="sb-next-button primary">Request a school workspace <span aria-hidden="true">↗</span></a><a href="{{ url('/apps') }}" class="sb-next-button secondary">Explore the learning apps <span aria-hidden="true">→</span></a></div><p class="sb-schools-disclaimer">Pilot workflow · institutional verification required · not an automatic school approval.</p></div>
        <div class="sb-schools-preview" aria-label="Illustrative dashboard concept, not live school statistics"><div class="school-preview-top"><b><span>✳</span> StudyBuddy / School Space</b><small>CONCEPT PREVIEW</small></div><div class="school-preview-greet">Good morning, educators. <span>☀️</span></div><div class="school-preview-meta">A calmer place to organize learning.</div><div class="school-preview-grid"><div class="school-preview-card purple"><span>✦</span><strong>Learning worlds</strong><small>Discover age-appropriate activities</small></div><div class="school-preview-card peach"><span>▦</span><strong>Staff space</strong><small>Private, invitation-led access</small></div><div class="school-preview-card aqua"><span>♧</span><strong>Family links</strong><small>Consent and connected accounts</small></div><div class="school-preview-card lilac"><span>✓</span><strong>School review</strong><small>Human approval before access</small></div></div><div class="school-preview-bottom"><span class="circle-icon">✦</span><span>Learning made a little brighter.</span><b>↗</b></div></div>
    </section>
    <div class="sb-school-ticker" aria-hidden="true"><span>STUDENTS ✦ TEACHERS ✦ FAMILIES ✦ SCHOOLS ✦ STUDENTS ✦ TEACHERS ✦ FAMILIES ✦ SCHOOLS ✦</span></div>
    <section class="sb-schools-value"><p class="sb-next-eyebrow">School-ready thinking</p><h2>More than a login.<br><em>A connected learning community.</em></h2><div class="sb-schools-value-grid"><article><span>01</span><h3>School-managed access</h3><p>Institutions apply, are manually reviewed, and invite staff through time-limited, email-specific links.</p></article><article><span>02</span><h3>Playful learning</h3><p>Keep StudyBuddy's actual educational app library, lessons and friendly learning mechanics at the heart.</p></article><article><span>03</span><h3>Real boundaries</h3><p>Separate school membership from ordinary accounts, with server-side authorization checks.</p></article><article><span>04</span><h3>Grow responsibly</h3><p>Classroom management, detailed analytics and family communication need a dedicated privacy-reviewed next phase.</p></article></div></section>
    <section class="sb-schools-world" aria-labelledby="sb-schools-world-title">
        <div class="sb-schools-world__copy">
            <p class="sb-next-eyebrow">WELCOME TO THE WORLD OF STUDYBUDDY</p>
            <h2 id="sb-schools-world-title">Serious about learning.<br><em>Joyful about getting there.</em></h2>
            <p>Give your teachers a thoughtful, private workspace while learners explore the same delightful educational worlds they know and love.</p>
            <small>Artistic concept · no student data or actual school metrics are shown</small>
        </div>
    </section>
    @include('partials.studybuddy-universe-school-roles')

@php
$sb10SchoolItems=[
 ['src'=>asset('assets/studybuddy-brand/pages/school-world-banner.webp'),'alt'=>'Illustrated StudyBuddy school world','title'=>'The school experience','subtitle'=>'Thoughtfully connected spaces','url'=>route('schools.info')],
 ['src'=>asset('assets/studybuddy-brand/pages/path-teachers.webp'),'alt'=>'Illustrated teacher learning path','title'=>'For educators','subtitle'=>'Invitation-led collaboration','url'=>url('/roles')],
 ['src'=>asset('assets/studybuddy-brand/pages/path-apps.webp'),'alt'=>'Illustrated StudyBuddy learning apps','title'=>'Learning adventures','subtitle'=>'Explore the existing catalogue','url'=>url('/apps')],
 ['src'=>asset('assets/studybuddy-brand/pages/path-parents.webp'),'alt'=>'Illustrated family learning path','title'=>'For families','subtitle'=>'Learning together','url'=>url('/roles')],
];
@endphp
<section class="sb10-school-gallery" aria-labelledby="sb10-school-title" data-sb10-gallery-shell>
 <p>EXPLORE STUDYBUDDY FOR SCHOOLS</p><h2 id="sb10-school-title">A welcoming place to learn together.</h2>
 <div data-sb10-component="schools" data-sb10-config="sb10-school-gallery" aria-label="Illustrated learning journey gallery"></div>
 <script type="application/json" id="sb10-school-gallery">@json($sb10SchoolItems)</script>
 <div class="sb10-gallery-fallback">@foreach($sb10SchoolItems as $item)<a href="{{ $item['url'] }}"><img src="{{ $item['src'] }}" alt=""><strong>{{ $item['title'] }}</strong><small>{{ $item['subtitle'] }}</small></a>@endforeach</div>
</section>
    <section class="sb-schools-steps"><div><p class="sb-next-eyebrow">Join the pilot</p><h2>A thoughtful way in.</h2><p>No instant school-access checkbox. Every organization must pass a real review. Every organization must pass a real review.</p></div><ol><li><b>01</b><span><strong>Create an adult account</strong><small>Use your real work details and verify your email.</small></span></li><li><b>02</b><span><strong>Apply for your school</strong><small>Provide the official domain, institution email and intended use.</small></span></li><li><b>03</b><span><strong>Human verification</strong><small>Our team reviews school authorization before granting access.</small></span></li><li><b>04</b><span><strong>Invite approved colleagues</strong><small>Use the private workspace after approval; staff invitations expire.</small></span></li></ol></section>
    <section class="sb-schools-cta"><span aria-hidden="true">✦</span><h2>Ready to make learning<br>feel different?</h2><p>Let's build a safer, brighter space for your school.</p><a class="sb-next-button primary" href="{{ auth()->check() ? route('schools.apply') : route('register') }}">Begin school onboarding <span aria-hidden="true">↗</span></a></section>
</div>
@endsection

@push('styles')
@if(file_exists(public_path('assets/studybuddy-reactbits/reactbits.css')))<link rel="stylesheet" href="{{ asset('assets/studybuddy-reactbits/reactbits.css') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/studybuddy-reactbits/reactbits.css') }}">@endif
@endpush
@push('scripts')
@if(file_exists(public_path('assets/studybuddy-reactbits/reactbits.js')))<script defer src="{{ asset('assets/studybuddy-reactbits/reactbits.js') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/studybuddy-reactbits/reactbits.js') }}"></script>@endif
@endpush
