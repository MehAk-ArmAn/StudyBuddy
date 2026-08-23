@extends('layouts.app')

@section('title', 'Sign out')

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
                alt="Independent learner completing a StudyBuddy session"
            >
        </div>

        <div class="sb-system-screen__content">
            <div class="sb-system-screen__brand">
                <img
                    src="{{ asset('assets/studybuddy-brand/studybuddy-logo-mark.svg') }}"
                    alt=""
                >

                <span>StudyBuddy account</span>
            </div>

            <p class="sb-system-screen__eyebrow">
                Secure sign out
            </p>

            <h1>Finished learning for now?</h1>

            <p class="sb-system-screen__lead">
                Your profile, progress, goals, points and app activity
                will remain saved. You can return whenever you are ready.
            </p>

            <div class="sb-system-screen__actions">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="sb-system-button sb-system-button--primary"
                    >
                        Sign out securely
                    </button>
                </form>

                <a
                    class="sb-system-button sb-system-button--secondary"
                    href="{{ route('dashboard') }}"
                >
                    Return to dashboard
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
