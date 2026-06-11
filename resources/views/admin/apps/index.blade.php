@extends('admin.layouts.admin')
@section('title', 'Apps Manager')
@section('heading', 'Apps Manager')
@section('content')
@include('admin.partials.image-path-help')
<form method="POST" action="{{ route('admin.apps.save') }}" class="admin-cms-panel">@csrf @method('PUT')<div class="admin-cms-table"><table><thead><tr><th>Name</th><th>Description</th><th>Image Path</th><th>CTA</th><th>Status</th><th>Order</th></tr></thead><tbody>@foreach($apps as $app)<tr><td><input name="apps[{{ $app->id }}][title]" value="{{ $app->title }}"></td><td><textarea name="apps[{{ $app->id }}][description]">{{ $app->description }}</textarea></td><td><input name="apps[{{ $app->id }}][image_path]" value="{{ $app->image_path }}"></td><td><input name="apps[{{ $app->id }}][cta_text]" value="{{ $app->cta_text }}"></td><td><select name="apps[{{ $app->id }}][status]"><option @selected($app->status==='live')>live</option><option @selected($app->status==='preview')>preview</option><option @selected($app->status==='concept')>concept</option></select></td><td><input type="number" name="apps[{{ $app->id }}][sort_order]" value="{{ $app->sort_order }}"></td></tr>@endforeach</tbody></table></div><button class="admin-cms-save">Save Apps</button></form>
@endsection
