@extends('layouts.admin')

@section('title', Str::headline($module ?? ''))

@section('content')
    <section class="admin-panel">
        <p>{{ $table ?? '' }}</p>
        @include('admin.partials.image-path-help')
    </section>
@endsection
