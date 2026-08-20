<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Anak</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
            padding: 24px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #6366f1;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header h1 {
            margin: 0;
            color: #6366f1;
            font-size: 16px;
            font-weight: 700;
        }
        .header p {
            margin: 3px 0 0;
            color: #6b7280;
            font-size: 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .info-table td {
            padding: 4px 0;
            font-size: 10px;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 140px;
            color: #6b7280;
            font-weight: 500;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }
        thead th {
            background: #6366f1;
            color: #fff;
            padding: 6px 4px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #4f46e5;
            white-space: nowrap;
        }
        tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }
        tbody td {
            padding: 5px 4px;
            vertical-align: middle;
        }
        .badge-active {
            background: #d1fae5;
            color: #065f46;
            padding: 1px 6px;
            border-radius: 9999px;
            font-size: 8px;
            font-weight: 600;
        }
        .badge-inactive {
            background: #f3f4f6;
            color: #6b7280;
            padding: 1px 6px;
            border-radius: 9999px;
            font-size: 8px;
            font-weight: 600;
        }
        .summary-cards {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }
        .summary-card {
            flex: 1;
            padding: 8px 10px;
            border-radius: 6px;
            text-align: center;
        }
        .summary-card .label {
            font-size: 8px;
            color: #6b7280;
            font-weight: 500;
        }
        .summary-card .value {
            font-size: 12px;
            font-weight: 700;
            margin-top: 2px;
        }
        .sc-terapi { background: #f3e8ff; color: #7c3aed; border: 1px solid #e9d5ff; }
        .sc-spp { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .sc-vokasi { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .sc-subsidi { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .sc-total { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
        .signature-block {
            width: 100%;
            margin-top: 40px;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 20px;
        }
        .signature-label {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 35px;
        }
        .signature-date {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 3px;
        }
        .signature-name {
            font-size: 10px;
            font-weight: 600;
            color: #1f2937;
            border-top: 1px solid #1f2937;
            padding-top: 4px;
            display: inline-block;
            min-width: 150px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Klinik Terapi & Sekolah Anak Mandiri</h1>
        <p>Laporan Data Anak</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card sc-terapi">
            <div class="label">Total Terapi</div>
            <div class="value">Rp {{ number_format($totalTerapi, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card sc-spp">
            <div class="label">Total SPP</div>
            <div class="value">Rp {{ number_format($totalSekolah, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card sc-vokasi">
            <div class="label">Total Vokasi</div>
            <div class="value">Rp {{ number_format($totalVokasi, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card sc-subsidi">
            <div class="label">Total Subsidi</div>
            <div class="value">Rp {{ number_format($totalSubsidi, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card sc-total">
            <div class="label">Jumlah Anak</div>
            <div class="value">{{ $children->count() }} anak</div>
        </div>
    </div>

    <!-- Meta Info -->
    <table class="info-table">
        <tr>
            <td>No. Laporan</td>
            <td>:</td>
            <td>LP-{{ now()->format('Y') }}{{ now()->format('m') }}{{ now()->format('d') }}-CHD-{{ str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td>Tanggal Cetak</td>
            <td>:</td>
            <td>{{ $generatedDate }}</td>
        </tr>
        <tr>
            <td>Total Data</td>
            <td>:</td>
            <td>{{ $children->count() }} anak</td>
        </tr>
    </table>

    @if(count($filters) > 0)
    <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 6px; padding: 8px 12px; margin-bottom: 14px; font-size: 9px; color: #4338ca;">
        <strong>Filter Aktif:</strong>
        @foreach($filters as $key => $value)
            {{ $key }}: {{ $value }} &nbsp;|&nbsp;
        @endforeach
    </div>
    @endif

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Status</th>
                <th>Orang Tua</th>
                <th>HP</th>
                <th>Kelas</th>
                <th>SPP</th>
                <th>Subsidi</th>
                <th>Terapi</th>
                <th>Vokasi</th>
                <th>Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($children as $index => $child)
            <tr>
                <td style="text-align:center">{{ $index + 1 }}</td>
                <td>{{ $child->name }}</td>
                <td style="text-align:center">
                    <span class="{{ $child->is_active ? 'badge-active' : 'badge-inactive' }}">
                        {{ $child->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td>{{ $child->parent_name ?? '-' }}</td>
                <td>{{ $child->parent_whatsapp ?? '-' }}</td>
                <td>{{ $child->class_name ?? '-' }}</td>
                <td style="text-align:right">{{ $child->spp_fee ? number_format($child->spp_fee, 0, ',', '.') : '-' }}</td>
                <td style="text-align:right">{{ $child->has_subsidi && $child->subsidi_amount ? number_format($child->subsidi_amount, 0, ',', '.') : '-' }}</td>
                <td style="text-align:right">Rp {{ number_format($child->getTherapyTotal(), 0, ',', '.') }}</td>
                <td>{{ $child->getVokasiDetails() }}</td>
                <td style="text-align:right;font-weight:600;color:#6366f1">Rp {{ number_format($child->calculateInvoiceAmount(now()->month, now()->year), 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr class="empty-row">
                <td colspan="11">Belum ada data anak.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Block -->
    <div class="signature-block">
        <table class="signature-table">
            <tr>
                <div class="signature-date">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                <td>
                    <div style="margin-bottom: 35px;">
                        <div class="signature-label">Disusun oleh,</div>
                        
                    </div>
                    <div class="signature-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                </td>
                <td>
                    <div style="margin-bottom: 35px;">
                        <div class="signature-label">Mengetahui / Disetujui,</div>
                    </div>
                    <div class="signature-name">(Penanggung Jawab)</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        Dokumen ini dibuat otomatis oleh sistem &mdash; {{ config('app.name') }} &copy; {{ now()->year }}
    </div>
</body>
</html>
