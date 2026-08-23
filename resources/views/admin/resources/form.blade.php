@extends('layouts.admin')

@section('title', $title ?? 'Edit')

@php
    use App\Support\AdminLabel;

    $normalise = function ($value) {
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

    $isLong = function (string $field, $value) use ($normalise): bool {
        $string = $normalise($value);

        return in_array($field, ['value', 'description', 'content', 'body', 'subtitle', 'hero_body'], true)
            || AdminLabel::isTechnical($field)
            || strlen($string) > 110
            || str_contains($string, "\n");
    };

    $allFields = collect($fields ?? []);
    $mainFields = $allFields->reject(fn (string $f): bool => AdminLabel::isTechnical($f))->values();
    $advancedFields = $allFields->filter(fn (string $f): bool => AdminLabel::isTechnical($f))->values();
@endphp

@section('content')
<div class="sb-res" data-admin-skip-unified>

    <nav class="sb-res__crumbs" aria-label="Breadcrumb">
        @if(isset($route) && Route::has($route.'.index'))
            <a href="{{ isset($parent) ? route($route.'.index', $parent) : route($route.'.index') }}">Back to list</a>
            <span aria-hidden="true">/</span>
        @endif
        <span aria-current="page">{{ $title ?? 'Edit' }}</span>
    </nav>

    <header class="sb-res__header">
        <div>
            <h1>{{ $title ?? 'Edit' }}</h1>
            <p>Changes go live on the website as soon as you save.</p>
        </div>
    </header>

    @if($errors->any())
        <div class="sb-res__note sb-res__note--bad" role="alert">
            <strong>We could not save that.</strong>
            <p>Check the highlighted fields and try again.</p>
        </div>
    @endif

    <form class="sb-res__form" method="POST" action="{{ $action ?? '#' }}">
        @csrf
        @if(($method ?? 'POST') === 'PUT') @method('PUT') @endif

        <section class="sb-res__card">
            <div class="sb-res__card-head">
                <h2>Details</h2>
            </div>

            <div class="sb-res__grid">
                @foreach($mainFields as $field)
                    @php
                        $raw = old($field, data_get($item, $field));
                        $value = $normalise($raw);
                        $label = AdminLabel::humanize($field);
                    @endphp

                    @if(AdminLabel::isBoolean($field))
                        {{-- A hidden partner means unticking actually saves "off". --}}
                        <div class="sb-res__field">
                            <input type="hidden" name="{{ $field }}" value="0">
                            <label class="sb-res__toggle">
                                <input type="checkbox" name="{{ $field }}" value="1"
                                       @checked(filter_var($value, FILTER_VALIDATE_BOOLEAN))>
                                <span>
                                    <strong>{{ $label }}</strong>
                                    <small>{{ AdminLabel::booleanLabel($field, true)[0] }} when ticked</small>
                                </span>
                            </label>
                            @error($field)<strong class="sb-res__error">{{ $message }}</strong>@enderror
                        </div>
                    @else
                        <label class="sb-res__field {{ $isLong($field, $raw) ? 'sb-res__field--wide' : '' }}">
                            <span>{{ $label }}</span>

                            @if($isLong($field, $raw))
                                <textarea name="{{ $field }}" rows="5" @error($field) aria-invalid="true" @enderror>{{ $value }}</textarea>
                            @else
                                <input name="{{ $field }}" value="{{ $value }}" @error($field) aria-invalid="true" @enderror>
                            @endif

                            @if(AdminLabel::isImage($field))
                                <small>A path on this site, or a full https:// address.</small>
                            @endif

                            @error($field)<strong class="sb-res__error">{{ $message }}</strong>@enderror
                        </label>
                    @endif
                @endforeach
            </div>
        </section>

        @if($advancedFields->isNotEmpty())
            <details class="sb-res__advanced">
                <summary>Advanced settings</summary>
                <p class="sb-res__advanced-hint">
                    These hold structured data. Leave them alone unless you know the format —
                    they must stay valid JSON.
                </p>

                <div class="sb-res__grid">
                    @foreach($advancedFields as $field)
                        @php($raw = old($field, data_get($item, $field)))
                        <label class="sb-res__field sb-res__field--wide">
                            <span>{{ AdminLabel::humanize($field) }}</span>
                            <textarea name="{{ $field }}" rows="7" spellcheck="false"
                                      @error($field) aria-invalid="true" @enderror>{{ $normalise($raw) }}</textarea>
                            @error($field)<strong class="sb-res__error">{{ $message }}</strong>@enderror
                        </label>
                    @endforeach
                </div>
            </details>
        @endif

        <div class="sb-res__form-actions">
            <button class="sb-res__btn sb-res__btn--primary" type="submit">Save changes</button>
            @if(isset($route) && Route::has($route.'.index'))
                <a class="sb-res__btn" href="{{ isset($parent) ? route($route.'.index', $parent) : route($route.'.index') }}">Cancel</a>
            @endif
        </div>
    </form>
</div>
@endsection
