@extends('admin.layouts.admin')
@section('title', 'Mobile Preview')
@section('heading', 'Mobile Preview Manager')
@section('content')
<form method="POST" action="{{ route('admin.mobile-preview.save') }}" class="admin-cms-panel">@csrf @method('PUT')@foreach($items as $group => $rows)<section class="admin-cms-edit-section"><h2>{{ ucfirst(str_replace('_',' ', $group)) }}</h2>@foreach($rows as $item)<div class="admin-cms-row"><input name="items[{{ $item->id }}][title]" value="{{ $item->title }}"><textarea name="items[{{ $item->id }}][description]">{{ $item->description }}</textarea></div>@endforeach</section>@endforeach<button class="admin-cms-save">Save Mobile Preview</button></form>
@endsection
