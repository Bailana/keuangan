@extends('layouts.app')

@section('page-title', 'Data Anak')

@section('content')
<!-- Ambient background -->
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 via-sky-50 to-emerald-100"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:2s"></div>
    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay:4s"></div>
</div>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Data Anak</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola data anak didik dan layanan yang diikuti</p>
        </div>
        @if(auth()->user()->isAdmin())
        <div class="flex items-center gap-2">
            <a href="{{ route('children.export.pdf', request()->query()) }}"
                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2.5 rounded-xl font-medium text-sm flex items-center gap-2 shadow-lg shadow-red-600/20 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span class="hidden sm:inline">PDF</span>
            </a>
            <a href="{{ route('children.export.excel', request()->query()) }}"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-2.5 rounded-xl font-medium text-sm flex items-center gap-2 shadow-lg shadow-emerald-600/20 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                <span class="hidden sm:inline">Excel</span>
            </a>
            <button onclick="openModal('create')"
                class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl font-medium text-sm flex items-center gap-2 shadow-lg shadow-slate-800/20 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="hidden sm:inline">Tambah</span> Anak
            </button>
        </div>
        @endif
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
        <!-- Total Terapi -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(147,51,234,0.85) 0%, rgba(124,58,237,0.90) 50%, rgba(109,40,217,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(124,58,237,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Total Terapi</p>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold">Rp {{ number_format($totalTerapi, 0, ',', '.') }}</p>
                <p class="text-white/70 text-xs mt-1 font-medium">Pendapatan terapi seluruh anak</p>
            </div>
        </div>

        <!-- Total SPP -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(37,99,235,0.85) 0%, rgba(29,78,216,0.90) 50%, rgba(30,64,175,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(37,99,235,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Total SPP</p>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold">Rp {{ number_format($totalSekolah, 0, ',', '.') }}</p>
                <p class="text-white/70 text-xs mt-1 font-medium">Pendapatan SPP seluruh anak</p>
            </div>
        </div>

        <!-- Total Vokasi -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(217,119,6,0.85) 0%, rgba(180,80,0,0.90) 50%, rgba(161,67,2,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(217,119,6,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Total Vokasi</p>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold">Rp {{ number_format($totalVokasi, 0, ',', '.') }}</p>
                <p class="text-white/70 text-xs mt-1 font-medium">Pendapatan vokasi seluruh anak</p>
            </div>
        </div>

        <!-- Total Subsidi -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(5,150,105,0.85) 0%, rgba(4,120,87,0.90) 50%, rgba(6,95,70,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(5,150,105,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Total Subsidi</p>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold">Rp {{ number_format($totalSubsidi, 0, ',', '.') }}</p>
                <p class="text-white/70 text-xs mt-1 font-medium">Total subsidi seluruh anak</p>
            </div>
        </div>

        <!-- Total Invoice (Tagihan) -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(220,38,38,0.85) 0%, rgba(185,28,28,0.90) 50%, rgba(153,27,27,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(185,28,28,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Total Tagihan</p>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 01.5-.5h5a.5.5 0 01.5.5v5a.5.5 0 01-.5.5h-5a.5.5 0 01-.5-.5v-5z"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold">Rp {{ number_format($totalInvoice, 0, ',', '.') }}</p>
                <p class="text-white/70 text-xs mt-1 font-medium">Tagihan Bulan Ini</p>
            </div>
        </div>

        <!-- Total Parent Support -->
        <div class="relative rounded-2xl overflow-hidden p-5 text-white"
             style="background: linear-gradient(135deg, rgba(14,165,233,0.85) 0%, rgba(2,132,199,0.90) 50%, rgba(3,105,161,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(14,165,233,0.25), 0 2px 8px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/15 to-transparent pointer-events-none"></div>
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-white/75 text-xs font-semibold uppercase tracking-widest">Total Parent Support</p>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                         style="background: rgba(255,255,255,0.18); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.3); box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                </div>
                <p class="text-2xl font-bold">Rp {{ number_format($totalParentSupport, 0, ',', '.') }}</p>
                <p class="text-white/70 text-xs mt-1 font-medium">Pendapatan parent support seluruh anak</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="relative rounded-2xl p-4 mb-6"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                -webkit-backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
        <div class="relative z-10">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nama Anak</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama anak..."
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                </div>
                <div class="w-[180px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Layanan</label>
                    <select name="category" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                        <option value="">Semua Layanan</option>
                        <option value="terapi" {{ request('category') == 'terapi' ? 'selected' : '' }}>Terapi</option>
                        <option value="spp" {{ request('category') == 'spp' ? 'selected' : '' }}>Sekolah</option>
                        <option value="vokasi" {{ request('category') == 'vokasi' ? 'selected' : '' }}>Vokasi</option>
                    </select>
                </div>
                <div class="w-[180px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">No. HP Orang Tua</label>
                    <input type="text" name="parent_phone" value="{{ request('parent_phone') }}"
                        placeholder="08xxxxxxxxxx"
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.335a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('search') || request('category') || request('month') || request('year') || request('parent_phone'))
                <a href="{{ route('children.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Reset
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Children Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($children as $child)
        <div class="relative rounded-2xl overflow-hidden p-5 hover:shadow-md transition-shadow"
             style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.8);
                    border: 1px solid rgba(255,255,255,0.7);">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/50 to-transparent pointer-events-none rounded-t-2xl"></div>
            <div class="relative z-10">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ substr($child->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $child->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $child->class_name ?? 'Tidak ada kelas' }}</p>
                    </div>
                </div>
                <form action="{{ route('children.toggle-active', $child) }}{{ request()->query() ? '?' . http_build_query(request()->query()) : '' }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-3 py-1 rounded-full text-xs font-semibold transition-all active:scale-95
                               {{ $child->is_active
                                   ? 'text-emerald-700 bg-emerald-100 border border-emerald-200 hover:bg-emerald-200'
                                   : 'text-gray-500 bg-gray-100 border border-gray-200 hover:bg-gray-200' }}">
                        {{ $child->is_active ? 'Aktif' : 'Nonaktif' }}
                    </button>
                </form>
            </div>

            <div class="space-y-2 text-sm text-gray-600 mb-4">
                @if($child->parent_name)
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>{{ $child->parent_name }}</span>
                </div>
                @endif
                @if($child->parent_whatsapp)
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>{{ $child->formattedWhatsapp }}</span>
                </div>
                @endif
            </div>

            <div class="flex flex-wrap gap-2 mb-4">
                @if($child->isTakingTerapi())
                <span class="px-2 py-1 rounded-lg text-xs font-medium" style="background: rgba(168,85,247,0.12); color: #7c3aed; border: 1px solid rgba(168,85,247,0.25);">Terapi</span>
                @endif
                @if($child->isTakingSekolah())
                <span class="px-2 py-1 rounded-lg text-xs font-medium" style="background: rgba(59,130,246,0.12); color: #2563eb; border: 1px solid rgba(59,130,246,0.25);">Sekolah</span>
                @endif
                @if($child->isTakingVokasi())
                <span class="px-2 py-1 rounded-lg text-xs font-medium" style="background: rgba(245,158,11,0.12); color: #d97706; border: 1px solid rgba(245,158,11,0.25);">Vokasi</span>
                @endif
            </div>

            @if($child->has_subsidi && $child->subsidi_amount > 0)
            <div class="text-xs text-emerald-600 mb-4">
                Subsidi: Rp {{ number_format($child->subsidi_amount, 0, ',', '.') }}
            </div>
            @endif

            <div class="flex items-center justify-between pt-4 border-t border-white/50">
                <div>
                    <div class="text-xs text-gray-500">Tagihan Bulan Ini</div>
                    <div class="font-bold text-gray-900">Rp {{ number_format($child->getCurrentInvoiceAmount(), 0, ',', '.') }}</div>
                </div>
                <div class="flex gap-2">
                    @if(auth()->user()->isAdmin())
                    @if($child->parent_whatsapp)
                    <a href="{{ $child->whatsapp_url }}" target="_blank" rel="noopener noreferrer"
                        class="p-2 rounded-lg hover:bg-emerald-50 transition-colors text-emerald-600" title="WhatsApp">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                    @endif
                    <button onclick='openEditModal({{ json_encode($child) }})'
                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors text-blue-600" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form action="{{ route('children.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data anak ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 rounded-lg hover:bg-red-50 transition-colors text-red-600" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 rounded-2xl"
             style="background: rgba(255,255,255,0.35); backdrop-filter: blur(24px) saturate(180%);
                    -webkit-backdrop-filter: blur(24px) saturate(180%);
                    border: 1px solid rgba(255,255,255,0.6);">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <p class="text-gray-500">Belum ada data anak</p>
            @if(auth()->user()->isAdmin())
            <button onclick="openModal('create')" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                Tambah Anak Pertama
            </button>
            @endif
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($children->hasPages())
    <div class="mt-6 flex items-center justify-between gap-2">
        <p class="text-xs text-gray-400 shrink-0">
            Halaman {{ $children->currentPage() }} dari {{ $children->lastPage() }} — {{ $children->total() }} anak
        </p>
        <nav class="flex items-center gap-1 shrink-0" aria-label="Pagination">
            {{-- Previous --}}
            @if ($children->onFirstPage())
                <span class="px-2.5 py-1.5 rounded-lg text-xs text-gray-300 cursor-not-allowed opacity-50">Prev</span>
            @else
                <a href="{{ $children->previousPageUrl() }}" class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-600 hover:bg-white/60 hover:text-gray-900 transition-colors border border-gray-200">Prev</a>
            @endif

            {{-- Pages --}}
            @foreach ($children->getUrlRange(1, $children->lastPage()) as $page => $url)
                @if ($page == $children->currentPage())
                    <span class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-white"
                         style="background: linear-gradient(135deg, rgba(124,58,237,0.85) 0%, rgba(109,40,217,0.95) 100%);
                                box-shadow: 0 4px 12px rgba(124,58,237,0.3);">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-600 hover:bg-white/60 hover:text-gray-900 transition-colors border border-gray-200">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next --}}
            @if ($children->hasMorePages())
                <a href="{{ $children->nextPageUrl() }}" class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-600 hover:bg-white/60 hover:text-gray-900 transition-colors border border-gray-200">Next</a>
            @else
                <span class="px-2.5 py-1.5 rounded-lg text-xs text-gray-300 cursor-not-allowed opacity-50">Next</span>
            @endif
        </nav>
    </div>
    @endif
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('create')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between p-4 sm:p-5 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Tambah Anak Baru</h2>
                <button onclick="closeModal('create')" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 sm:p-5">
            <form id="createForm" action="{{ route('children.store') }}" method="POST" class="space-y-5">
                @csrf
                <div id="therapy_types_wrapper_create"></div>
                <div id="vocational_types_wrapper_create"></div>

                <!-- Basic Info -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Informasi Dasar
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Anak <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kelas (opsional)</label>
                            <input type="text" name="class_name" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Orang Tua</label>
                            <input type="text" name="parent_name" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">No. WhatsApp</label>
                            <input type="text" name="parent_whatsapp" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm">
                        </div>
                    </div>
                </div>

                <!-- Services Section -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Layanan
                    </h3>

                    <!-- Terapi Card -->
                    <div class="mb-3 rounded-xl border border-gray-200 overflow-hidden">
                        <label class="flex items-center gap-3 p-3 cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="checkbox" class="therapy-toggle w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500" data-type="terapi">
                            <div class="flex-1">
                                <span class="font-medium text-gray-900 text-sm">Terapi</span>
                                <p class="text-xs text-gray-500 mt-0.5">Pilih jenis terapi dan jumlah sesi</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </label>
                        <div class="therapy-details hidden details-panel border-t border-gray-100 p-3 bg-gray-50/50">
                            <div id="therapyCreateList" class="space-y-2"></div>
                        </div>
                    </div>

                    <!-- Sekolah Card -->
                    <div class="mb-3 rounded-xl border border-gray-200 overflow-hidden">
                        <label class="flex items-center gap-3 p-3 cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="checkbox" id="is_sekolah_create" name="is_sekolah" value="1" class="sekolah-toggle w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="toggleSekolah(this)">
                            <div class="flex-1">
                                <span class="font-medium text-gray-900 text-sm">Sekolah</span>
                                <p class="text-xs text-gray-500 mt-0.5">Layanan pendidikan bulanan</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </label>
                        <div id="schoolClassFieldCreate" class="hidden details-panel border-t border-gray-100 p-3 bg-blue-50/50">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kelas</label>
                                    <input type="text" name="class_name" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">SPP Bulanan (Rp)</label>
                                    <input type="number" name="spp_fee" id="spp_fee_create" value="{{ config('settings.school_fee', 1000000) }}" min="0" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm" oninput="calculateEstimate('create')">
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan untuk harga default</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vokasi Card -->
                    <div class="mb-3 rounded-xl border border-gray-200 overflow-hidden">
                        <label class="flex items-center gap-3 p-3 cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="checkbox" class="vocasi-toggle w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500" data-type="vokasi">
                            <div class="flex-1">
                                <span class="font-medium text-gray-900 text-sm">Vokasi</span>
                                <p class="text-xs text-gray-500 mt-0.5">Pilih jenis vokasi dan jumlah sesi</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </label>
                        <div class="vocasi-details hidden details-panel border-t border-gray-100 p-3 bg-gray-50/50">
                            <div id="vokasiCreateList" class="space-y-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Subsidi Section -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Subsidi <span class="text-gray-400 font-normal">(opsional)</span>
                    </h3>
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                        <label class="flex items-center gap-3 cursor-pointer mb-4">
                            <input type="checkbox" name="has_subsidi" value="1" id="has_subsidi_create" onchange="toggleSubsidi(this)" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="font-medium text-gray-900">Anak ini mendapatkan subsidi</span>
                        </label>
                        <div id="subsidiFieldCreate" class="hidden">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Subsidi (Rp)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                                    <input type="number" name="subsidi_amount" id="subsidi_amount_create" min="0" step="100" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 pl-10 pr-4 py-2.5 text-sm" oninput="calculateEstimate('create')">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parent Support Section -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Parent Support <span class="text-gray-400 font-normal">(opsional)</span>
                    </h3>
                    <div class="p-4 bg-sky-50 rounded-xl border border-sky-200">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="has_parent_support" value="1" id="has_parent_support_create" onchange="calculateEstimate('create')" class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                            <div>
                                <span class="font-medium text-gray-900">Tambahkan Parent Support</span>
                                <p class="text-xs text-gray-500 mt-0.5">Rp {{ number_format(config('settings.parent_support_fee', 25000), 0, ',', '.') }} / bulan</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Estimate Card -->
                <div id="estimateCard" class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-emerald-700 font-medium">Estimasi Tagihan Bulanan</div>
                            <div class="text-2xl font-bold text-emerald-700 mt-1" id="estimateAmountCreate">Rp 0</div>
                        </div>
                        <div class="text-right text-xs text-emerald-600" id="estimateBreakdownCreate"></div>
                    </div>
                </div>
            </form>
            </div>
            <div class="border-t border-gray-200 p-4 sm:p-5 flex justify-end gap-3">
                <button onclick="closeModal('create')" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium">Batal</button>
                <button type="submit" form="createForm" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium shadow-lg shadow-slate-800/20">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal('edit')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between p-4 sm:p-5 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Edit Anak</h2>
                <button onclick="closeModal('edit')" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 sm:p-5">
            <form id="editForm" action="" method="POST" class="space-y-5">
                @csrf @method('PUT')
                <div id="therapy_types_wrapper_edit"></div>
                <div id="vocational_types_wrapper_edit"></div>

                <!-- Basic Info -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Informasi Dasar
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Anak <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required id="edit_name" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kelas (opsional)</label>
                            <input type="text" name="class_name" id="edit_class_name" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Orang Tua</label>
                            <input type="text" name="parent_name" id="edit_parent_name" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">No. WhatsApp</label>
                            <input type="text" name="parent_whatsapp" id="edit_parent_whatsapp" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm">
                        </div>
                    </div>
                </div>

                <!-- Services Section -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Layanan
                    </h3>

                    <!-- Terapi Card -->
                    <div class="mb-3 rounded-xl border border-gray-200 overflow-hidden">
                        <label class="flex items-center gap-3 p-3 cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="checkbox" class="therapy-toggle-edit w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500" data-type="terapi">
                            <div class="flex-1">
                                <span class="font-medium text-gray-900 text-sm">Terapi</span>
                                <p class="text-xs text-gray-500 mt-0.5">Pilih jenis terapi dan jumlah sesi</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </label>
                        <div class="therapy-details-edit hidden details-panel border-t border-gray-100 p-3 bg-gray-50/50">
                            <div id="therapyEditList" class="space-y-2"></div>
                        </div>
                    </div>

                    <!-- Sekolah Card -->
                    <div class="mb-3 rounded-xl border border-gray-200 overflow-hidden">
                        <label class="flex items-center gap-3 p-3 cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="checkbox" name="is_sekolah" value="1" id="edit_is_sekolah" class="sekolah-toggle-edit w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="toggleSekolah(this)">
                            <div class="flex-1">
                                <span class="font-medium text-gray-900 text-sm">Sekolah</span>
                                <p class="text-xs text-gray-500 mt-0.5">Layanan pendidikan bulanan</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </label>
                        <div id="schoolClassFieldEdit" class="hidden details-panel border-t border-gray-100 p-3 bg-blue-50/50">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kelas (opsional)</label>
                                    <input type="text" name="class_name" id="edit_class_name_school" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">SPP Bulanan (Rp)</label>
                                    <input type="number" name="spp_fee" id="spp_fee_edit" min="0" class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm" oninput="calculateEstimate('edit')">
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan untuk harga default</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vokasi Card -->
                    <div class="mb-3 rounded-xl border border-gray-200 overflow-hidden">
                        <label class="flex items-center gap-3 p-3 cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="checkbox" class="vocasi-toggle-edit w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500" data-type="vokasi">
                            <div class="flex-1">
                                <span class="font-medium text-gray-900 text-sm">Vokasi</span>
                                <p class="text-xs text-gray-500 mt-0.5">Pilih jenis vokasi dan jumlah sesi</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </label>
                        <div class="vocasi-details-edit hidden details-panel border-t border-gray-100 p-3 bg-gray-50/50">
                            <div id="vokasiEditList" class="space-y-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Subsidi Section -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Subsidi <span class="text-gray-400 font-normal">(opsional)</span>
                    </h3>
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                        <label class="flex items-center gap-3 cursor-pointer mb-4">
                            <input type="checkbox" name="has_subsidi" value="1" id="has_subsidi_edit" onchange="toggleSubsidi(this)" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="font-medium text-gray-900">Anak ini mendapatkan subsidi</span>
                        </label>
                        <div id="subsidiFieldEdit" class="hidden">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Subsidi (Rp)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                                    <input type="number" name="subsidi_amount" id="subsidi_amount_edit" min="0" step="100" class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 pl-10 pr-4 py-2.5 text-sm" oninput="calculateEstimate('edit')">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Parent Support Section -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Parent Support <span class="text-gray-400 font-normal">(opsional)</span>
                    </h3>
                    <div class="p-4 bg-sky-50 rounded-xl border border-sky-200">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="has_parent_support" value="1" id="has_parent_support_edit" onchange="calculateEstimate('edit')" class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                            <div>
                                <span class="font-medium text-gray-900">Tambahkan Parent Support</span>
                                <p class="text-xs text-gray-500 mt-0.5">Rp {{ number_format(config('settings.parent_support_fee', 25000), 0, ',', '.') }} / bulan</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Estimate Card -->
                <div id="editEstimateCard" class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-emerald-700 font-medium">Estimasi Tagihan Bulanan</div>
                            <div class="text-2xl font-bold text-emerald-700 mt-1" id="estimateAmountEdit">Rp 0</div>
                        </div>
                        <div class="text-right text-xs text-emerald-600" id="estimateBreakdownEdit"></div>
                    </div>
                </div>
            </form>
            </div>
            <div class="border-t border-gray-200 p-4 sm:p-5 flex justify-end gap-3">
                <button onclick="closeModal('edit')" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium">Batal</button>
                <button type="submit" form="editForm" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium shadow-lg shadow-slate-800/20">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<style>
.details-panel {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}
.details-panel.open {
    max-height: 500px;
}
</style>

<script>
const therapyTypes = @json($therapyTypes ?? []);
const vocationalTypes = @json($vocationalTypes ?? []);
const schoolFee = {{ config('settings.school_fee', 1000000) }};

function openModal(type) {
    document.getElementById(type + 'Modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    if (type === 'create') {
        populateTherapyList('therapyCreateList', true);
        populateVokasiList('vokasiCreateList', true);
        calculateEstimate('create');
    }
}

function openEditModal(child) {
    document.getElementById('editModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Populate form
    document.getElementById('editForm').action = `/children/${child.id}`;
    document.getElementById('edit_name').value = child.name;
    document.getElementById('edit_class_name').value = child.class_name || '';
    document.getElementById('edit_parent_name').value = child.parent_name || '';
    document.getElementById('edit_parent_whatsapp').value = child.parent_whatsapp || '';
    document.getElementById('edit_is_sekolah').checked = child.is_taking_sekolah;
    document.getElementById('edit_class_name_school').value = child.class_name || '';
    document.getElementById('spp_fee_edit').value = child.spp_fee || schoolFee;
    document.getElementById('has_subsidi_edit').checked = child.has_subsidi;
    document.getElementById('subsidi_amount_edit').value = child.subsidi_amount || 0;
    document.getElementById('has_parent_support_edit').checked = child.has_parent_support || false;

    // Toggle sekolah card
    const sekolahToggle = document.querySelector('#editModal input[name="is_sekolah"]');
    const sekolahField = document.getElementById('schoolClassFieldEdit');
    const sekolahArrow = sekolahToggle?.closest('.mb-3')?.querySelector('svg');
    if (child.is_taking_sekolah) {
        sekolahToggle.checked = true;
        sekolahField.classList.remove('hidden');
        sekolahField.classList.add('open');
        if (sekolahArrow) sekolahArrow.style.transform = 'rotate(180deg)';
    }

    // Toggle therapy cards — check if child has any therapy types
    const therapyToggles = document.querySelectorAll('#editModal .therapy-toggle-edit');
    const hasTherapy = child.therapy_types_data && child.therapy_types_data.length > 0;
    therapyToggles.forEach(toggle => {
        if (toggle.dataset.type === 'terapi') {
            toggle.checked = hasTherapy;
            const card = toggle.closest('.mb-3');
            const details = card.querySelector('.therapy-details-edit');
            const arrow = card.querySelector('svg');
            if (hasTherapy) {
                details.classList.remove('hidden');
                details.classList.add('open');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
            }
        }
    });

    // Toggle vokasi cards — check if child has any vokasi types
    const vokasiToggles = document.querySelectorAll('#editModal .vocasi-toggle-edit');
    const hasVokasi = child.vocational_types_data && child.vocational_types_data.length > 0;
    vokasiToggles.forEach(toggle => {
        if (toggle.dataset.type === 'vokasi') {
            toggle.checked = hasVokasi;
            const card = toggle.closest('.mb-3');
            const details = card.querySelector('.vocasi-details-edit');
            const arrow = card.querySelector('svg');
            if (hasVokasi) {
                details.classList.remove('hidden');
                details.classList.add('open');
                if (arrow) arrow.style.transform = 'rotate(180deg)';
            }
        }
    });

    // Populate therapy and vokasi (after cards are opened)
    populateTherapyList('therapyEditList', false, child.therapy_types_data);
    populateVokasiList('vokasiEditList', false, child.vocational_types_data);

    // Recalculate estimate
    calculateEstimate('edit');
}

function closeModal(type) {
    document.getElementById(type + 'Modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function populateTherapyList(containerId, isCreate, existingTypes = []) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    if (therapyTypes.length === 0) {
        container.innerHTML = '<p class="text-xs text-gray-400 text-center py-2">Belum ada jenis terapi. Tambahkan di halaman Pengaturan.</p>';
        return;
    }

    therapyTypes.forEach(therapy => {
        const sessions = existingTypes.find(t => t.id == therapy.id)?.pivot?.monthly_sessions || 0;
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-2.5 bg-white rounded-lg border border-gray-100 hover:border-purple-200 transition-colors';
        div.dataset.therapyId = therapy.id;
        div.dataset.therapyPrice = therapy.price_per_session;
        div.innerHTML = `
            <div class="flex-1 min-w-0">
                <div class="font-medium text-gray-900 text-sm">${therapy.name}</div>
                <div class="text-xs text-gray-500">Rp ${Math.floor(therapy.price_per_session).toLocaleString('id-ID')} / sesi</div>
            </div>
            <div class="flex items-center gap-2 ml-3">
                <input type="number" name="therapy_sessions[${therapy.id}]" id="therapy_${therapy.id}_${isCreate ? 'create' : 'edit'}" min="0" max="30" value="${sessions}" class="w-16 rounded-lg border-gray-200 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 px-2 py-1.5 text-sm text-center" oninput="calculateEstimate('${isCreate ? 'create' : 'edit'}')">
                <span class="text-xs text-gray-400 whitespace-nowrap">sesi/bln</span>
            </div>
        `;
        container.appendChild(div);
    });
}

function populateVokasiList(containerId, isCreate, existingTypes = []) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    if (vocationalTypes.length === 0) {
        container.innerHTML = '<p class="text-xs text-gray-400 text-center py-2">Belum ada jenis vokasi. Tambahkan di halaman Pengaturan.</p>';
        return;
    }

    vocationalTypes.forEach(vokasi => {
        const sessions = existingTypes.find(t => t.id == vokasi.id)?.pivot?.monthly_sessions || 0;
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-2.5 bg-white rounded-lg border border-gray-100 hover:border-amber-200 transition-colors';
        div.dataset.vokasiId = vokasi.id;
        div.dataset.vokasiPrice = vokasi.price_per_session;
        div.innerHTML = `
            <div class="flex-1 min-w-0">
                <div class="font-medium text-gray-900 text-sm">${vokasi.name}</div>
                <div class="text-xs text-gray-500">Rp ${Math.floor(vokasi.price_per_session).toLocaleString('id-ID')} / sesi</div>
            </div>
            <div class="flex items-center gap-2 ml-3">
                <input type="number" name="vocational_sessions[${vokasi.id}]" id="vocational_${vokasi.id}_${isCreate ? 'create' : 'edit'}" min="0" max="30" value="${sessions}" class="w-16 rounded-lg border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 px-2 py-1.5 text-sm text-center" oninput="calculateEstimate('${isCreate ? 'create' : 'edit'}')">
                <span class="text-xs text-gray-400 whitespace-nowrap">sesi/bln</span>
            </div>
        `;
        container.appendChild(div);
    });
}

function toggleSekolah(checkbox) {
    const isCreate = checkbox.id.includes('create');
    const fieldId = isCreate ? 'schoolClassFieldCreate' : 'schoolClassFieldEdit';
    const field = document.getElementById(fieldId);
    const arrow = checkbox.closest('.mb-3').querySelector('svg');
    if (checkbox.checked) {
        field.classList.remove('hidden');
        field.classList.add('open');
        if (arrow) arrow.style.transform = 'rotate(180deg)';
    } else {
        field.classList.add('hidden');
        field.classList.remove('open');
        if (arrow) arrow.style.transform = '';
        const input = field.querySelector('input[type="text"]');
        if (input) input.value = '';
    }
    calculateEstimate(isCreate ? 'create' : 'edit');
}

// Toggle terapi and vokasi cards
document.addEventListener('DOMContentLoaded', function() {
    function setupToggle(checkboxClass, detailsClass, calculateType) {
        document.querySelectorAll(checkboxClass).forEach(cb => {
            cb.addEventListener('change', function() {
                const card = this.closest('.mb-3');
                const details = card.querySelector('.' + detailsClass);
                const arrow = card.querySelector('svg');
                if (this.checked) {
                    details.classList.remove('hidden');
                    details.classList.add('open');
                    if (arrow) arrow.style.transform = 'rotate(180deg)';
                    updateServiceHiddenInput(calculateType);
                } else {
                    details.classList.add('hidden');
                    details.classList.remove('open');
                    if (arrow) arrow.style.transform = '';
                    card.querySelectorAll('input[type="number"]').forEach(i => i.value = 0);
                    calculateEstimate(calculateType);
                    updateServiceHiddenInput(calculateType);
                }
            });
        });
    }
    setupToggle('.therapy-toggle', 'therapy-details', 'create');
    setupToggle('.vocasi-toggle', 'vocasi-details', 'create');
    setupToggle('.therapy-toggle-edit', 'therapy-details-edit', 'edit');
    setupToggle('.vocasi-toggle-edit', 'vocasi-details-edit', 'edit');
});

function toggleSubsidi(checkbox) {
    const isCreate = checkbox.id.includes('create');
    const fieldId = isCreate ? 'subsidiFieldCreate' : 'subsidiFieldEdit';
    const inputId = isCreate ? 'subsidi_amount_create' : 'subsidi_amount_edit';
    const field = document.getElementById(fieldId);
    const input = document.getElementById(inputId);

    if (checkbox.checked) {
        field.classList.remove('hidden');
    } else {
        field.classList.add('hidden');
        if (input) input.value = 0;
    }
    calculateEstimate(isCreate ? 'create' : 'edit');
}

function updateServiceHiddenInput(type) {
    const prefix = type === 'create' ? 'create' : 'edit';
    const therapyContainer = document.getElementById(`therapy${prefix.charAt(0).toUpperCase() + prefix.slice(1)}List`);
    const vokasiContainer = document.getElementById(`vokasi${prefix.charAt(0).toUpperCase() + prefix.slice(1)}List`);

    // Get therapy types
    const therapyIds = [];
    if (therapyContainer) {
        therapyContainer.querySelectorAll('[data-therapy-id]').forEach(item => {
            const sessions = parseInt(item.querySelector('input[type="number"]').value) || 0;
            if (sessions > 0) {
                therapyIds.push(item.dataset.therapyId);
            }
        });
    }

    // Create multiple hidden inputs for therapy types array
    const therapyInput = document.getElementById(`therapy_types_${prefix}`);
    if (therapyInput) {
        therapyInput.parentNode.removeChild(therapyInput);
    }
    const therapyWrapper = document.getElementById(`therapy_types_wrapper_${prefix}`);
    if (therapyWrapper) {
        therapyWrapper.innerHTML = '';
        therapyIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'therapy_types[]';
            input.value = id;
            therapyWrapper.appendChild(input);
        });
    }

    // Get vokasi types
    const vokasiIds = [];
    if (vokasiContainer) {
        vokasiContainer.querySelectorAll('[data-vokasi-id]').forEach(item => {
            const sessions = parseInt(item.querySelector('input[type="number"]').value) || 0;
            if (sessions > 0) {
                vokasiIds.push(item.dataset.vokasiId);
            }
        });
    }

    // Create multiple hidden inputs for vokasi types array
    const vokasiInput = document.getElementById(`vocational_types_${prefix}`);
    if (vokasiInput) {
        vokasiInput.parentNode.removeChild(vokasiInput);
    }
    const vokasiWrapper = document.getElementById(`vocational_types_wrapper_${prefix}`);
    if (vokasiWrapper) {
        vokasiWrapper.innerHTML = '';
        vokasiIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'vocational_types[]';
            input.value = id;
            vokasiWrapper.appendChild(input);
        });
    }
}

function calculateEstimate(type = 'create') {
    const modalId = type === 'edit' ? 'editModal' : 'createModal';
    const modal = document.getElementById(modalId);
    if (!modal || modal.classList.contains('hidden')) return;

    let total = 0;
    let breakdown = [];

    // Therapy calculation — scope to modal only
    modal.querySelectorAll('input[name^="therapy_sessions["]').forEach(input => {
        const match = input.name.match(/therapy_sessions\[(\d+)\]/);
        if (!match) return;
        const sessions = parseInt(input.value) || 0;
        const therapyCard = input.closest('[data-therapy-id]');
        const price = parseFloat(therapyCard?.dataset.therapyPrice) || 0;
        const therapyName = therapyCard?.querySelector('.font-medium')?.textContent?.trim() || 'Terapi';
        if (sessions > 0 && price > 0) {
            total += price * sessions;
            breakdown.push(`${sessions}× ${therapyName}: Rp ${Math.floor(price).toLocaleString('id-ID')}`);
        }
    });

    // School calculation — scope to modal only
    const sekolahCheck = modal.querySelector('input[name="is_sekolah"]:checked');
    if (sekolahCheck) {
        const sppInput = modal.querySelector('input[name="spp_fee"]');
        const sppPrice = sppInput ? (parseFloat(sppInput.value) || schoolFee) : 0;
        if (sppPrice > 0) {
            total += sppPrice;
            breakdown.push(`SPP: Rp ${Math.floor(sppPrice).toLocaleString('id-ID')}`);
        }
    }

    // Vocational calculation — scope to modal only
    modal.querySelectorAll('input[name^="vocational_sessions["]').forEach(input => {
        const match = input.name.match(/vocational_sessions\[(\d+)\]/);
        if (!match) return;
        const sessions = parseInt(input.value) || 0;
        const vokasiCard = input.closest('[data-vokasi-id]');
        const price = parseFloat(vokasiCard?.dataset.vokasiPrice) || 0;
        const vokasiName = vokasiCard?.querySelector('.font-medium')?.textContent?.trim() || 'Vokasi';
        if (sessions > 0 && price > 0) {
            total += price * sessions;
            breakdown.push(`${sessions}× ${vokasiName}: Rp ${Math.floor(price).toLocaleString('id-ID')}`);
        }
    });

    // Subsidi deduction — scope to modal only
    const subsidiInput = modal.querySelector('input[name="subsidi_amount"]');
    const subsidiCheck = modal.querySelector('input[name="has_subsidi"]:checked');
    const subsidi = subsidiCheck?.checked && subsidiInput?.value ? parseFloat(subsidiInput.value) || 0 : 0;

    // Parent Support addition — scope to modal only
    const parentSupportCheck = modal.querySelector('input[name="has_parent_support"]:checked');
    const parentSupportFee = {{ config('settings.parent_support_fee', 25000) }};
    if (parentSupportCheck?.checked) {
        total += parentSupportFee;
        breakdown.push(`Parent Support: Rp ${parentSupportFee.toLocaleString('id-ID')}`);
    }

    // Update UI
    const estimateAmount = modal.querySelector('#estimateAmount' + type.charAt(0).toUpperCase() + type.slice(1));
    const estimateBreakdown = modal.querySelector('#estimateBreakdown' + type.charAt(0).toUpperCase() + type.slice(1));
    const finalAmount = Math.max(0, total - subsidi);

    if (estimateAmount) {
        estimateAmount.textContent = `Rp ${Math.floor(finalAmount).toLocaleString('id-ID')}`;
    }
    if (estimateBreakdown) {
        let breakdownHTML = breakdown.join('<br>');
        if (subsidi > 0) {
            breakdownHTML += `<br><span class="text-emerald-600">- Subsidi: Rp ${Math.floor(subsidi).toLocaleString('id-ID')}</span>`;
        }
        estimateBreakdown.innerHTML = breakdownHTML || '<span class="text-gray-400">Belum ada layanan dipilih</span>';
    }
}

// Initialize on modal open
document.getElementById('createForm')?.addEventListener('submit', function(e) {
    updateServiceHiddenInput('create');
    this.submit();
});

document.getElementById('editForm')?.addEventListener('submit', function(e) {
    updateServiceHiddenInput('edit');
    this.submit();
});
</script>
@endsection
