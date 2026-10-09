<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ad {{ $campaign->reference }} results</title>
</head>
<body style="margin:0;padding:24px;background:#f6f6f4;font-family:Arial,Helvetica,sans-serif;color:#1a1e23;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:12px;">
        <tr>
            <td style="padding:28px;">
                <p style="margin:0 0 16px;font-size:15px;">Hello {{ $campaign->advertiser->contact_name ?? 'there' }},</p>
                <p style="margin:0 0 16px;font-size:15px;line-height:1.5;">
                    {{ $kind === 'final' ? 'Your ad has finished. Here are its results' : "Here is last week's summary" }}
                    for <strong>{{ $campaign->advertiser->business_name }}</strong> (ad {{ $campaign->reference }}),
                    {{ \Carbon\Carbon::parse($from)->format('d M') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}.
                </p>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;text-align:center;">
                    <tr>
                        <td style="padding:12px;background:#f6f6f4;border-radius:8px;"><div style="font-size:22px;font-weight:bold;">{{ number_format($stats['views']) }}</div><div style="font-size:12px;color:#6b7280;">VIEWS</div></td>
                        <td style="width:8px;"></td>
                        <td style="padding:12px;background:#f6f6f4;border-radius:8px;"><div style="font-size:22px;font-weight:bold;">{{ number_format($stats['clicks']) }}</div><div style="font-size:12px;color:#6b7280;">TAPS</div></td>
                        <td style="width:8px;"></td>
                        <td style="padding:12px;background:#f6f6f4;border-radius:8px;"><div style="font-size:22px;font-weight:bold;">{{ $stats['ctr'] === null ? '—' : $stats['ctr'] . '%' }}</div><div style="font-size:12px;color:#6b7280;">TAP RATE</div></td>
                    </tr>
                </table>
                @if($stats['placements'])
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px;font-size:14px;">
                        <tr style="color:#6b7280;"><td style="padding:4px 0;">Placement</td><td align="right">Views</td><td align="right">Taps</td></tr>
                        @foreach($stats['placements'] as $code => $row)
                            <tr><td style="padding:4px 0;border-top:1px solid #eee;">{{ $code }}</td><td align="right" style="border-top:1px solid #eee;">{{ number_format($row['views'] ?? 0) }}</td><td align="right" style="border-top:1px solid #eee;">{{ number_format($row['clicks'] ?? 0) }}</td></tr>
                        @endforeach
                    </table>
                @endif
                <p style="margin:0 0 24px;">
                    <a href="{{ route('customer.ads.show', $campaign) }}" style="display:inline-block;background:#FEDC33;color:#1a1e23;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:999px;">{{ $kind === 'final' ? 'See results & renew' : 'See full results' }}</a>
                </p>
                <p style="margin:0;font-size:12px;color:#6b7280;line-height:1.5;">
                    A view counts when at least half the ad is on screen for one second. Seema Holidays · Seema Cabs Goa
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
