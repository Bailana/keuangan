@extends('layouts.app')

@section('page-title', 'E-Statement')

@section('content')
<!-- Ambient background -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 via-sky-50 to-emerald-100"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-violet-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:2s"></div>
    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:4s"></div>
</div>

<div class="space-y-6 relative max-w-5xl mx-auto">

    <!-- Back link & Month Filter -->
    <div class="flex items-center justify-between">
        <a href="{{ route('wallets.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Dompet
        </a>
        <form action="{{ route('wallets.statement') }}" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="wallet_slug" value="{{ $wallet->slug }}">
            <input type="month" name="month" value="{{ $month->format('Y-m') }}" required
                class="rounded-xl border-gray-300/60 text-sm px-3 py-2 bg-white/70 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
            <button type="submit"
                class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-md shadow-blue-500/20 transition-all active:scale-95 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Tampilkan
            </button>
            <a href="{{ route('wallets.export.pdf', ['wallet_slug' => $wallet->slug, 'month' => $month->format('Y-m')]) }}"
                class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Unduh PDF
            </a>
        </form>
    </div>

    <!-- Header Card — Glass Style -->
    <div class="relative rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>

        <div class="relative z-10 px-6 py-5 border-b border-gray-200/50 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">{{ $wallet->name }}</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $month->locale('id')->format('F Y') }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Saldo Akhir</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($currentBalance, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Summary -->
        <div class="relative z-10 grid grid-cols-3 gap-4 px-6 py-4 border-b border-gray-200/50">
            <div class="text-center">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Saldo Awal</p>
                <p class="text-sm font-semibold text-gray-900 mt-1">Rp {{ number_format($currentBalance - $income + $expense, 0, ',', '.') }}</p>
            </div>
            <div class="text-center border-x border-gray-200/50">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Pemasukan</p>
                <p class="text-sm font-semibold text-emerald-600 mt-1">+Rp {{ number_format($income, 0, ',', '.') }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Pengeluaran</p>
                <p class="text-sm font-semibold text-red-600 mt-1">-Rp {{ number_format($expense, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Transactions -->
        <div class="relative z-10 px-6 py-5">
            <h2 class="text-sm font-bold text-gray-900 mb-4">Rincian Transaksi</h2>
            @if($records->isEmpty() && $incomeRecords->isEmpty())
                <p class="text-sm text-gray-500 text-center py-8">Tidak ada transaksi pada bulan ini.</p>
            @else
                <div class="space-y-2">
                    @foreach($incomeRecords as $r)
                    <div class="flex items-center gap-3 py-3 px-4 rounded-xl bg-emerald-50/70 border border-emerald-100/50">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $r->sender_name ?? $r->category_name ?? 'Pemasukan' }}</p>
                            <p class="text-xs text-gray-500">{{ $r->category_name }} &middot; {{ \Carbon\Carbon::parse($r->date)->format('d M Y') }}</p>
                        </div>
                        <p class="text-sm font-semibold text-emerald-600 flex-shrink-0">+Rp {{ number_format($r->amount, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                    @foreach($records as $r)
                    <div class="flex items-center gap-3 py-3 px-4 rounded-xl bg-gray-50/70 border border-gray-100/50">
                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $r->title }}</p>
                            <p class="text-xs text-gray-500">{{ $r->category_name }} &middot; {{ \Carbon\Carbon::parse($r->date)->format('d M Y') }}</p>
                        </div>
                        <p class="text-sm font-semibold text-red-600 flex-shrink-0">-Rp {{ number_format($r->amount, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
