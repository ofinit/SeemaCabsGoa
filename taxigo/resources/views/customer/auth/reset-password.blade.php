@extends('customer.layouts.app')

@php($withNav = false)
@php($hideDeveloperFooter = true)
@section('title', 'New Password — Seema Cabs Goa')

@section('content')
<div class="min-h-screen px-6 pt-6 pb-10 flex flex-col justify-between" style="background: linear-gradient(180deg, #FBF7F1 0%, #FDE9D2 100%);">
    <div>
        @include('customer.components.topbar', ['back' => route('customer.login'), 'right' => ''])

        <div class="mt-8">
            <h1 class="text-2xl font-bold text-ink mb-1">Set New Password</h1>
            <p class="text-muted text-sm mb-8">Choose a new password for <span class="font-semibold text-ink">{{ $email }}</span>.</p>

            <div class="card" x-data="{
                email: '{{ $email }}', password: '', confirm: '', loading: false, error: '',
                async submit() {
                    if (this.password !== this.confirm) { this.error = 'Passwords do not match.'; return; }
                    this.loading = true; this.error = '';
                    try {
                        const res = await apiFetch('{{ route('customer.forgot-password.reset.submit') }}', { method: 'POST', body: { email: this.email, password: this.password } });
                        window.location.href = res.data.redirect;
                    } catch (e) {
                        this.error = e.message;
                        this.loading = false;
                    }
                }
            }">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="field-label">New Password</label>
                        <input x-model="password" type="password" minlength="8" required class="field-input" placeholder="Enter new password">
                    </div>
                    <div>
                        <label class="field-label">Confirm Password</label>
                        <input x-model="confirm" type="password" minlength="8" required class="field-input" placeholder="Re-enter new password">
                    </div>
                    @include('customer.components.error-banner', ['model' => 'error'])
                    <button type="submit" class="btn-primary w-full" :disabled="loading">
                        <span x-show="!loading">Update Password</span>
                        <span x-show="loading">Updating&hellip;</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @include('customer.components.developer-footer', ['wrapperClass' => 'mt-8 py-0'])
</div>
@endsection
