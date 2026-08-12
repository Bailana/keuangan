@extends('layouts.app')

@section('page-title', 'Log Login')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Log Login</h1>
            <p class="text-sm text-gray-500 mt-1">Riwayat masuk dan keluar pengguna sistem</p>
        </div>
        @if(auth()->user()->isAdmin())
        <div class="flex items-center gap-3">
            <button onclick="clearAll()" id="clearAllBtn" class="hidden px-4 py-2 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 border border-red-200 transition-all">
                Hapus Semua
            </button>
        </div>
        @endif
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="relative rounded-2xl overflow-hidden p-4 bg-gradient-to-br from-slate-800 to-slate-900 text-white">
            <div class="relative z-10">
                <p class="text-white/60 text-xs font-semibold uppercase tracking-wider">Total Log</p>
                <p class="text-2xl font-bold mt-1">{{ number_format($summary['total']) }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-4 bg-gradient-to-br from-emerald-600 to-emerald-700 text-white">
            <div class="relative z-10">
                <p class="text-white/70 text-xs font-semibold uppercase tracking-wider">Hari Ini</p>
                <p class="text-2xl font-bold mt-1">{{ number_format($summary['today']) }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-4 bg-gradient-to-br from-blue-600 to-blue-700 text-white">
            <div class="relative z-10">
                <p class="text-white/70 text-xs font-semibold uppercase tracking-wider">Login</p>
                <p class="text-2xl font-bold mt-1">{{ number_format($summary['logins']) }}</p>
            </div>
        </div>
        <div class="relative rounded-2xl overflow-hidden p-4 bg-gradient-to-br from-gray-600 to-gray-700 text-white">
            <div class="relative z-10">
                <p class="text-white/70 text-xs font-semibold uppercase tracking-wider">Logout</p>
                <p class="text-2xl font-bold mt-1">{{ number_format($summary['logouts']) }}</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="relative rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.55); backdrop-filter: blur(24px) saturate(180%);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="px-6 py-4 border-b border-gray-100">
            <form action="{{ route('login-logs.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Cari IP</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Alamat IP..."
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/60">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Aksi</label>
                    <select name="action" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/60">
                        <option value="">Semua</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Pengguna</label>
                    <select name="user_id" class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white/60">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                        <option value="{{ $user->user_id ?? $user->id }}" {{ request('user_id') == ($user->user_id ?? $user->id) ? 'selected' : '' }}>
                            {{ $user->user ? $user->user->name : 'Unknown' }}
                        </option>
                        @endforeach
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
                @if(request()->hasAny(['search', 'action', 'user_id', 'date_from', 'date_to']))
                <a href="{{ route('login-logs.index') }}" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">Reset</a>
                @endif
            </form>
        </div>

        <!-- Login Log List -->
        <div class="divide-y divide-gray-100/50">
            @forelse($logs as $log)
            <div class="px-6 py-4 hover:bg-gray-50/50 transition-colors flex items-center gap-4">
                <!-- Checkbox -->
                @if(auth()->user()->isAdmin())
                <div class="flex-shrink-0">
                    <input type="checkbox" name="ids[]" value="{{ $log->id }}" class="login-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" data-id="{{ $log->id }}">
                </div>
                @endif

                <!-- Action Badge -->
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $log->action_color }}">
                        {!! $log->action_icon !!}
                        {{ $log->action_label }}
                    </span>
                </div>

                <!-- User -->
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="w-8 h-8 rounded-full {{ $log->user ? 'bg-gradient-to-br from-emerald-400 to-blue-500' : 'bg-gray-300' }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $log->user ? $log->user->name : 'Unknown' }}</p>
                        <p class="text-xs text-gray-400">{{ $log->user ? $log->user->email : 'System' }}</p>
                    </div>
                </div>

                <!-- IP & Location -->
                <div class="hidden md:flex items-center gap-4 flex-shrink-0">
                    <div>
                        <p class="text-xs text-gray-400">IP Address</p>
                        <p class="text-sm font-mono text-gray-700">{{ $log->ip_address ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Lokasi</p>
                        <p class="text-sm text-gray-700">{{ $log->location ?? '-' }}</p>
                    </div>
                </div>

                <!-- User Agent -->
                <div class="hidden lg:block flex-shrink-0 max-w-[200px]">
                    <p class="text-xs text-gray-400 truncate" title="{{ $log->user_agent }}">{{ $log->user_agent ?? '-' }}</p>
                </div>

                <!-- Date -->
                <div class="flex-shrink-0 text-right">
                    <p class="text-sm font-medium text-gray-700">{{ $log->created_at->format('d M Y') }}</p>
                    <p class="text-xs text-gray-400">{{ $log->created_at->format('H:i') }}</p>
                </div>

                <!-- Delete Button -->
                @if(auth()->user()->isAdmin())
                <div class="flex-shrink-0">
                    <form action="{{ route('login-logs.destroy', $log) }}" method="POST" onsubmit="return confirm('Hapus log ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-gray-500">Belum ada log login</p>
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
function clearBulkSelection() {
    document.querySelectorAll('.login-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('bulkActions').classList.add('hidden');
}

function bulkDelete() {
    const ids = [];
    document.querySelectorAll('.login-checkbox:checked').forEach(cb => ids.push(cb.value));
    if (ids.length === 0) return;
    if (!confirm(`Hapus ${ids.length} log login?`)) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('login-logs.bulk-delete') }}';
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

document.querySelectorAll('.login-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const checked = document.querySelectorAll('.login-checkbox:checked').length;
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
