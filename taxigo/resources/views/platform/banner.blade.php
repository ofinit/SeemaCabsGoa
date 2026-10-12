{{-- OfinIT billing banner in the admin panel (both logins): unpaid invoices / suspension. --}}
@php
    $billingDue = \App\Services\Platform\PlatformBilling::unpaid()->orderBy('due_date')->get();
    $billingSuspended = \App\Services\Platform\PlatformSettings::suspended();
@endphp
@if ($billingSuspended || $billingDue->isNotEmpty())
    @php
        $firstDue = $billingDue->first();
        $overdue = $firstDue && $firstDue->due_date && $firstDue->due_date->toDateString() < now('Asia/Kolkata')->toDateString();
    @endphp
    <div class="alert {{ $billingSuspended || $overdue ? 'alert-danger' : 'alert-warning' }} d-flex flex-wrap align-items-center gap-2">
        <div class="flex-grow-1">
            @if ($billingSuspended)
                <strong>Service suspended</strong> — the website and app are unavailable until the OfinIT invoice is paid.
            @else
                <strong>OfinIT invoice{{ $billingDue->count() > 1 ? 's' : '' }} due</strong>:
                ₹{{ number_format((float) $billingDue->sum('amount_due'), 2) }} by {{ optional($firstDue->due_date)->format('d M Y') }}.
                Unpaid invoices suspend the website and app the next day.
            @endif
        </div>
        @unless (request()->routeIs('admin.billing.*'))
            <a href="{{ route('admin.billing.index') }}" class="btn btn-sm {{ $billingSuspended || $overdue ? 'btn-danger' : 'btn-warning' }}">Pay now</a>
        @endunless
    </div>
@endif
