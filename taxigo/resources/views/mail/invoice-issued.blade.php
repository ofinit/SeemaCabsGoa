<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->label() }} {{ $invoice->number }}</title>
</head>
<body style="margin:0;padding:24px;background:#f6f6f4;font-family:Arial,Helvetica,sans-serif;color:#1a1e23;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:12px;">
        <tr>
            <td style="padding:28px;">
                <p style="margin:0 0 16px;font-size:15px;">Hello {{ $invoice->recipient['name'] ?? 'there' }},</p>
                <p style="margin:0 0 16px;font-size:15px;line-height:1.5;">
                    Your {{ strtolower($invoice->label()) }} <strong>{{ $invoice->number }}</strong>
                    @if($invoice->booking) for booking <strong>{{ $invoice->booking->booking_id }}</strong> @endif
                    is ready.
                </p>
                <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 20px;font-size:14px;">
                    <tr><td style="padding:2px 16px 2px 0;color:#6b7280;">Date</td><td>{{ optional($invoice->issue_date)->format('d M Y') }}</td></tr>
                    <tr><td style="padding:2px 16px 2px 0;color:#6b7280;">Amount</td><td><strong>&#8377;{{ number_format((float) $invoice->total, 2) }}</strong></td></tr>
                </table>
                <p style="margin:0 0 24px;">
                    <a href="{{ $invoice->publicUrl() }}" style="display:inline-block;background:#FEDC33;color:#1a1e23;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:999px;">View / download</a>
                </p>
                <p style="margin:0;font-size:12px;color:#6b7280;line-height:1.5;">
                    Use your browser's Print &rarr; Save as PDF to keep a copy.<br>
                    {{ $invoice->supplier['legal_name'] ?? 'Seema Holidays' }}
                    @if(!empty($invoice->supplier['gstin'])) &middot; GSTIN {{ $invoice->supplier['gstin'] }} @endif
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
