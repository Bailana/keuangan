<header class="bg-white border-b border-gray-200 sticky top-0 z-30 px-4 sm:px-6 py-3">
    <div class="flex items-center justify-between">
        <!-- Hamburger (mobile only) -->
        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <!-- Title -->
        <h2 class="text-base sm:text-lg font-semibold text-gray-800 truncate px-2">
            @yield('page-title', 'Dashboard Keuangan')
        </h2>

        <!-- Right side -->
        <div class="flex items-center gap-2">
            <span class="hidden sm:inline text-xs text-gray-400">
                {{ \Carbon\Carbon::now()->format('d M Y') }}
            </span>
        </div>
    </div>
</header>
