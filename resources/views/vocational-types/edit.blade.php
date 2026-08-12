@extends('layouts.app')

@section('page-title', 'Edit Jenis Vokasi')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('vocational-types.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors flex-shrink-0">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Edit Jenis Vokasi</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 sm:p-8">
        <form action="{{ route('vocational-types.update', $vocationalType) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Vokasi <span class="text-red-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name', $vocationalType->name) }}"
                    class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 px-4 py-2.5 text-sm transition-all">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga per Sesi <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                    <input type="number" name="price_per_session" required min="0" step="1000" value="{{ old('price_per_session', $vocationalType->price_per_session) }}"
                        class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 pl-10 pr-4 py-2.5 text-sm transition-all">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="3"
                    class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 px-4 py-2.5 text-sm transition-all">{{ old('notes', $vocationalType->notes) }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('vocational-types.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
