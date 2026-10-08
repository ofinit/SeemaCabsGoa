@extends('customer.layouts.app')

@php($withNav = false)
@section('title', 'Search Results — Seema Cabs Goa')

@section('content')
<div class="pt-4 pb-10" x-data="searchResults(@js($cabs ?? []), @js($surge ?? false), @js($error ?? ''), @js($fromName ?? ''), @js($toName ?? ''))">
    @include('customer.components.topbar', ['title' => 'Available Cabs', 'back' => route('customer.book'), 'backClick' => 'goBack()', 'right' => ''])

    <div class="px-4 space-y-4">
        <template x-if="surge">
            <div class="bg-amber-500/15 border border-amber-500/30 text-amber-900 rounded-2xl px-4 py-3 flex items-center justify-between text-xs font-semibold">
                <span class="flex items-center gap-1.5">
                    <span>⚡</span> High demand in this area. Slight surge pricing active.
                </span>
                <button @click="surge = false" class="text-amber-900/60 hover:text-amber-900">&#10005;</button>
            </div>
        </template>

        {{-- Trip Route Summary Banner --}}
        <div class="card-bezel">
            <div class="card-core p-4 bg-gradient-to-r from-gold/15 via-white to-white flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-muted block mb-0.5">Selected Route</span>
                    <template x-if="fromName && toName">
                        <div class="flex items-center gap-2 font-bold text-ink text-sm truncate">
                            <span x-text="fromName" class="truncate"></span>
                            <span class="text-muted shrink-0">&rarr;</span>
                            <span x-text="toName" class="truncate"></span>
                        </div>
                    </template>
                    <template x-if="!(fromName && toName)">
                        <div class="h-4 w-44 bg-black/10 rounded-full animate-pulse my-1"></div>
                    </template>
                    <p class="text-xs text-muted mt-1 flex items-center gap-1.5 font-medium">
                        <span>🗓️</span> <span x-text="pickupDateLabel + ' • ' + formatTime(pickupTime)"></span>
                    </p>
                </div>
                <a href="{{ route('customer.book') }}" class="px-2.5 py-1 rounded-full bg-black/5 hover:bg-gold text-[11px] font-semibold text-ink transition shrink-0">
                    Edit
                </a>
            </div>
        </div>

        <div x-show="loading" style="display:none" class="py-8">
            @include('customer.components.spinner', ['label' => 'Finding available cabs in Goa...'])
        </div>

        <p x-show="!loading && !cabs.length && error" x-text="error" class="text-center text-danger text-sm py-10"></p>

        {{-- Vehicle Tier Results --}}
        <div class="space-y-3.5">
            <template x-for="(cab, i) in cabs" :key="cab.cab_id">
                <div class="card-bezel animate-in" :style="`animation-delay: ${i * 80}ms`">
                    <div class="card-core space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <h3 class="font-bold text-ink text-base" x-text="cab.cab_name"></h3>
                                    <template x-if="i === 1">
                                        <span class="px-2 py-0.5 rounded-full bg-gold/30 text-yellow-950 text-[10px] font-bold">Popular</span>
                                    </template>
                                    <template x-if="i === 0">
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-bold">Value</span>
                                    </template>
                                </div>
                                <p class="text-xs text-muted" x-text="cab.model"></p>
                            </div>
                            <div class="text-right shrink-0">
                                <span x-show="cab.surge_price > 0" class="status-badge status-badge--assigned mb-1 inline-block">Surge</span>
                                <div class="font-extrabold text-ink text-xl tabular-nums leading-none">
                                    &#8377;<span x-text="cab.price"></span>
                                </div>
                                <span class="text-[10px] text-muted block mt-0.5 font-medium">all-inclusive</span>
                            </div>
                        </div>

                        {{-- Features Bar --}}
                        <div class="flex items-center gap-3 text-xs text-muted font-medium pt-1 border-t border-black/[0.04]">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                                <span x-text="cab.ac || 'AC'"></span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span>👥</span> <span x-text="cab.seat + ' Seats'"></span>
                            </span>
                            <span class="flex items-center gap-1">
                                <span>🧳</span> <span x-text="cab.baggage + ' Bags'"></span>
                            </span>
                            <span class="flex items-center gap-1 text-[11px] text-muted/80">
                                <span>🛣️</span> <span x-text="cab.base_km + ' km incl.'"></span>
                            </span>
                        </div>

                        {{-- Bottom Action Row --}}
                        <div class="flex items-center justify-between pt-2">
                            <img :src="cab.image" class="h-11 w-auto object-contain drop-shadow-sm" alt="Vehicle">
                            <button @click="bookNow(cab)" class="btn-primary py-2 px-4 text-xs font-bold inline-flex items-center gap-1.5 shadow-sm">
                                <span>Select &amp; Continue</span>
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function searchResults(initialCabs = [], initialSurge = false, initialError = '', initialFromName = '', initialToName = '') {
        const params = new URLSearchParams(window.location.search);
        return {
            cabs: initialCabs,
            loading: false,
            error: initialError,
            surge: initialSurge,
            fromName: initialFromName,
            toName: initialToName,
            pickupDateLabel: formatDMY(params.get('pickup_date') || ''),
            pickupTime: params.get('pickup_time') || '',
            goBack() {
                const trip = params.get('trip') || 'airport';
                window.location.href = '{{ route('customer.book') }}?trip=' + encodeURIComponent(trip);
            },
            bookNow(cab) {
                sessionStorage.setItem('pendingBooking', JSON.stringify({
                    cab,
                    search_query: window.location.search,
                    tab: params.get('tab') || '',
                    trip_type: params.get('trip') === 'airport' ? 'Airport Transfer' : (params.get('trip') === 'city' ? 'In-City Rides' : params.get('trip')),
                    cab_type: cab.cab_type,
                    cab_model: cab.cab_type,
                    cab_id: cab.id,
                    pickup_from: params.get('from'),
                    drop_to: params.get('to'),
                    air_port_drop: params.get('air_port_drop') || null,
                    pickup_date: params.get('pickup_date'),
                    pickup_time: params.get('pickup_time'),
                    pickup_address: this.fromName,
                    drop_of_address: this.toName,
                    base_fare: cab.price,
                    tax_amount: cab.tax_amount,
                    total_payment: cab.full_payment,
                    part_payment: cab.part_payment,
                    remain_payment: cab.remain_payment,
                    surge_price: cab.surge_price,
                    company_payment: cab.company_payment,
                    fleet_operator_payment: cab.fleet_operator_payment,
                }));
                window.location.href = '{{ route('customer.book.finding') }}';
            },
        };
    }
</script>
@endpush
