@extends('admin.layouts.admin')
@section('title', 'Badges Manager')
@section('heading', 'Badges Manager')
@section('content')
<form method="POST" action="{{ route('admin.badges.save') }}" class="admin-cms-panel">@csrf @method('PUT')<div class="admin-cms-table"><table><thead><tr><th>Name</th><th>Description</th><th>Image Path</th><th>Requirement</th><th>Active</th></tr></thead><tbody>@foreach($badges as $badge)<tr><td><input name="badges[{{ $badge->id }}][name]" value="{{ $badge->name }}"></td><td><textarea name="badges[{{ $badge->id }}][description]">{{ $badge->description }}</textarea></td><td><input name="badges[{{ $badge->id }}][image_path]" value="{{ $badge->image_path }}"></td><td><input name="badges[{{ $badge->id }}][requirement_text]" value="{{ $badge->requirement_text }}"></td><td><input type="checkbox" name="badges[{{ $badge->id }}][is_active]" @checked($badge->is_active)></td></tr>@endforeach</tbody></table></div><button class="admin-cms-save">Save Badges</button></form>
@endsection
