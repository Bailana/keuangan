<?php

namespace App\Exports;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ProfitLossExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private Carbon $startDate;
    private Carbon $endDate;
    private ?int $walletId;

    public function __construct(Carbon $startDate, Carbon $endDate, ?int $walletId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->walletId = $walletId;
    }

    public function array(): array
    {
        $incomeQuery = Income::selectRaw('income_category_id, SUM(amount) as total')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->groupBy('income_category_id');
        if ($this->walletId) {
            $incomeQuery->where('wallet_id', $this->walletId);
        }
        $incomeData = $incomeQuery->with('category')->get();

        $expenseQuery = Expense::selectRaw('expense_category_id, SUM(amount) as total')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->groupBy('expense_category_id');
        if ($this->walletId) {
            $expenseQuery->where('wallet_id', $this->walletId);
        }
        $expenseData = $expenseQuery->with('category')->get();

        $rows = [];
        $rows[] = ['LAPORAN LABA / RUGI'];
        $rows[] = ['Periode: ' . $this->startDate->format('d F Y') . ' - ' . $this->endDate->format('d F Y')];
        $rows[] = [];
        $rows[] = ['PENDAPATAN'];
        $rows[] = ['Kategori', 'Jumlah'];
        $totalIncome = 0;
        foreach ($incomeData as $item) {
            $rows[] = [$item->category?->name ?? 'Umum', $item->total];
            $totalIncome += (float) $item->total;
        }
        $rows[] = ['TOTAL PENDAPATAN', $totalIncome];
        $rows[] = [];
        $rows[] = ['BEBAN'];
        $rows[] = ['Kategori', 'Jumlah'];
        $totalExpense = 0;
        foreach ($expenseData as $item) {
            $rows[] = [$item->category?->name ?? 'Umum', $item->total];
            $totalExpense += (float) $item->total;
        }
        $rows[] = ['TOTAL BEBAN', $totalExpense];
        $rows[] = [];
        $rows[] = ['LABA / RUGI BERSIH', $totalIncome - $totalExpense];

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Laporan Laba/Rugi';
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 20];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
