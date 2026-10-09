@extends('customer.layouts.app')

@section('title', 'Ad ' . $campaign->reference . ' — Seema Cabs Goa')

@php
    use App\Models\AdCampaign;
    $label = $campaign->displayStatus();
    $creatives = $campaign->creatives->keyBy('shape');
@endphp

@section('content')
<div class="pt-4 pb-6" x-data="adCampaign()">
    @include('customer.components.topbar', ['title' => 'Ad ' . $campaign->reference, 'back' => route('customer.advertise')])

    <div class="px-4 space-y-4">
        @if(session('error'))
            <div class="rounded-2xl bg-red-50 border border-red-200 text-danger text-sm px-4 py-3">{{ session('error') }}</div>
        @endif
        <div x-show="error" class="rounded-2xl bg-red-50 border border-red-200 text-danger text-sm px-4 py-3" x-text="error" style="display:none"></div>

        <div class="card space-y-2">
            <div class="flex items-center justify-between gap-2">
                <h2 class="font-semibold text-ink">{{ $campaign->advertiser->business_name }}</h2>
                <span class="status-badge {{ in_array($label, ['Live', 'Scheduled']) ? 'status-badge--completed' : (str_contains($label, 'Rejected') || str_contains($label, 'Cancelled') ? 'status-badge--cancelled' : 'status-badge--confirmed') }}">{{ $label }}</span>
            </div>
            <p class="text-sm text-muted">{{ $campaign->start_date?->format('d M Y') }} – {{ $campaign->end_date?->format('d M Y') }} · {{ $campaign->days }} days</p>
            <p class="text-sm text-ink">{{ $campaign->items->map(fn ($i) => $i->placement->code . ' ' . $i->placement->name)->implode(' · ') }}</p>
            <p class="text-sm text-muted break-all">Link: {{ $campaign->landing_url ?: '—' }}</p>
            <p class="text-sm text-muted">Audience: {{ \App\Services\Ads\AdTargeting::describe($campaign->targeting) }}</p>
            @if($campaign->headline)<p class="text-sm text-muted">Message: {{ $campaign->headline }}</p>@endif

            @switch($campaign->status)
                @case(AdCampaign::IN_REVIEW)
                    <p class="text-sm text-ink pt-1">Paid {{ AdCampaign::rupees($campaign->total_amount) }}. Our team reviews ads within 24 hours — we'll notify you.</p>
                    @break
                @case(AdCampaign::CHANGES_REQUESTED)
                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-sm">
                        <p class="font-semibold">Changes requested</p>
                        <p>{{ implode(', ', $reasons) }}</p>
                        @if($campaign->review_note)<p class="text-muted mt-1">{{ $campaign->review_note }}</p>@endif
                    </div>
                    <a href="{{ route('customer.ads.edit', $campaign) }}" class="btn-primary w-full">Edit &amp; resubmit</a>
                    @break
                @case(AdCampaign::REJECTED)
                    <div class="rounded-xl bg-red-50 border border-red-200 p-3 text-sm">
                        <p class="font-semibold">Not approved</p>
                        <p>{{ implode(', ', $reasons) }}</p>
                        @if($campaign->review_note)<p class="text-muted mt-1">{{ $campaign->review_note }}</p>@endif
                        @if($campaign->refunded_at)<p class="mt-1">Refund of {{ AdCampaign::rupees($campaign->refund_amount) }} started on {{ $campaign->refunded_at->format('d M Y') }}.</p>@endif
                    </div>
                    @break
                @case(AdCampaign::DRAFT)
                @case(AdCampaign::PENDING_PAYMENT)
                    <a href="{{ route('customer.ads.edit', $campaign) }}" class="btn-primary w-full">Continue</a>
                    @break
            @endswitch
        </div>

        @if($campaign->creatives->isNotEmpty())
            <div class="card space-y-3">
                <h2 class="font-semibold text-ink">Images</h2>
                @foreach($creatives as $creative)
                    <img src="{{ $creative->url() }}" class="w-full h-auto rounded-xl border border-black/10" alt="Ad image {{ $creative->shape }}">
                @endforeach
            </div>
        @endif

        @if($campaign->isPaid())
            <div class="card space-y-3">
                <h2 class="font-semibold text-ink">Results</h2>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-xl bg-cream p-3"><p class="text-lg font-extrabold text-ink tabular-nums">{{ number_format($stats['views']) }}</p><p class="text-[11px] text-muted uppercase">Views</p></div>
                    <div class="rounded-xl bg-cream p-3"><p class="text-lg font-extrabold text-ink tabular-nums">{{ number_format($stats['clicks']) }}</p><p class="text-[11px] text-muted uppercase">Taps</p></div>
                    <div class="rounded-xl bg-cream p-3"><p class="text-lg font-extrabold text-ink tabular-nums">{{ $stats['ctr'] === null ? '—' : $stats['ctr'] . '%' }}</p><p class="text-[11px] text-muted uppercase">Tap rate</p></div>
                </div>
                @if($stats['placements'])
                    <table class="w-full text-sm">
                        <thead><tr class="text-muted text-xs"><th class="text-left font-semibold py-1">Placement</th><th class="text-right font-semibold">Views</th><th class="text-right font-semibold">Taps</th></tr></thead>
                        <tbody>
                        @foreach($stats['placements'] as $code => $row)
                            <tr class="border-t border-black/[0.05]"><td class="py-1.5">{{ $code }}</td><td class="text-right tabular-nums">{{ number_format($row['views'] ?? 0) }}</td><td class="text-right tabular-nums">{{ number_format($row['clicks'] ?? 0) }}</td></tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
                @if($stats['days'])
                    <details class="text-sm">
                        <summary class="cursor-pointer text-muted">By day</summary>
                        <table class="w-full mt-2">
                            @foreach($stats['days'] as $day => $row)
                                <tr class="border-t border-black/[0.05]"><td class="py-1">{{ \Carbon\Carbon::parse($day)->format('d M') }}</td><td class="text-right tabular-nums">{{ number_format($row['views'] ?? 0) }} views</td><td class="text-right tabular-nums">{{ number_format($row['clicks'] ?? 0) }} taps</td></tr>
                            @endforeach
                        </table>
                    </details>
                @endif
                <p class="text-xs text-muted">A view counts when at least half the ad is on screen for one second. Numbers update every few minutes.</p>
                @if($stats['qr']['cards'])
                    <p class="text-sm text-ink">In-cab QR cards: {{ $stats['qr']['placed'] }} of {{ $stats['qr']['cards'] }} placed · {{ number_format($stats['qr']['scans']) }} scans</p>
                @endif
                @if($stats['push']['recipients'] || $stats['push']['scheduled'])
                    <p class="text-sm text-ink">Sponsored push:
                        {{ $stats['push']['sent_at'] ? 'sent to ' . number_format($stats['push']['recipients']) . ' customers (' . number_format($stats['push']['delivered']) . ' delivered)' : 'scheduled for ' . $campaign->start_date->format('d M') . ', 10 am – 8 pm' }}</p>
                @endif
                @if($stats['conversions'])
                    <p class="text-sm text-ink">Conversions: {{ number_format($stats['conversions']) }}
                        @if($stats['conversion_value']) · value {{ AdCampaign::rupees($stats['conversion_value']) }}@endif
                        @if($stats['clicks']) · {{ round($stats['conversions'] * 100 / max(1, $stats['clicks']), 1) }}% of taps @endif</p>
                @endif
                <a href="{{ route('customer.ads.export', $campaign) }}" class="text-sm underline">Download results (CSV)</a>
            </div>

            @if($campaign->landing_type === 'website')
                <div class="card space-y-2" id="conversions">
                    <h2 class="font-semibold text-ink">Track leads &amp; sales <span class="text-muted font-normal text-sm">(optional)</span></h2>
                    @if($campaign->conversion_token)
                        <p class="text-xs text-muted">Add this once to every page of your website (your web developer can help):</p>
                        <pre class="text-[11px] bg-cream rounded-xl p-3 overflow-x-auto whitespace-pre-wrap break-all">&lt;script&gt;(function(){var p=new URLSearchParams(location.search),c=p.get('sc_click');if(c){try{localStorage.setItem('sc_click',c)}catch(e){}}
window.seemaConversion=function(label,value){var id='';try{id=localStorage.getItem('sc_click')||''}catch(e){}
new Image().src='{{ route('ads.conversion', $campaign->conversion_token) }}?click='+id+'&amp;label='+encodeURIComponent(label||'lead')+'&amp;value='+(value||0)};})();&lt;/script&gt;</pre>
                        <p class="text-xs text-muted">Then, on your "thank you" page after a booking or enquiry, run <code>seemaConversion('booking', 1500)</code> — the label and the amount in rupees are up to you. Conversions show up in the results above.</p>
                    @else
                        <p class="text-xs text-muted">See how many people who tapped your ad went on to book or enquire on your website.</p>
                        <button type="button" class="btn-outline w-full !py-2.5 text-sm" @click="conversion()" :disabled="busy">Turn on conversion tracking</button>
                    @endif
                </div>
            @endif

            <div class="card space-y-2">
                <h2 class="font-semibold text-ink">Payment</h2>
                <div class="text-sm space-y-1">
                    <div class="flex justify-between"><span class="text-muted">Price</span><span class="tabular-nums">{{ AdCampaign::rupees($campaign->list_amount) }}</span></div>
                    @if($campaign->discount_amount > 0)
                        <div class="flex justify-between"><span class="text-muted">Discount ({{ (float) $campaign->discount_percent }}%)</span><span class="tabular-nums">− {{ AdCampaign::rupees($campaign->discount_amount) }}</span></div>
                    @endif
                    @if($campaign->bundle_discount > 0)
                        <div class="flex justify-between"><span class="text-muted">Bundle saving</span><span class="tabular-nums">− {{ AdCampaign::rupees($campaign->bundle_discount) }}</span></div>
                    @endif
                    @if($campaign->exclusivity_amount > 0)
                        <div class="flex justify-between"><span class="text-muted">Category exclusivity</span><span class="tabular-nums">+ {{ AdCampaign::rupees($campaign->exclusivity_amount) }}</span></div>
                    @endif
                    @if($campaign->promo_discount > 0)
                        <div class="flex justify-between"><span class="text-muted">{{ $campaign->promo_label }}</span><span class="tabular-nums">− {{ AdCampaign::rupees($campaign->promo_discount) }}</span></div>
                    @endif
                    <div class="flex justify-between"><span class="text-muted">GST {{ (float) $campaign->gst_rate }}%</span><span class="tabular-nums">{{ AdCampaign::rupees($campaign->gstAmount()) }}</span></div>
                    <div class="flex justify-between font-bold"><span>Paid</span><span class="tabular-nums">{{ AdCampaign::rupees($campaign->total_amount) }}</span></div>
                    @if($campaign->refund_amount > 0)
                        <div class="flex justify-between text-success"><span>Refunded</span><span class="tabular-nums">{{ AdCampaign::rupees($campaign->refund_amount) }}</span></div>
                    @endif
                </div>
                @foreach($invoices as $invoice)
                    <a class="block text-sm underline" href="{{ \Illuminate\Support\Facades\URL::signedRoute('invoices.public', $invoice) }}" target="_blank">{{ $invoice->label() }} {{ $invoice->number }}</a>
                @endforeach
            </div>
        @endif

        @if(in_array($campaign->status, [AdCampaign::APPROVED, AdCampaign::PAUSED, AdCampaign::EXPIRED]))
            <button type="button" class="btn-primary w-full" @click="renew()" :disabled="busy">Renew this ad</button>
            <p class="text-xs text-muted text-center">Same image and link, from the day after it ends. No new review if you keep them unchanged.</p>
        @endif

        <a href="https://wa.me/919642118800?text={{ rawurlencode('Hi, about my ad ' . $campaign->reference) }}" target="_blank" class="btn-outline w-full">Need help? WhatsApp us</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function adCampaign() {
    return {
        busy: false, error: '',
        async conversion() {
            this.busy = true; this.error = '';
            try {
                const res = await apiFetch(@js(route('customer.actions.ads.campaigns.conversion', $campaign)), { method: 'POST', body: {} });
                window.location.href = res.data.redirect;
                window.location.reload();
            } catch (e) { this.error = e.message; this.busy = false; }
        },
        async renew() {
            this.busy = true; this.error = '';
            try {
                const res = await apiFetch(@js(route('customer.actions.ads.campaigns.renew', $campaign)), { method: 'POST', body: {} });
                window.location.href = res.data.redirect;
            } catch (e) { this.error = e.message; this.busy = false; }
        },
    };
}
</script>
@endpush
