@extends('customer.layouts.app')

@section('title', 'Your Rides — Seema Cabs Goa')

@section('content')
<div class="pt-4 pb-20" x-data="ridesList(@js($rides ?? []))">
    @include('customer.components.topbar', ['title' => 'Ride History'])
    @include('customer.components.ad-slot', ['screen' => \App\Services\Ads\AdServer::SCREEN_ACCOUNT, 'class' => 'px-4 mb-4'])

    <div class="px-4">
        <!-- FILTER TABS -->
        <div class="flex items-center gap-1.5 p-1 bg-sand/40 rounded-2xl mb-4 border border-sand/60">
            <button type="button" @click="filterTab = 'all'"
                    class="flex-1 py-2 text-xs font-bold rounded-xl transition-all"
                    :class="filterTab === 'all' ? 'bg-ink text-white shadow-sm' : 'text-muted hover:text-ink'">
                All
            </button>
            <button type="button" @click="filterTab = 'active'"
                    class="flex-1 py-2 text-xs font-bold rounded-xl transition-all"
                    :class="filterTab === 'active' ? 'bg-ink text-white shadow-sm' : 'text-muted hover:text-ink'">
                Active
            </button>
            <button type="button" @click="filterTab = 'completed'"
                    class="flex-1 py-2 text-xs font-bold rounded-xl transition-all"
                    :class="filterTab === 'completed' ? 'bg-ink text-white shadow-sm' : 'text-muted hover:text-ink'">
                Completed
            </button>
            <button type="button" @click="filterTab = 'cancelled'"
                    class="flex-1 py-2 text-xs font-bold rounded-xl transition-all"
                    :class="filterTab === 'cancelled' ? 'bg-ink text-white shadow-sm' : 'text-muted hover:text-ink'">
                Cancelled
            </button>
        </div>

        <div x-show="loading" style="display:none" class="py-12">
            @include('customer.components.spinner')
        </div>

        <!-- EMPTY STATE -->
        <div x-show="!loading && !filteredRides.length" class="text-center py-12 px-6">
            <div class="w-16 h-16 rounded-3xl bg-sand/50 border border-sand flex items-center justify-center mx-auto mb-4 text-ink">
                <svg class="w-8 h-8 opacity-40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9L2.1 11.2c-.1.2-.1.5-.1.8v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
            </div>
            <h3 class="font-bold text-ink text-base">No Rides Found</h3>
            <p class="text-xs text-muted mt-1 max-w-xs mx-auto">You have no rides in this category yet. Book your next journey across Goa with Seema Cabs.</p>
            <a href="{{ route('customer.book') }}" class="btn-primary inline-flex items-center gap-2 mt-5 py-2.5 px-5 text-xs">
                <span>Book a Ride</span>
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

        <!-- RIDES LIST -->
        <div class="space-y-4" x-show="!loading && filteredRides.length">
            <template x-for="(ride, i) in filteredRides" :key="ride.id">
                <div class="card-bezel p-1.5 animate-in" :style="`animation-delay: ${i * 60}ms`">
                    <div class="card-core p-4">
                        <!-- HEADER -->
                        <div class="flex items-start justify-between gap-3 mb-3 pb-2.5 border-b border-sand/40">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-ink text-sm" x-text="ride.cab_name"></h3>
                                    <span class="text-[10px] text-muted font-semibold bg-sand/60 px-2 py-0.5 rounded-full" x-text="ride.model"></span>
                                </div>
                                <p class="text-[11px] text-muted mt-0.5">Booking ID: <span class="font-semibold text-ink" x-text="'#' + ride.booking_id"></span></p>
                            </div>
                            <span class="status-badge shrink-0" :class="'status-badge--' + statusOf(ride)" x-text="statusLabel(statusOf(ride))"></span>
                        </div>

                        <!-- OTP BANNER FOR ACTIVE RIDES -->
                        <div x-show="statusOf(ride) === 'assigned' || statusOf(ride) === 'confirmed'" class="bg-gold/10 border border-gold/30 rounded-xl px-3 py-2 mb-3 flex items-center justify-between">
                            <span class="text-[11px] font-bold text-ink/80 uppercase tracking-wider">Start Ride OTP</span>
                            <span class="text-sm font-black text-ink tracking-widest tabular-nums" x-text="ride.trip_otp"></span>
                        </div>

                        <!-- WAYPOINTS -->
                        <div class="flex items-stretch gap-3 text-xs mb-3.5">
                            <!-- Left Indicator Spine -->
                            <div class="flex flex-col items-center py-0.5 shrink-0 w-3">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-emerald-100 shrink-0"></span>
                                <div class="w-0.5 flex-1 my-1 border-l-2 border-dashed border-gray-300"></div>
                                <span class="w-2.5 h-2.5 rounded-[2px] bg-gold ring-2 ring-gold/30 shrink-0"></span>
                            </div>
                            <!-- Right Content -->
                            <div class="flex-1 space-y-2.5 min-w-0">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-muted">Pickup Location</p>
                                    <p class="text-ink font-semibold text-xs mt-0.5 leading-snug break-words" x-text="ride.pickup_address || (ride.getPickupFrom && ride.getPickupFrom.name) || '—'"></p>
                                    <p class="text-[10px] text-muted mt-0.5 tabular-nums" x-text="formatDMY(ride.pickup_date) + ' • ' + formatTime(ride.pickup_time)"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-muted">Dropoff Location</p>
                                    <p class="text-ink font-semibold text-xs mt-0.5 leading-snug break-words" x-text="ride.drop_of_address || (ride.getDropTo && ride.getDropTo.name) || '—'"></p>
                                </div>
                            </div>
                        </div>

                        <!-- FOOTER -->
                        <div class="flex items-center justify-between pt-3 border-t border-sand/40">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-muted tracking-wider">Total Fare</p>
                                <p class="font-extrabold text-ink text-sm tabular-nums" x-text="'₹' + ride.total_payment"></p>
                            </div>

                            <template x-if="statusOf(ride) === 'assigned' || statusOf(ride) === 'confirmed'">
                                <div class="flex items-center gap-2">
                                    <a :href="'/app/trip/' + ride.id" class="btn-primary py-2 px-3.5 text-xs" x-show="statusOf(ride) === 'assigned'">
                                        Driver &amp; OTP
                                    </a>
                                    <button @click="toCancel = ride" class="btn-outline py-2 px-3 text-xs border-danger/30 text-danger hover:bg-danger/5">
                                        Cancel
                                    </button>
                                </div>
                            </template>

                            <template x-if="statusOf(ride) === 'cancelled' || statusOf(ride) === 'completed'">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('customer.book') }}" class="btn-secondary py-2 px-3 text-xs">
                                        Rebook
                                    </a>
                                    <button @click="toDelete = ride" class="btn-outline py-2 px-3 text-xs text-muted hover:text-danger">
                                        Delete
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- CANCEL MODAL -->
    <div x-show="toCancel" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-end" style="display:none">
        <div x-show="toCancel"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
             class="bg-white rounded-t-[32px] w-full max-w-app mx-auto p-6 max-h-[85vh] overflow-y-auto border-t border-sand shadow-2xl" @click.outside="toCancel = null">
            <div class="w-12 h-1.5 rounded-full bg-sand/80 mx-auto mb-4"></div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-ink text-base">Cancel Reservation</h3>
                <button @click="toCancel = null" class="w-8 h-8 rounded-full bg-sand/40 flex items-center justify-center text-muted hover:text-ink text-xs">&times;</button>
            </div>
            <div class="text-center py-4">
                <div class="w-14 h-14 rounded-2xl bg-danger/10 text-danger flex items-center justify-center mx-auto mb-3 font-bold text-xl">!</div>
                <p class="font-bold text-ink text-base">Are you sure you want to cancel?</p>
                <p class="text-xs text-muted mt-1.5 max-w-xs mx-auto leading-relaxed">Free cancellation applies prior to chauffeur dispatch. Any applicable refund will be processed as per policy.</p>
            </div>
            <div class="flex gap-3 mt-4">
                <button @click="toCancel = null" class="btn-outline flex-1 py-3 text-xs font-bold">Keep Ride</button>
                <button @click="confirmCancel" class="btn-primary flex-1 py-3 text-xs font-bold bg-danger hover:bg-danger/90 text-white">Yes, Cancel</button>
            </div>
        </div>
    </div>

    <!-- DELETE MODAL -->
    <div x-show="toDelete" x-transition.opacity class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-end" style="display:none">
        <div x-show="toDelete"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
             class="bg-white rounded-t-[32px] w-full max-w-app mx-auto p-6 max-h-[85vh] overflow-y-auto border-t border-sand shadow-2xl" @click.outside="toDelete = null">
            <div class="w-12 h-1.5 rounded-full bg-sand/80 mx-auto mb-4"></div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-ink text-base">Remove From History</h3>
                <button @click="toDelete = null" class="w-8 h-8 rounded-full bg-sand/40 flex items-center justify-center text-muted hover:text-ink text-xs">&times;</button>
            </div>
            <div class="text-center py-4">
                <div class="w-14 h-14 rounded-2xl bg-sand/50 text-ink flex items-center justify-center mx-auto mb-3 font-bold text-xl">
                    <svg class="w-6 h-6 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </div>
                <p class="font-bold text-ink text-base">Delete this ride log?</p>
                <p class="text-xs text-muted mt-1 max-w-xs mx-auto leading-relaxed">This trip record will be permanently deleted from your ride history.</p>
            </div>
            <div class="flex gap-3 mt-4">
                <button @click="toDelete = null" class="btn-outline flex-1 py-3 text-xs font-bold">Keep</button>
                <button @click="confirmDelete" class="btn-primary flex-1 py-3 text-xs font-bold bg-ink text-white">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function ridesList(initialRides = []) {
        return {
            rides: initialRides,
            loading: false,
            toCancel: null,
            toDelete: null,
            filterTab: 'all',
            async load() {
                try {
                    const res = await apiFetch('{{ route('customer.actions.bookings.index') }}');
                    this.rides = res.data.bookings || [];
                } catch (e) {
                } finally {
                    this.loading = false;
                }
            },
            statusOf(ride) {
                if (ride.status == 3) return 'cancelled';
                if (ride.status == 4) return 'completed';
                if (ride.status == 1) return ride.driver ? 'assigned' : 'confirmed';
                return 'confirmed';
            },
            statusLabel(s) {
                return { assigned: 'Driver Assigned', confirmed: 'Booking Confirmed', cancelled: 'Cancelled', completed: 'Completed' }[s];
            },
            get filteredRides() {
                if (this.filterTab === 'all') return this.rides;
                if (this.filterTab === 'active') return this.rides.filter(r => ['assigned', 'confirmed'].includes(this.statusOf(r)));
                if (this.filterTab === 'completed') return this.rides.filter(r => this.statusOf(r) === 'completed');
                if (this.filterTab === 'cancelled') return this.rides.filter(r => this.statusOf(r) === 'cancelled');
                return this.rides;
            },
            async confirmCancel() {
                try {
                    await apiFetch('{{ route('customer.actions.cancel-ride') }}', { method: 'POST', body: { booking_id: this.toCancel.booking_id, cancel_by: 'You' } });
                    this.toCancel.status = 3;
                    this.toCancel = null;
                    window.showToast('Ride Cancelled', 'Your ride has been cancelled.');
                } catch (e) {
                    window.showToast('Error', e.message);
                }
            },
            async confirmDelete() {
                try {
                    await apiFetch(`/app/actions/bookings/${this.toDelete.id}`, { method: 'DELETE' });
                    this.rides = this.rides.filter(r => r.id !== this.toDelete.id);
                    this.toDelete = null;
                    window.showToast('Deleted', 'Ride removed from history.');
                } catch (e) {
                    window.showToast('Error', e.message);
                }
            },
        };
    }
</script>
@endpush
