<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\InvoicePayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceController extends Controller
{
    public function index()
    {
        $currentMonth = (int) request('month', now()->month);
        $currentYear = (int) request('year', now()->year);
        $statusFilter = request('status');
        $serviceFilter = request('service');
        $search = request('search');

        $children = Child::with(['therapyTypes', 'vocationalTypes', 'invoicePayments'])->orderBy('name')->get();

        // Apply filters
        if ($search) {
            $children = $children->filter(function ($child) use ($search) {
                return stripos($child->name, $search) !== false ||
                       stripos($child->parent_name ?? '', $search) !== false;
            });
        }

        if ($serviceFilter) {
            $children = $children->filter(function ($child) use ($serviceFilter) {
                $services = [];
                foreach ($child->therapyTypes as $t) $services[] = $t->name;
                foreach ($child->vocationalTypes as $v) $services[] = $v->name;
                if ($child->isTakingSekolah()) $services[] = 'Sekolah';
                return in_array($serviceFilter, $services);
            });
        }

        // Calculate status and totals for filtered children
        $totalPaid = 0;
        $totalUnpaid = 0;

        foreach ($children as $child) {
            $child->payment_status = $this->getPaymentStatus($child, $currentMonth, $currentYear);
            $child->unpaid_months = $this->getUnpaidMonths($child);
            $child->invoice_amount = $child->calculateInvoiceAmount($currentMonth, $currentYear);

            if ($child->payment_status === 'paid') {
                $totalPaid += $child->invoice_amount;
            } else {
                $totalUnpaid += $child->invoice_amount;
            }
        }

        // Apply status filter after calculation
        if ($statusFilter) {
            $children = $children->filter(function ($child) use ($statusFilter) {
                return $statusFilter === 'paid' ? $child->payment_status === 'paid' : $child->payment_status === 'unpaid';
            });
        }

        // Get all unique services for filter dropdown
        $allServices = [];
        $allChildren = Child::with(['therapyTypes', 'vocationalTypes'])->get();
        foreach ($allChildren as $c) {
            foreach ($c->therapyTypes as $t) $allServices[] = $t->name;
            foreach ($c->vocationalTypes as $v) $allServices[] = $v->name;
            if ($c->isTakingSekolah()) $allServices[] = 'Sekolah';
        }
        $allServices = array_unique($allServices);
        sort($allServices);

        // Paginate: 6 cards per page
        $currentPage = request('page', 1);
        $perPage = 6;
        $totalFiltered = count($children);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedChildren = new LengthAwarePaginator(
            $children->slice($offset, $perPage),
            $totalFiltered,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('invoices.index', compact('children', 'paginatedChildren', 'currentMonth', 'currentYear', 'totalPaid', 'totalUnpaid', 'allServices'));
    }

    public function generate(Request $request, Child $child)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $child = Child::with(['therapyTypes', 'vocationalTypes'])->findOrFail($child->id);

        if (!$child->isTakingTerapi() && !$child->isTakingVokasi() && !$child->isTakingSekolah()) {
            return redirect()->back()->with('error', 'Anak ini belum memiliki layanan aktif.');
        }

        $amount = $child->calculateInvoiceAmount($month, $year);

        $payment = InvoicePayment::firstOrCreate(
            ['child_id' => $child->id, 'month' => $month, 'year' => $year],
            ['amount' => $amount]
        );

        // Generate unique invoice number: INV-B[YY][MM]-[NNN]
        $sequence = InvoicePayment::where('month', $month)
            ->where('year', $year)
            ->where('is_paid', false)
            ->count() + 1;
        $invoiceNumber = 'INV-B' . substr($year, 2) . sprintf('%02d', $month) . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);

        $payment->update(['invoice_number' => $invoiceNumber]);

        $kopSuratPath = public_path('images/kop_surat.png');
        $logoPath = public_path('images/logo_am.png');

        $pdf = Pdf::loadView('invoices.invoice-unpaid', [
            'child' => $child,
            'payment' => $payment,
            'month' => $month,
            'year' => $year,
            'invoiceNumber' => $invoiceNumber,
            'generatedDate' => now()->format('d F Y'),
            'kopSuratPath' => $kopSuratPath,
            'logoPath' => $logoPath,
        ]);

        $filename = 'Invoice-Belum-Lunas-' . $child->name . '-' . $month . '-' . $year . '.pdf';
        return $pdf->download($filename);
    }

    public function generatePaid(Request $request, Child $child)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $child = Child::with(['therapyTypes', 'vocationalTypes'])->findOrFail($child->id);

        $amount = $child->calculateInvoiceAmount($month, $year);

        $payment = InvoicePayment::firstOrCreate(
            ['child_id' => $child->id, 'month' => $month, 'year' => $year],
            ['amount' => $amount, 'is_paid' => true, 'paid_date' => now()->format('Y-m-d')]
        );

        // Generate unique invoice number: INV-P[YY][MM]-[NNN]
        $sequence = InvoicePayment::where('month', $month)
            ->where('year', $year)
            ->where('is_paid', true)
            ->count() + 1;
        $invoiceNumber = 'INV-P' . substr($year, 2) . sprintf('%02d', $month) . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);

        $payment->update(['invoice_number' => $invoiceNumber]);

        $kopSuratPath = public_path('images/kop_surat.png');
        $logoPath = public_path('images/logo_am.png');

        $pdf = Pdf::loadView('invoices.invoice-paid', [
            'child' => $child,
            'payment' => $payment,
            'month' => $month,
            'year' => $year,
            'invoiceNumber' => $invoiceNumber,
            'generatedDate' => now()->format('d F Y'),
            'kopSuratPath' => $kopSuratPath,
            'logoPath' => $logoPath,
            'generatedBy' => Auth::user()->name ?? 'Admin',
        ]);

        $filename = 'Invoice-Lunas-' . $child->name . '-' . $month . '-' . $year . '.pdf';
        return $pdf->download($filename);
    }

    public function whatsapp(Request $request, Child $child)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
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
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $child = Child::with(['therapyTypes', 'vocationalTypes'])->findOrFail($child->id);

        $payment = InvoicePayment::firstOrCreate(
            ['child_id' => $child->id, 'month' => $month, 'year' => $year],
            ['amount' => $child->calculateInvoiceAmount($month, $year)]
        );
        $payment->markAsPaid();

        // Create income records for each service
        $this->createIncomeForChild($child, $payment, $month, $year);

        return redirect()->back()->with('success', "Pembayaran {$child->name} bulan {$month}/{$year} ditandai LUNAS.");
    }

    public function markUnpaid(Request $request, Child $child)
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $child = Child::findOrFail($child->id);

        $payment = InvoicePayment::where('child_id', $child->id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($payment) {
            $payment->markAsUnpaid();

            // Delete income records for this child and month/year
            \App\Models\Income::where('child_id', $child->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->delete();

            return redirect()->back()->with('success', "Pembayaran {$child->name} bulan {$month}/{$year} ditandai BELUM BAYAR.");
        }

        return redirect()->back()->with('error', 'Data tagihan tidak ditemukan.');
    }

    private function createIncomeForChild(Child $child, InvoicePayment $payment, int $month, int $year): void
    {
        $defaultWallet = \App\Models\Wallet::where('is_default', true)->first();
        if (!$defaultWallet) {
            return;
        }

        $date = now()->format('Y-m-d');

        // Delete all existing incomes for this child/month/year
        \App\Models\Income::where('child_id', $child->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->delete();

        // Build service list for this child
        $services = [];
        foreach ($child->therapyTypes as $therapy) {
            $services[] = 'Terapi';
        }
        foreach ($child->vocationalTypes as $vokasi) {
            $services[] = 'Vokasi';
        }
        if ($child->isTakingSekolah()) $services[] = 'SPP';
        if ($child->has_parent_support) $services[] = 'Parent Support';

        // Determine primary category for the income record
        $primaryCategory = \App\Models\IncomeCategory::where('name', 'SPP')->first();
        if (!$primaryCategory) $primaryCategory = \App\Models\IncomeCategory::where('name', 'Terapi')->first();
        if (!$primaryCategory) $primaryCategory = \App\Models\IncomeCategory::where('name', 'Vokasi')->first();

        // Build detailed notes with all services and amounts
        $noteParts = ["Pembayaran Invoice - {$child->name} bulan {$month}/{$year}"];
        foreach ($child->therapyTypes as $therapy) {
            $sessions = $therapy->pivot->monthly_sessions ?? 4;
            $amount = (float) $therapy->price_per_session * (int) $sessions;
            $noteParts[] = "Terapi {$therapy->name}: {$sessions}x Rp " . number_format($amount, 0, ',', '.');
        }
        foreach ($child->vocationalTypes as $vokasi) {
            $sessions = $vokasi->pivot->monthly_sessions ?? 4;
            $amount = (float) $vokasi->price_per_session * (int) $sessions;
            $noteParts[] = "Vokasi {$vokasi->name}: {$sessions}x Rp " . number_format($amount, 0, ',', '.');
        }
        if ($child->isTakingSekolah() && $child->spp_fee > 0) {
            $noteParts[] = "SPP: Rp " . number_format((float) $child->spp_fee, 0, ',', '.');
        }
        if ($child->has_parent_support) {
            $parentSupportFee = (float) config('settings.parent_support_fee', 25000);
            $noteParts[] = "Parent Support: Rp " . number_format($parentSupportFee, 0, ',', '.');
        }
        if ($child->has_subsidi && (float) $child->subsidi_amount > 0) {
            $noteParts[] = "Subsidi: -Rp " . number_format((float) $child->subsidi_amount, 0, ',', '.');
        }

        $notes = implode(' | ', $noteParts);

        // Create single income record with net amount (gross - subsidi)
        if ($primaryCategory) {
            \App\Models\Income::create([
                'child_id' => $child->id,
                'income_category_id' => $primaryCategory->id,
                'date' => $date,
                'amount' => (float) $payment->amount,
                'wallet_id' => $defaultWallet->id,
                'notes' => $notes,
            ]);
        }
    }

    private function getPaymentStatus(Child $child, int $month, int $year): string
    {
        $payment = $child->invoicePayments()
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($payment) {
            return $payment->is_paid ? 'paid' : 'unpaid';
        }

        $invoiceAmount = $child->calculateInvoiceAmount($month, $year);
        if ($invoiceAmount <= 0) {
            return 'paid';
        }

        $totalIncome = $child->incomes()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        return $totalIncome >= $invoiceAmount ? 'paid' : 'unpaid';
    }

    private function getUnpaidMonths(Child $child): array
    {
        $months = [];
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // If child was created in current month, don't show previous months as unpaid
        $childCreatedMonth = (int) $child->created_at?->format('m');
        $childCreatedYear = (int) $child->created_at?->format('Y');
        $skipPreviousMonths = ($childCreatedYear === $currentYear && $childCreatedMonth === $currentMonth);

        for ($m = 1; $m <= 12; $m++) {
            // Skip months before child was created
            if ($skipPreviousMonths && $m < $currentMonth) {
                continue;
            }
            $status = $this->getPaymentStatus($child, $m, $currentYear);
            if ($status === 'unpaid' && $m <= $currentMonth) {
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
