<?php

namespace App\Http\Controllers;

use App\Models\FinancialPlan;
use App\Exports\FinancialPlanExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ActivityLog;

class FinancialPlanController extends Controller
{
    public function index()
    {
        $query = FinancialPlan::query();

        if (request()->filled('type')) {
            $query->where('type', request('type'));
        }

        if (request()->filled('category')) {
            $query->where('category', request('category'));
        }

        if (request()->filled('year')) {
            $query->where('year', request('year'));
        }

        if (request()->filled('month')) {
            $query->where('month', request('month'));
        }

        $plans = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(15)
            ->withQueryString();

        $years = range(2024, 2030);
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $totalIncome = $query->clone()->where('type', 'income')->sum('target_amount');
        $totalExpense = $query->clone()->where('type', 'expense')->sum('target_amount');
        $balance = $totalIncome - $totalExpense;

        return view('plans.index', compact('plans', 'years', 'months', 'totalIncome', 'totalExpense', 'balance'));
    }

    public function exportPdf(Request $request)
    {
        $query = FinancialPlan::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        $plans = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $totalIncome = $query->clone()->where('type', 'income')->sum('target_amount');
        $totalExpense = $query->clone()->where('type', 'expense')->sum('target_amount');

        $filters = [
            'type' => $request->type ?? null,
            'category' => $request->category ?? null,
            'year' => $request->year ?? null,
            'month' => $request->month ?? null,
        ];

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export_pdf',
            'subject_type' => FinancialPlan::class,
            'description' => auth()->user()->name . ' mengekspor PDF Perencanaan Keuangan',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $kopSuratPath = public_path('images/kop_surat.png');
        $logoPath = public_path('images/logo_am.png');

        $pdf = Pdf::loadView('exports.financial-plan', [
            'plans' => $plans,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'filters' => $filters,
            'generatedDate' => now()->format('d F Y H:i'),
            'kopSuratPath' => $kopSuratPath,
            'logoPath' => $logoPath,
        ])
        ->setPaper('a4', 'portrait');

        return $pdf->download('Perencanaan-Keuangan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = FinancialPlan::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        $plans = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $totalIncome = $query->clone()->where('type', 'income')->sum('target_amount');
        $totalExpense = $query->clone()->where('type', 'expense')->sum('target_amount');

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'export_excel',
            'subject_type' => FinancialPlan::class,
            'description' => auth()->user()->name . ' mengekspor Excel Perencanaan Keuangan',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Excel::download(new FinancialPlanExport($plans, $totalIncome, $totalExpense), 'Perencanaan-Keuangan-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function create()
    {
        return view('plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'type' => 'required|in:income,expense',
            'category' => 'nullable|string|max:255',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2024',
            'target_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        FinancialPlan::create($validated);
        return redirect()->route('plans.index')->with('success', 'Perencanaan keuangan berhasil ditambahkan.');
    }

    public function edit(FinancialPlan $plan)
    {
        return view('plans.edit', compact('plan'));
    }

    public function update(Request $request, FinancialPlan $plan)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'type' => 'required|in:income,expense',
            'category' => 'nullable|string|max:255',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2024',
            'target_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $plan->update($validated);
        return redirect()->route('plans.index')->with('success', 'Perencanaan keuangan berhasil diperbarui.');
    }

    public function destroy(FinancialPlan $plan)
    {
        $plan->delete();
        return redirect()->route('plans.index')->with('success', 'Perencanaan keuangan berhasil dihapus.');
    }
}
