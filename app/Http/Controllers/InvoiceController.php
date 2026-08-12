<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\InvoicePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index()
    {
        $children = Child::with(['therapyTypes', 'vocationalTypes', 'invoicePayments'])->orderBy('name')->get();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        foreach ($children as $child) {
            $child->payment_status = $this->getPaymentStatus($child, $currentMonth, $currentYear);
            $child->unpaid_months = $this->getUnpaidMonths($child);
            $child->invoice_amount = $child->calculateInvoiceAmount($currentMonth, $currentYear);
        }

        return view('invoices.index', compact('children', 'currentMonth', 'currentYear'));
    }

    public function generate(Request $request, Child $child)
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $child = Child::with(['therapyTypes', 'vocationalTypes'])->findOrFail($child->id);

        if (!$child->isTakingTerapi() && !$child->isTakingVokasi() && !$child->isTakingSekolah()) {
            return redirect()->back()->with('error', 'Anak ini belum memiliki layanan aktif.');
        }

        $amount = $child->calculateInvoiceAmount($month, $year);

        $payment = InvoicePayment::firstOrCreate(
            ['child_id' => $child->id, 'month' => $month, 'year' => $year],
            ['amount' => $amount]
        );

        $pdf = Pdf::loadView('invoices.invoice-unpaid', [
            'child' => $child,
            'payment' => $payment,
            'month' => $month,
            'year' => $year,
            'invoiceNumber' => 'INV-' . $year . sprintf('%02d', $month) . '-' . sprintf('%03d', $child->id),
            'generatedDate' => now()->format('d F Y'),
        ]);

        $filename = 'Invoice-Belum-Lunas-' . $child->name . '-' . $month . '-' . $year . '.pdf';
        return $pdf->download($filename);
    }

    public function generatePaid(Request $request, Child $child)
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $child = Child::with(['therapyTypes', 'vocationalTypes'])->findOrFail($child->id);

        $amount = $child->calculateInvoiceAmount($month, $year);

        $payment = InvoicePayment::firstOrCreate(
            ['child_id' => $child->id, 'month' => $month, 'year' => $year],
            ['amount' => $amount, 'is_paid' => true, 'paid_date' => now()->format('Y-m-d')]
        );

        $pdf = Pdf::loadView('invoices.invoice-paid', [
            'child' => $child,
            'payment' => $payment,
            'month' => $month,
            'year' => $year,
            'invoiceNumber' => 'INV-' . $year . sprintf('%02d', $month) . '-' . sprintf('%03d', $child->id),
            'generatedDate' => now()->format('d F Y'),
        ]);

        $filename = 'Invoice-Lunas-' . $child->name . '-' . $month . '-' . $year . '.pdf';
        return $pdf->download($filename);
    }

    public function whatsapp(Request $request, Child $child)
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);
        $type = $request->query('type', 'unpaid');

        $child = Child::with(['therapyTypes', 'vocationalTypes'])->findOrFail($child->id);

        if (!$child->parent_whatsapp) {
            return redirect()->back()->with('error', 'Nomor WhatsApp orang tua belum diisi.');
        }

        $phone = $this->formatPhoneNumber($child->parent_whatsapp);
        $monthName = Carbon::create($year, $month, 1)->format('F Y');

        if ($type === 'paid') {
            $message = $this->buildPaidMessage($child, $monthName, $year, $month);
        } else {
            $message = $this->buildUnpaidMessage($child, $monthName, $year, $month);
        }

        $waUrl = 'https://wa.me/' . $phone . '?text=' . urlencode($message);
        return redirect($waUrl);
    }

    public function markPaid(Request $request, Child $child)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $child = Child::with(['therapyTypes', 'vocationalTypes'])->findOrFail($child->id);

        $payment = InvoicePayment::firstOrCreate(
            ['child_id' => $child->id, 'month' => $month, 'year' => $year],
            ['amount' => $child->calculateInvoiceAmount($month, $year)]
        );
        $payment->markAsPaid();

        return redirect()->route('invoices.index')->with('success', "Pembayaran {$child->name} bulan {$month}/{$year} ditandai LUNAS.");
    }

    public function markUnpaid(Request $request, Child $child)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $child = Child::findOrFail($child->id);

        $payment = InvoicePayment::where('child_id', $child->id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($payment) {
            $payment->markAsUnpaid();
            return redirect()->route('invoices.index')->with('success', "Pembayaran {$child->name} bulan {$month}/{$year} ditandai BELUM BAYAR.");
        }

        return redirect()->route('invoices.index')->with('error', 'Data tagihan tidak ditemukan.');
    }

    private function getPaymentStatus(Child $child, int $month, int $year): string
    {
        $payment = $child->invoicePayments()
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($payment && $payment->is_paid) {
            return 'paid';
        }

        $invoiceAmount = $child->calculateInvoiceAmount($month, $year);
        if ($invoiceAmount <= 0) {
            return 'paid'; // No invoice to pay
        }

        $totalIncome = $child->incomes()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        if ($totalIncome >= $invoiceAmount) {
            return 'paid';
        }

        return 'unpaid';
    }

    private function getUnpaidMonths(Child $child): array
    {
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $status = $this->getPaymentStatus($child, $m, now()->year);
            if ($status === 'unpaid' && $m <= now()->month) {
                $months[] = $m;
            }
        }
        return $months;
    }

    private function buildUnpaidMessage(Child $child, string $monthName, int $year, int $month): string
    {
        $adminName = Auth::user()->name ?? 'Klinik Terapi & Sekolah Mandiri';
        $amount = $child->calculateInvoiceAmount($month, $year);
        $fee = number_format($amount, 0, ',', '.');
        $dueDate = Carbon::create($year, $month, 10)->format('d F Y');

        $services = $this->formatServicesForWhatsApp($child);

        $subsidiInfo = '';
        if ($child->has_subsidi && $child->subsidi_amount > 0) {
            $subsidiFee = number_format((float)$child->subsidi_amount, 0, ',', '.');
            $subsidiInfo = "\n🎁 Subsidi: -Rp {$subsidiFee}\n";
        }

        return "Assalamu'alaikum Wr. Wb.\n\n"
            . "Yth. Bapak/Ibu {$child->parent_name},\n\n"
            . "Kami dari *{$adminName}* ingin menyampaikan tagihan untuk:\n\n"
            . "👤 Nama Anak: *{$child->name}*\n"
            . "📚 Kelas: " . ($child->class_name ?? '-') . "\n"
            . "🏫 Layanan:\n{$services}\n"
            . "📅 Periode: *{$monthName}*\n"
            . "💰 Total Tagihan: *Rp {$fee}*"
            . $subsidiInfo
            . "\n⏰ Jatuh tempo: {$dueDate}\n\n"
            . "Mohon untuk melakukan pembayaran sebelum tanggal jatuh tempo. Invoice terlampir siap diunduh dari sistem kami.\n\n"
            . "Terima kasih atas perhatian dan kerja samanya.\n\n"
            . "Wassalamu'alaikum Wr. Wb.\n"
            . "— {$adminName}";
    }

    private function buildPaidMessage(Child $child, string $monthName, int $year, int $month): string
    {
        $adminName = Auth::user()->name ?? 'Klinik Terapi & Sekolah Mandiri';
        $amount = $child->calculateInvoiceAmount($month, $year);
        $fee = number_format($amount, 0, ',', '.');
        $paidDate = now()->format('d F Y');

        $services = $this->formatServicesForWhatsApp($child);

        $subsidiInfo = '';
        if ($child->has_subsidi && $child->subsidi_amount > 0) {
            $subsidiFee = number_format((float)$child->subsidi_amount, 0, ',', '.');
            $subsidiInfo = "\n🎁 Subsidi: -Rp {$subsidiFee}\n";
        }

        return "Assalamu'alaikum Wr. Wb.\n\n"
            . "Yth. Bapak/Ibu {$child->parent_name},\n\n"
            . "Terima kasih! Pembayaran untuk:\n\n"
            . "👤 Nama Anak: *{$child->name}*\n"
            . "📚 Kelas: " . ($child->class_name ?? '-') . "\n"
            . "🏫 Layanan:\n{$services}\n"
            . "📅 Periode: *{$monthName}*\n"
            . "💰 Dibayar: *Rp {$fee}*"
            . $subsidiInfo
            . "\n✅ Status: *LUNAS*\n"
            . "📆 Tanggal: {$paidDate}\n\n"
            . "Invoice bukti lunas dapat diunduh dari sistem kami untuk arsip Bapak/Ibu.\n\n"
            . "Semoga Allah memberikan kesehatan dan keberkahan untuk {$child->name} selalu. Aamiin.\n\n"
            . "Wassalamu'alaikum Wr. Wb.\n"
            . "— {$adminName}";
    }

    private function formatServicesForWhatsApp(Child $child): string
    {
        $lines = [];
        foreach ($child->therapyTypes as $therapy) {
            $sessions = $therapy->pivot->monthly_sessions ?? 4;
            $price = (float) $therapy->price_per_session * (int) $sessions;
            $lines[] = "   • {$therapy->name} ({$sessions} sesi) — Rp " . number_format($price, 0, ',', '.');
        }
        foreach ($child->vocationalTypes as $vokasi) {
            $sessions = $vokasi->pivot->monthly_sessions ?? 4;
            $price = (float) $vokasi->price_per_session * (int) $sessions;
            $lines[] = "   • {$vokasi->name} ({$sessions} sesi) — Rp " . number_format($price, 0, ',', '.');
        }
        if ($child->isTakingSekolah()) {
            $lines[] = "   • SPP Sekolah (Kelas {$child->class_name})";
        }
        return implode("\n", $lines);
    }

    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        return $phone;
    }
}
