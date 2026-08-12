@extends('layouts.app')

@section('page-title', 'Dashboard Keuangan')

@section('content')
<!-- Ambient background — necessary for glass effect -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 via-sky-50 to-emerald-100"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:2s"></div>
    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-violet-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:4s"></div>
</div>

<div class="space-y-6 relative">
    <!-- Welcome & month -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Dashboard Keuangan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->locale('id')->format('F Y') }}</p>
        </div>
    </div>

    <!-- Summary Cards — iOS 26 Glass -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <!-- Pemasukan -->
        <div class="relative rounded-2xl overflow-hidden p-5 sm:p-6 text-white"
             style="background: linear-gradient(135deg, rgba(16,185,129,0.85) 0%, rgba(5,150,105,0.90) 50%, rgba(4,120,87,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(16,185,129,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <!-- Specular highlight -->
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-4 -bottom-8 w-24 h-24 bg-black/5 rounded-full blur-xl pointer-events-none"></div>
            <!-- Content -->
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Pemasukan</p>
                    <p class="text-2xl sm:text-3xl font-bold mt-2 truncate">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                    <p class="text-white/70 text-xs mt-2 font-medium">Bulan ini</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
            </div>
        </div>

        <!-- Pengeluaran -->
        <div class="relative rounded-2xl overflow-hidden p-5 sm:p-6 text-white"
             style="background: linear-gradient(135deg, rgba(239,68,68,0.85) 0%, rgba(220,38,38,0.90) 50%, rgba(185,28,28,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(239,68,68,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-4 -bottom-8 w-24 h-24 bg-black/5 rounded-full blur-xl pointer-events-none"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Pengeluaran</p>
                    <p class="text-2xl sm:text-3xl font-bold mt-2 truncate">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                    <p class="text-white/70 text-xs mt-2 font-medium">Bulan ini</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
            </div>
        </div>

        <!-- Saldo -->
        <div class="relative rounded-2xl overflow-hidden p-5 sm:p-6 text-white"
             style="background: linear-gradient(135deg, {{ $balance >= 0 ? 'rgba(59,130,246,0.85) 0%, rgba(37,99,235,0.90) 50%, rgba(29,78,216,0.95) 100%' : 'rgba(51,65,85,0.85) 0%, rgba(30,41,59,0.90) 50%, rgba(15,23,42,0.95) 100%' }});
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(59,130,246,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-4 -bottom-8 w-24 h-24 bg-black/5 rounded-full blur-xl pointer-events-none"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Saldo</p>
                    <p class="text-2xl sm:text-3xl font-bold mt-2 truncate">Rp {{ number_format($balance, 0, ',', '.') }}</p>
                    <p class="text-white/70 text-xs mt-2 font-medium">
                        @if($balance >= 0)
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                                Positif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Defisit
                            </span>
                        @endif
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Default Wallet Card -->
    @if($defaultWallet)
    <div class="relative rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.55); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1"
             style="background: {{ $defaultWallet->slug === 'bsi' ? 'linear-gradient(90deg, #60a5fa, #3b82f6)' : ($defaultWallet->slug === 'mandiri' ? 'linear-gradient(90deg, #fbbf24, #f59e0b)' : 'linear-gradient(90deg, #34d399, #10b981)') }};"></div>
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/60 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10 px-6 py-5">
            <div class="flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg"
                     style="background: {{ $defaultWallet->slug === 'bsi' ? 'linear-gradient(135deg, #60a5fa, #2563eb)' : ($defaultWallet->slug === 'mandiri' ? 'linear-gradient(135deg, #fbbf24, #d97706)' : 'linear-gradient(135deg, #34d399, #059669)') }};">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Dompet Default</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            <svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Default
                        </span>
                    </div>
                    <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $defaultWallet->name }}</p>
                    @if($defaultWallet->account_number)
                    <p class="text-xs text-gray-400 font-mono">{{ $defaultWallet->account_number }}</p>
                    @endif
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Saldo</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($defaultWallet->getCurrentBalance(), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Monthly Line Chart — Glass Card -->
    <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <!-- Specular highlight -->
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900">Tren 6 Bulan Terakhir</h3>
                <p class="text-xs text-gray-500 mt-0.5">Pemasukan vs Pengeluaran per bulan</p>
            </div>
            @if(count($walletBalances) > 1)
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                <select name="wallet_id" onchange="this.form.submit()"
                        class="px-3 py-1.5 text-xs font-medium border border-gray-200 rounded-lg bg-white/80 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach($walletBalances as $w)
                        <option value="{{ $w->id }}" {{ request('wallet_id', $defaultWallet?->id) == $w->id ? 'selected' : '' }}>
                            {{ $w->name }}
                        </option>
                    @endforeach
                </select>
            </form>
            @endif
            <div class="flex items-center gap-4 text-xs font-medium">
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full" style="background:#10b981;box-shadow:0 0 8px rgba(16,185,129,0.5)"></span>
                    Pemasukan
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full" style="background:#ef4444;box-shadow:0 0 8px rgba(239,68,68,0.5)"></span>
                    Pengeluaran
                </span>
            </div>
        </div>
        <div class="relative h-72">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Recent & Plans -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Recent Transactions — Glass Card -->
        <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden"
             style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                    border: 1px solid rgba(255,255,255,0.7);">
            <!-- Specular highlight -->
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
            <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4">Transaksi Terbaru</h3>
            <div class="space-y-1.5">
                @php
                    $incomeTx = $recentIncomes->map(fn($i) => ['type' => 'income', 'data' => $i]);
                    $expenseTx = $recentExpenses->map(fn($e) => ['type' => 'expense', 'data' => $e]);
                    $allTx = collect($incomeTx->toArray())->merge($expenseTx->toArray())->sortByDesc(fn($x) => $x['data']->date)->take(5);
                @endphp
                @forelse($allTx as $tx)
                    <div class="flex items-center justify-between py-3 px-4 rounded-xl transition-all duration-200"
                         style="background: rgba(255,255,255,0.35); border: 1px solid rgba(255,255,255,0.5); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                                  style="{{ $tx['type'] === 'income' ? 'background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3)' : 'background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3)' }}">
                                <svg class="w-4 h-4" style="{{ $tx['type'] === 'income' ? 'color:#10b981' : 'color:#ef4444' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $tx['type'] === 'income' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $tx['data']->category->name ?? '-' }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $tx['data']->date->format('d M Y') }}@if($tx['type'] === 'income' && $tx['data']->child) · {{ $tx['data']->child->name }}@endif</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold whitespace-nowrap"
                              style="{{ $tx['type'] === 'income' ? 'color:#059669' : 'color:#dc2626' }}">
                            {{ $tx['type'] === 'income' ? '+' : '-' }}Rp {{ number_format($tx['data']->amount, 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-8">Belum ada transaksi.</p>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Plans — Glass Card -->
        <div class="rounded-2xl p-5 sm:p-6 relative overflow-hidden"
             style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                    border: 1px solid rgba(255,255,255,0.7);">
            <!-- Specular highlight -->
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
            <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4">Perencanaan Bulan Depan</h3>
            @if($incomePlans->count() > 0 || $expensePlans->count() > 0)
                <div class="space-y-1.5">
                    @foreach($nextPlans as $plan)
                        <div class="flex items-center justify-between py-3 px-4 rounded-xl transition-all duration-200"
                             style="background: rgba(255,255,255,0.35); border: 1px solid rgba(255,255,255,0.5); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                      style="{{ $plan->type === 'income' ? 'background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);color:#059669' : 'background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#dc2626' }}">
                                    {{ $plan->type === 'income' ? 'P' : 'K' }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $plan->title ?? '无标题' }}
                                        <span class="text-xs text-gray-400 ml-1">({{ $plan->service }})</span>
                                    </p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::create($plan->year, $plan->month, 1)->locale('id')->format('F Y') }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-gray-700 whitespace-nowrap">Rp {{ number_format((int)$plan->target_amount, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-sm text-gray-500">Belum ada perencanaan untuk bulan depan.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trendChart').getContext('2d');

    const gradientIncome = ctx.createLinearGradient(0, 0, 0, 288);
    gradientIncome.addColorStop(0, 'rgba(16, 185, 129, 0.30)');
    gradientIncome.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    const gradientExpense = ctx.createLinearGradient(0, 0, 0, 288);
    gradientExpense.addColorStop(0, 'rgba(239, 68, 68, 0.30)');
    gradientExpense.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthLabels) !!},
            datasets: [
                {
                    label: 'Pemasukan',
                    data: {!! json_encode($monthlyIncome) !!},
                    borderColor: '#10b981',
                    backgroundColor: gradientIncome,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                },
                {
                    label: 'Pengeluaran',
                    data: {!! json_encode($monthlyExpense) !!},
                    borderColor: '#ef4444',
                    backgroundColor: gradientExpense,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255,255,255,0.95)',
                    titleColor: '#111827',
                    bodyColor: '#374151',
                    borderColor: 'rgba(255,255,255,0.5)',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: true,
                    boxPadding: 4,
                    cornerRadius: 12,
                    backdropFilter: 'blur(8px)',
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#6b7280', font: { size: 11 } },
                    border: { display: false }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                    ticks: {
                        color: '#6b7280',
                        font: { size: 11 },
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                            return value;
                        }
                    },
                    border: { display: false },
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection
