<?php

namespace App\Exports;

use App\Models\FinancialPlan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialPlanExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private Collection $plans;
    private int $totalIncome;
    private int $totalExpense;

    public function __construct(Collection $plans, int $totalIncome, int $totalExpense)
    {
        $this->plans = $plans;
        $this->totalIncome = $totalIncome;
        $this->totalExpense = $totalExpense;
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['LAPORAN PERENCANAAN KEUANGAN'];
        $rows[] = ['Tanggal Export: ' . now()->format('d F Y H:i')];
        $rows[] = [];
        $rows[] = ['No', 'Tipe', 'Kategori', 'Target Amount', 'Catatan'];

        $no = 1;
        foreach ($this->plans as $plan) {
            $rows[] = [
                $no++,
                ucfirst($plan->type),
                $plan->category ?? '-',
                $plan->target_amount,
                $plan->notes ?? '-',
            ];
        }

        $rows[] = [];
        $rows[] = ['TOTAL PEMASUKAN', '', '', $this->totalIncome];
        $rows[] = ['TOTAL PENGELUARAN', '', '', $this->totalExpense];
        $rows[] = ['SURPLUS / DEFISIT', '', '', $this->totalIncome - $this->totalExpense];

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Perencanaan Keuangan';
    }

    public function columnWidths(): array
    {
        return ['A' => 5, 'B' => 10, 'C' => 20, 'D' => 18, 'E' => 40];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
