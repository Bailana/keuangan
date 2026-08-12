<?php

namespace App\Exports;

use App\Models\Child;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ChildExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    private Collection $children;

    public function __construct(Collection $children)
    {
        $this->children = $children;
    }

    public function array(): array
    {
        return $this->children->map(function ($child) {
            $therapyNames = [];
            $therapySessions = [];
            foreach ($child->therapyTypes as $t) {
                $therapyNames[] = $t->name;
                $therapySessions[] = $t->pivot->monthly_sessions ?? 0;
            }
            $vokasiNames = [];
            $vokasiSessions = [];
            foreach ($child->vocationalTypes as $v) {
                $vokasiNames[] = $v->name;
                $vokasiSessions[] = $v->pivot->monthly_sessions ?? 0;
            }

            return [
                $child->name,
                $child->is_active ? 'Aktif' : 'Nonaktif',
                $child->parent_name ?? '-',
                $child->parent_whatsapp ?? '-',
                $child->class_name ?? '-',
                $child->spp_fee ? number_format($child->spp_fee, 0, ',', '.') : '-',
                $child->has_subsidi ? number_format($child->subsidi_amount ?? 0, 0, ',', '.') : '-',
                implode(', ', $therapyNames) ?: '-',
                implode(', ', $therapySessions) ?: '-',
                implode(', ', $vokasiNames) ?: '-',
                implode(', ', $vokasiSessions) ?: '-',
                number_format($child->calculateGrossAmount(), 0, ',', '.'),
                number_format($child->getSubsidiAmount(), 0, ',', '.'),
                number_format($child->getCurrentInvoiceAmount(), 0, ',', '.'),
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'Nama Anak',
            'Status',
            'Nama Orang Tua',
            'No. HP Orang Tua',
            'Kelas',
            'SPP Bulanan',
            'Subsidi',
            'Jenis Terapi',
            'Sesi Terapi',
            'Jenis Vokasi',
            'Sesi Vokasi',
            'Total Layanan',
            'Total Subsidi',
            'Tagihan Bersih',
        ];
    }

    public function title(): string
    {
        return 'Data Anak';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 10,
            'C' => 20,
            'D' => 18,
            'E' => 16,
            'F' => 14,
            'G' => 14,
            'H' => 24,
            'I' => 12,
            'J' => 20,
            'K' => 12,
            'L' => 14,
            'M' => 14,
            'N' => 14,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '6366f1']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
