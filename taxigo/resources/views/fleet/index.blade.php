@extends('fleet.layout')
@section('title', 'Bookings — Seema Cabs Fleet')

@push('head')
<style>
    .tabs { display:flex; gap:8px; overflow-x:auto; padding:14px 0 4px; scrollbar-width:none; }
    .tabs::-webkit-scrollbar { display:none; }
    .tab { flex:0 0 auto; padding:8px 14px; border-radius:999px; background:#fff; border:1px solid var(--line); font-weight:700; font-size:14px; color:var(--muted); }
    .tab.on { background:var(--ink); border-color:var(--ink); color:#fff; }
    .tab b { font-weight:800; margin-left:4px; }
    .list { display:grid; gap:10px; margin-top:12px; }
    .bk { display:block; }
    .row { display:flex; justify-content:space-between; align-items:flex-start; gap:10px; }
    .when { font-size:17px; font-weight:800; }
    .route { margin:8px 0 6px; font-weight:600; }
    .route .arrow { color:var(--muted); margin:0 4px; }
    .meta { display:flex; flex-wrap:wrap; gap:4px 12px; font-size:13px; color:var(--muted); }
    .money { font-weight:800; white-space:nowrap; }
    .empty { text-align:center; padding:48px 16px; color:var(--muted); }
    .updated { text-align:center; font-size:12px; color:var(--muted); margin-top:14px; }
</style>
@endpush

@section('body')
<div class="bar">
    <h1>Bookings</h1>
    <button class="btn-icon" type="button" onclick="location.reload()" aria-label="Refresh">⟳</button>
    <form method="POST" action="{{ route('fleet.logout') }}" onsubmit="this.token.value = (function(){ try { return localStorage.getItem('fleetToken') || '' } catch(e) { return '' } })(); return confirm('Log out? This phone will stop getting booking alerts.')">
        @csrf
        <input type="hidden" name="token">
        <button class="btn-icon" aria-label="Log out" title="Log out">⏻</button>
    </form>
</div>
<div class="wrap">
    @if ($suspended)
        <div class="banner">Service is suspended for an unpaid OfinIT invoice — new bookings are paused. Pay it in Billing (admin panel).</div>
    @endif

    <nav class="tabs">
        @foreach (\App\Services\Fleet\FleetBookings::TABS as $key => $label)
            <a class="tab {{ $tab === $key ? 'on' : '' }}" href="{{ route('fleet.index', ['tab' => $key]) }}">{{ $label }}@isset($counts[$key])<b>{{ $counts[$key] }}</b>@endisset</a>
        @endforeach
    </nav>

    <div class="list">
        @forelse ($bookings as $b)
            <a class="card bk" href="{{ route('fleet.show', $b['id']) }}">
                <div class="row">
                    <div>
                        <div class="when">{{ $b['pickup_time'] }} <span class="muted small" style="font-weight:600">{{ $b['pickup_day'] }}, {{ $b['pickup_date'] }}</span></div>
                        <div class="muted small">{{ $b['reference'] }} · {{ $b['trip'] }}</div>
                    </div>
                    <span class="pill {{ $b['status'] }}">{{ $b['status_label'] }}</span>
                </div>
                <div class="route">{{ $b['from'] }}<span class="arrow">→</span>{{ $b['to'] }}</div>
                <div class="row">
                    <div class="meta"><span>👤 {{ $b['customer'] }}</span><span>🚕 {{ $b['cab'] }}</span>@if ($b['driver'])<span>🧑‍✈️ {{ $b['driver'] }}</span>@endif</div>
                    <div class="money">₹{{ number_format($b['total']) }}</div>
                </div>
            </a>
        @empty
            <div class="empty">No {{ strtolower(\App\Services\Fleet\FleetBookings::TABS[$tab]) }} bookings.</div>
        @endforelse
    </div>
    <div class="updated">Updated {{ now('Asia/Kolkata')->format('g:i A') }} · refreshes every minute</div>
</div>
@endsection

@push('scripts')
<script>
    document.body.dataset.page = 'list';
    // Keep the list fresh while it's on screen.
    setInterval(() => { if (!document.hidden) location.reload(); }, 60000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden && Date.now() - performance.timeOrigin > 60000) location.reload(); });
</script>
@endpush
