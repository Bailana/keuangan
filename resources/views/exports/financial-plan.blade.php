<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perencanaan Keuangan</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 9pt;
            color: #1f2937;
            background: #fff;
            line-height: 1.4;
        }
        /* === HEADER SECTION === */
        .page-header {
            width: 100%;
            background: #fff;
        }
        .kop {
            width: 100%;
            margin-bottom: 0;
        }
        .kop img {
            width: 100%;
            height: auto;
            display: block;
        }
        /* === BODY CONTENT === */
        .body-content {
            padding: 14px 20px 20px 20px;
        }
        /* Title Block */
        .title-block {
            text-align: center;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1e40af;
        }
        .title-block h1 {
            font-size: 14pt;
            font-weight: 700;
            color: #1e40af;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .title-block .subtitle {
            font-size: 9pt;
            color: #6b7280;
        }
        /* Meta Info */
        .meta-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        .meta-info td {
            padding: 2px 0;
            vertical-align: top;
        }
        .meta-info td:first-child {
            width: 120px;
            color: #6b7280;
            font-weight: 500;
        }
        .meta-info td:last-child {
            color: #1f2937;
        }
        /* Summary Cards - Menonjol */
        .summary-block {
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-row {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }
        .summary-row td {
            padding: 14px 12px;
            color: #fff;
            text-align: center;
            vertical-align: middle;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .summary-income {
            background: #059669;
            border: 2px solid #047857;
        }
        .summary-expense {
            background: #dc2626;
            border: 2px solid #b91c1c;
        }
        .summary-balance {
            background: #4f46e5;
            border: 2px solid #3730a3;
        }
        .card-label {
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
            opacity: 0.95;
        }
        .card-value {
            font-size: 14pt;
            font-weight: 800;
            font-family: 'Courier New', monospace;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 16px;
            font-size: 8.5pt;
        }
        .data-table thead th {
            background: #1e40af;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #1e3a8a;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .data-table thead th.text-center {
            text-align: center;
        }
        .data-table thead th.text-right {
            text-align: right;
        }
        .data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .data-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }
        .data-table tbody td {
            padding: 5px 8px;
            vertical-align: middle;
        }
        .data-table .text-right {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        .data-table .text-center {
            text-align: center;
        }
        .data-table .text-num {
            font-family: 'Courier New', monospace;
            text-align: right;
        }
        /* Badges */
        .badge-income {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: 600;
            border: 1px solid #a7f3d0;
        }
        .badge-expense {
            display: inline-block;
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: 600;
            border: 1px solid #fecaca;
        }
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 24px;
            color: #9ca3af;
            font-style: italic;
        }
        /* Footer */
        .report-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer-left {
            font-size: 7pt;
            color: #9ca3af;
        }
        .footer-right {
            text-align: right;
        }
        .footer-right .signature-label {
            font-size: 8pt;
            color: #6b7280;
            margin-bottom: 3px;
        }
        .footer-right .signature-name {
            font-size: 9pt;
            font-weight: 600;
            color: #1f2937;
            border-top: 1px solid #374151;
            padding-top: 2px;
            min-width: 140px;
            display: inline-block;
        }
        .footer-right .signature-role {
            font-size: 7pt;
            color: #6b7280;
        }
        /* Page numbers */
        .page-number {
            text-align: center;
            font-size: 7pt;
            color: #9ca3af;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <img src="{{ $logoPath }}" alt="Watermark" style="
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        width: 300px;
        height: 300px;
        opacity: 0.06;
        pointer-events: none;
        z-index: 0;
    ">

    <!-- Header with Kop Surat -->
    <div class="page-header">
        <div class="kop">
            <img src="{{ $kopSuratPath }}" alt="Kop Surat">
        </div>
    </div>

    <!-- Body Content -->
    <div class="body-content">
        <!-- Title Block -->
        <div class="title-block">
            <h1>Perencanaan Keuangan</h1>
            <div class="subtitle">Laporan Rencana Pemasukan & Pengeluaran</div>
        </div>

        <!-- Meta Info -->
        <table class="meta-info">
            <tr>
                <td>Tanggal Export</td>
                <td>: {{ $generatedDate }}</td>
            </tr>
            @if($filters['type'] ?? null)
            <tr>
                <td>Tipe</td>
                <td>: {{ $filters['type'] == 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
            </tr>
            @endif
            @if($filters['category'] ?? null)
            <tr>
                <td>Kategori</td>
                <td>: {{ $filters['category'] }}</td>
            </tr>
            @endif
            @if($filters['year'] ?? null)
            <tr>
                <td>Tahun</td>
                <td>: {{ $filters['year'] }}</td>
            </tr>
            @endif
            @if($filters['month'] ?? null)
            <tr>
                <td>Bulan</td>
                <td>: {{ $filters['month'] }}</td>
            </tr>
            @endif
        </table>

        <!-- Summary Cards -->
        <table class="summary-row">
            <tr>
                <td class="summary-income">
                    <div class="card-label">Total Pemasukan</div>
                    <div class="card-value">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                </td>
                <td class="summary-expense">
                    <div class="card-label">Total Pengeluaran</div>
                    <div class="card-value">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                </td>
                <td class="summary-balance">
                    <div class="card-label">Surplus / Defisit</div>
                    <div class="card-value">Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>

        <!-- Data Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>Tanggal</th>
                    <th class="text-center" style="width: 80px;">Tipe</th>
                    <th>Kategori</th>
                    <th class="text-right">Target Amount</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plans as $index => $plan)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ sprintf('%04d-%02d', $plan->year, $plan->month) }}</td>
                    <td class="text-center">
                        <span class="badge-{{ $plan->type }}">{{ ucfirst($plan->type) }}</span>
                    </td>
                    <td>{{ $plan->category ?? '-' }}</td>
                    <td class="text-num">Rp {{ number_format($plan->target_amount, 0, ',', '.') }}</td>
                    <td>{{ $plan->notes ?? '-' }}</td>
                </tr>
                @endforeach
                @if($plans->isEmpty())
                <tr>
                    <td colspan="6" class="empty-state">
                        Tidak ada data perencanaan keuangan
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Footer -->
        <div class="report-footer">
            <div class="footer-left">
                <div>Dicetak pada: {{ $generatedDate }}</div>
                <div style="margin-top: 2px;">Halaman 1 dari 1</div>
            </div>
            <div class="footer-right">
                <div class="signature-label">Administrator</div>
                <div class="signature-name">{{ auth()->user()->name ?? 'Administrator' }}</div>
                <div class="signature-role">Klinic Terapi & Sekolah Khusus Anak Mandiri</div>
            </div>
        </div>
    </div>
</body>
</html>
