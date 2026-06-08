@extends('admin.layouts.admin')
@section('title', 'Showcase Manager')
@section('heading', 'Showcase Manager')
@section('content')
<form method="POST" action="{{ route('admin.showcase.save') }}" class="admin-cms-panel">@csrf @method('PUT')@foreach($panels as $panel)<section class="admin-cms-edit-section"><h2>{{ $panel->number }}</h2><label>Title<input name="panels[{{ $panel->id }}][title]" value="{{ $panel->title }}"></label><label>Description<textarea name="panels[{{ $panel->id }}][description]">{{ $panel->description }}</textarea></label></section>@endforeach<button class="admin-cms-save">Save Showcase</button></form>
@endsection
