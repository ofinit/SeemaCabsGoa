@extends('customer.layouts.app')

@section('title', 'Home — Seema Cabs Goa')

@section('content')
<div class="pt-4 pb-8">
    @include('customer.components.topbar', ['showLogo' => true, 'title' => 'Home'])

    <div class="px-4 space-y-5">
        {{-- Interactive Route Launchpad --}}
        <div>
            <a href="{{ route('customer.book') }}" class="group card-bezel block hover:border-gold/40 transition-all">
                <div class="card-core flex items-center justify-between py-3.5 px-4 bg-white">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-gold/20 flex items-center justify-center text-ink shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        </span>
                        <div>
                            <span class="text-sm font-semibold text-ink block">Where to in Goa?</span>
                            <span class="text-xs text-muted">Airport drops, beach rides, sightseeing</span>
                        </div>
                    </div>
                    <span class="w-8 h-8 rounded-full bg-cream flex items-center justify-center text-ink group-hover:bg-gold transition-colors shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </span>
                </div>
            </a>

            {{-- Rapid Route Chips: one horizontally scrolling row. Touch swipes
                 natively; for mouse users the vertical wheel scrolls sideways and
                 the row can be dragged (a drag never triggers the chip's link). --}}
            <div class="flex gap-2 mt-2.5 overflow-x-auto no-scrollbar py-0.5" style="cursor: grab"
                 x-data="{ down: false, moved: false, startX: 0, startLeft: 0 }"
                 @wheel="const max = $el.scrollWidth - $el.clientWidth, next = Math.max(0, Math.min(max, $el.scrollLeft + $event.deltaY)); if (Math.abs($event.deltaY) > Math.abs($event.deltaX) && next !== $el.scrollLeft) { $event.preventDefault(); $el.scrollLeft = next }"
                 @mousedown="down = true; moved = false; startX = $event.pageX; startLeft = $el.scrollLeft"
                 @mousemove.window="if (down) { const dx = $event.pageX - startX; if (Math.abs(dx) > 5) moved = true; $el.scrollLeft = startLeft - dx }"
                 @mouseup.window="down = false"
                 @click.capture="if (moved) { $event.preventDefault(); $event.stopPropagation(); moved = false }"
                 @dragstart.prevent>
                <a href="{{ route('customer.book', ['trip' => 'airport', 'airportDirection' => 'drop', 'to' => '2']) }}" class="px-3 py-1.5 rounded-pill bg-white border border-black/[0.06] text-xs font-medium text-ink whitespace-nowrap hover:border-gold active:scale-95 transition flex items-center gap-1.5 shadow-sm">
                    <span>✈️</span> Mopa Airport (GOX)
                </a>
                <a href="{{ route('customer.book', ['trip' => 'airport', 'airportDirection' => 'drop', 'to' => '1']) }}" class="px-3 py-1.5 rounded-pill bg-white border border-black/[0.06] text-xs font-medium text-ink whitespace-nowrap hover:border-gold active:scale-95 transition flex items-center gap-1.5 shadow-sm">
                    <span>✈️</span> Dabolim (GOI)
                </a>
                <a href="{{ route('customer.book', ['trip' => 'city', 'to' => 'Calangute']) }}" class="px-3 py-1.5 rounded-pill bg-white border border-black/[0.06] text-xs font-medium text-ink whitespace-nowrap hover:border-gold active:scale-95 transition flex items-center gap-1.5 shadow-sm">
                    <span>🏖️</span> North Goa
                </a>
                <a href="{{ route('customer.book', ['trip' => 'city', 'to' => 'Madgaon']) }}" class="px-3 py-1.5 rounded-pill bg-white border border-black/[0.06] text-xs font-medium text-ink whitespace-nowrap hover:border-gold active:scale-95 transition flex items-center gap-1.5 shadow-sm">
                    <span>🌴</span> South Goa
                </a>
            </div>
        </div>

        @if(!empty($currentBooking))
            <a href="{{ route('customer.trip', $currentBooking['id']) }}" class="card-bezel block bg-ink text-white p-1.5">
                <div class="card-core bg-gradient-to-br from-[#1A1C23] to-[#0E1015] p-4 text-white border border-white/10 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gold block">Ride In Progress</span>
                            <span class="text-xs text-sand/80">{{ $currentBooking['cab_name'] ?? 'Your Cab' }} • OTP: <strong class="text-white tabular-nums">{{ $currentBooking['trip_otp'] ?? '' }}</strong></span>
                        </div>
                    </div>
                    <span class="btn-primary py-1.5 px-3 text-xs shrink-0">Track &raquo;</span>
                </div>
            </a>
        @endif

        @include('customer.components.install-banner')

        {{-- Intent Bento Tiles --}}
        <div>
            <div class="flex items-center justify-between mb-2.5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-muted">Services</h2>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('customer.book', ['trip' => 'airport']) }}" class="col-span-2 group card-bezel block hover:border-gold/40 transition-all">
                    <div class="card-core bg-gradient-to-r from-gold/15 via-white to-white flex items-center justify-between p-4">
                        <div>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gold/30 text-[10px] font-bold uppercase tracking-wider text-yellow-950 mb-1.5">Best Rates</span>
                            <h3 class="font-bold text-ink text-[15px]">Goa Airport Transfers</h3>
                            <p class="text-xs text-muted mt-0.5">Best rates for Mopa (GOX) &amp; Dabolim (GOI)</p>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-gold/20 flex items-center justify-center text-ink shrink-0 group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg>
                        </div>
                    </div>
                </a>

                <a href="{{ route('customer.book', ['trip' => 'city']) }}" class="group card-bezel block hover:border-gold/40 transition-all">
                    <div class="card-core p-4 h-full flex flex-col justify-between">
                        <div class="w-8 h-8 rounded-xl bg-black/5 flex items-center justify-center text-ink mb-3 group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-ink text-sm">Local Rides</h3>
                            <p class="text-[11px] text-muted mt-0.5">Quick beach &amp; club drops</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('customer.book', ['trip' => 'discover']) }}" class="group card-bezel block hover:border-gold/40 transition-all">
                    <div class="card-core p-4 h-full flex flex-col justify-between">
                        <div class="w-8 h-8 rounded-xl bg-black/5 flex items-center justify-center text-ink mb-3 group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/><circle cx="12" cy="12" r="4"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-ink text-sm">Sightseeing</h3>
                            <p class="text-[11px] text-muted mt-0.5">North &amp; South Goa tours</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Advertisements Carousel --}}
        @if(count($ads))
            <div x-data="{
                ads: @js($ads), active: 0, timer: null,
                init() {
                    if (this.ads.length > 1) {
                        this.timer = setInterval(() => { this.active = (this.active + 1) % this.ads.length; }, 4500);
                    }
                }
            }">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-muted">Advertisement</h2>
                    <div class="flex gap-1" x-show="ads.length > 1">
                        <template x-for="(ad, i) in ads" :key="i">
                            <button type="button" @click="active = i" class="w-1.5 h-1.5 rounded-full transition-all duration-300" :class="i === active ? 'bg-gold w-4' : 'bg-gray-300'"></button>
                        </template>
                    </div>
                </div>
                <div class="card p-0 overflow-hidden relative border border-black/[0.06] shadow-sm">
                    <template x-for="(ad, i) in ads" :key="ad.id">
                        <a :href="ad.click_url || ad.banner_url || '#'" target="_blank" rel="noopener sponsored"
                           :data-ad-id="ad.id" :data-ad-screen="ad.screen"
                           class="block relative"
                           x-show="i === active"
                           x-transition:enter="transition ease-out duration-500"
                           x-transition:enter-start="opacity-0 scale-[1.02]"
                           x-transition:enter-end="opacity-100 scale-100"
                           x-transition:leave="transition ease-in duration-300 absolute inset-0"
                           x-transition:leave-start="opacity-100"
                           x-transition:leave-end="opacity-0">
                            <img :src="ad.banner_image" class="w-full h-auto" alt="Advertisement">
                            <span class="ad-report absolute top-1.5 right-1.5 rounded bg-black/55 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white cursor-pointer" role="button" title="Why am I seeing this? / Report">Sponsored ⓘ</span>
                        </a>
                    </template>
                </div>
            </div>
        @endif

        {{-- P2 inline ad card --}}
        @include('customer.components.ad-slot', ['screen' => \App\Services\Ads\AdServer::SCREEN_HOME_INLINE])

        {{-- Fleet Overview --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-xs font-bold uppercase tracking-wider text-muted">Our Fleet in Goa</h2>
                <span class="text-xs text-muted">Verified &amp; Sanitized</span>
            </div>

            <div class="space-y-3.5">
                @foreach([
                    ['name' => 'Hatchback', 'tag' => 'Pocket Friendly', 'seats' => 4, 'baggage' => 2, 'image' => 'hatchback.png', 'desc' => 'WagonR, Swift or similar'],
                    ['name' => 'Sedan', 'tag' => 'Most Popular', 'seats' => 4, 'baggage' => 3, 'image' => 'sedan.png', 'desc' => 'Dzire, Etios or similar'],
                    ['name' => 'SUV', 'tag' => 'Spacious Group', 'seats' => 6, 'baggage' => 4, 'image' => 'suv.png', 'desc' => 'Ertiga, Carens, Innova'],
                ] as $cab)
                    <a href="{{ route('customer.book') }}" class="block card-bezel group transition-all duration-200 hover:-translate-y-0.5 active:scale-[0.99] animate-in" style="animation-delay: {{ $loop->index * 80 }}ms">
                        <div class="card-core flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-bold text-ink text-[15px] group-hover:text-yellow-600 transition-colors">{{ $cab['name'] }}</h3>
                                    <span class="px-2 py-0.5 rounded-full bg-gold/20 text-yellow-950 text-[10px] font-semibold">{{ $cab['tag'] }}</span>
                                </div>
                                <p class="text-xs text-muted mb-2.5">{{ $cab['desc'] }}</p>
                                <div class="flex items-center gap-3 text-xs text-muted font-medium">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                                        AC
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-ink/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        {{ $cab['seats'] }} Seats
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-ink/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="7" width="16" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>
                                        {{ $cab['baggage'] }} Bags
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <img src="{{ asset('cabs/' . $cab['image']) }}" alt="{{ $cab['name'] }}" class="w-24 h-auto drop-shadow-md group-hover:scale-105 transition-transform duration-200">
                                <div class="w-7 h-7 rounded-full bg-amber-50 group-hover:bg-gold text-ink/60 group-hover:text-ink flex items-center justify-center transition-all duration-200 shadow-sm shrink-0">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
