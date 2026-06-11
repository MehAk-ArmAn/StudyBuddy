@extends('layouts.studybuddy')

@section('content')
    <section class="feature-section reveal-on-load">
        <form method="post" action="{{ route('login.attempt') }}">
            @csrf
            <input name="email" type="email" value="{{ old('email') }}" aria-label="{{ \App\Support\Cms::setting('auth.email_label') }}">
            <input name="password" type="password" aria-label="{{ \App\Support\Cms::setting('auth.password_label') }}">
            <button class="button" type="submit">{{ \App\Support\Cms::setting('auth.login_button_label') }}</button>
        </form>
    </section>
@endsection
