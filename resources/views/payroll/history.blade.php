@extends('layouts.app')

@section('page-title', 'Rekap Slip Gaji')

@section('content')
<div class="space-y-6 relative">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Rekap Slip Gaji</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Riwayat pembayaran gaji karyawan</p>
        </div>
    </div>

    <div class="relative rounded-2xl overflow-hidden"
         style="background: rgba(255,255,255,0.45); backdrop-filter: blur(24px) saturate(180%);
                box-shadow: 0 8px 32px rgba(0,0,0,0.08), inset 0 1px 0 rgba(255,255,255,0.8);
                border: 1px solid rgba(255,255,255,0.7);">
        <div class="relative z-10 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/50">
                <thead class="bg-indigo-50/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Karyawan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Jabatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Periode</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Gaji Pokok</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Tunjangan</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Bonus Sesi</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Total Komp.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">BPJS Kes.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">BPJS Ket.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Total Bersih</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Tanggal Bayar</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Slip Gaji</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/50">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-indigo-50/20 transition-colors">
                        <td class="px-4 py-4 font-medium text-gray-900">{{ $payment->employee_name }}</td>
                        <td class="px-4 py-4 text-sm text-gray-600">{{ $payment->position ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::create($payment->year, $payment->month, 1)->locale('id')->format('F Y') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm text-gray-900">
                            Rp {{ number_format($payment->base_salary, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm text-emerald-600">
                            +Rp {{ number_format($payment->salary_extra ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm text-emerald-600">
                            Rp {{ number_format($payment->session_bonus, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm font-medium text-gray-900">
                            Rp {{ number_format($payment->total_compensation, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm text-red-600">
                            -Rp {{ number_format($payment->transport_allowance, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm text-red-600">
                            -Rp {{ number_format($payment->deductions, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-emerald-600">
                            Rp {{ number_format($payment->net_salary, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-center text-sm text-gray-500">
                            {{ $payment->paid_at?->format('d M Y') }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('payroll.slip.pdf', $payment) }}" target="_blank"
                                   class="p-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors"
                                   title="Download PDF Slip Gaji">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </a>
                                @if($payment->whatsapp)
                                <a href="{{ route('payroll.slip.whatsapp', $payment) }}" target="_blank"
                                   class="p-2 rounded-lg bg-green-50 hover:bg-green-100 text-green-600 transition-colors"
                                   title="Kirim ke WhatsApp">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="px-4 py-8 text-center text-sm text-gray-400">Belum ada riwayat pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="relative z-10 px-4 py-3 border-t border-gray-200/50">{{ $payments->links() }}</div>
        @endif
    </div>
</div>
@endsection
