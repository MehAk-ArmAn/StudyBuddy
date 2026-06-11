@extends('admin.layouts.admin')
@section('title', 'Assets Library')
@section('heading', 'Assets Library')
@section('content')
<section class="admin-cms-panel">
    <h2>Asset path strategy</h2>
    @include('admin.partials.image-path-help')
    <div class="admin-cms-table"><table><thead><tr><th>Preview</th><th>Name</th><th>Path</th><th>Status</th></tr></thead><tbody>@foreach($assets as $asset)@php($previewUrl = \App\Support\Cms::imageUrl($asset->path))<tr><td>@if($previewUrl)<img class="admin-asset-thumb" src="{{ $previewUrl }}" alt="{{ $asset->name }}" onerror="this.hidden = true; this.nextElementSibling.hidden = false;"><span class="admin-asset-missing" hidden></span>@else<span class="admin-asset-missing"></span>@endif</td><td>{{ $asset->name }}</td><td>{{ $asset->path }}</td><td>{{ $previewUrl ? 'Available' : 'Missing' }}</td></tr>@endforeach</tbody></table></div>
</section>
@endsection
