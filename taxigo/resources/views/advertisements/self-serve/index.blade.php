@extends('layouts.main')
@section('title', 'Ad Review Queue')
@php use App\Models\AdCampaign; @endphp
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Advertisements</li>
                        <li class="breadcrumb-item" aria-current="page">Self-serve ads</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title"><h2 class="mb-0">Self-serve ads</h2></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>

        <div class="col-12 d-flex flex-wrap gap-2 align-items-center mb-3">
            @php
                $tabCounts = [
                    'review' => $counts[AdCampaign::IN_REVIEW] ?? 0,
                    'changes' => $counts[AdCampaign::CHANGES_REQUESTED] ?? 0,
                    'live' => $counts[AdCampaign::APPROVED] ?? 0,
                    'paused' => $counts[AdCampaign::PAUSED] ?? 0,
                    'payment' => $counts[AdCampaign::PENDING_PAYMENT] ?? 0,
                ];
            @endphp
            @foreach (\App\Http\Controllers\Admin\AdCampaignController::TABS as $key => $label)
                <a class="btn btn-sm {{ $tab === $key ? 'btn-primary' : 'btn-light-primary' }}" href="{{ route('admin.advertisements.selfServe.index', ['tab' => $key]) }}">
                    {{ $label }}@isset($tabCounts[$key]) ({{ $tabCounts[$key] }})@endisset
                </a>
            @endforeach
            <form class="ms-auto d-flex gap-2" method="GET">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input class="form-control form-control-sm" name="q" value="{{ request('q') }}" placeholder="Reference or business">
                <button class="btn btn-sm btn-secondary">Search</button>
            </form>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Ad</th>
                                    <th>Advertiser</th>
                                    <th>Placements</th>
                                    <th>Dates</th>
                                    <th class="text-end">Total</th>
                                    <th>Status</th>
                                    <th>{{ $tab === 'review' ? 'Waiting' : 'Updated' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($campaigns as $campaign)
                                    @php
                                        $hours = $campaign->submitted_at ? (int) $campaign->submitted_at->diffInHours(now(), true) : null;
                                    @endphp
                                    <tr>
                                        <td><a href="{{ route('admin.advertisements.selfServe.show', $campaign) }}" class="fw-semibold">{{ $campaign->reference }}</a></td>
                                        <td>
                                            {{ $campaign->advertiser->business_name }}
                                            <div class="small text-muted">{{ $campaign->advertiser->category->name ?? '—' }} · {{ ucfirst($campaign->tier) }}</div>
                                        </td>
                                        <td>{{ $campaign->items->map(fn ($i) => $i->placement->code)->implode(', ') }}</td>
                                        <td class="small">{{ $campaign->start_date?->format('d-m-Y') }} → {{ $campaign->end_date?->format('d-m-Y') }}<br>{{ $campaign->days }} days</td>
                                        <td class="text-end">{{ AdCampaign::rupees($campaign->total_amount) }}</td>
                                        <td>
                                            <span class="badge bg-light-secondary">{{ $campaign->displayStatus() }}</span>
                                            @if ($campaign->needs_second_approval)<span class="badge bg-light-warning">2 admins</span>@endif
                                            @if ($campaign->advertiser->category?->second_approval)<span class="badge bg-light-warning">high risk</span>@endif
                                            @if ($campaign->auto_flags)<span class="badge bg-light-danger" title="{{ implode('; ', $campaign->auto_flags) }}">flagged</span>@endif
                                        </td>
                                        <td class="small">
                                            @if ($tab === 'review' && $hours !== null)
                                                <span class="{{ $hours >= 24 ? 'text-danger fw-bold' : '' }}">{{ $hours }} h</span>
                                            @else
                                                {{ $campaign->updated_at?->format('d-m-Y H:i') }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">Nothing here.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{ $campaigns->links() }}
        </div>
    </div>
@endsection
