@extends('layouts.app')

@section('page-title', 'Log Aktivitas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Log Aktivitas</h1>
            <p class="text-sm text-gray-500 mt-1">Riwayat semua aktivitas di sistem (login, transaksi, ekspor)</p>
        </div>
        @if(auth()->user()->isAdmin())
        <div class="flex items-center gap-3">
            <button onclick="clearAll()" id="clearAllBtn" class="hidden px-4 py-2 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 border border-red-200 transition-all">
                Hapus Semua
            </button>
        </div>
        @endif
    </div>

    <!-- Filters -->
    <div class="relative rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.55); backdrop-filter: blur(24px) saturate(180%);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="px-6 py-4 border-b border-gray-100">
            <form action="{{ route('activity-logs.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari aktivitas..."
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Aksi</label>
                    <select name="action" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/60">
                        <option value="">Semua</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                        <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Dibuat</option>
                        <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Diperbarui</option>
                        <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Dihapus</option>
                        <option value="export_pdf" {{ request('action') == 'export_pdf' ? 'selected' : '' }}>Export PDF</option>
                        <option value="export_excel" {{ request('action') == 'export_excel' ? 'selected' : '' }}>Export Excel</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tipe</label>
                    <select name="subject_type" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/60">
                        <option value="">Semua</option>
                        <option value="{{ Income::class }}" {{ request('subject_type') == Income::class ? 'selected' : '' }}>Pemasukan</option>
                        <option value="{{ Expense::class }}" {{ request('subject_type') == Expense::class ? 'selected' : '' }}>Pengeluaran</option>
                        <option value="{{ Wallet::class }}" {{ request('subject_type') == Wallet::class ? 'selected' : '' }}>Dompet</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dari</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/60">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/60">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'action', 'subject_type', 'date_from', 'date_to']))
                <a href="{{ route('activity-logs.index') }}" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">Reset</a>
                @endif
            </form>
        </div>

        <!-- Activity List -->
        <div class="divide-y divide-gray-100/50">
            @forelse($logs as $log)
            <div class="px-6 py-4 hover:bg-gray-50/50 transition-colors" style="cursor: pointer;" onclick="toggleDetail({{ $log->id }})">
                <div class="grid grid-cols-[32px_100px_40px_1fr_auto] gap-4 items-center sm:grid-cols-[32px_110px_40px_1fr_auto]">
                    <!-- Checkbox -->
                    @if(auth()->user()->isAdmin())
                    <div class="flex-shrink-0" onclick="event.stopPropagation()">
                        <input type="checkbox" name="ids[]" value="{{ $log->id }}" class="log-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" data-id="{{ $log->id }}">
                    </div>
                    @endif

                    <!-- Action Badge -->
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $log->action_color }}">
                            @if($log->action === 'create')
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            @elseif($log->action === 'update')
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            @elseif($log->action === 'delete')
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            @elseif($log->action === 'login')
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            @elseif($log->action === 'logout')
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            @elseif($log->action === 'export_pdf')
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            @elseif($log->action === 'export_excel')
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @endif
                            {{ $log->action_label }}
                        </span>
                    </div>

                    <!-- User Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full {{ $log->user ? 'bg-gradient-to-br from-purple-400 to-blue-500' : 'bg-gray-300' }} flex items-center justify-center text-white text-xs font-bold">
                            {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="min-w-0">
                        <p class="text-sm text-gray-900 truncate">{{ $log->description }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $log->user ? $log->user->name : 'System' }} &middot; {{ $log->subject_label ?? '-' }}
                        </p>
                    </div>

                    <!-- Date + Toggle -->
                    <div class="flex-shrink-0 flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-gray-700">{{ $log->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $log->created_at->format('H:i') }}</p>
                        </div>
                        <button onclick="event.stopPropagation(); toggleDetail({{ $log->id }})" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors" title="Lihat detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Detail Row (hidden by default) -->
            <div id="detail-{{ $log->id }}" class="hidden px-6 py-3 bg-gray-50/50 border-t border-gray-100/50">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">IP Address</p>
                        <p class="text-gray-700 font-mono text-xs">{{ $log->ip_address ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Lokasi</p>
                        <p class="text-gray-700 font-mono text-xs">{{ $log->location ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">User Agent</p>
                        <p class="text-gray-700 font-mono text-xs truncate" title="{{ $log->user_agent }}">{{ $log->user_agent ?? '-' }}</p>
                    </div>
                    @if($log->old_values && in_array($log->action, ['update', 'delete']))
                    <div class="sm:col-span-2 lg:col-span-3">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Perubahan Data</p>
                        <div class="mt-1 max-h-40 overflow-y-auto space-y-1">
                            @foreach($log->old_values as $key => $old)
                            @if($log->action === 'delete' || (isset($log->new_values[$key]) && $log->new_values[$key] !== $old))
                            <div class="flex items-center gap-2 text-xs">
                                @if($log->action === 'delete')
                                    <span class="text-red-500 line-through font-mono">{{ is_numeric($old) ? number_format($old, 0, ',', '.') : $old }}</span>
                                    <span class="text-gray-400">({{ $key }})</span>
                                @else
                                    <span class="text-red-500 line-through font-mono">{{ is_numeric($old) ? number_format($old, 0, ',', '.') : $old }}</span>
                                    <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    <span class="text-emerald-600 font-mono">{{ is_numeric($log->new_values[$key]) ? number_format($log->new_values[$key], 0, ',', '.') : $log->new_values[$key] }}</span>
                                    <span class="text-gray-400">({{ $key }})</span>
                                @endif
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @if(auth()->user()->isAdmin())
                <div class="mt-3 flex items-center gap-2">
                    <form action="{{ route('activity-logs.destroy', $log) }}" method="POST" onsubmit="return confirm('Hapus log aktivitas ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium text-red-600 hover:bg-red-50 border border-red-200 transition-colors">
                            <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus Log
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-gray-500">Belum ada log aktivitas</p>
            </div>
            @endforelse
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100/50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    <!-- Bulk Delete -->
    @if(auth()->user()->isAdmin())
    <div id="bulkActions" class="hidden fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 bg-gray-900 text-white rounded-2xl shadow-2xl">
        <span class="text-sm" id="selectedCount">0 dipilih</span>
        <button onclick="bulkDelete()" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-red-600 hover:bg-red-700 transition-colors">Hapus Dipilih</button>
        <button onclick="clearBulkSelection()" class="p-1.5 rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif
</div>

<script>
function toggleDetail(id) {
    const el = document.getElementById('detail-' + id);
    if (el) {
        el.classList.toggle('hidden');
    }
}

function clearBulkSelection() {
    document.querySelectorAll('.log-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('bulkActions').classList.add('hidden');
}

function bulkDelete() {
    const ids = [];
    document.querySelectorAll('.log-checkbox:checked').forEach(cb => ids.push(cb.value));
    if (ids.length === 0) return;
    if (!confirm(`Hapus ${ids.length} log aktivitas?`)) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('activity-logs.bulk-delete') }}';
    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = '{{ csrf_token() }}';
    form.appendChild(token);
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    document.body.appendChild(form);
    form.submit();
}

document.querySelectorAll('.log-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const checked = document.querySelectorAll('.log-checkbox:checked').length;
        const bulkActions = document.getElementById('bulkActions');
        if (checked > 0) {
            document.getElementById('selectedCount').textContent = checked + ' dipilih';
            bulkActions.classList.remove('hidden');
        } else {
            bulkActions.classList.add('hidden');
        }
    });
});
</script>
@endsection
