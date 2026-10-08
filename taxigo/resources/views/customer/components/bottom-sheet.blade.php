{{--
    Visual-only bottom sheet. Include this inside any element whose Alpine
    scope defines a boolean `open` (e.g. x-data="{ open: false }") — it does
    not declare its own x-data so it can share state with the trigger button
    next to it.

    Usage:
        <div x-data="{ open: false }">
            <button @click="open = true">Select Date & Time</button>
            @component('customer.components.bottom-sheet')
                ...sheet content...
            @endcomponent
        </div>
--}}
<div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/40 z-40" @click="open = false" style="display:none"></div>
<div x-show="open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="translate-y-full"
     x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-y-0"
     x-transition:leave-end="translate-y-full"
     class="fixed bottom-0 left-0 right-0 z-50 max-w-app mx-auto bg-white rounded-t-[28px] shadow-sheet p-5 max-h-[85vh] overflow-y-auto"
     style="display:none">
    <div class="w-10 h-1.5 bg-gray-200 rounded-full mx-auto mb-4"></div>
    {{ $slot }}
</div>
