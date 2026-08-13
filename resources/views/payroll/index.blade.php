@extends('layouts.app')

@section('page-title', 'Gaji Karyawan')

@section('content')
<div class="space-y-6 relative">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Gaji Karyawan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola data penggajian karyawan klinik & sekolah</p>
        </div>
        @if(auth()->check() && auth()->user()->isAdmin())
        <a href="{{ route('payroll.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm flex items-center gap-2 shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">Tambah Gaji</span>
        </a>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="relative rounded-2xl overflow-hidden p-4 text-white"
             style="background: linear-gradient(135deg, rgba(99,102,241,0.85) 0%, rgba(79,70,229,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(99,102,241,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
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
                <div class="w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                    <select name="month" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        <option value="">Semua Bulan</option>
                        @foreach($months as $m)
                            <option value="{{ $m['month'] }}-{{ $m['year'] }}"
                                {{ request('month') == $m['month'] . '-' . $m['year'] ? 'selected' : '' }}>
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
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
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

    <!-- Table -->
    <div class="relative rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="relative z-10 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/50">
                <thead class="bg-indigo-50/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Karyawan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Bulan</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Gaji Pokok</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Tunjangan</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">BPJS Kes.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">BPJS Ket.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Total Bersih</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/50">
                    @forelse($records as $record)
                    <tr class="hover:bg-indigo-50/20 transition-colors">
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-900">{{ $record->employee_name }}</div>
                            @if($record->whatsapp)
                            <div class="text-xs text-gray-500">{{ $record->whatsapp }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $record->position ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ Carbon\Carbon::create($record->year, $record->month, 1)->locale('id')->format('F Y') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm font-medium text-gray-900">
                            Rp {{ number_format($record->base_salary, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm text-emerald-600">
                            +Rp {{ number_format($record->session_bonus + $record->salary_extra, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm text-red-600">
                            -Rp {{ number_format($record->transport_allowance, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm text-red-600">
                            -Rp {{ number_format($record->deductions, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-gray-900">
                            Rp {{ number_format($record->net_salary, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($record->paid)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    Lunas
                                </span>
                                <div class="text-xs text-gray-400 mt-1">{{ $record->paid_at?->format('d M Y') }}</div>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                    Belum
                                </span>
                            @endif
                        </td>
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-1">
                                @if(!$record->paid)
                                <button onclick="openMarkPaidModal({{ $record->id }}, '{{ addslashes($record->employee_name) }}', {{ $record->net_salary }})"
                                    class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs transition-colors">
                                    Bayar
                                </button>
                                @endif
                                <a href="{{ route('payroll.edit', $record) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50/50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('payroll.destroy', $record) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50/50 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 10 : 9 }}" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada data gaji.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
        <div class="relative z-10 px-4 py-3 border-t border-gray-200/50">{{ $records->links() }}</div>
        @endif
    </div>
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
