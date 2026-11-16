@extends('layouts.app')

@section('title', 'Contact us')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-contact-form-final.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-contact-form-final.css')) ? filemtime(public_path('assets/css/studybuddy-contact-form-final.css')) : time() }}">

<div class="sb-contact-final">
    <section class="sb-contact-final-hero">
        <div class="sb-contact-final-copy">
            <p>Contact StudyBuddy</p>
            <h1>Need help? Send us a message.</h1>
            <span>Use this form for account help, parent or teacher setup, safety questions, learning app feedback, or data deletion requests.</span>
        </div>

        <div class="sb-contact-final-card">
            <img src="{{ asset('assets/studybuddy-brand/pages/hero-dolphin-book.webp') }}" alt="The StudyBuddy dolphin leaping out of an open book">
            <strong>Support inbox</strong>
            <small>{{ $settings['support_email'] ?? 'support@studybuddy.fun' }}</small>
        </div>
    </section>

    @if(session('status'))
        <section class="sb-contact-final-success">{{ session('status') }}</section>
    @endif

    @if($errors->any())
        <section class="sb-contact-final-error">
            <strong>Fix this first:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="sb-contact-final-grid">
        <form method="POST" action="{{ route('studybuddy.contact.store') }}" class="sb-contact-final-form">
            @csrf

            <div class="form-head">
                <p>Message Form</p>
                <h2>Tell us what happened</h2>
                <span>Your message goes securely to the StudyBuddy support team.</span>
            </div>

            <div class="form-grid">
                <label>
                    <span>Name</span>
                    <input name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required placeholder="Your name">
                </label>

                <label>
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required placeholder="you@example.com">
                </label>

                <label>
                    <span>Your role</span>
                    <select name="role">
                        <option value="">Choose role</option>
                        <option value="student" @selected(old('role') === 'student')>Student</option>
                        <option value="parent" @selected(old('role') === 'parent')>Parent</option>
                        <option value="teacher" @selected(old('role') === 'teacher')>Teacher</option>
                        <option value="independent_learner" @selected(old('role') === 'independent_learner')>Independent learner</option>
                        <option value="visitor" @selected(old('role') === 'visitor')>Visitor</option>
                    </select>
                </label>

                <label>
                    <span>Topic</span>
                    <select name="category" required>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="wide">
                    <span>Subject</span>
                    <input name="subject" value="{{ old('subject') }}" required placeholder="Example: I need help connecting a child account">
                </label>

                <label class="wide">
                    <span>Message</span>
                    <textarea name="message" rows="8" required placeholder="Write your message here. Include the page, account email, and what you need help with.">{{ old('message') }}</textarea>
                </label>

                <label class="wide consent">
                    <input type="checkbox" name="consent" value="1" @checked(old('consent')) required>
                    <span>I agree that StudyBuddy can store this message so the support team can review and reply.</span>
                </label>
            </div>

            <button type="submit">Send message</button>
        </form>

        <aside class="sb-contact-final-side">
            <article>
                <strong>Safety concern</strong>
                <p>Choose Safety Concern so our support team can prioritize it.</p>
            </article>

            <article>
                <strong>Parent or teacher setup</strong>
                <p>Share the dashboard page, learner email, class name, or Connect Code step that needs help.</p>
            </article>

            <article>
                <strong>Data deletion</strong>
                <p>Choose Data Deletion Request and include the account email plus what data should be reviewed.</p>
            </article>
        </aside>
    </section>
</div>
@endsection
