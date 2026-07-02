@extends('layouts.admin')

@section('title', $title ?? 'Admin Form')

@section('content')
<section class="sb-control-resource">
    <form class="sb-control-panel sb-control-form" method="POST" action="{{ ($method ?? 'POST') === 'PUT' ? route($route . '.update', $item) : route($route . '.store') }}">
        @csrf
        @if(($method ?? 'POST') === 'PUT') @method('PUT') @endif

        <div class="sb-control-panel-head wide">
            <div><p class="sb-control-kicker">Editor</p><h2>{{ $title ?? 'Edit' }}</h2><p>Keep data clean and easy to understand.</p></div>
            <div class="sb-control-row-actions">
                @if(isset($route) && Route::has($route . '.index'))<a href="{{ route($route . '.index') }}">Back</a>@endif
                <button class="primary" type="submit">Save</button>
            </div>
        </div>

        <div class="sb-control-form-grid">
            @foreach(($fields ?? []) as $field)
                @php
                    $value = old($field, data_get($item, $field));
                    $isLong = in_array($field, ['value', 'description', 'content', 'settings']) || strlen((string) $value) > 110;
                @endphp
                <label class="{{ $isLong ? 'wide' : '' }}">
                    <span>{{ str($field)->replace('_', ' ')->title() }}</span>
                    @if($isLong)<textarea name="{{ $field }}" rows="7">{{ $value }}</textarea>@else<input name="{{ $field }}" value="{{ $value }}">@endif
                    @error($field)<small>{{ $message }}</small>@enderror
                </label>
            @endforeach
        </div>
    </form>
</section>
@endsection
