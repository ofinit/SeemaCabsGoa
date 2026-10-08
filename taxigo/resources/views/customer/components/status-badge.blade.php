@php
    // $status: assigned | confirmed | cancelled | refunded | completed
    $labels = [
        'assigned' => 'Driver Assigned',
        'confirmed' => 'Booking Confirmed',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
        'completed' => 'Completed',
    ];
    $label = $labels[$status] ?? ucfirst($status);
@endphp
<span class="status-badge status-badge--{{ $status }}">{{ $label }}</span>
