<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>OfinIT billing</title></head>
<body style="margin:0;padding:24px;background:#f6f6f4;font-family:Arial,Helvetica,sans-serif;color:#1a1e23;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;">
    <tr><td style="padding:28px;font-size:15px;line-height:1.5;">
        <p style="margin:0 0 14px;">Hello Seema Holidays team,</p>
        @switch($kind)
            @case('issued')
                <p style="margin:0 0 14px;">OfinIT's invoices for last month are ready. Please pay each one using its payment link <strong>by the 5th</strong>. If anything is still unpaid on the 6th, the Seema Cabs Goa website and app are suspended until it is paid.</p>
                @break
            @case('reminder')
                <p style="margin:0 0 14px;">A reminder that these invoices are due on the 5th.</p>
                @break
            @case('due_today')
                <p style="margin:0 0 14px;"><strong>These invoices are due today.</strong> If they are not paid today, the website and app will be suspended from midnight.</p>
                @break
            @case('suspended')
                <p style="margin:0 0 14px;"><strong>The Seema Cabs Goa website and app are now suspended</strong> because these invoices are unpaid. Rides already under way and SOS keep working. Pay now and the service comes back automatically within minutes.</p>
                @break
            @case('restored')
                <p style="margin:0 0 14px;">Payment received — the Seema Cabs Goa website and app are working again. Thank you.</p>
                @break
        @endswitch
        @if($invoices->isNotEmpty())
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;margin:0 0 18px;">
                @foreach($invoices as $invoice)
                    <tr>
                        <td style="padding:8px 0;border-top:1px solid #eee;">{{ $invoice->label() }} <strong>{{ $invoice->number }}</strong><br><span style="color:#6b7280;">{{ $invoice->meta['description'] ?? '' }} · due {{ optional($invoice->due_date)->format('d M Y') }}</span></td>
                        <td style="padding:8px 0;border-top:1px solid #eee;text-align:right;white-space:nowrap;">&#8377;{{ number_format((float) $invoice->amount_due, 2) }}<br>
                            @if($invoice->payment_link_url)<a href="{{ $invoice->payment_link_url }}" style="color:#1a1e23;font-weight:bold;">Pay now</a>@endif</td>
                    </tr>
                @endforeach
            </table>
        @endif
        <p style="margin:0 0 20px;"><a href="{{ route('admin.billing.index') }}" style="display:inline-block;background:#FEDC33;color:#1a1e23;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:999px;">Open Billing</a></p>
        <p style="margin:0;font-size:12px;color:#6b7280;">OfinIT Solutions Pvt. Ltd. · Platform for Seema Cabs Goa</p>
    </td></tr>
</table>
</body>
</html>
