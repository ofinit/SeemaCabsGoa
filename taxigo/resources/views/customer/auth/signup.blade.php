@extends('customer.layouts.app')

@php($withNav = false)
@php($hideDeveloperFooter = true)
@section('title', 'Sign Up — Seema Cabs Goa')

@section('content')
<div class="min-h-screen px-6 pt-14 pb-10 flex flex-col justify-between" style="background: linear-gradient(180deg, #FBF7F1 0%, #FDE9D2 100%);">
    <div>
    <div class="flex justify-center mb-8">
        <img src="{{ asset('app-icons/logo-wordmark.png') }}" alt="Seema Cabs Goa" class="w-40">
    </div>

    <h1 class="text-2xl font-bold text-ink mb-1">Welcome</h1>
    <p class="text-muted text-sm mb-6">Sign Up and discover Goa by Seema Cabs</p>

    <div class="mb-5">
        <button type="button" onclick="triggerGoogleSignIn('{{ $googleClientId }}')"
                class="btn-pill w-full bg-[#518EF8] text-white">
            <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#fff" d="M21.35 11.1h-9.17v2.92h5.27c-.23 1.4-1.6 4.1-5.27 4.1-3.17 0-5.76-2.62-5.76-5.85s2.59-5.85 5.76-5.85c1.8 0 3.01.77 3.7 1.43l2.52-2.43C16.9 3.7 14.6 2.7 12.18 2.7 6.99 2.7 2.8 6.9 2.8 12.17s4.2 9.47 9.38 9.47c5.42 0 9-3.8 9-9.15 0-.62-.07-1.1-.16-1.4z"/></svg>
            Google Sign Up
        </button>
    </div>

    <div class="flex items-center gap-3 mb-5">
        <div class="h-px bg-gray-200 flex-1"></div>
        <span class="text-xs text-muted">OR</span>
        <div class="h-px bg-gray-200 flex-1"></div>
    </div>

    {{-- Kept out of the x-data="" HTML attribute entirely: @json()'s structural
         double-quotes (unavoidable in valid JSON) would terminate a
         double-quoted attribute early, and this x-data already relies on
         single-quoted JS string literals so a single-quoted attribute isn't
         safe either. A script tag has no such quoting conflict. --}}
    <script type="application/json" id="signup-countries">@json($countries)</script>

    <script>
        function signupForm() {
            return {
                name: '',
                gender: '1',
                country_id: '',
                state_id: '',
                email: '',
                phone_number: '',
                password: '',
                agreeTerms: false,
                states: [],
                loading: false,
                error: '',
                countries: JSON.parse(document.getElementById('signup-countries')?.textContent || '[]'),
                policyModal: {
                    open: false,
                    tab: 'terms',
                    loading: false,
                    content: { terms: '', privacy: '', refund: '' },
                    titles: {
                        terms: 'Terms of Service',
                        privacy: 'Privacy Policy',
                        refund: 'Cancellation & Refund Policy'
                    },
                    urls: {
                        terms: '/terms-and-conditions.html',
                        privacy: '/privacy-policy.html',
                        refund: '/cancellation-refund-policy.html'
                    }
                },
                async openPolicy(tab = 'terms') {
                    this.policyModal.tab = tab;
                    this.policyModal.open = true;
                    if (!this.policyModal.content[tab]) {
                        this.policyModal.loading = true;
                        const slugMap = {
                            terms: 'terms-of-service',
                            privacy: 'privacy-policy',
                            refund: 'cancel-refund'
                        };
                        try {
                            const res = await apiFetch('/app/actions/page-content/' + slugMap[tab]);
                            if (res && res.data && res.data.content) {
                                this.policyModal.content[tab] = res.data.content;
                            }
                        } catch (e) {
                            this.policyModal.content[tab] = '<div class="text-center py-8"><p class="text-muted text-sm">Unable to load document right now. <a href="' + this.policyModal.urls[tab] + '" target="_blank" class="text-[#F36822] underline font-semibold">Click here to view on the website</a></p></div>';
                        } finally {
                            this.policyModal.loading = false;
                        }
                    }
                },
                agreeAndClosePolicy() {
                    this.agreeTerms = true;
                    this.policyModal.open = false;
                },
                async loadStates() {
                    this.state_id = '';
                    this.states = [];
                    if (!this.country_id) return;
                    try {
                        const res = await apiFetch('/app/actions/states/' + this.country_id);
                        this.states = res.data;
                    } catch (e) { /* ignore */ }
                },
                async submit() {
                    if (!this.agreeTerms) {
                        this.error = 'Please agree to the Terms of Service & Privacy Policy to continue.';
                        return;
                    }
                    this.loading = true;
                    this.error = '';
                    try {
                        const res = await apiFetch('{{ route('customer.signup.submit') }}', {
                            method: 'POST',
                            body: {
                                name: this.name,
                                gender: this.gender,
                                country_id: this.country_id,
                                state_id: this.state_id,
                                email: this.email,
                                phone_number: this.phone_number,
                                password: this.password,
                            }
                        });
                        window.location.href = res.data.redirect;
                    } catch (e) {
                        this.error = e.message;
                        this.loading = false;
                    }
                }
            };
        }
    </script>

    <div class="card" x-data="signupForm()">
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="field-label">Full Name</label>
                <input x-model="name" type="text" required class="field-input" placeholder="Enter Your Name">
            </div>

            <div>
                <label class="field-label">Gender</label>
                <div class="flex gap-2">
                    <label class="flex-1 flex items-center justify-center gap-2 border rounded-pill py-2.5 cursor-pointer" :class="gender === '1' ? 'border-gold bg-gold/10' : 'border-gray-200'">
                        <input type="radio" x-model="gender" value="1" class="accent-gold"> Male
                    </label>
                    <label class="flex-1 flex items-center justify-center gap-2 border rounded-pill py-2.5 cursor-pointer" :class="gender === '0' ? 'border-gold bg-gold/10' : 'border-gray-200'">
                        <input type="radio" x-model="gender" value="0" class="accent-gold"> Female
                    </label>
                    <label class="flex-1 flex items-center justify-center gap-2 border rounded-pill py-2.5 cursor-pointer" :class="gender === '2' ? 'border-gold bg-gold/10' : 'border-gray-200'">
                        <input type="radio" x-model="gender" value="2" class="accent-gold"> Other
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="field-label">Country</label>
                    @include('customer.components.searchable-select', ['model' => 'country_id', 'options' => 'countries', 'placeholder' => 'Select Country', 'onSelect' => 'loadStates()'])
                </div>
                <div>
                    <label class="field-label">State</label>
                    @include('customer.components.searchable-select', ['model' => 'state_id', 'options' => 'states', 'placeholder' => 'Select State'])
                </div>
            </div>

            <div>
                <label class="field-label">Email Id</label>
                <input x-model="email" type="email" required class="field-input" placeholder="Enter Email ID">
            </div>

            <div>
                <label class="field-label">Contact Number</label>
                <div class="flex gap-2">
                    <span class="field-input w-20 text-center px-0">+91</span>
                    <input x-model="phone_number" type="tel" maxlength="10" pattern="[0-9]{10}" required class="field-input flex-1" placeholder="Enter 10 digit mobile number">
                </div>
            </div>

            <div>
                <label class="field-label">Password</label>
                <input x-model="password" type="password" required minlength="8" class="field-input" placeholder="Enter Password">
            </div>

            <label class="flex items-start gap-2 text-xs text-muted cursor-pointer select-none">
                <input type="checkbox" x-model="agreeTerms" class="mt-0.5 accent-gold shrink-0">
                <span>
                    I agree to the 
                    <button type="button" @click.prevent.stop="openPolicy('terms')" class="text-[#F36822] hover:underline font-medium inline">Terms of Service</button>, 
                    <button type="button" @click.prevent.stop="openPolicy('privacy')" class="text-[#F36822] hover:underline font-medium inline">Privacy Policy</button>, 
                    <button type="button" @click.prevent.stop="openPolicy('refund')" class="text-[#F36822] hover:underline font-medium inline">Refund Policy</button> 
                    and promotional updates.
                </span>
            </label>

            @include('customer.components.error-banner', ['model' => 'error'])

            <button type="submit" class="btn-primary w-full" :disabled="loading">
                <span x-show="!loading">Sign Up</span>
                <span x-show="loading">Creating account&hellip;</span>
            </button>

            <p class="text-center text-sm text-muted">
                Already have an Account?
                <a href="{{ route('customer.login') }}" class="font-semibold text-[#F36822]">Login</a>
            </p>
        </form>

        <!-- POLICY MODAL / BOTTOM SHEET POPUP -->
        <div x-show="policyModal.open" 
             x-transition.opacity.duration.250ms
             @keydown.escape.window="policyModal.open = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4" 
             style="display:none">
            <div x-show="policyModal.open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="scale-95 opacity-0"
                 x-transition:enter-end="scale-100 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="scale-100 opacity-100"
                 x-transition:leave-end="scale-95 opacity-0"
                 class="bg-white rounded-3xl w-full mx-auto flex flex-col shadow-2xl border border-sand/40 overflow-hidden" 
                 style="max-width: 520px; max-height: 85vh;"
                 @click.outside="policyModal.open = false">

                <!-- Header -->
                <div class="px-5 pt-4 pb-3 border-b border-gray-100 flex items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-8 h-8 rounded-full bg-[#F36822]/10 text-[#F36822] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </span>
                        <h3 class="font-bold text-ink text-base truncate" x-text="policyModal.titles[policyModal.tab]"></h3>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <a :href="policyModal.urls[policyModal.tab]" target="_blank" title="Open in new window" 
                           class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-muted hover:text-ink transition">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                <polyline points="15 3 21 3 21 9"></polyline>
                                <line x1="10" y1="14" x2="21" y2="3"></line>
                            </svg>
                        </a>
                        <button type="button" @click="policyModal.open = false" 
                                class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-ink text-lg font-semibold transition">&times;</button>
                    </div>
                </div>

                <!-- Segmented Tab Switcher -->
                <div class="px-5 pt-2.5 pb-2.5 bg-gray-50/80 border-b border-gray-100 flex gap-2 overflow-x-auto no-scrollbar shrink-0">
                    <button type="button" @click="openPolicy('terms')" 
                            :class="policyModal.tab === 'terms' ? 'bg-[#F36822] text-white font-semibold shadow-sm' : 'bg-white text-muted hover:text-ink border border-gray-200'"
                            class="px-3.5 py-1.5 rounded-full text-xs shrink-0 transition">Terms of Service</button>
                    <button type="button" @click="openPolicy('privacy')" 
                            :class="policyModal.tab === 'privacy' ? 'bg-[#F36822] text-white font-semibold shadow-sm' : 'bg-white text-muted hover:text-ink border border-gray-200'"
                            class="px-3.5 py-1.5 rounded-full text-xs shrink-0 transition">Privacy Policy</button>
                    <button type="button" @click="openPolicy('refund')" 
                            :class="policyModal.tab === 'refund' ? 'bg-[#F36822] text-white font-semibold shadow-sm' : 'bg-white text-muted hover:text-ink border border-gray-200'"
                            class="px-3.5 py-1.5 rounded-full text-xs shrink-0 transition">Refund Policy</button>
                </div>

                <!-- Content Body -->
                <div class="flex-1 overflow-y-auto px-5 py-4 text-xs text-ink/85 leading-relaxed" style="min-height: 200px;">
                    <!-- Loader -->
                    <div x-show="policyModal.loading" class="flex flex-col items-center justify-center py-12 gap-3">
                        <svg class="animate-spin w-6 h-6 text-[#F36822]" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span class="text-xs text-muted">Loading document&hellip;</span>
                    </div>

                    <!-- Document Html Content -->
                    <div x-show="!policyModal.loading" class="space-y-2 [&_h2]:text-sm [&_h2]:font-bold [&_h2]:text-ink [&_h2]:mt-4 [&_h2]:mb-1 [&_p]:mb-2 [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:mb-2 [&_li]:mb-1 [&_strong]:text-ink" 
                         x-html="policyModal.content[policyModal.tab]"></div>
                </div>

                <!-- Footer Action Bar -->
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/80 flex gap-2.5 items-center shrink-0">
                    <button type="button" @click="policyModal.open = false" 
                            class="btn-outline flex-1 py-2.5 text-xs font-semibold">Close</button>
                    <button type="button" @click="agreeAndClosePolicy" 
                            class="btn-primary flex-1 py-2.5 text-xs font-semibold bg-[#F36822] hover:bg-[#E05915] text-white shadow-sm flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        <span>I Agree &amp; Accept</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>

    @include('customer.components.developer-footer', ['wrapperClass' => 'mt-8 py-0'])
</div>
@endsection
