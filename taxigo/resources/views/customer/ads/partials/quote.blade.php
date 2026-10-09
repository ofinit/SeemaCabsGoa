{{-- Price breakdown for the ad wizard (Alpine: `quote`, `money()`). Amounts are paise. --}}
<template x-if="quote">
    <div class="text-sm space-y-1.5">
        <div class="flex justify-between"><span class="text-muted" x-text="'Placements × ' + (quote.days || days) + ' days'"></span><span class="tabular-nums" x-text="money(quote.list_amount)"></span></div>
        <div class="flex justify-between" x-show="quote.discount_amount > 0"><span class="text-muted" x-text="'Discount (' + Number(quote.discount_percent) + '%)'"></span><span class="tabular-nums text-success" x-text="'− ' + money(quote.discount_amount)"></span></div>
        <div class="flex justify-between" x-show="!quote.inter_state"><span class="text-muted" x-text="'CGST ' + (quote.gst_rate / 2) + '% + SGST ' + (quote.gst_rate / 2) + '%'"></span><span class="tabular-nums" x-text="money(quote.cgst_amount + quote.sgst_amount)"></span></div>
        <div class="flex justify-between" x-show="quote.inter_state"><span class="text-muted" x-text="'IGST ' + quote.gst_rate + '%'"></span><span class="tabular-nums" x-text="money(quote.igst_amount)"></span></div>
        <div class="flex justify-between pt-1.5 border-t border-black/[0.06] font-bold text-ink"><span>Total</span><span class="tabular-nums" x-text="money(quote.total_amount)"></span></div>
    </div>
</template>
