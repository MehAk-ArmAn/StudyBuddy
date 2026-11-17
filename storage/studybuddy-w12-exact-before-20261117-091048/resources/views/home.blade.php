@extends('layouts.app')
@section('title', 'StudyBuddy · Learn. Play. Grow. Your Way.')
{{-- Wave 11: original Laravel view replaced with a separate, reversible presentation partial. --}}
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-wave11.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-wave11.css')) }}">
@endpush
@section('content')
@include('partials.studybuddy-wave11-landing')
@endsection
@push('scripts')
<script defer src="{{ asset('assets/js/studybuddy-wave11.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-wave11.js')) }}"></script>
@endpush
