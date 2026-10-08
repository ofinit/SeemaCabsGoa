{{--
    Searchable dropdown. `$model` and `$options` are plain JS expression
    strings evaluated against the ANCESTOR Alpine scope (e.g. model="fromValue",
    options="cities" where the including view's x-data exposes `fromValue` and
    `cities: [{id, name}]`). This component only owns its own open/query state.

    Usage:
        @include('customer.components.searchable-select', [
            'model' => 'fromValue', 'options' => 'cities', 'placeholder' => 'Select City',
        ])

    Pass 'onSelect' (a JS statement string) to run extra logic after the model
    is set, e.g. 'onSelect' => 'loadCabs()'.
--}}
@php($onSelect = $onSelect ?? '')
<div x-data="{ open: false, query: '' }" @click.outside="open = false" class="relative">
    <button type="button" @click="open = !open; query = ''" class="field-input flex items-center justify-between text-left w-full">
        <span class="truncate min-w-0"
              x-text="({{ $options }}.find(o => String(o.id) === String({{ $model }})) || {}).name || '{{ $placeholder }}'"
              :class="{{ $model }} ? 'text-ink' : 'text-muted'"></span>
        <svg class="w-4 h-4 text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
    </button>
    <div x-show="open" style="display:none"
         class="absolute z-30 mt-1 w-full bg-white rounded-2xl shadow-card border border-gray-100 max-h-64 overflow-hidden flex flex-col">
        <div class="p-2 border-b border-gray-100 shrink-0">
            <input type="text" x-model="query" placeholder="Search..." autocomplete="off"
                   class="w-full px-3 py-2 text-sm rounded-xl bg-gray-50 focus:outline-none">
        </div>
        <div class="overflow-y-auto">
            <template x-for="o in {{ $options }}.filter(o => o.name.toLowerCase().includes(query.toLowerCase()))" :key="o.id">
                <button type="button" @click="{{ $model }} = o.id; open = false; {{ $onSelect }}"
                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50" x-text="o.name"></button>
            </template>
            <p x-show="!{{ $options }}.filter(o => o.name.toLowerCase().includes(query.toLowerCase())).length" style="display:none" class="px-4 py-3 text-sm text-muted">No results</p>
        </div>
    </div>
</div>
