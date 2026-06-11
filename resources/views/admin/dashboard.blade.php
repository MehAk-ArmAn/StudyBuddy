@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <section class="admin-panel">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <tbody>
                    @foreach(config('admin_cms.resources') as $key => $resource)
                        <tr>
                            <td>{{ $resource['label'] ?? '' }}</td>
                            <td><a class="admin-button" href="{{ route('admin.resources.index', $key) }}">{{ \App\Support\Cms::setting('admin.manage_label') }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
