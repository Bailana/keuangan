@extends('layouts.app')

@section('page-title', 'Rekap Tunggakan')

@section('content')
<div class="space-y-6 relative">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Rekap Tunggakan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Monitoring tagihan dan pembayaran siswa</p>
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
                <div class="w-[140px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        <option value="">Semua</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Sebagian</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Jatuh Tempo</option>
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
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari Anak</label>
                    <input type="text" name="child_name" value="{{ request('child_name') }}"
                        placeholder="Nama anak..."
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.335a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('month') || request('status') || request('child_name') || request('wallet_id'))
                <a href="{{ route('reports.arrears') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Reset
                </a>
                @endif
                <div class="flex items-center gap-2 ml-auto">
                    <a href="{{ route('reports.arrears.export.pdf', request()->except('page')) }}"
                       target="_blank"
                       class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF
                    </a>
                    <a href="{{ route('reports.arrears.export.excel', request()->except('page')) }}"
                       class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Excel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="relative rounded-2xl overflow-hidden p-4 text-white"
             style="background: linear-gradient(135deg, rgba(16,185,129,0.85) 0%, rgba(5,150,105,0.95) 100%);
                    box-shadow: 0 8px 32px rgba(16,185,129,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Lunas</p>
                <p class="text-2xl font-bold mt-1">{{ $summary['paid'] }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-4 text-white"
             style="background: linear-gradient(135deg, rgba(245,158,11,0.85) 0%, rgba(217,119,6,0.95) 100%);
                    box-shadow: 0 8px 32px rgba(245,158,11,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Sebagian</p>
                <p class="text-2xl font-bold mt-1">{{ $summary['partial'] }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-4 text-white"
             style="background: linear-gradient(135deg, rgba(239,68,68,0.85) 0%, rgba(220,38,38,0.95) 100%);
                    box-shadow: 0 8px 32px rgba(239,68,68,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Belum Bayar</p>
                <p class="text-2xl font-bold mt-1">{{ $summary['unpaid'] }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-4 text-white"
             style="background: linear-gradient(135deg, rgba(127,29,29,0.85) 0%, rgba(127,29,29,0.95) 100%);
                    box-shadow: 0 8px 32px rgba(127,29,29,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Total Tunggakan</p>
                <p class="text-xl font-bold mt-1">Rp {{ number_format($summary['totalOutstanding'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="relative rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="relative z-10 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/50">
                <thead class="bg-amber-50/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Anak</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Layanan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Tagihan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Sudah Dibayar</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Tunggakan</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/50">
                    @forelse($children as $child)
                    <tr class="hover:bg-amber-50/20 transition-colors">
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-900">{{ $child->name }}</div>
                            <div class="text-xs text-gray-500">{{ $child->parent_name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            <div>{{ $child->class_name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $child->getTherapyDetails() }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm font-medium text-gray-900">
                            Rp {{ number_format($child->invoiceAmount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-sm text-emerald-600">
                            Rp {{ number_format($child->totalPaid, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm font-bold {{ $child->outstanding > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            Rp {{ number_format($child->outstanding, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            @php
                                $statusClass = match($child->paymentStatus) {
                                    'paid' => 'bg-emerald-100 text-emerald-700',
                                    'partial' => 'bg-amber-100 text-amber-700',
                                    'unpaid' => 'bg-gray-100 text-gray-600',
                                    'overdue' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-500',
                                };
                                $statusText = match($child->paymentStatus) {
                                    'paid' => 'Lunas',
                                    'partial' => 'Sebagian',
                                    'unpaid' => 'Belum Bayar',
                                    'overdue' => 'Jatuh Tempo',
                                    default => '-',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        @if(auth()->check() && auth()->user()->isAdmin() && $child->parent_whatsapp)
                        <td class="px-4 py-4 text-center">
                            @if($child->outstanding > 0)
                            <a href="{{ $child->whatsapp_url }}" target="_blank"
                               class="inline-flex items-center gap-1 px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs transition-colors"
                               title="Kirim pengingat WhatsApp">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                Kirim
                            </a>
                            @endif
                        </td>
                        @else
                        <td class="px-4 py-4 text-center text-sm text-gray-400">-</td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 7 : 6 }}" class="px-4 py-8 text-center text-sm text-gray-400">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
