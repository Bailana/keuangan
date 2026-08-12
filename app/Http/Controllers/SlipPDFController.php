<?php

namespace App\Http\Controllers;

use App\Models\SalaryPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SlipPDFController extends Controller
{
    public function generate(SalaryPayment $payment)
    {
        $companyName = 'KLINIK TERAPI & SEKOLAH KHUSUS ANAK MANDIRI';
        $kopSuratPath = public_path('images/kop_surat.png');
        $logoPath = public_path('images/logo_am.png');
        $adminName = auth()->user()->name ?? 'Administrator';

        $pdf = Pdf::loadView('payroll.slips.slip', compact('payment', 'companyName', 'kopSuratPath', 'logoPath', 'adminName'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('slip-gaji-' . $payment->employee_name . '-' . $payment->year . '-' . $payment->month . '.pdf');
    }

    public function sendWhatsApp(SalaryPayment $payment)
    {
        $companyName = 'KLINIK TERAPI & SEKOLAH KHUSUS ANAK MANDIRI';
        $whatsappNumber = $payment->whatsapp ?? $payment->salaryRecord?->whatsapp;

        if (!$whatsappNumber) {
            return back()->with('error', 'Nomor WhatsApp karyawan tidak ditemukan.');
        }

        // Format nomor WhatsApp (hapus spasi, strip, atau +)
        $whatsappNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);

        // Pastikan dimulai dengan 62
        if (strlen($whatsappNumber) > 10 && substr($whatsappNumber, 0, 2) === '08') {
            $whatsappNumber = '62' . substr($whatsappNumber, 1);
        } elseif (strlen($whatsappNumber) == 10 && substr($whatsappNumber, 0, 1) === '0') {
            $whatsappNumber = '62' . substr($whatsappNumber, 1);
        }

        $monthName = \Carbon\Carbon::create($payment->year, $payment->month, 1)->locale('id')->format('F');

        $message = "*SLIP GAJI*\n*{$companyName}*\n\nYth. {$payment->employee_name},\n\nBerikut adalah rincian gaji Anda untuk bulan {$monthName} {$payment->year}:\n\n*PENDAPATAN:*\n- Gaji Pokok: Rp " . number_format($payment->base_salary, 0, ',', '.') . "\n- Tunjangan Lain: Rp " . number_format($payment->salary_extra ?? 0, 0, ',', '.') . "\n- Bonus Sesi: Rp " . number_format($payment->session_bonus, 0, ',', '.') . "\n\n*TOTAL KOMPENSASI: Rp " . number_format($payment->total_compensation, 0, ',', '.') . "\n\n*POTONGAN:*\n- BPJS Kesehatan: -Rp " . number_format($payment->transport_allowance, 0, ',', '.') . "\n- BPJS Ketenagakerjaan: -Rp " . number_format($payment->deductions, 0, ',', '.') . "\n\n*TOTAL BERSIH: Rp " . number_format($payment->net_salary, 0, ',', '.') . "\n\nDemikian slip gaji ini kami sampaikan.\n\nHormat kami,\n*HR Department*";

        $url = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);

        return redirect()->away($url);
    }
}
