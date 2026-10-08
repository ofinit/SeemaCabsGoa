@extends('customer.layouts.app')

@php($withNav = false)
@php($hideDeveloperFooter = true)
@section('title', 'Forgot Password — Seema Cabs Goa')

@section('content')
<div class="min-h-screen px-6 pt-6 pb-10 flex flex-col justify-between" style="background: linear-gradient(180deg, #FBF7F1 0%, #FDE9D2 100%);">
    <div>
        @include('customer.components.topbar', ['back' => route('customer.login'), 'right' => ''])

        <div class="mt-8">
            <h1 class="text-2xl font-bold text-ink mb-1">Forgot Password?</h1>
            <p class="text-muted text-sm mb-8">Enter your registered email and we'll send you a one-time code to reset your password.</p>

            <div class="card" x-data="{
                email: '', loading: false, error: '',
                async submit() {
                    this.loading = true; this.error = '';
                    try {
                        const res = await apiFetch('{{ route('customer.forgot-password.submit') }}', { method: 'POST', body: { email: this.email } });
                        window.location.href = res.data.redirect;
                    } catch (e) {
                        this.error = e.message;
                        this.loading = false;
                    }
                }
            }">
                <form @submit.prevent="submit">
                    <label class="field-label">Email Id</label>
                    <input x-model="email" type="email" required class="field-input mb-3" placeholder="Enter your registered Email ID">
                    @include('customer.components.error-banner', ['model' => 'error'])
                    <button type="submit" class="btn-primary w-full" :disabled="loading">
                        <span x-show="!loading">Send OTP</span>
                        <span x-show="loading">Sending&hellip;</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @include('customer.components.developer-footer', ['wrapperClass' => 'mt-8 py-0'])
</div>
@endsection
