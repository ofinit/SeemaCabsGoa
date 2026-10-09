@extends('customer.layouts.app')

@php($withNav = false)
@section('title', 'Seema Cabs Goa')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-cream relative">
    <img src="{{ asset('app-icons/splash.svg') }}" alt="Seema Cabs Goa" class="w-full h-auto max-h-screen object-contain">
    @if($sponsor ?? null)
        {{-- P16 app-open sponsor --}}
        <div class="absolute inset-x-0 bottom-10 flex justify-center px-6">
            <a href="{{ $sponsor['click_url'] ?: '#' }}" target="_blank" rel="noopener sponsored" data-ad-id="{{ $sponsor['id'] }}" data-ad-screen="14"
               class="flex items-center gap-3 bg-white/90 rounded-2xl px-4 py-3 shadow-card max-w-xs">
                <img src="{{ $sponsor['banner_image'] }}" class="w-12 h-12 rounded-xl object-cover" alt="">
                <span class="text-left">
                    <span class="ad-report block text-[10px] uppercase tracking-wider text-muted" role="button">Presented by · Sponsored ⓘ</span>
                    <span class="block text-sm font-semibold text-ink">{{ $sponsor['headline'] }}</span>
                </span>
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    setTimeout(() => { window.location.href = '{{ $next }}'; }, 1800);
</script>
@endpush
