@extends('layouts.admin')

@section('title', $definition['label'] ?? '')

@section('content')
    <section class="admin-panel">
        @if(session('status'))<div class="admin-status">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="admin-errors">{{ $errors->first() }}</div>@endif
        <div class="admin-heading">
            <h1>{{ $definition['label'] ?? '' }}</h1>
            <a class="admin-button" href="{{ route('admin.resources.create', $resource) }}">{{ \App\Support\Cms::setting('admin.create_label') }}</a>
        </div>
        <form method="get" class="admin-search">
            <input name="search" value="{{ $search ?? '' }}">
            <button class="admin-button" type="submit">{{ \App\Support\Cms::setting('admin.search_label') }}</button>
        </form>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        @foreach($definition['columns'] as $column)<th>{{ str($column)->headline() }}</th>@endforeach
                        <th>{{ \App\Support\Cms::setting('admin.actions_label') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            @foreach($definition['columns'] as $column)<td>{{ data_get($item, $column) }}</td>@endforeach
                            <td>
                                <a class="admin-button" href="{{ route('admin.resources.edit', [$resource, $item]) }}">{{ \App\Support\Cms::setting('admin.edit_label') }}</a>
                                <form method="post" action="{{ route('admin.resources.destroy', [$resource, $item]) }}" class="inline-form">
                                    @csrf
                                    @method('delete')
                                    <button class="admin-button" type="submit">{{ \App\Support\Cms::setting('admin.delete_label') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </section>
@endsection
