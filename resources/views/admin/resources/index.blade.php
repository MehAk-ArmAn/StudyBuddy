@extends('layouts.admin')

@section('title', $title ?? 'Admin Resource')

@section('content')
<section class="sb-control-resource">
    <div class="sb-control-panel">
        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Resource</p>
                <h2>{{ $title ?? 'Items' }}</h2>
                @isset($description)<p>{{ $description }}</p>@endisset
            </div>
            <div class="sb-control-row-actions">
                @foreach(($quick_links ?? []) as $link)<a href="{{ $link['url'] ?? '#' }}">{{ $link['label'] ?? 'Open' }}</a>@endforeach
                @if(isset($route) && Route::has($route . '.create'))<a class="primary" href="{{ route($route . '.create') }}">Create new</a>@endif
            </div>
        </div>
        <div class="sb-control-table-wrap"><table class="sb-control-table"><thead><tr>@foreach(($fields ?? []) as $field)<th>{{ str($field)->replace('_', ' ')->title() }}</th>@endforeach<th>Actions</th></tr></thead><tbody>
        @forelse($items as $item)
            <tr>@foreach(($fields ?? []) as $field)@php $value=data_get($item,$field); if(is_array($value)||is_object($value))$value=json_encode($value,JSON_UNESCAPED_SLASHES); @endphp<td title="{{ $value }}">{{ str($value ?? '—')->limit(86) }}</td>@endforeach<td><div class="sb-control-table-actions">@if(isset($route)&&Route::has($route.'.edit'))<a href="{{ route($route.'.edit',$item) }}">Edit</a>@endif @if(isset($route)&&Route::has($route.'.destroy'))<form method="POST" action="{{ route($route.'.destroy',$item) }}" onsubmit="return confirm('Delete this item?')">@csrf @method('DELETE')<button type="submit">Delete</button></form>@endif</div></td></tr>
        @empty
            <tr><td colspan="{{ count($fields ?? []) + 1 }}"><div class="sb-control-empty">No records yet.</div></td></tr>
        @endforelse
        </tbody></table></div>
        @if(method_exists($items, 'links'))<div class="sb-control-pagination">{{ $items->links() }}</div>@endif
    </div>
</section>
@endsection
