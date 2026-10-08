@extends('layouts.main')

@section('title', 'Payment Gateway Settings')

@section('css')
    <style>
        .gateway-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .gateway-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .form-switch .form-check-input {
            width: 2.8em;
            height: 1.4em;
            cursor: pointer;
        }
        .badge-mode {
            font-size: 11px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">Payment Gateways</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h2 class="mb-0">Payment Gateway &amp; Split Settlement Settings</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @include('layouts.message')
        </div>

        <div class="col-12">
            <form method="post" id="paymentForm" action="{{ route('admin.setting.paymentGateway.store') }}">
                @csrf

                @php
                    $razorpayEnabled = ($envoirements['razorpay_enabled'] ?? '1') === '1';
                    $cashfreeEnabled = ($envoirements['cashfree_enabled'] ?? '0') === '1';
                    $primaryGateway = $envoirements['primary_payment_gateway'] ?? 'both';
                    $cashfreeMode = $envoirements['cashfree_mode'] ?? 'sandbox';
                @endphp

                {{-- Primary Gateway Routing Strategy --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Gateway Routing &amp; Checkout Strategy</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Choose which gateway is offered to passengers at booking checkout.</p>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="primary_payment_gateway" id="pgBoth" value="both" {{ $primaryGateway === 'both' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="pgBoth">
                                            Both Gateways (Customer Choice)
                                        </label>
                                    </div>
                                    <div class="text-muted small mt-2 ps-4">
                                        Passenger can select between Razorpay and Cashfree on the checkout screen.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="primary_payment_gateway" id="pgRazorpay" value="razorpay" {{ $primaryGateway === 'razorpay' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="pgRazorpay">
                                            Razorpay Only
                                        </label>
                                    </div>
                                    <div class="text-muted small mt-2 ps-4">
                                        Directly launch Razorpay checkout for all bookings.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded-3 h-100">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="primary_payment_gateway" id="pgCashfree" value="cashfree" {{ $primaryGateway === 'cashfree' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="pgCashfree">
                                            Cashfree Only
                                        </label>
                                    </div>
                                    <div class="text-muted small mt-2 ps-4">
                                        Directly launch Cashfree v3 modal checkout for all bookings.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Razorpay Settings Card --}}
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 gateway-card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="mb-0">Razorpay</h5>
                                    <span class="badge bg-primary">Standard + Route</span>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="razorpay_enabled" id="razorpay_enabled" value="1" {{ $razorpayEnabled ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="razorpay_enabled">Enable</label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label" for="paymentkey">Key ID</label>
                                    <input type="text" name="paymentkey" value="{{ $envoirements['paymentkey'] ?? '' }}"
                                        class="form-control" id="paymentkey" placeholder="rzp_live_... / rzp_test_...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="paymentsecrete">Key Secret</label>
                                    <input type="password" name="paymentsecrete" value="{{ $envoirements['paymentsecrete'] ?? '' }}"
                                        class="form-control" id="paymentsecrete" placeholder="Enter Razorpay Secret">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="razorpaywebhooksecret">Webhook Secret</label>
                                    <input type="text" name="razorpaywebhooksecret" value="{{ $envoirements['razorpaywebhooksecret'] ?? '' }}"
                                        class="form-control" id="razorpaywebhooksecret" placeholder="Enter Webhook Secret">
                                </div>
                                <div class="p-2 bg-light rounded text-muted small">
                                    <strong>Webhook URL:</strong> <code>{{ url('/api/razorpay/webhook') }}</code>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Cashfree Settings Card --}}
                    <div class="col-lg-6 mb-4">
                        <div class="card h-100 gateway-card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="mb-0">Cashfree Payments</h5>
                                    <span class="badge bg-success">PG + Easy Split</span>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="cashfree_enabled" id="cashfree_enabled" value="1" {{ $cashfreeEnabled ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="cashfree_enabled">Enable</label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label" for="cashfree_mode">Environment Mode</label>
                                    <select class="form-select" name="cashfree_mode" id="cashfree_mode">
                                        <option value="sandbox" {{ $cashfreeMode === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing / Test Cards)</option>
                                        <option value="production" {{ $cashfreeMode === 'production' ? 'selected' : '' }}>Production (Live Payments)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="cashfree_app_id">App ID / Client ID</label>
                                    <input type="text" name="cashfree_app_id" value="{{ $envoirements['cashfree_app_id'] ?? '' }}"
                                        class="form-control" id="cashfree_app_id" placeholder="CF App ID (e.g. TEST101...)">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="cashfree_secret_key">Secret Key</label>
                                    <input type="password" name="cashfree_secret_key" value="{{ $envoirements['cashfree_secret_key'] ?? '' }}"
                                        class="form-control" id="cashfree_secret_key" placeholder="Enter Cashfree Secret Key">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="cashfree_webhook_secret">Webhook Secret (Optional if same as Secret Key)</label>
                                    <input type="text" name="cashfree_webhook_secret" value="{{ $envoirements['cashfree_webhook_secret'] ?? '' }}"
                                        class="form-control" id="cashfree_webhook_secret" placeholder="Cashfree Webhook Signature Secret">
                                </div>
                                <div class="p-2 bg-light rounded text-muted small">
                                    <strong>Webhook URL:</strong> <code>{{ url('/api/cashfree/webhook') }}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-end mb-4">
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="ti ti-device-floppy me-1"></i> Save Payment Gateway Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
