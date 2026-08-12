<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Card -->
    <div class="glass-card rounded-3xl p-8 animate-fade-in-up-delay-2">
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-white tracking-tight">Selamat Datang</h2>
            <p class="text-sm text-white/50 mt-1">Masuk ke akun Anda untuk melanjutkan</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">
                    Email
                </label>
                <input id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@email.com"
                    class="glass-input w-full px-4 py-3 rounded-xl text-sm" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">
                    Password
                </label>
                <input id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="glass-input w-full px-4 py-3 rounded-xl text-sm" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between mb-6">
                <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                    <input id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="rounded border-white/20 bg-white/10 text-blue-400 shadow-sm focus:ring-blue-400 focus:ring-offset-transparent text-sm" />
                    <span class="text-sm text-white/60">Ingat saya</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="glass-link text-sm font-medium" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>

            <!-- Submit -->
            <button type="submit" class="glass-btn-primary w-full py-3 rounded-xl font-semibold text-sm">
                Masuk
            </button>
        </form>

        <!-- Divider -->
        <div class="flex items-center gap-3 my-6">
            <div class="flex-1 h-px bg-white/10"></div>
            <span class="text-xs text-white/30 font-medium">atau</span>
            <div class="flex-1 h-px bg-white/10"></div>
        </div>

        <!-- Register link -->
        <p class="text-center text-sm text-white/50">
            Belum punya akun?
            <a href="{{ route('register') }}" class="glass-link font-semibold">Daftar sekarang</a>
        </p>
    </div>
</x-guest-layout>
