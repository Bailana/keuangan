@extends('layouts.app')

@section('page-title', 'Profil')

@section('content')
<div class="max-w-4xl mx-auto">

    <!-- Success Alerts -->
    @if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2 animate-fade-in-up">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('status') === 'profile-updated')
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2 animate-fade-in-up">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Profil berhasil diperbarui!
    </div>
    @endif
    @if(session('status') === 'password-updated')
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2 animate-fade-in-up">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Kata sandi berhasil diubah!
    </div>
    @endif
    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm animate-fade-in-up">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        <!-- Left Column: Profile Card (3 cols) -->
        <div class="lg:col-span-3 space-y-6">

            <!-- Avatar Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="h-20 bg-gradient-to-r from-emerald-500 via-teal-500 to-blue-500 relative">
                    <div class="absolute inset-0 bg-black/10"></div>
                </div>
                <div class="px-5 pb-5">
                    <div class="flex items-end gap-4 -mt-7 mb-5">
                        <div class="relative flex-shrink-0">
                            <div class="w-14 h-14 rounded-xl bg-white shadow-lg flex items-center justify-center text-emerald-600 text-xl font-bold border-2 border-white overflow-hidden">
                                @if(auth()->user()->avatar_path && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->avatar_path))
                                    <img src="{{ auth()->user()->avatarUrl() }}" class="w-full h-full object-cover" alt="Avatar">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                            <label for="avatar" class="absolute -bottom-1 -right-1 w-6 h-6 bg-white border border-gray-200 rounded-lg flex items-center justify-center cursor-pointer hover:border-emerald-400 hover:bg-emerald-50 transition-all shadow-sm">
                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </label>
                            <input type="file" name="avatar" id="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        </div>
                        <div class="flex-1 min-w-0 pb-0.5">
                            <h1 class="text-base font-bold text-gray-900 truncate">{{ auth()->user()->name }}</h1>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                            <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full font-semibold
                                @if(auth()->user()->role === 'superadmin') bg-purple-100 text-purple-700
                                @elseif(auth()->user()->role === 'admin') bg-amber-100 text-amber-700
                                @else bg-blue-100 text-blue-700
                                @endif">
                                @if(auth()->user()->role === 'superadmin') Superadmin
                                @elseif(auth()->user()->role === 'admin') Admin
                                @else Viewer
                                @endif
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf @method('patch')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 py-2 text-sm transition-all">
                                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 py-2 text-sm transition-all">
                                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">No. Telepon <span class="text-gray-400 font-normal">(opsional)</span></label>
                                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 py-2 text-sm transition-all">
                                @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Jabatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                                <input type="text" name="job_title" value="{{ old('job_title', $user->job_title) }}" placeholder="Contoh: Orang Tua"
                                    class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3 py-2 text-sm transition-all">
                                @error('job_title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium shadow-md shadow-emerald-500/20 transition-all active:scale-95 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Password + Delete (2 cols) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Password Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Ubah Kata Sandi</h2>
                        <p class="text-xs text-gray-500">Min. 8 karakter</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-3">
                    @csrf @method('put')
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" name="current_password" required
                            class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 px-3 py-2 text-sm transition-all">
                        @error('current_password', 'updatePassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Baru</label>
                            <input type="password" name="password" required
                                class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 px-3 py-2 text-sm transition-all">
                            @error('password', 'updatePassword') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Konfirmasi</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 px-3 py-2 text-sm transition-all">
                        </div>
                    </div>
                    <div class="pt-1">
                        <button type="submit" class="w-full px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium shadow-md shadow-amber-500/20 transition-all active:scale-95 flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>

            <!-- Delete Account Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-red-200 p-5">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-red-700">Hapus Akun</h2>
                        <p class="text-xs text-gray-500">Tidak dapat dibatalkan</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mb-4">
                    Semua data dan riwayat keuangan Anda akan hilang permanen.
                </p>
                <button type="button" onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                    class="w-full px-4 py-2 rounded-lg border border-red-300 text-red-600 text-sm font-medium hover:bg-red-50 transition-colors">
                    Hapus Akun
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('deleteModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-5 animate-fade-in-up">
        <div class="flex items-center gap-2.5 mb-4">
            <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Hapus Akun?</h3>
                <p class="text-xs text-gray-500">Masukkan kata sandi untuk konfirmasi</p>
            </div>
        </div>
        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-3">
            @csrf @method('delete')
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Kata Sandi</label>
                <input type="password" name="password" required placeholder="Kata sandi akun Anda"
                    class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 px-3 py-2 text-sm transition-all">
                @error('password', 'userDeletion') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                    class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium shadow-md transition-all active:scale-95">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
function previewAvatar(input) {
    const avatarBox = input.closest('.relative').querySelector('div:first-child');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            avatarBox.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-xl">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
