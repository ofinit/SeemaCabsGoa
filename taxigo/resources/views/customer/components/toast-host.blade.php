<div x-data="toast()" data-toast-host>
    <div
        x-show="visible"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="fixed top-4 left-4 right-4 z-50 max-w-app mx-auto"
        style="display:none"
    >
        <div class="bg-success text-white rounded-2xl shadow-card px-4 py-3 flex items-center justify-between gap-3">
            <div class="min-w-0">
                <p class="font-semibold text-sm truncate" x-text="title"></p>
                <p class="text-xs text-white/90 truncate" x-text="message"></p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a x-show="actionLabel" :href="actionHref" class="bg-white/20 hover:bg-white/30 rounded-pill px-3 py-1.5 text-xs font-semibold flex items-center gap-1">
                    <span x-text="actionLabel"></span>
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
                <button @click="visible=false" class="text-white/80 text-xs px-1">&#10005;</button>
            </div>
        </div>
    </div>
</div>
