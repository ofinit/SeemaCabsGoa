{{--
    Shared inline error banner for action failures (form submit, payment,
    booking, etc), replacing a bare line of red text with a styled card +
    icon so a failure actually reads as an error, not a stray line of text.
    Usage: @include('customer.components.error-banner', ['model' => 'error'])
    `model` is the Alpine expression holding the error message (falsy = hidden).
--}}
@php($model = $model ?? 'error')
<div x-show="{{ $model }}" x-transition
     class="rounded-2xl bg-danger/10 border border-danger/20 px-4 py-3 flex items-start gap-2.5 mb-3" style="display:none">
    <svg class="w-5 h-5 text-danger shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10" />
        <path d="M12 7.5v5M12 16h.01" />
    </svg>
    <p class="text-sm text-danger leading-snug min-w-0" x-text="{{ $model }}"></p>
</div>
