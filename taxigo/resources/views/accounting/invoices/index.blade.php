@extends('layouts.main')
@section('title', 'Invoices')
@php use App\Models\Invoice; @endphp
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Accounting</li>
                        <li class="breadcrumb-item" aria-current="page">Invoices</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title"><h2 class="mb-0">Invoices</h2></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>

        @foreach ($profiles as $profile)
            @if ($profile && !$profile->isReadyForInvoicing())
                <div class="col-12">
                    <div class="alert alert-warning">
                        <strong>{{ $profile->legal_name }}</strong> profile is incomplete ({{ implode(', ', $profile->missingForInvoicing()) }}), so its documents are saved as drafts without numbers.
                        <a href="{{ route('admin.setting.businessProfiles') }}">Complete it</a>, then issue the drafts.
                    </div>
                </div>
            @endif
        @endforeach

        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.invoices.index') }}" class="row g-2 align-items-end">
                        <div class="col-auto">
                            <label class="form-label small" for="month">Month</label>
                            <input type="month" class="form-control" id="month" name="month" value="{{ $month->format('Y-m') }}">
                        </div>
                        <div class="col-auto">
                            <label class="form-label small" for="type">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All documents</option>
                                @foreach (Invoice::TYPE_LABELS as $key => $label)
                                    <option value="{{ $key }}" {{ $type === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-light-primary" type="submit">Show</button>
                            <a class="btn btn-light-secondary" href="{{ route('admin.invoices.index', ['scope' => 'all']) }}">All time</a>
                        </div>
                        <div class="col-auto ms-auto">
                            <a class="btn btn-outline-secondary" href="{{ route('admin.invoices.gstr1', ['month' => $month->format('Y-m'), 'supplier' => 'supplier']) }}">GSTR-1 CSV — Seema Holidays</a>
                            <a class="btn btn-outline-secondary" href="{{ route('admin.invoices.gstr1', ['month' => $month->format('Y-m'), 'supplier' => 'platform']) }}">GSTR-1 CSV — OfinIT</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-1">OfinIT platform fee — {{ $month->format('F Y') }}</h6>
                    @if ($platformInvoice)
                        <p class="small mb-2">{{ $platformInvoice->isIssued() ? 'Issued ' . $platformInvoice->number : 'Draft ready for review' }} · ₹{{ number_format((float) $platformInvoice->total, 2) }}
                            @if ($platformInvoice->paid_at) · <span class="text-success">paid</span>@endif
                        </p>
                    @else
                        <p class="small text-muted mb-2">Bills completed and no-show bookings picked up this month.</p>
                    @endif
                    @if (!$platformInvoice || !$platformInvoice->isIssued())
                        <form method="POST" action="{{ route('admin.invoices.platformFee') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
                            <button class="btn btn-sm btn-primary" type="submit">{{ $platformInvoice ? 'Rebuild draft' : 'Prepare draft' }}</button>
                        </form>
                    @endif
                    @if ($drafts > 0)
                        <form method="POST" action="{{ route('admin.invoices.issueDrafts') }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-light-warning" type="submit">Issue {{ $drafts }} draft(s)</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Number</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Booking / period</th>
                                    <th>Recipient</th>
                                    <th class="text-end">Taxable</th>
                                    <th class="text-end">GST</th>
                                    <th class="text-end">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($list as $invoice)
                                    <tr>
                                        <td>{{ $invoice->number ?? '—' }} @unless($invoice->isIssued())<span class="badge bg-light-warning">Draft</span>@endunless</td>
                                        <td>{{ $invoice->label() }}</td>
                                        <td>{{ optional($invoice->issue_date)->format('d-m-Y') ?? '—' }}</td>
                                        <td>
                                            @if ($invoice->booking) {{ $invoice->booking->booking_id }}
                                            @elseif ($invoice->period_start) {{ $invoice->period_start->format('M Y') }} @endif
                                        </td>
                                        <td>{{ $invoice->recipient['name'] ?? '' }}@if(!empty($invoice->recipient['gstin']))<br><small class="text-muted">{{ $invoice->recipient['gstin'] }}</small>@endif</td>
                                        <td class="text-end">₹{{ number_format((float) $invoice->taxable_value, 2) }}</td>
                                        <td class="text-end">₹{{ number_format($invoice->taxTotal(), 2) }}</td>
                                        <td class="text-end">₹{{ number_format((float) $invoice->total, 2) }}</td>
                                        <td class="text-nowrap text-end">
                                            <a class="btn btn-sm btn-light-info" href="{{ route('admin.invoices.show', $invoice) }}" target="_blank" rel="noopener"><i class="ti ti-eye"></i></a>
                                            @unless ($invoice->isIssued())
                                                <form method="POST" action="{{ route('admin.invoices.issue', $invoice) }}" class="d-inline" onsubmit="return confirm('Issue this document? It will get a number and can no longer be changed.');">
                                                    @csrf
                                                    <button class="btn btn-sm btn-light-success" type="submit">Issue</button>
                                                </form>
                                            @endunless
                                            @if ($invoice->type === Invoice::PLATFORM_FEE && $invoice->isIssued())
                                                <form method="POST" action="{{ route('admin.invoices.markPaid', $invoice) }}" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm {{ $invoice->paid_at ? 'btn-light-success' : 'btn-light-secondary' }}" type="submit">{{ $invoice->paid_at ? 'Paid' : 'Mark paid' }}</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="text-center text-muted p-4">No documents for this selection.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($list->hasPages())
                    <div class="card-footer">{{ $list->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
