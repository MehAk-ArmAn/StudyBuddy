@extends('admin.layouts.admin')
@section('title', $page->title)
@section('heading', $page->title)
@section('content')
<form method="POST" action="{{ route('admin.pages.update', $page) }}" class="admin-cms-panel">@csrf @method('PUT')
    @foreach($page->sections as $section)<section class="admin-cms-edit-section"><h2>{{ $section->title }}</h2>@foreach($section->blocks as $block)<label>{{ $block->label }}<textarea name="blocks[{{ $block->id }}]" rows="{{ $block->type === 'textarea' ? 4 : 2 }}">{{ old('blocks.'.$block->id, $block->value) }}</textarea></label>@endforeach</section>@endforeach
    <button class="admin-cms-save" type="submit">Save Page Content</button>
</form>
@endsection
