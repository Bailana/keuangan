<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ActivityLog;
use App\Exports\ExpenseExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseController extends Controller
{
    private function buildFilteredQuery()
    {
        $query = Expense::with(['category']);

        if (request()->filled('category_id')) {
            $query->where('expense_category_id', request('category_id'));
        }

        if (request()->filled('date_from')) {
            $query->where('date', '>=', request('date_from'));
        }

        if (request()->filled('date_to')) {
            $query->where('date', '<=', request('date_to'));
        }

        if (request()->filled('bank')) {
            $query->where('bank_or_wallet', 'like', '%' . request('bank') . '%');
        }

        if (request()->filled('search')) {
            $query->where(function ($q) {
                $q->whereHas('category', function ($q) {
                    $q->where('name', 'like', '%' . request('search') . '%');
                })
                ->orWhere('recipient', 'like', '%' . request('search') . '%');
            });
        }

        return $query->latest();
    }

    private function getActiveFilters(): array
    {
        $summary = [];
        if (request()->filled('category_id')) {
            $category = ExpenseCategory::find(request('category_id'));
            $summary['Kategori'] = $category?->name;
        }
        if (request()->filled('bank')) {
            $summary['Bank/Dompet'] = request('bank');
        }
        if (request()->filled('date_from')) {
            $summary['Dari'] = request('date_from');
        }
        if (request()->filled('date_to')) {
            $summary['Sampai'] = request('date_to');
        }
        if (request()->filled('search')) {
            $summary['Pencarian'] = request('search');
        }
        return $summary;
    }

    public function index()
    {
        $query = $this->buildFilteredQuery();
        $expenses = $query->paginate(15)->withQueryString();
        $totalExpense = (clone $query)->sum('amount');

        $categories = ExpenseCategory::all(['id', 'name']);

        $wallets = \App\Models\Wallet::all(['id', 'name']);
        return view('expenses.index', compact('expenses', 'categories', 'totalExpense', 'wallets'));
    }

    public function createModal()
    {
        $categories = ExpenseCategory::all(['id', 'name']);
        $wallets = \App\Models\Wallet::all(['id', 'name']);
        return view('expenses.create', compact('categories', 'wallets'));
    }

    public function create()
    {
        $categories = ExpenseCategory::all();
        $wallets = \App\Models\Wallet::all(['id', 'name']);
        return view('expenses.create', compact('categories', 'wallets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:200',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'bank_or_wallet' => 'nullable|string|max:100',
            'wallet_id' => 'nullable|exists:wallets,id',
            'recipient' => 'nullable|string|max:200',
            'receipt_url' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('receipt_url')) {
            $path = $request->file('receipt_url')->store('receipts', 'public');
            $validated['receipt_url'] = $path;
        }

        Expense::create($validated);
        return redirect()->route('cash-flows', ['wallet_id' => request('wallet_id')])->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::all();
        $wallets = \App\Models\Wallet::all(['id', 'name']);
        return view('expenses.edit', compact('expense', 'categories', 'wallets'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:200',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'bank_or_wallet' => 'nullable|string|max:100',
            'wallet_id' => 'nullable|exists:wallets,id',
            'recipient' => 'nullable|string|max:200',
            'receipt_url' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('receipt_url')) {
            if ($expense->receipt_url) {
                \Storage::disk('public')->delete($expense->receipt_url);
            }
            $path = $request->file('receipt_url')->store('receipts', 'public');
            $validated['receipt_url'] = $path;
        }

        $expense->update($validated);
        return redirect()->route('cash-flows', ['wallet_id' => request('wallet_id')])->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_url) {
            \Storage::disk('public')->delete($expense->receipt_url);
        }
        $expense->delete();
        return redirect()->route('cash-flows', ['wallet_id' => request('wallet_id')])->with('success', 'Pengeluaran berhasil dihapus.');
    }

    public function exportPdf()
    {
        $records = $this->buildFilteredQuery()->get();
        $total = $records->sum('amount');
        $filters = $this->getActiveFilters();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export_pdf',
            'subject_type' => Expense::class,
            'description' => auth()->user()->name . ' mengekspor PDF laporan pengeluaran (' . count($records) . ' data, total Rp ' . number_format($total, 0, ',', '.') . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $pdf = Pdf::loadView('expenses.pdf-export', [
            'records' => $records,
            'total' => $total,
            'filters' => $filters,
            'generatedDate' => now()->format('d-m-Y'),
        ]);

        return $pdf->download('Laporan-Pengeluaran-' . now()->format('Ymd-His') . '.pdf');
    }

    public function exportExcel()
    {
        $records = $this->buildFilteredQuery()->get();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export_excel',
            'subject_type' => Expense::class,
            'description' => auth()->user()->name . ' mengekspor Excel laporan pengeluaran (' . count($records) . ' data)',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Excel::download(
            new ExpenseExport($records),
            'Laporan-Pengeluaran-' . now()->format('Ymd-His') . '.xlsx'
        );
    }
}
