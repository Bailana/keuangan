<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mandiri') }} — @yield('title')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen glass-bg relative overflow-hidden">
    <!-- Floating orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="relative z-10 min-h-screen flex flex-col justify-center items-center px-4 py-12">
        <!-- Logo -->
        <div class="animate-fade-in-up mb-8 text-center">
            <a href="{{ url('/') }}" class="inline-block">
                <div class="flex items-center justify-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center backdrop-blur-md shadow-lg">
                        <svg class="w-7 h-7 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <span class="text-xl font-bold text-white tracking-tight">Mandiri</span>
                        <span class="block text-xs text-white/50 font-medium -mt-0.5">Klinik & Sekolah</span>
                    </div>
                </div>
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
