<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @yield('page-title', config('app.name', 'Klinik Terapi & Sekolah Khusus Anak Mandiri'))
        @if(view()->exists('layouts.app') && request()->routeIs('expenses.*')) | KEUANGAN Klinik Terapi & Sekolah Khusus Anak Mandiri @endif
    </title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_am.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        @keyframes fade-in-up { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fade-in-up 0.35s ease-out; }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
        @media (max-width: 639px) { input, select, textarea { font-size: 16px !important; } }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 overflow-x-hidden"
      x-data="{
          sidebarOpen: false,
          profileOpen: false,
          sidebarCollapsed: {{ Auth::check() && Auth::user()->sidebar_collapsed ? 'true' : 'false' }},
          async toggleSidebar() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              await fetch('{{ route('settings.toggleSidebar') }}', {
                  method: 'POST',
                  headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
              });
          },
          get isDesktop() {
              return window.innerWidth >= 1024;
          },
          get sidebarWidth() {
              return this.sidebarCollapsed ? '80px' : '18rem';
          },
          get contentMargin() {
              if (this.isDesktop) return this.sidebarWidth;
              return this.sidebarOpen ? '18rem' : '0px';
          },
          closeMobileSidebar() {
              if (window.innerWidth < 1024) this.sidebarOpen = false;
          },
          init() {
              const saved = localStorage.getItem('sidebar_collapsed');
              if (saved !== null) this.sidebarCollapsed = JSON.parse(saved);
              this.$watch('sidebarCollapsed', v => localStorage.setItem('sidebar_collapsed', JSON.stringify(v)));
              window.addEventListener('resize', () => {
                  if (window.innerWidth >= 1024) this.sidebarOpen = false;
              });
          }
      }">

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen"
         x-cloak
         @click="sidebarOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden">
    </div>

    <!-- ===== DESKTOP SIDEBAR ===== -->
    <aside class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 lg:left-0 lg:z-50"
        :style="{ width: sidebarWidth, transition: 'width 0.3s ease' }"
        style="background: linear-gradient(135deg, rgba(15,23,42,0.92) 0%, rgba(30,41,59,0.88) 50%, rgba(15,23,42,0.92) 100%);
               backdrop-filter: blur(24px) saturate(180%);
               -webkit-backdrop-filter: blur(24px) saturate(180%);
               border-right: 1px solid rgba(255,255,255,0.1);
               box-shadow: 4px 0 24px rgba(0,0,0,0.2);">
        @include('layouts.sidebar')
    </aside>

    <!-- ===== MOBILE SIDEBAR ===== -->
    <aside class="fixed inset-y-0 left-0 z-50 w-72 lg:hidden"
        :style="{
            transform: sidebarOpen ? 'translateX(0)' : 'translateX(-100%)',
            transition: 'transform 0.3s ease',
            background: 'linear-gradient(135deg, rgba(15,23,42,0.92) 0%, rgba(30,41,59,0.88) 50%, rgba(15,23,42,0.92) 100%)',
            backdropFilter: 'blur(24px) saturate(180%)',
            WebkitBackdropFilter: 'blur(24px) saturate(180%)',
            borderRight: '1px solid rgba(255,255,255,0.1)',
            boxShadow: '4px 0 24px rgba(0,0,0,0.2)'
        }">
        @include('layouts.sidebar')
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="transition-all duration-300 ease-in-out"
         :style="'margin-left: ' + contentMargin">

        <!-- Topbar -->
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-gray-200/60 px-4 sm:px-6 py-3">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-2 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 rounded-xl hover:bg-gray-100 transition-colors flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <button @click="toggleSidebar()"
                        class="hidden lg:flex p-2 rounded-xl hover:bg-gray-100 transition-colors flex-shrink-0"
                        title="Toggle sidebar">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             :class="{'rotate-180': sidebarCollapsed}">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        </svg>
                    </button>
                    <h1 class="text-base sm:text-lg font-bold text-gray-900 truncate @yield('title-class')">@yield('page-title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <button @click="profileOpen = !profileOpen"
                        class="flex items-center gap-2 p-1.5 pr-3 rounded-xl hover:bg-gray-100 transition-all"
                        :class="profileOpen ? 'bg-gray-100' : ''">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-blue-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden sm:inline text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                        <svg class="hidden sm:inline w-4 h-4 text-gray-400 transition-transform" :class="profileOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="profileOpen"
                         x-cloak
                         @click.away="profileOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                         class="absolute right-4 top-14 z-50 w-60 bg-white rounded-2xl shadow-xl border border-gray-200/80 py-2 overflow-hidden">

                        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-blue-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-blue-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                            <span class="inline-block mt-1.5 text-xs px-2 py-0.5 rounded-full font-semibold
                                @if(auth()->user()->role === 'superadmin') bg-purple-100 text-purple-700
                                @elseif(auth()->user()->role === 'admin') bg-amber-100 text-amber-700
                                @else bg-blue-100 text-blue-700
                                @endif">
                                @if(auth()->user()->role === 'superadmin') Superadmin
                                @elseif(auth()->user()->role === 'admin') Admin
                                @else Viewer
                                @endif
                            </span>
                        </div>

                        <a href="{{ route('profile.edit') }}"
                           @click="profileOpen = false"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil Saya
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="min-h-screen" style="scroll-behavior: smooth;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                @if (session('success'))
                    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2 animate-fade-in-up">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('status'))
                    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2 animate-fade-in-up">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @if(session('status') === 'profile-updated') Profil berhasil diperbarui!
                        @elseif(session('status') === 'password-updated') Kata sandi berhasil diubah!
                        @endif
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm animate-fade-in-up">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
