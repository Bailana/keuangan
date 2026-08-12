@extends('layouts.app')

@section('page-title', 'Laporan Pendapatan')

@section('content')
<div class="space-y-6 relative">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Laporan Pendapatan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Breakdown pendapatan per layanan dan program</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="relative rounded-2xl p-4"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="w-[120px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
                    <select name="year" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        @foreach(range(now()->year - 2, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-[140px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                    <select name="month" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(now()->year, $m, 1)->locale('id')->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-[150px]">
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
                @if(request('month') || request('year') != now()->year || request('wallet_id'))
                <a href="{{ route('reports.revenue') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Reset
                </a>
                @endif
                <div class="flex items-center gap-2 ml-auto">
                    <a href="{{ route('reports.revenue.export.pdf', request()->except('page')) }}"
                       target="_blank"
                       class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF
                    </a>
                    <a href="{{ route('reports.revenue.export.excel', request()->except('page')) }}"
                       class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Excel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Total Card -->
    <div class="relative rounded-2xl overflow-hidden p-5 text-white"
         style="background: linear-gradient(135deg, rgba(16,185,129,0.85) 0%, rgba(5,150,105,0.95) 100%);
                backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(16,185,129,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                border: 1px solid rgba(255,255,255,0.3);">
        <div class="relative z-10 flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Total Pendapatan</p>
                <p class="text-3xl font-bold mt-1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                <p class="text-white/70 text-sm mt-1">
                    {{ \Carbon\Carbon::create($year, $month, 1)->locale('id')->format('F Y') }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-white/60 text-xs">Bulan Ini</p>
                <p class="text-2xl font-bold">{{ number_format($incomeByMonth[$month] ?? 0, 0, ',', '.') }}</p>
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
                    @php $pct = $totalIncome > 0 ? ($item->total / $totalIncome * 100) : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-700">{{ $item->category->name ?? 'Umum' }}</span>
                            <span class="text-sm font-semibold text-emerald-600">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ number_format($pct, 1) }}%</p>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Income by Child -->
        <div class="relative rounded-2xl overflow-hidden"
             style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                    border: 1px solid rgba(255,255,255,0.7);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
            <div class="relative z-10 px-6 py-4 border-b border-blue-100/50">
                <h2 class="text-sm font-bold text-gray-900">Pendapatan per Anak</h2>
            </div>
            <div class="relative z-10 px-6 py-4">
                <div class="space-y-3">
                    @forelse($incomeByChild as $item)
                    @php $pct = $totalIncome > 0 ? ($item->total / $totalIncome * 100) : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-700">{{ $item->child->name ?? '-' }}</span>
                            <span class="text-sm font-semibold text-blue-600">Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min($pct, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ number_format($pct, 1) }}%</p>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="relative rounded-2xl p-4"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Tren Pendapatan 12 Bulan Terakhir</h3>
            <div style="height: 250px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_map(fn($m) => Carbon\Carbon::create($year, $m, 1)->locale('id')->format('M'), range(1, 12))) !!},
        datasets: [{
            label: 'Pendapatan',
            data: {!! json_encode(array_map(fn($m) => $incomeByMonth[$m] ?? 0, range(1, 12))) !!},
            borderColor: 'rgba(16, 185, 129, 1)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 2,
            pointRadius: 4,
            pointBackgroundColor: 'rgba(16, 185, 129, 1)',
            fill: true,
            tension: 0.3,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endsection
