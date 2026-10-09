@extends('customer.layouts.app')

@php($withNav = false)
@section('title', ($state === 'paid' ? 'Booking Confirmed' : 'Payment') . ' — Seema Cabs Goa')

@section('content')
<div class="pt-10 pb-10 px-6">
    <div class="card-bezel">
        <div class="card-core p-7 text-center">
            @if ($state === 'paid')
                <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <h1 class="text-lg font-extrabold text-ink mb-1">Booking Confirmed!</h1>
                <p class="text-xs text-muted mb-4">Your payment was received and your ride is booked.</p>
                <div class="bg-cream rounded-2xl p-4 mb-5 text-xs space-y-1.5 border border-black/[0.04] text-left">
                    <div class="flex justify-between"><span class="text-muted">Booking ID</span><span class="font-bold text-ink font-mono">{{ $booking->booking_id }}</span></div>
                    <div class="flex justify-between"><span class="text-muted">Start OTP</span><span class="font-extrabold text-ink font-mono tracking-widest">{{ $booking->trip_otp }}</span></div>
                    @if ((float) $booking->total_payment > (float) $booking->part_payment)
                        <div class="flex justify-between pt-1 border-t border-black/[0.05]"><span class="text-muted">Pay driver at drop</span><span class="font-bold text-ink">&#8377;{{ number_format((float) $booking->total_payment - (float) $booking->part_payment, 0) }}</span></div>
                    @endif
                </div>
                <a href="{{ route('customer.trip', $booking->id) }}" class="btn-primary w-full py-3 text-sm font-bold">View Trip Details</a>
            @else
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-danger" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </div>
                <h1 class="text-lg font-extrabold text-ink mb-1">Payment not completed</h1>
                <p class="text-xs text-muted mb-2">{{ $message }}</p>
                <p class="text-xs text-muted mb-5">If money was deducted, it will be confirmed automatically within a few minutes or refunded by the bank.</p>
                @if ($booking)
                    <a href="{{ route('customer.trip', $booking->id) }}" class="btn-primary w-full py-3 text-sm font-bold mb-2">Check booking status</a>
                @endif
                <a href="{{ route('customer.book') }}" class="block text-xs font-semibold text-muted py-2">Book again</a>
            @endif
        </div>
    </div>
</div>
@endsection
