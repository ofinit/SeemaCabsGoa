@extends('fleet.layout')
@section('title', $b['reference'] . ' — Seema Cabs Fleet')

@push('head')
<style>
    .sec { margin-top:12px; }
    .k { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--muted); margin-bottom:4px; }
    .big { font-size:22px; font-weight:800; }
    .stop { display:flex; gap:12px; }
    .stop + .stop { margin-top:12px; }
    .dot { width:12px; height:12px; border-radius:50%; margin-top:5px; flex:0 0 12px; }
    .line { display:flex; justify-content:space-between; gap:12px; padding:6px 0; }
    .line + .line { border-top:1px solid var(--line); }
    .actions { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:12px; }
    .steps { display:flex; gap:6px; margin-top:12px; }
    .step { flex:1; height:6px; border-radius:6px; background:var(--grey-soft); }
    .step.on { background:var(--gold); }
    .step.done { background:var(--green); }
    .step.off { background:var(--red); }
</style>
@endpush

@section('body')
@php
    $phone = preg_replace('/\D/', '', (string) $b['customer_phone']);
    $wa = strlen($phone) === 10 ? '91' . $phone : $phone;
    $step = ['confirmed' => 1, 'assigned' => 2, 'completed' => 3][$b['status']] ?? 0;
@endphp
<div class="bar">
    <a class="btn-icon" href="{{ url()->previous() !== url()->current() && str_contains(url()->previous(), '/fleet') ? url()->previous() : route('fleet.index') }}" aria-label="Back">←</a>
    <h1>{{ $b['reference'] }}</h1>
    <span class="pill {{ $b['status'] }}" style="background:#fff">{{ $b['status_label'] }}</span>
</div>
<div class="wrap">
    @if ($suspended)
        <div class="banner">Service is suspended for an unpaid OfinIT invoice.</div>
    @endif

    <div class="card sec">
        <div class="k">{{ $b['trip'] }}{{ $b['package'] ? ' · ' . $b['package'] : '' }}</div>
        <div class="big">{{ $b['pickup_time'] }} · {{ $b['pickup_day'] }}, {{ $b['pickup_date'] }}</div>
        <div class="steps" aria-label="Trip status">
            @if (in_array($b['status'], ['cancelled', 'no_show'], true))
                <div class="step off"></div>
            @else
                <div class="step {{ $step >= 1 ? ($step > 1 ? 'done' : 'on') : '' }}"></div>
                <div class="step {{ $step >= 2 ? ($step > 2 ? 'done' : 'on') : '' }}"></div>
                <div class="step {{ $step >= 3 ? 'done' : '' }}"></div>
            @endif
        </div>
        <div class="muted small" style="margin-top:6px">
            @if (in_array($b['status'], ['cancelled', 'no_show'], true))
                {{ $b['status_label'] }}{{ $b['refund'] > 0 ? ' · ₹' . number_format($b['refund'], 2) . ' refunded' : '' }}
            @else
                Confirmed → Driver assigned → Completed
            @endif
        </div>
    </div>

    <div class="card sec">
        <div class="stop"><span class="dot" style="background:var(--green)"></span><div>
            <div class="k">Pick-up</div><div style="font-weight:700">{{ $b['from'] }}</div>
            @if ($b['pickup_address'] && $b['pickup_address'] !== $b['from'])<div class="muted small">{{ $b['pickup_address'] }}</div>@endif
        </div></div>
        <div class="stop"><span class="dot" style="background:var(--gold)"></span><div>
            <div class="k">Drop-off</div><div style="font-weight:700">{{ $b['to'] }}</div>
            @if ($b['drop_address'] && $b['drop_address'] !== $b['to'])<div class="muted small">{{ $b['drop_address'] }}</div>@endif
        </div></div>
    </div>

    <div class="card sec">
        <div class="k">Customer</div>
        <div style="font-weight:700; font-size:17px">{{ $b['customer'] }}</div>
        @if ($phone)
            <div class="muted">{{ $b['customer_phone'] }}</div>
            <div class="actions">
                <a class="btn btn-dark" href="tel:{{ $phone }}">📞 Call</a>
                <a class="btn btn-green" href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Hi ' . $b['customer'] . ', this is Seema Cabs about your booking ' . $b['reference'] . '.') }}">WhatsApp</a>
            </div>
        @endif
    </div>

    <div class="card sec">
        <div class="k">Cab &amp; driver</div>
        <div class="line"><span class="muted">Cab</span><span style="font-weight:700">{{ $b['cab'] }}{{ $b['cab_number'] ? ' · ' . $b['cab_number'] : '' }}</span></div>
        <div class="line"><span class="muted">Driver</span><span style="font-weight:700">{{ $b['driver'] ?? 'Not assigned yet' }}</span></div>
        @if ($b['driver_phone'])
            <a class="btn btn-line" style="width:100%; margin-top:8px" href="tel:{{ preg_replace('/\D/', '', $b['driver_phone']) }}">📞 Call driver</a>
        @endif
    </div>

    <div class="card sec">
        <div class="k">Fare</div>
        <div class="line"><span class="muted">Total</span><span style="font-weight:800">₹{{ number_format($b['total'], 2) }}</span></div>
        <div class="line"><span class="muted">Paid online</span><span>₹{{ number_format($b['advance'], 2) }}</span></div>
        <div class="line"><span class="muted">Driver collects</span><span style="font-weight:800">₹{{ number_format($b['balance'], 2) }}</span></div>
    </div>

    <p class="muted small" style="text-align:center; margin-top:14px">Booked {{ $b['booked_at'] }}</p>
</div>
@endsection
