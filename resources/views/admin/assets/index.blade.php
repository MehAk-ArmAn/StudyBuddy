@extends('admin.layouts.admin')
@section('title', 'Assets Library')
@section('heading', 'Assets Library')
@section('content')
<section class="admin-cms-panel"><div class="admin-cms-table"><table><thead><tr><th>Preview</th><th>Name</th><th>Path</th><th>Status</th></tr></thead><tbody>@foreach($assets as $asset)<tr><td>@if(\App\Support\Cms::assetExists($asset->path))<img class="admin-asset-thumb" src="{{ asset($asset->path) }}" alt="{{ $asset->name }}">@else<span class="admin-asset-missing"></span>@endif</td><td>{{ $asset->name }}</td><td>{{ $asset->path }}</td><td>{{ \App\Support\Cms::assetExists($asset->path) ? 'Available' : 'Missing' }}</td></tr>@endforeach</tbody></table></div></section>
@endsection
