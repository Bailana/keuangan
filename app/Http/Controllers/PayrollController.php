<?php

namespace App\Http\Controllers;

use App\Models\SalaryRecord;
use App\Models\SalaryPayment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $this->generateForCurrentMonth();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $query = SalaryRecord::query();

        if ($request->filled('month')) {
            // Handle combined month-year format like "9-2026"
            if (str_contains($request->month, '-')) {
                [$m, $y] = explode('-', $request->month);
                $query->where('month', (int)$m)->where('year', (int)$y);
            } else {
                $query->where('month', (int)$request->month);
                if ($request->filled('year')) {
                    $query->where('year', (int)$request->year);
                }
            }
        } else {
            $query->where('month', $currentMonth)->where('year', $currentYear);
        }

        if (!$request->filled('month') && $request->filled('employee_name')) {
            // Apply default month/year when only employee name is searched
            $query->where('month', $currentMonth)->where('year', $currentYear);
        }
        if ($request->filled('employee_name')) {
            $query->where('employee_name', 'like', '%' . $request->employee_name . '%');
        }
        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->where('paid', true);
            } elseif ($request->status === 'unpaid') {
                $query->where('paid', false);
            }
        }

        $perPage = (int) max(5, min(100, $request->input('per_page', 15)));
        $records = $query->orderBy('employee_name')->orderBy('salary_date', 'desc')->paginate($perPage);

        // Build stats based on the same query used for records
        $statsQuery = SalaryRecord::query();
        if ($request->filled('month')) {
            if (str_contains($request->month, '-')) {
                [$m, $y] = explode('-', $request->month);
                $statsQuery->where('month', (int)$m)->where('year', (int)$y);
            } else {
                $statsQuery->where('month', (int)$request->month);
                if ($request->filled('year')) {
                    $statsQuery->where('year', (int)$request->year);
                }
            }
        } else {
            $statsQuery->where('month', $currentMonth)->where('year', $currentYear);
        }

        $stats = [
            'total' => $statsQuery->count(),
            'paid' => (clone $statsQuery)->where('paid', true)->count(),
            'unpaid' => (clone $statsQuery)->where('paid', false)->count(),
            'total_paid' => (clone $statsQuery)->where('paid', true)->sum('net_salary'),
            'total_unpaid' => (clone $statsQuery)->where('paid', false)->sum('net_salary'),
        ];

        $months = [];
        for ($i = 0; $i < 24; $i++) {
            $date = now()->subMonths($i);
            $months[] = ['month' => $date->month, 'year' => $date->year];
        }

        $generatedCount = session('payroll_generated', 0);
        session()->forget('payroll_generated');

        // Store current query string so save/edit can redirect back to the same filtered+paginated view
        session(['payroll_last_index_query' => $request->query()]);

        return view('payroll.index', compact('records', 'stats', 'months', 'perPage', 'generatedCount', 'currentMonth', 'currentYear'));
    }

    public function create()
    {
        $salaryRecords = SalaryRecord::where('paid', false)->orderBy('employee_name')->orderBy('salary_date', 'desc')->limit(12)->get();
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        return view('payroll.create', compact('salaryRecords', 'employees', 'currentMonth', 'currentYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'salary_date' => 'required|date',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'base_salary' => 'nullable|numeric|min:0',
            'salary_extra' => 'nullable|numeric|min:0',
            'total_sessions' => 'nullable|numeric|min:0',
            'session_rate' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
        ]);

        $sessionBonus = ($validated['total_sessions'] ?? 0) * ($validated['session_rate'] ?? 0);

        $validated['base_salary'] = $validated['base_salary'] ?? 0;
        $validated['salary_extra'] = $validated['salary_extra'] ?? 0;
        $validated['transport_allowance'] = $validated['transport_allowance'] ?? 0;
        $validated['deductions'] = $validated['deductions'] ?? 0;

        DB::beginTransaction();
        try {
            $record = SalaryRecord::updateOrCreate(
                [
                    'employee_name' => $validated['employee_name'],
                    'month' => $validated['month'],
                    'year' => $validated['year'],
                ],
                [
                    'position' => $validated['position'],
                    'phone' => $validated['phone'],
                    'whatsapp' => $validated['whatsapp'],
                    'salary_date' => $validated['salary_date'],
                    'base_salary' => $validated['base_salary'],
                    'salary_extra' => $validated['salary_extra'],
                    'total_sessions' => $validated['total_sessions'],
                    'session_bonus' => $sessionBonus,
                    'transport_allowance' => $validated['transport_allowance'],
                    'deductions' => $validated['deductions'],
                ]
            );

            $record->calculateTotals();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        return $this->redirectPayrollIndexWithFilters()->with('success', 'Data gaji berhasil disimpan.');
    }

    public function edit(SalaryRecord $payroll)
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get();
        return view('payroll.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, SalaryRecord $payroll)
    {
        $validated = $request->validate([
            'employee_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'salary_date' => 'required|date',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'base_salary' => 'nullable|numeric|min:0',
            'salary_extra' => 'nullable|numeric|min:0',
            'total_sessions' => 'nullable|numeric|min:0',
            'session_rate' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
        ]);

        $sessionBonus = ($validated['total_sessions'] ?? 0) * ($validated['session_rate'] ?? 0);

        $validated['base_salary'] = $validated['base_salary'] ?? 0;
        $validated['salary_extra'] = $validated['salary_extra'] ?? 0;
        $validated['transport_allowance'] = $validated['transport_allowance'] ?? 0;
        $validated['deductions'] = $validated['deductions'] ?? 0;

        DB::beginTransaction();
        try {
            $payroll->update($validated);
            $payroll->session_bonus = $sessionBonus;
            $payroll->calculateTotals();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }

        return $this->redirectPayrollIndexWithFilters()->with('success', 'Data gaji berhasil diperbarui.');
    }

    public function markPaid(Request $request, SalaryRecord $payroll)
    {
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $payroll->update([
            'paid' => true,
            'paid_at' => now()->format('Y-m-d'),
            'net_salary' => $validated['paid_amount'],
        ]);

        SalaryPayment::updateOrCreate(
            ['salary_record_id' => $payroll->id],
            [
                'employee_name' => $payroll->employee_name,
                'position' => $payroll->position,
                'whatsapp' => $payroll->whatsapp,
                'month' => $payroll->month,
                'year' => $payroll->year,
                'base_salary' => $payroll->base_salary,
                'salary_extra' => $payroll->salary_extra,
                'total_sessions' => $payroll->total_sessions,
                'session_bonus' => $payroll->session_bonus,
                'transport_allowance' => $payroll->transport_allowance,
                'total_compensation' => $payroll->total_compensation,
                'deductions' => $payroll->deductions,
                'net_salary' => $validated['paid_amount'],
                'paid_at' => now()->format('Y-m-d'),
            ]
        );

        // Create expense record for payroll
        $this->createExpenseForPayroll($payroll, $validated['paid_amount']);

        return $this->redirectPayrollIndexWithFilters()->with('success', 'Pembayaran gaji ditandai lunas.');
    }

    public function markUnpaid(Request $request, SalaryRecord $payroll)
    {
        $payroll->update(['paid' => false, 'paid_at' => null]);
        SalaryPayment::where('salary_record_id', $payroll->id)->delete();

        // Delete expense record for this payroll
        \App\Models\Expense::where('title', 'like', '%Gaji ' . $payroll->employee_name . '%')
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->delete();

        return $this->redirectPayrollIndexWithFilters()->with('success', 'Status pembayaran dikembalikan ke belum dibayar.');
    }

    /**
     * Redirect back to payroll index with the same filters + pagination as
     * the user had when they left the index page (stored in session).
     */
    private function redirectPayrollIndexWithFilters()
    {
        $filters = session('payroll_last_index_query', []);

        // Only keep the filter fields that the index form actually uses
        $allowed = ['month', 'status', 'employee_name', 'per_page', 'page', 'year'];
        $filters = array_intersect_key($filters, array_flip($allowed));

        return redirect()->route('payroll.index', $filters);
    }

    private function createExpenseForPayroll(SalaryRecord $payroll, float $amount): void
    {
        $category = \App\Models\ExpenseCategory::where('name', 'Gaji Karyawan')->first();
        $wallet = \App\Models\Wallet::where('is_default', true)->first();

        if (!$category || !$wallet) {
            return;
        }

        // Delete existing expense for this payroll first
        \App\Models\Expense::where('title', 'like', '%Gaji ' . $payroll->employee_name . '%')
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->delete();

        \App\Models\Expense::create([
            'expense_category_id' => $category->id,
            'title' => 'Gaji ' . $payroll->employee_name . ' - ' . \Carbon\Carbon::create($payroll->year, $payroll->month, 1)->format('F Y'),
            'date' => now()->format('Y-m-d'),
            'amount' => $amount,
            'wallet_id' => $wallet->id,
            'notes' => 'Pembayaran gaji ' . $payroll->employee_name . ' bulan ' . $payroll->month . '/' . $payroll->year,
        ]);
    }

    public function destroy(Request $request, SalaryRecord $payroll)
    {
        $payroll->delete();
        return $this->redirectPayrollIndexWithFilters()->with('success', 'Data gaji berhasil dihapus.');
    }

    private function generateForCurrentMonth()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Check if current month already has records
        $currentExists = SalaryRecord::where('month', $currentMonth)->where('year', $currentYear)->exists();

        if ($currentExists) {
            return;
        }

        // Get previous month data
        $prevMonth = $currentMonth === 1 ? 12 : $currentMonth - 1;
        $prevYear = $currentMonth === 1 ? $currentYear - 1 : $currentYear;

        $prevRecords = SalaryRecord::where('month', $prevMonth)->where('year', $prevYear)->get();

        $created = 0;
        foreach ($prevRecords as $record) {
            $existing = SalaryRecord::where('employee_name', $record->employee_name)
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->first();

            if (!$existing) {
                SalaryRecord::create([
                    'employee_name' => $record->employee_name,
                    'position' => $record->position,
                    'phone' => $record->phone,
                    'whatsapp' => $record->whatsapp,
                    'salary_date' => now()->format('Y-m-d'),
                    'month' => $currentMonth,
                    'year' => $currentYear,
                    'base_salary' => $record->base_salary,
                    'salary_extra' => $record->salary_extra,
                    'total_sessions' => $record->total_sessions,
                    'session_bonus' => $record->session_bonus,
                    'transport_allowance' => $record->transport_allowance,
                    'total_compensation' => $record->total_compensation,
                    'deductions' => $record->deductions,
                    'net_salary' => $record->net_salary,
                    'paid' => false,
                    'paid_at' => null,
                ]);
                $created++;
            }
        }

        if ($created > 0) {
            session(['payroll_generated' => $created]);
        }
    }

    public function getEmployeeSalaries(Request $request)
    {
        $employeeName = $request->input('employee_name');
        $month = $request->input('month');
        $year = $request->input('year');

        $query = SalaryRecord::query();
        if ($employeeName) {
            $query->where('employee_name', $employeeName);
        }
        if ($month) {
            $query->where('month', $month);
        }
        if ($year) {
            $query->where('year', $year);
        }

        $salaries = $query->orderBy('employee_name')->orderBy('salary_date', 'desc')->get();

        return response()->json($salaries);
    }

    public function generateSlip(SalaryRecord $payroll)
    {
        $payment = $payroll->payrollPayments()->first();

        // Jika belum ada SalaryPayment, buat data dummy dari SalaryRecord
        if (!$payment) {
            $payment = (object) [
                'employee_name' => $payroll->employee_name,
                'position' => $payroll->position,
                'whatsapp' => $payroll->whatsapp,
                'month' => $payroll->month,
                'year' => $payroll->year,
                'base_salary' => $payroll->base_salary,
                'salary_extra' => $payroll->salary_extra,
                'total_sessions' => $payroll->total_sessions,
                'session_bonus' => $payroll->session_bonus,
                'transport_allowance' => $payroll->transport_allowance,
                'total_compensation' => $payroll->total_compensation,
                'deductions' => $payroll->deductions,
                'net_salary' => $payroll->net_salary,
                'paid_at' => $payroll->paid_at,
            ];
        }

        $companyName = 'KLINIK TERAPI & SEKOLAH KHUSUS ANAK MANDIRI';
        $kopSuratPath = public_path('images/kop_surat.png');
        $logoPath = public_path('images/logo_am.png');
        $adminName = auth()->user()->name ?? 'Administrator';

        $pdf = Pdf::loadView('payroll.slips.slip', compact('payment', 'companyName', 'kopSuratPath', 'logoPath', 'adminName'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('slip-gaji-' . $payment->employee_name . '-' . $payment->year . '-' . $payment->month . '.pdf');
    }
}
