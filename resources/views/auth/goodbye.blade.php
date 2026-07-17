@extends('layouts.app')

@section('title', 'See you soon')

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('assets/css/studybuddy-system-screens.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-system-screens.css')) ? filemtime(public_path('assets/css/studybuddy-system-screens.css')) : time() }}"
>
@endpush

@section('content')
<section class="sb-system-screen">
    <div class="sb-system-screen__panel">
        <div class="sb-system-screen__visual">
            <span class="sb-system-screen__glow" aria-hidden="true"></span>

            <img
                src="{{ asset('assets/images/roles/independent-learner.svg') }}"
                alt="Independent learner ending a successful StudyBuddy session"
            >
        </div>

        <div class="sb-system-screen__content">
            <div class="sb-system-screen__brand">
                <img
                    src="{{ asset('assets/studybuddy-brand/studybuddy-logo-mark.svg') }}"
                    alt=""
                >

                <span>StudyBuddy</span>
            </div>

            <p class="sb-system-screen__eyebrow">
                Session completed
            </p>

            <h1>
                See you soon{{ filled($name ?? null) ? ', '.$name : '' }}.
            </h1>

            <p class="sb-system-screen__lead">
                You have been signed out safely. Your learning progress
                remains ready for the next time you return.
            </p>

            <div class="sb-system-screen__actions">
                <a
                    class="sb-system-button sb-system-button--primary"
                    href="{{ url('/') }}"
                >
                    Return to StudyBuddy
                </a>

                <a
                    class="sb-system-button sb-system-button--secondary"
                    href="{{ route('login') }}"
                >
                    Sign in again
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
