@php
    use App\Models\Invoice;
    use App\Support\AmountInWords;

    $s = $invoice->supplier ?? [];
    $r = $invoice->recipient ?? [];
    $meta = $invoice->meta ?? [];
    $money = fn ($v) => number_format((float) $v, 2);
    $isPlatform = $invoice->type === Invoice::PLATFORM_FEE || ($meta['platform'] ?? false);
    $showBase = $invoice->lines->whereNotNull('base_amount')->isNotEmpty();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $invoice->label() }} {{ $invoice->number ?? '(draft)' }}</title>
    <style>
        :root { --ink: #1a1e23; --muted: #6b7280; --line: #e5e7eb; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f3f4f6; color: var(--ink); font: 13px/1.45 Arial, Helvetica, sans-serif; }
        .page { max-width: 820px; margin: 24px auto; background: #fff; padding: 36px 40px; position: relative; }
        .draft { position: absolute; top: 40%; left: 0; right: 0; text-align: center; font-size: 96px; font-weight: 700; color: rgba(220, 38, 38, .12); transform: rotate(-20deg); pointer-events: none; }
        .toolbar { max-width: 820px; margin: 16px auto 0; text-align: right; }
        .toolbar button { background: #FEDC33; border: 0; padding: 10px 18px; border-radius: 999px; font-weight: 700; cursor: pointer; }
        h1 { font-size: 20px; margin: 0 0 4px; letter-spacing: .02em; text-transform: uppercase; }
        .grid { display: flex; justify-content: space-between; gap: 24px; }
        .muted { color: var(--muted); }
        .box { border: 1px solid var(--line); border-radius: 6px; padding: 12px 14px; flex: 1; }
        .box h3 { margin: 0 0 6px; font-size: 11px; text-transform: uppercase; color: var(--muted); letter-spacing: .05em; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { padding: 8px 10px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: top; }
        th { font-size: 11px; text-transform: uppercase; color: var(--muted); background: #fafafa; }
        td.num, th.num { text-align: right; white-space: nowrap; }
        .totals { margin-left: auto; width: 340px; margin-top: 8px; }
        .totals td { border: 0; padding: 4px 10px; }
        .totals tr.grand td { border-top: 2px solid var(--ink); font-weight: 700; font-size: 15px; padding-top: 8px; }
        .words { margin-top: 10px; font-weight: 600; }
        .sign { margin-top: 40px; text-align: right; }
        .note { margin-top: 28px; font-size: 11px; color: var(--muted); }
        @media print {
            body { background: #fff; }
            .page { margin: 0; padding: 0; max-width: none; }
            .toolbar { display: none; }
        }
        @media (max-width: 640px) {
            .page { padding: 20px 16px; margin: 0; }
            .grid { flex-direction: column; gap: 12px; }
            .totals { width: 100%; }
        }
    </style>
</head>
<body>
<div class="toolbar"><button type="button" onclick="window.print()">Print / Save as PDF</button></div>
<div class="page">
    @unless($invoice->isIssued())<div class="draft">DRAFT</div>@endunless

    <div class="grid">
        <div>
            <h1>{{ $invoice->label() }}</h1>
            <div class="muted">
                @if($invoice->type === Invoice::RECEIPT_VOUCHER) Advance received (Section 31(3)(d), CGST Act)
                @elseif($invoice->type === Invoice::REFUND_VOUCHER) Refund of advance (Section 31(3)(e), CGST Act)
                @elseif($invoice->type === Invoice::CREDIT_NOTE) Issued under Section 34, CGST Act
                @else Original for recipient @endif
            </div>
        </div>
        <div style="text-align:right">
            <div><strong>No.</strong> {{ $invoice->number ?? 'Not yet issued' }}</div>
            <div><strong>Date:</strong> {{ optional($invoice->issue_date)->format('d M Y') ?? '—' }}</div>
            @if($invoice->period_start)
                <div><strong>Period:</strong> {{ $invoice->period_start->format('d M Y') }} – {{ $invoice->period_end->format('d M Y') }}</div>
            @endif
            @if($invoice->booking)
                <div><strong>Booking:</strong> {{ $invoice->booking->booking_id }}</div>
            @endif
            @if($invoice->related)
                <div><strong>Against:</strong> {{ $invoice->related->label() }} {{ $invoice->related->number }}</div>
            @endif
        </div>
    </div>

    <div class="grid" style="margin-top:20px">
        <div class="box">
            <h3>{{ $isPlatform ? 'Supplier (platform)' : 'Supplier' }}</h3>
            <strong>{{ $s['legal_name'] ?? '' }}</strong>
            @if(!empty($s['trade_name']) && $s['trade_name'] !== ($s['legal_name'] ?? null))<div>({{ $s['trade_name'] }})</div>@endif
            @foreach($s['address'] ?? [] as $line)<div>{{ $line }}</div>@endforeach
            <div>GSTIN: <strong>{{ $s['gstin'] ?? '—' }}</strong></div>
            @if(!empty($s['pan']))<div>PAN: {{ $s['pan'] }}</div>@endif
            @if(!empty($s['email']) || !empty($s['phone']))<div class="muted">{{ trim(($s['email'] ?? '') . '  ' . ($s['phone'] ?? '')) }}</div>@endif
        </div>
        <div class="box">
            <h3>{{ $invoice->is_b2b ? 'Bill to (registered recipient)' : 'Bill to' }}</h3>
            <strong>{{ $r['name'] ?? '' }}</strong>
            @foreach($r['address'] ?? [] as $line)<div>{{ $line }}</div>@endforeach
            @if(!empty($r['gstin']))<div>GSTIN: <strong>{{ $r['gstin'] }}</strong></div>@endif
            @if(!empty($r['state_code']))<div>State code: {{ $r['state_code'] }}</div>@endif
            @if(!$invoice->is_b2b && (!empty($r['email']) || !empty($r['phone'])))<div class="muted">{{ trim(($r['email'] ?? '') . '  ' . ($r['phone'] ?? '')) }}</div>@endif
            <div class="muted" style="margin-top:6px">Place of supply: {{ $invoice->place_of_supply === '30' ? 'Goa (30)' : $invoice->place_of_supply }} &middot; Reverse charge: No</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:36px">#</th>
                <th>Description</th>
                <th>SAC</th>
                @if($showBase)<th class="num">Fare (₹)</th><th class="num">Fee %</th>@endif
                <th class="num">Taxable value (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $i => $line)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $line->description }}</td>
                    <td>{{ $line->sac_code ?? $invoice->sac_code ?? '—' }}</td>
                    @if($showBase)
                        <td class="num">{{ $line->base_amount !== null ? $money($line->base_amount) : '' }}</td>
                        <td class="num">{{ $line->rate_percent !== null ? rtrim(rtrim(number_format((float) $line->rate_percent, 2), '0'), '.') : '' }}</td>
                    @endif
                    <td class="num">{{ $money($line->taxable_value) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Taxable value</td><td class="num">₹{{ $money($invoice->taxable_value) }}</td></tr>
        @if((float) $invoice->igst_amount > 0)
            <tr><td>IGST @ {{ (float) $invoice->igst_rate }}%</td><td class="num">₹{{ $money($invoice->igst_amount) }}</td></tr>
        @else
            <tr><td>CGST @ {{ (float) $invoice->cgst_rate }}%</td><td class="num">₹{{ $money($invoice->cgst_amount) }}</td></tr>
            <tr><td>SGST @ {{ (float) $invoice->sgst_rate }}%</td><td class="num">₹{{ $money($invoice->sgst_amount) }}</td></tr>
        @endif
        <tr class="grand"><td>Total</td><td class="num">₹{{ $money($invoice->total) }}</td></tr>
        @if(($meta['kind'] ?? null) === 'completion' && isset($meta['advance_received']))
            <tr><td class="muted">Less: advance received{{ !empty($meta['receipt_voucher']) ? ' (' . $meta['receipt_voucher'] . ')' : '' }}</td><td class="num muted">₹{{ $money($meta['advance_received']) }}</td></tr>
            <tr><td class="muted">Balance collected by driver</td><td class="num muted">₹{{ $money($meta['balance_collected_by_driver'] ?? 0) }}</td></tr>
        @endif
    </table>
    <div class="words">{{ AmountInWords::inr((float) $invoice->total) }}</div>

    @if(($meta['kind'] ?? null) === 'no_show')
        <p class="note">The advance paid online was forfeited as the customer did not show up for the booked ride, as per the cancellation terms.{{ !empty($meta['receipt_voucher']) ? ' Advance receipt voucher: ' . $meta['receipt_voucher'] . '.' : '' }}</p>
    @endif
    @if(!empty($meta['reason']) && $invoice->type === Invoice::CREDIT_NOTE)
        <p class="note">Reason: {{ $meta['reason'] }}</p>
    @endif
    @if($isPlatform && !empty($s['bank_details']))
        <p class="note"><strong>Bank details:</strong> {{ $s['bank_details'] }}</p>
    @endif

    <div class="sign">
        For <strong>{{ $s['legal_name'] ?? '' }}</strong><br><br><br>
        Authorised Signatory{{ !empty($s['signatory_name']) ? ' — ' . $s['signatory_name'] : '' }}
    </div>
    <p class="note">This is a computer-generated document.</p>
</div>
</body>
</html>
