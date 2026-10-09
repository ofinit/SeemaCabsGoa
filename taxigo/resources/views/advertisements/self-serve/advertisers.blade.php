@extends('layouts.main')
@section('title', 'Advertisers')
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Advertisements</li>
                        <li class="breadcrumb-item" aria-current="page">Advertisers</li>
                    </ul>
                </div>
                <div class="col-md-12 d-flex align-items-center">
                    <h2 class="mb-0">Advertisers</h2>
                    <form class="ms-auto d-flex gap-2" method="GET">
                        <input class="form-control form-control-sm" name="q" value="{{ request('q') }}" placeholder="Business, phone or email">
                        <button class="btn btn-sm btn-secondary">Search</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Business</th><th>Contact</th><th>GSTIN</th><th>Licences</th><th>Ads</th><th style="width:380px">Category · status</th></tr></thead>
                            <tbody>
                                @forelse ($advertisers as $a)
                                    <tr>
                                        <td class="fw-semibold">{{ $a->business_name }}<div class="small text-muted">since {{ $a->created_at->format('d-m-Y') }}</div></td>
                                        <td class="small">{{ $a->contact_name }}<br>{{ $a->phone }}<br>{{ $a->email }}</td>
                                        <td class="small">{{ $a->gstin ?: '—' }}</td>
                                        <td class="small">
                                            @forelse ($a->licences as $l)
                                                <div><a href="{{ route('admin.advertisements.selfServe.licences.show', $l) }}" target="_blank">{{ $l->number ?: $l->type }}</a> · {{ $l->status }} · to {{ $l->valid_until?->format('d-m-Y') }}</div>
                                            @empty — @endforelse
                                        </td>
                                        <td><a href="{{ route('admin.advertisements.selfServe.index', ['tab' => 'live', 'q' => $a->business_name]) }}">{{ $a->campaigns_count }}</a></td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.advertisements.selfServe.advertisers.update', $a) }}" class="d-flex gap-1 flex-wrap">
                                                @csrf
                                                <select class="form-select form-select-sm" name="category_id" style="max-width:170px">
                                                    @foreach ($categories as $c)
                                                        <option value="{{ $c->id }}" @selected($c->id === $a->category_id)>{{ $c->name }} ({{ $c->tier }})</option>
                                                    @endforeach
                                                </select>
                                                <select class="form-select form-select-sm" name="status" style="max-width:100px">
                                                    <option value="active" @selected($a->status === 'active')>Active</option>
                                                    <option value="blocked" @selected($a->status === 'blocked')>Blocked</option>
                                                </select>
                                                <input class="form-control form-control-sm" name="status_note" value="{{ $a->status_note }}" placeholder="Note" style="max-width:120px">
                                                <button class="btn btn-sm btn-light-primary">Save</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No advertisers yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{ $advertisers->links() }}
        </div>
    </div>
@endsection
