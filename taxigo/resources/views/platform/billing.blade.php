@extends('layouts.main')
@section('title', 'Billing')
@php use App\Models\Invoice; @endphp
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item" aria-current="page">Billing</li>
                    </ul>
                </div>
                <div class="col-md-12"><div class="page-header-title"><h2 class="mb-0">Billing — OfinIT platform</h2></div></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>

        @if ($suspended)
            <div class="col-12">
                <div class="alert alert-danger">
                    <strong>The service is suspended</strong> since {{ $suspendedAt ? \Carbon\Carbon::parse($suspendedAt)->format('d M Y, H:i') : '—' }}.
                    The website and app show "temporarily unavailable"; rides under way and SOS still work. Pay the invoices below and the service comes back automatically.
                </div>
            </div>
        @endif

        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">To pay</h5>
                    <p class="small text-muted mb-0">Invoices are issued on the 1st and due on the <strong>5th</strong>. Anything unpaid on the 6th suspends the website and app. Each invoice has its own Razorpay payment link (OfinIT Solutions).</p></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Invoice</th><th>For</th><th class="text-end">Invoice total</th><th class="text-end">Already paid by split / credits</th><th class="text-end">Amount due</th><th>Due</th><th></th></tr></thead>
                            <tbody>
                                @forelse ($unpaid as $invoice)
                                    @php $late = $invoice->due_date && $invoice->due_date->toDateString() < now('Asia/Kolkata')->toDateString(); @endphp
                                    <tr class="{{ $late ? 'table-danger' : '' }}">
                                        <td><a href="{{ route('admin.billing.invoice', $invoice) }}" target="_blank">{{ $invoice->number }}</a><div class="small text-muted">{{ $invoice->label() }}</div></td>
                                        <td class="small">{{ $invoice->meta['description'] ?? '' }}</td>
                                        <td class="text-end">₹{{ number_format((float) $invoice->total, 2) }}</td>
                                        <td class="text-end small">₹{{ number_format((float) $invoice->collected_by_split + (float) $invoice->credit_applied, 2) }}</td>
                                        <td class="text-end fw-bold">₹{{ number_format((float) $invoice->amount_due, 2) }}</td>
                                        <td class="small {{ $late ? 'text-danger fw-bold' : '' }}">{{ optional($invoice->due_date)->format('d M Y') }}{{ $late ? ' — overdue' : '' }}</td>
                                        <td class="text-end text-nowrap">
                                            @if ($invoice->payment_link_url)
                                                <a class="btn btn-sm btn-success" href="{{ $invoice->payment_link_url }}" target="_blank" rel="noopener">Pay now</a>
                                            @else
                                                <span class="small text-muted">Payment link pending</span>
                                            @endif
                                            @if ($isAdmin)
                                                <div class="mt-1 d-flex gap-1 justify-content-end">
                                                    @if ($invoice->payment_link_id)
                                                        <form method="POST" action="{{ route('admin.platformBilling.checkLink', $invoice) }}">@csrf<button class="btn btn-sm btn-light-secondary">Check</button></form>
                                                    @endif
                                                    <form method="POST" action="{{ route('admin.platformBilling.createLink', $invoice) }}" onsubmit="return confirm('Create a new payment link? The old one is cancelled.')">@csrf<button class="btn btn-sm btn-light-secondary">{{ $invoice->payment_link_id ? 'New link' : 'Create link' }}</button></form>
                                                    <button class="btn btn-sm btn-light-primary" type="button" data-bs-toggle="collapse" data-bs-target="#pay-{{ $invoice->id }}">Record bank payment</button>
                                                </div>
                                                <form method="POST" action="{{ route('admin.platformBilling.recordPayment', $invoice) }}" class="collapse mt-2" id="pay-{{ $invoice->id }}">
                                                    @csrf
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <input class="form-control form-control-sm" name="reference" placeholder="UTR / reference" required style="max-width:160px">
                                                        <input class="form-control form-control-sm" type="date" name="paid_on" value="{{ now('Asia/Kolkata')->toDateString() }}" required style="max-width:150px">
                                                        <button class="btn btn-sm btn-primary">Save</button>
                                                    </div>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">Nothing to pay. 🎉</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Invoices &amp; credit notes</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead><tr><th>Document</th><th>Period</th><th class="text-end">Total</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse ($documents as $doc)
                                    <tr>
                                        <td><a href="{{ route('admin.billing.invoice', $doc) }}" target="_blank">{{ $doc->number }}</a> <span class="small text-muted">{{ $doc->label() }}</span></td>
                                        <td class="small">{{ optional($doc->period_start)->format('M Y') }}</td>
                                        <td class="text-end">₹{{ number_format((float) $doc->total, 2) }}</td>
                                        <td class="small">
                                            @if ($doc->type === Invoice::CREDIT_NOTE) credit note
                                            @elseif ($doc->paid_at) paid {{ $doc->paid_at->format('d M Y') }} ({{ ['razorpay_link' => 'payment link', 'bank' => 'bank transfer', 'split' => 'gateway split', 'credit' => 'credit note'][$doc->paid_via] ?? ($doc->paid_via ?: 'recorded') }}{{ $doc->payment_reference ? ' · ' . $doc->payment_reference : '' }})
                                            @else unpaid @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted small p-3">No invoices yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if ($isAdmin)
            <div class="col-lg-6">
                <form method="POST" action="{{ route('admin.platformBilling.settings') }}" class="card">
                    @csrf
                    <div class="card-header"><h5 class="mb-0">Settlement method <span class="badge bg-light-secondary">OfinIT only</span></h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-check"><input class="form-check-input" type="radio" name="mode" value="invoice" @checked($mode === 'invoice')>
                                <span class="form-check-label"><strong>Monthly invoice</strong> — Seema collects every payment; OfinIT invoices on the 1st (ride platform fee + GST; ad share, 90% incl. GST), due on the 5th, suspension on the 6th if unpaid.</span></label>
                            <label class="form-check mt-2"><input class="form-check-input" type="radio" name="mode" value="split" @checked($mode === 'split')>
                                <span class="form-check-label"><strong>Gateway split / Route</strong> — OfinIT's share is split out at Razorpay / Cashfree on every payment (needs the split feature enabled on Seema's gateway accounts).</span></label>
                        </div>
                        <p class="small text-muted">Suspension and restore are automatic: anything unpaid on the 6th suspends the website and app; the service comes back the moment Razorpay captures the payment (or OfinIT records a bank payment).</p>
                        <div class="mb-3">
                            <label class="form-label small mb-0">Seema billing emails (invoices, payment links, reminders)</label>
                            <input class="form-control form-control-sm" name="billing_emails" value="{{ old('billing_emails', $billingEmails) }}" placeholder="accounts@seemaholidays.com, owner@…">
                            <span class="small text-muted">Separate with commas. Empty = Seema's business-profile email.</span>
                        </div>
                        <h6 class="mt-4">OfinIT's Razorpay account (payment links)</h6>
                        <p class="small text-muted">Used only on the server to create and check a payment link for each invoice. Never shown on the website.</p>
                        <div class="mb-2"><label class="form-label small mb-0">Key id</label><input class="form-control form-control-sm" name="razorpay_key" value="{{ $razorpayKey }}" placeholder="rzp_live_…"></div>
                        <div class="mb-2"><label class="form-label small mb-0">Key secret {!! $razorpayReady ? '<span class="text-success">· saved</span>' : '' !!}</label><input class="form-control form-control-sm" type="password" name="razorpay_secret" autocomplete="new-password" placeholder="{{ $razorpayReady ? 'Leave empty to keep' : '' }}"></div>
                        <div class="mb-2"><label class="form-label small mb-0">Webhook secret {!! $webhookReady ? '<span class="text-success">· saved</span>' : '' !!}</label><input class="form-control form-control-sm" type="password" name="webhook_secret" autocomplete="new-password" placeholder="{{ $webhookReady ? 'Leave empty to keep' : '' }}"></div>
                        <p class="small text-muted mb-0">In OfinIT's Razorpay dashboard → Webhooks, add <code>{{ url('/api/ofinit/razorpay/webhook') }}</code> with the event <strong>payment_link.paid</strong> and the same secret.</p>
                    </div>
                    <div class="card-footer"><button class="btn btn-primary">Save</button></div>
                </form>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Actions <span class="badge bg-light-secondary">OfinIT only</span></h5></div>
                    <div class="card-body">
                        <p class="small mb-1">Billing emails go to: {!! $seemaEmails ? e(implode(', ', $seemaEmails)) : '<span class="text-danger">none — enter Seema\'s billing email in the settings</span>' !!}</p>
                        @if ($drafts->isNotEmpty())
                            <p class="small text-danger">Draft invoices waiting to be issued: {{ $drafts->count() }} — usually because a business profile is incomplete.</p>
                        @endif
                        <form method="POST" action="{{ route('admin.platformBilling.run') }}" class="d-flex gap-2 mb-3">
                            @csrf
                            <input class="form-control form-control-sm" type="month" name="month" value="{{ now('Asia/Kolkata')->subMonthNoOverflow()->format('Y-m') }}" style="max-width:170px">
                            <button class="btn btn-sm btn-secondary">Issue invoices &amp; payment links now</button>
                        </form>
                        <p class="small mb-0">Service: <strong class="{{ $suspended ? 'text-danger' : 'text-success' }}">{{ $suspended ? 'suspended' : 'active' }}</strong>
                            @if ($suspendedReason) <span class="text-muted">· last change: {{ $suspendedReason }}</span>@endif</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
