@extends('customer.layouts.app')

@section('title', 'Advertise With Us — Seema Cabs Goa')

@section('content')
<div class="pt-4">
    @include('customer.components.topbar', ['title' => 'Advertise With Us'])

    <div class="px-4 space-y-4">
        <div class="card text-center">
            <img src="{{ asset('app-icons/logo-wordmark.png') }}" class="w-32 mx-auto mb-4" alt="Seema Cabs Goa">
            <h2 class="font-semibold text-ink mb-1">Advertise Inside Seema Cabs Goa App!</h2>
            <p class="text-sm text-muted mb-4">Promote your business to travellers in Goa — on the home screen, while they wait for a cab, and on their trip.</p>
            @if($enabled)
                <a href="{{ route('customer.ads.create') }}" class="btn-primary w-full">Create an ad</a>
                <p class="text-xs text-muted mt-3">From 7 days · pay online · reviewed within 24 hours · GST invoice</p>
            @else
                <p class="text-sm text-muted">New ad bookings are paused right now. Contact us on WhatsApp to book.</p>
            @endif
        </div>

        @if($campaigns->isNotEmpty())
            <div>
                <h2 class="font-semibold text-ink mb-2">My ads</h2>
                <div class="space-y-2">
                    @foreach($campaigns as $campaign)
                        @php
                            $label = $campaign->displayStatus();
                            $tone = match (true) {
                                in_array($label, ['Live', 'Scheduled']) => 'status-badge--completed',
                                in_array($label, ['In review', 'Awaiting second approval', 'Awaiting payment', 'Draft']) => 'status-badge--confirmed',
                                $label === 'Changes requested' || $label === 'Paused' => 'status-badge--assigned',
                                str_contains($label, 'refunded') => 'status-badge--refunded',
                                default => 'status-badge--cancelled',
                            };
                        @endphp
                        <a href="{{ $campaign->isEditable() && !$campaign->isPaid() ? route('customer.ads.edit', $campaign) : route('customer.ads.show', $campaign) }}" class="card p-4 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                @if($agency && $agency->isApproved())<p class="text-[11px] uppercase tracking-wider text-muted">{{ $campaign->advertiser->business_name ?? '' }}</p>@endif
                                <p class="font-semibold text-ink text-sm">{{ $campaign->reference }}
                                    <span class="text-muted font-normal">· {{ $campaign->items->map(fn ($i) => $i->placement->code)->implode(', ') }}</span></p>
                                <p class="text-xs text-muted mt-0.5">
                                    @if($campaign->start_date)
                                        {{ $campaign->start_date->format('d M') }} – {{ $campaign->end_date->format('d M Y') }} ·
                                    @endif
                                    {{ \App\Models\AdCampaign::rupees($campaign->total_amount) }}
                                </p>
                            </div>
                            <span class="status-badge {{ $tone }} shrink-0">{{ $label }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if(count($ads))
            <div>
                <h2 class="font-semibold text-ink mb-2">Running now</h2>
                <div class="card p-0 overflow-hidden">
                    @foreach($ads as $ad)
                        <a href="{{ $ad['click_url'] ?: ($ad['banner_url'] ?: '#') }}" target="_blank" rel="noopener sponsored"
                           data-ad-id="{{ $ad['id'] }}" data-ad-screen="0" class="block relative">
                            <img src="{{ $ad['banner_image'] }}" class="w-full h-auto" alt="Advertisement" loading="lazy">
                            <span class="ad-report absolute top-1.5 right-1.5 rounded bg-black/55 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white cursor-pointer" role="button" title="Why am I seeing this? / Report">Sponsored ⓘ</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if($enabled)
            <div class="card text-sm" x-data="{ open: false, name: '', gstin: '', msg: '', busy: false,
                async apply() {
                    this.busy = true; this.msg = '';
                    try { const r = await apiFetch(@js(route('customer.actions.ads.agency')), { method: 'POST', body: { name: this.name, gstin: this.gstin } }); this.msg = r.message; this.open = false; }
                    catch (e) { this.msg = e.message; }
                    this.busy = false;
                } }">
                @if($agency && $agency->isApproved())
                    <p class="font-semibold text-ink">Agency account: {{ $agency->name }}</p>
                    <p class="text-muted text-xs mt-1">Book ads for any of your clients — pick or add the client in the first step.</p>
                    <a href="{{ route('customer.ads.create') }}?new_client=1" class="btn-outline w-full mt-3 !py-2.5 text-sm">Add a client</a>
                @elseif($agency)
                    <p class="text-muted">Your agency account request ({{ $agency->name }}) is {{ $agency->status === 'blocked' ? 'not approved' : 'being reviewed' }}.</p>
                @else
                    <button type="button" class="underline text-muted" @click="open = !open">Advertising agency? Manage several clients from one login</button>
                    <div x-show="open" class="space-y-2 mt-3" style="display:none">
                        <input class="field-input" x-model="name" placeholder="Agency name" maxlength="120">
                        <input class="field-input uppercase" x-model="gstin" placeholder="Agency GSTIN (optional)" maxlength="15">
                        <button type="button" class="btn-primary w-full" @click="apply()" :disabled="busy || !name">Apply</button>
                    </div>
                @endif
                <p class="text-xs mt-2" x-show="msg" x-text="msg"></p>
            </div>
        @endif

        <a href="https://wa.me/919642118800" target="_blank" class="btn-success w-full">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.4A10 10 0 1 0 12 2Zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20Zm4.4-5.9c-.2-.1-1.4-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.6.8-.8 1-.1.2-.3.2-.5.1-.2-.1-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.3-1.6-.1-.2 0-.4.1-.5l.4-.4c.1-.1.2-.3.2-.4.1-.2 0-.3 0-.4-.1-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 1.9 0 1.1.8 2.2.9 2.3.1.2 1.6 2.5 4 3.4.5.2 1 .4 1.3.5.6.2 1.1.1 1.5.1.5-.1 1.4-.6 1.6-1.1.2-.5.2-1 .1-1.1-.1-.1-.2-.2-.4-.3Z"/></svg>
            Need help? WhatsApp us
        </a>
        <p class="text-center text-xs text-muted">Advertising space is sold by Seema Holidays. Platform by OfinIT Solutions Pvt. Ltd.</p>
    </div>
</div>
@endsection
