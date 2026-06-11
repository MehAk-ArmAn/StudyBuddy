@extends('layouts.studybuddy')

@section('content')
    <section class="feature-section reveal-on-load">
        <form method="post" action="{{ route('register.store') }}">
            @csrf
            <input name="name" value="{{ old('name') }}" aria-label="{{ \App\Support\Cms::setting('auth.name_label') }}">
            <input name="email" type="email" value="{{ old('email') }}" aria-label="{{ \App\Support\Cms::setting('auth.email_label') }}">
            <input name="password" type="password" aria-label="{{ \App\Support\Cms::setting('auth.password_label') }}">
            <select name="role" aria-label="{{ \App\Support\Cms::setting('auth.role_label') }}">
                @foreach(['student', 'parent', 'teacher'] as $role)
                    <option value="{{ $role }}" @selected(old('role') === $role)>{{ \App\Support\Cms::setting('auth.role_'.$role.'_label') }}</option>
                @endforeach
            </select>
            <button class="button" type="submit">{{ \App\Support\Cms::setting('auth.register_button_label') }}</button>
        </form>
    </section>
@endsection
