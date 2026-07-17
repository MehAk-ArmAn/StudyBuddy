@extends('layouts.app')

@section('title', 'Contact StudyBuddy')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-contact-system.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-contact-system.css')) ? filemtime(public_path('assets/css/studybuddy-contact-system.css')) : time() }}">

<div class="sb-contact-page">
    <section class="sb-contact-hero">
        <div>
            <p class="sb-contact-kicker">Contact StudyBuddy</p>
            <h1>Need help? Send a message to the StudyBuddy team.</h1>
            <p>Use this form for account help, parent or teacher setup, safety questions, app feedback, or data requests.</p>
        </div>

        <aside>
            <img src="https://github.com/MehAk-ArmAn/StudyBuddy-Imgs/blob/main/hero/hero-dolphin-book.png?raw=true" alt="StudyBuddy support visual">
            <strong>Support inbox</strong>
            <span>{{ $settings['support_email'] ?? 'support@studybuddy.fun' }}</span>
            <p>Your message is saved in the admin Control Room.</p>
        </aside>
    </section>

    @if(session('status'))
        <div class="sb-contact-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="sb-contact-error">
            <strong>Fix this first:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="sb-contact-grid">
        <form method="POST" action="{{ route('studybuddy.contact.store') }}" class="sb-contact-form">
            @csrf
            <h2>Send your request</h2>

            <div class="sb-contact-fields">
                <label><span>Name</span><input name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required></label>
                <label><span>Email</span><input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required></label>

                <label>
                    <span>Your role</span>
                    <select name="role">
                        <option value="">Choose role</option>
                        <option value="student">Student</option>
                        <option value="parent">Parent</option>
                        <option value="teacher">Teacher</option>
                        <option value="independent_learner">Independent learner</option>
                        <option value="visitor">Visitor</option>
                    </select>
                </label>

                <label>
                    <span>Topic</span>
                    <select name="category" required>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="wide"><span>Subject</span><input name="subject" value="{{ old('subject') }}" required></label>
                <label class="wide"><span>Message</span><textarea name="message" rows="7" required>{{ old('message') }}</textarea></label>

                <label class="wide consent-row">
                    <input type="checkbox" name="consent" value="1" required>
                    <span>I agree that StudyBuddy can store this message so the admin team can review and reply.</span>
                </label>
            </div>

            <button type="submit">Send message</button>
        </form>

        <aside class="sb-contact-side">
            <article><strong>Safety concern?</strong><p>Choose Safety Concern so admins see it as priority.</p></article>
            <article><strong>Teacher setup?</strong><p>Share your organization, class issue, and student connection step.</p></article>
            <article><strong>Data deletion?</strong><p>Choose Data Deletion Request and include the account email.</p></article>
        </aside>
    </section>
</div>
@endsection
