@extends('customer.layouts.app')

@section('title', 'Notifications — Seema Cabs Goa')

@section('content')
<div class="pt-4" x-data="notificationsList(@js($items ?? []))">
    @include('customer.components.topbar', ['title' => 'Notifications'])

    <div class="px-4">
        <div x-show="loading" style="display:none">
            @include('customer.components.spinner')
        </div>
        <p x-show="!loading && !items.length" class="text-center text-muted text-sm py-10">No notifications yet.</p>

        <div class="space-y-3">
            <template x-for="(n, i) in items" :key="n.id">
                <div class="card flex items-start justify-between gap-3 animate-in" :style="`animation-delay: ${i * 60}ms`">
                    <div class="min-w-0">
                        <p class="font-semibold text-ink text-sm" x-text="n.title"></p>
                        <p class="text-xs text-muted mt-1" x-text="n.text"></p>
                    </div>
                    <button @click="remove(n)" class="text-danger shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6h16Z"/></svg>
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function notificationsList(initialItems = []) {
        return {
            items: initialItems,
            loading: false,
            async load() {
                try {
                    const res = await apiFetch('{{ route('customer.actions.notifications.index') }}');
                    this.items = res.data || [];
                } finally {
                    this.loading = false;
                }
            },
            async remove(n) {
                this.items = this.items.filter(i => i.id !== n.id);
                try {
                    await apiFetch(`/app/actions/notifications/${n.id}`, { method: 'DELETE' });
                } catch (e) { /* already removed from view */ }
            },
        };
    }
</script>
@endpush
