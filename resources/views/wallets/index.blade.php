@extends('layouts.app')

@section('page-title', 'Dompet')

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
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Dompet & Saldo</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola saldo setiap dompet dan unduh e-statement bulanan</p>
        </div>
        @if(auth()->user()->isAdmin())
        <button onclick="openModal('createModal')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm flex items-center gap-2 shadow-lg shadow-blue-500/30 transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">Tambah</span> Dompet
        </button>
        @endif
    </div>

    <!-- Wallet Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        @foreach($wallets as $wallet)
        @php
            $accentColor = $wallet->slug === 'bsi' ? 'blue' : ($wallet->slug === 'mandiri' ? 'amber' : 'emerald');
        @endphp
        <div class="relative rounded-2xl overflow-hidden"
             style="background: rgba(255,255,255,0.55); backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                    border: 1px solid rgba(255,255,255,0.7);">

            <!-- Gradient accent bar -->
            <div class="absolute top-0 left-0 w-full h-1"
                 style="background: {{ $wallet->slug === 'bsi' ? 'linear-gradient(90deg, #60a5fa, #3b82f6)' : ($wallet->slug === 'mandiri' ? 'linear-gradient(90deg, #fbbf24, #f59e0b)' : 'linear-gradient(90deg, #34d399, #10b981)') }};"></div>

            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/60 to-transparent pointer-events-none rounded-t-2xl"></div>

            <!-- Wallet Header -->
            <div class="relative z-10 px-6 py-5">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <!-- Icon -->
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg"
                             style="background: {{ $wallet->slug === 'bsi' ? 'linear-gradient(135deg, #60a5fa, #2563eb)' : ($wallet->slug === 'mandiri' ? 'linear-gradient(135deg, #fbbf24, #d97706)' : 'linear-gradient(135deg, #34d399, #059669)') }};">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>

                        <!-- Info -->
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Nama Dompet</p>
                            <h2 class="text-base font-bold text-gray-900 truncate">{{ $wallet->name }}</h2>

                            @if($wallet->owner_name)
                            <div class="flex items-center gap-1.5 mt-1.5 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="font-medium truncate">{{ $wallet->owner_name }}</span>
                            </div>
                            @endif

                            @if($wallet->account_number)
                            <div class="flex items-center gap-1.5 mt-1 text-xs text-gray-400">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <span class="font-mono">{{ $wallet->account_number }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    @if(auth()->user()->isAdmin())
                    <div class="flex items-center gap-1 ml-4">
                        <form action="{{ route('wallets.setDefault', $wallet) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="p-2 rounded-xl transition-colors {{ $wallet->is_default ? 'bg-blue-100 hover:bg-blue-200' : 'hover:bg-gray-100' }}"
                                title="{{ $wallet->is_default ? 'Dompet default' : 'Tetapkan sebagai dompet default' }}">
                                @if($wallet->is_default)
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                @endif
                            </button>
                        </form>
                        <button onclick="openModal('editModal-{{ $wallet->slug }}')"
                            class="p-2 rounded-xl hover:bg-gray-100 transition-colors"
                            title="Edit dompet">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form action="{{ route('wallets.destroy', $wallet) }}" method="POST" class="inline" onsubmit="return confirm('Hapus dompet {{ $wallet->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl hover:bg-red-50 transition-colors" title="Hapus dompet">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Balance -->
            <div class="relative z-10 px-6 py-5 border-t border-gray-200/50 bg-gradient-to-r from-gray-50/50 to-transparent">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Saldo Tersedia</p>
                        <p class="text-3xl font-bold text-gray-900">
                            Rp {{ number_format($wallet->current_balance, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 mb-1">Status</p>
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium"
                              style="background: {{ $wallet->current_balance >= 0 ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)' }};
                                     color: {{ $wallet->current_balance >= 0 ? '#059669' : '#dc2626' }};">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($wallet->current_balance >= 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                @endif
                            </svg>
                            {{ $wallet->current_balance >= 0 ? 'Positif' : 'Negatif' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="relative z-10 px-6 py-4 border-t border-gray-200/50 space-y-3">
                <button onclick="document.getElementById('balanceModal-{{ $wallet->slug }}').classList.remove('hidden')"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300/60 text-gray-700 text-sm font-medium hover:bg-white/60 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Set Saldo Awal
                </button>

                <a href="{{ route('wallets.statement', ['wallet_slug' => $wallet->slug, 'month' => date('Y-m')]) }}"
                    class="w-full px-4 py-3 rounded-xl {{ $accentColor === 'blue' ? 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/20' : ($accentColor === 'amber' ? 'bg-amber-600 hover:bg-amber-700 shadow-amber-500/20' : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-500/20') }} text-white text-sm font-medium shadow-md transition-all active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    E-Statement
                </a>
            </div>
        </div>

        <!-- Balance Modal -->
        <div id="balanceModal-{{ $wallet->slug }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('balanceModal-{{ $wallet->slug }}')"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 animate-fade-in-up">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-bold text-gray-900">Set Saldo Awal</h3>
                    <button onclick="closeModal('balanceModal-{{ $wallet->slug }}')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('wallets.setBalance', $wallet) }}">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Saldo (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                            <input type="number" name="balance" required min="0" step="0.01"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 pl-10 pr-4 py-2.5 text-sm transition-all">
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="closeModal('balanceModal-{{ $wallet->slug }}')"
                            class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-md transition-all active:scale-95">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Wallet Modal -->
        <div id="editModal-{{ $wallet->slug }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('editModal-{{ $wallet->slug }}')"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 animate-fade-in-up">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-bold text-gray-900">Edit Dompet</h3>
                    <button onclick="closeModal('editModal-{{ $wallet->slug }}')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('wallets.update', $wallet) }}">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Dompet <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $wallet->name) }}" required
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pemilik <span class="text-red-500">*</span></label>
                            <input type="text" name="owner_name" value="{{ old('owner_name', $wallet->owner_name) }}" required
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all"
                                placeholder="Contoh: Ahmad Fauzi">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Rekening <span class="text-red-500">*</span></label>
                            <input type="text" name="account_number" value="{{ old('account_number', $wallet->account_number) }}" required
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all"
                                placeholder="Contoh: 1234567890">
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end mt-5">
                        <button type="button" onclick="closeModal('editModal-{{ $wallet->slug }}')"
                            class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-md transition-all active:scale-95">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Create Wallet Modal -->
<div id="createModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('createModal')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 animate-fade-in-up">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-gray-900">Tambah Dompet</h3>
            <button onclick="closeModal('createModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('wallets.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Dompet <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all"
                        placeholder="Contoh: Bank BCA">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pemilik <span class="text-red-500">*</span></label>
                    <input type="text" name="owner_name" value="{{ old('owner_name') }}" required
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all"
                        placeholder="Contoh: Ahmad Fauzi">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Rekening <span class="text-red-500">*</span></label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}" required
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all"
                        placeholder="Contoh: 1234567890">
                </div>
            </div>
            <div class="flex gap-3 justify-end mt-5">
                <button type="button" onclick="closeModal('createModal')"
                    class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-md transition-all active:scale-95">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    }
});
</script>
@endsection
