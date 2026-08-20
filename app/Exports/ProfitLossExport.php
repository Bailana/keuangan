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

    /**
     * Parse income notes to extract per-category amounts.
     * Note format uses Indonesian number format (dot as thousand separator): Rp 500.000
     *
     * Income is stored as NET amount (gross - subsidi), so any subsidy must be
     * allocated back to the appropriate category:
     *   - If the child attends school: deduct from SPP
     *   - Otherwise: deduct from Terapi
     */
    private function parseIncomeBreakdown(string $notes, ?Income $record = null): array
    {
        $patterns = [
            'Terapi' => '/Terapi\s+[^:]+?:\s*[\d]+x\s*Rp\s*([\d,.]+)/i',
            'Vokasi' => '/Vokasi\s+[^:]+?:\s*[\d]+x\s*Rp\s*([\d,.]+)/i',
            'SPP' => '/SPP:\s*Rp\s*([\d,.]+)/i',
            'Parent Support' => '/Parent Support:\s*Rp\s*([\d,.]+)/i',
            'Subsidi' => '/Subsidi:\s*[-]?\s*Rp\s*([\d,.]+)/i',
        ];

        $result = [
            'Terapi' => 0,
            'Vokasi' => 0,
            'SPP' => 0,
            'Parent Support' => 0,
        ];

        $subsidiAmt = 0;
        foreach ($patterns as $cat => $pattern) {
            if (preg_match($pattern, $notes, $matches)) {
                $amount = (float) str_replace(['.', ','], '', $matches[count($matches) - 1]);
                if ($cat === 'Subsidi') {
                    $subsidiAmt += $amount;
                } else {
                    $result[$cat] += $amount;
                }
            }
        }

        // Allocate subsidy to the correct category based on child's services
        if ($subsidiAmt > 0 && $record) {
            if ($record->child && $record->child->isTakingSekolah()) {
                $result['SPP'] = max(0, $result['SPP'] - $subsidiAmt);
            } else {
                $result['Terapi'] = max(0, $result['Terapi'] - $subsidiAmt);
            }
        }

        return $result;
    }

    public function array(): array
    {
        $incomeQuery = Income::whereBetween('date', [$this->startDate, $this->endDate]);
        if ($this->walletId) {
            $incomeQuery->where('wallet_id', $this->walletId);
        }
        $incomeData = $incomeQuery->get();

        $expenseQuery = Expense::selectRaw('expense_category_id, SUM(amount) as total')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->groupBy('expense_category_id');
        if ($this->walletId) {
            $expenseQuery->where('wallet_id', $this->walletId);
        }
        $expenseData = $expenseQuery->with('category')->get();

        // Aggregate income breakdown from notes
        $incomeBreakdown = [
            'Terapi' => 0,
            'Vokasi' => 0,
            'SPP' => 0,
            'Parent Support' => 0,
        ];
        foreach ($incomeData as $record) {
            $breakdown = $this->parseIncomeBreakdown($record->notes ?? '', $record);
            foreach ($breakdown as $cat => $amount) {
                $incomeBreakdown[$cat] += $amount;
            }
        }

        $rows = [];
        $rows[] = ['LAPORAN LABA / RUGI'];
        $rows[] = ['Periode: ' . $this->startDate->format('d F Y') . ' - ' . $this->endDate->format('d F Y')];
        $rows[] = [];
        $rows[] = ['PENDAPATAN'];
        $rows[] = ['Kategori', 'Jumlah'];
        $totalIncome = 0;
        foreach ($incomeBreakdown as $cat => $amount) {
            if ($amount > 0) {
                $rows[] = [$cat, $amount];
                $totalIncome += $amount;
            }
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
