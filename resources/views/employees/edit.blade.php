@extends('layouts.app')

@section('page-title', 'Edit Karyawan')

@section('content')
<div class="space-y-6 relative">
    <div class="flex items-center gap-3">
        <a href="{{ route('employees.index') }}"
           class="p-2 rounded-xl hover:bg-gray-100 transition-colors text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Edit Karyawan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $employee->name }}</p>
        </div>
    </div>

    <div class="relative rounded-2xl p-6"
         style="background: rgba(255,255,255,0.55); backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/60 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10">
            <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-6">
                @csrf @method('PUT')

                <div>
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Informasi Pribadi</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="{{ old('name', $employee->name) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">NIP</label>
                            <input type="text" name="nip" value="{{ old('nip', $employee->nip) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jabatan</label>
                            <input type="text" name="position" value="{{ old('position', $employee->position) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp</label>
                            <input type="text" name="whatsapp" value="{{ old('whatsapp', $employee->whatsapp) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Informasi BANK</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Bank</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Rekening</label>
                            <input type="text" name="bank_account" value="{{ old('bank_account', $employee->bank_account) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Masuk Kerja</label>
                            <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}"
                                class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-4 py-2.5 text-sm transition-all">{{ old('notes', $employee->notes) }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200/50">
                    <a href="{{ route('employees.index') }}"
                       class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">Batal</a>
                    <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium shadow-lg shadow-emerald-500/20 transition-all active:scale-95">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
