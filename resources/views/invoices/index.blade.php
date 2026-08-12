@extends('layouts.app')

@section('page-title', 'Invoice & Tagihan')

@section('content')
<!-- Ambient background -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 via-sky-50 to-emerald-100"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-violet-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:2s"></div>
    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:4s"></div>
</div>

<div class="space-y-6 relative">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Invoice & Tagihan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola tagihan pembayaran anak didik per bulan</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <!-- Total Tagihan -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(59,130,246,0.85) 0%, rgba(37,99,235,0.90) 50%, rgba(30,64,175,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(37,99,235,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none rounded-t-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Total Tagihan</p>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 01.5-.5h5a.5.5 0 01.5.5v5a.5.5 0 01-.5.5h-5a.5.5 0 01-.5-.5v-5z"/></svg>
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-bold">Rp {{ number_format($totalPaid + $totalUnpaid, 0, ',', '.') }}</p>
                <p class="text-white/70 text-xs mt-1 font-medium">{{ \Carbon\Carbon::create(null, $currentMonth, 1)->locale('id')->format('F') }} {{ $currentYear }}</p>
            </div>
        </div>

        <!-- Sudah Dibayar -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(16,185,129,0.85) 0%, rgba(5,150,105,0.90) 50%, rgba(4,120,87,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(5,150,105,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none rounded-t-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Sudah Dibayar</p>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-bold">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                <p class="text-white/70 text-xs mt-1 font-medium">Tagihan lunas bulan ini</p>
            </div>
        </div>

        <!-- Belum Dibayar -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white col-span-2 sm:col-span-1"
             style="background: linear-gradient(135deg, rgba(239,68,68,0.85) 0%, rgba(220,38,38,0.90) 50%, rgba(185,28,28,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(220,38,38,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none rounded-t-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Belum Dibayar</p>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-xl sm:text-2xl font-bold">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</p>
                <p class="text-white/70 text-xs mt-1 font-medium">Tagihan belum lunas bulan ini</p>
            </div>
        </div>
    </div>

    <!-- Filter — Glass Card -->
    <div class="relative rounded-2xl p-4"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                    <select name="month" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="w-[100px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
                    <select name="year" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        <option value="">Semua</option>
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        <option value="">Semua</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>
                <div class="w-[180px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Layanan</label>
                    <select name="service" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        <option value="">Semua Layanan</option>
                        @foreach($allServices as $service)
                            <option value="{{ $service }}" {{ request('service') == $service ? 'selected' : '' }}>{{ $service }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari Anak</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama anak atau orang tua..."
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.335a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('month') || request('year') || request('status') || request('service') || request('search'))
                <a href="{{ route('invoices.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Reset
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Invoice Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($children as $child)
        <div class="relative rounded-2xl overflow-hidden hover:shadow-md transition-shadow"
             style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                    border: 1px solid rgba(255,255,255,0.7);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
            <div class="relative z-10 p-5">

                <!-- Header: Name + Status -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            {{ substr($child->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">{{ $child->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $child->parent_name ?? '-' }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold flex-shrink-0"
                          style="{{ $child->payment_status === 'paid'
                              ? 'background:rgba(16,185,129,0.15);color:#059669;border:1px solid rgba(16,185,129,0.3)'
                              : 'background:rgba(239,68,68,0.15);color:#dc2626;border:1px solid rgba(239,68,68,0.3)' }}">
                        {{ $child->payment_status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                    </span>
                </div>

                <!-- Service Badges -->
                <div class="flex flex-wrap gap-1.5 mb-4">
                    @foreach($child->therapyTypes as $t)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                              style="background: rgba(147,51,234,0.15); color: #7c3aed; border: 1px solid rgba(147,51,234,0.3);">
                            {{ $t->name }}
                        </span>
                    @endforeach
                    @foreach($child->vocationalTypes as $v)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                              style="background: rgba(245,158,11,0.15); color: #d97706; border: 1px solid rgba(245,158,11,0.3);">
                            {{ $v->name }}
                        </span>
                    @endforeach
                    @if($child->isTakingSekolah())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                              style="background: rgba(59,130,246,0.15); color: #2563eb; border: 1px solid rgba(59,130,246,0.3);">
                            Sekolah
                        </span>
                    @endif
                </div>

                <!-- Rincian -->
                <div class="space-y-1 mb-4 text-xs text-gray-600">
                    @if($child->therapyTypes->count() > 0)
                        @foreach($child->therapyTypes as $t)
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-400 flex-shrink-0"></span>
                                <span>{{ $t->name }} <span class="text-gray-400">({{ $t->pivot->monthly_sessions ?? 4 }} sesi)</span></span>
                            </div>
                        @endforeach
                    @endif
                    @if($child->isTakingSekolah())
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                            <span>Sekolah</span>
                        </div>
                    @endif
                    @if($child->vocationalTypes->count() > 0)
                        @foreach($child->vocationalTypes as $v)
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 flex-shrink-0"></span>
                                <span>{{ $v->name }} <span class="text-gray-400">({{ $v->pivot->monthly_sessions ?? 4 }} sesi)</span></span>
                            </div>
                        @endforeach
                    @endif
                    @if($child->therapyTypes->count() === 0 && $child->vocationalTypes->count() === 0 && !$child->isTakingSekolah())
                        <span class="text-gray-400">-</span>
                    @endif
                </div>

                <!-- Belum Bayar Months -->
                @if($child->unpaid_months && count($child->unpaid_months) > 0)
                <div class="mb-4">
                    <p class="text-xs text-gray-500 mb-2">Belum bayar:</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($child->unpaid_months as $month)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium"
                                  style="background: rgba(239,68,68,0.15); color: #dc2626; border: 1px solid rgba(239,68,68,0.3);">
                                {{ \Carbon\Carbon::create(null, $month, 1)->format('M') }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Bottom: Amount + Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-white/50">
                    <div>
                        <div class="text-xs text-gray-500">Tagihan</div>
                        <div class="font-bold text-gray-900 text-sm">
                            Rp {{ number_format($child->invoice_amount ?? $child->calculateInvoiceAmount($currentMonth, $currentYear), 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <!-- PDF -->
                        <a href="{{ route('invoices.generate', ['child' => $child->id, 'month' => $currentMonth, 'year' => $currentYear]) }}"
                           target="_blank"
                           class="p-2 rounded-lg text-blue-600 hover:bg-blue-50/50 transition-colors" title="Download PDF">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        </a>

                        @if(auth()->user()->isAdmin())
                            @if($child->payment_status === 'paid')
                                <!-- Mark Unpaid -->
                                <form action="{{ route('invoices.markUnpaid', $child->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="month" value="{{ $currentMonth }}">
                                    <input type="hidden" name="year" value="{{ $currentYear }}">
                                    <button type="submit" class="p-2 rounded-lg text-red-600 hover:bg-red-50/50 transition-colors" title="Tandai Belum Bayar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                            @else
                                <!-- Mark Paid -->
                                <form action="{{ route('invoices.markPaid', $child->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="month" value="{{ $currentMonth }}">
                                    <input type="hidden" name="year" value="{{ $currentYear }}">
                                    <button type="submit" class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50/50 transition-colors" title="Tandai Lunas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                            @endif
                        @endif

                        <!-- WhatsApp -->
                        <a href="{{ route('invoices.whatsapp', ['child' => $child->id, 'month' => $currentMonth, 'year' => $currentYear]) }}"
                           target="_blank"
                           class="p-2 rounded-lg text-green-600 hover:bg-green-50/50 transition-colors" title="Kirim via WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 rounded-2xl"
             style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                    border: 1px solid rgba(255,255,255,0.7);">
            <div class="flex flex-col items-center gap-3">
                <div class="w-16 h-16 rounded-full flex items-center justify-center"
                     style="background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.2);">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Belum ada data anak</p>
                    <p class="text-xs text-gray-400 mt-1">Tambahkan data anak terlebih dahulu di menu Data Anak</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

@endsection
