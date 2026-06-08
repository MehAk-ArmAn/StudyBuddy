@extends('admin.layouts.admin')
@section('title', 'Page Content')
@section('heading', 'Page Content Manager')
@section('content')
<section class="admin-cms-panel"><h2>Public Pages</h2><div class="admin-cms-table"><table><thead><tr><th>Page</th><th>Sections</th><th>Blocks</th><th></th></tr></thead><tbody>@foreach($pages as $page)<tr><td>{{ $page->title }}</td><td>{{ $page->sections->count() }}</td><td>{{ $page->sections->sum(fn($s) => $s->blocks->count()) }}</td><td><a href="{{ route('admin.pages.edit', $page) }}">Edit</a></td></tr>@endforeach</tbody></table></div></section>
@endsection
