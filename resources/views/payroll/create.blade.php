@extends('layouts.app')

@section('page-title', 'Tambah Gaji Karyawan')

@section('content')
<div class="space-y-6 relative">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('payroll.index') }}"
           class="p-2 rounded-xl hover:bg-gray-100 transition-colors text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Tambah Gaji Karyawan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Input data penggajian karyawan baru</p>
        </div>
    </div>

    <div class="relative rounded-2xl p-6"
         style="background: rgba(255,255,255,0.55); backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/60 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10">
            <form action="{{ route('payroll.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Employee Info -->
                <div>
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Informasi Karyawan</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Karyawan <span class="text-red-500">*</span></label>
                            <select name="employee_name" id="employeeName" required
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                                <option value="">— Pilih Karyawan —</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->name }}"
                                        data-position="{{ $emp->position }}"
                                        data-whatsapp="{{ $emp->whatsapp }}"
                                        data-phone="{{ $emp->phone }}"
                                        {{ old('employee_name') == $emp->name ? 'selected' : '' }}>
                                        {{ $emp->name }} @if($emp->position) ({{$emp->position}}) @endif
                                    </option>
                                @endforeach
                                <option value="{{ old('employee_name') }}" {{ !empty(old('employee_name')) && !collect($employees)->contains('name', old('employee_name')) ? 'selected' : '' }}>
                                    {{ old('employee_name') ?? '' }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jabatan</label>
                            <input type="text" name="position" id="position" value="{{ old('position') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Telepon</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp</label>
                            <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                    </div>
                </div>

                <!-- Period -->
                <div>
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Periode Gaji</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan <span class="text-red-500">*</span></label>
                            <select name="month" required
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ old('month', $currentMonth) == $m ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create(now()->year, $m, 1)->locale('id')->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                            <input type="number" name="year" required min="2020" max="2030" value="{{ old('year', $currentYear) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Payroll <span class="text-red-500">*</span></label>
                            <input type="date" name="salary_date" required value="{{ old('salary_date', date('Y-m-d')) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                    </div>
                </div>

                <!-- Salary Components -->
                <div>
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Komponen Gaji</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Gaji Pokok (Rp)</label>
                            <input type="number" name="base_salary" min="0" step="1000" value="{{ old('base_salary', 0) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tunjangan Lain (Rp)</label>
                            <input type="number" name="salary_extra" min="0" step="1000" value="{{ old('salary_extra', 0) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Total Sesi (jam/shift)</label>
                            <input type="number" name="total_sessions" min="0" step="0.5" value="{{ old('total_sessions', 0) }}" id="totalSessions"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tarif per Sesi (Rp)</label>
                            <input type="number" name="session_rate" min="0" step="100" value="{{ old('session_rate', 0) }}" id="sessionRate"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">BPJS Kesehatan (Rp)</label>
                            <input type="number" name="transport_allowance" min="0" step="1000" value="{{ old('transport_allowance', 0) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">BPJS Ketenagakerjaan (Rp)</label>
                            <input type="number" name="deductions" min="0" step="1000" value="{{ old('deductions', 0) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                    </div>
                </div>

                <!-- Preview -->
                <div class="rounded-xl bg-gray-50 p-4 space-y-2">
                    <h4 class="text-sm font-bold text-gray-700">Preview Perhitungan</h4>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Gaji Pokok</span>
                        <span class="font-medium" id="previewBase">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tunjangan Lain</span>
                        <span class="font-medium" id="previewExtra">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Bonus Sesi</span>
                        <span class="font-medium text-emerald-600" id="previewSession">Rp 0</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between text-sm">
                        <span class="text-gray-600">Total Kompensasi</span>
                        <span class="font-bold" id="previewTotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">BPJS Kesehatan</span>
                        <span class="font-medium text-red-600" id="previewTransport">-Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">BPJS Ketenagakerjaan</span>
                        <span class="font-medium text-red-600" id="previewDeductions">-Rp 0</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between">
                        <span class="font-bold text-gray-900">Total Bersih</span>
                        <span class="font-bold text-emerald-600 text-lg" id="previewNet">Rp 0</span>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200/50">
                    <a href="{{ route('payroll.index') }}"
                       class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">Batal</a>
                    <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium shadow-lg shadow-emerald-500/20 transition-all active:scale-95">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('employeeName').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected.dataset.position) {
        document.getElementById('position').value = selected.dataset.position || '';
    }
    if (selected.dataset.whatsapp) {
        document.getElementById('whatsapp').value = selected.dataset.whatsapp || '';
    }
    if (selected.dataset.phone) {
        document.getElementById('phone').value = selected.dataset.phone || '';
    }
});

function updatePreview() {
    const base = parseFloat(document.querySelector('input[name="base_salary"]').value) || 0;
    const extra = parseFloat(document.querySelector('input[name="salary_extra"]').value) || 0;
    const sessions = parseFloat(document.getElementById('totalSessions').value) || 0;
    const rate = parseFloat(document.getElementById('sessionRate').value) || 0;
    const transport = parseFloat(document.querySelector('input[name="transport_allowance"]').value) || 0;
    const deductions = parseFloat(document.querySelector('input[name="deductions"]').value) || 0;

    const sessionBonus = sessions * rate;
    const totalComp = base + extra + sessionBonus;
    const net = totalComp - transport - deductions;

    document.getElementById('previewBase').textContent = 'Rp ' + base.toLocaleString('id-ID');
    document.getElementById('previewExtra').textContent = 'Rp ' + extra.toLocaleString('id-ID');
    document.getElementById('previewSession').textContent = 'Rp ' + sessionBonus.toLocaleString('id-ID');
    document.getElementById('previewTransport').textContent = '-Rp ' + transport.toLocaleString('id-ID');
    document.getElementById('previewTotal').textContent = 'Rp ' + totalComp.toLocaleString('id-ID');
    document.getElementById('previewDeductions').textContent = '-Rp ' + deductions.toLocaleString('id-ID');
    document.getElementById('previewNet').textContent = 'Rp ' + net.toLocaleString('id-ID');
}

document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', updatePreview);
});
updatePreview();
</script>
@endsection
