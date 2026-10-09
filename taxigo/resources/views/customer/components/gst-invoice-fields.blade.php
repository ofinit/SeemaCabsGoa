{{--
    Optional business GSTIN for a B2B tax invoice. Expects the parent Alpine
    component to expose `gst` = { enabled, gstin, legal_name, address } and
    `gstPayload()`; see gstInvoiceState() in the page script.
--}}
<div class="card-bezel">
    <div class="card-core p-4 space-y-3">
        <label class="flex items-center justify-between gap-3 cursor-pointer">
            <span>
                <span class="text-sm font-semibold text-ink block">I need a GST invoice for my business</span>
                <span class="text-[11px] text-muted">Optional &middot; lets your business claim the GST back</span>
            </span>
            <input type="checkbox" x-model="gst.enabled" class="w-5 h-5 shrink-0" style="accent-color:#FEDC33">
        </label>
        <div x-show="gst.enabled" x-transition style="display:none" class="space-y-3 pt-1">
            <div>
                <label class="field-label">GSTIN</label>
                <input x-model="gst.gstin" @input="gst.gstin = gst.gstin.toUpperCase()" type="text" maxlength="15"
                    class="field-input font-mono tracking-wider" placeholder="e.g. 30ABCDE1234F1Z5" autocomplete="off">
            </div>
            <div>
                <label class="field-label">Registered business name</label>
                <input x-model="gst.legal_name" type="text" maxlength="191" class="field-input" placeholder="As on GST registration">
            </div>
            <div>
                <label class="field-label">Billing address</label>
                <textarea x-model="gst.address" rows="2" maxlength="500" class="field-input" placeholder="Registered business address"></textarea>
            </div>
        </div>
    </div>
</div>
