@extends('layouts.admin')

@section('title', $title ?? 'Admin Resource')

@php
    use App\Support\AdminLabel;

    $records = $items ?? collect();

    // Technical columns stay out of the summary table; they are still editable
    // on the record itself, under Advanced.
    $columns = collect($fields ?? [])
        ->reject(fn (string $field): bool => AdminLabel::isTechnical($field))
        ->values();

    // Drop columns that tell the reader nothing on this page — every row empty,
    // or every row identical (a sort order of 0 all the way down). The first
    // column always stays, since it identifies the row.
    $columns = $columns
        ->filter(function (string $field, int $index) use ($records): bool {
            if ($index === 0 || $records->isEmpty()) {
                return true;
            }

            $values = $records->map(fn ($row) => data_get($row, $field))
                ->map(fn ($value) => is_scalar($value) || $value === null ? (string) $value : json_encode($value));

            return $values->filter(fn ($value) => $value !== '' && $value !== null)->isNotEmpty()
                && $values->unique()->count() > 1;
        })
        ->values();
    $singular = \Illuminate\Support\Str::singular(\Illuminate\Support\Str::of($title ?? 'item')->after(':')->trim());

    $preview = function ($value) {
        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->toArray();
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return (string) ($value ?? '');
    };

    $imageSrc = function (?string $path): ?string {
        if (! filled($path)) {
            return null;
        }

        return \Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/'])
            ? $path
            : asset($path);
    };
@endphp

@section('content')
<div class="sb-res" data-admin-skip-unified>

    <header class="sb-res__header">
        <div>
            <h1>{{ $title ?? 'Items' }}</h1>
            <p>{{ $intro ?? 'Everything here appears on the public website.' }}</p>
        </div>

        @if(isset($route) && Route::has($route.'.create'))
            <a class="sb-res__btn sb-res__btn--primary"
               href="{{ isset($parent) ? route($route.'.create', $parent) : route($route.'.create') }}">
                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <path d="M12 5v14M5 12h14"></path>
                </svg>
                Add {{ \Illuminate\Support\Str::lower($singular) }}
            </a>
        @endif
    </header>

    @if(session('status'))
        <div class="sb-res__note sb-res__note--good" role="status">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="sb-res__note sb-res__note--bad" role="alert">
            <strong>We could not save that.</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if($records->isEmpty())
        <div class="sb-res__empty">
            <span class="sb-res__empty-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.6">
                    <rect x="3" y="4" width="18" height="16" rx="3"></rect>
                    <path d="M3 10h18M9 20V10"></path>
                </svg>
            </span>
            <h2>Nothing here yet</h2>
            <p>Add the first one and it will show up on the website straight away.</p>
            @if(isset($route) && Route::has($route.'.create'))
                <a class="sb-res__btn sb-res__btn--primary"
                   href="{{ isset($parent) ? route($route.'.create', $parent) : route($route.'.create') }}">
                    Add {{ \Illuminate\Support\Str::lower($singular) }}
                </a>
            @endif
        </div>
    @else
        <div class="sb-res__card">
            <div class="sb-res__table-wrap">
                <table class="sb-res__table">
                    <caption class="sb-visually-hidden">{{ $title ?? 'Items' }}</caption>
                    <thead>
                        <tr>
                            @foreach($columns as $field)
                                <th scope="col">{{ AdminLabel::humanize($field) }}</th>
                            @endforeach
                            <th scope="col"><span class="sb-visually-hidden">Actions</span></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($records as $item)
                            <tr>
                                @foreach($columns as $index => $field)
                                    @php($raw = data_get($item, $field))
                                    <{{ $index === 0 ? 'th scope=row' : 'td' }} data-label="{{ AdminLabel::humanize($field) }}">
                                        @if(AdminLabel::isBoolean($field))
                                            @php([$badge, $on] = AdminLabel::booleanLabel($field, $raw))
                                            <span class="sb-res__badge {{ $on ? 'is-on' : 'is-off' }}">{{ $badge }}</span>

                                        @elseif(AdminLabel::isImage($field) && $imageSrc($preview($raw)))
                                            <span class="sb-res__thumb">
                                                <img src="{{ $imageSrc($preview($raw)) }}" alt=""
                                                     loading="lazy" onerror="this.closest('.sb-res__thumb').classList.add('is-missing');this.remove()">
                                            </span>

                                        @elseif(filled($preview($raw)))
                                            <span class="sb-res__cell">{{ \Illuminate\Support\Str::limit($preview($raw), 70) }}</span>
                                        @else
                                            <span class="sb-res__muted">—</span>
                                        @endif
                                    </{{ $index === 0 ? 'th' : 'td' }}>
                                @endforeach

                                <td data-label="Actions">
                                    <div class="sb-res__actions">
                                        @if(isset($route) && Route::has($route.'.edit'))
                                            <a class="sb-res__btn sb-res__btn--small"
                                               href="{{ isset($parent) ? route($route.'.edit', [$parent, $item]) : route($route.'.edit', $item) }}">
                                                Edit
                                            </a>
                                        @endif

                                        @if(isset($route) && Route::has($route.'.destroy'))
                                            <form method="POST"
                                                  action="{{ isset($parent) ? route($route.'.destroy', [$parent, $item]) : route($route.'.destroy', $item) }}"
                                                  onsubmit="return confirm('Delete “{{ AdminLabel::describe($item) }}”? This cannot be undone.')">
                                                @csrf @method('DELETE')
                                                <button class="sb-res__btn sb-res__btn--small sb-res__btn--danger" type="submit">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($records, 'links'))
            <div class="sb-res__pagination">{{ $records->links() }}</div>
        @endif
    @endif
</div>
@endsection
