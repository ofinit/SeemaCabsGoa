@extends('customer.layouts.app')

@section('title', 'Advertise With Us — Seema Cabs Goa')

@section('content')
<div class="pt-4" x-data="advertiseAds(@js($ads ?? []))">
    @include('customer.components.topbar', ['title' => 'Advertise With Us'])

    <div class="px-4 space-y-4">
        <div class="card text-center">
            <img src="{{ asset('app-icons/logo-wordmark.png') }}" class="w-32 mx-auto mb-4" alt="Seema Cabs Goa">
            <h2 class="font-semibold text-ink mb-1">Advertise Inside Seema Cabs Goa App!</h2>
            <p class="text-sm text-muted">Promote your business directly to travelers in Goa! Reach thousands of cab users every day.</p>
        </div>

        <div x-show="ads.length">
            <h2 class="font-semibold text-ink mb-2">Advertisement</h2>
            <div class="card p-0 overflow-hidden">
                <template x-for="ad in ads" :key="ad.id">
                    <a :href="ad.banner_url || '#'" target="_blank"><img :src="ad.banner_image" class="w-full h-auto" alt=""></a>
                </template>
            </div>
        </div>

        <a href="https://wa.me/919822385180" target="_blank" class="btn-success w-full">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.4A10 10 0 1 0 12 2Zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20Zm4.4-5.9c-.2-.1-1.4-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.6.8-.8 1-.1.2-.3.2-.5.1-.2-.1-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.3-1.6-.1-.2 0-.4.1-.5l.4-.4c.1-.1.2-.3.2-.4.1-.2 0-.3 0-.4-.1-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.2-.8.8-.8 1.9 0 1.1.8 2.2.9 2.3.1.2 1.6 2.5 4 3.4.5.2 1 .4 1.3.5.6.2 1.1.1 1.5.1.5-.1 1.4-.6 1.6-1.1.2-.5.2-1 .1-1.1-.1-.1-.2-.2-.4-.3Z"/></svg>
            Contact Us
        </a>
        <p class="text-center text-xs text-muted">Ads are managed exclusively by OfinIT Solutions Pvt. Ltd.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function advertiseAds(initialAds = []) {
        return {
            ads: initialAds,
            async load() {
                if (this.ads.length) return;
                try {
                    const res = await apiFetch('{{ route('customer.actions.advertisements') }}');
                    this.ads = (res.data && res.data.data) || [];
                } catch (e) { /* non-critical */ }
            },
        };
    }
</script>
@endpush
