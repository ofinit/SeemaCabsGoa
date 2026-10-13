@extends('layouts.main')
@section('title', 'Complimentary Ad')
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.advertisements.selfServe.index') }}">Self-serve ads</a></li>
                        <li class="breadcrumb-item" aria-current="page">Complimentary ad</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title"><h2 class="mb-0">Complimentary ad (no payment)</h2></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>
        <div class="col-lg-9">
            <form method="POST" action="{{ route('admin.advertisements.selfServe.complimentary.store') }}" enctype="multipart/form-data" class="card">
                @csrf
                <div class="card-body">
                    <p class="text-muted small">Runs like a paid ad — same placements, slot limits and serving — but with no payment, invoice or Route payout. It goes live on the start date without review. Shown as "Complimentary" everywhere and left out of ad revenue.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Advertiser / business name</label>
                            <input class="form-control" name="business_name" value="{{ old('business_name') }}" required maxlength="120">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Placements</label>
                            <div class="row g-1">
                                @foreach ($placements as $placement)
                                    <div class="col-md-6">
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" name="placements[]" value="{{ $placement->id }}" @checked(in_array($placement->id, (array) old('placements', [])))>
                                            <span class="form-check-label">{{ $placement->code }} {{ $placement->name }} <span class="text-muted small">({{ $placement->shapeKey() }})</span></span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Start date</label>
                            <input type="date" class="form-control" name="start_date" value="{{ old('start_date', now('Asia/Kolkata')->toDateString()) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Days</label>
                            <input type="number" class="form-control" name="days" value="{{ old('days', 30) }}" min="1" max="366" required>
                            <div class="form-text">In-cab QR cards (P18): 30, 60 or 90.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cabs carrying QR cards (P18 only)</label>
                            <input type="number" class="form-control" name="targeting[cabs]" value="{{ old('targeting.cabs') }}" min="1" max="200">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">When tapped, open</label>
                            <select class="form-select" name="landing_type">
                                @foreach (['website' => 'Website', 'whatsapp' => 'WhatsApp', 'call' => 'Phone call'] as $key => $label)
                                    <option value="{{ $key }}" @selected(old('landing_type') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Link / WhatsApp number / phone</label>
                            <input class="form-control" name="landing_value" value="{{ old('landing_value') }}" required maxlength="500">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">One-line message / push title <span class="text-muted small">(P16, P11)</span></label>
                            <input class="form-control" name="headline" value="{{ old('headline') }}" maxlength="40">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Push message <span class="text-muted small">(P11)</span></label>
                            <input class="form-control" name="push_body" value="{{ old('push_body') }}" maxlength="120">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sightseeing package <span class="text-muted small">(P14)</span></label>
                            <select class="form-select" name="targeting[package_id]">
                                <option value="">—</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}" @selected(old('targeting.package_id') == $package->id)>{{ $package->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website page groups <span class="text-muted small">(P9)</span></label>
                            @foreach ($pageGroups as $key => $label)
                                <label class="form-check">
                                    <input class="form-check-input" type="checkbox" name="targeting[page_groups][]" value="{{ $key }}" @checked(in_array($key, (array) old('targeting.page_groups', [])))>
                                    <span class="form-check-label small">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="col-12">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" name="image" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">Centre-cropped to each placement's size. For a better fit, add a separate image per size below (optional).</div>
                        </div>
                        @foreach ($placements->unique(fn ($p) => $p->shapeKey()) as $placement)
                            <div class="col-md-4">
                                <label class="form-label small">Image for {{ $placement->shapeKey() }} ({{ $placement->shape }})</label>
                                <input type="file" class="form-control form-control-sm" name="images[{{ $placement->shapeKey() }}]" accept="image/jpeg,image/png,image/webp">
                            </div>
                        @endforeach

                        <div class="col-12">
                            <label class="form-label">Internal note</label>
                            <input class="form-control" name="note" value="{{ old('note') }}" maxlength="500" placeholder="Why it's free (e.g. partner, house ad)">
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('admin.advertisements.selfServe.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-success">Create &amp; run ad</button>
                </div>
            </form>
        </div>
    </div>
@endsection
