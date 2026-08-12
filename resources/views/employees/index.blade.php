@extends('layouts.app')

@section('page-title', 'Data Karyawan')

@section('content')
<div class="space-y-6 relative">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Data Karyawan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola data karyawan dan pegawai</p>
        </div>
        @if(auth()->check() && auth()->user()->isAdmin())
        <a href="{{ route('employees.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm flex items-center gap-2 shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">Tambah Karyawan</span>
        </a>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="relative rounded-2xl overflow-hidden p-4 text-white"
             style="background: linear-gradient(135deg, rgba(99,102,241,0.85) 0%, rgba(79,70,229,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(99,102,241,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Total Karyawan</p>
                <p class="text-2xl font-bold mt-1">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-4 text-white"
             style="background: linear-gradient(135deg, rgba(16,185,129,0.85) 0%, rgba(5,150,105,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(16,185,129,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Aktif</p>
                <p class="text-2xl font-bold mt-1">{{ $stats['active'] }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-4 text-white col-span-2 sm:col-span-1"
             style="background: linear-gradient(135deg, rgba(107,114,128,0.85) 0%, rgba(75,85,99,0.95) 100%);
                    backdrop-filter: blur(24px) saturate(180%);
                    box-shadow: 0 8px 32px rgba(107,114,128,0.25), inset 0 1px 0 rgba(255,255,255,0.35);
                    border: 1px solid rgba(255,255,255,0.3);">
            <div class="relative z-10">
                <p class="text-white/75 text-xs font-semibold uppercase tracking-wider">Tidak Aktif</p>
                <p class="text-2xl font-bold mt-1">{{ $stats['inactive'] }}</p>
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
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari Karyawan</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama, NIP, jabatan..."
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                </div>
                <div class="w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white/60">
                        <option value="">Semua</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.335a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if(request('search') || request('status'))
                <a href="{{ route('employees.index') }}"
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">NIP</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">WhatsApp</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Riwayat Gaji</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/50">
                    @forelse($employees as $employee)
                    <tr class="hover:bg-indigo-50/20 transition-colors">
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-900">{{ $employee->name }}</div>
                            @if($employee->email)
                            <div class="text-xs text-gray-500">{{ $employee->email }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $employee->nip ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $employee->position ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $employee->whatsapp ?? '-' }}</td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                {{ $employee->salary_records_count }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($employee->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-500">
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-1">
                                <button onclick="toggleStatus({{ $employee->id }}, {{ $employee->is_active ? 'true' : 'false' }})"
                                    class="px-2 py-1 text-xs font-medium rounded-lg transition-colors
                                        {{ $employee->is_active ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                                    {{ $employee->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                                <a href="{{ route('employees.edit', $employee) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50/50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Hapus karyawan ini?')">
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
                    <tr><td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 7 : 6 }}" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada data karyawan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
        <div class="relative z-10 px-4 py-3 border-t border-gray-200/50">{{ $employees->links() }}</div>
        @endif
    </div>
</div>

<script>
function toggleStatus(id, isActive) {
    const action = isActive ? 'Nonaktifkan' : 'Aktifkan';
    if (!confirm(`Yakin ingin ${action.toLowerCase()} karyawan ini?`)) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/employees/${id}/toggle-status`;
    form.innerHTML = '@csrf @method("PUT")';
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
