<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#F6C21C">
    <title>@yield('title', 'Seema Cabs Fleet')</title>
    <link rel="icon" href="{{ asset('app-icons/logo-wordmark.png') }}">
    <style>
        :root { --gold:#F6C21C; --gold-soft:#FFF6D6; --ink:#1c1c1e; --muted:#6b7280; --line:#ececec; --bg:#f6f5f2; --card:#fff;
                --green:#0f9d58; --green-soft:#e6f6ee; --blue:#1d6fd8; --blue-soft:#e8f0fd; --red:#d93025; --red-soft:#fdecea; --grey-soft:#f1f1f1; }
        * { box-sizing:border-box; -webkit-tap-highlight-color:transparent; }
        html, body { margin:0; background:var(--bg); color:var(--ink); font:15px/1.45 system-ui,-apple-system,"Segoe UI",Roboto,sans-serif; }
        a { color:inherit; text-decoration:none; }
        .wrap { max-width:640px; margin:0 auto; padding:0 16px calc(24px + env(safe-area-inset-bottom)); }
        .bar { position:sticky; top:0; z-index:10; background:var(--gold); padding:calc(10px + env(safe-area-inset-top)) 16px 10px; display:flex; align-items:center; gap:12px; }
        .bar h1 { font-size:18px; margin:0; flex:1; font-weight:800; }
        .bar .btn-icon { width:38px; height:38px; border-radius:12px; background:rgba(255,255,255,.55); border:0; display:grid; place-items:center; font-size:18px; color:var(--ink); }
        .card { background:var(--card); border-radius:16px; padding:14px 16px; box-shadow:0 1px 3px rgba(0,0,0,.06); }
        .pill { display:inline-block; font-size:12px; font-weight:700; padding:3px 10px; border-radius:999px; white-space:nowrap; }
        .pill.confirmed { background:var(--gold-soft); color:#8a6400; }
        .pill.assigned { background:var(--blue-soft); color:var(--blue); }
        .pill.completed { background:var(--green-soft); color:var(--green); }
        .pill.cancelled, .pill.no_show { background:var(--red-soft); color:var(--red); }
        .muted { color:var(--muted); }
        .small { font-size:13px; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; border:0; border-radius:12px; padding:12px 16px; font-weight:700; font-size:15px; cursor:pointer; }
        .btn-gold { background:var(--gold); color:var(--ink); }
        .btn-dark { background:var(--ink); color:#fff; }
        .btn-green { background:#25D366; color:#fff; }
        .btn-line { background:#fff; border:1px solid var(--line); color:var(--ink); }
        .banner { background:var(--red-soft); color:var(--red); border-radius:12px; padding:10px 14px; font-size:13px; font-weight:600; margin:12px 0 0; }
        .toast { position:fixed; left:16px; right:16px; top:calc(70px + env(safe-area-inset-top)); z-index:50; background:var(--ink); color:#fff; border-radius:14px; padding:12px 16px; box-shadow:0 8px 24px rgba(0,0,0,.25); display:none; font-weight:600; }
        .toast.show { display:block; }
    </style>
    @stack('head')
</head>
<body>
    @yield('body')
    <div class="toast" id="toast"></div>

    {{-- Inside the Android app: register for new-booking alerts and open the booking an alert was tapped on. --}}
    <script>
    (function () {
        const cap = window.Capacitor;
        window.fleetToast = function (text) {
            const t = document.getElementById('toast');
            t.textContent = text; t.classList.add('show');
            clearTimeout(window.__toastTimer); window.__toastTimer = setTimeout(() => t.classList.remove('show'), 5000);
        };
        if (!cap || !cap.isNativePlatform || !cap.isNativePlatform() || !cap.Plugins || !cap.Plugins.PushNotifications) return;
        const push = cap.Plugins.PushNotifications;
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const loggedIn = @json(\App\Http\Controllers\Fleet\FleetController::allowed(auth()->user()));

        push.removeAllListeners && push.removeAllListeners();
        push.addListener('pushNotificationActionPerformed', (action) => {
            const url = action && action.notification && action.notification.data && action.notification.data.url;
            if (url) window.location.href = url;
        });
        push.addListener('pushNotificationReceived', (n) => {
            window.fleetToast((n.title || 'New booking') + (n.body ? ' · ' + n.body.split('\n')[0] : ''));
            if (document.body.dataset.page === 'list') setTimeout(() => window.location.reload(), 1500);
        });
        if (!loggedIn) return;

        push.addListener('registration', (t) => {
            try { localStorage.setItem('fleetToken', t.value); } catch (e) {}
            fetch(@json(route('fleet.token')), { method: 'POST', credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ token: t.value, platform: 'android' }) }).catch(() => {});
        });
        push.addListener('registrationError', (e) => console.warn('Push registration failed', e));

        (async () => {
            try {
                if (push.createChannel) {
                    await push.createChannel({ id: @json(\App\Services\Fleet\FleetAlerts::CHANNEL), name: 'New bookings',
                        description: 'Alerts for new and cancelled bookings', importance: 5, visibility: 1,
                        sound: @json(\App\Services\Fleet\FleetAlerts::SOUND) + '.wav', vibration: true, lights: true, lightColor: '#F6C21C' });
                }
                let perm = await push.checkPermissions();
                if (perm.receive !== 'granted') perm = await push.requestPermissions();
                if (perm.receive === 'granted') await push.register();
            } catch (e) { console.warn('Push setup failed', e); }
        })();
    })();
    </script>
    @stack('scripts')
</body>
</html>
