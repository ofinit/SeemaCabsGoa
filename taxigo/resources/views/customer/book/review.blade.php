@extends('customer.layouts.app')

@php($withNav = false)
@section('title', 'Review Booking — Seema Cabs Goa')

@section('content')
<div class="pt-4 pb-28" x-data="reviewBooking(@js($userData ?? []), @js($countries ?? []), @js($states ?? []), @js($paymentKey ?? ''))" x-init="load">
    @include('customer.components.topbar', ['title' => 'Review Booking', 'back' => route('customer.book'), 'backClick' => 'goBack()', 'right' => ''])

    <div class="px-4 space-y-4" x-show="draft">
        {{-- Vehicle & Fare Header --}}
        <div class="card-bezel">
            <div class="card-core bg-gradient-to-r from-gold/15 via-white to-white flex items-center justify-between p-4">
                <div class="flex items-center gap-3">
                    <img :src="draft && draft.cab && draft.cab.image" class="w-14 h-auto object-contain" alt="">
                    <div>
                        <h3 class="font-bold text-ink text-base" x-text="draft && draft.cab && draft.cab.cab_name"></h3>
                        <p class="text-xs text-muted" x-text="(draft && draft.cab && draft.cab.model) + ' or similar'"></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">Total Fare</span>
                    <span class="font-extrabold text-ink text-xl tabular-nums">&#8377;<span x-text="draft && draft.cab && draft.cab.full_payment"></span></span>
                    <span x-show="gstAmount() > 0" style="display:none" class="text-[10px] text-muted block"
                        x-text="'Fare ₹' + draft.cab.price + ' + GST ₹' + draft.cab.tax_amount"></span>
                </div>
            </div>
        </div>

        {{-- Route Summary --}}
        <div class="card-bezel">
            <div class="card-core p-4 space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-muted">Trip Route</h4>
                <div class="flex items-stretch gap-3 text-xs">
                    <div class="flex flex-col items-center py-0.5 shrink-0 w-3.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100 shrink-0"></span>
                        <div class="w-0.5 flex-1 my-1 border-l-2 border-dashed border-gray-300"></div>
                        <span class="w-2.5 h-2.5 rounded-md bg-gold ring-4 ring-yellow-100 shrink-0"></span>
                    </div>
                    <div class="flex-1 space-y-3 min-w-0">
                        <div>
                            <p class="text-[10px] text-muted uppercase font-bold">Pick-up</p>
                            <p class="font-semibold text-ink text-[13px] leading-snug break-words" x-text="draft && draft.pickup_address"></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-muted uppercase font-bold">Drop-off</p>
                            <p class="font-semibold text-ink text-[13px] leading-snug break-words" x-text="draft && draft.drop_of_address"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inclusions / Exclusions --}}
        <div class="card-bezel">
            <div class="card-core p-4">
                <div class="flex p-1 bg-black/[0.04] rounded-pill mb-3 border border-black/[0.04]">
                    <button type="button" @click="tab = 'inclusions'" class="flex-1 py-1.5 rounded-pill text-xs font-bold transition-all" :class="tab === 'inclusions' ? 'bg-gold text-ink shadow-sm' : 'text-muted hover:text-ink'">Inclusions</button>
                    <button type="button" @click="tab = 'exclusions'" class="flex-1 py-1.5 rounded-pill text-xs font-bold transition-all" :class="tab === 'exclusions' ? 'bg-gold text-ink shadow-sm' : 'text-muted hover:text-ink'">Exclusions</button>
                </div>
                <ul x-show="tab === 'inclusions'" class="text-xs text-ink space-y-2">
                    <li class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-success flex items-center justify-center text-[10px] font-bold">✓</span>
                        <span>State permit taxes included</span>
                    </li>
                    <li class="flex items-center gap-2" x-show="gstAmount() > 0" style="display:none">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-success flex items-center justify-center text-[10px] font-bold">✓</span>
                        <span x-text="'GST @ ' + (draft ? draft.cab.gst_rate : '') + '% included in the total'"></span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-success flex items-center justify-center text-[10px] font-bold">✓</span>
                        <span>Driver Allowance &amp; Fuel included</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-success flex items-center justify-center text-[10px] font-bold">✓</span>
                        <span x-text="(draft ? draft.cab.base_km : '') + ' km included in base price'"></span>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-success flex items-center justify-center text-[10px] font-bold">✓</span>
                        <span>30 mins free waiting time at pickup</span>
                    </li>
                </ul>
                <ul x-show="tab === 'exclusions'" class="text-xs text-ink space-y-2" style="display:none">
                    <li class="flex items-center gap-2 text-muted">
                        <span class="w-4 h-4 rounded-full bg-black/5 text-muted flex items-center justify-center text-[10px] font-bold">&times;</span>
                        <span>Extra waiting after 30 mins: &#8377;100 per 30 mins</span>
                    </li>
                    <li class="flex items-center gap-2 text-muted">
                        <span class="w-4 h-4 rounded-full bg-black/5 text-muted flex items-center justify-center text-[10px] font-bold">&times;</span>
                        <span>Distance beyond included kms at standard per-km rate</span>
                    </li>
                    <li class="flex items-center gap-2 text-muted">
                        <span class="w-4 h-4 rounded-full bg-black/5 text-muted flex items-center justify-center text-[10px] font-bold">&times;</span>
                        <span>Special entry parking if required</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Confirm Traveller Info (only shown if missing) --}}
        <div class="card-bezel" x-show="profileLoaded && !allPrefilled" style="display:none">
            <div class="card-core p-4 space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-muted">Traveller Information</h4>
                <div class="space-y-3">
                    <div x-show="!prefilled.name" style="display:none">
                        <label class="field-label">Name</label>
                        <input x-model="traveller.name" type="text" class="field-input" placeholder="Enter Your Name">
                    </div>
                    <div x-show="!prefilled.gender" style="display:none">
                        <label class="field-label">Gender</label>
                        <div class="flex gap-2">
                            <label class="flex-1 flex items-center justify-center gap-2 border rounded-pill py-2 cursor-pointer text-xs" :class="traveller.gender === '1' ? 'border-gold bg-gold/10 font-bold' : 'border-gray-200'">
                                <input type="radio" x-model="traveller.gender" value="1"> Male
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-2 border rounded-pill py-2 cursor-pointer text-xs" :class="traveller.gender === '0' ? 'border-gold bg-gold/10 font-bold' : 'border-gray-200'">
                                <input type="radio" x-model="traveller.gender" value="0"> Female
                            </label>
                            <label class="flex-1 flex items-center justify-center gap-2 border rounded-pill py-2 cursor-pointer text-xs" :class="traveller.gender === '2' ? 'border-gold bg-gold/10 font-bold' : 'border-gray-200'">
                                <input type="radio" x-model="traveller.gender" value="2"> Other
                            </label>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3" x-show="!prefilled.country_id || !prefilled.state_id" style="display:none">
                        <div x-show="!prefilled.country_id" style="display:none">
                            <label class="field-label">Country</label>
                            @include('customer.components.searchable-select', ['model' => 'traveller.country_id', 'options' => 'countries', 'placeholder' => 'Select', 'onSelect' => 'loadStates(traveller.country_id)'])
                        </div>
                        <div x-show="!prefilled.state_id" style="display:none">
                            <label class="field-label">State</label>
                            @include('customer.components.searchable-select', ['model' => 'traveller.state_id', 'options' => 'states', 'placeholder' => 'Select'])
                        </div>
                    </div>
                    <div x-show="!prefilled.email" style="display:none">
                        <label class="field-label">Email Id</label>
                        <input x-model="traveller.email" type="email" class="field-input" placeholder="Enter Email ID">
                    </div>
                    <div x-show="!prefilled.phone_number" style="display:none">
                        <label class="field-label">Contact Number</label>
                        <div class="flex gap-2">
                            <span class="field-input w-20 text-center px-0">+91</span>
                            <input x-model="traveller.phone_number" type="tel" maxlength="10" class="field-input flex-1" placeholder="10 digit number">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('customer.components.gst-invoice-fields')

        @include('customer.components.error-banner', ['model' => 'error'])

        {{-- Payment Trust Bar --}}
        {{-- Payment Gateway Selector (shown when both are enabled) --}}
        <template x-if="pgSettings.razorpay_enabled && pgSettings.cashfree_enabled">
            <div class="card-bezel mb-3">
                <div class="card-core space-y-2.5">
                    <span class="text-xs font-bold text-ink uppercase tracking-wider block">Payment Gateway</span>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <label class="border rounded-xl p-2.5 flex items-center gap-2 cursor-pointer transition-all"
                            :class="selectedGateway === 'cashfree' ? 'border-amber-400 bg-amber-50 font-bold text-ink' : 'border-black/[0.08] text-muted'">
                            <input type="radio" name="checkout_pg" value="cashfree" x-model="selectedGateway" class="hidden">
                            <span class="w-3.5 h-3.5 rounded-full border border-current flex items-center justify-center">
                                <span class="w-2 h-2 rounded-full bg-gold" x-show="selectedGateway === 'cashfree'"></span>
                            </span>
                            <span>Cashfree (UPI/Cards)</span>
                        </label>
                        <label class="border rounded-xl p-2.5 flex items-center gap-2 cursor-pointer transition-all"
                            :class="selectedGateway === 'razorpay' ? 'border-amber-400 bg-amber-50 font-bold text-ink' : 'border-black/[0.08] text-muted'">
                            <input type="radio" name="checkout_pg" value="razorpay" x-model="selectedGateway" class="hidden">
                            <span class="w-3.5 h-3.5 rounded-full border border-current flex items-center justify-center">
                                <span class="w-2 h-2 rounded-full bg-gold" x-show="selectedGateway === 'razorpay'"></span>
                            </span>
                            <span>Razorpay (UPI/Cards)</span>
                        </label>
                    </div>
                </div>
            </div>
        </template>

        <div class="rounded-2xl bg-black/[0.03] border border-black/[0.04] p-3 text-center space-y-1.5">
            <div class="flex items-center justify-center gap-3 text-xs text-muted font-medium">
                <span>🔒 256-bit SSL Secure</span>
                <span>•</span>
                <span>UPI (GPay / PhonePe / Paytm)</span>
                <span>•</span>
                <span>Cards</span>
            </div>
            <p class="text-[11px] text-muted">Part payment online to confirm ride &bull; Balance paid to driver</p>
        </div>
    </div>

    {{-- Sticky Floating Bottom Action Dock --}}
    <div class="fixed bottom-0 inset-x-0 max-w-app mx-auto dock-glass px-5 py-3.5 z-40" x-show="draft">
        <div class="flex items-center justify-between gap-4">
            <div>
                <span class="text-[11px] text-muted block font-medium">Pay Advance to Book</span>
                <div class="font-extrabold text-ink text-xl tabular-nums leading-tight">
                    &#8377;<span x-text="draft ? draft.cab.part_payment : ''"></span>
                </div>
                <span class="text-[10px] text-muted block">Remaining &#8377;<span x-text="draft ? draft.cab.remain_payment : ''"></span> to driver</span>
            </div>

            <button type="button" @click="payNow" class="btn-primary py-3.5 px-6 text-sm font-bold flex-1 flex items-center justify-center gap-2" :disabled="paying || !profileLoaded">
                <span x-show="!paying" class="flex items-center gap-1.5">
                    <span>Pay &amp; Confirm</span>
                    <span class="btn-icon-bubble">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </span>
                </span>
                <span x-show="paying">Processing&hellip;</span>
            </button>
        </div>
    </div>

    <template x-if="paying">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center px-8">
            <div class="bg-white rounded-card-outer p-8 text-center max-w-xs shadow-2xl">
                <svg class="w-10 h-10 mx-auto mb-4 animate-spin text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"/><path d="M22 12a10 10 0 0 0-10-10"/></svg>
                <p class="font-bold text-ink text-sm">Processing your payment&hellip;</p>
                <p class="text-xs text-muted mt-2">Please do not press back or close the browser.</p>
            </div>
        </div>
    </template>

    <template x-if="confirmed">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center px-8">
            <div class="bg-white rounded-card-outer p-7 text-center max-w-xs animate-in shadow-2xl">
                <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-3 animate-pop">
                    <svg class="w-7 h-7 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <h3 class="text-lg font-extrabold text-ink mb-1">Booking Confirmed!</h3>
                <p class="text-xs text-muted mb-4">Your driver has been alerted and will arrive on time.</p>
                <div class="bg-cream rounded-2xl p-4 mb-4 text-xs space-y-1.5 border border-black/[0.04]">
                    <div class="flex justify-between">
                        <span class="text-muted">Start OTP:</span>
                        <span class="font-extrabold text-ink text-sm font-mono tracking-widest" x-text="otp"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted">Booking ID:</span>
                        <span class="font-bold text-ink font-mono" x-text="bookingId"></span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-black/[0.05]">
                        <span class="text-muted">Pay driver at drop:</span>
                        <span class="font-bold text-ink" x-text="'₹' + (draft ? draft.cab.remain_payment : '')"></span>
                    </div>
                </div>
                <a :href="'/app/trip/' + bookingId" class="btn-primary w-full py-3 text-sm font-bold">View Trip Details</a>
            </div>
        </div>
    </template>
</div>
@endsection

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
@push('scripts')
<script>
    function reviewBooking(initialUser = {}, initialCountries = [], initialStates = [], initialPaymentKey = '') {
        return {
            draft: null, tab: 'inclusions',
            countries: initialCountries,
            states: initialStates,
            paymentKey: initialPaymentKey,
            selectedGateway: 'razorpay',
            pgSettings: {
                razorpay_enabled: true,
                cashfree_enabled: false,
                primary: 'both',
                cashfree_mode: 'sandbox',
            },
            traveller: {
                name: initialUser.name || '',
                gender: initialUser.gender != null ? String(initialUser.gender) : '1',
                country_id: initialUser.country_id || '',
                state_id: initialUser.state_id || '',
                email: initialUser.email || '',
                phone_number: initialUser.phone_number || ''
            },
            prefilled: {
                name: !!initialUser.name,
                gender: initialUser.gender != null,
                country_id: !!initialUser.country_id,
                state_id: !!initialUser.state_id,
                email: !!initialUser.email,
                phone_number: !!initialUser.phone_number
            },
            profileLoaded: true,
            gst: {
                enabled: !!initialUser.gstin,
                gstin: initialUser.gstin || '',
                legal_name: initialUser.gst_legal_name || '',
                address: initialUser.gst_billing_address || '',
            },
            gstAmount() { return this.draft && this.draft.cab ? Number(this.draft.cab.tax_amount || 0) : 0; },
            gstPayload() {
                if (!this.gst.enabled || !this.gst.gstin.trim()) return {};
                return {
                    customer_gstin: this.gst.gstin.trim(),
                    customer_legal_name: this.gst.legal_name.trim(),
                    customer_billing_address: this.gst.address.trim(),
                };
            },
            get allPrefilled() { return Object.values(this.prefilled).every(Boolean); },
            error: '', paying: false, confirmed: false, otp: '', bookingId: '',
            goBack() {
                const raw = sessionStorage.getItem('pendingBooking');
                if (raw) {
                    try {
                        const draft = JSON.parse(raw);
                        if (draft.search_query) {
                            window.location.href = '{{ route('customer.book.results') }}' + draft.search_query;
                            return;
                        }
                        if (draft.pickup_from && draft.drop_to) {
                            const params = new URLSearchParams({
                                trip: draft.trip_type === 'Airport Transfer' ? 'airport' : 'city',
                                tab: draft.tab || (draft.air_port_drop ? String(draft.air_port_drop) : (draft.trip_type === 'Airport Transfer' ? '1' : '3')),
                                from: draft.pickup_from,
                                to: draft.drop_to,
                                pickup_date: draft.pickup_date || '',
                                pickup_time: draft.pickup_time || '',
                                air_port_drop: draft.air_port_drop || '',
                            });
                            window.location.href = '{{ route('customer.book.results') }}?' + params.toString();
                            return;
                        }
                    } catch (e) {
                        console.error('Error navigating back to search results:', e);
                    }
                }
                window.location.href = '{{ route('customer.book') }}';
            },
            async load() {
                const raw = sessionStorage.getItem('pendingBooking');
                if (!raw) { window.location.href = '{{ route('customer.book') }}'; return; }
                this.draft = JSON.parse(raw);

                // Fetch payment settings to configure gateway toggles
                try {
                    const settingsRes = await apiFetch('{{ route('customer.actions.settings') }}');
                    if (settingsRes && settingsRes.data) {
                        this.pgSettings.razorpay_enabled = settingsRes.data.razorpay_enabled;
                        this.pgSettings.cashfree_enabled = settingsRes.data.cashfree_enabled;
                        this.pgSettings.primary = settingsRes.data.primary_payment_gateway || 'both';
                        this.pgSettings.cashfree_mode = settingsRes.data.cashfree_mode || 'sandbox';
                        this.paymentKey = settingsRes.data.payment_key || this.paymentKey;

                        if (this.pgSettings.cashfree_enabled && !this.pgSettings.razorpay_enabled) {
                            this.selectedGateway = 'cashfree';
                        } else if (this.pgSettings.primary === 'cashfree' && this.pgSettings.cashfree_enabled) {
                            this.selectedGateway = 'cashfree';
                        } else {
                            this.selectedGateway = 'razorpay';
                        }
                    }
                } catch (e) { /* non-critical */ }

                // If not prefilled by server, fetch as fallback
                if (!this.countries.length) {
                    try {
                        const countriesRes = await apiFetch('{{ route('customer.actions.countries') }}');
                        this.countries = countriesRes.data || [];
                    } catch (e) { /* non-critical */ }
                }
            },
            saveProfile() {
                // Best-effort: persist whatever was collected on this screen (the
                // fields that weren't already on file) so future bookings don't
                // ask for them again. Never blocks or surfaces errors — the
                // booking itself has already succeeded by the time this runs.
                apiFetch('{{ route('customer.actions.account.update') }}', { method: 'POST', body: {
                    name: this.traveller.name,
                    gender: this.traveller.gender,
                    country_id: this.traveller.country_id || null,
                    state_id: this.traveller.state_id || null,
                    email: this.traveller.email,
                    phone_number: this.traveller.phone_number,
                }}).catch(() => {});
            },
            async loadStates(countryId) {
                if (!countryId) { this.states = []; return; }
                try {
                    const res = await apiFetch(`/app/actions/states/${countryId}`);
                    this.states = res.data || [];
                } catch (e) { /* ignore */ }
            },
            async payNow() {
                this.error = '';
                if (!this.profileLoaded) return; // profile still loading — button is disabled, but guard just in case
                if (!this.traveller.name || !this.traveller.email || !this.traveller.phone_number) {
                    this.error = 'Please fill in your name, email and contact number.';
                    return;
                }
                if (this.gst.enabled && (!/^[0-9]{2}[A-Z0-9]{13}$/.test(this.gst.gstin.trim()) || !this.gst.legal_name.trim() || !this.gst.address.trim())) {
                    this.error = 'For a GST invoice, enter a valid 15-character GSTIN, the registered business name and billing address — or untick the GST invoice option.';
                    return;
                }
                this.paying = true;
                try {
                    const [dd, mm, yyyy] = [this.draft.pickup_date.slice(8, 10), this.draft.pickup_date.slice(5, 7), this.draft.pickup_date.slice(0, 4)];
                    const bookingPayload = {
                        customer_id: {{ auth('customer')->id() }},
                        cab_id: this.draft.cab_id,
                        trip_type: this.draft.trip_type,
                        cab_model: this.draft.cab_model,
                        cab_type: this.draft.cab_type,
                        pickup_address: this.draft.pickup_address,
                        drop_of_address: this.draft.drop_of_address,
                        air_port_drop: this.draft.air_port_drop || null,
                        pickup_from: this.draft.pickup_from,
                        drop_to: this.draft.drop_to,
                        pickup_date: `${dd}-${mm}-${yyyy}`,
                        pickup_time: this.draft.pickup_time,
                        base_fare: this.draft.base_fare,
                        tax_amount: this.draft.tax_amount,
                        total_payment: this.draft.total_payment,
                        part_payment: this.draft.part_payment,
                        remain_payment: this.draft.remain_payment,
                        surge_price: this.draft.surge_price,
                        company_payment: this.draft.company_payment,
                        fleet_operator_payment: this.draft.fleet_operator_payment,
                        name: this.traveller.name,
                        gender: this.traveller.gender,
                        country_id: this.traveller.country_id || null,
                        state_id: this.traveller.state_id || null,
                        phone_number: this.traveller.phone_number,
                        ...this.gstPayload(),
                    };
                    const bookingRes = await apiFetch('{{ route('customer.actions.bookings.store') }}', { method: 'POST', body: bookingPayload });
                    this.bookingId = bookingRes.data.booking_id;
                    this.otp = bookingRes.data.otp;

                    if (this.selectedGateway === 'cashfree') {
                        // Cashfree Modal Checkout
                        const cfOrderRes = await apiFetch('{{ route('customer.actions.cashfree.create-order') }}', {
                            method: 'POST',
                            body: {
                                booking_id: this.bookingId,
                                name: this.traveller.name,
                                email: this.traveller.email,
                                phone: this.traveller.phone_number,
                            }
                        });

                        const cashfree = Cashfree({ mode: cfOrderRes.mode || this.pgSettings.cashfree_mode || 'sandbox' });
                        cashfree.checkout({
                            paymentSessionId: cfOrderRes.payment_session_id,
                            redirectTarget: '_modal'
                        }).then(async (result) => {
                            if (result.error) {
                                this.paying = false;
                                this.error = result.error.message || 'Payment was cancelled or failed.';
                                return;
                            }
                            if (result.paymentDetails) {
                                try {
                                    await apiFetch('{{ route('customer.actions.confirm-payment') }}', { method: 'POST', body: {
                                        booking_id: this.bookingId,
                                        status: 1,
                                        transaction_id: cfOrderRes.order_id,
                                        payment_gateway: 'cashfree',
                                        pg_order_id: cfOrderRes.order_id,
                                    }});
                                    sessionStorage.removeItem('pendingBooking');
                                    this.paying = false;
                                    this.confirmed = true;
                                    this.saveProfile();
                                } catch (e) {
                                    this.paying = false;
                                    this.error = 'Payment succeeded but confirmation failed: ' + e.message;
                                }
                            }
                        });
                    } else {
                        // Razorpay Modal Checkout
                        const orderRes = await apiFetch('{{ route('customer.actions.create-order') }}', { method: 'POST', body: { booking_id: this.bookingId } });
                        let paymentKey = this.paymentKey;
                        if (!paymentKey) {
                            const settingsRes = await apiFetch('{{ route('customer.actions.settings') }}');
                            paymentKey = settingsRes.data.payment_key;
                        }

                        const rzp = new Razorpay({
                            key: paymentKey,
                            amount: orderRes.amount,
                            currency: orderRes.currency,
                            order_id: orderRes.order_id,
                            name: 'Seema Cabs Goa',
                            description: 'Booking ' + this.bookingId,
                            prefill: { name: this.traveller.name, email: this.traveller.email, contact: this.traveller.phone_number },
                            theme: { color: '#FEDC33' },
                            handler: async (response) => {
                                try {
                                    await apiFetch('{{ route('customer.actions.confirm-payment') }}', { method: 'POST', body: {
                                        booking_id: this.bookingId,
                                        status: 1,
                                        transaction_id: response.razorpay_payment_id,
                                        payment_gateway: 'razorpay',
                                    }});
                                    sessionStorage.removeItem('pendingBooking');
                                    this.paying = false;
                                    this.confirmed = true;
                                    this.saveProfile();
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
