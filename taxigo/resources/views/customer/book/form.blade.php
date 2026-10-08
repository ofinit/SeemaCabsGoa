@extends('customer.layouts.app')

@section('title', 'Book Ride — Seema Cabs Goa')

@section('content')
<div class="pt-4 pb-8">
    @include('customer.components.topbar', ['title' => 'Book Ride', 'back' => $editBookingId ? true : null, 'showLogo' => !$editBookingId])

    <div class="px-4 space-y-4">
        {{-- Trust Pill --}}
        <div class="rounded-2xl bg-gradient-to-r from-gold/20 via-gold/10 to-transparent border border-gold/30 px-4 py-3 flex items-center gap-3">
            <span class="w-8 h-8 rounded-full bg-gold flex items-center justify-center text-ink shrink-0 font-bold text-sm">✓</span>
            <div>
                <p class="font-bold text-ink text-xs">Fixed Rates &bull; Guaranteed Pickup</p>
                <p class="text-[11px] text-muted">Clean AC Cabs &bull; No Hidden Night Surcharges in Goa</p>
            </div>
        </div>

        <div x-data="bookRideForm(@js($cities ?? []), @js($airports ?? []), @js($packages ?? []))" x-init="initDates">
            {{-- Segmented Trip Switcher --}}
            <div class="flex p-1 bg-black/[0.04] rounded-pill mb-4 border border-black/[0.04]">
                <button type="button" @click="trip = 'airport'" class="flex-1 py-2 rounded-pill text-xs font-bold transition-all duration-200" :class="trip === 'airport' ? 'bg-gold text-ink shadow-sm' : 'text-muted hover:text-ink'">
                    Airport
                </button>
                <button type="button" @click="trip = 'city'" class="flex-1 py-2 rounded-pill text-xs font-bold transition-all duration-200" :class="trip === 'city' ? 'bg-gold text-ink shadow-sm' : 'text-muted hover:text-ink'">
                    Local Rides
                </button>
                <button type="button" @click="trip = 'discover'; loadPackages()" class="flex-1 py-2 rounded-pill text-xs font-bold transition-all duration-200" :class="trip === 'discover' ? 'bg-gold text-ink shadow-sm' : 'text-muted hover:text-ink'">
                    Sightseeing
                </button>
            </div>

            <template x-if="trip !== 'discover'">
                <div class="card-bezel">
                    <div class="card-core space-y-4">
                        <template x-if="trip === 'airport'">
                            <div>
                                <label class="field-label">Transfer Type</label>
                                @include('customer.components.simple-select', [
                                    'model' => 'airportDirection',
                                    'options' => "[{value:'pickup',label:'✈️ Airport Pickup (Arriving in Goa)'},{value:'drop',label:'🛫 Airport Drop (Departing Goa)'}]",
                                ])
                            </div>
                        </template>

                        {{-- Waypoint Spine --}}
                        <div class="relative pl-7 space-y-3.5">
                            <!-- Connecting Dashed Spine Line -->
                            <div class="absolute left-[5px] top-3.5 bottom-[54px] w-0.5 border-l-2 border-dashed border-gray-300 pointer-events-none z-0"></div>

                            {{-- Pickup Point --}}
                            <div class="relative">
                                <span class="absolute -left-7 top-0.5 w-3 h-3 rounded-full bg-emerald-500 ring-4 ring-emerald-100 z-10"></span>
                                <label class="field-label flex items-center justify-between">
                                    <span>Pickup Location</span>
                                    <template x-if="trip === 'airport' && airportDirection === 'pickup'">
                                        <span class="text-[10px] text-gold font-bold normal-case">Airport</span>
                                    </template>
                                </label>
                                <template x-if="trip === 'airport' && airportDirection === 'pickup'">
                                    @include('customer.components.searchable-select', ['model' => 'fromValue', 'options' => 'airports', 'placeholder' => 'Select Airport'])
                                </template>
                                <template x-if="!(trip === 'airport' && airportDirection === 'pickup')">
                                    @include('customer.components.searchable-select', ['model' => 'fromValue', 'options' => 'cities', 'placeholder' => 'Select City / Area'])
                                </template>
                            </div>

                            {{-- Dropoff Point --}}
                            <div class="relative">
                                <span class="absolute -left-7 top-0.5 w-3 h-3 rounded-md bg-gold ring-4 ring-yellow-100 z-10"></span>
                                <label class="field-label flex items-center justify-between">
                                    <span>Destination Location</span>
                                    <template x-if="trip === 'airport' && airportDirection === 'drop'">
                                        <span class="text-[10px] text-gold font-bold normal-case">Airport</span>
                                    </template>
                                </label>
                                <template x-if="trip === 'airport' && airportDirection === 'drop'">
                                    @include('customer.components.searchable-select', ['model' => 'toValue', 'options' => 'airports', 'placeholder' => 'Select Airport'])
                                </template>
                                <template x-if="!(trip === 'airport' && airportDirection === 'drop')">
                                    @include('customer.components.searchable-select', ['model' => 'toValue', 'options' => 'cities', 'placeholder' => 'Select City / Area'])
                                </template>
                            </div>
                        </div>

                        <p x-show="fromValue && toValue && fromValue === toValue" style="display:none" class="text-danger text-xs">Pickup and drop cannot be the same location.</p>

                        {{-- Date & Time with Rapid Chips --}}
                        <div class="pt-2 border-t border-black/[0.05]">
                            <div class="flex items-center justify-between mb-2">
                                <label class="field-label mb-0">Pickup Schedule</label>
                                <div class="flex gap-1.5">
                                    <button type="button" @click="setTimePreset('now')" class="px-2 py-0.5 rounded-full bg-black/5 text-[10px] font-semibold text-ink hover:bg-gold transition active:scale-95">Leave Now</button>
                                    <button type="button" @click="setTimePreset('tomorrow')" class="px-2 py-0.5 rounded-full bg-black/5 text-[10px] font-semibold text-ink hover:bg-gold transition active:scale-95">Tomorrow</button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    @include('customer.components.date-field', ['model' => 'pickupDate', 'min' => 'today'])
                                </div>
                                <div>
                                    <input type="time" x-model="pickupTime" class="field-input">
                                </div>
                            </div>
                        </div>

                        @include('customer.components.error-banner', ['model' => 'error'])

                        <button type="button" @click="search" class="btn-primary w-full py-3.5 text-base flex items-center justify-center gap-2" :disabled="!canSearch">
                            <span>Find Available Cabs</span>
                            <span class="btn-icon-bubble">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </span>
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="trip === 'discover'">
                <div class="space-y-3">
                    <div x-show="packagesLoading" style="display:none" class="py-8 text-center">
                        <div class="inline-block w-6 h-6 border-2 border-gold border-t-transparent rounded-full animate-spin"></div>
                    </div>

                    <template x-for="(p, i) in (packages || [])" :key="p.id">
                        <a :href="`/app/discover/${p.id}`" class="card-bezel block animate-in active:scale-[0.98] transition-transform" :style="`animation-delay: ${i * 90}ms`">
                            <div class="card-core p-0 overflow-hidden">
                                <div class="relative">
                                    <img :src="p.images && p.images[0]" class="w-full h-44 object-cover bg-gray-100" alt="">
                                    <div class="absolute top-2.5 right-2.5 bg-black/60 backdrop-blur-md text-white text-[11px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                                        <span>⏱️</span>
                                        <span x-text="`${p.start_time || 'Full Day'} - ${p.end_time || ''}`"></span>
                                    </div>
                                </div>
                                <div class="p-3.5 flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-ink text-base" x-text="p.title"></p>
                                        <p class="text-xs text-muted flex items-center gap-1 mt-0.5">
                                            <span>📍</span>
                                            <span x-text="p.location || 'Goa'"></span>
                                        </p>
                                    </div>
                                    <span class="btn-primary py-1.5 px-4 text-xs font-bold shrink-0">View Tour &raquo;</span>
                                </div>
                            </div>
                        </a>
                    </template>

                    <p x-show="packagesLoaded && !packagesLoading && (!packages || packages.length === 0)" style="display:none" class="text-sm text-muted text-center py-6">No sightseeing packages available right now.</p>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function bookRideForm(initialCities = [], initialAirports = [], initialPackages = []) {
        const urlParams = new URLSearchParams(window.location.search);
        return {
            trip: urlParams.get('trip') || 'airport',
            airportDirection: urlParams.get('airportDirection') || 'pickup',
            fromValue: urlParams.get('from') || '',
            toValue: urlParams.get('to') || '',
            pickupDate: urlParams.get('pickup_date') || '',
            pickupTime: urlParams.get('pickup_time') || '',
            cities: initialCities,
            airports: initialAirports.length ? initialAirports : [
                { id: '1', name: 'Dabolim Goa Airport (GOI)' },
                { id: '2', name: 'Manohar International Airport (GOX)' },
            ],
            packages: Array.isArray(initialPackages) ? initialPackages : [],
            packagesLoading: false,
            packagesLoaded: Array.isArray(initialPackages) && initialPackages.length > 0,
            error: '',
            today: new Date().toISOString().slice(0, 10),
            get canSearch() {
                return this.fromValue && this.toValue && this.fromValue !== this.toValue && this.pickupDate && this.pickupTime;
            },
            initDates() {
                if (!this.pickupDate) {
                    this.setTimePreset('now');
                }
                if (this.trip === 'discover' && (!this.packages || !this.packages.length)) {
                    this.loadPackages();
                }
            },
            async loadPackages() {
                if (this.packages && this.packages.length > 0) {
                    this.packagesLoaded = true;
                    return;
                }
                this.packagesLoading = true;
                try {
                    const res = await fetch('{{ route('customer.actions.sightseeing.list') }}');
                    const json = await res.json();
                    if (json && json.status && Array.isArray(json.data)) {
                        this.packages = json.data;
                    }
                } catch (e) {
                    console.error('Failed to load sightseeing packages:', e);
                } finally {
                    this.packagesLoading = false;
                    this.packagesLoaded = true;
                }
            },
            setTimePreset(type) {
                const now = new Date();
                if (type === 'now') {
                    now.setMinutes(now.getMinutes() + 20);
                    this.pickupDate = now.toISOString().slice(0, 10);
                    this.pickupTime = now.toTimeString().slice(0, 5);
                } else if (type === 'tomorrow') {
                    now.setDate(now.getDate() + 1);
                    this.pickupDate = now.toISOString().slice(0, 10);
                    this.pickupTime = '09:00';
                }
            },
            search() {
                this.error = '';
                if (this.fromValue === this.toValue) {
                    this.error = 'Pickup and drop cannot be the same.';
                    return;
                }
                const tab = this.trip === 'airport' ? (this.airportDirection === 'pickup' ? 1 : 2) : 3;
                const params = new URLSearchParams({
                    trip: this.trip,
                    tab,
                    from: this.fromValue,
                    to: this.toValue,
                    pickup_date: this.pickupDate,
                    pickup_time: this.pickupTime,
                    air_port_drop: this.trip === 'airport' ? (this.airportDirection === 'pickup' ? 1 : 2) : '',
                });
                window.location.href = '{{ route('customer.book.results') }}?' + params.toString();
            },
        };
    }
</script>
@endpush
