@extends('customer.layouts.app')

@php($withNav = false)
@section('title', 'Finding Your Taxi — Seema Cabs Goa')

@section('content')
<div class="min-h-screen flex flex-col px-4 pt-6 pb-10"
     x-data="findingTaxi(@js($topAd ?? null), @js($bottomAd ?? null))" x-init="init">

    <div class="flex-1 space-y-6">
        <template x-for="slot in ads" :key="slot.key">
            <div x-show="slot.ad" style="display:none">
                <h2 class="font-semibold text-ink mb-2">Advertisement</h2>
                <a :href="slot.ad && (slot.ad.banner_url || '#')" target="_blank" class="card p-0 overflow-hidden block">
                    <img :src="slot.ad && slot.ad.banner_image" class="w-full h-auto" alt="Advertisement">
                </a>
            </div>
        </template>
    </div>

    <div class="w-full max-w-xs mx-auto text-center pt-8">
        <div class="relative h-2 bg-gray-200 rounded-full overflow-visible mb-5 mx-3">
            <div class="h-full bg-gold rounded-full" :style="`width: ${progress}%`"></div>
            <div class="absolute -top-4 transition-none" :style="`left: calc(${progress}% - 15px)`">
                <svg class="w-9 h-9 drop-shadow" viewBox="0 0 32 24" fill="none">
                    <rect x="13" y="4.2" width="5.4" height="2.4" rx="0.6" fill="#3B4254" />
                    <path d="M1 15.5c0-1.4.5-2.7 1.4-3.8L5.6 8a5 5 0 0 1 3.9-1.9h9c1.7 0 3.3.8 4.3 2.2l2.6 3.5c.7.9 1.6 1.4 2.6 1.4H29a1.4 1.4 0 0 1 1.4 1.4v2.4a2 2 0 0 1-2 2H2.6a1.6 1.6 0 0 1-1.6-1.6v-1.9Z" fill="#FFCE45" />
                    <path d="M2.2 13.8 3.9 9.4h2.3l-2.4 4.7-1.6-.3Z" fill="#F2643B" />
                    <path d="M10 6.9h7.9c1.2 0 2.4.5 3.2 1.5l1.5 1.7H9.4l.6-3.2Z" fill="#4FC1E9" />
                    <circle cx="12" cy="14.4" r="0.7" fill="#3B4254" />
                    <circle cx="15" cy="14.4" r="0.7" fill="#3B4254" />
                    <circle cx="18" cy="14.4" r="0.7" fill="#3B4254" />
                    <circle cx="21" cy="14.4" r="0.7" fill="#3B4254" />
                    <path d="M24 9.5h2.6l1 1.7H24.7l-.7-1.7Z" fill="#F7F9FB" />
                    <circle cx="7.5" cy="18" r="3" fill="#3B4254" />
                    <circle cx="7.5" cy="18" r="1.2" fill="#F7F9FB" />
                    <circle cx="23.5" cy="18" r="3" fill="#3B4254" />
                    <circle cx="23.5" cy="18" r="1.2" fill="#F7F9FB" />
                </svg>
            </div>
        </div>
        <p class="text-sm font-semibold text-ink">Finding Your Taxi</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function findingTaxi(topAd = null, bottomAd = null) {
        return {
            ads: [{ key: 'top', ad: topAd }, { key: 'bottom', ad: bottomAd }],
            progress: 0,
            async init() {
                if (!sessionStorage.getItem('pendingBooking')) {
                    window.location.href = '{{ route('customer.book') }}';
                    return;
                }

                if (!this.ads[0].ad && !this.ads[1].ad) {
                    this.loadAds();
                }

                const durationMs = 12000;
                const start = Date.now();
                const tick = () => {
                    const elapsed = Date.now() - start;
                    this.progress = Math.min(100, (elapsed / durationMs) * 100);
                    if (this.progress < 100) {
                        requestAnimationFrame(tick);
                    } else {
                        window.location.replace('{{ route('customer.book.review') }}');
                    }
                };
                requestAnimationFrame(tick);
            },
            async loadAds() {
                try {
                    const res = await apiFetch('{{ route('customer.actions.advertisements') }}');
                    this.ads[0].ad = (res.data && res.data.data && res.data.data[0]) || null;
                    this.ads[1].ad = (res.data && res.data.second_data && res.data.second_data[0]) || (res.data && res.data.data && res.data.data[1]) || null;
                } catch (e) { /* ads are non-critical */ }
            },
        };
    }
</script>
@endpush
