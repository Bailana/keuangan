<x-guest-layout>
    <div class="glass-card rounded-3xl p-8 animate-fade-in-up-delay-2">
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center mx-auto mb-4 backdrop-blur-md">
                <svg class="w-7 h-7 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white tracking-tight">Reset Password</h2>
            <p class="text-sm text-white/50 mt-1">Buat password baru untuk akun Anda</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="glass-input w-full px-4 py-3 rounded-xl text-sm" readonly />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">Password Baru</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="glass-input w-full px-4 py-3 rounded-xl text-sm" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru" class="glass-input w-full px-4 py-3 rounded-xl text-sm" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="glass-btn-primary w-full py-3 rounded-xl font-semibold text-sm">
                Reset Password
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="glass-link text-sm font-medium">← Kembali ke masuk</a>
        </div>
    </div>
</x-guest-layout>
