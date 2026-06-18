@extends('layouts.admin')

@section('page_title', $method === 'POST' ? 'Create User' : 'Edit User')

@section('content')
    @php
        $action = $method === 'POST' ? route('admin.users.store') : route('admin.users.update', $user);
    @endphp

    @if($errors->any())
        <div class="error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form class="editor" method="POST" action="{{ $action }}">
        @csrf
        @if($method !== 'POST') @method('PUT') @endif

        <label>Name <input name="name" value="{{ old('name', $user->name) }}" required></label>
        <label>Email <input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
        <label>Role
            <select name="role">
                @foreach(['student'=>'Student','parent'=>'Parent','teacher'=>'Teacher','professional'=>'Professional','admin'=>'Admin'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $user->role ?: 'student') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>Learning Stage <input name="learning_stage" value="{{ old('learning_stage', $user->learning_stage) }}"></label>
        <label>Avatar Style <input name="avatar_style" value="{{ old('avatar_style', $user->avatar_style ?: 'dolphin-cadet') }}"></label>
        <label>Cosmic Points <input type="number" min="0" name="cosmic_points" value="{{ old('cosmic_points', $user->cosmic_points ?? 0) }}"></label>
        @if($method === 'POST')
            <label>Temporary Access Key <input name="password" value="ChangeMe12345!"></label>
        @endif
        <label><input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $user->is_admin))> Admin access</label>

        <button>Save</button>
        <a class="button" href="{{ route('admin.users.index') }}">Cancel</a>
    </form>
@endsection
