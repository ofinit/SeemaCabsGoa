@extends('layouts.main')
@section('title', 'Ad Reports')
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Advertisements</li>
                        <li class="breadcrumb-item" aria-current="page">Ad reports</li>
                    </ul>
                </div>
                <div class="col-md-12"><div class="page-header-title"><h2 class="mb-0">Ad reports</h2></div></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>
        <div class="col-12 d-flex gap-2 mb-3">
            @foreach (['open' => 'Open', 'actioned' => 'Upheld', 'dismissed' => 'Dismissed'] as $key => $label)
                <a class="btn btn-sm {{ $status === $key ? 'btn-primary' : 'btn-light-primary' }}" href="{{ route('admin.advertisements.selfServe.reports', ['status' => $key]) }}">{{ $label }}</a>
            @endforeach
            <span class="ms-auto small text-muted align-self-center">Ads are paused automatically after {{ \App\Services\Ads\AdSettings::get(\App\Services\Ads\AdSettings::REPORT_THRESHOLD) }} reports from different people.</span>
        </div>

        @forelse ($groups as $adId => $reports)
            @php
                $ad = $reports->first()->advertisement;
                $campaign = $reports->first()->campaign;
            @endphp
            <div class="col-12">
                <div class="card">
                    <div class="card-body d-flex flex-wrap gap-4">
                        <div style="width:220px">
                            @if ($ad)<img src="{{ $ad->add_banner_image }}" class="img-fluid rounded border" alt="">@endif
                            <div class="small mt-1">
                                @if ($campaign)
                                    <a href="{{ route('admin.advertisements.selfServe.show', $campaign) }}">{{ $campaign->reference }}</a> · {{ $campaign->advertiser->business_name ?? '' }}
                                    <div><span class="badge bg-light-secondary">{{ $campaign->displayStatus() }}</span></div>
                                @else
                                    Admin-created ad #{{ $adId }} · <span class="badge bg-light-secondary">{{ $ad?->approval_status }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-2"><strong>{{ $reports->count() }}</strong> report(s)</p>
                            <table class="table table-sm mb-2">
                                @foreach ($reports as $r)
                                    <tr>
                                        <td class="small text-nowrap">{{ $r->created_at->format('d-m-Y H:i') }}</td>
                                        <td class="small">{{ $reasons[$r->reason] ?? $r->reason }}@if ($r->note)<div class="text-muted">{{ $r->note }}</div>@endif</td>
                                        <td class="small text-muted">{{ $r->platform }} · {{ $r->user_id ? 'customer #' . $r->user_id : 'visitor' }}</td>
                                    </tr>
                                @endforeach
                            </table>
                            @if ($status === 'open' && $ad)
                                <form method="POST" action="{{ route('admin.advertisements.selfServe.reports.resolve', $ad) }}" class="d-flex gap-2">
                                    @csrf
                                    <button class="btn btn-sm btn-light-success" name="action" value="dismiss">Dismiss — ad is fine (resume)</button>
                                    <button class="btn btn-sm btn-light-danger" name="action" value="uphold">Uphold — keep it paused</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="card"><div class="card-body text-muted">No {{ $status }} reports.</div></div></div>
        @endforelse
    </div>
@endsection
