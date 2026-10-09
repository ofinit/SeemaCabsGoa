@extends('customer.layouts.app')

@php($withNav = false)
@section('title', 'Discover Goa — Seema Cabs Goa')

@section('content')
<div class="pt-4 pb-10" x-data="packageDetail({{ $packageId }}, @js($pkg), @js($cities), @js($accountData))">
    @include('customer.components.topbar', ['title' => 'Discover Goa', 'back' => route('customer.book') . '?trip=discover', 'right' => ''])

    <div class="px-4">
        <div x-show="loading" style="display:none">
            @include('customer.components.spinner')
        </div>

        <template x-if="pkg">
            <div>
                <div class="rounded-card overflow-hidden mb-4">
                    <img :src="pkg.images[0]" class="w-full h-48 object-cover" alt="">
                </div>

                <div class="rounded-card bg-gradient-to-r from-peach to-peach-light px-5 py-4 mb-4">
                    <p class="font-semibold text-ink" x-text="pkg.title"></p>
                    <div class="flex items-center gap-3 text-xs text-ink/70 mt-1">
                        <span x-text="pkg.start_time + ' to ' + pkg.end_time"></span>
                        <span>&bull;</span>
                        <span x-text="pkg.location"></span>
                    </div>
                </div>

                <div class="card mb-4">
                    <h3 class="font-semibold text-ink mb-2">About</h3>
                    <p class="text-sm text-muted whitespace-pre-line" x-text="pkg.description"></p>
                </div>

                <div class="card mb-4" x-show="pkg.terms_and_condition">
                    <h3 class="font-semibold text-ink mb-2">Terms &amp; Conditions</h3>
                    <p class="text-sm text-muted whitespace-pre-line" x-text="pkg.terms_and_condition"></p>
                </div>

                <div class="card">
                    <h3 class="font-semibold text-ink mb-3">Book This Package</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="field-label">Pickup City</label>
                            @include('customer.components.searchable-select', ['model' => 'cityId', 'options' => 'cities', 'placeholder' => 'Select City', 'onSelect' => 'loadCabs()'])
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="field-label">Date</label>
                                @include('customer.components.date-field', ['model' => 'pickupDate', 'min' => 'today'])
                            </div>
                            <div>
                                <label class="field-label">Time</label>
                                <input type="time" x-model="pickupTime" class="field-input">
                            </div>
                        </div>
                        <div x-show="!loading && !namePrefilled" style="display:none">
                            <label class="field-label">Your Name</label>
                            <input type="text" x-model="name" class="field-input" placeholder="Enter Your Name">
                        </div>

                        <div class="space-y-2" x-show="cabs.length">
                            <template x-for="cab in cabs" :key="cab.cab_type">
                                <label class="flex items-center justify-between border rounded-2xl px-4 py-3 cursor-pointer" :class="String(selectedCabType) === String(cab.cab_type) ? 'border-gold bg-gold/10' : 'border-gray-200'">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" x-model="selectedCabType" :value="cab.cab_type" class="accent-gold">
                                        <div>
                                            <p class="font-medium text-ink text-sm" x-text="cab.cab_name"></p>
                                            <p class="text-xs text-muted" x-text="cab.model"></p>
                                        </div>
                                    </div>
                                    <span class="font-semibold text-ink" x-text="'₹' + cab.price"></span>
                                </label>
                            </template>
                        </div>

                        @include('customer.components.gst-invoice-fields')
                        @include('customer.components.error-banner', ['model' => 'error'])

                        <button @click="payNow" class="btn-primary w-full" :disabled="!canPay || paying">
                            <span x-show="!paying">Book Now</span>
                            <span x-show="paying">Processing&hellip;</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <template x-if="confirmed">
        <div class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center px-8">
            <div class="bg-white rounded-card p-8 text-center max-w-xs animate-in">
                <div class="w-14 h-14 rounded-full border-2 border-success flex items-center justify-center mx-auto mb-4 animate-pop">
                    <svg class="w-7 h-7 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <p class="text-lg font-bold text-ink mb-1">Congratulations!</p>
                <p class="text-sm text-muted mb-4">Your sightseeing booking is confirmed.</p>
                <p class="text-sm mb-4">Booking ID: <span class="font-bold text-ink" x-text="bookingId"></span> &bull; OTP: <span class="font-bold text-ink" x-text="otp"></span></p>
                <a href="{{ route('customer.rides') }}" class="btn-primary w-full">View My Rides</a>
            </div>
        </div>
    </template>
</div>
@endsection

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
@push('scripts')
<script>
    function packageDetail(packageId, initialPkg = null, initialCities = [], initialAccount = null) {
        return {
            packageId,
            pkg: initialPkg,
            loading: !initialPkg,
            cities: initialCities,
            cabs: [],
            cityId: '',
            pickupDate: '',
            pickupTime: '',
            name: initialAccount ? (initialAccount.name || '') : '',
            selectedCabType: '',
            error: '',
            paying: false,
            confirmed: false,
            bookingId: '',
            otp: '',
            today: new Date().toISOString().slice(0, 10),
            namePrefilled: !!(initialAccount && initialAccount.name),
            accountData: initialAccount,
            gst: {
                enabled: !!(initialAccount && initialAccount.gstin),
                gstin: (initialAccount && initialAccount.gstin) || '',
                legal_name: (initialAccount && initialAccount.gst_legal_name) || '',
                address: (initialAccount && initialAccount.gst_billing_address) || '',
            },
            gstPayload() {
                if (!this.gst.enabled || !this.gst.gstin.trim()) return {};
                return { customer_gstin: this.gst.gstin.trim(), customer_legal_name: this.gst.legal_name.trim(), customer_billing_address: this.gst.address.trim() };
            },
            get canPay() { return this.cityId && this.pickupDate && this.pickupTime && this.name && this.selectedCabType; },
            async load() {
                if (this.pkg && this.cities.length) return;
                try {
                    const [listRes, citiesRes, accountRes] = await Promise.all([
                        apiFetch('{{ route('customer.actions.sightseeing.list') }}'),
                        apiFetch('{{ route('customer.actions.cities') }}'),
                        apiFetch('{{ route('customer.actions.account.show') }}').catch(() => null),
                    ]);
                    this.pkg = (listRes.data || []).find(p => p.id === this.packageId);
                    this.cities = citiesRes.data || [];
                    if (accountRes) {
                        this.accountData = accountRes.data;
                        this.name = accountRes.data.name || '';
                        this.namePrefilled = !!accountRes.data.name;
                    } else {
                        this.namePrefilled = false;
                    }
                } finally {
                    this.loading = false;
                }
            },
            async loadCabs() {
                this.cabs = []; this.selectedCabType = '';
                if (!this.cityId) return;
                try {
                    const res = await apiFetch('{{ route('customer.actions.sightseeing.cab-list') }}', { method: 'POST', body: { sightseeing_id: this.packageId, city_id: this.cityId } });
                    this.cabs = res.data || [];
                } catch (e) { this.error = e.message; }
            },
            async payNow() {
                this.error = ''; this.paying = true;
                try {
                    const cab = this.cabs.find(c => String(c.cab_type) === String(this.selectedCabType));
                    const [dd, mm, yyyy] = [this.pickupDate.slice(8, 10), this.pickupDate.slice(5, 7), this.pickupDate.slice(0, 4)];
                    const bookingRes = await apiFetch('{{ route('customer.actions.sightseeing.book') }}', { method: 'POST', body: {
                        customer_id: {{ auth('customer')->id() }},
                        sight_seeing_package_id: this.packageId,
                        cab_type: this.selectedCabType,
                        pickup_from: this.cityId,
                        pickup_date: `${dd}-${mm}-${yyyy}`,
                        pickup_time: this.pickupTime,
                        price: cab.price,
                        name: this.name,
                        ...this.gstPayload(),
                    }});
                    this.bookingId = bookingRes.data.booking_id;
                    this.otp = bookingRes.data.otp;

                    const settingsRes = await apiFetch('{{ route('customer.actions.settings') }}');
                    const useCashfree = settingsRes.data.cashfree_enabled && (!settingsRes.data.razorpay_enabled || settingsRes.data.primary_payment_gateway === 'cashfree');

                    if (useCashfree) {
                        const cfOrderRes = await apiFetch('{{ route('customer.actions.cashfree.create-order') }}', {
                            method: 'POST',
                            body: {
                                booking_id: this.bookingId,
                                name: this.name,
                            }
                        });

                        const cashfree = Cashfree({ mode: cfOrderRes.mode || settingsRes.data.cashfree_mode || 'sandbox' });
                        cashfree.checkout({
                            paymentSessionId: cfOrderRes.payment_session_id,
                            redirectTarget: '_modal'
                        }).then(async (result) => {
                            if (result.error) {
                                this.paying = false;
                                this.error = result.error.message || 'Payment cancelled.';
                                return;
                            }
                            if (result.paymentDetails) {
                                try {
                                    await apiFetch('{{ route('customer.actions.confirm-payment') }}', { method: 'POST', body: {
                                        booking_id: this.bookingId, status: 1, transaction_id: cfOrderRes.order_id, payment_gateway: 'cashfree', pg_order_id: cfOrderRes.order_id,
                                    }});
                                    this.paying = false;
                                    this.confirmed = true;
                                } catch (e) {
                                    this.paying = false;
                                    this.error = 'Payment succeeded but confirmation failed: ' + e.message;
                                }
                            }
                        });
                    } else {
                        const orderRes = await apiFetch('{{ route('customer.actions.create-order') }}', { method: 'POST', body: { booking_id: this.bookingId } });

                        const rzp = new Razorpay({
                            key: settingsRes.data.payment_key,
                            amount: orderRes.amount,
                            currency: orderRes.currency,
                            order_id: orderRes.order_id,
                            name: 'Seema Cabs Goa',
                            description: this.pkg.title,
                            theme: { color: '#FEDC33' },
                            handler: async (response) => {
                                try {
                                    await apiFetch('{{ route('customer.actions.confirm-payment') }}', { method: 'POST', body: {
                                        booking_id: this.bookingId, status: 1, transaction_id: response.razorpay_payment_id, payment_gateway: 'razorpay',
                                    }});
                                    this.paying = false;
                                    this.confirmed = true;
                                    // Persist a newly-typed name back to the profile (best-effort)
                                    // so it isn't asked for again on the next booking.
                                    if (!this.namePrefilled && this.accountData) {
                                        apiFetch('{{ route('customer.actions.account.update') }}', { method: 'POST', body: {
                                            name: this.name,
                                            gender: this.accountData.gender != null ? String(this.accountData.gender) : '1',
                                            country_id: this.accountData.country_id || null,
                                            state_id: this.accountData.state_id || null,
                                            email: this.accountData.email || '',
                                            phone_number: this.accountData.phone_number || '',
                                        }}).catch(() => {});
                                    }
                                } catch (e) {
                                    this.paying = false;
                                    this.error = 'Payment succeeded but confirmation failed: ' + e.message;
                                }
                            },
                            modal: { ondismiss: () => { this.paying = false; } },
                        });
                        rzp.on('payment.failed', () => { this.paying = false; this.error = 'Payment failed. Please try again.'; });
                        rzp.open();
                    }
                } catch (e) {
                    this.paying = false;
                    this.error = e.message;
                }
            },
        };
    }
</script>
@endpush
