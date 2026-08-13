<?php

namespace App\Http\Controllers;

use App\Models\SalaryRecord;
use App\Models\SalaryPayment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = SalaryRecord::query();

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
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

        $records = $query->orderBy('salary_date', 'desc')->paginate(20);

        $stats = [
            'total' => SalaryRecord::count(),
            'paid' => SalaryRecord::where('paid', true)->count(),
            'unpaid' => SalaryRecord::where('paid', false)->count(),
            'total_paid' => SalaryRecord::where('paid', true)->sum('net_salary'),
            'total_unpaid' => SalaryRecord::where('paid', false)->sum('net_salary'),
        ];

        $months = [];
        for ($i = 0; $i < 24; $i++) {
            $date = now()->subMonths($i);
            $months[] = ['month' => $date->month, 'year' => $date->year];
        }

        return view('payroll.index', compact('records', 'stats', 'months'));
    }

    public function create()
    {
        $salaryRecords = SalaryRecord::where('paid', false)->orderBy('salary_date', 'desc')->limit(12)->get();
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

        return redirect()->route('payroll.index')->with('success', 'Data gaji berhasil disimpan.');
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

        return redirect()->route('payroll.index')->with('success', 'Data gaji berhasil diperbarui.');
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

        return redirect()->route('payroll.index')->with('success', 'Pembayaran gaji ditandai lunas.');
    }

    public function markUnpaid(SalaryRecord $payroll)
    {
        $payroll->update(['paid' => false, 'paid_at' => null]);
        SalaryPayment::where('salary_record_id', $payroll->id)->delete();

        // Delete expense record for this payroll
        \App\Models\Expense::where('title', 'like', '%Gaji ' . $payroll->employee_name . '%')
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->delete();

        return redirect()->route('payroll.index')->with('success', 'Status pembayaran dikembalikan ke belum dibayar.');
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

    public function destroy(SalaryRecord $payroll)
    {
        $payroll->delete();
        return redirect()->route('payroll.index')->with('success', 'Data gaji berhasil dihapus.');
    }

    public function paymentHistory()
    {
        $payments = SalaryPayment::orderBy('paid_at', 'desc')->paginate(20);
        return view('payroll.history', compact('payments'));
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

        $salaries = $query->orderBy('salary_date', 'desc')->get();

        return response()->json($salaries);
    }
}
