@extends('customer.layouts.app')

@php($withNav = false)
@section('title', 'Your Driver & Ride — Seema Cabs Goa')

@section('content')
<div class="pt-4 pb-12" x-data="tripDetail('{{ $bookingId }}', @js($booking ?? null), @js($error ?? ''))">
    @include('customer.components.topbar', ['title' => 'Trip Status', 'back' => route('customer.rides'), 'right' => ''])

    <div class="px-4">
        <div x-show="loading" style="display:none" class="py-12">
            @include('customer.components.spinner')
        </div>
        <p x-show="error" x-text="error" class="text-center text-danger text-sm py-10"></p>

        <template x-if="booking">
            <div class="space-y-4">
                <!-- HERO OTP / DRIVER ASSIGNMENT CARD -->
                <template x-if="booking.assignDriver">
                    <div class="card-bezel bg-ink text-white p-2">
                        <div class="card-core bg-gradient-to-br from-[#1A1C23] to-[#0E1015] p-5 text-white border border-white/10">
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-white/10">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-sand/80">Driver On Duty</span>
                                </div>
                                <div class="bg-gold/15 text-gold border border-gold/30 px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase">
                                    Confirmed
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center shrink-0">
                                        <svg class="w-8 h-8 text-gold" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 2c-4 0-9 2-9 6v2h18v-2c0-4-5-6-9-6Z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-base text-white" x-text="booking.assignDriver.driver_name"></p>
                                        <p class="text-xs text-sand/70 mt-0.5" x-text="(booking.getCabDetails ? booking.getCabDetails.number : '')"></p>
                                        <p class="text-[11px] text-gold/90 font-medium mt-0.5" x-text="booking.cab_name + ' • ' + booking.model"></p>
                                    </div>
                                </div>

                                <div class="text-right shrink-0 bg-white/5 border border-white/10 rounded-2xl px-4 py-2.5">
                                    <p class="text-[10px] uppercase font-bold tracking-widest text-sand/60">Start OTP</p>
                                    <p class="text-2xl font-black tracking-widest text-gold tabular-nums" x-text="booking.trip_otp"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="!booking.assignDriver">
                    <div class="card-bezel p-1.5">
                        <div class="card-core p-5 text-center bg-sand/30">
                            <div class="w-12 h-12 rounded-2xl bg-gold/20 text-ink flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"/></svg>
                            </div>
                            <h3 class="font-bold text-ink text-base">Booking Confirmed</h3>
                            <p class="text-xs text-muted mt-1 leading-relaxed">Our dispatch team is assigning a premium chauffeur near your location.</p>
                            <div class="mt-4 inline-flex items-center gap-3 bg-white px-4 py-2 rounded-2xl border border-sand shadow-sm">
                                <span class="text-xs font-semibold text-muted uppercase tracking-wider">Your Ride OTP</span>
                                <span class="text-lg font-black text-ink tracking-widest tabular-nums" x-text="booking.trip_otp"></span>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Ads (P5 booking confirmed / P6 driver details / P7 ride complete); never above the driver card or SOS. --}}
                <template x-if="!booking.assignDriver && booking.status != 3 && booking.status != 4 && booking.status != 5">
                    <div>@include('customer.components.ad-slot', ['screen' => \App\Services\Ads\AdServer::SCREEN_BOOKING_CONFIRMED])</div>
                </template>
                <template x-if="booking.assignDriver && booking.status != 3 && booking.status != 4 && booking.status != 5">
                    <div>@include('customer.components.ad-slot', ['screen' => \App\Services\Ads\AdServer::SCREEN_DRIVER_DETAILS])</div>
                </template>
                <template x-if="booking.status == 4">
                    <div>@include('customer.components.ad-slot', ['screen' => \App\Services\Ads\AdServer::SCREEN_RATING])</div>
                </template>

                <!-- QUICK ACTIONS (CALL DRIVER / SOS) -->
                <div class="grid grid-cols-2 gap-3">
                    <a :href="booking.assignDriver ? ('tel:' + booking.assignDriver.driver_mobile) : '#'"
                       class="flex items-center justify-center gap-2.5 py-3.5 px-4 rounded-2xl font-bold text-xs bg-ink text-white shadow-card transition-all active:scale-[0.98]"
                       :class="!booking.assignDriver && 'opacity-40 pointer-events-none'">
                        <div class="w-7 h-7 rounded-xl bg-white/10 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-gold" viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8c1.4 2.7 3.6 4.9 6.3 6.3l2.1-2.1c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.5.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.5.1.4 0 .8-.2 1L6.6 10.8Z"/></svg>
                        </div>
                        Call Chauffeur
                    </a>
                    <a :href="'/app/trip/' + bookingId + '/sos'"
                       class="flex items-center justify-center gap-2.5 py-3.5 px-4 rounded-2xl font-bold text-xs bg-red-50 text-danger border border-danger/25 transition-all active:scale-[0.98]">
                        <div class="w-7 h-7 rounded-xl bg-danger/10 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-danger" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 1 21h22L12 2Zm1 14h-2v2h2v-2Zm0-6h-2v4h2v-4Z"/></svg>
                        </div>
                        Emergency SOS
                    </a>
                </div>

                <!-- ROUTE DETAILS WITH WAYPOINT SPINE -->
                <div class="card-bezel p-1.5">
                    <div class="card-core p-4">
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-sand/40">
                            <h3 class="font-bold text-ink text-sm">Route Itinerary</h3>
                            <span class="text-[11px] font-semibold text-muted" x-text="'ID: #' + booking.booking_id"></span>
                        </div>

                        <div class="flex items-stretch gap-3 text-xs">
                            <div class="flex flex-col items-center py-0.5 shrink-0 w-3.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100 shrink-0"></span>
                                <div class="w-0.5 flex-1 my-1 border-l-2 border-dashed border-gray-300"></div>
                                <span class="w-2.5 h-2.5 rounded-[3px] bg-gold ring-4 ring-gold/20 shrink-0"></span>
                            </div>
                            <div class="flex-1 space-y-3 min-w-0">
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-muted">Pickup Location</p>
                                    <p class="text-ink font-semibold text-xs mt-0.5 leading-snug break-words" x-text="booking.pickup_address || (booking.getPickupFrom && booking.getPickupFrom.name)"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-muted">Dropoff Location</p>
                                    <p class="text-ink font-semibold text-xs mt-0.5 leading-snug break-words" x-text="booking.drop_of_address || (booking.getDropTo && booking.getDropTo.name)"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VEHICLE SPECS -->
                <div class="card-bezel p-1.5">
                    <div class="card-core p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="font-bold text-ink text-sm" x-text="booking.cab_name"></h3>
                                <p class="text-xs text-muted" x-text="booking.model"></p>
                            </div>
                            <span class="bg-sand/60 text-ink/80 text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                Clean Cab Guaranteed
                            </span>
                        </div>
                        <div class="flex items-center gap-2 pt-2 border-t border-sand/40">
                            <span class="inline-flex items-center gap-1.5 bg-sand/30 border border-sand px-3 py-1.5 rounded-xl text-xs font-semibold text-ink" x-text="booking.seat + ' Seats'"></span>
                            <span class="inline-flex items-center gap-1.5 bg-sand/30 border border-sand px-3 py-1.5 rounded-xl text-xs font-semibold text-ink" x-text="booking.ac"></span>
                            <span class="inline-flex items-center gap-1.5 bg-sand/30 border border-sand px-3 py-1.5 rounded-xl text-xs font-semibold text-ink" x-text="booking.baggage + ' Bags'"></span>
                        </div>
                    </div>
                </div>

                <!-- FARE BREAKDOWN -->
                <div class="card-bezel p-1.5">
                    <div class="card-core p-4">
                        <h3 class="font-bold text-ink text-sm mb-3">Fare Breakdown</h3>
                        <div class="space-y-2 text-xs">
                            {{-- v1 bookings stored the internal markup as "tax"; show their all-inclusive fare only. --}}
                            <div class="flex justify-between text-muted">
                                <span>Fare (all-inclusive)</span>
                                <span class="font-semibold text-ink tabular-nums" x-text="'₹' + (booking.pricing_version >= 2 ? booking.base_fare : booking.total_payment)"></span>
                            </div>
                            <div class="flex justify-between text-muted" x-show="booking.pricing_version >= 2 && parseFloat(booking.tax_amount) > 0" style="display:none">
                                <span x-text="'GST @ ' + parseFloat(booking.gst_rate) + '% (CGST + SGST)'"></span>
                                <span class="font-semibold text-ink tabular-nums" x-text="'₹' + booking.tax_amount"></span>
                            </div>
                            <div class="flex justify-between items-center font-bold text-sm text-ink border-t border-sand/60 pt-2.5">
                                <span>Total Trip Fare</span>
                                <span class="text-base text-ink tabular-nums" x-text="'₹' + booking.total_payment"></span>
                            </div>
                            <div class="flex justify-between text-muted pt-1">
                                <span>Part Payment (Advance)</span>
                                <span class="font-semibold text-emerald-600 tabular-nums" x-text="'₹' + (booking.part_payment || 0)"></span>
                            </div>
                            <div class="flex justify-between text-ink font-semibold bg-sand/30 p-2.5 rounded-xl mt-1">
                                <span>Pay to Driver on Arrival</span>
                                <span class="text-sm font-black tabular-nums" x-text="'₹' + (booking.total_payment - booking.part_payment)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($invoices->isNotEmpty())
                    <!-- GST DOCUMENTS -->
                    <div class="card-bezel p-1.5">
                        <div class="card-core p-4">
                            <h3 class="font-bold text-ink text-sm mb-3">Invoices &amp; receipts</h3>
                            <div class="space-y-2 text-xs">
                                @foreach ($invoices as $invoice)
                                    <a href="{{ route('customer.invoices.show', $invoice) }}" target="_blank" rel="noopener"
                                       class="flex items-center justify-between gap-3 p-2.5 rounded-xl border border-black/[0.06] hover:border-gold">
                                        <span>
                                            <span class="font-semibold text-ink block">{{ $invoice->label() }}</span>
                                            <span class="text-muted">{{ $invoice->number }} &middot; {{ optional($invoice->issue_date)->format('d M Y') }}</span>
                                        </span>
                                        <span class="font-bold text-ink tabular-nums">&#8377;{{ number_format((float) $invoice->total, 2) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- SWIPE TO CANCEL -->
                <template x-if="booking.status != 3 && booking.status != 4 && booking.status != 5">
                    <div x-data="swipeToConfirm()" x-on:swipe-confirmed.window="cancelRide" class="pt-2">
                        <div class="relative h-14 rounded-full bg-red-50 border border-danger/25 overflow-hidden shadow-inner" x-ref="track">
                            <p class="absolute inset-0 flex items-center justify-center text-danger text-xs font-bold uppercase tracking-wider pointer-events-none select-none">
                                Swipe To Cancel Ride &raquo;
                            </p>
                            <div x-ref="handle" class="absolute top-1 left-1 w-12 h-12 rounded-full bg-danger flex items-center justify-center text-white shadow-md cursor-grab active:cursor-grabbing transition-transform"
                                 :style="`transform: translateX(${x}px)`"
                                 @mousedown="start" @touchstart="start"
                                 @mousemove.window="move" @touchmove.window="move"
                                 @mouseup.window="end" @touchend.window="end">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 6l6 6-6 6M5 12h14"/></svg>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function tripDetail(bookingId, initialBooking = null, initialError = '') {
        return {
            bookingId,
            booking: initialBooking,
            loading: !initialBooking && !initialError,
            error: initialError,
            async load() {
                if (this.booking || this.error) return;
                try {
                    const res = await apiFetch(`/app/actions/bookings/${this.bookingId}`);
                    this.booking = res.data.details;
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.loading = false;
                }
            },
            async cancelRide() {
                try {
                    await apiFetch('{{ route('customer.actions.cancel-ride') }}', { method: 'POST', body: { booking_id: this.booking.booking_id, cancel_by: 'You' } });
                    window.showToast('Ride Cancelled', 'Your ride has been cancelled successfully.');
                    setTimeout(() => window.location.href = '{{ route('customer.rides') }}', 1200);
                } catch (e) {
                    window.showToast('Error', e.message);
                }
            },
        };
    }
</script>
@endpush
