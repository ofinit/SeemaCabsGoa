@extends('layouts.main')
@section('title', 'Ad ' . $campaign->reference)
@php
    use App\Models\AdCampaign;
    $advertiser = $campaign->advertiser;
    $category = $advertiser->category;
    $creatives = $campaign->creatives->keyBy('shape');
    $canDecide = $campaign->status === AdCampaign::IN_REVIEW;
    $awaitingSecond = $campaign->needs_second_approval && $campaign->first_approved_by;
@endphp
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.advertisements.selfServe.index') }}">Self-serve ads</a></li>
                        <li class="breadcrumb-item" aria-current="page">{{ $campaign->reference }}</li>
                    </ul>
                </div>
                <div class="col-md-12 d-flex align-items-center gap-2 flex-wrap">
                    <h2 class="mb-0">{{ $campaign->reference }} — {{ $advertiser->business_name }}</h2>
                    <span class="badge bg-light-primary fs-6">{{ $campaign->displayStatus() }}</span>
                    @if ($campaign->needs_second_approval)<span class="badge bg-light-warning">Needs two admins</span>@endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>

        @if ($category && $category->second_approval)
            <div class="col-12">
                <div class="alert alert-warning"><strong>High-risk category ({{ $category->name }}).</strong> Check the category rules carefully: {{ $category->rules }}</div>
            </div>
        @endif
        @if ($campaign->reports->where('status', 'open')->isNotEmpty())
            <div class="col-12">
                <div class="alert alert-danger"><strong>{{ $campaign->reports->where('status', 'open')->count() }} open user report(s).</strong>
                    <a href="{{ route('admin.advertisements.selfServe.reports') }}">Review reports</a></div>
            </div>
        @endif
        @if ($campaign->paused_reason && $campaign->status === AdCampaign::PAUSED)
            <div class="col-12">
                <div class="alert alert-warning">Paused automatically: {{ ['reports' => 'user reports', 'licence' => 'licence expired', 'link' => 'link not opening', 'admin' => 'by an admin'][$campaign->paused_reason] ?? $campaign->paused_reason }}. Resume it from the box on the right once fixed.</div>
            </div>
        @endif
        @if ($campaign->auto_flags)
            <div class="col-12">
                <div class="alert alert-warning"><strong>Automated checks flagged:</strong> {{ implode(' · ', $campaign->auto_flags) }}</div>
            </div>
        @endif
        @if ($conflicts && in_array($campaign->status, [AdCampaign::IN_REVIEW, AdCampaign::CHANGES_REQUESTED]))
            <div class="col-12">
                <div class="alert alert-danger"><strong>Slot conflict:</strong>
                    @foreach ($conflicts as $code => $date) {{ $code }} sold out from {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}. @endforeach
                    Check other bookings before approving.</div>
            </div>
        @endif

        <div class="col-lg-7">
            {{-- Creatives in the booked placements --}}
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Creative</h5></div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-4">
                        @foreach ($campaign->items as $item)
                            @php $creative = $creatives[$item->placement->shapeKey()] ?? null; @endphp
                            <div style="width: 220px">
                                <div class="small fw-semibold mb-1">{{ $item->placement->code }} · {{ $item->placement->name }}</div>
                                <div class="border rounded-4 p-2 bg-light" style="border-width: 4px !important">
                                    @if ($creative)
                                        <a href="{{ $creative->url() }}" target="_blank"><img src="{{ $creative->url() }}" class="img-fluid rounded" alt=""></a>
                                        <div class="small text-muted mt-1">{{ $creative->width }}×{{ $creative->height }} · {{ number_format($creative->bytes / 1024) }} KB</div>
                                    @else
                                        <div class="text-danger small">No image for this shape</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <hr>
                    @if ($campaign->push_body)<p class="mb-1"><strong>Push:</strong> Sponsored · {{ $campaign->headline }} — {{ $campaign->push_body }}</p>@endif
                    <p class="mb-1"><strong>Audience:</strong> {{ \App\Services\Ads\AdTargeting::describe($campaign->targeting) }}</p>
                    @if ($campaign->headline)<p class="mb-1"><strong>App-open message:</strong> {{ $campaign->headline }}</p>@endif
                    @if ($campaign->exclusive_category)<p class="mb-1"><strong>Category exclusivity</strong> booked.</p>@endif
                    <p class="mb-1"><strong>Link ({{ $campaign->landing_type }}):</strong>
                        @if ($campaign->landing_url)
                            <a href="{{ $campaign->landing_url }}" target="_blank" rel="noopener noreferrer">{{ $campaign->landing_url }}</a>
                        @else — @endif
                    </p>
                </div>
            </div>

            {{-- Checklist & decision --}}
            @if ($canDecide)
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Approval checklist</h5></div>
                    <form method="POST" action="{{ route('admin.advertisements.selfServe.decide', $campaign) }}" class="card-body">
                        @csrf
                        @if ($awaitingSecond)
                            <div class="alert alert-info">First approval by {{ optional($campaign->reviews->firstWhere('stage', 'first'))->reviewer->name ?? 'admin #' . $campaign->first_approved_by }}
                                on {{ $campaign->first_approved_at?->format('d-m-Y H:i') }}. A <strong>different admin</strong> must give the second approval.</div>
                        @endif
                        @foreach ($checklist as $section => $items)
                            <h6 class="mt-3">{{ $section }}</h6>
                            @foreach ($items as $key => $text)
                                <div class="d-flex align-items-start justify-content-between gap-3 border-bottom py-2">
                                    <div class="small">{{ $text }}
                                        @if ($key === 'payment')
                                            <div class="{{ $campaign->isPaid() ? 'text-success' : 'text-danger' }}">
                                                {{ $campaign->isPaid() ? 'System: verified ' . ucfirst($campaign->payment_gateway) . ' payment ' . $campaign->transaction_id . ' on ' . $campaign->paid_at->format('d-m-Y H:i') : 'System: not paid' }}
                                            </div>
                                        @elseif ($key === 'licence')
                                            <div class="text-muted">{{ $category && $category->licence_required ? 'Required: ' . $category->licence_label : 'Not required for this category' }}</div>
                                        @elseif ($key === 'schedule')
                                            <div class="{{ $conflicts ? 'text-danger' : 'text-success' }}">{{ $conflicts ? 'System: slot conflict (see above)' : 'System: no slot conflicts' }}</div>
                                        @endif
                                    </div>
                                    <div class="btn-group btn-group-sm flex-shrink-0" role="group">
                                        <input type="radio" class="btn-check" name="checks[{{ $key }}]" id="c-{{ $key }}-p" value="pass" @checked(old("checks.$key") === 'pass')>
                                        <label class="btn btn-outline-success" for="c-{{ $key }}-p">Pass</label>
                                        <input type="radio" class="btn-check" name="checks[{{ $key }}]" id="c-{{ $key }}-f" value="fail" @checked(old("checks.$key") === 'fail')>
                                        <label class="btn btn-outline-danger" for="c-{{ $key }}-f">Fail</label>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach

                        <h6 class="mt-4">Reason codes <span class="text-muted small">(for changes / reject)</span></h6>
                        <div class="row">
                            @foreach ($reasons as $code => $label)
                                <div class="col-md-6">
                                    <label class="form-check small">
                                        <input class="form-check-input" type="checkbox" name="reasons[]" value="{{ $code }}" @checked(in_array($code, old('reasons', [])))>
                                        <span class="form-check-label">{{ $code }} — {{ $label }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <label class="form-label mt-3">Note to the advertiser</label>
                        <textarea class="form-control" name="note" rows="2" maxlength="1000">{{ old('note') }}</textarea>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button class="btn btn-success" name="decision" value="approve">{{ $campaign->needs_second_approval && !$campaign->first_approved_by ? 'Approve (first of two)' : 'Approve' }}</button>
                            <button class="btn btn-warning" name="decision" value="changes">Request changes</button>
                            <button class="btn btn-danger" name="decision" value="reject" onclick="return confirm('Reject this ad and refund the advertiser in full?')">Reject &amp; refund</button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Audit log --}}
            <div class="card">
                <div class="card-header"><h5 class="mb-0">History</h5></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tbody>
                            @forelse ($campaign->reviews as $review)
                                <tr>
                                    <td class="small text-nowrap">{{ $review->created_at->format('d-m-Y H:i') }}</td>
                                    <td class="small">{{ $review->stage === 'advertiser' ? 'Advertiser' : ($review->reviewer->name ?? 'Admin #' . $review->reviewer_id) }}</td>
                                    <td class="small">{{ ucfirst($review->decision) }}{{ $review->stage === 'second' ? ' (second)' : '' }}
                                        @if ($review->reason_codes) · {{ implode(', ', $review->reason_codes) }} @endif
                                        @if ($review->checklist && $review->stage !== 'advertiser')
                                            · {{ collect($review->checklist)->filter(fn ($v) => $v === 'fail')->keys()->implode(', ') ?: 'all pass' }}
                                        @endif
                                        @if ($review->note)<div class="text-muted">{{ $review->note }}</div>@endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="text-muted small p-3">No reviews yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Advertiser</h5></div>
                <div class="card-body small">
                    <p class="mb-1"><strong>{{ $advertiser->business_name }}</strong> @if ($advertiser->isBlocked())<span class="badge bg-danger">Blocked</span>@endif</p>
                    @if ($advertiser->agency)<p class="mb-1">Booked by agency: <strong>{{ $advertiser->agency->name }}</strong></p>@endif
                    <p class="mb-1">{{ $category->name ?? '—' }} · <strong>{{ ucfirst($category->tier ?? 'standard') }}</strong> tier (booked as {{ $campaign->tier }})</p>
                    @if ($category && $category->rules)<p class="mb-1 text-muted">{{ $category->rules }}</p>@endif
                    <p class="mb-1">{{ $advertiser->contact_name }} · {{ $advertiser->phone }} · {{ $advertiser->email }}</p>
                    <p class="mb-1">Account: {{ $advertiser->user->name ?? '—' }} (#{{ $advertiser->user_id }})</p>
                    <p class="mb-1">GSTIN: {{ $advertiser->gstin ?: '—' }} @if ($advertiser->legal_name)· {{ $advertiser->legal_name }}@endif</p>
                    @if ($advertiser->billing_address)<p class="mb-1">{{ $advertiser->billing_address }}</p>@endif
                    <h6 class="mt-3">Licences</h6>
                    @forelse ($advertiser->licences as $licence)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-1 gap-2">
                            <div>
                                <a href="{{ route('admin.advertisements.selfServe.licences.show', $licence) }}" target="_blank">{{ $licence->type }} {{ $licence->number }}</a>
                                <div class="text-muted">valid to {{ $licence->valid_until?->format('d-m-Y') ?? '—' }} · {{ $licence->status }}</div>
                            </div>
                            <form method="POST" action="{{ route('admin.advertisements.selfServe.licences.verify', $licence) }}" class="d-flex gap-1">
                                @csrf
                                <button class="btn btn-sm btn-light-success" name="status" value="verified">Verify</button>
                                <button class="btn btn-sm btn-light-danger" name="status" value="rejected">Reject</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-muted mb-0">None uploaded.</p>
                    @endforelse
                    @if ($otherCampaigns->isNotEmpty())
                        <h6 class="mt-3">Other ads</h6>
                        @foreach ($otherCampaigns as $other)
                            <div><a href="{{ route('admin.advertisements.selfServe.show', $other) }}">{{ $other->reference }}</a> · {{ $other->displayStatus() }}</div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5 class="mb-0">Booking & money</h5></div>
                <div class="card-body small">
                    <p class="mb-2">{{ $campaign->start_date?->format('d-m-Y') }} → {{ $campaign->end_date?->format('d-m-Y') }} ({{ $campaign->days }} days)</p>
                    <table class="table table-sm">
                        @foreach ($campaign->items as $item)
                            <tr><td>{{ $item->placement->code }} {{ $item->placement->name }}</td><td class="text-end">{{ AdCampaign::rupees($item->price_per_day) }} × {{ $item->days }}</td><td class="text-end">{{ AdCampaign::rupees($item->subtotal) }}</td></tr>
                        @endforeach
                        @if ($campaign->peak_amount > 0)
                            <tr><td colspan="2" class="text-muted">incl. peak-season pricing</td><td class="text-end text-muted">{{ AdCampaign::rupees($campaign->peak_amount) }}</td></tr>
                        @endif
                        @if ($campaign->bundle_discount > 0)
                            <tr><td colspan="2">Bundle saving</td><td class="text-end">− {{ AdCampaign::rupees($campaign->bundle_discount) }}</td></tr>
                        @endif
                        @if ($campaign->exclusivity_amount > 0)
                            <tr><td colspan="2">Category exclusivity</td><td class="text-end">+ {{ AdCampaign::rupees($campaign->exclusivity_amount) }}</td></tr>
                        @endif
                        @if ($campaign->discount_amount > 0)
                            <tr><td colspan="2">Discount {{ (float) $campaign->discount_percent }}%</td><td class="text-end">− {{ AdCampaign::rupees($campaign->discount_amount) }}</td></tr>
                        @endif
                        @if ($campaign->promo_discount > 0)
                            <tr><td colspan="2">{{ $campaign->promo_label }}</td><td class="text-end">− {{ AdCampaign::rupees($campaign->promo_discount) }}</td></tr>
                        @endif
                        <tr><td colspan="2">Net (taxable)</td><td class="text-end">{{ AdCampaign::rupees($campaign->net_amount) }}</td></tr>
                        <tr><td colspan="2">{{ $campaign->inter_state ? 'IGST' : 'CGST + SGST' }} {{ (float) $campaign->gst_rate }}%</td><td class="text-end">{{ AdCampaign::rupees($campaign->gstAmount()) }}</td></tr>
                        <tr class="fw-bold"><td colspan="2">Total</td><td class="text-end">{{ AdCampaign::rupees($campaign->total_amount) }}</td></tr>
                        <tr><td colspan="2">OfinIT share ({{ 100 - (float) $campaign->seema_percent }}%) + GST</td><td class="text-end">{{ AdCampaign::rupees($campaign->ofinit_total) }}</td></tr>
                        <tr><td colspan="2">Seema Holidays keeps</td><td class="text-end">{{ AdCampaign::rupees($campaign->total_amount - $campaign->ofinit_total) }}</td></tr>
                    </table>
                    <p class="mb-1">Payment: {{ $campaign->isPaid() ? ucfirst($campaign->payment_gateway) . ' ' . $campaign->transaction_id . ' · ' . $campaign->paid_at->format('d-m-Y H:i') : 'not paid' }}</p>
                    <p class="mb-1">OfinIT split:
                        @if ($campaign->transfer_status === 'done')
                            <span class="text-success">{{ AdCampaign::rupees($campaign->transfer_amount) }} · {{ $campaign->transfer_reference }}</span>
                        @elseif ($campaign->isPaid())
                            <span class="text-danger">{{ $campaign->transfer_error ?: 'pending' }}</span>
                            <form method="POST" action="{{ route('admin.advertisements.selfServe.retryTransfer', $campaign) }}" class="d-inline">@csrf<button class="btn btn-sm btn-link p-0">Retry</button></form>
                        @else — @endif
                    </p>
                    @if ($campaign->refund_amount > 0 || $campaign->refund_error)
                        <p class="mb-1">Refund: {{ AdCampaign::rupees($campaign->refund_amount) }} {{ $campaign->refund_reference }} @if ($campaign->refund_error)<span class="text-danger">· {{ $campaign->refund_error }}</span>@endif</p>
                    @endif
                    @foreach ($campaign->invoices as $invoice)
                        <div><a href="{{ route('admin.invoices.show', $invoice) }}" target="_blank">{{ $invoice->label() }} {{ $invoice->number ?? '(draft)' }}</a> · {{ AdCampaign::rupees((int) round($invoice->total * 100)) }}</div>
                    @endforeach
                </div>
            </div>

            @if ($campaign->isPaid())
                <div class="card">
                    <div class="card-header d-flex justify-content-between"><h5 class="mb-0">Results</h5><a class="small" href="{{ route('admin.advertisements.selfServe.export', $campaign) }}">CSV</a></div>
                    <div class="card-body small">
                        <p class="mb-1">{{ number_format($stats['views']) }} views · {{ number_format($stats['clicks']) }} taps · tap rate {{ $stats['ctr'] === null ? '—' : $stats['ctr'] . '%' }}</p>
                        @foreach ($stats['placements'] as $code => $row)
                            <div>{{ $code }}: {{ number_format($row['views'] ?? 0) }} views, {{ number_format($row['clicks'] ?? 0) }} taps</div>
                        @endforeach
                        @if ($stats['conversions'])<div>Conversions: {{ $stats['conversions'] }} · {{ AdCampaign::rupees($stats['conversion_value']) }}</div>@endif
                        @if ($campaign->qrCards->isNotEmpty())
                            <div class="mt-2">QR cards: {{ $stats['qr']['placed'] }} / {{ $stats['qr']['cards'] }} placed · {{ $stats['qr']['scans'] }} scans ·
                                <a href="{{ route('admin.advertisements.selfServe.qrCards', $campaign) }}" target="_blank">print &amp; assign cabs</a></div>
                        @endif
                        @foreach ($campaign->pushSends as $push)
                            <div class="mt-2">Push on {{ $push->send_on->format('d-m-Y') }}: <strong>{{ $push->status }}</strong>
                                @if ($push->sent_at) · {{ $push->recipients }} customers, {{ $push->delivered }} delivered @endif
                                @if ($push->error)<span class="text-danger">· {{ $push->error }}</span>@endif
                                @if ($push->status === 'scheduled' && $campaign->status === AdCampaign::APPROVED)
                                    <form method="POST" action="{{ route('admin.advertisements.selfServe.push.send', $push) }}" class="d-inline" onsubmit="return confirm('Send this push now to opted-in customers?')">@csrf<button class="btn btn-sm btn-link p-0">Send now</button></form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (in_array($campaign->status, [AdCampaign::APPROVED, AdCampaign::PAUSED]))
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.advertisements.selfServe.pause', $campaign) }}">
                            @csrf
                            <input class="form-control form-control-sm mb-2" name="note" placeholder="Reason (optional)">
                            <button class="btn btn-sm {{ $campaign->status === AdCampaign::APPROVED ? 'btn-warning' : 'btn-success' }}">{{ $campaign->status === AdCampaign::APPROVED ? 'Pause ad' : 'Resume ad' }}</button>
                        </form>
                    </div>
                </div>
            @endif

            @if ($campaign->isPaid() && in_array($campaign->status, [AdCampaign::IN_REVIEW, AdCampaign::CHANGES_REQUESTED, AdCampaign::APPROVED, AdCampaign::PAUSED]))
                @php
                    $today = now('Asia/Kolkata')->startOfDay();
                    $used = $campaign->start_date->lte($today) ? min($campaign->days, (int) $campaign->start_date->diffInDays($today, true)) : 0;
                    $suggested = ((int) $campaign->total_amount - (int) $campaign->refund_amount) * max(0, $campaign->days - $used) / max(1, $campaign->days);
                @endphp
                <div class="card border-danger">
                    <div class="card-header"><h5 class="mb-0 text-danger">Cancel &amp; refund</h5></div>
                    <form method="POST" action="{{ route('admin.advertisements.selfServe.cancel', $campaign) }}" class="card-body" onsubmit="return confirm('Cancel this ad and refund the amount entered?')">
                        @csrf
                        <p class="small text-muted">Before the start date: full refund. After it starts: pro rata for unused days ({{ $used }} of {{ $campaign->days }} used). OfinIT's split is reversed in proportion and a credit note is issued.</p>
                        <label class="form-label small">Refund (₹)</label>
                        <input class="form-control form-control-sm mb-2" name="refund" type="number" step="0.01" min="0" value="{{ number_format($suggested / 100, 2, '.', '') }}">
                        <input class="form-control form-control-sm mb-2" name="note" placeholder="Reason (shown to the advertiser)" required>
                        <button class="btn btn-sm btn-danger">Cancel ad</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
