@extends('layouts.app')

@section('page-title', 'Arus Kas')

@section('content')
<!-- Ambient background -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 via-sky-50 to-emerald-100"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:2s"></div>
    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-violet-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:4s"></div>
</div>

<div class="space-y-6 relative">

    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Arus Kas</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Ringkasan pemasukan dan pengeluaran klinik & sekolah</p>
        </div>
        @if(auth()->check() && auth()->user()->isAdmin())
        <div class="flex gap-2">
            <button onclick="openModal('createIncomeModal')"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm flex items-center gap-2 shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                <span class="hidden sm:inline">Tambah Pemasukan</span>
            </button>
            <button onclick="openModal('createExpenseModal')"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm flex items-center gap-2 shadow-lg shadow-red-500/30 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                <span class="hidden sm:inline">Tambah Pengeluaran</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Wallet Selector — tampil sebelum ringkasan -->
    <div class="relative rounded-2xl p-4"
         style="background: rgba(255,255,255,0.55); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/60 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Pilih Dompet</p>
            <form method="GET" action="{{ route('cash-flows') }}" class="flex flex-wrap gap-3 items-center">
                @foreach($wallets as $w)
                    @php
                        $balance = $w->getCurrentBalance();
                        $isActive = request('wallet_id') == $w->id;
                        $accentColor = $w->slug === 'bsi' ? 'blue' : ($w->slug === 'mandiri' ? 'amber' : 'emerald');
                    @endphp
                    <button type="submit" name="wallet_id" value="{{ $w->id }}"
                        class="flex flex-col items-start gap-1 px-4 py-3 rounded-xl transition-all min-w-[160px]
                               {{ $isActive
                                   ? "bg-{$accentColor}-600 text-white shadow-lg shadow-{$accentColor}-500/30 border-{$accentColor}-500"
                                   : 'bg-white/70 text-gray-700 hover:bg-white border border-gray-200 hover:border-' . $accentColor . '-300' }}">
                        <div class="flex items-center gap-2 w-full">
                            <svg class="w-4 h-4 flex-shrink-0 {{ $isActive ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span class="text-sm font-semibold truncate">{{ $w->name }}</span>
                        </div>
                        @if($w->account_number)
                        <span class="text-xs font-mono {{ $isActive ? 'text-white/70' : 'text-gray-400' }} truncate">{{ $w->account_number }}</span>
                        @endif
                    </button>
                @endforeach
                @if(request('wallet_id'))
                <a href="{{ route('cash-flows') }}"
                    class="ml-2 px-3 py-2.5 rounded-xl text-xs font-medium text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                    ← Semua Dompet
                </a>
                @endif
            </form>
        </div>
    </div>

    @if(request('wallet_id'))
    @if($selectedWallet)
    <!-- Selected Wallet Card -->
    <div class="relative rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.55); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/60 to-transparent pointer-events-none rounded-t-2xl"></div>

        <!-- Gradient accent bar -->
        <div class="absolute top-0 left-0 w-full h-1"
             style="background: {{ $selectedWallet->slug === 'bsi' ? 'linear-gradient(90deg, #60a5fa, #3b82f6)' : ($selectedWallet->slug === 'mandiri' ? 'linear-gradient(90deg, #fbbf24, #f59e0b)' : 'linear-gradient(90deg, #34d399, #10b981)') }};"></div>

        <div class="relative z-10 px-6 py-5">
            <div class="flex items-center gap-5">
                <!-- Wallet Icon -->
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg"
                     style="background: {{ $selectedWallet->slug === 'bsi' ? 'linear-gradient(135deg, #60a5fa, #2563eb)' : ($selectedWallet->slug === 'mandiri' ? 'linear-gradient(135deg, #fbbf24, #d97706)' : 'linear-gradient(135deg, #34d399, #059669)') }};">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>

                <!-- Wallet Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Dompet Dipilih</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                              style="background: {{ $selectedWallet->slug === 'bsi' ? 'rgba(59,130,246,0.15)' : ($selectedWallet->slug === 'mandiri' ? 'rgba(245,158,11,0.15)' : 'rgba(16,185,129,0.15)') }};
                                     color: {{ $selectedWallet->slug === 'bsi' ? '#2563eb' : ($selectedWallet->slug === 'mandiri' ? '#d97706' : '#059669') }};">
                            {{ $selectedWallet->name }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 mt-2 flex-wrap">
                        @if($selectedWallet->owner_name)
                        <div class="flex items-center gap-1.5 text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span class="text-sm font-medium">{{ $selectedWallet->owner_name }}</span>
                        </div>
                        @endif
                        @if($selectedWallet->account_number)
                        <div class="flex items-center gap-1.5 text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span class="text-xs">{{ $selectedWallet->account_number }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Balance -->
                <div class="text-right flex-shrink-0">
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Saldo Tersedia</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900">
                        Rp {{ number_format($selectedWallet->getCurrentBalance(), 0, ',', '.') }}
                    </p>
                    <p class="text-xs mt-1 font-medium"
                       style="color: {{ $selectedWallet->getCurrentBalance() >= 0 ? '#059669' : '#dc2626' }};">
                        {{ $selectedWallet->getCurrentBalance() >= 0 ? '✓ Saldo positif' : '⚠ Saldo negatif' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Summary Cards — Glass Style -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <!-- Total Pemasukan -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(16,185,129,0.85) 0%, rgba(5,150,105,0.90) 50%, rgba(4,120,87,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(16,185,129,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Total Pemasukan</p>
                <p class="text-2xl sm:text-3xl font-bold mt-2">Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Total Pengeluaran -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(239,68,68,0.85) 0%, rgba(220,38,38,0.90) 50%, rgba(185,28,28,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(239,68,68,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Total Pengeluaran</p>
                <p class="text-2xl sm:text-3xl font-bold mt-2">Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Selisih -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, {{ ($totalIncome - $totalExpense) >= 0 ? 'rgba(59,130,246,0.85) 0%, rgba(37,99,235,0.90) 50%, rgba(29,78,216,0.95) 100%' : 'rgba(245,158,11,0.85) 0%, rgba(217,119,6,0.90) 50%, rgba(180,83,9,0.95) 100%' }});
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(59,130,246,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Selisih Kas</p>
                <p class="text-2xl sm:text-3xl font-bold mt-2">Rp {{ number_format(abs($totalIncome - $totalExpense), 0, ',', '.') }}</p>
                <p class="text-white/70 text-xs mt-1 font-medium">{{ ($totalIncome - $totalExpense) >= 0 ? 'Surplus' : 'Defisit' }}</p>
            </div>
        </div>

        <!-- Jumlah Data -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(99,102,241,0.85) 0%, rgba(79,70,229,0.90) 50%, rgba(67,56,202,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(99,102,241,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Total Transaksi</p>
                <p class="text-2xl sm:text-3xl font-bold mt-2">{{ $incomes->total() + $expenses->total() }}</p>
                <p class="text-white/70 text-xs mt-1 font-medium">record</p>
            </div>
        </div>
    </div>

    <!-- Filter — Glass Card -->
    <div class="relative rounded-2xl p-4"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari catatan / kategori / penerima..."
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                </div>
                <div class="w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dompet</label>
                    <select name="wallet_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                        <option value="">Semua Dompet</option>
                        @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" {{ request('wallet_id') == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dari</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                </div>
                <div class="w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.335a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('search') || request('wallet_id') || request('date_from') || request('date_to'))
                <a href="{{ route('cash-flows') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Reset
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Pemasukan Section -->
    <div class="relative rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10 px-6 py-4 border-b border-emerald-100/50 flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Pemasukan</h2>
                    <p class="text-xs text-emerald-600 font-medium">Total: Rp {{ number_format($totalIncome, 0, ',', '.') }} ({{ $incomes->total() }} record)</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('cash-flows') }}" class="flex items-center gap-2">
                    <input type="hidden" name="wallet_id" value="{{ request('wallet_id') }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <input type="hidden" name="child_id" value="{{ request('child_id') }}">
                    <input type="hidden" name="income_category_id" value="{{ request('income_category_id') }}">
                    <input type="hidden" name="expense_category_id" value="{{ request('expense_category_id') }}">
                    <input type="hidden" name="expense_per_page" value="{{ request('expense_per_page') }}">
                    <label class="text-xs text-gray-500 whitespace-nowrap mr-2">Tampilkan</label>
                    <select name="income_per_page" onchange="this.form.submit()"
                        class="text-xs border border-gray-200 rounded-lg pl-3 pr-7 py-1.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white/60 appearance-none cursor-pointer w-[72px] text-center">
                        @foreach([5, 10, 15, 25, 50] as $n)
                        <option value="{{ $n }}" {{ $incomePerPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <span class="text-xs text-gray-500">baris</span>
                </form>
                @if(auth()->check() && auth()->user()->isAdmin())
                <button onclick="openModal('createIncomeModal')"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all active:scale-95 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah
                </button>
                @endif
            </div>
        </div>
        <div class="relative z-10 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/50">
                <thead class="bg-emerald-50/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Anak</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden md:table-cell">Kategori</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden xl:table-cell">Dompet</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden lg:table-cell">Catatan</th>
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/50">
                    @forelse($incomes as $income)
                    <tr class="hover:bg-emerald-50/30 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="font-medium">{{ $income->date->format('d M') }}</div>
                            <div class="text-xs text-gray-500">{{ $income->date->format('Y') }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            @if($income->child)
                                <div class="font-medium text-gray-900">{{ $income->child->name }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($income->child->service) }}@if($income->child->class_name) {{ $income->child->class_name }}@endif</div>
                            @else
                                <span class="text-gray-500">Umum</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 hidden md:table-cell">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                  style="background: rgba(16,185,129,0.15); color: #059669; border: 1px solid rgba(16,185,129,0.3);">
                                {{ $income->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-semibold text-emerald-600">
                            +Rp {{ number_format($income->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 hidden xl:table-cell">{{ $income->wallet->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-500 hidden lg:table-cell">{{ Str::limit($income->notes, 40) }}</td>
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-1">
                                <a href="#" onclick="openEditIncomeModal({{ $income->id }}); return false;"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50/50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('incomes.destroy', $income) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="wallet_id" value="{{ request('wallet_id') }}">
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50/50 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 7 : 6 }}" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada pemasukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($incomes->count() > 0)
        <div class="relative z-10 px-4 py-3 border-t border-gray-200/50">
            {{ $incomes->withQueryString()->links('vendor.pagination.inkflows') }}
        </div>
        @endif
    </div>

    <!-- Pengeluaran Section -->
    <div class="relative rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10 px-6 py-4 border-b border-red-100/50 flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Pengeluaran</h2>
                    <p class="text-xs text-red-600 font-medium">Total: Rp {{ number_format($totalExpense, 0, ',', '.') }} ({{ $expenses->total() }} record)</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('cash-flows') }}" class="flex items-center gap-2">
                    <input type="hidden" name="wallet_id" value="{{ request('wallet_id') }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <input type="hidden" name="income_per_page" value="{{ request('income_per_page') }}">
                    <input type="hidden" name="child_id" value="{{ request('child_id') }}">
                    <input type="hidden" name="income_category_id" value="{{ request('income_category_id') }}">
                    <input type="hidden" name="expense_category_id" value="{{ request('expense_category_id') }}">
                    <label class="text-xs text-gray-500 whitespace-nowrap mr-2">Tampilkan</label>
                    <select name="expense_per_page" onchange="this.form.submit()"
                        class="text-xs border border-gray-200 rounded-lg pl-3 pr-7 py-1.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white/60 appearance-none cursor-pointer w-[72px] text-center">
                        @foreach([5, 10, 15, 25, 50] as $n)
                        <option value="{{ $n }}" {{ $expensePerPage == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <span class="text-xs text-gray-500">baris</span>
                </form>
                @if(auth()->check() && auth()->user()->isAdmin())
                <button onclick="openModal('createExpenseModal')"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all active:scale-95 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah
                </button>
                @endif
            </div>
        </div>
        <div class="relative z-10 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/50">
                <thead class="bg-red-50/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Judul</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden xl:table-cell">Penerima</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider hidden sm:table-cell">Bukti</th>
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/50">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-red-50/30 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            <div class="font-medium text-gray-900">{{ $expense->date->format('d M') }}</div>
                            <div class="text-xs text-gray-500">{{ $expense->date->format('Y') }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm {{ $expense->title ? '' : 'text-gray-400' }} max-w-[180px]">
                            <span class="truncate block" title="{{ $expense->title ?? '-' }}">{{ $expense->title ?? '<span class="text-gray-400">-</span>' }}</span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                  style="background: rgba(239,68,68,0.15); color: #dc2626; border: 1px solid rgba(239,68,68,0.3);">
                                {{ $expense->category->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-semibold text-red-600">
                            -Rp {{ number_format($expense->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 hidden xl:table-cell">{{ $expense->recipient ?? '-' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-center hidden sm:table-cell">
                            @if($expense->receipt_url)
                                <a href="{{ Storage::url($expense->receipt_url) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs underline">Lihat</a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-1">
                                <a href="#" onclick="openEditExpenseModal({{ $expense->id }}); return false;"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50/50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="wallet_id" value="{{ request('wallet_id') }}">
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50/50 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 7 : 6 }}" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada pengeluaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->count() > 0)
        <div class="relative z-10 px-4 py-3 border-t border-gray-200/50">
            {{ $expenses->withQueryString()->links('vendor.pagination.expenseflows') }}
        </div>
        @endif
    </div>

    @else
    <!-- Placeholder saat belum memilih dompet -->
    <div class="relative rounded-2xl p-12 text-center"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-blue-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        </div>
        <h3 class="text-base font-bold text-gray-900 mb-1">Pilih Dompet Terlebih Dahulu</h3>
        <p class="text-sm text-gray-500">Silakan pilih dompet di atas untuk melihat ringkasan arus kas</p>
    </div>
    @endif
</div>

<!-- Create Income Modal -->
<div id="createIncomeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('createIncomeModal')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-lg font-bold text-gray-900">Tambah Pemasukan</h2>
            <button onclick="closeModal('createIncomeModal')" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('incomes.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="wallet_id" value="{{ request('wallet_id') }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                <select name="income_category_id" id="incomeCategoryId" required
                        data-categories='[{"name":"SPP","id":2},{"name":"Terapi","id":10},{"name":"Vokasi","id":12},{"name":"Lain-lain","id":3}]'
                        class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                    <option value="">— Pilih kategori —</option>
                    @foreach($incomeCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('income_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div id="childField" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Anak <span class="text-gray-400 font-normal">(opsional)</span></label>
                <select name="child_id" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                    <option value="">— Umum / Tidak terkait anak —</option>
                    @foreach($children as $child)
                        <option value="{{ $child->id }}" {{ old('child_id') == $child->id ? 'selected' : '' }}>{{ $child->name }} ({{ $child->service }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pengirim</label>
                <input type="text" name="sender_name" value="{{ old('sender_name') }}" placeholder="Nama pengirim / pembayar (opsional)" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dompet <span class="text-gray-400 font-normal">(opsional)</span></label>
                <select name="wallet_id" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                    <option value="">— Pilih dompet —</option>
                    @foreach($wallets as $wallet)
                        <option value="{{ $wallet->id }}" {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>
                            {{ $wallet->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                        <input type="number" name="amount" required min="0" step="100" value="{{ old('amount') }}" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 pl-8 pr-4 py-2.5 text-sm transition-all">
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="2" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all" placeholder="Opsional...">{{ old('notes') }}</textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('createIncomeModal')" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium shadow-lg shadow-emerald-500/20 transition-all active:scale-95">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Create Expense Modal -->
<div id="createExpenseModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('createExpenseModal')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-lg font-bold text-gray-900">Tambah Pengeluaran</h2>
            <button onclick="closeModal('createExpenseModal')" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-4" id="expenseForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="wallet_id" value="{{ request('wallet_id') }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Pengeluaran <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Pembelian alat tulis" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                <select name="expense_category_id" id="expenseCategoryId" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                    <option value="">— Pilih kategori —</option>
                    @foreach($expenseCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="date" id="expenseDate" required value="{{ old('date', date('Y-m-d')) }}" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                        <input type="number" name="amount" id="expenseAmount" required min="0" step="100" value="{{ old('amount') }}" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 pl-8 pr-4 py-2.5 text-sm transition-all">
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sumber Dana (Dompet) <span class="text-red-500">*</span></label>
                <select name="wallet_id" id="walletId" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                    <option value="">— Pilih dompet —</option>
                    @foreach($wallets as $wallet)
                        <option value="{{ $wallet->id }}" {{ old('wallet_id') == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Penerima</label>
                <input type="text" name="recipient" id="recipient" value="{{ old('recipient') }}" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all" placeholder="Contoh: Bp. Ahmad">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Upload Bukti <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="file" name="receipt_url" accept="image/*,.pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-700 hover:file:bg-red-100 rounded-xl border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="2" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all" placeholder="Opsional...">{{ old('notes') }}</textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('createExpenseModal')" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium shadow-lg shadow-red-500/20 transition-all active:scale-95">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Income Modal -->
<div id="editIncomeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('editIncomeModal')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-lg font-bold text-gray-900">Edit Pemasukan</h2>
            <button onclick="closeModal('editIncomeModal')" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editIncomeForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="wallet_id" id="editIncomeWalletIdHidden" value="{{ request('wallet_id') }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                <select name="income_category_id" id="editIncomeCategoryId" required
                        data-categories='[{"name":"SPP","id":2},{"name":"Terapi","id":10},{"name":"Vokasi","id":12},{"name":"Lain-lain","id":3}]'
                        class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                    <option value="">— Pilih kategori —</option>
                </select>
            </div>
            <div id="editChildField" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Anak <span class="text-gray-400 font-normal">(opsional)</span></label>
                <select name="child_id" id="editChildId" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                    <option value="">— Umum / Tidak terkait anak —</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pengirim</label>
                <input type="text" name="sender_name" id="editSenderName" placeholder="Nama pengirim / pembayar (opsional)" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Dompet <span class="text-gray-400 font-normal">(opsional)</span></label>
                <select name="wallet_id" id="editWalletId" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                    <option value="">— Pilih dompet —</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="date" id="editDate" required class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="editAmount" required min="0" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                <textarea name="notes" id="editNotes" rows="2" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all" placeholder="Opsional..."></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('editIncomeModal')" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium shadow-lg shadow-emerald-500/20 transition-all active:scale-95">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Expense Modal -->
<div id="editExpenseModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('editExpenseModal')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-lg font-bold text-gray-900">Edit Pengeluaran</h2>
            <button onclick="closeModal('editExpenseModal')" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editExpenseForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="wallet_id" id="editExpenseWalletIdHidden" value="{{ request('wallet_id') }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Pengeluaran <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="editExpenseTitle" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                <select name="expense_category_id" id="editExpenseCategoryId" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                    <option value="">— Pilih kategori —</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="date" id="editExpenseDate" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="editExpenseAmount" required min="0" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sumber Dana (Dompet) <span class="text-red-500">*</span></label>
                <select name="wallet_id" id="editExpenseWalletId" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                    <option value="">— Pilih dompet —</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Penerima</label>
                <input type="text" name="recipient" id="editExpenseRecipient" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all" placeholder="Contoh: Bp. Ahmad">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                <textarea name="notes" id="editExpenseNotes" rows="2" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-4 py-2.5 text-sm transition-all" placeholder="Opsional..."></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('editExpenseModal')" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium shadow-lg shadow-red-500/20 transition-all active:scale-95">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    if (id === 'createIncomeModal') {
        resetIncomeModal();
    }
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}
function resetIncomeModal() {
    document.getElementById('incomeCategoryId').value = '';
    document.getElementById('childField').classList.add('hidden');
}
document.addEventListener('DOMContentLoaded', function() {
    const incomeCatSelect = document.getElementById('incomeCategoryId');
    if (incomeCatSelect) {
        incomeCatSelect.addEventListener('change', function() {
            const value = parseInt(this.value);
            const categories = JSON.parse(this.dataset.categories);
            const childField = document.getElementById('childField');
            const selectedCat = categories.find(c => c.id === value);
            // Show child field for SPP and Terapi, hide for Lain-lain
            const showChild = selectedCat && selectedCat.name !== 'Lain-lain';
            if (showChild) {
                childField.classList.remove('hidden');
                this.closest('form').querySelector('select[name="child_id"]').setAttribute('required', 'required');
            } else {
                childField.classList.add('hidden');
                this.closest('form').querySelector('select[name="child_id"]').removeAttribute('required');
            }
        });
    }
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal('createIncomeModal');
        closeModal('createExpenseModal');
        closeModal('editIncomeModal');
        closeModal('editExpenseModal');
        closeModal('editExpenseModal');
    }
});

let editingIncomeId = null;

function openEditIncomeModal(id) {
    editingIncomeId = id;
    fetch('{{ route('incomes.edit-modal', ':id') }}'.replace(':id', id))
        .then(response => response.json())
        .then(data => {
            // Populate categories
            const catSelect = document.getElementById('editIncomeCategoryId');
            catSelect.innerHTML = '<option value="">— Pilih kategori —</option>';
            data.categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.name;
                if (cat.id == data.income.income_category_id) option.selected = true;
                catSelect.appendChild(option);
            });

            // Populate children
            const childSelect = document.getElementById('editChildId');
            childSelect.innerHTML = '<option value="">— Umum / Tidak terkait anak —</option>';
            data.children.forEach(child => {
                const option = document.createElement('option');
                option.value = child.id;
                option.textContent = child.name;
                if (child.id == data.income.child_id) option.selected = true;
                childSelect.appendChild(option);
            });

            // Populate wallets
            const walletSelect = document.getElementById('editWalletId');
            walletSelect.innerHTML = '<option value="">— Pilih dompet —</option>';
            data.wallets.forEach(wallet => {
                const option = document.createElement('option');
                option.value = wallet.id;
                option.textContent = wallet.name;
                if (wallet.id == data.income.wallet_id) option.selected = true;
                walletSelect.appendChild(option);
            });

            // Populate other fields
            document.getElementById('editSenderName').value = data.income.sender_name || '';
            document.getElementById('editDate').value = data.income.date ? data.income.date.substring(0, 10) : '';
            document.getElementById('editAmount').value = parseFloat(data.income.amount).toFixed(0);
            document.getElementById('editNotes').value = data.income.notes || '';

            // Set form action
            document.getElementById('editIncomeForm').action = '{{ url('/') }}/incomes/' + id;

            // Preserve wallet_id filter
            const currentWalletId = '{{ request('wallet_id') }}';
            const editIncomeWalletHidden = document.getElementById('editIncomeWalletIdHidden');
            if (editIncomeWalletHidden) editIncomeWalletHidden.value = currentWalletId;
            const editIncomeWalletSelect = document.getElementById('editWalletId');
            if (editIncomeWalletSelect && currentWalletId) editIncomeWalletSelect.value = currentWalletId;

            // Reset child field visibility
            const catValue = parseInt(catSelect.value);
            const categories = JSON.parse(catSelect.dataset.categories);
            const selectedCat = categories.find(c => c.id === catValue);
            const childField = document.getElementById('editChildField');
            if (selectedCat && selectedCat.name !== 'Lain-lain') {
                childField.classList.remove('hidden');
                childSelect.setAttribute('required', 'required');
            } else {
                childField.classList.add('hidden');
                childSelect.removeAttribute('required');
            }

            openModal('editIncomeModal');
        })
        .catch(error => console.error('Error loading income data:', error));
}

let editingExpenseId = null;

function openEditExpenseModal(id) {
    editingExpenseId = id;
    fetch('{{ route('expenses.edit-modal', ':id') }}'.replace(':id', id))
        .then(response => response.json())
        .then(data => {
            // Populate categories
            const catSelect = document.getElementById('editExpenseCategoryId');
            catSelect.innerHTML = '<option value="">— Pilih kategori —</option>';
            data.categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.name;
                if (cat.id == data.expense.expense_category_id) option.selected = true;
                catSelect.appendChild(option);
            });

            // Populate wallets
            const walletSelect = document.getElementById('editExpenseWalletId');
            walletSelect.innerHTML = '<option value="">— Pilih dompet —</option>';
            data.wallets.forEach(wallet => {
                const option = document.createElement('option');
                option.value = wallet.id;
                option.textContent = wallet.name;
                if (wallet.id == data.expense.wallet_id) option.selected = true;
                walletSelect.appendChild(option);
            });

            // Populate other fields
            document.getElementById('editExpenseTitle').value = data.expense.title || '';
            document.getElementById('editExpenseDate').value = data.expense.date ? data.expense.date.substring(0, 10) : '';
            document.getElementById('editExpenseAmount').value = parseFloat(data.expense.amount).toFixed(0);
            document.getElementById('editExpenseRecipient').value = data.expense.recipient || '';
            document.getElementById('editExpenseNotes').value = data.expense.notes || '';

            // Set form action
            document.getElementById('editExpenseForm').action = '{{ url('/') }}/expenses/' + id;

            // Preserve wallet_id filter
            const currentWalletId = '{{ request('wallet_id') }}';
            const editExpenseWalletHidden = document.getElementById('editExpenseWalletIdHidden');
            if (editExpenseWalletHidden) editExpenseWalletHidden.value = currentWalletId;
            const editExpenseWalletSelect = document.getElementById('editExpenseWalletId');
            if (editExpenseWalletSelect && currentWalletId) editExpenseWalletSelect.value = currentWalletId;

            openModal('editExpenseModal');
        })
        .catch(error => console.error('Error loading expense data:', error));
}

// Add change listener for edit income category
document.addEventListener('DOMContentLoaded', function() {
    const editIncomeCatSelect = document.getElementById('editIncomeCategoryId');
    if (editIncomeCatSelect) {
        editIncomeCatSelect.addEventListener('change', function() {
            const value = parseInt(this.value);
            const categories = JSON.parse(this.dataset.categories);
            const childField = document.getElementById('editChildField');
            const selectedCat = categories.find(c => c.id === value);
            const showChild = selectedCat && selectedCat.name !== 'Lain-lain';
            if (showChild) {
                childField.classList.remove('hidden');
                this.closest('form').querySelector('select[name="child_id"]').setAttribute('required', 'required');
            } else {
                childField.classList.add('hidden');
                this.closest('form').querySelector('select[name="child_id"]').removeAttribute('required');
            }
        });
    }
});
</script>
@endsection
