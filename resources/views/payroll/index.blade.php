@extends('layouts.app')

@section('page-title', 'Gaji Karyawan')

@section('content')
<!-- Ambient background -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute inset-0 bg-gradient-to-br from-emerald-100 via-teal-50 to-cyan-100"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-teal-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:2s"></div>
    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:4s"></div>
</div>

<div class="space-y-6 relative">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Gaji Karyawan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola data penggajian karyawan klinik & sekolah</p>
        </div>
        @if(auth()->check() && auth()->user()->isAdmin())
        <div class="flex items-center gap-2">
            <a href="{{ route('payroll.create') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm flex items-center gap-2 shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="hidden sm:inline">Tambah Gaji</span>
            </a>
        </div>
        @endif
    </div>

    @if($generatedCount > 0)
    <div class="rounded-2xl p-4 flex items-center gap-3"
         style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3);">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>
            <p class="text-sm font-medium text-emerald-800">Data gaji bulan ini otomatis dibuat</p>
            <p class="text-xs text-emerald-600 mt-0.5">{{ $generatedCount }} record digenerate dari data bulan sebelumnya</p>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="relative rounded-2xl overflow-hidden p-4 text-white"
             style="background: linear-gradient(135deg, rgba(16,185,129,0.85) 0%, rgba(5,150,105,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(16,185,129,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Total Record</p>
                <p class="text-2xl font-bold mt-1">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-4 text-white"
             style="background: linear-gradient(135deg, rgba(16,185,129,0.85) 0%, rgba(5,150,105,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(16,185,129,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Sudah Dibayar</p>
                <p class="text-2xl font-bold mt-1">{{ $stats['paid'] }}</p>
                <p class="text-white/70 text-xs mt-1">Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-4 text-white"
             style="background: linear-gradient(135deg, rgba(245,158,11,0.85) 0%, rgba(217,119,6,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(245,158,11,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Belum Dibayar</p>
                <p class="text-2xl font-bold mt-1">{{ $stats['unpaid'] }}</p>
                <p class="text-white/70 text-xs mt-1">Rp {{ number_format($stats['total_unpaid'], 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-4 text-white"
             style="background: linear-gradient(135deg, rgba(239,68,68,0.85) 0%, rgba(220,38,38,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(239,68,68,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Total Kasar</p>
                <p class="text-xl font-bold mt-1">Rp {{ number_format($stats['total_paid'] + $stats['total_unpaid'], 0, ',', '.') }}</p>
            </div>
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
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <div class="w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                    <select name="month" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        <option value="">Semua Bulan</option>
                        @foreach($months as $m)
                            <option value="{{ $m['month'] }}-{{ $m['year'] }}"
                                {{ (request('month') == $m['month'] . '-' . $m['year']) || (!request('month') && $m['month'] == $currentMonth && $m['year'] == $currentYear) ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create($m['year'], $m['month'])->locale('id')->format('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        <option value="">Semua</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari Karyawan</label>
                    <input type="text" name="employee_name" value="{{ request('employee_name') }}"
                        placeholder="Nama karyawan..."
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.335a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('month') || request('status') || request('employee_name'))
                <a href="{{ route('payroll.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Reset
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Payroll Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($records as $record)
        <div class="relative rounded-2xl overflow-hidden hover:shadow-md transition-shadow flex flex-col"
             style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                    border: 1px solid rgba(255,255,255,0.7);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
            <div class="relative z-10 p-5 flex flex-col flex-grow">

                <!-- Header: Name + Status -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            {{ substr($record->employee_name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">{{ $record->employee_name }}</h3>
                            <p class="text-xs text-gray-500">{{ $record->position ?? 'Karyawan' }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold flex-shrink-0"
                          style="{{ $record->paid
                              ? 'background:rgba(16,185,129,0.15);color:#059669;border:1px solid rgba(16,185,129,0.3)'
                              : 'background:rgba(245,158,11,0.15);color:#d97706;border:1px solid rgba(245,158,11,0.3)' }}">
                        {{ $record->paid ? 'Lunas' : 'Belum' }}
                    </span>
                </div>

                <!-- Period -->
                <div class="flex items-center gap-2 mb-4 text-xs text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>{{ Carbon\Carbon::create($record->year, $record->month, 1)->locale('id')->format('F Y') }}</span>
                    @if($record->paid_at)
                    <span class="text-emerald-600">• Dibayar {{ $record->paid_at->format('d M Y') }}</span>
                    @endif
                </div>

                <!-- Salary Breakdown -->
                <div class="space-y-1.5 mb-4 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Gaji Pokok</span>
                        <span class="font-medium text-gray-700">Rp {{ number_format($record->base_salary, 0, ',', '.') }}</span>
                    </div>
                    @if($record->session_bonus > 0 || $record->salary_extra > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tunjangan</span>
                        <span class="font-medium text-emerald-600">+Rp {{ number_format($record->session_bonus + $record->salary_extra, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($record->transport_allowance > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500">BPJS Kes.</span>
                        <span class="font-medium text-red-600">-Rp {{ number_format($record->transport_allowance, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($record->deductions > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Potongan</span>
                        <span class="font-medium text-red-600">-Rp {{ number_format($record->deductions, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="border-t border-gray-200/50 pt-1.5 mt-1.5">
                        <div class="flex justify-between">
                            <span class="text-gray-600 font-medium">Total Bersih</span>
                            <span class="font-bold text-emerald-700">Rp {{ number_format($record->net_salary, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-auto flex items-center justify-between pt-4 border-t border-white/50">
                    <div class="text-xs text-gray-400">
                        @if($record->whatsapp)
                        <a href="https://wa.me/{{ $record->whatsapp }}" target="_blank" class="text-green-600 hover:text-green-700">
                            {{ $record->whatsapp }}
                        </a>
                        @else
                        -
                        @endif
                    </div>
                    <div class="flex items-center gap-1">
                        @if($record->paid)
                            <a href="{{ route('payroll.slip-pdf', $record) }}" target="_blank"
                               class="p-2 rounded-lg text-blue-600 hover:bg-blue-50/50 transition-colors" title="Download PDF Slip Gaji">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                            </a>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <form action="{{ route('payroll.markUnpaid', $record) }}?page={{ $records->currentPage() }}" method="POST" class="inline" onsubmit="return confirm('Batalkan pembayaran gaji {{ $record->employee_name }}?')">
                                @csrf
                                <button type="submit" class="p-2 rounded-lg text-red-600 hover:bg-red-50/50 transition-colors" title="Batalkan Pembayaran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </button>
                            </form>
                            @endif
                        @else
                            <button onclick="openMarkPaidModal({{ $record->id }}, '{{ addslashes($record->employee_name) }}', {{ $record->net_salary }})"
                                class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50/50 transition-colors" title="Tandai Lunas">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <a href="{{ route('payroll.edit', $record) }}?page={{ $records->currentPage() }}"
                               class="p-2 rounded-lg text-blue-600 hover:bg-blue-50/50 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('payroll.destroy', $record) }}?page={{ $records->currentPage() }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg text-red-600 hover:bg-red-50/50 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        @endif
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
                     style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2);">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Belum ada data gaji</p>
                    <p class="text-xs text-gray-400 mt-1">Data akan otomatis digenerate dari bulan sebelumnya</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($records->hasPages())
    <div class="mt-6 flex items-center justify-between gap-2">
        <p class="text-xs text-gray-400 shrink-0">
            Halaman {{ $records->currentPage() }} dari {{ $records->lastPage() }} — {{ $records->total() }} data gaji
        </p>
        <nav class="flex items-center gap-1 shrink-0" aria-label="Pagination">
            {{-- Previous --}}
            @if ($records->onFirstPage())
                <span class="px-2.5 py-1.5 rounded-lg text-xs text-gray-300 cursor-not-allowed opacity-50">Prev</span>
            @else
                <a href="{{ $records->previousPageUrl() }}" class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-600 hover:bg-white/60 hover:text-gray-900 transition-colors border border-gray-200">Prev</a>
            @endif

            {{-- Pages --}}
            @foreach ($records->getUrlRange(1, $records->lastPage()) as $page => $url)
                @if ($page == $records->currentPage())
                    <span class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-white"
                         style="background: linear-gradient(135deg, rgba(16,185,129,0.85) 0%, rgba(5,150,105,0.95) 100%);
                                box-shadow: 0 4px 12px rgba(16,185,129,0.3);">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-600 hover:bg-white/60 hover:text-gray-900 transition-colors border border-gray-200">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($records->hasMorePages())
                <a href="{{ $records->nextPageUrl() }}" class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-600 hover:bg-white/60 hover:text-gray-900 transition-colors border border-gray-200">Next</a>
            @else
                <span class="px-2.5 py-1.5 rounded-lg text-xs text-gray-300 cursor-not-allowed opacity-50">Next</span>
            @endif
        </nav>
    </div>
    @endif
</div>

<!-- Mark Paid Modal -->
<div id="markPaidModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('markPaidModal')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <h2 class="text-lg font-bold text-gray-900">Tandai Pembayaran</h2>
            <button onclick="closeModal('markPaidModal')" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="markPaidForm" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="paid_amount" id="paidAmount">
            @if($records->currentPage() > 1)
            <input type="hidden" name="page" value="{{ $records->currentPage() }}">
            @endif
            <p class="text-sm text-gray-600">Konfirmasi pembayaran untuk <strong id="paidEmployeeName"></strong></p>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Dibayar (Rp)</label>
                <input type="number" name="paid_amount" id="paidAmountInput" required min="0"
                    class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm">
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('markPaidModal')"
                    class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">Batal</button>
                <button type="submit"
                    class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium shadow-lg shadow-emerald-500/20 transition-all active:scale-95">
                    Konfirmasi Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openMarkPaidModal(id, name, amount) {
    document.getElementById('markPaidModal').classList.remove('hidden');
    document.getElementById('paidEmployeeName').textContent = name;
    document.getElementById('paidAmountInput').value = amount;
    document.getElementById('paidAmount').value = amount;
    document.getElementById('markPaidForm').action = '/payroll/' + id + '/mark-paid';
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal('markPaidModal');
});
</script>
@endsection
