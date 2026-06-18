@extends('layouts.app')

@section('content')
<section class="auth-wrap">
    <form class="auth-panel auth-form" method="POST" action="{{ route('register.store') }}">
        @csrf
        <h1>Create your StudyBuddy account</h1>
        @if($errors->any()) <div class="auth-error">{{ $errors->first() }}</div> @endif
        <label>Name <input name="name" value="{{ old('name') }}" required></label>
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>I am a
            <select name="role" required>
                <option value="student">Student</option>
                <option value="parent">Parent</option>
                <option value="teacher">Teacher</option>
                <option value="professional">Professional</option>
            </select>
        </label>
        <label>Learning stage <input name="learning_stage" value="{{ old('learning_stage') }}"></label>
        <label>Access key <input type="password" name="password" required></label>
        <label>Confirm key <input type="password" name="password_confirmation" required></label>
        <button class="btn" type="submit">Create dashboard</button>
        <p><a href="{{ route('login') }}">Already have an account?</a></p>
    </form>
</section>
@endsection
