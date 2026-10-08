{{--
    Date field that always DISPLAYS as DD-MM-YYYY, regardless of the visitor's
    browser locale — a plain <input type="date"> shows MM/DD/YYYY, DD/MM/YYYY,
    or YYYY/MM/DD depending on locale, which native HTML can't override. This
    keeps a real native date input (so the familiar native calendar picker
    still opens on tap) but hides it and displays our own reformatted text on
    a styled button instead.

    `model` is a plain JS expression string (the ancestor Alpine scope's date
    variable, stored as ISO yyyy-mm-dd — unchanged, so existing booking-submit
    code that reads it doesn't need to change). `min` is an optional JS
    expression string for the native input's :min binding (e.g. 'today').

    Usage:
        @include('customer.components.date-field', ['model' => 'pickupDate', 'min' => 'today'])
--}}
@php($min = $min ?? null)
<div class="relative" x-data="{
    get displayDate() {
        if (!{{ $model }}) return '';
        const [y, m, d] = {{ $model }}.split('-');
        return `${d}-${m}-${y}`;
    },
    openPicker() {
        const el = this.$refs.nativeDate;
        if (el.showPicker) el.showPicker(); else el.focus();
    },
}">
    <input type="date" x-model="{{ $model }}" x-ref="nativeDate"
           @if($min) :min="{{ $min }}" @endif
           class="absolute opacity-0 w-px h-px overflow-hidden -z-10" tabindex="-1" aria-hidden="true">
    <button type="button" @click="openPicker" class="field-input flex items-center justify-between text-left w-full">
        <span x-text="displayDate || 'DD-MM-YYYY'" :class="{{ $model }} ? 'text-ink' : 'text-muted'"></span>
        <svg class="w-4 h-4 text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
    </button>
</div>
