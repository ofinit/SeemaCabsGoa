@extends('customer.layouts.app')

@php($withNav = false)
@section('title', 'Seema Cabs Goa')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-cream">
    <img src="{{ asset('app-icons/splash.svg') }}" alt="Seema Cabs Goa" class="w-full h-auto max-h-screen object-contain">
</div>
@endsection

@push('scripts')
<script>
    setTimeout(() => { window.location.href = '{{ $next }}'; }, 1800);
</script>
@endpush
