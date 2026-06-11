@extends('layouts.studybuddy')

@section('content')
    <section class="feature-section reveal-on-load">
        <form method="post" action="{{ route('login.attempt') }}">
            @csrf
            <input name="email" type="email" value="{{ old('email') }}">
            <input name="password" type="password">
            <button class="button" type="submit"></button>
        </form>
    </section>
@endsection
