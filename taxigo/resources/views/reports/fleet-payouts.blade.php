@extends('layouts.main')
@section('title', 'Fleet Payouts')
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item" aria-current="page">Fleet Payouts</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title"><h2 class="mb-0">Fleet Payouts</h2></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="mb-2">The fleet operator's share of each online advance is paid out automatically after payment:
                        <strong>Razorpay</strong> by a Route transfer, <strong>Cashfree</strong> by an Easy Split attached to the order.
                        OfinIT's fee (+ GST) and any ride GST stay in the merchant account.</p>
                    <p class="small mb-0">
                        Paying to: <strong>{{ $operator->company_name ?? $operator->name ?? 'Fleet operator #' . ($operator->id ?? '—') }}</strong>
                        · Razorpay account: {!! $operator && $operator->razorpay_account ? '<code>' . e($operator->razorpay_account) . '</code>' : '<span class="text-danger">not set</span>' !!}
                        · Cashfree vendor: {!! $operator && $operator->cashfree_vendor_id ? '<code>' . e($operator->cashfree_vendor_id) . '</code>' : '<span class="text-danger">not set</span>' !!}
                        · <a href="{{ route('admin.setting.fleetOperators.index') }}">Edit fleet operator</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex flex-wrap gap-2 align-items-center mb-3">
            <a class="btn btn-sm {{ $filter === 'failed' ? 'btn-danger' : 'btn-light-danger' }}" href="{{ route('admin.report.fleetPayouts', ['filter' => 'failed']) }}">Not paid out ({{ $counts['failed'] }})</a>
            <a class="btn btn-sm {{ $filter === 'paid' ? 'btn-success' : 'btn-light-success' }}" href="{{ route('admin.report.fleetPayouts', ['filter' => 'paid']) }}">Paid out ({{ $counts['paid'] }})</a>
            <a class="btn btn-sm {{ $filter === 'all' ? 'btn-secondary' : 'btn-light-secondary' }}" href="{{ route('admin.report.fleetPayouts', ['filter' => 'all']) }}">All</a>
            @if ($counts['failed'] > 0)
                <form method="POST" action="{{ route('admin.report.fleetPayouts.retryFailed') }}" class="ms-auto"
                      onsubmit="return confirm('Retry payouts for unpaid bookings from the last 60 days? Payouts already made at the gateway are detected and not repeated.');">
                    @csrf
                    <button class="btn btn-sm btn-primary" type="submit">Retry all (last 60 days)</button>
                </form>
            @endif
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Booking</th>
                                    <th>Paid on</th>
                                    <th>Gateway</th>
                                    <th class="text-end">Advance</th>
                                    <th class="text-end">Operator share</th>
                                    <th>Payout</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    @php
                                        $settlement = json_decode((string) $payment->amount_settlement, true) ?: [];
                                        $share = (float) str_replace(',', '', (string) ($settlement['fleet_operator_total_payment'] ?? 0));
                                    @endphp
                                    <tr>
                                        <td>{{ optional($payment->bookingDetails)->booking_id ?? '—' }}</td>
                                        <td>{{ optional($payment->created_at)->timezone('Asia/Kolkata')->format('d M Y') }}</td>
                                        <td>{{ ucfirst($payment->payment_gateway ?? 'razorpay') }}</td>
                                        <td class="text-end">₹{{ number_format((float) $payment->amount, 2) }}</td>
                                        <td class="text-end">₹{{ number_format($payment->transfer_amount ?? $share, 2) }}</td>
                                        <td>
                                            @if ($payment->transfer_reference)
                                                <span class="badge bg-light-success">Paid</span>
                                                <small class="text-muted d-block">{{ $payment->transfer_reference }}</small>
                                            @else
                                                <span class="badge bg-light-danger">Not paid</span>
                                                @if ($payment->transfer_error)<small class="text-danger d-block">{{ $payment->transfer_error }}</small>@endif
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @unless ($payment->transfer_reference)
                                                <form method="POST" action="{{ route('admin.report.fleetPayouts.retry', $payment) }}" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-light-primary" type="submit">Retry</button>
                                                </form>
                                            @endunless
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted p-4">Nothing here.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($payments->hasPages())
                    <div class="card-footer">{{ $payments->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
