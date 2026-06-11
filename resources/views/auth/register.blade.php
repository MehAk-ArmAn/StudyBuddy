@extends('layouts.studybuddy')

@section('content')
    <section class="feature-section reveal-on-load">
        <form method="post" action="{{ route('register.store') }}">
            @csrf
            <input name="name" value="{{ old('name') }}">
            <input name="email" type="email" value="{{ old('email') }}">
            <input name="password" type="password">
            <select name="role">
                @foreach(['student', 'parent', 'teacher'] as $role)
                    <option value="{{ $role }}" @selected(old('role') === $role)></option>
                @endforeach
            </select>
            <button class="button" type="submit"></button>
        </form>
    </section>
@endsection
