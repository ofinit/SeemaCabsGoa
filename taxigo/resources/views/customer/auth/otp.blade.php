@extends('customer.layouts.app')

@php($withNav = false)
@php($hideDeveloperFooter = true)
@section('title', 'Verify OTP — Seema Cabs Goa')

@section('content')
<div class="min-h-screen px-6 pt-6 pb-10 flex flex-col justify-between" style="background: linear-gradient(180deg, #FBF7F1 0%, #FDE9D2 100%);">
    <div>
        @include('customer.components.topbar', ['back' => route('customer.forgot-password'), 'right' => ''])

        <div class="mt-8">
            <h1 class="text-2xl font-bold text-ink mb-1">Verify OTP</h1>
            <p class="text-muted text-sm mb-8">Enter the 4-digit code we emailed to <span class="font-semibold text-ink">{{ $email }}</span>.</p>

            <div class="card" x-data="{
                email: '{{ $email }}', otp: '', loading: false, error: '',
                async submit() {
                    this.loading = true; this.error = '';
                    try {
                        const res = await apiFetch('{{ route('customer.forgot-password.otp.submit') }}', { method: 'POST', body: { email: this.email, otp: this.otp } });
                        window.location.href = res.data.redirect;
                    } catch (e) {
                        this.error = e.message;
                        this.loading = false;
                    }
                }
            }">
                <form @submit.prevent="submit">
                    <label class="field-label">OTP</label>
                    <input x-model="otp" type="text" inputmode="numeric" maxlength="4" required class="field-input mb-3 text-center tracking-[0.5em] text-lg" placeholder="0000">
                    @include('customer.components.error-banner', ['model' => 'error'])
                    <button type="submit" class="btn-primary w-full" :disabled="loading">
                        <span x-show="!loading">Verify OTP</span>
                        <span x-show="loading">Verifying&hellip;</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @include('customer.components.developer-footer', ['wrapperClass' => 'mt-8 py-0'])
</div>
@endsection
