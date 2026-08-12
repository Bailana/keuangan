<?php

namespace App\Exports;

use App\Models\Income;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class RevenueExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
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
        $query = Income::selectRaw('income_category_id, child_id, SUM(amount) as total, date')
            ->whereBetween('date', [$this->startDate, $this->endDate]);
        if ($this->walletId) {
            $query->where('wallet_id', $this->walletId);
        }
        $data = $query->with(['category', 'child'])->get();

        $rows = [];
        $rows[] = ['LAPORAN PENDAPATAN'];
        $rows[] = ['Periode: ' . $this->startDate->format('d F Y') . ' - ' . $this->endDate->format('d F Y')];
        $rows[] = [];
        $rows[] = ['Tanggal', 'Anak', 'Kategori', 'Jumlah', 'Catatan'];
        $total = 0;
        foreach ($data as $item) {
            $rows[] = [
                $item->date->format('Y-m-d'),
                $item->child?->name ?? 'Umum',
                $item->category?->name ?? '-',
                $item->total,
                $item->notes ?? '-',
            ];
            $total += (float) $item->total;
        }
        $rows[] = [];
        $rows[] = ['TOTAL PENDAPATAN', '', '', $total];

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Laporan Pendapatan';
    }

    public function columnWidths(): array
    {
        return ['A' => 14, 'B' => 24, 'C' => 20, 'D' => 16, 'E' => 40];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
