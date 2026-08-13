@extends('layouts.app')

@section('page-title', 'Laporan Laba Rugi')

@section('content')
<div class="space-y-6 relative">
    <!-- Header -->
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(16,185,129,0.85) 0%, rgba(5,150,105,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(16,185,129,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Total Pendapatan</p>
                <p class="text-2xl sm:text-3xl font-bold mt-2">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(239,68,68,0.85) 0%, rgba(220,38,38,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(239,68,68,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Total Beban</p>
                <p class="text-2xl sm:text-3xl font-bold mt-2">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, {{ $netProfit >= 0 ? 'rgba(59,130,246,0.85)' : 'rgba(245,158,11,0.85)' }} 0%, {{ $netProfit >= 0 ? 'rgba(37,99,235,0.95)' : 'rgba(217,119,6,0.95)' }} 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(59,130,246,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Laba / Rugi Bersih</p>
                <p class="text-2xl sm:text-3xl font-bold mt-2">Rp {{ number_format(abs($netProfit), 0, ',', '.') }}</p>
                <p class="text-white/70 text-xs mt-1 font-medium">{{ $netProfit >= 0 ? '✓ Laba' : '⚠ Rugi' }} · Margin {{ number_format(abs($margin), 1) }}%</p>
            </div>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="relative rounded-2xl p-4"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
                    <select name="year" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        @foreach(range(now()->year - 3, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                    <select name="month" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        <option value="">Semua Bulan</option>
                        @foreach($months as $m => $label)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dompet</label>
                    <select name="wallet_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        <option value="">Semua Dompet</option>
                        @foreach($wallets as $w)
                            <option value="{{ $w->id }}" {{ request('wallet_id', $walletId) == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.335a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('month') || (request('year') && request('year') != now()->year) || request('wallet_id'))
                <a href="{{ route('reports.profit-loss') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Reset
                </a>
                @endif
                <div class="flex items-center gap-2 ml-auto">
                    <a href="{{ route('reports.profit-loss.export.pdf', request()->except('page')) }}"
                       target="_blank"
                       class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF
                    </a>
                    <a href="{{ route('reports.profit-loss.export.excel', request()->except('page')) }}"
                       class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Excel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Monthly Chart -->
    <div class="relative rounded-2xl p-4"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Tren Bulanan {{ $year }}</h3>
            <div style="height: 250px;">
                <canvas id="profitLossChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Income by Category -->
        <div class="relative rounded-2xl overflow-hidden"
             style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                    border: 1px solid rgba(255,255,255,0.7);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
            <div class="relative z-10 px-6 py-4 border-b border-emerald-100/50">
                <h2 class="text-sm font-bold text-gray-900">Pendapatan per Kategori</h2>
            </div>
            <div class="relative z-10 px-6 py-4">
                <div class="space-y-3">
                    @forelse($incomeByCategory as $item)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span class="text-sm text-gray-700">{{ $item->category->name ?? 'Umum' }}</span>
                        </div>
                        <span class="text-sm font-semibold text-emerald-600">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data pendapatan</p>
                    @endforelse
                    <div class="border-t pt-3 flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-900">Total</span>
                        <span class="text-sm font-bold text-emerald-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense by Category -->
        <div class="relative rounded-2xl overflow-hidden"
             style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                    border: 1px solid rgba(255,255,255,0.7);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
            <div class="relative z-10 px-6 py-4 border-b border-red-100/50">
                <h2 class="text-sm font-bold text-gray-900">Beban per Kategori</h2>
            </div>
            <div class="relative z-10 px-6 py-4">
                <div class="space-y-3">
                    @forelse($expenseByCategory as $item)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                            <span class="text-sm text-gray-700">{{ $item->category->name ?? 'Umum' }}</span>
                        </div>
                        <span class="text-sm font-semibold text-red-600">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data beban</p>
                    @endforelse
                    <div class="border-t pt-3 flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-900">Total</span>
                        <span class="text-sm font-bold text-red-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('profitLossChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_map(fn($m) => Carbon\Carbon::create($year, $m, 1)->locale('id')->format('M'), range(1, 12))) !!},
        datasets: [
            {
                label: 'Pendapatan',
                data: {!! json_encode(array_map(fn($m) => $monthlyData[$m-1]['income'], range(1, 12))) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1,
                borderRadius: 6,
            },
            {
                label: 'Beban',
                data: {!! json_encode(array_map(fn($m) => $monthlyData[$m-1]['expense'], range(1, 12))) !!},
                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1,
                borderRadius: 6,
            },
            {
                label: 'Laba/Rugi',
                data: {!! json_encode(array_map(fn($m) => $monthlyData[$m-1]['net'], range(1, 12))) !!},
                type: 'line',
                borderColor: 'rgba(59, 130, 246, 1)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                pointRadius: 3,
                fill: true,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endsection
