@extends('layouts.app')
@section('title', 'Study Space — StudyBuddy')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-w36/study-space.css') }}?v={{ filemtime(public_path('assets/studybuddy-w36/study-space.css')) }}">
<style>.sb36-page{padding:clamp(14px,3vw,36px) 14px 70px;min-height:80vh;background:linear-gradient(155deg,#0b1b42 0%,#15133f 50%,#151d44 100%)}.sb36-page-title{max-width:1350px;margin:0 auto 18px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:12px}.sb36-page-title p{font:700 11px/1.4 system-ui,sans-serif;letter-spacing:.13em;text-transform:uppercase;color:#d1bbff}.sb36-page-title a{border:1px solid #ffffff44;border-radius:9px;color:white;padding:9px 12px;font:600 12px system-ui,sans-serif;text-decoration:none}.sb36-page-title a:hover{background:#ffffff15}.sb36-page-title h2{font:700 clamp(19px,2vw,25px)/1.25 system-ui,sans-serif;color:#fff;margin:5px 0 0}.sb36-noscript{padding:30px;color:#fff;text-align:center}</style>
@endpush
@section('content')
<section class="sb36-page" aria-label="StudyBuddy personal study space">
 <div class="sb36-page-title"><div><p>STUDYBUDDY • YOUR WORKSPACE</p><h2>Study Space <span aria-hidden="true">✦</span></h2></div><a href="{{ route('dashboard') }}">← Back to dashboard</a></div>
 <div id="sb36-react-root" data-account-key="{{ hash_hmac('sha256', (string) auth()->id(), (string) config('app.key')) }}" aria-label="Private study notes and thread history"><div class="sb36-noscript">Loading your study workspace…</div></div>
 <noscript><p class="sb36-noscript">Enable JavaScript to use your session-only study notes.</p></noscript>
</section>
@endsection
@push('scripts')
<script defer src="{{ asset('assets/studybuddy-w36/study-space.bundle.js') }}?v={{ filemtime(public_path('assets/studybuddy-w36/study-space.bundle.js')) }}"></script>
@endpush
