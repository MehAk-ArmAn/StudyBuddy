@extends('admin.layouts.admin')
@section('title', 'Admin Users')
@section('heading', 'Admin Users')
@section('content')
<section class="admin-cms-panel"><h2>Create Admin User</h2><form method="POST" action="{{ route('admin.users.store') }}" class="admin-cms-row">@csrf<input name="name" placeholder="Name"><input name="email" type="email" placeholder="Email"><input name="password" type="password" placeholder="Temporary password"><button class="admin-cms-save">Create</button></form></section>
<section class="admin-cms-panel"><h2>Existing Admins</h2>@foreach($users as $user)<form method="POST" action="{{ route('admin.users.update', $user) }}" class="admin-cms-row">@csrf @method('PUT')<input name="name" value="{{ $user->name }}"><input name="email" value="{{ $user->email }}"><input name="password" type="password" placeholder="Leave blank to keep password"><label class="admin-cms-check"><input type="checkbox" name="is_active" @checked($user->is_active)> Active</label><button class="admin-cms-save">Update</button></form>@endforeach</section>
@endsection
