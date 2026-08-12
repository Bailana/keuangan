<?php

namespace App\Exports;

use App\Models\Income;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncomeExport implements FromArray, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
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
            $record->child?->name ?? 'Umum',
            $record->category?->name ?? '-',
            $record->sender_name ?? '-',
            (float) $record->amount,
            $record->notes ?? '-',
        ])->toArray();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Anak',
            'Kategori',
            'Nama Pengirim',
            'Jumlah',
            'Catatan',
        ];
    }

    public function title(): string
    {
        return 'Laporan Pemasukan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 24,
            'C' => 20,
            'D' => 24,
            'E' => 16,
            'F' => 40,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '10B981'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
            // Total row
            $this->records->count() + 2 => [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'D1FAE5'],
                ],
            ],
        ];
    }
}
