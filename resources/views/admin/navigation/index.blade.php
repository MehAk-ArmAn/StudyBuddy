@extends('admin.layouts.admin')
@section('title', 'Navigation')
@section('heading', 'Navigation Manager')
@section('content')
<form method="POST" action="{{ route('admin.navigation.save') }}" class="admin-cms-panel">@csrf @method('PUT')<div class="admin-cms-table"><table><thead><tr><th>Label</th><th>URL</th><th>Route</th><th>Order</th><th>Enabled</th></tr></thead><tbody>@foreach($items as $item)<tr><td><input name="items[{{ $item->id }}][label]" value="{{ $item->label }}"></td><td><input name="items[{{ $item->id }}][url]" value="{{ $item->url }}"></td><td><input name="items[{{ $item->id }}][route_name]" value="{{ $item->route_name }}"></td><td><input name="items[{{ $item->id }}][sort_order]" value="{{ $item->sort_order }}" type="number"></td><td><input name="items[{{ $item->id }}][is_enabled]" type="checkbox" @checked($item->is_enabled)></td></tr>@endforeach</tbody></table></div><button class="admin-cms-save">Save Navigation</button></form>
@endsection
