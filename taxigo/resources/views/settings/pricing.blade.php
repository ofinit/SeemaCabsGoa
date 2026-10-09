@extends('layouts.main')
@section('title', 'Pricing')
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">Pricing</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title"><h2 class="mb-0">Pricing</h2></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-1">Markup, platform fee &amp; advance</h5>
                    <p class="text-muted mb-0 small">
                        The markup is <strong>internal</strong>: it is built into the fare customers see and is never shown as a separate line.
                        Changes apply to new quotes and bookings immediately; existing bookings keep the prices they were booked at.
                    </p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.setting.savePricing') }}">
                        @csrf
                        @foreach ($fields as $key => $field)
                            <div class="mb-3">
                                <label class="form-label" for="{{ $key }}">{{ $field['label'] }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="{{ $field['min'] }}" max="{{ $field['max'] }}"
                                        name="{{ $key }}" id="{{ $key }}" class="form-control @error($key) is-invalid @enderror"
                                        value="{{ old($key, $settings[$key] ?? '') }}" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                @error($key)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        @endforeach
                        <p class="text-muted small">A negative markup is a discount. Fleet-operator commission is set under <a href="{{ route('admin.setting.plateFormFee') }}">Transaction Fees</a>.</p>
                        <button type="submit" class="btn btn-primary">Save pricing</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Example: ride with ₹1,000 base fare</h5></div>
                <div class="card-body">
                    <table class="table table-sm mb-2">
                        <thead><tr><th></th><th class="text-end">Now</th><th class="text-end">If GST on</th></tr></thead>
                        <tbody>
                            @foreach (['fare_before_markup' => 'Fare before markup', 'markup' => 'Markup (internal)', 'fare' => 'Fare shown to customer', 'gst' => 'GST', 'total' => 'Customer pays', 'advance' => 'Advance online', 'balance' => 'Balance to driver', 'platform_fee' => 'OfinIT platform fee'] as $k => $label)
                                <tr class="{{ $k === 'total' ? 'fw-bold' : '' }}">
                                    <td>{{ $label }}</td>
                                    <td class="text-end">₹{{ number_format($example['now'][$k], 2) }}</td>
                                    <td class="text-end">₹{{ number_format($example['with_gst'][$k], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="text-muted small mb-0">To keep customer totals unchanged when GST starts, set the markup to
                        (1 + current markup %) ÷ (1 + GST %) − 1, e.g. 20% with 5% GST → <strong>14.29%</strong>.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Recent changes</h5></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tbody>
                            @forelse ($history as $change)
                                <tr>
                                    <td class="small">{{ optional($change->created_at)->timezone('Asia/Kolkata')->format('d M Y H:i') }}<br><span class="text-muted">{{ $change->user_name }}</span></td>
                                    <td class="small">{{ $fields[$change->key]['label'] ?? $change->key }}<br>{{ $change->old_value ?? '—' }} → <strong>{{ $change->new_value }}</strong></td>
                                </tr>
                            @empty
                                <tr><td class="text-muted small p-3">No changes yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
