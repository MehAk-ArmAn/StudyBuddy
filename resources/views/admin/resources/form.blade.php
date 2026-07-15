@extends('layouts.admin')

@section('title', $title ?? 'Edit')

@section('content')
@php
    $normaliseAdminValue = function ($value) {
        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->toArray();
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string) ($value ?? '');
    };

    $looksLong = function ($field, $value) use ($normaliseAdminValue) {
        $string = $normaliseAdminValue($value);

        return in_array($field, [
            'value','description','content','body','settings','metadata','meta',
            'role_profile','child_emails','settings_json','metrics_json'
        ], true) || strlen($string) > 110 || str_contains($string, "\n");
    };
@endphp

<section class="sb-control-resource">
    <form class="sb-control-panel sb-control-form" method="POST" action="{{ $action ?? '#' }}">
        @csrf

        @if(($method ?? 'POST') === 'PUT')
            @method('PUT')
        @endif

        <div class="sb-control-panel-head wide">
            <div>
                <p class="sb-control-kicker">Editor</p>
                <h2>{{ $title ?? 'Edit' }}</h2>
                <p>JSON fields are safely shown as editable JSON, so admin pages do not crash.</p>
            </div>

            <div class="sb-control-row-actions">
                @if(isset($route) && Route::has($route.'.index'))
                    <a href="{{ route($route.'.index') }}">Back</a>
                @endif
                <button class="primary" type="submit">Save</button>
            </div>
        </div>

        <div class="sb-control-form-grid">
            @foreach(($fields ?? []) as $field)
                @php
                    $rawValue = old($field, data_get($item, $field));
                    $value = $normaliseAdminValue($rawValue);
                    $isLong = $looksLong($field, $rawValue);
                    $fieldLabel = str($field)->replace('_', ' ')->title();
                @endphp

                <label class="{{ $isLong ? 'wide' : '' }}">
                    <span>{{ $fieldLabel }}</span>

                    @if($isLong)
                        <textarea name="{{ $field }}" rows="8">{{ $value }}</textarea>
                    @else
                        <input name="{{ $field }}" value="{{ $value }}">
                    @endif

                    @if(is_array($rawValue) || is_object($rawValue))
                        <small class="admin-json-hint">JSON field — keep valid JSON when editing.</small>
                    @endif

                    @error($field)
                        <small>{{ $message }}</small>
                    @enderror
                </label>
            @endforeach
        </div>
    </form>
</section>
@endsection
