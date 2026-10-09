@extends('layouts.main')
@section('title', 'Ad Dashboard')
@php use App\Models\AdCampaign; @endphp
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Advertisements</li>
                        <li class="breadcrumb-item" aria-current="page">Ad dashboard</li>
                    </ul>
                </div>
                <div class="col-md-12 d-flex align-items-center">
                    <h2 class="mb-0">Ad dashboard — {{ $month->format('F Y') }}</h2>
                    <form class="ms-auto d-flex gap-2" method="GET">
                        <input class="form-control form-control-sm" type="month" name="month" value="{{ $month->format('Y-m') }}">
                        <button class="btn btn-sm btn-secondary">Show</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @php
            $m = $money;
            $seema = (int) ($m->net ?? 0) - (int) ($m->ofinit ?? 0);
            $tiles = [
                ['Ads paid', (int) ($m->ads ?? 0), null],
                ['Collected (incl. GST)', AdCampaign::rupees($m->gross ?? 0), null],
                ['GST', AdCampaign::rupees($m->gst ?? 0), null],
                ['Seema Holidays share', AdCampaign::rupees($seema), 'net of OfinIT share'],
                ['OfinIT share + GST', AdCampaign::rupees($m->ofinit_total ?? 0), 'split at the gateway'],
                ['Refunded', AdCampaign::rupees($m->refunds ?? 0), null],
                ['Offers & coupons given', AdCampaign::rupees($m->promos ?? 0), null],
                ['Live ads now', $live, null],
                ['Views', number_format($views), $clicks . ' taps · ' . ($views ? round($clicks * 100 / $views, 2) . '%' : '—')],
            ];
        @endphp
        @foreach ($tiles as [$label, $value, $hint])
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card"><div class="card-body">
                    <p class="text-muted small mb-1">{{ $label }}</p>
                    <h4 class="mb-0">{{ $value }}</h4>
                    @if ($hint)<p class="small text-muted mb-0">{{ $hint }}</p>@endif
                </div></div>
            </div>
        @endforeach

        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a class="btn btn-sm {{ $late_reviews ? 'btn-danger' : 'btn-light-primary' }}" href="{{ route('admin.advertisements.selfServe.index') }}">In review: {{ $in_review }}{{ $late_reviews ? " ({$late_reviews} over 24 h)" : '' }}</a>
                <a class="btn btn-sm {{ $open_reports ? 'btn-warning' : 'btn-light-primary' }}" href="{{ route('admin.advertisements.selfServe.reports') }}">Open reports: {{ $open_reports }}</a>
                <span class="btn btn-sm {{ $failed_splits ? 'btn-danger' : 'btn-light-secondary' }}">OfinIT splits failed: {{ $failed_splits }}</span>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Fill rate</h5><p class="small text-muted mb-0">Booked placement-days ÷ available (slots × days{{ ' × page groups for P9' }}).</p></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        @foreach ($fill as $row)
                            <tr>
                                <td class="small">{{ $row['code'] }} · {{ $row['name'] }}</td>
                                <td style="width:45%">
                                    <div class="progress" style="height:8px"><div class="progress-bar" style="width: {{ $row['rate'] }}%"></div></div>
                                </td>
                                <td class="small text-end">{{ $row['rate'] }}%</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Top advertisers</h5></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        @forelse ($top as $t)
                            <tr><td class="small">{{ $t->business_name }}</td><td class="small text-end">{{ $t->ads }} ad(s)</td><td class="small text-end">{{ AdCampaign::rupees($t->spend) }}</td></tr>
                        @empty
                            <tr><td class="small text-muted p-3">No paid ads this month.</td></tr>
                        @endforelse
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
