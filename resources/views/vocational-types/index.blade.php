@extends('layouts.app')

@section('page-title', 'Jenis Vokasi')

@section('content')
<!-- Ambient background -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 via-sky-50 to-emerald-100"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-amber-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-orange-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:2s"></div>
    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-yellow-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:4s"></div>
</div>

<div class="space-y-6 relative">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Jenis Vokasi</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola jenis vokasi dan harga per sesi</p>
        </div>
        @if(auth()->user()->isAdmin())
        <button onclick="openModal()"
            class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm flex items-center gap-2 shadow-lg shadow-amber-500/30 transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Vokasi
        </button>
        @endif
    </div>

    <!-- Cards — Glass Style -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($vocationalTypes as $vokasi)
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(245,158,11,0.85) 0%, rgba(217,119,6,0.90) 50%, rgba(180,83,9,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(245,158,11,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-semibold text-base">{{ $vokasi->name }}</h3>
                        <p class="text-white/70 text-sm mt-1">{{ $vokasi->notes ?? 'Tidak ada catatan' }}</p>
                    </div>
                </div>
                <div class="rounded-xl p-3 mb-3" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(12px);">
                    <div class="text-white/70 text-xs font-medium">Harga per Sesi</div>
                    <div class="text-xl font-bold text-white">Rp {{ number_format($vokasi->price_per_session, 0, ',', '.') }}</div>
                </div>
                @if(auth()->user()->isAdmin())
                <div class="flex gap-2">
                    <button onclick="openEditModal({{ $vokasi->id }}, '{{ addslashes($vokasi->name) }}', {{ $vokasi->price_per_session }}, '{{ addslashes($vokasi->notes ?? '') }}')"
                       class="flex-1 flex items-center justify-center gap-2 px-3 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition-all border border-white/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>
                    <form action="{{ route('vocational-types.destroy', $vokasi) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-red-500/80 hover:bg-red-500 text-white text-sm font-medium rounded-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full relative rounded-2xl p-12 text-center"
             style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                    border: 1px solid rgba(255,255,255,0.7);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3"
                     style="background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2);">
                    <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="text-sm text-gray-500">Belum ada jenis vokasi</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Vokasi -->
<div id="vokasiModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">Tambah Jenis Vokasi</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('vocational-types.store') }}" method="POST" id="vokasiForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Vokasi <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}"
                            class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 px-4 py-2.5 text-sm transition-all"
                            placeholder="Contoh: Painting, Komputer, Cooking">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga per Sesi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                            <input type="number" name="price_per_session" required min="0" step="1000" value="{{ old('price_per_session') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 pl-10 pr-4 py-2.5 text-sm transition-all"
                                placeholder="100000">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                        <textarea name="notes" rows="3"
                            class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 px-4 py-2.5 text-sm transition-all"
                            placeholder="Deskripsi singkat tentang jenis vokasi ini...">{{ old('notes') }}</textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeModal()"
                            class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('vokasiModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('vokasiModal').classList.add('hidden');
    document.getElementById('editVokasiModal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('vokasiForm').reset();
}

function openEditModal(id, name, price, notes) {
    document.getElementById('editVokasiModal').classList.remove('hidden');
    document.getElementById('edit_vokasi_id').value = id;
    document.getElementById('edit_vokasi_name').value = name;
    document.getElementById('edit_vokasi_price').value = price;
    document.getElementById('edit_vokasi_notes').value = notes;
    document.getElementById('editVokasiForm').action = '/vocational-types/' + id;
    document.body.style.overflow = 'hidden';
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>

<!-- Modal Edit Vokasi -->
<div id="editVokasiModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">Edit Jenis Vokasi</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form id="editVokasiForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_vokasi_id" name="vokasi_id">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Vokasi <span class="text-red-500">*</span></label>
                        <input type="text" id="edit_vokasi_name" name="name" required
                            class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 px-4 py-2.5 text-sm transition-all"
                            placeholder="Contoh: Painting, Komputer, Cooking">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga per Sesi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                            <input type="number" id="edit_vokasi_price" name="price_per_session" required min="0" step="1000"
                                class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 pl-10 pr-4 py-2.5 text-sm transition-all"
                                placeholder="100000">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                        <textarea id="edit_vokasi_notes" name="notes" rows="3"
                            class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 px-4 py-2.5 text-sm transition-all"
                            placeholder="Deskripsi singkat tentang jenis vokasi ini..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeModal()"
                            class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
