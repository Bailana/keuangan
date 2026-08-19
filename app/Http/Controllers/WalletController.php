<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::with('walletBalances')
            ->select(['id', 'name', 'slug', 'is_default', 'initial_balance', 'owner_name', 'account_number'])
            ->get()
            ->map(function ($wallet) {
                $wallet->current_balance = $wallet->getCurrentBalance();
                $wallet->month_start = $wallet->walletBalances()
                    ->where('month', '>=', now()->startOfMonth())
                    ->latest()
                    ->first();
                return $wallet;
            });

        return view('wallets.index', compact('wallets'));
    }

    public function create()
    {
        return view('wallets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:wallets,name',
            'owner_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
        ]);

        $wallet = Wallet::create($request->only(['name', 'owner_name', 'account_number']));

        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil ditambahkan.');
    }

    public function edit(Wallet $wallet)
    {
        return view('wallets.edit', compact('wallet'));
    }

    public function update(Request $request, Wallet $wallet)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:wallets,name,' . $wallet->id,
            'owner_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
        ]);

        $wallet->update($request->only(['name', 'owner_name', 'account_number']));

        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil diperbarui.');
    }

    public function destroy(Wallet $wallet)
    {
        if ($wallet->expenses()->count() > 0 || $wallet->incomes()->count() > 0) {
            return redirect()->route('wallets.index')->with('error', 'Dompet memiliki transaksi dan tidak dapat dihapus.');
        }

        $wallet->delete();
        return redirect()->route('wallets.index')->with('success', 'Dompet berhasil dihapus.');
    }

    public function setDefault(Request $request, Wallet $wallet)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        Wallet::where('is_default', true)->update(['is_default' => false]);
        $wallet->update(['is_default' => true]);

        return back()->with('success', $wallet->name . ' ditetapkan sebagai dompet default.');
    }

    public function setInitialBalance(Request $request, Wallet $wallet)
    {
        $request->validate([
            'balance' => 'required|numeric|min:0',
        ]);

        $wallet->initial_balance = $request->balance;
        $wallet->save();

        $balance = new WalletBalance();
        $balance->wallet_id = $wallet->id;
        $balance->balance = $request->balance;
        $balance->month = now()->startOfMonth();
        $balance->note = 'Saldo awal';
        $balance->save();

        return back()->with('success', 'Saldo awal berhasil disimpan.');
    }

    public function downloadStatement(Request $request)
    {
        $request->validate([
            'wallet_slug' => 'required|exists:wallets,slug',
            'month' => 'required|date_format:Y-m',
        ]);

        $wallet = Wallet::where('slug', $request->wallet_slug)->firstOrFail();
        $month = Carbon::parse($request->month);

        // Expense records for this wallet
        $records = DB::table('expenses')
            ->select(
                'expenses.date',
                'expenses.title',
                'expenses.amount',
                'expenses.expense_category_id',
                'expense_categories.name as category_name'
            )
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->where('expenses.wallet_id', $wallet->id)
            ->whereYear('expenses.date', $month->year)
            ->whereMonth('expenses.date', $month->month)
            ->orderBy('expenses.date')
            ->get();

        // Income records for this wallet
        $incomeRecords = DB::table('incomes')
            ->select(
                'incomes.date',
                'incomes.amount',
                'incomes.sender_name',
                'income_categories.name as category_name'
            )
            ->join('income_categories', 'incomes.income_category_id', '=', 'income_categories.id')
            ->where('incomes.wallet_id', $wallet->id)
            ->whereYear('incomes.date', $month->year)
            ->whereMonth('incomes.date', $month->month)
            ->orderBy('incomes.date')
            ->get();

        $income = $incomeRecords->sum('amount');
        $expense = $records->sum('amount');

        // Calculate opening balance using the new method
        $openingBalance = $wallet->getOpeningBalance($month->format('Y-m-d'), $month->copy()->endOfMonth()->format('Y-m-d'));
        $currentBalance = $openingBalance + $income - $expense;

        return view('wallets.statement', compact(
            'wallet', 'records', 'incomeRecords', 'income', 'expense', 'openingBalance', 'currentBalance', 'month'
        ));
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'wallet_slug' => 'required|exists:wallets,slug',
            'month' => 'required|date_format:Y-m',
        ]);

        $wallet = Wallet::where('slug', $request->wallet_slug)->firstOrFail();
        $month = Carbon::parse($request->month);

        // Fetch all invoice_payments for this wallet's children this month
        $invoicePayments = DB::table('invoice_payments')
            ->select('child_id', 'month', 'year', 'amount', 'notes')
            ->where('month', $month->month)
            ->where('year', $month->year)
            ->whereIn('child_id', function ($q) use ($wallet) {
                $q->select('child_id')
                    ->from('incomes')
                    ->where('wallet_id', $wallet->id);
            })
            ->pluck('amount', 'child_id');

        // Income records with child info for subsidi detection
        $incomeRecords = DB::table('incomes')
            ->select(
                'incomes.date',
                'incomes.amount',
                'incomes.sender_name',
                'incomes.notes',
                'incomes.child_id',
                'incomes.income_category_id',
                'income_categories.name as category_name'
            )
            ->join('income_categories', 'incomes.income_category_id', '=', 'income_categories.id')
            ->leftJoin('children', 'incomes.child_id', '=', 'children.id')
            ->where('incomes.wallet_id', $wallet->id)
            ->whereYear('incomes.date', $month->year)
            ->whereMonth('incomes.date', $month->month)
            ->orderBy('incomes.date')
            ->get()
            ->map(function ($r) use ($invoicePayments) {
                $r->type       = 'income';
                $r->child_id   = $r->child_id ?? null;
                $r->child_name = $r->child_name ?? null;

                // Use invoice estimate description for child payments
                if (!empty($r->child_id) && !empty($r->child_name)) {
                    $invoiceAmount = $invoicePayments->get($r->child_id);
                    $r->keterangan = 'Estimasi tagihan bulanan - ' . $r->child_name
                        . (!empty($invoiceAmount) ? ' (Rp ' . number_format((float)$invoiceAmount, 0, ',', '.') . ')' : '');
                } else {
                    $r->keterangan = trim((string)($r->notes ?? '')) !== ''
                        ? $r->notes
                        : ($r->sender_name ?? '-');
                }
                $r->is_subsidi = !empty($r->child_id) && !empty($r->child_name);
                return $r;
            });

        // Expense records
        $expenseRecords = DB::table('expenses')
            ->select(
                'expenses.date',
                'expenses.title',
                'expenses.notes',
                'expenses.amount',
                'expense_categories.name as category_name'
            )
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->where('expenses.wallet_id', $wallet->id)
            ->whereYear('expenses.date', $month->year)
            ->whereMonth('expenses.date', $month->month)
            ->orderBy('expenses.date')
            ->get()
            ->map(function ($r) {
                $r->type       = 'expense';
                $r->keterangan = trim((string)($r->notes ?? '')) !== ''
                    ? $r->notes
                    : ($r->title ?? '-');
                $r->is_subsidi = str_starts_with($r->title ?? '', 'Diskon Subsidi -');
                return $r;
            });

        // Merge & sort by date (preserve original order for same-date entries)
        $allTransactions = $incomeRecords->merge($expenseRecords)
            ->values()
            ->sortBy(function ($r) {
                return [$r->date, $r->type === 'income' ? 0 : 1];
            }, SORT_ASC, true)
            ->values();

        // Calculate running balance (set after transaction, not before)
        $openingBalance = $wallet->getOpeningBalance($month->format('Y-m-d'), $month->copy()->endOfMonth()->format('Y-m-d'));
        $running = $openingBalance;
        $allTransactions = $allTransactions->map(function ($r) use (&$running) {
            if ($r->type === 'income') {
                $running += (float) $r->amount;
            } else {
                $running -= (float) $r->amount;
            }
            $r->saldo = $running;
            return $r;
        })->values();

        $income = $incomeRecords->sum('amount');
        $expense = $expenseRecords->sum('amount');
        $currentBalance = $openingBalance + $income - $expense;

        // Total subsidi: sum of expense records created as "Diskon Subsidi"
        $totalSubsidi = DB::table('expenses')
            ->where('wallet_id', $wallet->id)
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->where('title', 'like', 'Diskon Subsidi%')
            ->sum('amount');

        $pdf = Pdf::loadView('wallets.pdf-export', compact(
            'wallet', 'allTransactions', 'incomeRecords', 'expenseRecords',
            'income', 'expense', 'openingBalance', 'currentBalance', 'month',
            'totalSubsidi'
        ));

        return $pdf->download('E-Statement-' . $wallet->name . '-' . $month->format('F-Y') . '.pdf');
    }
}
