{{--
    Styled dropdown for a short, static list of options (no search box —
    use searchable-select.blade.php instead for long/fetched lists).
    `$model` and `$options` are JS expression strings evaluated against the
    ancestor Alpine scope. `$options` should resolve to [{value, label}, ...].

    Usage:
        @include('customer.components.simple-select', [
            'model' => 'airportDirection',
            'options' => "[{value:'pickup',label:'Airport Pickup'},{value:'drop',label:'Airport Drop'}]",
        ])
--}}
<div x-data="{ open: false }" @click.outside="open = false" class="relative">
    <button type="button" @click="open = !open" class="field-input flex items-center justify-between text-left w-full">
        <span class="truncate min-w-0" x-text="({{ $options }}.find(o => o.value === {{ $model }}) || {}).label || ''"></span>
        <svg class="w-4 h-4 text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
    </button>
    <div x-show="open" style="display:none"
         class="absolute z-30 mt-1 w-full bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden">
        <template x-for="o in {{ $options }}" :key="o.value">
            <button type="button" @click="{{ $model }} = o.value; open = false"
                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50" x-text="o.label"></button>
        </template>
    </div>
</div>
