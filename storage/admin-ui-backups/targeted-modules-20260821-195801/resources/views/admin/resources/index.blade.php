@extends('layouts.admin')

@section('title', $title ?? 'Admin Resource')

@section('content')
@php
    $normaliseAdminValue = function ($value) {
        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->toArray();
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        return (string) ($value ?? '');
    };

    $shortValue = function ($value) use ($normaliseAdminValue) {
        return str($normaliseAdminValue($value))->limit(90);
    };
@endphp

<section class="sb-control-resource">
    <div class="sb-control-panel">
        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Admin Resource</p>
                <h2>{{ $title ?? 'Items' }}</h2>
                <p>Review and edit DB-managed content safely.</p>
            </div>

            <div class="sb-control-row-actions">
                @if(isset($route) && Route::has($route.'.create'))
                    @if(isset($parent))
                        <a class="primary" href="{{ route($route.'.create', $parent) }}">Create</a>
                    @else
                        <a class="primary" href="{{ route($route.'.create') }}">Create</a>
                    @endif
                @endif
            </div>
        </div>

        <div class="sb-control-table-wrap">
            <table class="sb-control-table">
                <thead>
                    <tr>
                        @foreach(($fields ?? []) as $field)
                            <th>{{ str($field)->replace('_', ' ')->title() }}</th>
                        @endforeach
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse(($items ?? collect()) as $item)
                        <tr>
                            @foreach(($fields ?? []) as $field)
                                <td>{{ $shortValue(data_get($item, $field)) }}</td>
                            @endforeach

                            <td>
                                <div class="sb-control-row-actions">
                                    @if(isset($route) && Route::has($route.'.edit'))
                                        @if(isset($parent))
                                            <a href="{{ route($route.'.edit', [$parent, $item]) }}">Edit</a>
                                        @else
                                            <a href="{{ route($route.'.edit', $item) }}">Edit</a>
                                        @endif
                                    @endif

                                    @if(isset($route) && Route::has($route.'.destroy'))
                                        <form method="POST" action="{{ isset($parent) ? route($route.'.destroy', [$parent, $item]) : route($route.'.destroy', $item) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this item?')">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($fields ?? []) + 1 }}">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists(($items ?? null), 'links'))
            <div class="sb-control-pagination">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
