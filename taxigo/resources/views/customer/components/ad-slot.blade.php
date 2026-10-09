@php
    // Usage: @include('customer.components.ad-slot', ['screen' => \App\Services\Ads\AdServer::SCREEN_RATING, 'context' => [...]])
    // `context` (optional) is the targeting context: area, trip, package_id.
    // Renders one live ad for the screen (rotating among the booked ones), or nothing.
    $slotAd = \App\Services\Ads\AdServer::forScreen((int) $screen, 5, $context ?? [])->shuffle()->first();
    $slotPayload = $slotAd ? \App\Services\Ads\AdServer::payload($slotAd, (int) $screen, 'pwa') : null;
@endphp
@if($slotPayload)
    <div class="{{ $class ?? '' }}">
        <a href="{{ $slotPayload['click_url'] ?: ($slotPayload['banner_url'] ?: '#') }}" target="_blank" rel="noopener sponsored"
           data-ad-id="{{ $slotPayload['id'] }}" data-ad-screen="{{ (int) $screen }}"
           class="card p-0 overflow-hidden block relative">
            <img src="{{ $slotPayload['banner_image'] }}" class="w-full h-auto" alt="Advertisement" loading="lazy">
            <span class="ad-report absolute top-1.5 right-1.5 rounded bg-black/55 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white cursor-pointer" role="button" title="Why am I seeing this? / Report">Sponsored ⓘ</span>
        </a>
    </div>
@endif
