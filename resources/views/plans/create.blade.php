@extends('layouts.app')

@section('page-title', 'Tambah Perencanaan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('plans.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors flex-shrink-0">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Tambah Perencanaan</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 sm:p-8">
        <form action="{{ route('plans.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Perencanaan</label>
                <input type="text" name="title" value="{{ old('title') }}"
                    placeholder="Contoh: Tabungan Pendidikan"
                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all bg-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe <span class="text-red-500">*</span></label>
                    <select name="type" required
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                        <option value="">— Pilih —</option>
                        <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
                    <select name="category"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                        <option value="">— Pilih —</option>
                        <option value="spp" {{ old('category') == 'spp' ? 'selected' : '' }}>SPP</option>
                        <option value="terapi" {{ old('category') == 'terapi' ? 'selected' : '' }}>Terapi</option>
                        <option value="gaji_karyawan" {{ old('category') == 'gaji_karyawan' ? 'selected' : '' }}>Gaji Karyawan</option>
                        <option value="bpjs_kesehatan" {{ old('category') == 'bpjs_kesehatan' ? 'selected' : '' }}>BPJS Kesehatan</option>
                        <option value="bpjs_ketenagakerjaan" {{ old('category') == 'bpjs_ketenagakerjaan' ? 'selected' : '' }}>BPJS Ketenagakerjaan</option>
                        <option value="inklusi" {{ old('category') == 'inklusi' ? 'selected' : '' }}>Inklusi</option>
                        <option value="pulsa_pascabayar" {{ old('category') == 'pulsa_pascabayar' ? 'selected' : '' }}>Pulsa & Pascabayar</option>
                        <option value="internet" {{ old('category') == 'internet' ? 'selected' : '' }}>Internet</option>
                        <option value="listrik" {{ old('category') == 'listrik' ? 'selected' : '' }}>Listrik</option>
                        <option value="tunjangan" {{ old('category') == 'tunjangan' ? 'selected' : '' }}>Tunjangan</option>
                        <option value="lain_lain" {{ old('category') == 'lain_lain' ? 'selected' : '' }}>Lain-lain</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan <span class="text-red-500">*</span></label>
                    <select name="month" required
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ old('month', date('n')) == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" name="year" required min="2024" value="{{ old('year', date('Y')) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Target (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                        <input type="number" name="target_amount" required min="0" step="1" value="{{ old('target_amount') }}"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 pl-10 pr-4 py-2.5 text-sm transition-all">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="2"
                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all"
                    placeholder="Opsional...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('plans.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection