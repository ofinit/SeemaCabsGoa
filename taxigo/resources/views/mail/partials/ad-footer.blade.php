{{-- P10 booking-email footer: one live ad with a tracked link (clicks counted; no tracking pixel). --}}
@php
    $mailAd = \App\Services\Ads\AdServer::forScreen(\App\Services\Ads\AdServer::SCREEN_EMAIL, 5)->shuffle()->first();
    $mailAdUrl = $mailAd ? \App\Services\Ads\AdServer::clickUrl($mailAd, \App\Services\Ads\AdServer::SCREEN_EMAIL, 'email') : null;
@endphp
@if($mailAd && $mailAdUrl)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:16px auto 0;">
        <tr><td style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;padding:0 0 4px;">Sponsored</td></tr>
        <tr><td><a href="{{ $mailAdUrl }}" target="_blank" rel="noopener sponsored"><img src="{{ $mailAd->add_banner_image }}" width="560" alt="Advertisement" style="display:block;width:100%;max-width:560px;height:auto;border:0;border-radius:8px;"></a></td></tr>
    </table>
@endif
