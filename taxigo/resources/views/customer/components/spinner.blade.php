{{--
    Shared animated loading spinner, shown instead of plain "Loading..." text
    while a screen's initial data is in flight.
    Usage: @include('customer.components.spinner', ['label' => 'Loading cabs...'])
    `label` is optional; omit for just the spinner with no caption.
--}}
@php($label = $label ?? null)
<div class="flex flex-col items-center justify-center py-10">
    <svg class="w-9 h-9 animate-spin text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
        <circle cx="12" cy="12" r="10" stroke-opacity="0.2" />
        <path d="M22 12a10 10 0 0 0-10-10" stroke-linecap="round" />
    </svg>
    @if($label)
        <p class="text-muted text-sm mt-3">{{ $label }}</p>
    @endif
</div>
