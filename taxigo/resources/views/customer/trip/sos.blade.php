@extends('customer.layouts.app')

@php($withNav = false)
@section('title', 'Emergency Safety SOS — Seema Cabs Goa')

@section('content')
<div class="min-h-screen flex flex-col justify-between px-5 py-6 bg-gradient-to-b from-[#FFF5F5] via-[#FFF9F6] to-sand/40"
     x-data="{ fleetPhone: @js($fleetPhone ?? '') }">

    <!-- TOP HEADER -->
    <div class="flex items-center justify-between">
        <a href="{{ route('customer.trip', $bookingId) }}" class="w-11 h-11 rounded-2xl bg-white border border-sand shadow-sm flex items-center justify-center text-ink transition-transform active:scale-95">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
        </a>
        <div class="inline-flex items-center gap-2 bg-red-100/80 border border-danger/20 px-3 py-1 rounded-full text-xs font-bold text-danger">
            <span class="w-2 h-2 rounded-full bg-danger animate-ping"></span>
            <span>Emergency Mode</span>
        </div>
        <div class="w-11"></div>
    </div>

    <!-- MAIN SOS ACTION -->
    <div class="text-center my-auto py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-black text-ink tracking-tight">Need Immediate Help?</h1>
            <p class="text-xs text-muted mt-1.5 max-w-xs mx-auto">Tap below to directly dial the Goa Police Control Room for immediate roadside assistance.</p>
        </div>

        <a href="tel:100" class="relative inline-flex items-center justify-center mx-auto transition-transform active:scale-95" style="width:230px;height:230px;">
            <span class="absolute inset-0 rounded-full bg-danger/10 animate-ping opacity-75"></span>
            <span class="absolute inset-3 rounded-full bg-danger/15"></span>
            <span class="absolute inset-7 rounded-full bg-white shadow-sheet"></span>
            <span class="relative w-32 h-32 rounded-full bg-gradient-to-br from-[#E0263C] to-[#B8182B] flex flex-col items-center justify-center text-white shadow-2xl gap-1.5 border-4 border-white">
                <svg class="w-9 h-9" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 1 21h22L12 2Zm1 14h-2v2h2v-2Zm0-6h-2v4h2v-4Z"/></svg>
                <span class="text-xs font-black tracking-widest uppercase">Call 100</span>
            </span>
        </a>

        <p class="text-[11px] font-bold text-muted uppercase tracking-wider mt-5">Goa Police Emergency Hotline</p>
    </div>

    <!-- SECONDARY DISPATCH FLEET OPERATOR CONTROL -->
    <div class="card-bezel p-1.5">
        <div class="card-core p-4 flex items-center justify-between gap-3 bg-white">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8c1.4 2.7 3.6 4.9 6.3 6.3l2.1-2.1c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.5.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.5.1.4 0 .8-.2 1L6.6 10.8Z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-ink text-xs">Seema Cabs Control Room</h3>
                    <p class="text-[11px] text-muted">24/7 Dispatch Team &amp; Fleet Help</p>
                </div>
            </div>
            <a :href="fleetPhone ? ('tel:' + fleetPhone) : '#'"
               class="py-2.5 px-4 rounded-xl font-bold text-xs bg-ink text-white shadow-sm flex items-center gap-1.5 shrink-0 transition-transform active:scale-95">
                <span>Call Fleet</span>
                <svg class="w-3.5 h-3.5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</div>
@endsection

