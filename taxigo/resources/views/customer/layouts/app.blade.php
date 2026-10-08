<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#F6C018">
    <title>@yield('title', 'Seema Cabs Goa')</title>

    <link rel="manifest" href="{{ asset('build/manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('app-icons/apple-touch-icon.png') }}">
    <link rel="icon" href="{{ asset('app-icons/icon-192.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500&display=swap" rel="stylesheet">

    @vite(['resources/css/customer.css', 'resources/js/customer.js'])
    @stack('head')
</head>
<body class="bg-cream text-ink font-sans antialiased min-h-screen">
    <div class="app-shell flex flex-col min-h-screen {{ ($withNav ?? true) ? 'pb-28' : 'pb-4' }}">

        <main class="flex-1">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        @if(!($hideDeveloperFooter ?? false))
            @include('customer.components.developer-footer')
        @endif

        @if($withNav ?? true)
            @include('customer.components.bottom-nav')
        @endif

        @include('customer.components.toast-host')
    </div>

    @stack('scripts')
</body>
</html>
