@extends('customer.layouts.app')

@php($withNav = false)
@php($hideDeveloperFooter = true)
@section('title', 'Login — Seema Cabs Goa')

@section('content')
<div class="min-h-screen px-6 pt-14 pb-10 flex flex-col justify-between" style="background: linear-gradient(180deg, #FBF7F1 0%, #FDE9D2 100%);">
    <div>
        <div class="flex justify-center mb-8">
            <img src="{{ asset('app-icons/logo-wordmark.png') }}" alt="Seema Cabs Goa" class="w-40">
        </div>

        <h1 class="text-2xl font-bold text-ink mb-1">Welcome</h1>
        <p class="text-muted text-sm mb-6">Login and discover Goa by Seema Cabs</p>

        <div class="mb-5">
            <button type="button" onclick="triggerGoogleSignIn('{{ $googleClientId }}')"
                    class="btn-pill w-full bg-[#518EF8] text-white">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#fff" d="M21.35 11.1h-9.17v2.92h5.27c-.23 1.4-1.6 4.1-5.27 4.1-3.17 0-5.76-2.62-5.76-5.85s2.59-5.85 5.76-5.85c1.8 0 3.01.77 3.7 1.43l2.52-2.43C16.9 3.7 14.6 2.7 12.18 2.7 6.99 2.7 2.8 6.9 2.8 12.17s4.2 9.47 9.38 9.47c5.42 0 9-3.8 9-9.15 0-.62-.07-1.1-.16-1.4z"/></svg>
                Google Login
            </button>
        </div>

        <div class="flex items-center gap-3 mb-5">
            <div class="h-px bg-gray-200 flex-1"></div>
            <span class="text-xs text-muted">OR</span>
            <div class="h-px bg-gray-200 flex-1"></div>
        </div>

        <div class="card" x-data="{
            email: '', password: '', loading: false, error: '',
            async submit() {
                this.loading = true; this.error = '';
                try {
                    const res = await apiFetch('{{ route('customer.login.submit') }}', { method: 'POST', body: { email: this.email, password: this.password } });
                    window.location.href = res.data.redirect;
                } catch (e) {
                    this.error = e.message;
                    this.loading = false;
                }
            }
        }">
            <form @submit.prevent="submit">
                <label class="field-label">Email Id or Contact Number</label>
                <p class="text-[11px] text-muted -mt-1 mb-2">Please sign in with your email address.</p>
                <input x-model="email" type="text" required class="field-input mb-4" placeholder="Enter Email ID or Contact Number">

                <label class="field-label">Password</label>
                <input x-model="password" type="password" required class="field-input mb-2" placeholder="Enter Password">

                <div class="text-right mb-4">
                    <a href="{{ route('customer.forgot-password') }}" class="text-xs font-semibold text-[#F36822]">Forgot Password?</a>
                </div>

                @include('customer.components.error-banner', ['model' => 'error'])

                <button type="submit" class="btn-primary w-full" :disabled="loading">
                    <span x-show="!loading">Login</span>
                    <span x-show="loading">Logging in&hellip;</span>
                </button>

                <p class="text-center text-sm text-muted mt-4">
                    Don't have an Account?
                    <a href="{{ route('customer.signup') }}" class="font-semibold text-[#F36822]">Sign Up</a>
                </p>
            </form>
        </div>
    </div>

    @include('customer.components.developer-footer', ['wrapperClass' => 'mt-8 py-0'])
</div>
@endsection
