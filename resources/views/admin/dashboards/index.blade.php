@extends('admin.layouts.admin')
@section('title', 'Dashboard Content')
@section('heading', 'Dashboard Content Manager')
@section('content')
<form method="POST" action="{{ route('admin.dashboards.save') }}" class="admin-cms-panel">@csrf @method('PUT')@foreach($widgets as $audience => $group)<section class="admin-cms-edit-section"><h2>{{ ucfirst($audience) }}</h2>@foreach($group as $widget)<div class="admin-cms-row"><input name="widgets[{{ $widget->id }}][title]" value="{{ $widget->title }}"><input name="widgets[{{ $widget->id }}][label]" value="{{ $widget->label }}"><input name="widgets[{{ $widget->id }}][value]" value="{{ $widget->value }}"><textarea name="widgets[{{ $widget->id }}][description]">{{ $widget->description }}</textarea></div>@endforeach</section>@endforeach<button class="admin-cms-save">Save Dashboard Content</button></form>
@endsection
