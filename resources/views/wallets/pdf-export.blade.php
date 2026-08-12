<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $wallet->name }} - {{ $month->locale('id')->isoFormat('MMMM YYYY') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            font-size: 9px;
            color: #1a1a2e;
            background: #fff;
            padding: 0;
        }

        /* === PAGE HEADER === */
        .page-header {
            background: linear-gradient(135deg, #1a3a5c 0%, #2563eb 100%);
            color: #fff;
            padding: 18px 24px 14px;
        }
        .page-header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .org-name {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #bfdbfe;
            margin-bottom: 2px;
        }
        .doc-title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .doc-subtitle {
            font-size: 8px;
            color: #93c5fd;
            margin-top: 2px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .statement-period {
            text-align: right;
        }
        .statement-period .period-label {
            font-size: 8px;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .statement-period .period-value {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        /* === DIVIDER === */
        .accent-line {
            height: 3px;
            background: linear-gradient(90deg, #2563eb, #38bdf8, #2563eb);
        }

        /* === ACCOUNT INFO === */
        .account-info {
            display: flex;
            justify-content: space-between;
            padding: 14px 24px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .account-left, .account-right {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .info-row {
            display: flex;
            gap: 8px;
            font-size: 9px;
        }
        .info-label {
            color: #64748b;
            font-weight: 500;
            min-width: 90px;
        }
        .info-value {
            color: #1e293b;
            font-weight: 600;
        }
        .info-value.bank-name {
            color: #1d4ed8;
            font-size: 10px;
        }

        /* === BALANCE SUMMARY === */
        .balance-summary {
            display: flex;
            margin: 0 24px;
            border: 1.5px solid #bfdbfe;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 14px;
        }
        .balance-section {
            flex: 1;
            padding: 10px 12px;
            text-align: center;
            background: #fff;
        }
        .balance-section:not(:last-child) {
            border-right: 1px solid #bfdbfe;
        }
        .balance-label {
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .balance-value {
            font-size: 13px;
            font-weight: 700;
            color: #1e40af;
        }
        .balance-value.income { color: #047857; }
        .balance-value.expense { color: #b91c1c; }

        /* === TRANSACTION TABLE === */
        .table-container {
            margin: 14px 24px;
        }
        .table-title {
            font-size: 9px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 0 6px;
            border-bottom: 2px solid #1e40af;
            margin-bottom: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }
        thead th {
            background: #1e3a5f;
            color: #fff;
            padding: 7px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #1e3a5f;
        }
        thead th:first-child { width: 32px; text-align: center; }
        thead th:nth-child(4) { text-align: right; }
        thead th:nth-child(5) { text-align: right; }
        tbody tr { border-bottom: 1px solid #e2e8f0; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:last-child { border-bottom: none; }
        tbody td {
            padding: 6px 8px;
            vertical-align: middle;
        }
        tbody td:first-child { text-align: center; color: #64748b; }
        tbody td:nth-child(4) { text-align: right; font-weight: 600; color: #047857; }
        tbody td:nth-child(5) { text-align: right; font-weight: 600; color: #047857; }
        .running-bal { color: #1e40af; font-weight: 600; }
        .row-expense td { color: #1e293b; }
        .row-expense td:nth-child(4) { color: #b91c1c; }
        .row-expense td:nth-child(5) { color: #b91c1c; }

        /* Totals row */
        .total-row td {
            background: #eff6ff !important;
            font-weight: 700;
            border-top: 2px solid #bfdbfe;
        }
        .total-row td:last-child,
        .total-row td:nth-child(5) {
            color: #1e40af;
        }

        /* === SUBTOTAL DIVIDER === */
        .section-subtotal {
            margin: 14px 24px 0;
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            font-size: 9px;
        }
        .section-subtotal .label { font-weight: 600; color: #166534; }
        .section-subtotal .value { font-weight: 700; color: #047857; }

        .section-subtotal.expense {
            background: #fef2f2;
            border-color: #fecaca;
        }
        .section-subtotal.expense .label { color: #991b1b; }
        .section-subtotal.expense .value { color: #b91c1c; }

        /* === SIGNATURE === */
        .signature-section {
            margin: 30px 24px 0;
            padding-top: 10px;
        }
        .signature-grid {
            display: table;
            width: 100%;
        }
        .signature-cell {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 10px;
        }
        .sig-label {
            font-size: 8px;
            color: #64748b;
            margin-bottom: 60px;
            font-weight: 500;
        }
        .sig-line {
            border-top: 1.5px solid #374151;
            margin: 0 auto;
            padding-top: 5px;
            display: inline-block;
            min-width: 140px;
        }
        .sig-name {
            font-size: 9px;
            font-weight: 700;
            color: #1e293b;
            display: block;
            white-space: nowrap;
        }
        .sig-title {
            font-size: 7.5px;
            color: #64748b;
            display: block;
            margin-top: 1px;
        }

        /* === FOOTER === */
        .footer {
            margin: 20px 24px 0;
            padding: 8px 12px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            font-size: 7px;
            color: #94a3b8;
            text-align: center;
        }

        /* === EMPTY STATE === */
        .empty-state {
            text-align: center;
            padding: 24px;
            color: #94a3b8;
            font-size: 9px;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="page-header">
        <div class="page-header-top">
            <div>
                <div class="org-name">Klinik Terapi & Sekolah Khusus Anak Mandiri</div>
                <div class="doc-title">E-STATEMENT</div>
                <div class="doc-subtitle">Rekening Koran</div>
            </div>
            <div class="statement-period">
                <div class="period-label">Periode</div>
                <div class="period-value">{{ $month->locale('id')->isoFormat('MMMM YYYY') }}</div>
            </div>
        </div>
    </div>
    <div class="accent-line"></div>

    <!-- ACCOUNT INFO -->
    <div class="account-info">
        <div class="account-left">
            <div class="info-row">
                <span class="info-label">Nama Bank</span>
                <span class="info-value bank-name">{{ $wallet->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pemilik Rekening</span>
                <span class="info-value">{{ $wallet->owner_name ?? '-' }}</span>
            </div>
        </div>
        <div class="account-right">
            <div class="info-row" style="justify-content: flex-end;">
                <span class="info-value">No. Rekening</span>
                <span class="info-label">{{ $wallet->account_number ?? '-' }}</span>
            </div>
            <div class="info-row" style="justify-content: flex-end;">
                <span class="info-value">Cetak Tanggal</span>
                <span class="info-label">{{ now()->locale('id')->isoFormat('DD MMMM YYYY') }}</span>
            </div>
        </div>
    </div>

    <!-- BALANCE SUMMARY -->
    <div class="balance-summary">
        <div class="balance-section">
            <div class="balance-label">Saldo Awal</div>
            <div class="balance-value">Rp {{ number_format($currentBalance - $income + $expense, 0, ',', '.') }}</div>
        </div>
        <div class="balance-section">
            <div class="balance-label">Total Pemasukan</div>
            <div class="balance-value income">+Rp {{ number_format($income, 0, ',', '.') }}</div>
        </div>
        <div class="balance-section">
            <div class="balance-label">Total Pengeluaran</div>
            <div class="balance-value expense">-Rp {{ number_format($expense, 0, ',', '.') }}</div>
        </div>
        <div class="balance-section">
            <div class="balance-label">Saldo Akhir</div>
            <div class="balance-value">Rp {{ number_format($currentBalance, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- === PEMASUKAN === -->
    @if(!$incomeRecords->isEmpty())
    <div class="table-container">
        <div class="table-title">Rincian Pemasukan ({{ $incomeRecords->count() }} Transaksi)</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Kategori</th>
                    <th>Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $balanceRun = $currentBalance - $income + $expense; @endphp
                @foreach($incomeRecords as $index => $r)
                @php $balanceRun += $r->amount; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->date)->format('d-m-Y') }}</td>
                    <td>{{ $r->sender_name ?? '-' }}</td>
                    <td>{{ $r->category_name ?? '-' }}</td>
                    <td>+{{ number_format($r->amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" style="text-align: right; padding-right: 12px;">TOTAL PEMASUKAN</td>
                    <td>+{{ number_format($income, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="section-subtotal">
        <span class="label">Total Debit / Pemasukan</span>
        <span class="value">Rp {{ number_format($income, 0, ',', '.') }}</span>
    </div>
    @endif

    <!-- === PENGELUARAN === -->
    @if(!$records->isEmpty())
    <div class="table-container" style="margin-top: 10px;">
        <div class="table-title">Rincian Pengeluaran ({{ $records->count() }} Transaksi)</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Kategori</th>
                    <th>Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $balanceRun2 = $currentBalance - $income + $expense + $income; @endphp
                @foreach($records as $index => $r)
                @php $balanceRun2 -= $r->amount; @endphp
                <tr class="row-expense">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->date)->format('d-m-Y') }}</td>
                    <td>{{ $r->title }}</td>
                    <td>{{ $r->category_name ?? '-' }}</td>
                    <td>-{{ number_format($r->amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" style="text-align: right; padding-right: 12px;">TOTAL PENGELUARAN</td>
                    <td>-{{ number_format($expense, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="section-subtotal expense">
        <span class="label">Total Kredit / Pengeluaran</span>
        <span class="value">Rp {{ number_format($expense, 0, ',', '.') }}</span>
    </div>
    @endif

    @if($records->isEmpty() && $incomeRecords->isEmpty())
    <div class="empty-state">
        <p>Tidak ada transaksi pada bulan {{ $month->locale('id')->isoFormat('MMMM YYYY') }}.</p>
    </div>
    @endif

    <!-- === SIGNATURES === -->
    <div class="signature-section">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <!-- Disusun Oleh -->
                <td style="width:33.33%; text-align:center; vertical-align:bottom; padding:0 8px;">
                    <div style="font-size:8px; color:#64748b; margin-bottom:55px; font-weight:500;">Disusun oleh,</div>
                    <div style="border-top:1.5px solid #374151; display:inline-block; min-width:140px; padding-top:5px;">
                        <span style="font-size:9px; font-weight:700; color:#1e293b; white-space:nowrap; display:block;">M. Baihaqi Maulana, S. Tr. Kom</span>
                        <span style="font-size:7.5px; color:#64748b; display:block; margin-top:1px;">Administrator</span>
                    </div>
                </td>
                <!-- Mengetahui -->
                <td style="width:33.33%; text-align:center; vertical-align:bottom; padding:0 8px;">
                    <div style="font-size:8px; color:#64748b; margin-bottom:55px; font-weight:500;">Mengetahui,</div>
                    <div style="border-top:1.5px solid #374151; display:inline-block; min-width:140px; padding-top:5px;">
                        <span style="font-size:9px; font-weight:700; color:#1e293b; white-space:nowrap; display:block;">Rovaldi Rama, S.E.</span>
                        <span style="font-size:7.5px; color:#64748b; display:block; margin-top:1px;">Kepala Sekolah</span>
                    </div>
                </td>
                <!-- Disetujui -->
                <td style="width:33.33%; text-align:center; vertical-align:bottom; padding:0 8px;">
                    <div style="font-size:8px; color:#64748b; margin-bottom:55px; font-weight:500;">Disetujui,</div>
                    <div style="border-top:1.5px solid #374151; display:inline-block; min-width:140px; padding-top:5px;">
                        <span style="font-size:9px; font-weight:700; color:#1e293b; white-space:nowrap; display:block;">Rovanita Rama, S.E., M.H.</span>
                        <span style="font-size:7.5px; color:#64748b; display:block; margin-top:1px;">Kepala Yayasan</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh sistem &mdash; {{ config('app.name') }} &copy; {{ now()->year }}
    </div>

</body>
</html>
