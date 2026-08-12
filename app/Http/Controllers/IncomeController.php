<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Child;
use App\Models\IncomeCategory;
use App\Models\Wallet;
use App\Models\ActivityLog;
use App\Exports\IncomeExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class IncomeController extends Controller
{
    private function buildFilteredQuery()
    {
        $query = Income::with(['child', 'category']);

        if (request()->filled('child_id')) {
            $query->where('child_id', request('child_id'));
        }

        if (request()->filled('category_id')) {
            $query->where('income_category_id', request('category_id'));
        }

        if (request()->filled('date_from')) {
            $query->where('date', '>=', request('date_from'));
        }

        if (request()->filled('date_to')) {
            $query->where('date', '<=', request('date_to'));
        }

        if (request()->filled('search')) {
            $query->where(function ($q) {
                $q->whereHas('child', function ($q) {
                    $q->where('name', 'like', '%' . request('search') . '%');
                })
                ->orWhereHas('category', function ($q) {
                    $q->where('name', 'like', '%' . request('search') . '%');
                })
                ->orWhere('notes', 'like', '%' . request('search') . '%');
            });
        }

        return $query->latest();
    }

    private function getActiveFilters(): array
    {
        $summary = [];
        if (request()->filled('child_id')) {
            $child = Child::find(request('child_id'));
            $summary['Anak'] = $child?->name;
        }
        if (request()->filled('category_id')) {
            $category = IncomeCategory::find(request('category_id'));
            $summary['Kategori'] = $category?->name;
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
        $incomes = $query->paginate(15)->withQueryString();
        $totalIncome = (clone $query)->sum('amount');

        $children = Child::all(['id', 'name', 'service']);
        $categories = IncomeCategory::all(['id', 'name']);
        $wallets = Wallet::all(['id', 'name']);

        return view('incomes.index', compact('incomes', 'children', 'categories', 'totalIncome', 'wallets'));
    }

    public function createModal()
    {
        $children = Child::all(['id', 'name', 'service']);
        $categories = IncomeCategory::whereIn('name', ['SPP', 'Terapi', 'Lain-lain'])->get(['id', 'name']);
        $wallets = Wallet::all(['id', 'name']);
        return view('incomes.create', compact('children', 'categories', 'wallets'));
    }

    public function create()
    {
        $children = Child::all();
        $categories = IncomeCategory::all();
        $wallets = Wallet::all(['id', 'name']);
        return view('incomes.create', compact('children', 'categories', 'wallets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id' => 'nullable|exists:children,id',
            'income_category_id' => 'required|exists:income_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'wallet_id' => 'nullable|exists:wallets,id',
            'notes' => 'nullable|string|max:500',
            'sender_name' => 'nullable|string|max:255',
        ]);

        Income::create($validated);
        return redirect()->route('cash-flows', ['wallet_id' => request('wallet_id')])->with('success', 'Pemasukan berhasil ditambahkan.');
    }

    public function editModal(Income $income)
    {
        $data = [
            'income' => $income,
            'children' => Child::all(['id', 'name', 'service']),
            'categories' => IncomeCategory::whereIn('name', ['SPP', 'Terapi', 'Lain-lain'])
                ->orderByRaw("CASE name WHEN 'SPP' THEN 1 WHEN 'Terapi' THEN 2 WHEN 'Lain-lain' THEN 3 ELSE 4 END")
                ->get(['id', 'name']),
            'wallets' => Wallet::all(['id', 'name']),
        ];
        return response()->json($data);
    }

    public function edit(Income $income)
    {
        $children = Child::all();
        $categories = IncomeCategory::all();
        $wallets = Wallet::all(['id', 'name']);
        return view('incomes.edit', compact('income', 'children', 'categories', 'wallets'));
    }

    public function update(Request $request, Income $income)
    {
        $validated = $request->validate([
            'child_id' => 'nullable|exists:children,id',
            'income_category_id' => 'required|exists:income_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'wallet_id' => 'nullable|exists:wallets,id',
            'notes' => 'nullable|string|max:500',
            'sender_name' => 'nullable|string|max:255',
        ]);

        $income->update($validated);
        return redirect()->route('cash-flows', ['wallet_id' => request('wallet_id')])->with('success', 'Pemasukan berhasil diperbarui.');
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return redirect()->route('cash-flows', ['wallet_id' => request('wallet_id')])->with('success', 'Pemasukan berhasil dihapus.');
    }

    public function exportPdf()
    {
        $records = $this->buildFilteredQuery()->get();
        $total = $records->sum('amount');
        $filters = $this->getActiveFilters();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export_pdf',
            'subject_type' => Income::class,
            'description' => auth()->user()->name . ' mengekspor PDF laporan pemasukan (' . count($records) . ' data, total Rp ' . number_format($total, 0, ',', '.') . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $pdf = Pdf::loadView('incomes.pdf-export', [
            'records' => $records,
            'total' => $total,
            'filters' => $filters,
            'generatedDate' => now()->format('d-m-Y'),
        ]);

        return $pdf->download('Laporan-Pemasukan-' . now()->format('Ymd-His') . '.pdf');
    }

    public function exportExcel()
    {
        $records = $this->buildFilteredQuery()->get();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export_excel',
            'subject_type' => Income::class,
            'description' => auth()->user()->name . ' mengekspor Excel laporan pemasukan (' . count($records) . ' data)',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return Excel::download(
            new IncomeExport($records),
            'Laporan-Pemasukan-' . now()->format('Ymd-His') . '.xlsx'
        );
    }
}
