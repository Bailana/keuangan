<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpenseExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private Collection $records;

    public function __construct(Collection $records)
    {
        $this->records = $records;
    }

    public function array(): array
    {
        return $this->records->map(fn($record) => [
            $record->date->format('Y-m-d'),
            $record->category?->name ?? '-',
            (float) $record->amount,
            $record->bank_or_wallet ?? '-',
            $record->recipient ?? '-',
            $record->notes ?? '-',
        ])->toArray();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kategori',
            'Jumlah',
            'Bank/Dompet',
            'Penerima',
            'Catatan',
        ];
    }

    public function title(): string
    {
        return 'Laporan Pengeluaran';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 20,
            'C' => 16,
            'D' => 18,
            'E' => 30,
            'F' => 40,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'EF4444'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
            $this->records->count() + 2 => [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'FEE2E2'],
                ],
            ],
        ];
    }
}
