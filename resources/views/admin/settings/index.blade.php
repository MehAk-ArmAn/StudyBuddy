@extends('admin.layouts.admin')
@section('title', 'Settings')
@section('heading', 'Settings')
@section('content')
<form method="POST" action="{{ route('admin.settings.save') }}" class="admin-cms-panel">@csrf @method('PUT')@foreach($settings as $setting)<label>{{ $setting->key }}<textarea name="settings[{{ $setting->key }}]">{{ $setting->value }}</textarea></label>@endforeach<button class="admin-cms-save">Save Settings</button></form>
@endsection
