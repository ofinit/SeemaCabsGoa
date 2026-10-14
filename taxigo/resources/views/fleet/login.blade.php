@extends('fleet.layout')
@section('title', 'Log in — Seema Cabs Fleet')

@push('head')
<style>
    .login { min-height:100vh; display:flex; flex-direction:column; justify-content:center; padding:24px 20px calc(24px + env(safe-area-inset-bottom)); max-width:420px; margin:0 auto; }
    .logo { text-align:center; margin-bottom:22px; }
    .logo img { width:170px; height:auto; }
    .tm { position:relative; display:inline-block; }
    .tm span { position:absolute; top:0; right:-0.9em; font-size:13px; font-weight:700; }
    h2 { margin:0 0 4px; font-size:24px; font-weight:800; }
    label { display:block; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:var(--muted); margin:16px 0 6px; }
    input { width:100%; padding:14px 16px; border:1px solid var(--line); border-radius:12px; font-size:16px; background:#fff; }
    input:focus { outline:2px solid var(--gold); border-color:transparent; }
    .err { background:var(--red-soft); color:var(--red); border-radius:12px; padding:10px 14px; font-size:14px; margin-top:16px; font-weight:600; }
</style>
@endpush

@section('body')
<div class="login">
    <div class="logo"><span class="tm"><img src="{{ asset('app-icons/logo-wordmark.png') }}" alt="Seema Cabs Goa"><span>&trade;</span></span></div>
    <h2>Fleet</h2>
    <p class="muted" style="margin:0">New bookings, trip details and status for Seema Cabs.</p>

    @if (session('error'))<div class="err">{{ session('error') }}</div>@endif
    @if ($errors->any())<div class="err">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('fleet.doLogin') }}" style="margin-top:8px">
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" inputmode="email" autocomplete="username" value="{{ old('email') }}" required>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <button class="btn btn-gold" style="width:100%; margin-top:22px; padding:15px">Log in</button>
    </form>
    <p class="muted small" style="text-align:center; margin-top:18px">Use the Seema Cabs fleet operator login.</p>
</div>
@endsection
