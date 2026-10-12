<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Temporarily unavailable — Seema Cabs Goa</title>
    <style>
        :root { --ink: #1a1e23; --muted: #6b7280; --gold: #FEDC33; --bg: #f6f6f4; --card: #ffffff; }
        @media (prefers-color-scheme: dark) { :root { --ink: #f3f4f6; --muted: #9ca3af; --bg: #111317; --card: #1b1e24; } }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--bg); color: var(--ink); font-family: Arial, Helvetica, sans-serif; padding: 16px; box-sizing: border-box; }
        .card { background: var(--card); border-radius: 20px; padding: 32px 24px; max-width: 420px; width: 100%; text-align: center; box-shadow: 0 4px 24px rgba(0,0,0,.06); }
        h1 { font-size: 22px; margin: 0 0 8px; }
        p { color: var(--muted); line-height: 1.5; margin: 0 0 16px; }
        .btn { display: block; background: var(--gold); color: #1a1e23; text-decoration: none; font-weight: bold; padding: 14px 20px; border-radius: 999px; margin-top: 10px; }
        .sos { display: block; border: 2px solid #dc2626; color: #dc2626; text-decoration: none; font-weight: bold; padding: 12px 20px; border-radius: 999px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Seema Cabs Goa is temporarily unavailable</h1>
        <p>We're not taking new bookings right now. Please check back later.</p>
        @if ($ride)
            <p><strong>You have a ride now.</strong> Your trip and SOS still work.</p>
            <a class="btn" href="{{ url('/app/trip/' . $ride->id) }}">Open my trip</a>
        @endif
        @foreach ($sos as $number)
            <a class="sos" href="tel:{{ $number }}">Emergency — call {{ $number }}</a>
        @endforeach
    </div>
</body>
</html>
