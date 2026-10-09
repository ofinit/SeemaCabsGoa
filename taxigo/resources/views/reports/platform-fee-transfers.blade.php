@extends('layouts.main')
@section('title', 'OfinIT Fee Transfers')
@php use App\Services\Payments\PlatformFeeTransferService; @endphp
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item" aria-current="page">OfinIT Fee Transfers</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title"><h2 class="mb-0">OfinIT Fee Transfers</h2></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="mb-2">Customers pay into the fleet operator's own payment gateway account. After each payment,
                        <strong>OfinIT's platform fee + GST</strong> is split out to OfinIT automatically:
                        <strong>Razorpay</strong> by a Route transfer, <strong>Cashfree</strong> by an Easy Split. The operator keeps the rest.
                        When a booking is refunded, OfinIT's share is reversed with it.</p>
                    <p class="small mb-0">
                        Operator: <strong>{{ $operator->company_name ?? 'Fleet operator #' . ($operator->id ?? '—') }}</strong>
                        · OfinIT Razorpay linked account: {!! $operator && $operator->razorpay_account ? '<code>' . e($operator->razorpay_account) . '</code>' : '<span class="text-danger">not set</span>' !!}
                        · OfinIT Cashfree vendor: {!! $operator && $operator->cashfree_vendor_id ? '<code>' . e($operator->cashfree_vendor_id) . '</code>' : '<span class="text-danger">not set</span>' !!}
                        · <a href="{{ route('admin.setting.fleetOperators.index') }}">Edit</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex flex-wrap gap-2 align-items-center mb-3">
            <a class="btn btn-sm {{ $filter === 'failed' ? 'btn-danger' : 'btn-light-danger' }}" href="{{ route('admin.report.feeTransfers', ['filter' => 'failed']) }}">Not transferred ({{ $counts['failed'] }})</a>
            <a class="btn btn-sm {{ $filter === 'paid' ? 'btn-success' : 'btn-light-success' }}" href="{{ route('admin.report.feeTransfers', ['filter' => 'paid']) }}">Transferred ({{ $counts['paid'] }})</a>
            <a class="btn btn-sm {{ $filter === 'all' ? 'btn-secondary' : 'btn-light-secondary' }}" href="{{ route('admin.report.feeTransfers', ['filter' => 'all']) }}">All</a>
            @if ($counts['failed'] > 0)
                <form method="POST" action="{{ route('admin.report.feeTransfers.retryFailed') }}" class="ms-auto"
                      onsubmit="return confirm('Retry OfinIT fee transfers for bookings from the last 60 days? Transfers that already exist at the gateway are detected and not repeated.');">
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
                                    <th class="text-end">Paid online</th>
                                    <th class="text-end">OfinIT fee + GST</th>
                                    <th>Transfer</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr>
                                        <td>{{ optional($payment->bookingDetails)->booking_id ?? '—' }}</td>
                                        <td>{{ optional($payment->created_at)->timezone('Asia/Kolkata')->format('d M Y') }}</td>
                                        <td>{{ ucfirst($payment->payment_gateway ?? 'razorpay') }}</td>
                                        <td class="text-end">₹{{ number_format((float) $payment->amount, 2) }}</td>
                                        <td class="text-end">₹{{ number_format((float) ($payment->transfer_amount ?? PlatformFeeTransferService::amountFor($payment)), 2) }}</td>
                                        <td>
                                            @if ($payment->transfer_reference)
                                                <span class="badge bg-light-success">Transferred</span>
                                                <small class="text-muted d-block">{{ $payment->transfer_reference }}</small>
                                            @else
                                                <span class="badge bg-light-danger">Not transferred</span>
                                                @if ($payment->transfer_error)<small class="text-danger d-block">{{ $payment->transfer_error }}</small>@endif
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @unless ($payment->transfer_reference)
                                                <form method="POST" action="{{ route('admin.report.feeTransfers.retry', $payment) }}" class="d-inline">
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
