@php
    // Usage: @include('customer.components.topbar', ['title' => 'Book Ride', 'back' => true])
    // $back may be `true` (browser back), a route() string, or omitted.
    // $backClick may be an Alpine or JS expression, e.g. 'goBack()'
    $showLogo = $showLogo ?? false;
    $backClick = $backClick ?? null;
@endphp
<div class="sticky top-0 z-30 bg-cream/90 backdrop-blur px-4 pt-4 pb-3">
    <div class="relative flex items-center justify-between gap-3 min-h-[40px]">
        <div class="{{ $showLogo && empty($back) && empty($backClick) ? 'w-28' : 'w-10' }} shrink-0 relative z-10">
            @if(!empty($backClick))
                <a href="{{ (!empty($back) && $back !== true) ? $back : route('customer.book') }}"
                   @click.prevent="{{ $backClick }}"
                   class="w-10 h-10 rounded-full bg-white shadow-card flex items-center justify-center text-ink hover:bg-sand/40 transition-colors cursor-pointer"
                   title="Go Back">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                </a>
            @elseif(!empty($back))
                <a href="{{ $back === true ? "javascript:if(window.history.length>1&&document.referrer){window.history.back();}else{window.location.href='" . route('customer.book') . "';}" : $back }}"
                   class="w-10 h-10 rounded-full bg-white shadow-card flex items-center justify-center text-ink hover:bg-sand/40 transition-colors"
                   title="Go Back">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                </a>
            @elseif($showLogo)
                <img src="{{ asset('app-icons/logo-wordmark.png') }}" alt="Seema Cabs Goa" class="h-9 w-auto">
            @endif
        </div>

        {{-- Absolutely centered on the bar itself, independent of the two side
             slots' widths (logo/back-button/right-slot are never equal width,
             so centering via flex order alone would drift off-center). --}}
        <h1 class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 max-w-[55%] text-base font-semibold text-ink truncate text-center">{{ $title ?? '' }}</h1>

        <div class="w-10 flex justify-end shrink-0 relative z-10">
            {{ $right ?? '' }}
            @if(!isset($right))
                <a href="{{ route('customer.account') }}" class="w-10 h-10 rounded-full bg-white shadow-card flex items-center justify-center text-ink">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 5-6 8-6s6.5 2 8 6"/></svg>
                </a>
            @endif
        </div>
    </div>
</div>
