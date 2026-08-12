<x-guest-layout>
    <div class="glass-card rounded-3xl p-8 animate-fade-in-up-delay-2">
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-white tracking-tight">Buat Akun Baru</h2>
            <p class="text-sm text-white/50 mt-1">Isi data diri Anda untuk mendaftar</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe" class="glass-input w-full px-4 py-3 rounded-xl text-sm" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com" class="glass-input w-full px-4 py-3 rounded-xl text-sm" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="glass-input w-full px-4 py-3 rounded-xl text-sm" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password" class="glass-input w-full px-4 py-3 rounded-xl text-sm" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="glass-btn-primary w-full py-3 rounded-xl font-semibold text-sm">
                Daftar
            </button>
        </form>

        <div class="flex items-center gap-3 my-6">
            <div class="flex-1 h-px bg-white/10"></div>
            <span class="text-xs text-white/30 font-medium">atau</span>
            <div class="flex-1 h-px bg-white/10"></div>
        </div>

        <p class="text-center text-sm text-white/50">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="glass-link font-semibold">Masuk di sini</a>
        </p>
    </div>
</x-guest-layout>
