<nav class="fixed bottom-0 left-0 right-0 z-40 pointer-events-none pb-4 sm:pb-5 px-3">
    <div class="max-w-app mx-auto pointer-events-auto">
        <div class="bg-white/95 backdrop-blur-xl rounded-full shadow-[0_12px_36px_-6px_rgba(20,20,20,0.12),0_4px_12px_rgba(0,0,0,0.04)] border border-slate-200/80 p-1.5 flex items-center justify-between gap-1">

            {{-- 1. Home --}}
            <a href="{{ route('customer.home') }}"
               class="flex flex-col items-center justify-center flex-1 py-1.5 px-1 rounded-full transition-all duration-200 active:scale-95 group {{ request()->routeIs('customer.home') ? 'text-ink font-bold bg-amber-500/10' : 'text-slate-400 hover:text-ink' }}">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-105" style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="{{ request()->routeIs('customer.home') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 10.5 12 3l9 7.5"/>
                    <path d="M5 9.5v10.5a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/>
                </svg>
                <span class="text-[10px] leading-tight mt-0.5 tracking-tight font-semibold">Home</span>
            </a>

            {{-- 2. Advertise (Promo Tag Icon) --}}
            <a href="{{ route('customer.advertise') }}"
               class="flex flex-col items-center justify-center flex-1 py-1.5 px-1 rounded-full transition-all duration-200 active:scale-95 group {{ request()->routeIs('customer.advertise') ? 'text-ink font-bold bg-amber-500/10' : 'text-slate-400 hover:text-ink' }}">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-105" style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="{{ request()->routeIs('customer.advertise') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
                <span class="text-[10px] leading-tight mt-0.5 tracking-tight font-semibold">Advertise</span>
            </a>

            {{-- 3. INTEGRATED LUXURY FLUSH "BOOK" CTA --}}
            <a href="{{ route('customer.book') }}"
               class="group relative flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-full bg-gradient-to-r from-[#F7C948] via-[#F4BE37] to-[#E2A718] text-ink font-extrabold text-xs shadow-md border border-amber-300/80 active:scale-95 transition-all duration-200 shrink-0 hover:shadow-lg {{ request()->routeIs('customer.book*') ? 'ring-2 ring-ink ring-offset-2 ring-offset-white' : '' }}">
                <svg class="w-4 h-4 text-ink shrink-0 group-hover:scale-110 transition-transform duration-200" style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                </svg>
                <span class="tracking-tight font-black text-[12px] uppercase">Book</span>
            </a>

            {{-- 4. Rides / Activity (Route Waypoint Icon) --}}
            <a href="{{ route('customer.rides') }}"
               class="flex flex-col items-center justify-center flex-1 py-1.5 px-1 rounded-full transition-all duration-200 active:scale-95 group {{ request()->routeIs('customer.rides*') ? 'text-ink font-bold bg-amber-500/10' : 'text-slate-400 hover:text-ink' }}">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-105" style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="6" cy="19" r="3"/>
                    <path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/>
                    <circle cx="18" cy="5" r="3"/>
                </svg>
                <span class="text-[10px] leading-tight mt-0.5 tracking-tight font-semibold">Rides</span>
            </a>

            {{-- 5. Account --}}
            <a href="{{ route('customer.account') }}"
               class="flex flex-col items-center justify-center flex-1 py-1.5 px-1 rounded-full transition-all duration-200 active:scale-95 group {{ request()->routeIs('customer.account') ? 'text-ink font-bold bg-amber-500/10' : 'text-slate-400 hover:text-ink' }}">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-105" style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="{{ request()->routeIs('customer.account') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M20 21a8 8 0 0 0-16 0"/>
                </svg>
                <span class="text-[10px] leading-tight mt-0.5 tracking-tight font-semibold">Account</span>
            </a>

        </div>
    </div>
</nav>
