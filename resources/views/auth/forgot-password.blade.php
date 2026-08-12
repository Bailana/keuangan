<x-guest-layout>
    <div class="glass-card rounded-3xl p-8 animate-fade-in-up-delay-2">
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center mx-auto mb-4 backdrop-blur-md">
                <svg class="w-7 h-7 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white tracking-tight">Lupa Password?</h2>
            <p class="text-sm text-white/50 mt-1">Masukkan email Anda dan kami akan mengirimkan tautan reset.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-6">
                <label for="email" class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com" class="glass-input w-full px-4 py-3 rounded-xl text-sm" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <button type="submit" class="glass-btn-primary w-full py-3 rounded-xl font-semibold text-sm">
                Kirim Tautan Reset
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="glass-link text-sm font-medium">← Kembali ke masuk</a>
        </div>
    </div>
</x-guest-layout>
