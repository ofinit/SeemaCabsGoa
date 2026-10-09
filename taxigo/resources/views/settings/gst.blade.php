@extends('layouts.main')
@section('title', 'GST')
@php
    use App\Services\Pricing\PricingSettings as P;
    $startValue = old('gst_start_at', $settings[P::GST_START_AT] ? \Carbon\Carbon::parse($settings[P::GST_START_AT])->format('Y-m-d\TH:i') : '');
@endphp
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">GST</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title"><h2 class="mb-0">GST</h2></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>

        <div class="col-lg-7">
            <form method="POST" action="{{ route('admin.setting.saveGst') }}">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-1">GST on rides &amp; packages (Seema Holidays → customers)</h5>
                        <p class="text-muted small mb-0">GST is added on top of the fare. It applies <strong>only to bookings made on or after the start date</strong>; earlier bookings are never recalculated.</p>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="gst_enabled" name="gst_enabled" value="1"
                                {{ old('gst_enabled', $settings[P::GST_ENABLED]) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="gst_enabled">Charge GST</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="gst_start_at">GST applies to bookings made from (IST)</label>
                            <input type="datetime-local" class="form-control" id="gst_start_at" name="gst_start_at" value="{{ $startValue }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="{{ P::GST_RATE_RIDES }}">Rate — cab rides (%)</label>
                                <input type="number" step="0.01" min="0" max="40" class="form-control" id="{{ P::GST_RATE_RIDES }}" name="{{ P::GST_RATE_RIDES }}" value="{{ old(P::GST_RATE_RIDES, $settings[P::GST_RATE_RIDES]) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="{{ P::GST_SAC_RIDES }}">SAC — cab rides</label>
                                <input type="text" inputmode="numeric" class="form-control" id="{{ P::GST_SAC_RIDES }}" name="{{ P::GST_SAC_RIDES }}" value="{{ old(P::GST_SAC_RIDES, $settings[P::GST_SAC_RIDES]) }}" placeholder="as confirmed by your CA">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="{{ P::GST_RATE_PACKAGES }}">Rate — sightseeing packages (%)</label>
                                <input type="number" step="0.01" min="0" max="40" class="form-control" id="{{ P::GST_RATE_PACKAGES }}" name="{{ P::GST_RATE_PACKAGES }}" value="{{ old(P::GST_RATE_PACKAGES, $settings[P::GST_RATE_PACKAGES]) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="{{ P::GST_SAC_PACKAGES }}">SAC — sightseeing packages</label>
                                <input type="text" inputmode="numeric" class="form-control" id="{{ P::GST_SAC_PACKAGES }}" name="{{ P::GST_SAC_PACKAGES }}" value="{{ old(P::GST_SAC_PACKAGES, $settings[P::GST_SAC_PACKAGES]) }}" placeholder="as confirmed by your CA">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-1">GST on the OfinIT platform fee (OfinIT → Seema Holidays)</h5>
                        <p class="text-muted small mb-0">Used on the monthly platform-fee tax invoice.</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="{{ P::GST_RATE_PLATFORM_FEE }}">Rate (%)</label>
                                <input type="number" step="0.01" min="0" max="40" class="form-control" id="{{ P::GST_RATE_PLATFORM_FEE }}" name="{{ P::GST_RATE_PLATFORM_FEE }}" value="{{ old(P::GST_RATE_PLATFORM_FEE, $settings[P::GST_RATE_PLATFORM_FEE]) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="{{ P::GST_SAC_PLATFORM_FEE }}">SAC</label>
                                <input type="text" inputmode="numeric" class="form-control" id="{{ P::GST_SAC_PLATFORM_FEE }}" name="{{ P::GST_SAC_PLATFORM_FEE }}" value="{{ old(P::GST_SAC_PLATFORM_FEE, $settings[P::GST_SAC_PLATFORM_FEE]) }}" placeholder="as confirmed by your CA">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="tdstitle">TDS recorded in settlements (%)</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control" id="tdstitle" name="tdstitle" value="{{ old('tdstitle', $settings['tdstitle'] ?? '0') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mb-4">Save GST settings</button>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Status</h5></div>
                <div class="card-body">
                    @if ($settings[P::GST_ENABLED] === '1')
                        <p class="mb-2"><span class="badge bg-light-success">On</span>
                            from {{ $settings[P::GST_START_AT] ? \Carbon\Carbon::parse($settings[P::GST_START_AT])->format('d M Y, h:i A') : 'now' }}</p>
                    @else
                        <p class="mb-2"><span class="badge bg-light-secondary">Off</span> — customers are not charged GST.</p>
                    @endif
                    @foreach (['Seema Holidays' => $supplier, 'OfinIT' => $platform] as $name => $profile)
                        <p class="mb-1 small">{{ $name }} profile:
                            @if ($profile && $profile->isReadyForInvoicing())
                                <span class="text-success">ready ({{ $profile->gstin }})</span>
                            @else
                                <span class="text-danger">incomplete — {{ implode(', ', $profile?->missingForInvoicing() ?? ['missing']) }}</span>
                            @endif
                        </p>
                    @endforeach
                    <a href="{{ route('admin.setting.businessProfiles') }}" class="small">Edit business profiles →</a>
                    <hr>
                    <p class="small mb-1">With GST on, a ₹1,000 base-fare ride is:</p>
                    <p class="small mb-0">Fare ₹{{ number_format($example['with_gst']['fare'], 2) }} + GST ₹{{ number_format($example['with_gst']['gst'], 2) }} = <strong>₹{{ number_format($example['with_gst']['total'], 2) }}</strong>
                        (advance ₹{{ number_format($example['with_gst']['advance'], 2) }}).</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Recent changes</h5></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tbody>
                            @forelse ($history as $change)
                                <tr>
                                    <td class="small">{{ optional($change->created_at)->timezone('Asia/Kolkata')->format('d M Y H:i') }}<br><span class="text-muted">{{ $change->user_name }}</span></td>
                                    <td class="small">{{ $change->key }}<br>{{ $change->old_value ?? '—' }} → <strong>{{ $change->new_value }}</strong></td>
                                </tr>
                            @empty
                                <tr><td class="text-muted small p-3">No changes yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
