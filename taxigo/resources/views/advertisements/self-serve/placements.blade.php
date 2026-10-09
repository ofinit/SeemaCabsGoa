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
    </div>
@endsection
