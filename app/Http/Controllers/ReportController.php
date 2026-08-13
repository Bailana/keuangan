<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\Child;
use App\Models\Wallet;
use App\Models\InvoicePayment;
use App\Exports\ProfitLossExport;
use App\Exports\RevenueExport;
use App\Exports\ArrearsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ActivityLog;

class ReportController extends Controller
{
    private function applyWalletFilter($query, $walletId)
    {
        if ($walletId) {
            $query->where('wallet_id', $walletId);
        }
        return $query;
    }

    public function profitLoss(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month');
        $walletId = $request->input('wallet_id');

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::create($year, $m, 1)->locale('id')->format('F Y');
        }

        if ($month) {
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();
        } else {
            $startDate = Carbon::create($year, 1, 1);
            $endDate = Carbon::create($year, 12, 31);
        }

        $incomeQuery = $this->applyWalletFilter(
            Income::selectRaw('income_category_id, SUM(amount) as total')->whereBetween('date', [$startDate, $endDate])->groupBy('income_category_id')->with('category'),
            $walletId
        );
        $expenseQuery = $this->applyWalletFilter(
            Expense::selectRaw('expense_category_id, SUM(amount) as total')->whereBetween('date', [$startDate, $endDate])->groupBy('expense_category_id')->with('category'),
            $walletId
        );

        $incomeByCategory = $incomeQuery->get();
        $expenseByCategory = $expenseQuery->get();

        $totalIncome = $this->applyWalletFilter(Income::whereBetween('date', [$startDate, $endDate]), $walletId)->sum('amount');
        $totalExpense = $this->applyWalletFilter(Expense::whereBetween('date', [$startDate, $endDate]), $walletId)->sum('amount');
        $netProfit = $totalIncome - $totalExpense;
        $margin = $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0;

        $monthlyIncome = [];
        $monthlyExpense = [];
        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($year, $m, 1);
            $end = $start->copy()->endOfMonth();
            $monthlyIncome[$m] = $this->applyWalletFilter(Income::whereBetween('date', [$start, $end]), $walletId)->sum('amount');
            $monthlyExpense[$m] = $this->applyWalletFilter(Expense::whereBetween('date', [$start, $end]), $walletId)->sum('amount');
        }

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = [
                'label' => $months[$m],
                'income' => $monthlyIncome[$m],
                'expense' => $monthlyExpense[$m],
                'net' => $monthlyIncome[$m] - $monthlyExpense[$m],
            ];
        }

        $wallets = Wallet::all(['id', 'name']);
        $selectedWallet = $walletId ? $wallets->firstWhere('id', $walletId) : null;

        return view('reports.profit-loss', compact(
            'incomeByCategory', 'expenseByCategory',
            'totalIncome', 'totalExpense', 'netProfit', 'margin',
            'months', 'year', 'month', 'monthlyData',
            'wallets', 'selectedWallet', 'walletId'
        ));
    }

    public function exportProfitLossPdf(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month');
        $walletId = $request->input('wallet_id');

        if ($month) {
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();
        } else {
            $startDate = Carbon::create($year, 1, 1);
            $endDate = Carbon::create($year, 12, 31);
        }

        $incomeByCategory = $this->applyWalletFilter(
            Income::selectRaw('income_category_id, SUM(amount) as total')->whereBetween('date', [$startDate, $endDate])->groupBy('income_category_id')->with('category'),
            $walletId
        )->get();
        $expenseByCategory = $this->applyWalletFilter(
            Expense::selectRaw('expense_category_id, SUM(amount) as total')->whereBetween('date', [$startDate, $endDate])->groupBy('expense_category_id')->with('category'),
            $walletId
        )->get();
        $totalIncome = $this->applyWalletFilter(Income::whereBetween('date', [$startDate, $endDate]), $walletId)->sum('amount');
        $totalExpense = $this->applyWalletFilter(Expense::whereBetween('date', [$startDate, $endDate]), $walletId)->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export_pdf',
            'subject_type' => Income::class,
            'description' => auth()->user()->name . ' mengekspor PDF Laporan Laba/Rugi (' . date('F Y', strtotime($startDate)) . ', total Rp ' . number_format($netProfit, 0, ',', '.') . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $pdf = Pdf::loadView('reports.pdf.profit-loss', [
            'incomeByCategory' => $incomeByCategory,
            'expenseByCategory' => $expenseByCategory,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netProfit' => $netProfit,
            'margin' => $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0,
            'period' => $month ? Carbon::create($year, $month, 1)->locale('id')->format('F Y') : 'Tahun ' . $year,
            'wallet' => $walletId ? Wallet::find($walletId) : null,
            'generatedDate' => now()->format('d-m-Y'),
        ]);

        return $pdf->download('Laporan-Laba-Rugi-' . date('Y-m') . '.pdf');
    }

    public function exportProfitLossExcel(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month');
        $walletId = $request->input('wallet_id');

        if ($month) {
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();
        } else {
            $startDate = Carbon::create($year, 1, 1);
            $endDate = Carbon::create($year, 12, 31);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export_excel',
            'subject_type' => Income::class,
            'description' => auth()->user()->name . ' mengekspor Excel Laporan Laba/Rugi (' . date('F Y', strtotime($startDate)) . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Excel::download(new ProfitLossExport($startDate, $endDate, $walletId), 'Laporan-Laba-Rugi-' . date('Y-m') . '.xlsx');
    }

    public function revenue(Request $request) {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $groupBy = $request->input('group_by', 'category');
        $walletId = $request->input('wallet_id');

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $incomeQuery = $this->applyWalletFilter(
            Income::selectRaw('income_category_id, SUM(amount) as total')->whereBetween('date', [$startDate, $endDate])->groupBy('income_category_id')->with('category'),
            $walletId
        );
        $incomeByCategory = $incomeQuery->get();

        $childQuery = $this->applyWalletFilter(
            Income::selectRaw('child_id, SUM(amount) as total')->whereBetween('date', [$startDate, $endDate])->whereNotNull('child_id')->groupBy('child_id')->with('child'),
            $walletId
        );
        $incomeByChild = $childQuery->get();

        $incomeByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($year, $m, 1);
            $end = $start->copy()->endOfMonth();
            $incomeByMonth[$m] = $this->applyWalletFilter(Income::whereBetween('date', [$start, $end]), $walletId)->sum('amount');
        }

        $therapyQuery = $this->applyWalletFilter(
            Income::selectRaw('child_id, SUM(amount) as total')
                ->whereBetween('date', [$startDate, $endDate])
                ->whereHas('child', function ($q) {
                    $q->whereHas('therapyTypes');
                })
                ->groupBy('child_id'),
            $walletId
        );
        $therapyIncome = $therapyQuery->get();

        $vocationalQuery = $this->applyWalletFilter(
            Income::selectRaw('child_id, SUM(amount) as total')
                ->whereBetween('date', [$startDate, $endDate])
                ->whereHas('child', function ($q) {
                    $q->whereHas('vocationalTypes');
                })
                ->groupBy('child_id'),
            $walletId
        );
        $vocationalIncome = $vocationalQuery->get();

        $schoolQuery = $this->applyWalletFilter(
            Income::selectRaw('child_id, SUM(amount) as total')
                ->whereBetween('date', [$startDate, $endDate])
                ->whereHas('child', function ($q) {
                    $q->whereNotNull('class_name');
                })
                ->groupBy('child_id'),
            $walletId
        );
        $schoolIncome = $schoolQuery->get();

        $totalIncome = $this->applyWalletFilter(Income::whereBetween('date', [$startDate, $endDate]), $walletId)->sum('amount');

        $wallets = Wallet::all(['id', 'name']);
        $selectedWallet = $walletId ? $wallets->firstWhere('id', $walletId) : null;

        return view('reports.revenue', compact(
            'incomeByCategory', 'incomeByChild',
            'therapyIncome', 'vocationalIncome', 'schoolIncome',
            'totalIncome', 'incomeByMonth',
            'year', 'month', 'groupBy',
            'wallets', 'selectedWallet', 'walletId'
        ));
    }

    public function exportRevenuePdf(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $walletId = $request->input('wallet_id');

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $incomeByCategory = $this->applyWalletFilter(
            Income::selectRaw('income_category_id, SUM(amount) as total')->whereBetween('date', [$startDate, $endDate])->groupBy('income_category_id')->with('category'),
            $walletId
        )->get();
        $incomeByChild = $this->applyWalletFilter(
            Income::selectRaw('child_id, SUM(amount) as total')->whereBetween('date', [$startDate, $endDate])->whereNotNull('child_id')->groupBy('child_id')->with('child'),
            $walletId
        )->get();
        $totalIncome = $this->applyWalletFilter(Income::whereBetween('date', [$startDate, $endDate]), $walletId)->sum('amount');

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export_pdf',
            'subject_type' => Income::class,
            'description' => auth()->user()->name . ' mengekspor PDF Laporan Pendapatan (' . Carbon::create($year, $month, 1)->locale('id')->format('F Y') . ', total Rp ' . number_format($totalIncome, 0, ',', '.') . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $pdf = Pdf::loadView('reports.pdf.revenue', [
            'incomeByCategory' => $incomeByCategory,
            'incomeByChild' => $incomeByChild,
            'totalIncome' => $totalIncome,
            'period' => Carbon::create($year, $month, 1)->locale('id')->format('F Y'),
            'wallet' => $walletId ? Wallet::find($walletId) : null,
            'generatedDate' => now()->format('d-m-Y'),
        ]);

        return $pdf->download('Laporan-Pendapatan-' . date('Y-m') . '.pdf');
    }

    public function exportRevenueExcel(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $walletId = $request->input('wallet_id');

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export_excel',
            'subject_type' => Income::class,
            'description' => auth()->user()->name . ' mengekspor Excel Laporan Pendapatan (' . Carbon::create($year, $month, 1)->locale('id')->format('F Y') . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Excel::download(new RevenueExport($startDate, $endDate, $walletId), 'Laporan-Pendapatan-' . date('Y-m') . '.xlsx');
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
            return 'paid';
        }

        $totalPaid = $child->incomes()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        if ($totalPaid >= $invoiceAmount) {
            return 'paid';
        }

        if ($totalPaid > 0) {
            return 'partial';
        }

        $dueDate = Carbon::create($year, $month, 10);
        if (now()->isAfter($dueDate)) {
            return 'overdue';
        }

        return 'unpaid';
    }

    public function agingSchedule(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $walletId = $request->input('wallet_id');

        $children = Child::query()
            ->with(['invoicePayments', 'therapyTypes', 'vocationalTypes'])
            ->get();

        $aging = [
            'current' => 0,
            '30days' => 0,
            '60days' => 0,
            'over60days' => 0,
        ];

        foreach ($children as $child) {
            $outstanding = $this->getOutstandingAmount($child, $month, $year, $walletId);
            if ($outstanding > 0) {
                $dueDate = Carbon::create($year, $month, 10);
                $daysOverdue = now()->diffInDays($dueDate, false);

                if ($daysOverdue <= 0) {
                    $aging['current'] += $outstanding;
                } elseif ($daysOverdue <= 30) {
                    $aging['30days'] += $outstanding;
                } elseif ($daysOverdue <= 60) {
                    $aging['60days'] += $outstanding;
                } else {
                    $aging['over60days'] += $outstanding;
                }
            }
        }

        return view('reports.aging', compact('aging', 'year', 'month'));
    }

    private function getOutstandingAmount(Child $child, int $month, int $year, ?int $walletId = null): float
    {
        $payment = $child->invoicePayments()
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($payment && $payment->is_paid) {
            return 0;
        }

        $invoiceAmount = $child->calculateInvoiceAmount($month, $year);
        if ($invoiceAmount <= 0) {
            return 0;
        }

        $incomeQuery = $child->incomes()->whereMonth('date', $month)->whereYear('date', $year);
        if ($walletId) {
            $incomeQuery->where('wallet_id', $walletId);
        }
        $totalPaid = $incomeQuery->sum('amount');

        return max(0, $invoiceAmount - $totalPaid);
    }
}
