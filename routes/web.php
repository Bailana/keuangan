<?php

use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\FinancialPlanController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TherapyTypeController;
use App\Http\Controllers\VocationalTypeController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SlipPDFController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Wallet routes
    Route::get('/wallets', [WalletController::class, 'index'])->name('wallets.index');
    Route::post('/wallets/{wallet}/balance', [WalletController::class, 'setInitialBalance'])->name('wallets.setBalance');
    Route::get('/wallets/statement', [WalletController::class, 'downloadStatement'])->name('wallets.statement');
    Route::get('/wallets/export/pdf', [WalletController::class, 'exportPdf'])->name('wallets.export.pdf');
    Route::get('/wallets/create', [WalletController::class, 'create'])->name('wallets.create');
    Route::post('/wallets', [WalletController::class, 'store'])->name('wallets.store');
    Route::get('/wallets/{wallet}/edit', [WalletController::class, 'edit'])->name('wallets.edit');
    Route::put('/wallets/{wallet}', [WalletController::class, 'update'])->name('wallets.update');
    Route::delete('/wallets/{wallet}', [WalletController::class, 'destroy'])->name('wallets.destroy');
    Route::post('/wallets/{wallet}/set-default', [WalletController::class, 'setDefault'])->name('wallets.setDefault');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/settings/toggle-sidebar', [SettingsController::class, 'toggleSidebar'])->name('settings.toggleSidebar');

    // Activity Logs (admin only) - includes login, logout, create, update, delete, export
    Route::middleware('role:admin')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::delete('/activity-logs/{activityLog}', [ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
        Route::post('/activity-logs/bulk-delete', [ActivityLogController::class, 'bulkDelete'])->name('activity-logs.bulk-delete');
        Route::delete('/activity-logs/clear-all', [ActivityLogController::class, 'clearAll'])->name('activity-logs.clear-all');
    });

    // Arus Kas (unified income + expense)
    Route::get('/cash-flows', [CashFlowController::class, 'index'])->name('cash-flows');

    // Master Data Karyawan
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::put('/employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');

    // Penggajian
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
    Route::post('/payroll', [PayrollController::class, 'store'])->name('payroll.store');
    Route::get('/payroll/{payroll}/edit', [PayrollController::class, 'edit'])->name('payroll.edit');
    Route::put('/payroll/{payroll}', [PayrollController::class, 'update'])->name('payroll.update');
    Route::post('/payroll/{payroll}/mark-paid', [PayrollController::class, 'markPaid'])->name('payroll.markPaid');
    Route::post('/payroll/{payroll}/mark-unpaid', [PayrollController::class, 'markUnpaid'])->name('payroll.markUnpaid');
    Route::delete('/payroll/{payroll}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
    Route::get('/payroll/history', [PayrollController::class, 'paymentHistory'])->name('payroll.history');
    Route::get('/payroll/employee-salaries', [PayrollController::class, 'getEmployeeSalaries'])->name('payroll.employee-salaries');
    Route::get('/payroll/history/{payment}/pdf', [SlipPDFController::class, 'generate'])->name('payroll.slip.pdf');
    Route::get('/payroll/history/{payment}/whatsapp', [SlipPDFController::class, 'sendWhatsApp'])->name('payroll.slip.whatsapp');


    Route::get('/children', [ChildController::class, 'index'])->name('children');
    Route::get('/children/index', [ChildController::class, 'index'])->name('children.index');
    Route::get('/children/create', [ChildController::class, 'create'])->name('children.create');
    Route::get('/children/{child}/edit', [ChildController::class, 'edit'])->name('children.edit');

    // Therapy Types
    Route::get('/therapy-types', [TherapyTypeController::class, 'index'])->name('therapy-types.index');
    Route::get('/therapy-types/create', [TherapyTypeController::class, 'create'])->name('therapy-types.create');
    Route::post('/therapy-types', [TherapyTypeController::class, 'store'])->name('therapy-types.store');
    Route::get('/therapy-types/{therapyType}/edit', [TherapyTypeController::class, 'edit'])->name('therapy-types.edit');
    Route::put('/therapy-types/{therapyType}', [TherapyTypeController::class, 'update'])->name('therapy-types.update');
    Route::delete('/therapy-types/{therapyType}', [TherapyTypeController::class, 'destroy'])->name('therapy-types.destroy');

    // Vocational Types
    Route::get('/vocational-types', [VocationalTypeController::class, 'index'])->name('vocational-types.index');
    Route::get('/vocational-types/create', [VocationalTypeController::class, 'create'])->name('vocational-types.create');
    Route::post('/vocational-types', [VocationalTypeController::class, 'store'])->name('vocational-types.store');
    Route::get('/vocational-types/{vocationalType}/edit', [VocationalTypeController::class, 'edit'])->name('vocational-types.edit');
    Route::put('/vocational-types/{vocationalType}', [VocationalTypeController::class, 'update'])->name('vocational-types.update');
    Route::delete('/vocational-types/{vocationalType}', [VocationalTypeController::class, 'destroy'])->name('vocational-types.destroy');

    Route::get('/plans', [FinancialPlanController::class, 'index'])->name('plans');
    Route::get('/plans/index', [FinancialPlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/create', [FinancialPlanController::class, 'create'])->name('plans.create');
    Route::get('/plans/{plan}/edit', [FinancialPlanController::class, 'edit'])->name('plans.edit');
    Route::get('/plans/export/pdf', [FinancialPlanController::class, 'exportPdf'])->name('plans.export.pdf');
    Route::get('/plans/export/excel', [FinancialPlanController::class, 'exportExcel'])->name('plans.export.excel');

    // Laporan Keuangan
    Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    Route::get('/reports/profit-loss/export/pdf', [ReportController::class, 'exportProfitLossPdf'])->name('reports.profit-loss.export.pdf');
    Route::get('/reports/profit-loss/export/excel', [ReportController::class, 'exportProfitLossExcel'])->name('reports.profit-loss.export.excel');
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/revenue/export/pdf', [ReportController::class, 'exportRevenuePdf'])->name('reports.revenue.export.pdf');
    Route::get('/reports/revenue/export/excel', [ReportController::class, 'exportRevenueExcel'])->name('reports.revenue.export.excel');
    Route::get('/reports/aging', [ReportController::class, 'agingSchedule'])->name('reports.aging');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
    Route::get('/invoices/index', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{child}/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::get('/invoices/{child}/generate-paid', [InvoiceController::class, 'generatePaid'])->name('invoices.generatePaid');
    Route::get('/invoices/{child}/whatsapp', [InvoiceController::class, 'whatsapp'])->name('invoices.whatsapp');

    // Admin-only write actions
    Route::middleware('role:admin')->group(function () {
        Route::post('/incomes', [IncomeController::class, 'store'])->name('incomes.store');
        Route::put('/incomes/{income}', [IncomeController::class, 'update'])->name('incomes.update');
        Route::delete('/incomes/{income}', [IncomeController::class, 'destroy'])->name('incomes.destroy');

        Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
        Route::get('/expenses/{expense}/edit-modal', [ExpenseController::class, 'editModal'])->name('expenses.edit-modal');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

        Route::post('/children', [ChildController::class, 'store'])->name('children.store');
        Route::put('/children/{child}', [ChildController::class, 'update'])->name('children.update');
        Route::delete('/children/{child}', [ChildController::class, 'destroy'])->name('children.destroy');
        Route::post('/children/{child}/toggle-active', [ChildController::class, 'toggleActive'])->name('children.toggle-active');
        Route::get('/children/export/pdf', [ChildController::class, 'exportPdf'])->name('children.export.pdf');
        Route::get('/children/export/excel', [ChildController::class, 'exportExcel'])->name('children.export.excel');

        Route::post('/plans', [FinancialPlanController::class, 'store'])->name('plans.store');
        Route::put('/plans/{plan}', [FinancialPlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/{plan}', [FinancialPlanController::class, 'destroy'])->name('plans.destroy');

        // Invoice payment management (admin only)
        Route::post('/invoices/{child}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.markPaid');
        Route::post('/invoices/{child}/mark-unpaid', [InvoiceController::class, 'markUnpaid'])->name('invoices.markUnpaid');
    });

    // Edit modal routes (accessible by admin and viewer)
    Route::get('/incomes/{income}/edit-modal', [IncomeController::class, 'editModal'])->name('incomes.edit-modal');

    // User Management (superadmin only)
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});


Route::middleware(['web'])->group(function () {
    require __DIR__.'/auth.php';
});
