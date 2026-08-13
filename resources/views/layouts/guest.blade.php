<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Klinik Terapi & Sekolah Khusus Anak Mandiri') }} — @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_am.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fade-in-up 0.35s ease-out; }
        @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .animate-spin-slow { animation: spin-slow 2s linear infinite; }
        @keyframes dollar-bounce {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.15); opacity: 0.8; }
        }
        .animate-dollar-bounce { animation: dollar-bounce 1.2s ease-in-out infinite; }
    </style>
    <style>#page-loader{transition:opacity .3s ease,display 0s .3s}#page-loader.hide{opacity:0;pointer-events:none;display:none}</style>
</head>
<body class="min-h-screen glass-bg relative overflow-hidden">

    <!-- Page loading overlay — render sync at top of body -->
    <div id="page-loader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center
                                bg-white/80 backdrop-blur-md">
        <div class="flex flex-col items-center gap-4">
            <div class="relative w-20 h-20 flex items-center justify-center">
                <div class="absolute inset-0 rounded-full bg-emerald-400/20 animate-dollar-bounce"></div>
                <div class="absolute inset-2 rounded-full bg-emerald-400/30 animate-dollar-bounce" style="animation-delay:.15s"></div>
                <div class="relative w-14 h-14 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-400/40">
                    <svg class="w-8 h-8 text-white animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-semibold text-gray-500 tracking-wide">Memuat halaman…</p>
        </div>
    </div>
    <script>
      (function(){
        var l=document.getElementById('page-loader');
        if(!l)return;
        // Always hide after a very short delay
        setTimeout(function(){
          l.style.display='none';
        },100);
      })();
    </script>

    <!-- Floating orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="relative z-10 min-h-screen flex flex-col justify-center items-center px-4 py-12">
        <!-- Logo -->
        <div class="animate-fade-in-up mb-8 text-center">
            <a href="{{ url('/') }}" class="inline-block">
                <img src="{{ asset('images/logo_am.png') }}" alt="Logo" class="h-14 w-auto mx-auto mb-3">
                <p class="text-xs text-white/50 font-medium tracking-wide">Klinik Terapi & Sekolah Khusus Anak Mandiri</p>
            </a>
        </div>

        <!-- Card -->
        <div class="w-full sm:max-w-md animate-fade-in-up-delay">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <p class="mt-8 text-xs text-white/30 text-center">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</body>
</html>
