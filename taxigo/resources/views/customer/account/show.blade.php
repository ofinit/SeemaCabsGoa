@extends('customer.layouts.app')

@section('title', 'Account — Seema Cabs Goa')

@section('content')
<div class="pt-4" x-data="accountDetail(@js($userData ?? []), @js($countries ?? []), @js($states ?? []))">
    @include('customer.components.topbar', ['title' => 'Account Detail', 'right' => ''])

    <div class="px-4 space-y-4">

        {{-- 1. READ-ONLY PROFILE OVERVIEW (DEFAULT VIEW) --}}
        <template x-if="!isEditing">
            <div class="card-bezel">
                <div class="card-core p-5 space-y-5">

                    {{-- User Header Banner --}}
                    <div class="flex items-center justify-between gap-3 pb-4 border-b border-black/[0.06]">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-gold to-[#FFE082] text-ink font-black text-lg flex items-center justify-center shadow-sm shrink-0 uppercase tracking-tight"
                                 x-text="getInitials(form.name)">
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="font-extrabold text-ink text-base leading-tight" x-text="form.name || 'Valued Passenger'"></h2>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-[10px] font-bold text-emerald-800 border border-emerald-200/60">
                                        <svg class="w-2.5 h-2.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        Verified
                                    </span>
                                </div>
                                <p class="text-xs text-muted mt-0.5" x-text="form.phone_number ? '+91 ' + form.phone_number : 'Passenger'"></p>
                            </div>
                        </div>

                        <button type="button" 
                                @click="startEdit" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-black/10 bg-black/5 hover:bg-black/10 text-ink text-xs font-bold transition active:scale-95 shrink-0">
                            <svg class="w-3.5 h-3.5 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                            </svg>
                            <span>Edit</span>
                        </button>
                    </div>

                    {{-- Information Details List --}}
                    <div class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between py-1 border-b border-black/[0.04]">
                            <span class="text-muted font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-muted/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Full Name
                            </span>
                            <span class="font-bold text-ink" x-text="form.name || '—'"></span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-black/[0.04]">
                            <span class="text-muted font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-muted/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                Contact Number
                            </span>
                            <span class="font-bold text-ink tabular-nums" x-text="form.phone_number ? '+91 ' + form.phone_number : '—'"></span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-black/[0.04]">
                            <span class="text-muted font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-muted/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                Email Address
                            </span>
                            <span class="font-semibold text-ink" x-text="form.email || '—'"></span>
                        </div>

                        <div class="flex items-center justify-between py-1 border-b border-black/[0.04]">
                            <span class="text-muted font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-muted/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                                Gender
                            </span>
                            <span class="font-bold text-ink" x-text="genderLabel"></span>
                        </div>

                        <div class="flex items-center justify-between py-1">
                            <span class="text-muted font-medium flex items-center gap-2">
                                <svg class="w-4 h-4 text-muted/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                Region / State
                            </span>
                            <span class="font-bold text-ink" x-text="locationLabel"></span>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- 2. EDIT PROFILE MODE (ACTIVE ONLY WHEN USER TAPS EDIT) --}}
        <template x-if="isEditing">
            <div class="card-bezel animate-in">
                <div class="card-core p-5 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-black/[0.06]">
                        <div>
                            <h3 class="font-extrabold text-ink text-base">Edit Account Details</h3>
                            <p class="text-[11px] text-muted">Update your personal contact details</p>
                        </div>
                        <button type="button" @click="cancelEdit" class="text-xs text-muted hover:text-ink font-semibold px-2 py-1">
                            Cancel
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="field-label">Full Name</label>
                            <input x-model="form.name" type="text" class="field-input" placeholder="e.g. Johnson Thomas">
                        </div>

                        <div>
                            <label class="field-label">Gender</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="flex items-center justify-center gap-1.5 border rounded-2xl py-2 px-2 cursor-pointer transition-all text-xs font-bold"
                                       :class="String(form.gender) === '1' ? 'border-ink bg-ink text-white shadow-sm' : 'border-sand bg-white text-muted hover:border-ink/30'">
                                    <input type="radio" x-model="form.gender" value="1" class="hidden"> Male
                                </label>
                                <label class="flex items-center justify-center gap-1.5 border rounded-2xl py-2 px-2 cursor-pointer transition-all text-xs font-bold"
                                       :class="String(form.gender) === '0' ? 'border-ink bg-ink text-white shadow-sm' : 'border-sand bg-white text-muted hover:border-ink/30'">
                                    <input type="radio" x-model="form.gender" value="0" class="hidden"> Female
                                </label>
                                <label class="flex items-center justify-center gap-1.5 border rounded-2xl py-2 px-2 cursor-pointer transition-all text-xs font-bold"
                                       :class="String(form.gender) === '2' ? 'border-ink bg-ink text-white shadow-sm' : 'border-sand bg-white text-muted hover:border-ink/30'">
                                    <input type="radio" x-model="form.gender" value="2" class="hidden"> Other
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="field-label">Country</label>
                                @include('customer.components.searchable-select', ['model' => 'form.country_id', 'options' => 'countries', 'placeholder' => 'Select', 'onSelect' => 'loadStates()'])
                            </div>
                            <div>
                                <label class="field-label">State</label>
                                @include('customer.components.searchable-select', ['model' => 'form.state_id', 'options' => 'states', 'placeholder' => 'Select'])
                            </div>
                        </div>

                        <div>
                            <label class="field-label">Email Address</label>
                            <input x-model="form.email" type="email" class="field-input" placeholder="name@example.com">
                        </div>

                        <div>
                            <label class="field-label">Contact Number</label>
                            <div class="flex gap-2">
                                <span class="field-input w-16 text-center px-0 font-bold bg-sand/30 flex items-center justify-center text-xs text-ink">+91</span>
                                <input x-model="form.phone_number" type="tel" maxlength="10" class="field-input flex-1 tabular-nums font-semibold" placeholder="9876543210">
                            </div>
                        </div>

                        <p x-show="message" x-text="message" style="display:none" class="text-xs font-semibold p-2.5 rounded-xl text-center" :class="messageOk ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-danger border border-danger/20'"></p>

                        <div class="flex gap-2 pt-2">
                            <button type="button" @click="cancelEdit" class="btn-outline flex-1 py-3 text-xs font-bold">
                                Cancel
                            </button>
                            <button type="button" @click="save" class="btn-primary flex-1 py-3 text-xs font-bold" :disabled="saving">
                                <span x-show="!saving">Save Changes</span>
                                <span x-show="saving">Saving&hellip;</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- 3. SECONDARY ACTIONS --}}
        <div x-data="installPrompt()" x-show="show" x-transition style="display:none">
            <button type="button" @click="install" class="btn-outline w-full flex items-center justify-center gap-2 py-3 text-xs font-bold">
                <svg class="w-4 h-4 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="4"/><path d="M12 8v8M8 12h8"/></svg>
                Install Seema Cabs App
            </button>
            @include('customer.components.ios-install-sheet')
        </div>

        <form action="{{ route('customer.logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full py-3 rounded-2xl border border-sand bg-white text-ink text-xs font-bold hover:bg-sand/30 transition-all shadow-xs">
                Log Out
            </button>
        </form>

        <button @click="showDelete = true" class="w-full text-center text-danger/70 hover:text-danger text-xs font-semibold py-1.5 transition-colors">
            Delete Account
        </button>
    </div>

    <!-- DELETE ACCOUNT MODAL (ZERO-SCROLL COMPACT SHEET) -->
    <div x-show="showDelete" 
         x-cloak 
         @click.self="showDelete = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-end justify-center" 
         style="display:none">
        <div class="bg-white rounded-t-[32px] w-full max-w-app mx-auto p-5 pb-6 border-t border-black/10 shadow-2xl overflow-hidden animate-in">
            <div class="w-10 h-1 rounded-full bg-black/15 mx-auto mb-3.5"></div>
            <h3 class="font-extrabold text-ink text-base mb-1">Delete Account</h3>
            <p class="text-xs text-muted mb-3.5 leading-relaxed">This will permanently remove your profile and ride history. Please let us know why you are leaving:</p>
            
            <div class="mb-4 relative">
                <select x-model="deleteReason" class="field-input w-full appearance-none pr-10 text-xs font-semibold bg-white cursor-pointer border border-black/15 focus:border-gold focus:ring-1 focus:ring-gold">
                    <option value="" disabled selected>Select a reason for leaving&hellip;</option>
                    <option value="High fares">High fares</option>
                    <option value="Unreliable service (late arrivals, cancellations)">Unreliable service</option>
                    <option value="Difficult to book">Difficult to book</option>
                    <option value="Limited availability">Limited availability</option>
                    <option value="Poor customer service">Poor customer service</option>
                    <option value="Other">Other</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-muted">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>

            <div class="flex gap-2.5">
                <button type="button" @click="showDelete = false" class="btn-outline flex-1 py-3 text-xs font-bold">
                    Keep Account
                </button>
                <button type="button" @click="deleteAccount" class="btn-primary flex-1 py-3 text-xs font-bold bg-danger hover:bg-danger/90 text-white disabled:opacity-50 disabled:cursor-not-allowed" :disabled="!deleteReason">
                    Delete Permanently
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function accountDetail(initialUser = {}, initialCountries = [], initialStates = []) {
        return {
            isEditing: false,
            form: {
                name: initialUser.name || '',
                gender: String(initialUser.gender ?? '1'),
                country_id: initialUser.country_id || '',
                state_id: initialUser.state_id || '',
                email: initialUser.email || '',
                phone_number: initialUser.phone_number || '',
            },
            originalForm: {},
            initialUser: initialUser,
            countries: initialCountries,
            states: initialStates,
            saving: false,
            message: '',
            messageOk: true,
            showDelete: false,
            deleteReason: '',
            init() {
                this.originalForm = { ...this.form };
            },
            getInitials(name) {
                if (!name) return 'SC';
                const parts = name.trim().split(/\s+/);
                if (parts.length >= 2) {
                    return (parts[0][0] + parts[1][0]).toUpperCase();
                }
                return name.slice(0, 2).toUpperCase();
            },
            get genderLabel() {
                if (String(this.form.gender) === '1') return 'Male';
                if (String(this.form.gender) === '0') return 'Female';
                if (String(this.form.gender) === '2') return 'Other';
                return 'Not specified';
            },
            get locationLabel() {
                let parts = [];
                if (this.form.state_id) {
                    const s = this.states.find(x => String(x.id) === String(this.form.state_id));
                    if (s) parts.push(s.name);
                    else if (this.initialUser.state_name) parts.push(this.initialUser.state_name);
                } else if (this.initialUser.state_name) {
                    parts.push(this.initialUser.state_name);
                }

                if (this.form.country_id) {
                    const c = this.countries.find(x => String(x.id) === String(this.form.country_id));
                    if (c) parts.push(c.name);
                    else if (this.initialUser.country_name) parts.push(this.initialUser.country_name);
                } else if (this.initialUser.country_name) {
                    parts.push(this.initialUser.country_name);
                }

                return parts.length ? parts.join(', ') : 'Goa, India';
            },
            startEdit() {
                this.originalForm = { ...this.form };
                this.message = '';
                this.isEditing = true;
            },
            cancelEdit() {
                this.form = { ...this.originalForm };
                this.message = '';
                this.isEditing = false;
            },
            async loadStates() {
                if (!this.form.country_id) { this.states = []; return; }
                try {
                    const res = await apiFetch(`/app/actions/states/${this.form.country_id}`);
                    this.states = res.data || [];
                } catch (e) { /* ignore */ }
            },
            async save() {
                this.saving = true; 
                this.message = '';
                try {
                    await apiFetch('{{ route('customer.actions.account.update') }}', { 
                        method: 'POST', 
                        body: this.form 
                    });
                    this.message = 'Details updated successfully.'; 
                    this.messageOk = true;
                    this.originalForm = { ...this.form };
                    setTimeout(() => {
                        this.isEditing = false;
                        this.message = '';
                    }, 1000);
                } catch (e) {
                    this.message = e.message; 
                    this.messageOk = false;
                } finally {
                    this.saving = false;
                }
            },
            async deleteAccount() {
                try {
                    await apiFetch('{{ route('customer.actions.account.delete') }}', { 
                        method: 'POST', 
                        body: { reason: this.deleteReason } 
                    });
                    document.querySelector('form[action="{{ route('customer.logout') }}"]').submit();
                } catch (e) {
                    window.showToast('Error', e.message);
                }
            },
        };
    }
</script>
@endpush
