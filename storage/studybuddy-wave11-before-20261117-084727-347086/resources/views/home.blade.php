@extends('layouts.app')
@section('title', 'StudyBuddy · Learn. Play. Grow. Your Way.')
{{-- Wave 10: original Laravel view replaced with a separate, reversible presentation partial. --}}
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-wave10.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-wave10.css')) }}">
@endpush
@section('content')
@include('partials.studybuddy-wave10-landing')
@endsection
@push('scripts')
<script defer src="{{ asset('assets/js/studybuddy-wave10.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-wave10.js')) }}"></script>
@endpush
