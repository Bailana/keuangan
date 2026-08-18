@extends('layouts.app')

@section('page-title', 'Edit Anak')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('children.index') }}" class="p-2 rounded-xl hover:bg-gray-100 transition-colors flex-shrink-0">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Edit Anak</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 sm:p-8">
        <form action="{{ route('children.update', $child) }}" method="POST" class="space-y-6" id="childForm">
            @csrf @method('PUT')

            <!-- Basic Info -->
            <div>
                <h2 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Informasi Dasar
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Anak <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $child->name) }}"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kelas (opsional)</label>
                        <input type="text" name="class_name" value="{{ old('class_name', $child->class_name) }}"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Orang Tua</label>
                        <input type="text" name="parent_name" value="{{ old('parent_name', $child->parent_name) }}"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">No. WhatsApp</label>
                        <input type="text" name="parent_whatsapp" value="{{ old('parent_whatsapp', $child->parent_whatsapp) }}"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all">
                    </div>
                </div>
            </div>

            <!-- Therapy Types -->
            <div>
                <h2 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Terapi
                </h2>
                <div class="space-y-3">
                    @foreach($therapyTypes as $therapy)
                    @php
                        $sessions = $child->therapyTypes->firstWhere('id', $therapy->id)?->pivot->monthly_sessions ?? 0;
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-200" data-therapy-id="{{ $therapy->id }}" data-therapy-price="{{ $therapy->price_per_session }}">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $therapy->name }}</div>
                            <div class="text-xs text-gray-500">Rp {{ number_format($therapy->price_per_session, 0, ',', '.') }} / sesi</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="number" name="therapy_sessions[{{ $therapy->id }}]" id="therapy_{{ $therapy->id }}"
                                min="0" max="30" value="{{ $sessions }}"
                                class="w-20 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 px-2 py-1 text-sm text-center"
                                onchange="calculateEstimate()" oninput="calculateEstimate()">
                            <span class="text-xs text-gray-500">sesi/bulan</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- School Service -->
            <div>
                <h2 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Layanan Sekolah
                </h2>
                <div class="p-3 bg-blue-50 rounded-xl border border-blue-200">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_sekolah" value="1" {{ $child->isTakingSekolah() ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            onchange="toggleSekolah(this)">
                        <span class="font-medium text-gray-900">Aktif mengikuti sekolah</span>
                    </label>
                    <div id="schoolClassField" class="mt-3 {{ $child->isTakingSekolah() ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kelas</label>
                        <input type="text" name="class_name" value="{{ old('class_name', $child->class_name) }}"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5 mt-3">SPP Bulanan (Rp)</label>
                        <input type="number" name="spp_fee" id="spp_fee" value="{{ old('spp_fee', $child->spp_fee ?? config('settings.school_fee', 1000000)) }}" min="0"
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 px-4 py-2.5 text-sm transition-all"
                            oninput="calculateEstimate()">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan untuk menggunakan harga default sistem</p>
                    </div>
                </div>
            </div>

            <!-- Vocational Types -->
            <div>
                <h2 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Vokasi
                </h2>
                <div class="space-y-3">
                    @foreach($vocationalTypes as $vokasi)
                    @php
                        $sessions = $child->vocationalTypes->firstWhere('id', $vokasi->id)?->pivot->monthly_sessions ?? 0;
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-200" data-vokasi-id="{{ $vokasi->id }}" data-vokasi-price="{{ $vokasi->price_per_session }}">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">{{ $vokasi->name }}</div>
                            <div class="text-xs text-gray-500">Rp {{ number_format($vokasi->price_per_session, 0, ',', '.') }} / sesi</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="number" name="vocational_sessions[{{ $vokasi->id }}]" id="vocational_{{ $vokasi->id }}"
                                min="0" max="30" value="{{ $sessions }}"
                                class="w-20 rounded-lg border-gray-300 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 px-2 py-1 text-sm text-center"
                                onchange="calculateEstimate()" oninput="calculateEstimate()">
                            <span class="text-xs text-gray-500">sesi/bulan</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Subsidi Section -->
            <div>
                <h2 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Subsidi <span class="text-gray-400 font-normal">(opsional)</span>
                </h2>
                <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                    <label class="flex items-center gap-3 cursor-pointer mb-4">
                        <input type="checkbox" name="has_subsidi" value="1" id="has_subsidi" {{ $child->has_subsidi ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                            onchange="toggleSubsidi(this)">
                        <span class="font-medium text-gray-900">Anak ini mendapatkan subsidi</span>
                    </label>
                    <div id="subsidiField" class="{{ $child->has_subsidi ? '' : 'hidden' }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Subsidi (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                                <input type="number" name="subsidi_amount" id="subsidi_amount"
                                    min="0" step="100" value="{{ old('subsidi_amount', $child->subsidi_amount) }}"
                                    class="w-full rounded-xl border-gray-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 pl-10 pr-4 py-2.5 text-sm transition-all"
                                    oninput="calculateEstimate()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parent Support Section -->
            <div>
                <h2 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    Parent Support <span class="text-gray-400 font-normal">(opsional)</span>
                </h2>
                <div class="p-4 bg-sky-50 rounded-xl border border-sky-200">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="has_parent_support" value="1" id="has_parent_support" {{ $child->has_parent_support ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500"
                            onchange="calculateEstimate()">
                        <div>
                            <span class="font-medium text-gray-900">Tambahkan Parent Support</span>
                            <p class="text-xs text-gray-500 mt-0.5">Rp {{ number_format(config('settings.parent_support_fee', 25000), 0, ',', '.') }} / bulan</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Estimate Card -->
            <div id="estimateCard" class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-4" style="display: block;">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-emerald-700 font-medium">Estimasi Tagihan Bulanan</div>
                        <div class="text-2xl font-bold text-emerald-700 mt-1" id="estimateAmount">
                            Rp {{ number_format($child->calculateInvoiceAmount(now()->month, now()->year), 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="text-right text-xs text-emerald-600" id="estimateBreakdown">
                        {!! nl2br(e($child->getTherapyDetails() . ($child->isTakingSekolah() ? "\nSekolah\n" : '') . $child->getVokasiDetails())) !!}
                        @if($child->has_subsidi && $child->subsidi_amount > 0)
                            <br><span style="color: #059669;">- Subsidi: Rp {{ number_format($child->subsidi_amount, 0, ',', '.') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('children.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium shadow-lg shadow-slate-800/20 transition-all active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSessions(checkbox, inputId) {
    // Function kept for compatibility but no longer used
}

function toggleSekolah(checkbox) {
    const classField = document.getElementById('schoolClassField');
    if (checkbox.checked) {
        classField.classList.remove('hidden');
    } else {
        classField.classList.add('hidden');
        classField.querySelector('input').value = '';
    }
    calculateEstimate();
}

function toggleSubsidi(checkbox) {
    const field = document.getElementById('subsidiField');
    if (checkbox.checked) {
        field.classList.remove('hidden');
    } else {
        field.classList.add('hidden');
        document.getElementById('subsidi_amount').value = 0;
    }
    calculateEstimate();
}

function calculateEstimate() {
    let total = 0;
    let breakdown = [];

    // Therapy calculation
    const therapyInputs = document.querySelectorAll('input[name^="therapy_sessions["]');
    therapyInputs.forEach(input => {
        const match = input.name.match(/therapy_sessions\[(\d+)\]/);
        if (!match) return;
        const therapyId = match[1];
        const sessions = parseInt(input.value) || 0;

        // Find price from data attribute
        const therapyCard = document.querySelector(`[data-therapy-id="${therapyId}"]`);
        const price = parseFloat(therapyCard?.dataset.therapyPrice) || 0;

        if (sessions > 0) {
            const lineTotal = price * sessions;
            total += lineTotal;
            breakdown.push(`Terapi: ${sessions}× Rp ${price.toLocaleString('id-ID')}`);
        }
    });

    // School calculation - use spp_fee input value
    const sekolahCheck = document.querySelector('input[name="is_sekolah"]:checked');
    if (sekolahCheck) {
        const sppInput = document.getElementById('spp_fee');
        const sppPrice = sppInput ? (parseFloat(sppInput.value) || {{ $child->spp_fee ?? config('settings.school_fee', 1000000) }}) : 0;
        total += sppPrice;
        breakdown.push(`SPP Sekolah: Rp ${sppPrice.toLocaleString('id-ID')}`);
    }

    // Parent Support calculation
    const parentSupportCheck = document.getElementById('has_parent_support');
    const parentSupportFee = {{ config('settings.parent_support_fee', 25000) }};
    if (parentSupportCheck?.checked) {
        total += parentSupportFee;
        breakdown.push(`Parent Support: Rp ${parentSupportFee.toLocaleString('id-ID')}`);
    }

    // Vocational calculation
    const vokasiInputs = document.querySelectorAll('input[name^="vocational_sessions["]');
    vokasiInputs.forEach(input => {
        const match = input.name.match(/vocational_sessions\[(\d+)\]/);
        if (!match) return;
        const vokasiId = match[1];
        const sessions = parseInt(input.value) || 0;

        // Find price from data attribute
        const vokasiCard = document.querySelector(`[data-vokasi-id="${vokasiId}"]`);
        const price = parseFloat(vokasiCard?.dataset.vokasiPrice) || 0;

        if (sessions > 0) {
            const lineTotal = price * sessions;
            total += lineTotal;
            breakdown.push(`Vokasi: ${sessions}× Rp ${price.toLocaleString('id-ID')}`);
        }
    });

    // Subsidi deduction
    const subsidiCheck = document.getElementById('has_subsidi');
    const subsidiInput = document.getElementById('subsidi_amount');
    const subsidi = subsidiCheck?.checked && subsidiInput?.value ? parseFloat(subsidiInput.value) || 0 : 0;

    // Update UI
    const estimateCard = document.getElementById('estimateCard');
    const estimateAmount = document.getElementById('estimateAmount');
    const estimateBreakdown = document.getElementById('estimateBreakdown');

    const finalAmount = Math.max(0, total - subsidi);

    estimateCard.style.display = 'block';
    estimateAmount.textContent = `Rp ${finalAmount.toLocaleString('id-ID')}`;

    let breakdownHTML = breakdown.join('<br>');
    if (subsidi > 0) {
        breakdownHTML += `<br><span style="color: #059669;">- Subsidi: Rp ${subsidi.toLocaleString('id-ID')}</span>`;
    }
    estimateBreakdown.innerHTML = breakdownHTML;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateEstimate();
});
</script>
@endsection
