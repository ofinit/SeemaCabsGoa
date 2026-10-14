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
    .stack { display:grid; gap:8px; margin-top:12px; }
    .steps { display:flex; gap:6px; margin-top:12px; }
    .step { flex:1; height:6px; border-radius:6px; background:var(--grey-soft); }
    .step.on { background:var(--gold); }
    .step.done { background:var(--green); }
    .step.off { background:var(--red); }
    .drv { display:flex; align-items:center; gap:12px; padding:12px; border:1px solid var(--line); border-radius:14px; margin-top:8px; }
    .drv input { width:20px; height:20px; accent-color:#1c1c1e; }
    .drv.match { border-color:var(--gold); background:var(--gold-soft); }
    .tag { font-size:11px; font-weight:800; color:#8a6400; }
</style>
@endpush

@section('body')
@php
    $phone = preg_replace('/\D/', '', (string) $b['customer_phone']);
    $wa = strlen($phone) === 10 ? '91' . $phone : $phone;
    $driverPhone = preg_replace('/\D/', '', (string) $b['driver_phone']);
    $driverWa = strlen($driverPhone) === 10 ? '91' . $driverPhone : $driverPhone;
    $open = in_array($b['status'], ['confirmed', 'assigned'], true);
    $step = ['confirmed' => 1, 'assigned' => 2, 'completed' => 3][$b['status']] ?? 0;
@endphp
<div class="bar">
    <a class="btn-icon" href="{{ route('fleet.index') }}" aria-label="Back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
    </a>
    <h1>{{ $b['reference'] }}</h1>
    <button class="btn-icon" type="button" onclick="fleetRefresh(this)" aria-label="Refresh">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 11a8 8 0 1 0-2.3 5.7"/><path d="M20 4v7h-7"/></svg>
    </button>
</div>
<div class="wrap">
    @if (session('success'))<div class="flash ok">{{ session('success') }}</div>@endif
    @if (session('error'))<div class="flash err">{{ session('error') }}</div>@endif
    @if ($errors->any())<div class="flash err">{{ $errors->first() }}</div>@endif
    @if ($suspended)
        <div class="banner">Service is suspended for an unpaid OfinIT invoice.</div>
    @endif

    <div class="card sec">
        <div class="row" style="display:flex; justify-content:space-between; gap:8px; align-items:flex-start">
            <div class="k">{{ $b['trip'] }}{{ $b['package'] ? ' · ' . $b['package'] : '' }}</div>
            <span class="pill {{ $b['status'] }}">{{ $b['status_label'] }}</span>
        </div>
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
                <a class="btn btn-dark" href="tel:{{ $phone }}">Call</a>
                <a class="btn btn-green" href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Hi ' . $b['customer'] . ', this is Seema Cabs about your booking ' . $b['reference'] . '.') }}">WhatsApp</a>
            </div>
        @endif
    </div>

    <div class="card sec">
        <div class="k">Cab &amp; driver</div>
        <div class="line"><span class="muted">Cab</span><span style="font-weight:700">{{ $b['cab'] }}{{ $b['cab_number'] ? ' · ' . $b['cab_number'] : '' }}</span></div>
        <div class="line"><span class="muted">Driver</span><span style="font-weight:700">{{ $b['driver'] ?? 'Not assigned yet' }}</span></div>
        <div class="stack">
            @if ($b['driver'])
                @if ($driverPhone)
                    <a class="btn btn-green btn-block" href="https://wa.me/{{ $driverWa }}?text={{ rawurlencode($driverMessage) }}">Send trip to driver (WhatsApp)</a>
                    <a class="btn btn-line btn-block" href="tel:{{ $driverPhone }}">Call driver</a>
                @endif
                <button class="btn btn-line btn-block" type="button" onclick="copyTrip(this)">Copy trip details</button>
            @endif
            @if ($open)
                <button class="btn btn-gold btn-block" type="button" onclick="openSheet('driver-sheet')">{{ $b['driver'] ? 'Change driver' : 'Assign driver' }}</button>
            @endif
        </div>
    </div>

    <div class="card sec">
        <div class="k">Fare</div>
        <div class="line"><span class="muted">Total</span><span style="font-weight:800">₹{{ number_format($b['total'], 2) }}</span></div>
        <div class="line"><span class="muted">Paid online</span><span>₹{{ number_format($b['advance'], 2) }}</span></div>
        <div class="line"><span class="muted">Driver collects</span><span style="font-weight:800">₹{{ number_format($b['balance'], 2) }}</span></div>
    </div>

    @if ($open || $canUndoNoShow || $canRefund)
        <div class="card sec">
            <div class="k">Manage trip</div>
            <div class="stack">
                @if ($open)
                    <form method="POST" action="{{ route('fleet.action', $b['id']) }}" onsubmit="confirmSubmit(this, 'Mark trip completed?', 'The customer gets the final invoice. This can\'t be undone here.', 'Completed'); return false;">
                        @csrf <input type="hidden" name="action" value="complete">
                        <button class="btn btn-dark btn-block" @disabled(!$b['driver'])>Mark trip completed{{ $b['driver'] ? '' : ' (assign a driver first)' }}</button>
                    </form>
                    @if ($canNoShow)
                        <button class="btn btn-line btn-block" type="button" onclick="openSheet('noshow-sheet')">Customer didn't show up</button>
                    @endif
                    <button class="btn btn-red btn-block" type="button" onclick="openSheet('cancel-sheet')">Cancel booking</button>
                @endif
                @if ($canRefund)
                    <form method="POST" action="{{ route('fleet.action', $b['id']) }}" onsubmit="confirmSubmit(this, 'Refund the advance?', '₹{{ number_format($b['advance'], 2) }} goes back to the customer, if the booking is within the refund window.', 'Refund'); return false;">
                        @csrf <input type="hidden" name="action" value="refund">
                        <button class="btn btn-line btn-block">Refund advance (₹{{ number_format($b['advance'], 2) }})</button>
                    </form>
                @endif
                @if ($canUndoNoShow)
                    <form method="POST" action="{{ route('fleet.action', $b['id']) }}" onsubmit="confirmSubmit(this, 'Undo no-show?', 'The booking goes back to confirmed.', 'Undo'); return false;">
                        @csrf <input type="hidden" name="action" value="undo_no_show">
                        <button class="btn btn-line btn-block">Undo no-show</button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <p class="muted small" style="text-align:center; margin-top:14px">Booked {{ $b['booked_at'] }}</p>
</div>

@if ($open)
    {{-- Assign / change driver --}}
    <div class="sheet" id="driver-sheet" hidden>
        <form class="sheet-panel" method="POST" action="{{ route('fleet.action', $b['id']) }}">
            @csrf <input type="hidden" name="action" value="assign">
            <h3>{{ $b['driver'] ? 'Change driver' : 'Assign driver' }}</h3>
            <p class="muted small" style="margin:0">Booking needs a {{ $b['cab'] }}. The customer is told the driver's name and cab.</p>
            @forelse ($drivers as $d)
                <label class="drv {{ $d['matches'] ? 'match' : '' }}">
                    <input type="radio" name="driver_id" value="{{ $d['id'] }}" required {{ $b['driver'] === $d['name'] ? 'checked' : '' }}>
                    <span style="flex:1">
                        <b>{{ $d['name'] }}</b><br>
                        <span class="muted small">{{ $d['cab'] }}{{ $d['cab_number'] ? ' · ' . $d['cab_number'] : '' }}{{ $d['phone'] ? ' · ' . $d['phone'] : '' }}</span>
                    </span>
                    @if ($d['matches'])<span class="tag">{{ strtoupper($b['cab']) }}</span>@endif
                </label>
            @empty
                <p class="muted">No drivers on your fleet yet. Add them in the admin panel (Cab Management).</p>
            @endforelse
            <div class="sheet-actions">
                <button class="btn btn-line" type="button" onclick="closeSheet('driver-sheet')">Back</button>
                <button class="btn btn-gold" {{ $drivers->isEmpty() ? 'disabled' : '' }}>Assign</button>
            </div>
        </form>
    </div>

    {{-- Cancel (+ refund) --}}
    <div class="sheet" id="cancel-sheet" hidden>
        <form class="sheet-panel" method="POST" action="{{ route('fleet.action', $b['id']) }}" onsubmit="this.querySelectorAll('button').forEach(b => b.disabled = true)">
            @csrf <input type="hidden" name="action" value="cancel">
            <h3>Cancel booking {{ $b['reference'] }}?</h3>
            <p class="muted small" style="margin:0 0 12px">The customer is notified that Seema Cabs cancelled the ride.</p>
            <label class="drv"><input type="checkbox" name="refund" value="1" checked>
                <span>Refund the advance of <b>₹{{ number_format($b['advance'], 2) }}</b><br><span class="muted small">Follows the refund window in Settings → Booking &amp; Cancellation.</span></span></label>
            <div class="sheet-actions">
                <button class="btn btn-line" type="button" onclick="closeSheet('cancel-sheet')">Keep booking</button>
                <button class="btn btn-red">Cancel booking</button>
            </div>
        </form>
    </div>

    @if ($canNoShow)
        <div class="sheet" id="noshow-sheet" hidden>
            <form class="sheet-panel" method="POST" action="{{ route('fleet.action', $b['id']) }}" onsubmit="this.querySelectorAll('button').forEach(b => b.disabled = true)">
                @csrf <input type="hidden" name="action" value="no_show">
                <h3>Customer didn't show up?</h3>
                <p class="muted small" style="margin:0 0 12px">The advance is kept (non-refundable) and invoiced. You can undo this today.</p>
                <input type="text" name="reason" maxlength="255" required placeholder="What happened? e.g. waited 30 min, phone off">
                <div class="sheet-actions">
                    <button class="btn btn-line" type="button" onclick="closeSheet('noshow-sheet')">Back</button>
                    <button class="btn btn-red">Mark no-show</button>
                </div>
            </form>
        </div>
    @endif
@endif
@endsection

@push('scripts')
<script>
    const tripText = @json($driverMessage);
    window.copyTrip = async (btn) => {
        try { await navigator.clipboard.writeText(tripText); }
        catch (e) {
            const t = document.createElement('textarea'); t.value = tripText; document.body.appendChild(t); t.select();
            document.execCommand('copy'); t.remove();
        }
        const label = btn.textContent; btn.textContent = 'Copied ✓'; setTimeout(() => btn.textContent = label, 2000);
    };
</script>
@endpush
