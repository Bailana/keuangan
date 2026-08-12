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

    <!-- Period Selector — Glass Card -->
    <div class="relative rounded-2xl p-5"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10 flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-700">Periode:</span>
                <div class="flex items-center gap-2">
                    <select id="monthSelect" class="rounded-xl border-gray-300 text-sm px-4 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 min-w-[140px] bg-white/60">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->format('F') }}
                            </option>
                        @endfor
                    </select>
                    <select id="yearSelect" class="rounded-xl border-gray-300 text-sm px-4 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 w-24 bg-white/60">
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <a id="updateLink" href="{{ route('invoices.index', ['month' => $currentMonth, 'year' => $currentYear]) }}"
               class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition-colors shadow-sm">
                Tampilkan
            </a>
        </div>
    </div>

    <!-- Invoice Table — Glass Card -->
    <div class="relative rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10 overflow-x-auto rounded-2xl">
            <table class="w-full text-sm text-left">
                <thead class="bg-white/30">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-600 w-[220px]">Nama Anak</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 w-[200px]">Layanan</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 w-[220px]">Rincian</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 w-[100px]">Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 w-[220px]">Belum Bayar</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-right w-[140px]">Tagihan</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-center w-[140px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/50">
                    @forelse($children as $child)
                    <tr class="hover:bg-white/30 transition-colors">
                        <!-- Nama Anak -->
                        <td class="px-6 py-5">
                            <div class="font-semibold text-gray-900">{{ $child->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $child->parent_name ?? '-' }}</div>
                        </td>

                        <!-- Layanan Badges -->
                        <td class="px-6 py-5">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($child->therapyTypes as $t)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                          style="background: rgba(147,51,234,0.15); color: #7c3aed; border: 1px solid rgba(147,51,234,0.3);">
                                        {{ $t->name }}
                                    </span>
                                @endforeach
                                @foreach($child->vocationalTypes as $v)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                          style="background: rgba(245,158,11,0.15); color: #d97706; border: 1px solid rgba(245,158,11,0.3);">
                                        {{ $v->name }}
                                    </span>
                                @endforeach
                                @if($child->isTakingSekolah())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                          style="background: rgba(59,130,246,0.15); color: #2563eb; border: 1px solid rgba(59,130,246,0.3);">
                                        Sekolah
                                    </span>
                                @endif
                                @if($child->therapyTypes->count() === 0 && $child->vocationalTypes->count() === 0 && !$child->isTakingSekolah())
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </div>
                        </td>

                        <!-- Rincian Layanan -->
                        <td class="px-6 py-5">
                            <div class="space-y-1 text-xs text-gray-600">
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
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold
                                @if($child->payment_status === 'paid') bg-emerald-100 text-emerald-700
                                @else bg-red-100 text-red-700
                                @endif"
                                style="{{ $child->payment_status === 'paid' ? 'background:rgba(16,185,129,0.15);color:#059669;border:1px solid rgba(16,185,129,0.3)' : 'background:rgba(239,68,68,0.15);color:#dc2626;border:1px solid rgba(239,68,68,0.3)' }}">
                                {{ $child->payment_status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        </td>

                        <!-- Belum Bayar -->
                        <td class="px-6 py-5">
                            @if($child->unpaid_months && count($child->unpaid_months) > 0)
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($child->unpaid_months as $month)
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium"
                                              style="background: rgba(239,68,68,0.15); color: #dc2626; border: 1px solid rgba(239,68,68,0.3);">
                                            {{ \Carbon\Carbon::create(null, $month, 1)->format('M') }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Tidak ada</span>
                            @endif
                        </td>

                        <!-- Tagihan -->
                        <td class="px-6 py-5 text-right">
                            <div class="font-bold text-gray-900">
                                Rp {{ number_format($child->invoice_amount ?? $child->calculateInvoiceAmount($currentMonth, $currentYear), 0, ',', '.') }}
                            </div>
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-center gap-2">
                                @if($child->payment_status === 'paid')
                                    <!-- Download Invoice -->
                                    <a href="{{ route('invoices.generate', ['child' => $child->id, 'month' => $currentMonth, 'year' => $currentYear]) }}"
                                       target="_blank"
                                       class="group flex items-center gap-1.5 px-3 py-1.5 text-blue-600 hover:bg-blue-50/50 rounded-lg transition-all text-xs font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                                        <span class="hidden sm:inline">PDF</span>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                    <!-- Mark Unpaid -->
                                    <form action="{{ route('invoices.markUnpaid', $child->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="month" value="{{ $currentMonth }}">
                                        <input type="hidden" name="year" value="{{ $currentYear }}">
                                        <button type="submit" class="group flex items-center gap-1.5 px-3 py-1.5 text-red-600 hover:bg-red-50/50 rounded-lg transition-all text-xs font-medium" title="Tandai Belum Bayar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span class="hidden sm:inline">Belum</span>
                                        </button>
                                    </form>
                                    @endif
                                @else
                                    <!-- Download Invoice -->
                                    <a href="{{ route('invoices.generate', ['child' => $child->id, 'month' => $currentMonth, 'year' => $currentYear]) }}"
                                       target="_blank"
                                       class="group flex items-center gap-1.5 px-3 py-1.5 text-blue-600 hover:bg-blue-50/50 rounded-lg transition-all text-xs font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                                        <span class="hidden sm:inline">PDF</span>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                    <!-- Mark Paid -->
                                    <form action="{{ route('invoices.markPaid', $child->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="month" value="{{ $currentMonth }}">
                                        <input type="hidden" name="year" value="{{ $currentYear }}">
                                        <button type="submit" class="group flex items-center gap-1.5 px-3 py-1.5 text-emerald-600 hover:bg-emerald-50/50 rounded-lg transition-all text-xs font-medium" title="Tandai Lunas">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span class="hidden sm:inline">Lunas</span>
                                        </button>
                                    </form>
                                    @endif
                                @endif
                                <!-- WhatsApp -->
                                <a href="{{ route('invoices.whatsapp', ['child' => $child->id, 'month' => $currentMonth, 'year' => $currentYear]) }}"
                                   target="_blank"
                                   class="group flex items-center gap-1.5 px-3 py-1.5 text-green-600 hover:bg-green-50/50 rounded-lg transition-all text-xs font-medium"
                                   title="Kirim via WhatsApp">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    <span class="hidden sm:inline">WA</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
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
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('monthSelect').addEventListener('change', updateLink);
document.getElementById('yearSelect').addEventListener('change', updateLink);

function updateLink() {
    const month = document.getElementById('monthSelect').value;
    const year = document.getElementById('yearSelect').value;
    document.getElementById('updateLink').href = "{{ route('invoices.index') }}?month=" + month + "&year=" + year;
}
</script>
@endsection
