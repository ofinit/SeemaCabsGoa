@extends('layouts.main')
@section('title', 'Business Profiles')
@php
    $titles = [
        'supplier' => ['Seema Holidays', 'Supplies rides & packages to customers. Issues receipt vouchers, tax invoices and refund vouchers to customers.'],
        'platform' => ['OfinIT Solutions Pvt. Ltd.', 'Provides the platform. Issues the monthly platform-fee tax invoice to Seema Holidays.'],
    ];
    $fields = [
        'legal_name' => ['Legal name (as on GST registration)', 'text', true],
        'trade_name' => ['Trade name', 'text', false],
        'gstin' => ['GSTIN', 'text', false],
        'pan' => ['PAN', 'text', false],
        'address_line_one' => ['Address line 1', 'text', true],
        'address_line_two' => ['Address line 2', 'text', false],
        'city' => ['City', 'text', false],
        'pincode' => ['PIN code', 'text', false],
        'state_name' => ['State', 'text', true],
        'state_code' => ['State code (GST)', 'text', true],
        'email' => ['Email', 'email', false],
        'phone' => ['Phone', 'text', false],
        'signatory_name' => ['Authorised signatory', 'text', false],
        'invoice_prefix' => ['Invoice number prefix', 'text', true],
    ];
@endphp
@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center gy-3">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Settings</li>
                        <li class="breadcrumb-item" aria-current="page">Business Profiles</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title"><h2 class="mb-0">Business Profiles</h2></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">@include('layouts.message')</div>

        @foreach ($profiles as $role => $profile)
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-1">{{ $titles[$role][0] }}
                            @if ($profile->isReadyForInvoicing())
                                <span class="badge bg-light-success ms-1">Ready to invoice</span>
                            @else
                                <span class="badge bg-light-danger ms-1">Incomplete</span>
                            @endif
                        </h5>
                        <p class="text-muted small mb-0">{{ $titles[$role][1] }}</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.setting.saveBusinessProfile', $role) }}">
                            @csrf
                            <div class="row">
                                @foreach ($fields as $field => [$label, $type, $required])
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="{{ $role }}_{{ $field }}">{{ $label }}{!! $required ? ' <span class="text-danger">*</span>' : '' !!}</label>
                                        <input type="{{ $type }}" class="form-control" id="{{ $role }}_{{ $field }}" name="{{ $field }}"
                                            value="{{ old('_role') === $role ? old($field) : $profile->{$field} }}" {{ $required ? 'required' : '' }}
                                            @if ($field === 'gstin') maxlength="15" style="text-transform:uppercase" @endif
                                            @if ($field === 'state_code') maxlength="2" inputmode="numeric" @endif>
                                    </div>
                                @endforeach
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="{{ $role }}_bank_details">Bank details (printed on {{ $role === 'platform' ? 'platform-fee invoices' : 'invoices' }})</label>
                                    <textarea class="form-control" id="{{ $role }}_bank_details" name="bank_details" rows="2">{{ old('_role') === $role ? old('bank_details') : $profile->bank_details }}</textarea>
                                </div>
                            </div>
                            <input type="hidden" name="_role" value="{{ $role }}">
                            @if (!$profile->isReadyForInvoicing())
                                <p class="small text-danger">Needed before invoices can be issued: {{ implode(', ', $profile->missingForInvoicing()) }}.</p>
                            @endif
                            <button type="submit" class="btn btn-primary">Save {{ $titles[$role][0] }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Recent changes</h5></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tbody>
                            @forelse ($history as $change)
                                <tr>
                                    <td class="small" style="width:170px">{{ optional($change->created_at)->timezone('Asia/Kolkata')->format('d M Y H:i') }}</td>
                                    <td class="small">{{ $change->key }}</td>
                                    <td class="small">{{ $change->old_value ?? '—' }} → <strong>{{ $change->new_value ?? '—' }}</strong></td>
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
