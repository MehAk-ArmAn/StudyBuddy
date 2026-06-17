@extends('layouts.admin')

@section('content')
    @php
        $parentParams = $parentParams ?? (isset($parent) ? (is_array($parent) ? $parent : [$parent]) : []);
    @endphp

    <h2>{{ $title }}</h2>
    <a class="button" href="{{ route($route.'.create', $parentParams) }}">Create</a>

    <table>
        <thead>
            <tr>
                @foreach($fields as $field)
                    <th>{{ $field }}</th>
                @endforeach
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    @foreach($fields as $field)
                        <td>{{ is_array($item->$field) ? json_encode($item->$field) : \Illuminate\Support\Str::limit((string) $item->$field, 55) }}</td>
                    @endforeach
                    <td>
                        <a href="{{ route($route.'.edit', array_merge($parentParams, [$item])) }}">Edit</a>

                        @if($route === 'admin.homepage-sections')
                            <a href="{{ route('admin.homepage-sections.items.index', $item) }}">Items</a>
                        @endif

                        @if($route === 'admin.pages')
                            <a href="{{ route('admin.pages.sections.index', $item) }}">Sections</a>
                        @endif

                        @if($route === 'admin.pages.sections')
                            <a href="{{ route('admin.pages.sections.items.index', array_merge($parentParams, [$item])) }}">Items</a>
                        @endif

                        <form method="POST" action="{{ route($route.'.destroy', array_merge($parentParams, [$item])) }}">
                            @csrf
                            @method('DELETE')
                            <button>Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $items->links() }}
@endsection
