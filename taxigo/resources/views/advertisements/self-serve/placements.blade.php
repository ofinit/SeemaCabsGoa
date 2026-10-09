@extends('layouts.main')
@section('title', 'Ad Placements & Pricing')
@php use App\Services\Ads\AdSettings; @endphp
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Advertisements</li>
                        <li class="breadcrumb-item" aria-current="page">Placements &amp; pricing</li>
                    </ul>
                </div>
                <div class="col-md-12"><div class="page-header-title"><h2 class="mb-0">Placements &amp; pricing</h2></div></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>

        <div class="col-12">
            <form method="POST" action="{{ route('admin.advertisements.selfServe.placements.save') }}" class="card">
                @csrf
                <div class="card-header"><h5 class="mb-0">Placements</h5>
                    <p class="small text-muted mb-0">Prices are per day in rupees, before GST. Slots = ads that share the placement on the same day. Changes apply to ads not yet paid.</p></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Code</th><th>Placement</th><th>Image</th><th>Standard ₹/day</th><th>Premium ₹/day</th><th>Slots</th><th>On sale</th></tr></thead>
                            <tbody>
                                @foreach ($placements as $p)
                                    <tr>
                                        <td class="fw-semibold">{{ $p->code }}</td>
                                        <td>{{ $p->name }}<div class="small text-muted">{{ $p->description }} · screen {{ $p->screen_id }}</div></td>
                                        <td class="small">{{ $p->shape }}<br>{{ $p->width }}×{{ $p->height }}</td>
                                        <td style="width:130px"><input class="form-control form-control-sm" type="number" step="1" min="0" name="placements[{{ $p->id }}][price_standard]" value="{{ $p->price_standard / 100 }}"></td>
                                        <td style="width:130px"><input class="form-control form-control-sm" type="number" step="1" min="0" name="placements[{{ $p->id }}][price_premium]" value="{{ $p->price_premium / 100 }}"></td>
                                        <td style="width:90px"><input class="form-control form-control-sm" type="number" min="1" max="20" name="placements[{{ $p->id }}][slots]" value="{{ $p->slots }}" @disabled($p->exclusive)>
                                            @if ($p->exclusive)<input type="hidden" name="placements[{{ $p->id }}][slots]" value="1"><div class="small text-muted">exclusive</div>@endif</td>
                                        <td><input type="hidden" name="placements[{{ $p->id }}][active]" value="0"><input class="form-check-input" type="checkbox" name="placements[{{ $p->id }}][active]" value="1" @checked($p->active)></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer"><button class="btn btn-primary">Save placements</button></div>
            </form>
        </div>

        <div class="col-lg-6">
            <form method="POST" action="{{ route('admin.advertisements.selfServe.settings.save') }}" class="card">
                @csrf
                <div class="card-header"><h5 class="mb-0">Ad settings</h5>
                    <p class="small text-muted mb-0">Seema Holidays keeps its commission % of the net price; OfinIT receives the rest + GST by split. Changes are audited.</p></div>
                <div class="card-body">
                    @foreach ($labels as $key => $label)
                        <div class="mb-2">
                            <label class="form-label small mb-0">{{ $label }}</label>
                            @if ($key === AdSettings::ENABLED)
                                <select class="form-select form-select-sm" name="{{ $key }}">
                                    <option value="1" @selected($settings[$key] === '1')>Yes — advertisers can book</option>
                                    <option value="0" @selected($settings[$key] === '0')>No — paused</option>
                                </select>
                            @elseif ($key === AdSettings::SECOND_APPROVAL)
                                <select class="form-select form-select-sm" name="{{ $key }}">
                                    <option value="0" @selected($settings[$key] === '0')>No — one admin approves (risky ads are highlighted)</option>
                                    <option value="1" @selected($settings[$key] === '1')>Yes — a second, different admin must also approve</option>
                                </select>
                            @else
                                <input class="form-control form-control-sm" name="{{ $key }}" value="{{ old($key, $settings[$key]) }}">
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="card-footer"><button class="btn btn-primary">Save settings</button></div>
            </form>
        </div>

        <div class="col-lg-6">
            <form method="POST" action="{{ route('admin.advertisements.selfServe.categories.save') }}" class="card">
                @csrf
                <div class="card-header"><h5 class="mb-0">Advertiser categories</h5>
                    <p class="small text-muted mb-0">The tier sets the price. "High risk" categories are highlighted for the reviewer (and need a second admin if that setting is on).</p></div>
                <div class="card-body p-0">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Category</th><th>Tier</th><th>Licence</th><th>High risk</th><th>Active</th></tr></thead>
                        <tbody>
                            @foreach ($categories as $c)
                                <tr>
                                    <td class="small">{{ $c->name }}<div class="text-muted">{{ $c->licence_label }}</div></td>
                                    <td><select class="form-select form-select-sm" name="categories[{{ $c->id }}][tier]">
                                        <option value="standard" @selected($c->tier === 'standard')>Standard</option>
                                        <option value="premium" @selected($c->tier === 'premium')>Premium</option>
                                    </select></td>
                                    <td><input type="hidden" name="categories[{{ $c->id }}][licence_required]" value="0"><input class="form-check-input" type="checkbox" name="categories[{{ $c->id }}][licence_required]" value="1" @checked($c->licence_required)></td>
                                    <td><input type="hidden" name="categories[{{ $c->id }}][second_approval]" value="0"><input class="form-check-input" type="checkbox" name="categories[{{ $c->id }}][second_approval]" value="1" @checked($c->second_approval)></td>
                                    <td><input type="hidden" name="categories[{{ $c->id }}][active]" value="0"><input class="form-check-input" type="checkbox" name="categories[{{ $c->id }}][active]" value="1" @checked($c->active)></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer"><button class="btn btn-primary">Save categories</button></div>
            </form>
        </div>

        <div class="col-lg-6">
            <form method="POST" action="{{ route('admin.advertisements.selfServe.bundles.save') }}" class="card">
                @csrf
                <div class="card-header"><h5 class="mb-0">Bundles</h5>
                    <p class="small text-muted mb-0">Per-day price when an advertiser books every placement in the bundle. Applied automatically when cheaper.</p></div>
                <div class="card-body p-0">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Bundle</th><th>Standard ₹/day</th><th>Premium ₹/day</th><th>On</th></tr></thead>
                        <tbody>
                            @foreach ($bundles as $b)
                                <tr>
                                    <td class="small">{{ $b->name }}<div class="text-muted">{{ implode(' + ', $b->placement_codes) }}</div></td>
                                    <td><input class="form-control form-control-sm" type="number" min="0" name="bundles[{{ $b->id }}][price_standard]" value="{{ $b->price_standard / 100 }}"></td>
                                    <td><input class="form-control form-control-sm" type="number" min="0" name="bundles[{{ $b->id }}][price_premium]" value="{{ $b->price_premium / 100 }}"></td>
                                    <td><input type="hidden" name="bundles[{{ $b->id }}][active]" value="0"><input class="form-check-input" type="checkbox" name="bundles[{{ $b->id }}][active]" value="1" @checked($b->active)></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer"><button class="btn btn-primary">Save bundles</button></div>
            </form>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Peak-season pricing</h5>
                    <p class="small text-muted mb-0">Every ad day inside a window is priced × the multiplier (the highest applies when windows overlap).</p></div>
                <div class="card-body p-0">
                    <table class="table table-sm align-middle mb-0">
                        <tbody>
                            @forelse ($peaks as $peak)
                                <tr>
                                    <td class="small">{{ $peak->name }}</td>
                                    <td class="small">{{ $peak->start_date->format('d-m-Y') }} → {{ $peak->end_date->format('d-m-Y') }}</td>
                                    <td class="small">× {{ rtrim(rtrim(number_format($peak->multiplier, 2), '0'), '.') }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.advertisements.selfServe.peaks.delete', $peak) }}" onsubmit="return confirm('Remove this peak window?')">@csrf<button class="btn btn-sm btn-link text-danger p-0">Remove</button></form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="text-muted small p-3">No peak windows.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <form method="POST" action="{{ route('admin.advertisements.selfServe.peaks.add') }}" class="card-footer d-flex flex-wrap gap-2">
                    @csrf
                    <input class="form-control form-control-sm" name="name" placeholder="e.g. Carnival" required style="max-width:140px">
                    <input class="form-control form-control-sm" type="date" name="start_date" required style="max-width:150px">
                    <input class="form-control form-control-sm" type="date" name="end_date" required style="max-width:150px">
                    <input class="form-control form-control-sm" type="number" step="0.1" min="1" max="5" name="multiplier" value="1.5" required style="max-width:80px">
                    <button class="btn btn-sm btn-primary">Add</button>
                </form>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Coupons</h5>
                    <p class="small text-muted mb-0">Advertisers enter the code at checkout. A coupon and the launch offer don't combine — the bigger saving applies.</p></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead><tr><th>Code</th><th>Discount</th><th>Used</th><th>Per advertiser</th><th>Minimum</th><th>Valid</th><th>First ad only</th><th></th></tr></thead>
                            <tbody>
                                @forelse ($coupons as $c)
                                    <tr class="{{ $c->active ? '' : 'text-muted' }}">
                                        <td class="fw-semibold">{{ $c->code }}<div class="small text-muted">{{ $c->description }}</div></td>
                                        <td>{{ $c->label() }}</td>
                                        <td>{{ $c->used }}{{ $c->max_uses ? ' / ' . $c->max_uses : '' }}</td>
                                        <td>{{ $c->per_advertiser }}</td>
                                        <td>{{ $c->min_amount ? \App\Models\AdCampaign::rupees($c->min_amount) : '—' }}</td>
                                        <td class="small">{{ $c->valid_from?->format('d-m-Y') ?? 'now' }} → {{ $c->valid_until?->format('d-m-Y') ?? 'no end' }}</td>
                                        <td>{{ $c->first_booking_only ? 'Yes' : 'No' }}</td>
                                        <td><form method="POST" action="{{ route('admin.advertisements.selfServe.coupons.toggle', $c) }}">@csrf<button class="btn btn-sm {{ $c->active ? 'btn-light-warning' : 'btn-light-success' }}">{{ $c->active ? 'Disable' : 'Enable' }}</button></form></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-muted small p-3">No coupons yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.advertisements.selfServe.coupons.add') }}" class="card-footer">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2"><label class="form-label small mb-0">Code</label><input class="form-control form-control-sm text-uppercase" name="code" required maxlength="30" value="{{ old('code') }}"></div>
                        <div class="col-md-2"><label class="form-label small mb-0">Type</label><select class="form-select form-select-sm" name="type"><option value="percent">% off</option><option value="flat">₹ off</option></select></div>
                        <div class="col-md-1"><label class="form-label small mb-0">Value</label><input class="form-control form-control-sm" type="number" min="1" name="value" required value="{{ old('value') }}"></div>
                        <div class="col-md-1"><label class="form-label small mb-0">Max uses</label><input class="form-control form-control-sm" type="number" min="1" name="max_uses" value="{{ old('max_uses') }}"></div>
                        <div class="col-md-1"><label class="form-label small mb-0">Per adv.</label><input class="form-control form-control-sm" type="number" min="1" name="per_advertiser" value="1" required></div>
                        <div class="col-md-1"><label class="form-label small mb-0">Min ₹</label><input class="form-control form-control-sm" type="number" min="0" name="min_amount" value="{{ old('min_amount') }}"></div>
                        <div class="col-md-2"><label class="form-label small mb-0">Valid until</label><input class="form-control form-control-sm" type="date" name="valid_until" value="{{ old('valid_until') }}"></div>
                        <div class="col-md-1"><label class="form-check small mb-1"><input class="form-check-input" type="checkbox" name="first_booking_only" value="1"> 1st ad</label></div>
                        <div class="col-md-1"><button class="btn btn-sm btn-primary w-100">Add</button></div>
                        <div class="col-12"><input class="form-control form-control-sm" name="description" placeholder="Note (optional, e.g. Carnival promo)" maxlength="150"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
