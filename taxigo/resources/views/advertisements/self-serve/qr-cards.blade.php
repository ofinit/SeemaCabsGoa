<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR cards — {{ $campaign->reference }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #1a1e23; margin: 0; background: #f4f4f2; }
        .bar { background: #fff; padding: 16px 20px; border-bottom: 1px solid #ddd; }
        .bar h1 { font-size: 18px; margin: 0 0 4px; }
        .bar p { margin: 4px 0; font-size: 13px; color: #555; }
        table { border-collapse: collapse; width: 100%; font-size: 13px; background: #fff; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #eee; text-align: left; }
        .msg { padding: 8px 20px; font-size: 13px; }
        .msg.ok { color: #0a7a3d; } .msg.err { color: #b42318; }
        .sheet { display: flex; flex-wrap: wrap; gap: 8mm; padding: 10mm; }
        /* A6 portrait: 105 × 148 mm */
        .card { width: 105mm; height: 148mm; box-sizing: border-box; background: #fff; border: 0.3mm dashed #bbb; padding: 6mm;
                display: flex; flex-direction: column; align-items: center; page-break-inside: avoid; break-inside: avoid; }
        .card .ad { width: 100%; height: 72mm; object-fit: cover; border-radius: 3mm; }
        .card .qr { width: 42mm; height: 42mm; margin-top: 4mm; }
        .card .qr svg { width: 100%; height: 100%; }
        .card .cta { font-weight: bold; font-size: 14pt; margin-top: 2mm; }
        .card .meta { margin-top: auto; width: 100%; display: flex; justify-content: space-between; font-size: 7pt; color: #777; }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .sheet { padding: 0; gap: 4mm; }
            @page { size: A4; margin: 8mm; }
        }
    </style>
</head>
<body>
    <div class="bar no-print">
        <h1>In-cab QR cards — {{ $campaign->reference }} · {{ $campaign->advertiser->business_name }}</h1>
        <p>{{ $campaign->start_date->format('d M Y') }} → {{ $campaign->end_date->format('d M Y') }} · {{ $campaign->qrCards->count() }} card(s), one per cab. Each card has its own QR code, so scans are counted per cab.</p>
        <p>Print on A4 (4 A6 cards per sheet, cut along the dashed lines), assign each card to a cab and record the date it was placed. <button onclick="window.print()">Print cards</button>
            <a href="{{ route('admin.advertisements.selfServe.show', $campaign) }}">Back to the ad</a></p>
    </div>
    @if (session('success'))<div class="msg ok no-print">{{ session('success') }}</div>@endif
    @if (session('error'))<div class="msg err no-print">{{ session('error') }}</div>@endif

    <div class="no-print" style="padding: 0 20px 10px;">
        <table>
            <thead><tr><th>Card</th><th>Cab</th><th>Placed on</th><th>Removed on</th><th>Scans</th><th></th></tr></thead>
            <tbody>
                @foreach ($campaign->qrCards as $card)
                    <tr>
                        <td><code>{{ $card->code }}</code></td>
                        <td><select name="cab_id" form="qr-{{ $card->id }}"><option value="">—</option>@foreach ($cabs as $cab)<option value="{{ $cab->id }}" @selected($card->cab_id === $cab->id)>{{ $cab->number }}</option>@endforeach</select></td>
                        <td><input type="date" name="placed_on" form="qr-{{ $card->id }}" value="{{ $card->placed_on?->format('Y-m-d') }}"></td>
                        <td><input type="date" name="removed_on" form="qr-{{ $card->id }}" value="{{ $card->removed_on?->format('Y-m-d') }}"></td>
                        <td>{{ $card->scans }}</td>
                        <td><button form="qr-{{ $card->id }}">Save</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @foreach ($campaign->qrCards as $card)
            <form id="qr-{{ $card->id }}" method="POST" action="{{ route('admin.advertisements.selfServe.qrCards.update', $card) }}">@csrf</form>
        @endforeach
    </div>

    <div class="sheet">
        @foreach ($campaign->qrCards as $card)
            <div class="card">
                @if ($creative)<img class="ad" src="{{ $creative->url() }}" alt="">@endif
                <div class="qr">{!! \App\Services\Ads\AdQrService::svg($card, 400) !!}</div>
                <div class="cta">Scan for the offer</div>
                <div class="meta"><span>Sponsored · Seema Cabs Goa</span><span>{{ $card->code }}{{ $card->cab ? ' · ' . $card->cab->number : '' }}</span></div>
            </div>
        @endforeach
    </div>
</body>
</html>
