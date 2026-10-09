@extends('layouts.main')

@section('title', 'Dashboard')

@php
    $fmt = fn($n) => '₹' . number_format((float) $n, 2);
@endphp

@section('content')
<style>
    .fin-section-title{font-size:12px;font-weight:700;letter-spacing:.06em;color:#6b7280;text-transform:uppercase;margin:0 0 14px}
    .fin-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:32px}
    @media (max-width:1200px){.fin-grid{grid-template-columns:repeat(2,1fr)}}
    @media (max-width:600px){.fin-grid{grid-template-columns:1fr}}
    .fin-card{border-radius:14px;padding:20px 22px;color:#fff;box-shadow:0 4px 14px rgba(0,0,0,.08)}
    .fin-card .fin-label{font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;opacity:.85;margin:0 0 10px;display:flex;align-items:center;justify-content:space-between;gap:8px}
    .fin-card .fin-value{font-size:26px;font-weight:700;line-height:1.1;margin:0 0 6px}
    .fin-card .fin-sub{font-size:12px;opacity:.8;margin:0}
    .fin-card .fin-icon{font-size:20px;opacity:.85}
    .fin-c1{background:linear-gradient(135deg,#2f6fed,#2148c9)}
    .fin-c2{background:linear-gradient(135deg,#3730a3,#1e1b6e)}
    .fin-c3{background:linear-gradient(135deg,#8b3cf0,#5b1fa8)}
    .fin-c4{background:linear-gradient(135deg,#0d9c8f,#0a6e66)}
    .fin-c5{background:linear-gradient(135deg,#22a047,#146e30)}
    .fin-c6{background:linear-gradient(135deg,#e13a3a,#a01f1f)}
    .fin-c7{background:linear-gradient(135deg,#37424e,#1a2027)}
    .fin-c8{background:linear-gradient(135deg,#5a3a26,#3a2417)}
    .fin-t1{background:linear-gradient(135deg,#2f6fed,#1d4bb0)}
    .fin-t2{background:linear-gradient(135deg,#0d8f6e,#0a6650)}
    .fin-t3{background:linear-gradient(135deg,#7c3aed,#521fa3)}
    .fin-t4{background:linear-gradient(135deg,#e2711d,#b0500f)}
    .ov-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:16px;margin-bottom:32px}
    @media (max-width:1200px){.ov-grid{grid-template-columns:repeat(3,1fr)}}
    @media (max-width:600px){.ov-grid{grid-template-columns:repeat(2,1fr)}}
    .ov-card{background:#fff;border-radius:14px;padding:16px 18px;box-shadow:0 2px 8px rgba(0,0,0,.06);display:flex;align-items:center;gap:12px}
    .ov-icon{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
    .ov-value{font-size:20px;font-weight:700;line-height:1.15;margin:0}
    .ov-label{font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;margin:0}
</style>

@if($isVendor)
    <p class="text-muted mb-3">Showing figures for your own fleet only.</p>
@endif

@if($platform)
    <p class="fin-section-title">Platform Overview</p>
    <div class="ov-grid">
        <div class="ov-card">
            <div class="ov-icon" style="background:#e8f0fe;color:#2148c9">👤</div>
            <div><p class="ov-value">{{ number_format($platform['total_customers']) }}</p><p class="ov-label">Customers</p></div>
        </div>
        <div class="ov-card">
            <div class="ov-icon" style="background:#eae6fb;color:#5b1fa8">🏢</div>
            <div><p class="ov-value">{{ number_format($platform['total_fleet_operators']) }}</p><p class="ov-label">Fleet Operators</p></div>
        </div>
        <div class="ov-card">
            <div class="ov-icon" style="background:#e3f6f2;color:#0a6e66">🧑‍✈️</div>
            <div><p class="ov-value">{{ number_format($platform['total_drivers']) }}</p><p class="ov-label">Drivers</p></div>
        </div>
        <div class="ov-card">
            <div class="ov-icon" style="background:#fdf0e3;color:#b0500f">🚕</div>
            <div><p class="ov-value">{{ number_format($platform['total_cabs']) }}</p><p class="ov-label">Cabs</p></div>
        </div>
        <div class="ov-card">
            <div class="ov-icon" style="background:#eef1f4;color:#37424e">📋</div>
            <div><p class="ov-value">{{ number_format($platform['total_bookings']) }}</p><p class="ov-label">Total Bookings</p></div>
        </div>
        <div class="ov-card">
            <div class="ov-icon" style="background:#fde8e8;color:#a01f1f">💰</div>
            <div><p class="ov-value">{{ $fmt($summary['seema_cabs_commission']) }}</p><p class="ov-label">Platform Profit</p></div>
        </div>
    </div>
@endif

<p class="fin-section-title">Financial Summary</p>
<div class="fin-grid">
    <div class="fin-card fin-c1">
        <p class="fin-label">Total Gross Amount</p>
        <p class="fin-value">{{ $fmt($summary['gross_amount']) }}</p>
        <p class="fin-sub">Total amount charged to customers</p>
    </div>
    <div class="fin-card fin-c2">
        <p class="fin-label">Base Fare (Cab Rates)</p>
        <p class="fin-value">{{ $fmt($summary['base_fare']) }}</p>
        <p class="fin-sub">All trip types, before tax &amp; surge</p>
    </div>
    <div class="fin-card fin-c3">
        <p class="fin-label">Surge Pricing</p>
        <p class="fin-value">{{ $fmt($summary['surge_pricing']) }}</p>
        <p class="fin-sub">{{ $summary['surge_pricing'] > 0 ? 'Surge charges collected' : 'No surge bookings recorded yet' }}</p>
    </div>
    <div class="fin-card fin-c4">
        <p class="fin-label">Fare Markup</p>
        <p class="fin-value">{{ $fmt($summary['other_charges']) }}</p>
        <p class="fin-sub">Internal markup in fares &middot; GST {{ $fmt($summary['gst_collected'] ?? 0) }}</p>
    </div>

    <div class="fin-card fin-c5">
        <p class="fin-label">Collected Online (PG)</p>
        <p class="fin-value">{{ $fmt($summary['collected_online']) }}</p>
        <p class="fin-sub">Amount paid online via payment gateway</p>
    </div>
    <div class="fin-card fin-c6">
        <p class="fin-label">Seema Cabs Commission</p>
        <p class="fin-value">{{ $fmt($summary['seema_cabs_commission']) }}</p>
        <p class="fin-sub">Aggregator's share of PG collection</p>
    </div>
    <div class="fin-card fin-c7">
        <p class="fin-label">Platform Commission</p>
        <p class="fin-value">{{ $fmt($summary['platform_commission']) }}</p>
        <p class="fin-sub">Platform's share of PG collection</p>
    </div>
    <div class="fin-card fin-c8">
        <p class="fin-label">Cash to Drivers</p>
        <p class="fin-value">{{ $fmt($summary['cash_to_drivers']) }}</p>
        <p class="fin-sub">Balance paid directly in cash by customer</p>
    </div>
</div>

<p class="fin-section-title">Revenue by Trip Type</p>
<div class="fin-grid">
    <div class="fin-card fin-t1">
        <p class="fin-label">Airport Pickup <span class="fin-icon">✈️</span></p>
        <p class="fin-value">{{ $fmt($summary['by_trip_type']['airport_pickup']['amount']) }}</p>
        <p class="fin-sub">{{ $summary['by_trip_type']['airport_pickup']['count'] }} bookings</p>
    </div>
    <div class="fin-card fin-t2">
        <p class="fin-label">Airport Drop <span class="fin-icon">🛬</span></p>
        <p class="fin-value">{{ $fmt($summary['by_trip_type']['airport_drop']['amount']) }}</p>
        <p class="fin-sub">{{ $summary['by_trip_type']['airport_drop']['count'] }} bookings</p>
    </div>
    <div class="fin-card fin-t3">
        <p class="fin-label">In-City Rides <span class="fin-icon">🗺️</span></p>
        <p class="fin-value">{{ $fmt($summary['by_trip_type']['in_city']['amount']) }}</p>
        <p class="fin-sub">{{ $summary['by_trip_type']['in_city']['count'] }} bookings</p>
    </div>
    <div class="fin-card fin-t4">
        <p class="fin-label">Sightseeing / Other <span class="fin-icon">📖</span></p>
        <p class="fin-value">{{ $fmt($summary['by_trip_type']['sightseeing']['amount']) }}</p>
        <p class="fin-sub">{{ $summary['by_trip_type']['sightseeing']['count'] }} bookings</p>
    </div>
</div>
@endsection

@section('scripts')
    <script type="module">
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/11.10.0/firebase-app.js";
        import {
            getAnalytics
        } from "https://www.gstatic.com/firebasejs/11.10.0/firebase-analytics.js";
        import {
            getMessaging,
            getToken
        } from "https://www.gstatic.com/firebasejs/11.10.0/firebase-messaging.js";

        const firebaseConfig = {
            apiKey: "AIzaSyAcYLyOnmoNTBMcngWjsL6VunHWgO8BC-k",
            authDomain: "seema-cabs-goa.firebaseapp.com",
            projectId: "seema-cabs-goa",
            messagingSenderId: "465925738585",
            appId: "1:465925738585:web:ec57383822d2c8d0eaa148",
        };

        const app = initializeApp(firebaseConfig);
        const analytics = getAnalytics(app);
        const messaging = getMessaging(app);

        const serviceWorkerPath = "{{ asset('/firebase-messaging-sw.js') }}";
        navigator.serviceWorker.register(serviceWorkerPath)
            .then((registration) => {
                console.log('Service Worker registered with scope:', registration.scope);

                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        getToken(messaging, {
                                vapidKey: "BPpiuvkWhmE5ArCl3mObZGHa6W8SIghP5oj4N27uqS-Y_WduhKrW9L7klPRuHMkBbG5NdbQi-4hNEobpwG09XQ8",
                                serviceWorkerRegistration: registration
                            })
                            .then((token) => {
                                if (token) {
                                    console.log('FCM Token:', token);
                                    fetch('{{ route('admin.storeToken') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                token: token
                                            })
                                        })
                                        .then(res => res.json())
                                        .then(data => console.log('Token saved:', data))
                                        .catch(err => console.error('Error saving token:', err));
                                } else {
                                    console.log('No registration token available.');
                                }
                            })
                            .catch(err => {
                                console.error('Error retrieving token:', err);
                            });
                    } else {
                        console.log('Notification permission denied.');
                    }
                });
            })
            .catch((err) => {
                console.error('Service Worker registration failed:', err);
            });
    </script>
@endsection
