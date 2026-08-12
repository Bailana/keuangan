<x-guest-layout>
    <div class="glass-card rounded-3xl p-8 animate-fade-in-up-delay-2">
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center mx-auto mb-4 backdrop-blur-md">
                <svg class="w-7 h-7 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white tracking-tight">Verifikasi Email</h2>
            <p class="text-sm text-white/50 mt-1">Terima sudah mendaftar! Verifikasi email Anda untuk memulai.</p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 bg-emerald-500/20 border border-emerald-400/30 rounded-xl px-4 py-3 text-center">
                <p class="text-sm text-emerald-300 font-medium">Tautan verifikasi baru telah dikirim ke email Anda.</p>
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
            @csrf
            <button type="submit" class="glass-btn-primary w-full py-3 rounded-xl font-semibold text-sm">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="glass-link text-sm font-medium">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
