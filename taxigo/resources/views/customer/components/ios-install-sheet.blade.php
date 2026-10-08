{{--
    Step-by-step "Add to Home Screen" instructions for iOS Safari, which has
    no programmatic install API. Include inside an element whose Alpine scope
    defines `showIosInstructions` (i.e. the installPrompt() data factory).
--}}
<div x-show="showIosInstructions" x-transition.opacity class="fixed inset-0 bg-black/40 z-40" @click="showIosInstructions = false" style="display:none"></div>
<div x-show="showIosInstructions"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="translate-y-full"
     x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-y-0"
     x-transition:leave-end="translate-y-full"
     class="fixed bottom-0 left-0 right-0 z-50 max-w-app mx-auto bg-white rounded-t-[28px] shadow-sheet p-6 max-h-[85vh] overflow-y-auto"
     style="display:none">
    <div class="w-10 h-1.5 bg-gray-200 rounded-full mx-auto mb-4"></div>
    <h3 class="font-semibold text-ink mb-4">Add to Home Screen</h3>
    <div class="space-y-4">
        <div class="flex items-start gap-3">
            <span class="w-6 h-6 rounded-full bg-gold text-ink text-xs font-bold flex items-center justify-center shrink-0">1</span>
            <p class="text-sm text-ink pt-0.5">
                Tap the Share icon
                <svg class="w-4 h-4 inline-block align-text-bottom mx-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M8 7l4-4 4 4"/><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"/></svg>
                in Safari's toolbar.
            </p>
        </div>
        <div class="flex items-start gap-3">
            <span class="w-6 h-6 rounded-full bg-gold text-ink text-xs font-bold flex items-center justify-center shrink-0">2</span>
            <p class="text-sm text-ink pt-0.5">Scroll down and tap <span class="font-semibold">Add to Home Screen</span>.</p>
        </div>
        <div class="flex items-start gap-3">
            <span class="w-6 h-6 rounded-full bg-gold text-ink text-xs font-bold flex items-center justify-center shrink-0">3</span>
            <p class="text-sm text-ink pt-0.5">Tap <span class="font-semibold">Add</span> in the top-right corner.</p>
        </div>
    </div>
    <button type="button" @click="showIosInstructions = false" class="btn-outline w-full mt-6">Got It</button>
</div>
