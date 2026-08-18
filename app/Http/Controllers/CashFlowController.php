<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\Child;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use App\Models\Wallet;
use Illuminate\Http\Request;

class CashFlowController extends Controller
{
    public function index()
    {
        $children = Child::all();
        $incomeCategories = IncomeCategory::whereIn('name', ['SPP', 'Terapi', 'Vokasi', 'Lain-lain'])
            ->orderByRaw("CASE name
                WHEN 'SPP' THEN 1
                WHEN 'Terapi' THEN 2
                WHEN 'Vokasi' THEN 3
                WHEN 'Lain-lain' THEN 4
                ELSE 5
            END")
            ->get();
        $expenseCategories = ExpenseCategory::all()
            ->filter(fn($cat) => $cat->name !== 'SPP' && $cat->name !== 'Terapi')
            ->sort(function ($a, $b) {
                if ($a->name === 'Lain-lain') return 1;
                if ($b->name === 'Lain-lain') return -1;
                return $a->name <=> $b->name;
            });
        $wallets = Wallet::all()->map(function ($w) {
            $w->current_balance = $w->getCurrentBalance();
            return $w;
        });

        // Income query
        $incomeQuery = Income::with(['child', 'category', 'wallet']);

        if (request()->filled('child_id')) {
            $incomeQuery->where('child_id', request('child_id'));
        }
        if (request()->filled('income_category_id')) {
            $incomeQuery->where('income_category_id', request('income_category_id'));
        }
        if (request()->filled('date_from')) {
            $incomeQuery->where('date', '>=', request('date_from'));
        }
        if (request()->filled('date_to')) {
            $incomeQuery->where('date', '<=', request('date_to'));
        }
        if (request()->filled('wallet_id')) {
            $incomeQuery->where('wallet_id', request('wallet_id'));
        }
        if (request()->filled('search')) {
            $incomeQuery->where(function ($q) {
                $q->whereHas('child', function ($q) {
                    $q->where('name', 'like', '%' . request('search') . '%');
                })
                ->orWhereHas('category', function ($q) {
                    $q->where('name', 'like', '%' . request('search') . '%');
                })
                ->orWhere('notes', 'like', '%' . request('search') . '%');
            });
        }
        $incomePerPage = (int) max(5, min(100, request('income_per_page', 15)));
        $incomes = $incomeQuery->latest()->paginate($incomePerPage);
        $totalIncome = $incomeQuery->sum('amount');

        // Expense query
        $expenseQuery = Expense::with(['category', 'wallet']);

        if (request()->filled('expense_category_id')) {
            $expenseQuery->where('expense_category_id', request('expense_category_id'));
        }
        if (request()->filled('date_from')) {
            $expenseQuery->where('date', '>=', request('date_from'));
        }
        if (request()->filled('date_to')) {
            $expenseQuery->where('date', '<=', request('date_to'));
        }
        if (request()->filled('wallet_id')) {
            $expenseQuery->where('wallet_id', request('wallet_id'));
        }
        if (request()->filled('search')) {
            $expenseQuery->where(function ($q) {
                $q->whereHas('category', function ($q) {
                    $q->where('name', 'like', '%' . request('search') . '%');
                })
                ->orWhere('recipient', 'like', '%' . request('search') . '%')
                ->orWhere('title', 'like', '%' . request('search') . '%');
            });
        }
        $expensePerPage = (int) max(5, min(100, request('expense_per_page', 15)));
        $expenses = $expenseQuery->latest()->paginate($expensePerPage);
        $totalExpense = $expenseQuery->sum('amount');

        $selectedWallet = request('wallet_id') ? $wallets->firstWhere('id', request('wallet_id')) : null;

        return view('cash-flows.index', compact(
            'incomes', 'expenses', 'children',
            'incomeCategories', 'expenseCategories', 'wallets',
            'totalIncome', 'totalExpense', 'selectedWallet',
            'incomePerPage', 'expensePerPage'
        ));
    }
}
