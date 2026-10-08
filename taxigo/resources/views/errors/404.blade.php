<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#F6C018">
    <title>404 - Route Not Found | Seema Cabs Goa</title>

    <link rel="manifest" href="{{ asset('build/manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('app-icons/apple-touch-icon.png') }}">
    <link rel="icon" href="{{ asset('app-icons/icon-192.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500&display=swap" rel="stylesheet">

    @vite(['resources/css/customer.css', 'resources/js/customer.js'])

    <style>
        /* Standalone fallback styles ensuring premium visuals even before CSS bundle hydrates */
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #F8F6F0;
            color: #0E1015;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }
        .pulse-emerald {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.15); }
        }
    </style>
</head>
<body class="bg-cream text-ink font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-gold selection:text-ink">

    <!-- Mobile-First App Shell -->
    <div class="app-shell flex flex-col min-h-screen px-4 py-5 max-w-[480px] mx-auto w-full justify-between">
        
        <!-- Top App Bar / Header -->
        <header class="flex items-center justify-between gap-3 pt-1 pb-3">
            <a href="{{ url('/app/home') }}" class="inline-flex items-center gap-2 group">
                <img src="{{ asset('app-icons/logo-wordmark.png') }}" 
                     alt="Seema Cabs Goa" 
                     class="h-9 w-auto object-contain transition-transform group-hover:scale-105"
                     onerror="this.onerror=null; this.src='{{ asset('app-icons/icon-192.png') }}'; this.className='w-9 h-9 rounded-xl shadow-sm';">
            </a>

            <!-- 24/7 Live Operator Badge -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 pulse-emerald"></span>
                <span>Operator Online</span>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="my-auto py-4 flex flex-col items-center text-center">
            
            <!-- Doppelrand / Visual Bezel Card -->
            <div class="w-full card-bezel mb-6 shadow-sm">
                <div class="card-core flex flex-col items-center p-6 relative overflow-hidden bg-gradient-to-b from-amber-50/70 via-white to-white">
                    
                    <!-- Decorative subtle road horizon background -->
                    <div class="absolute -top-12 -right-12 w-36 h-36 bg-gold/15 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-amber-400/10 rounded-full blur-xl pointer-events-none"></div>

                    <!-- 404 Status Chip -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-100/90 text-amber-900 border border-amber-300/60 text-xs font-bold uppercase tracking-wider mb-4 shadow-xs">
                        <svg class="w-3.5 h-3.5 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <span>Error 404 &bull; Route Missing</span>
                    </div>

                    <!-- Cab Graphics & Illustration -->
                    <div class="relative w-44 h-44 sm:w-48 sm:h-48 flex items-center justify-center my-1">
                        <div class="absolute inset-0 bg-radial from-amber-200/40 via-amber-100/10 to-transparent rounded-full filter blur-xl pointer-events-none scale-105"></div>
                        <img src="{{ asset('cabs/taxi-doodle-404.png') }}" 
                             alt="Seema Cab Taxi" 
                             class="relative z-10 w-full h-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105"
                             onerror="this.onerror=null; this.src='{{ asset('cabs/taxi-404.svg') }}';">
                    </div>

                    <!-- Big 404 Numerals -->
                    <h2 class="text-4xl font-extrabold text-ink tracking-tight mt-1 mb-1">
                        4<span class="text-gold">0</span>4
                    </h2>

                    <!-- Title & Copy -->
                    <h3 class="text-lg font-bold text-ink mb-1.5">
                        Looks Like You Took a Wrong Turn
                    </h3>
                    <p class="text-xs text-muted leading-relaxed max-w-[320px]">
                        The page or ride path you are searching for is not mapped or has moved. Need an immediate cab or assistance in Goa? Our operator is standing by.
                    </p>
                </div>
            </div>

            <!-- Operator Action Card (WhatsApp & Phone) -->
            <div class="w-full bg-white rounded-card-inner border border-black/[0.06] p-4 mb-4 shadow-card text-left">
                <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-black/[0.04]">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base">
                            <svg class="w-5 h-5 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.761.88 2.791.88h.001c3.181 0 5.767-2.586 5.768-5.766 0-3.18-2.586-5.766-5.769-5.766zm9.969 5.766c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.242L2 22l1.507-5.076C2.651 15.448 2 13.799 2 11.938 2 6.42 6.482 1.938 12 1.938s10 4.482 10 10z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-ink">Goa Central Dispatch</div>
                            <div class="text-[11px] text-muted">Direct Fleet & Booking Assistance</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            Available 24x7
                        </span>
                    </div>
                </div>

                <!-- Primary WhatsApp Button (as requested by user) -->
                <a href="https://wa.me/919822385180?text=Hi%20Seema%20Cabs%2C%20I%20am%20facing%20an%20issue%20or%20need%20assistance%20with%20booking%20a%20taxi%20in%20Goa." 
                   target="_blank" 
                   rel="noopener noreferrer"
                   id="btn-whatsapp-operator"
                   class="w-full flex items-center justify-center gap-3 bg-[#25D366] hover:bg-[#20ba59] active:scale-[0.98] text-white font-bold py-3.5 px-5 rounded-xl shadow-[0_4px_16px_rgba(37,211,102,0.35)] transition-all duration-200 mb-2.5">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Contact Operator on WhatsApp</span>
                </a>

                <!-- Quick Direct Call Button -->
                <a href="tel:+919822385180" 
                   id="btn-call-operator"
                   class="w-full flex items-center justify-center gap-2.5 bg-black/[0.04] hover:bg-black/[0.08] active:scale-[0.98] text-ink font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors">
                    <svg class="w-4 h-4 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    <span>Call Operator directly: +91 9822385180</span>
                </a>
            </div>

            <!-- Return Home / Navigation Options -->
            <div class="w-full flex flex-col gap-2.5">
                <a href="{{ url('/app/home') }}" 
                   id="btn-return-home"
                   class="w-full btn-primary py-3.5 text-center text-sm font-bold flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span>Return to App Home</span>
                </a>

                <a href="javascript:if(window.history.length>1){window.history.back();}else{window.location.href='{{ url('/app/home') }}';}" 
                   id="btn-go-back"
                   class="w-full btn-outline py-3 text-center text-xs font-semibold flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 12H5M11 18l-6-6 6-6"/>
                    </svg>
                    <span>Go Back to Previous Page</span>
                </a>
            </div>

            <!-- Quick Service Links -->
            <div class="mt-5 pt-4 border-t border-black/[0.06] w-full">
                <div class="text-[11px] font-bold uppercase tracking-wider text-muted mb-2.5">
                    Popular Cab Services
                </div>
                <div class="flex items-center justify-center gap-2 flex-wrap">
                    <a href="{{ url('/app/home') }}" class="px-3 py-1 rounded-full bg-white border border-black/[0.06] text-xs font-medium text-ink hover:border-gold transition-colors">
                        ✈️ Airport Transfer
                    </a>
                    <a href="{{ url('/app/home') }}" class="px-3 py-1 rounded-full bg-white border border-black/[0.06] text-xs font-medium text-ink hover:border-gold transition-colors">
                        🏖️ Sightseeing Tours
                    </a>
                    <a href="{{ url('/app/home') }}" class="px-3 py-1 rounded-full bg-white border border-black/[0.06] text-xs font-medium text-ink hover:border-gold transition-colors">
                        🚖 Point-to-Point
                    </a>
                </div>
            </div>

        </main>

        <!-- Footer Note -->
        <footer class="text-center pt-3 pb-1">
            <p class="text-[11px] text-muted">
                Seema Cabs Goa &bull; Verified Drivers &bull; Transparent Pricing
            </p>
            @include('customer.components.developer-footer', ['wrapperClass' => 'py-2 px-0'])
        </footer>

    </div>

</body>
</html>
