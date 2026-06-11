@extends('layouts.admin')

@section('title', $definition['label'] ?? '')

@section('content')
    <section class="admin-panel">
        @if($errors->any())<div class="admin-errors">{{ $errors->first() }}</div>@endif
        <form method="post" action="{{ $item->exists ? route('admin.resources.update', [$resource, $item]) : route('admin.resources.store', $resource) }}">
            @csrf
            @if($item->exists) @method('put') @endif
            @foreach($definition['fields'] as $name => $field)
                @include('admin.partials.form-field', ['name' => $name, 'field' => $field, 'value' => old($name, data_get($item, $name))])
            @endforeach
            <button class="admin-button" type="submit">{{ \App\Support\Cms::setting('admin.save_label') }}</button>
        </form>
    </section>
@endsection
