<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ArrearsExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private Collection $children;
    private string $period;

    public function __construct(Collection $children, string $period)
    {
        $this->children = $children;
        $this->period = $period;
    }

    public function array(): array
    {
        return $this->children->map(function ($child) {
            return [
                $child->name,
                trim(implode(', ', $child->getServiceLabels())),
                $child->class_name ?? '-',
                $child->invoiceAmount,
                $child->totalPaid,
                $child->outstanding,
                $this->formatStatus($child->paymentStatus),
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Nama Anak',
            'Layanan',
            'Kelas',
            'Tagihan',
            'Sudah Dibayar',
            'Tunggakan',
            'Status',
        ];
    }

    public function title(): string
    {
        return 'Laporan Tunggakan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 24,
            'B' => 20,
            'C' => 12,
            'D' => 16,
            'E' => 16,
            'F' => 16,
            'G' => 14,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D97706']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }

    private function formatStatus(string $status): string
    {
        return match($status) {
            'paid' => 'Lunas',
            'partial' => 'Sebagian',
            'unpaid' => 'Belum Bayar',
            'overdue' => 'Jatuh Tempo',
            default => $status,
        };
    }
}
