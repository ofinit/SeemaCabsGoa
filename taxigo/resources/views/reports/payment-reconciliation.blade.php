@extends('layouts.main')

@section('title', 'Reconcile Cancelled Payments')
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item" aria-current="page">Reconcile Cancelled Payments</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Reconcile Cancelled Payments</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p class="text-muted">
        Bookings that were paid, then cancelled, with no refund recorded in our own database — cross-checked
        directly against Razorpay's payment records to see whether the money was actually kept or was refunded
        outside this system. Note: only the "Charged via Razorpay" amount (the online deposit) can ever show as
        refunded here — the rest of the Total Trip Fare is cash paid directly to the driver and never touches
        Razorpay, so it isn't reflected in this comparison.
    </p>

    @if($configError)
        <div class="alert alert-danger">{{ $configError }}</div>
    @else
        @if(!empty($monthsScanned) || !empty($monthsFailed))
            <div class="alert alert-info">
                <strong>Settlement recon scan</strong> (fallback for payments older than Razorpay's 180-day direct-lookup window):
                scanned {{ count($monthsScanned) }} month(s){{ !empty($monthsScanned) ? ' (' . implode(', ', $monthsScanned) . ')' : '' }}.
                @if(!empty($monthsFailed))
                    <br><span class="text-danger">{{ count($monthsFailed) }} month(s) failed to scan: {{ implode('; ', $monthsFailed) }} — rows relying only on those months may be incomplete.</span>
                @endif
            </div>
        @endif

        @if(empty($results))
            <div class="alert alert-success">No paid-and-cancelled bookings with an unrecorded refund found.</div>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Pickup Date</th>
                                    <th>Total Trip Fare</th>
                                    <th>Charged via Razorpay</th>
                                    <th>Refunded via Razorpay</th>
                                    <th>Source</th>
                                    <th>Verdict</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $r)
                                    <tr>
                                        <td>{{ $r['booking_id'] }}</td>
                                        <td>{{ $r['pickup_date'] }}</td>
                                        <td>₹{{ number_format($r['our_amount'], 2) }}</td>
                                        <td>{{ $r['charged_amount'] !== null ? '₹' . number_format($r['charged_amount'], 2) : '—' }}</td>
                                        <td>{{ $r['amount_refunded'] !== null ? '₹' . number_format($r['amount_refunded'], 2) : '—' }}</td>
                                        <td>{{ $r['source'] ?? '—' }}</td>
                                        <td>
                                            @if(str_contains($r['verdict'], 'Not refunded'))
                                                <span class="badge bg-success">{{ $r['verdict'] }}</span>
                                            @elseif(str_contains($r['verdict'], 'refunded via Razorpay'))
                                                <span class="badge bg-danger">{{ $r['verdict'] }}</span>
                                            @else
                                                <span class="badge bg-secondary" title="{{ $r['error'] }}">{{ $r['verdict'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endsection
