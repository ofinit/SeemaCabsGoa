{{--
    Dismissible "Add to Home Screen" banner. Shows only when genuinely
    installable: Chrome/Android/desktop Chrome (real beforeinstallprompt) or
    iOS Safari (no native prompt API, so it opens step-by-step instructions
    instead). Hidden entirely once installed, already dismissed, or on a
    browser that supports neither path (e.g. desktop Firefox).
    Usage: @include('customer.components.install-banner')
--}}
<div x-data="installPrompt({ dismissible: true })" x-show="show" x-transition style="display:none" class="mb-5">
    <div class="rounded-2xl bg-gradient-to-r from-peach to-peach-light px-4 py-3 flex items-center gap-3">
        <img src="{{ asset('app-icons/icon-192.png') }}" alt="" class="w-11 h-11 rounded-xl shrink-0">
        <div class="min-w-0 flex-1">
            <p class="font-semibold text-ink text-sm">Add Seema Cabs to Home Screen</p>
            <p class="text-xs text-ink/70">Book rides faster, right from your home screen</p>
        </div>
        <button type="button" @click="install" class="btn-primary py-2 px-3 text-xs shrink-0">Install</button>
        <button type="button" @click="dismiss" class="text-ink/50 text-lg leading-none shrink-0 px-1">&#10005;</button>
    </div>

    @include('customer.components.ios-install-sheet')
</div>
