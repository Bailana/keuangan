<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\FinancialPlan;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $nextMonth = $now->addMonth()->month;
        $nextYear = $now->addMonth()->year;

        $totalIncome = Income::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $totalExpense = Expense::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        $incomeByChild = Income::selectRaw('child_id, SUM(amount) as total')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->groupBy('child_id')
            ->with('child:id,name')
            ->get();

        $expenseByCategory = Expense::selectRaw('expense_category_id, SUM(amount) as total')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->groupBy('expense_category_id')
            ->with('category:id,name')
            ->get();

        $recentIncomes = Income::with(['child', 'category'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        $recentExpenses = Expense::with(['category'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        $months = collect(range(5, 0))->map(function ($i) use ($now) {
            $m = $now->copy()->subMonths($i);
            return [
                'label' => $m->locale('id')->format('M y'),
                'month' => $m->month,
                'year' => $m->year,
            ];
        })->reverse();

        $monthlyIncome = [];
        $monthlyExpense = [];
        $monthLabels = [];
        foreach ($months as $m) {
            $monthLabels[] = $m['label'];
            $monthlyIncome[] = Income::whereMonth('date', $m['month'])
                ->whereYear('date', $m['year'])
                ->sum('amount');
            $monthlyExpense[] = Expense::whereMonth('date', $m['month'])
                ->whereYear('date', $m['year'])
                ->sum('amount');
        }

        $nextPlans = FinancialPlan::where('month', $nextMonth)
            ->where('year', $nextYear)
            ->orderBy('type')
            ->get();

        $incomePlans = $nextPlans->where('type', 'income');
        $expensePlans = $nextPlans->where('type', 'expense');

        // Default wallet
        $defaultWallet = Wallet::where('is_default', true)->first();

        // Wallet balances
        $walletBalances = Wallet::select(['id', 'name', 'slug', 'initial_balance'])
            ->get()
            ->map(function ($wallet) {
                $wallet->current_balance = $wallet->getCurrentBalance();
                return $wallet;
            });

        // Chart data per selected wallet (default if none specified, all if no default)
        $selectedWalletId = request('wallet_id', $defaultWallet?->id);
        $selectedWallet = Wallet::find($selectedWalletId);

        $monthlyIncome = [];
        $monthlyExpense = [];
        $monthLabels = [];
        foreach ($months as $m) {
            $monthLabels[] = $m['label'];
            if ($selectedWallet) {
                $monthlyIncome[] = Income::where('wallet_id', $selectedWallet->id)
                    ->whereMonth('date', $m['month'])
                    ->whereYear('date', $m['year'])
                    ->sum('amount');
                $monthlyExpense[] = Expense::where('wallet_id', $selectedWallet->id)
                    ->whereMonth('date', $m['month'])
                    ->whereYear('date', $m['year'])
                    ->sum('amount');
            } else {
                $monthlyIncome[] = Income::whereMonth('date', $m['month'])
                    ->whereYear('date', $m['year'])
                    ->sum('amount');
                $monthlyExpense[] = Expense::whereMonth('date', $m['month'])
                    ->whereYear('date', $m['year'])
                    ->sum('amount');
            }
        }

        return view('dashboard', compact(
            'totalIncome', 'totalExpense', 'balance',
            'incomeByChild', 'expenseByCategory',
            'recentIncomes', 'recentExpenses',
            'incomePlans', 'expensePlans', 'nextPlans',
            'currentMonth', 'currentYear',
            'monthLabels', 'monthlyIncome', 'monthlyExpense',
            'walletBalances',
            'defaultWallet',
        ));
    }
}
