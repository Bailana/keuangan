@extends('layouts.app')

@section('page-title', 'Perencanaan Keuangan')

@section('content')
<!-- Ambient background -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 via-sky-50 to-emerald-100"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:2s"></div>
    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-violet-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:4s"></div>
</div>

<div class="space-y-6 relative">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Perencanaan Keuangan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Rencana pemasukan & pengeluaran bulan depan</p>
        </div>
        @if(auth()->user()->isAdmin())
        <button onclick="openModal()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm flex items-center gap-2 shadow-lg shadow-blue-500/30 transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">Tambah</span> Rencana
        </button>
        @endif
    </div>

    <!-- Summary Cards — iOS 26 Glass -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <!-- Pemasukan -->
        <div class="relative rounded-2xl overflow-hidden p-5 sm:p-6 text-white"
             style="background: linear-gradient(135deg, rgba(16,185,129,0.85) 0%, rgba(5,150,105,0.90) 50%, rgba(4,120,87,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(16,185,129,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-4 -bottom-8 w-24 h-24 bg-black/5 rounded-full blur-xl pointer-events-none"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Total Pemasukan</p>
                    <p class="text-2xl sm:text-3xl font-bold mt-2 truncate">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
            </div>
        </div>

        <!-- Pengeluaran -->
        <div class="relative rounded-2xl overflow-hidden p-5 sm:p-6 text-white"
             style="background: linear-gradient(135deg, rgba(239,68,68,0.85) 0%, rgba(220,38,38,0.90) 50%, rgba(185,28,28,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(239,68,68,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-4 -bottom-8 w-24 h-24 bg-black/5 rounded-full blur-xl pointer-events-none"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Total Pengeluaran</p>
                    <p class="text-2xl sm:text-3xl font-bold mt-2 truncate">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
            </div>
        </div>

        <!-- Saldo -->
        <div class="relative rounded-2xl overflow-hidden p-5 sm:p-6 text-white"
             style="background: linear-gradient(135deg, {{ $balance >= 0 ? 'rgba(59,130,246,0.85) 0%, rgba(37,99,235,0.90) 50%, rgba(29,78,216,0.95) 100%' : 'rgba(51,65,85,0.85) 0%, rgba(30,41,59,0.90) 50%, rgba(15,23,42,0.95) 100%' }});
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(59,130,246,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-4 -bottom-8 w-24 h-24 bg-black/5 rounded-full blur-xl pointer-events-none"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Saldo</p>
                    <p class="text-2xl sm:text-3xl font-bold mt-2 truncate">Rp {{ number_format($balance, 0, ',', '.') }}</p>
                    <p class="text-white/70 text-xs mt-2 font-medium">
                        @if($balance >= 0)
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                                Positif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Defisit
                            </span>
                        @endif
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter — Glass Card -->
    <div class="relative rounded-2xl p-4"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10">
            <form method="GET" class="flex flex-nowrap gap-2 items-end">
                <div class="min-w-0 flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tipe</label>
                    <select name="type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                        <option value="">Semua Tipe</option>
                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div class="min-w-0 flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                    <select name="category" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                        <option value="">Semua Kategori</option>
                        <option value="spp" {{ request('category') == 'spp' ? 'selected' : '' }}>SPP</option>
                        <option value="terapi" {{ request('category') == 'terapi' ? 'selected' : '' }}>Terapi</option>
                        <option value="gaji_karyawan" {{ request('category') == 'gaji_karyawan' ? 'selected' : '' }}>Gaji Karyawan</option>
                        <option value="bpjs_kesehatan" {{ request('category') == 'bpjs_kesehatan' ? 'selected' : '' }}>BPJS Kesehatan</option>
                        <option value="bpjs_ketenagakerjaan" {{ request('category') == 'bpjs_ketenagakerjaan' ? 'selected' : '' }}>BPJS Ketenagakerjaan</option>
                        <option value="inklusi" {{ request('category') == 'inklusi' ? 'selected' : '' }}>Inklusi</option>
                        <option value="pulsa_pascabayar" {{ request('category') == 'pulsa_pascabayar' ? 'selected' : '' }}>Pulsa & Pascabayar</option>
                        <option value="internet" {{ request('category') == 'internet' ? 'selected' : '' }}>Internet</option>
                        <option value="listrik" {{ request('category') == 'listrik' ? 'selected' : '' }}>Listrik</option>
                        <option value="tunjangan" {{ request('category') == 'tunjangan' ? 'selected' : '' }}>Tunjangan</option>
                        <option value="lain_lain" {{ request('category') == 'lain_lain' ? 'selected' : '' }}>Lain-lain</option>
                    </select>
                </div>
                <div class="min-w-0 flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
                    <select name="year" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                        <option value="">Semua Tahun</option>
                        @foreach($years ?? [] as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0 flex-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                    <select name="month" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                        <option value="">Semua Bulan</option>
                        @foreach($months ?? [] as $m => $label)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.335a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('type') || request('category') || request('year') || request('month'))
                <a href="{{ route('plans.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors shrink-0">
                    Reset
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Table — Glass Card -->
    <div class="relative rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10 overflow-x-auto rounded-2xl">
            <table class="min-w-full divide-y divide-gray-200/50">
                <thead class="bg-white/30">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Judul</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden md:table-cell">Layanan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden lg:table-cell">Periode</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Target</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider hidden xl:table-cell">Catatan</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/50">
                    @forelse($plans as $plan)
                        <tr class="hover:bg-white/30 transition-colors">
                            <td class="px-4 py-4 text-sm text-gray-900 font-medium">{{ $plan->title ?? '-' }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $plan->type === 'income' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $plan->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600 hidden md:table-cell">{{ $plan->category ? ucwords(str_replace('_', ' ', $plan->category)) : '-' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-400 md:hidden">{{ $plan->category ? ucwords(str_replace('_', ' ', $plan->category)) : '-' }} · {{ \Carbon\Carbon::create($plan->year, $plan->month, 1)->locale('id')->format('F Y') }}</td>
                            <td class="px-4 py-4 text-sm text-gray-900 hidden lg:table-cell">{{ \Carbon\Carbon::create($plan->year, $plan->month, 1)->locale('id')->format('F Y') }}</td>
                            <td class="px-4 py-4 text-right text-sm font-semibold text-gray-900">
                                Rp {{ number_format((int)$plan->target_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 hidden xl:table-cell">{{ Str::limit($plan->notes, 40) }}</td>
                            @if(auth()->user()->isAdmin())
                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1">
                                    <button onclick="openEditModal({{ json_encode($plan) }})"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50/50 rounded-lg transition-colors"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button onclick="openDeleteModal({{ $plan->id }}, '{{ addslashes($plan->title ?? '-') }}')"
                                       class="p-1.5 text-red-600 hover:bg-red-50/50 rounded-lg transition-colors"
                                       title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" class="px-4 py-12 text-center text-sm text-gray-500">Belum ada perencanaan keuangan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($plans->hasPages())
        <div class="relative z-10 px-4 py-3 border-t border-gray-200/50">{{ $plans->links() }}</div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Perencanaan?</h3>
                <p class="text-sm text-gray-500 mb-1">Apakah Anda yakin ingin menghapus</p>
                <p id="deletePlanTitle" class="text-sm font-semibold text-gray-700 mb-6"></p>
                <div class="flex gap-3">
                    <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                        Batal
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition-colors">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Edit Perencanaan</h2>
                    <button onclick="closeEditModal()" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form id="editForm" method="POST" class="space-y-5">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Perencanaan</label>
                        <input type="text" name="title" id="editTitle"
                            placeholder="Contoh: Tabungan Pendidikan"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe <span class="text-red-500">*</span></label>
                            <select name="type" id="editType" required
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                                <option value="income">Pemasukan</option>
                                <option value="expense">Pengeluaran</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori</label>
                            <select name="category" id="editService"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                                <option value="">— Pilih —</option>
                                <option value="spp">SPP</option>
                                <option value="terapi">Terapi</option>
                                <option value="gaji_karyawan">Gaji Karyawan</option>
                                <option value="bpjs_kesehatan">BPJS Kesehatan</option>
                                <option value="bpjs_ketenagakerjaan">BPJS Ketenagakerjaan</option>
                                <option value="inklusi">Inklusi</option>
                                <option value="pulsa_pascabayar">Pulsa & Pascabayar</option>
                                <option value="internet">Internet</option>
                                <option value="listrik">Listrik</option>
                                <option value="tunjangan">Tunjangan</option>
                                <option value="lain_lain">Lain-lain</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan <span class="text-red-500">*</span></label>
                            <select name="month" id="editMonth" required
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m, 1)->locale('id')->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun <span class="text-red-500">*</span></label>
                            <input type="number" name="year" id="editYear" required min="2024"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Target (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                            <input type="number" name="target_amount" id="editTargetAmount" required min="0" step="1" value=""
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 pl-10 pr-4 py-2.5 text-sm transition-all"
                                oninput="this.value = Math.round(this.value)">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                        <textarea name="notes" id="editNotes" rows="2"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all"
                            placeholder="Opsional..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeEditModal()"
                            class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-lg shadow-blue-500/20 transition-all active:scale-95">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="planModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Tambah Perencanaan</h2>
                    <button onclick="closeModal()" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('plans.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Perencanaan</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                            placeholder="Contoh: Tabungan Pendidikan"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all bg-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
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
                    <div class="grid grid-cols-2 gap-4">
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
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Target (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                            <input type="number" name="target_amount" required min="0" step="1" value="{{ old('target_amount') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 pl-10 pr-4 py-2.5 text-sm transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                        <textarea name="notes" rows="2"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all"
                            placeholder="Opsional...">{{ old('notes') }}</textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeModal()"
                            class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-lg shadow-blue-500/20 transition-all active:scale-95">
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
    document.getElementById('planModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('planModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function openEditModal(plan) {
    document.getElementById('editForm').action = '/plans/' + plan.id;
    document.getElementById('editTitle').value = plan.title || '';
    document.getElementById('editType').value = plan.type;
    document.getElementById('editService').value = plan.category || '';
    document.getElementById('editMonth').value = plan.month;
    document.getElementById('editYear').value = plan.year;
    document.getElementById('editTargetAmount').value = Math.round(plan.target_amount);
    document.getElementById('editNotes').value = plan.notes || '';
    document.getElementById('editModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function openDeleteModal(id, title) {
    document.getElementById('deleteForm').action = '/plans/' + id;
    document.getElementById('deletePlanTitle').textContent = title;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeEditModal();
        closeDeleteModal();
    }
});
</script>
@endsection
