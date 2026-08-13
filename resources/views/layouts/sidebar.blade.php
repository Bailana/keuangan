<!-- Logo -->
<div class="px-5 py-5 border-b border-white/10 flex items-center gap-3 flex-shrink-0">
    <img src="{{ asset('images/logo_am.png') }}" alt="Logo AM" class="w-10 h-10 flex-shrink-0 object-contain">
    <div x-show="!sidebarCollapsed" x-transition.opacity>
        <h1 class="text-base font-bold text-white leading-tight">KEUANGAN</h1>
        <p class="text-slate-300/80 text-xs">Klinik Terapi & Sekolah Khusus Anak Mandiri</p>
    </div>
</div>

<!-- Navigation -->
<nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
    <div class="mb-1 px-3" x-show="!sidebarCollapsed" x-transition.opacity>
        <p class="text-[10px] font-bold text-slate-400/60 uppercase tracking-widest">Menu</p>
    </div>

    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}"
       onclick="window.location.href='{{ route('dashboard') }}'; return false;"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
              {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white shadow-lg shadow-black/10' : 'text-slate-300/80 hover:bg-white/10 hover:text-white' }}"
       :class="sidebarCollapsed ? 'justify-center px-2' : ''"
       :title="sidebarCollapsed ? 'Dashboard' : ''">
        <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
        </span>
        <span x-show="!sidebarCollapsed" x-transition.opacity>Dashboard</span>
    </a>

    <!-- Keuangan & Transaksi (Dropdown) -->
    <div x-data="{ open: {{ request()->routeIs('cash-flows') || request()->routeIs('wallets.*') || request()->routeIs('invoices.*') ? 'true' : 'false' }} }">
        <button @click="open = !open"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                       text-slate-300/80 hover:bg-white/10 hover:text-white"
                :class="sidebarCollapsed ? 'justify-center px-2' : ''"
                :title="sidebarCollapsed ? 'Keuangan & Transaksi' : ''">
            <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="flex-1 text-left">Keuangan & Transaksi</span>
            <svg x-show="!sidebarCollapsed" x-transition class="w-4 h-4 text-slate-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="!sidebarCollapsed && open" x-collapse x-transition>
            <div class="ml-4 mt-1 space-y-1 border-l-2 border-white/10 pl-3">
                <!-- Arus Kas -->
                <div>
                    <a href="{{ route('cash-flows') }}"
                       onclick="window.location.href='{{ route('cash-flows') }}'; return false;"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                              {{ request()->routeIs('cash-flows') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 11l5-5m0 0l5 5m-5-5v12M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                        Arus Kas
                    </a>
                </div>
                <div class="my-1 border-t border-white/5"></div>
                <!-- Invoice -->
                <a href="{{ route('invoices.index') }}"
                   onclick="window.location.href='{{ route('invoices.index') }}'; return false;"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                          {{ request()->routeIs('invoices.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Invoice
                </a>
                <div class="my-1 border-t border-white/5"></div>
                <!-- Dompet -->
                <a href="{{ route('wallets.index') }}"
                   onclick="window.location.href='{{ route('wallets.index') }}'; return false;"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                          {{ request()->routeIs('wallets.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Dompet
                </a>
            </div>
        </div>
    </div>

    <!-- Penggajian (Dropdown) -->
    <div x-data="{ open: {{ request()->routeIs('payroll.*') ? 'true' : 'false' }} }">
        <button @click="open = !open"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                       text-slate-300/80 hover:bg-white/10 hover:text-white"
                :class="sidebarCollapsed ? 'justify-center px-2' : ''"
                :title="sidebarCollapsed ? 'Penggajian' : ''">
            <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </span>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="flex-1 text-left">Penggajian</span>
            <svg x-show="!sidebarCollapsed" x-transition class="w-4 h-4 text-slate-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="!sidebarCollapsed && open" x-collapse x-transition>
            <div class="ml-4 mt-1 space-y-1 border-l-2 border-white/10 pl-3">
                <a href="{{ route('payroll.index') }}"
                   onclick="window.location.href='{{ route('payroll.index') }}'; return false;"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                          {{ request()->routeIs('payroll.index') || request()->routeIs('payroll.create') || request()->routeIs('payroll.edit') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Gaji Karyawan
                </a>
                <a href="{{ route('payroll.history') }}"
                   onclick="window.location.href='{{ route('payroll.history') }}'; return false;"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                          {{ request()->routeIs('payroll.history') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Rekap Slip Gaji
                </a>
            </div>
        </div>
    </div>

    <!-- Laporan Keuangan (Dropdown) -->
    <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
        <button @click="open = !open"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                       text-slate-300/80 hover:bg-white/10 hover:text-white"
                :class="sidebarCollapsed ? 'justify-center px-2' : ''"
                :title="sidebarCollapsed ? 'Laporan Keuangan' : ''">
            <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </span>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="flex-1 text-left">Laporan Keuangan</span>
            <svg x-show="!sidebarCollapsed" x-transition class="w-4 h-4 text-slate-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="!sidebarCollapsed && open" x-collapse x-transition>
            <div class="ml-4 mt-1 space-y-1 border-l-2 border-white/10 pl-3">
                <a href="{{ route('reports.profit-loss') }}"
                   onclick="window.location.href='{{ route('reports.profit-loss') }}'; return false;"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                          {{ request()->routeIs('reports.profit-loss') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Laba / Rugi
                </a>
                <a href="{{ route('reports.arrears') }}"
                   onclick="window.location.href='{{ route('reports.arrears') }}'; return false;"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                          {{ request()->routeIs('reports.arrears') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Rekap Tunggakan
                </a>
                <a href="{{ route('reports.revenue') }}"
                   onclick="window.location.href='{{ route('reports.revenue') }}'; return false;"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                          {{ request()->routeIs('reports.revenue') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    Laporan Pendapatan
                </a>
            </div>
        </div>
    </div>

    <!-- Perencanaan -->
    <a href="{{ route('plans.index') }}"
       onclick="window.location.href='{{ route('plans.index') }}'; return false;"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
              {{ request()->routeIs('plans.*') ? 'bg-white/15 text-white shadow-lg shadow-black/10' : 'text-slate-300/80 hover:bg-white/10 hover:text-white' }}"
       :class="sidebarCollapsed ? 'justify-center px-2' : ''"
       :title="sidebarCollapsed ? 'Perencanaan' : ''">
        <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        </span>
        <span x-show="!sidebarCollapsed" x-transition.opacity>Perencanaan</span>
    </a>

    <!-- Log Aktivitas (Admin Only) -->
    @if(auth()->check() && auth()->user()->isAdmin())
    <a href="{{ route('activity-logs.index') }}"
       onclick="window.location.href='{{ route('activity-logs.index') }}'; return false;"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
              {{ request()->routeIs('activity-logs.*') ? 'bg-white/15 text-white shadow-lg shadow-black/10' : 'text-slate-300/80 hover:bg-white/10 hover:text-white' }}"
       :class="sidebarCollapsed ? 'justify-center px-2' : ''"
       :title="sidebarCollapsed ? 'Log Aktivitas' : ''">
        <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </span>
        <span x-show="!sidebarCollapsed" x-transition.opacity>Log Aktivitas</span>
    </a>
    @endif

    <!-- Manajemen Pengguna (Superadmin Only) -->
    @if(auth()->check() && auth()->user()->isSuperAdmin())
    <a href="{{ route('users.index') }}"
       onclick="window.location.href='{{ route('users.index') }}'; return false;"
       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
              {{ request()->routeIs('users.*') ? 'bg-white/15 text-white shadow-lg shadow-black/10' : 'text-slate-300/80 hover:bg-white/10 hover:text-white' }}"
       :class="sidebarCollapsed ? 'justify-center px-2' : ''"
       :title="sidebarCollapsed ? 'Manajemen Pengguna' : ''">
        <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </span>
        <span x-show="!sidebarCollapsed" x-transition.opacity>Manajemen Pengguna</span>
    </a>
    @endif

    <div class="my-2 border-t border-white/10" x-show="!sidebarCollapsed" x-transition.opacity></div>
    <div class="px-3 mb-1" x-show="!sidebarCollapsed" x-transition.opacity>
        <p class="text-[10px] font-bold text-slate-400/60 uppercase tracking-widest">Kelola</p>
    </div>

    <!-- Master Data (Dropdown) -->
    <div x-data="{ open: {{ request()->routeIs('therapy-types.*') || request()->routeIs('vocational-types.*') || request()->routeIs('children.*') || request()->routeIs('employees.*') ? 'true' : 'false' }} }">
        <button @click="open = !open"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                       text-slate-300/80 hover:bg-white/10 hover:text-white"
                :class="sidebarCollapsed ? 'justify-center px-2' : ''"
                :title="sidebarCollapsed ? 'Master Data' : ''">
            <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
            </span>
            <span x-show="!sidebarCollapsed" x-transition.opacity class="flex-1 text-left">Master Data</span>
            <svg x-show="!sidebarCollapsed" x-transition class="w-4 h-4 text-slate-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="!sidebarCollapsed && open" x-collapse x-transition>
            <div class="ml-4 mt-1 space-y-1 border-l-2 border-white/10 pl-3">
                <a href="{{ route('therapy-types.index') }}"
                   onclick="window.location.href='{{ route('therapy-types.index') }}'; return false;"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                          {{ request()->routeIs('therapy-types.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Jenis Terapi
                </a>
                <a href="{{ route('vocational-types.index') }}"
                   onclick="window.location.href='{{ route('vocational-types.index') }}'; return false;"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                          {{ request()->routeIs('vocational-types.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Jenis Vokasi
                </a>
                <a href="{{ route('children.index') }}"
                   onclick="window.location.href='{{ route('children.index') }}'; return false;"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                          {{ request()->routeIs('children.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Data Anak
                </a>
                <a href="{{ route('employees.index') }}"
                   onclick="window.location.href='{{ route('employees.index') }}'; return false;"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200
                          {{ request()->routeIs('employees.*') ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Karyawan
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- User Card -->
<div class="px-3 py-4 border-t border-white/10 flex-shrink-0">
    <div class="flex items-center gap-3"
         :class="sidebarCollapsed ? 'justify-center' : ''">
        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-blue-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0" x-show="!sidebarCollapsed" x-transition.opacity>
            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
            <span class="inline-block text-xs px-2 py-0.5 rounded-full font-semibold
                @if(auth()->user()->role === 'superadmin') bg-purple-500/20 text-purple-300
                @elseif(auth()->user()->role === 'admin') bg-amber-500/20 text-amber-300
                @else bg-emerald-500/20 text-emerald-300
                @endif">
                @if(auth()->user()->role === 'superadmin') Superadmin
                @elseif(auth()->user()->role === 'admin') Admin
                @else Viewer
                @endif
            </span>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0"
              :class="sidebarCollapsed ? 'hidden' : ''">
            @csrf
            <button type="submit"
                class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-400 hover:text-white hover:bg-white/10 transition-all"
                title="Logout">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </button>
        </form>
    </div>
</div>
