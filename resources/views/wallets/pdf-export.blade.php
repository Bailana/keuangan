<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $wallet->name }} - {{ $month->locale('id')->isoFormat('MMMM YYYY') }}</title>
    <style>
        @page {
            margin: 15mm;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            font-size: 9px;
            color: #1a1a2e;
            background: #fff;
            width: 100%;
        }

        /* === HEADER UTAMA (halaman 1) === */
        .main-header {
            background: #1e3a5f;
            color: #fff;
            padding: 12px 0;
            width: 100%;
        }
        .main-header-top {
            display: table;
            width: 100%;
        }
        .main-header-left {
            display: table-cell;
            vertical-align: middle;
            padding: 0 20px;
        }
        .main-header-right {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            padding: 0 20px;
        }
        .org-name {
            font-size: 9px;
            font-weight: 600;
            color: #bfdbfe;
            margin-bottom: 2px;
        }
        .doc-title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .doc-subtitle {
            font-size: 7px;
            color: #93c5fd;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .period-box { text-align: right; }
        .period-label {
            font-size: 6px;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .period-value {
            font-size: 12px;
            font-weight: 700;
            color: #fff;
        }
        .header-accent {
            height: 3px;
            background: #2563eb;
            width: 100%;
        }

        /* === ACCOUNT INFO === */
        .account-info {
            display: table;
            width: 100%;
            padding: 10px 20px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 8px;
        }
        .info-row { display: table-row; }
        .info-cell-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-cell-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        .info-row-inner {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }
        .info-label {
            color: #64748b;
            font-weight: 500;
            font-size: 8px;
            display: table-cell;
        }
        .info-value {
            color: #1e293b;
            font-weight: 600;
            font-size: 8px;
            display: table-cell;
        }
        .info-value.bank-name {
            color: #1d4ed8;
            font-size: 9px;
        }

        /* === BALANCE CARDS === */
        .balance-summary {
            display: table;
            width: 100%;
            margin: 12px 0;
            border-collapse: collapse;
        }
        .balance-card {
            display: table-cell;
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .balance-card:first-child { border-left: 1px solid #e2e8f0; }
        .balance-card:nth-child(2) { border-left: 1px solid #bfdbfe; }
        .balance-card:nth-child(3) { border-left: 1px solid #fecaca; }
        .balance-card:last-child { border-right: 1px solid #e2e8f0; }
        .balance-card.beginning { background: #eff6ff; border-color: #bfdbfe; }
        .balance-card.income   { background: #f0fdf4; border-color: #bbf7d0; }
        .balance-card.expense  { background: #fef2f2; border-color: #fecaca; }
        .balance-card.end      { background: #faf5ff; border-color: #e9d5ff; }
        .card-label {
            font-size: 6px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .balance-card.beginning .card-label { color: #1d4ed8; }
        .balance-card.income .card-label    { color: #047857; }
        .balance-card.expense .card-label   { color: #b91c1c; }
        .balance-card.end .card-label       { color: #7e22ce; }
        .card-value { font-size: 11px; font-weight: 700; }
        .balance-card.beginning .card-value { color: #1e40af; }
        .balance-card.income .card-value    { color: #047857; }
        .balance-card.expense .card-value   { color: #b91c1c; }
        .balance-card.end .card-value       { color: #7e22ce; }

        /* === TABLE === */
        .table-section { margin: 0 0 10px; }
        .table-title {
            font-size: 8px;
            font-weight: 700;
            color: #fff;
            background: #1e3a5f;
            padding: 6px 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5px;
        }
        thead th {
            background: #1e3a5f;
            color: #fff;
            padding: 5px 6px;
            text-align: left;
            font-weight: 600;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #1a3550;
        }
        thead th.text-center { text-align: center; }
        thead th.text-right { text-align: right; }
        tbody tr { border-bottom: 1px solid #e2e8f0; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:last-child { border-bottom: none; }
        tbody td { padding: 5px 6px; vertical-align: top; }
        tbody td.text-center { text-align: center; color: #64748b; font-weight: 500; }
        tbody td.text-right { text-align: right; font-weight: 600; white-space: nowrap; }
        tbody td.col-saldo { color: #1e40af; }

        .note-cell { color: #334155; line-height: 1.3; word-wrap: break-word; overflow-wrap: break-word; }
        .category-cell { color: #64748b; font-size: 7px; }

        .row-income td.col-income { color: #047857; }
        .row-expense td.col-expense { color: #b91c1c; }

        /* Highlighted row for children with subsidi */
        .row-subsidi {
            background: #fffbeb !important;
            border-left: 3px solid #f59e0b !important;
        }
        .row-subsidi td { color: #92400e; }

        .total-row td {
            background: #f1f5f9 !important;
            font-weight: 700;
            border-top: 2px solid #cbd5e1;
        }
        .total-row td.col-income { color: #047857; }
        .total-row td.col-expense { color: #b91c1c; }
        .total-row td.col-saldo { color: #1e40af; }

        .empty-state {
            text-align: center;
            padding: 20px;
            color: #94a3b8;
            font-size: 8px;
        }

        /* === SIGNATURES === */
        .signature-section { margin: 20px 20px 0; }
        .signature-grid { width: 100%; border-collapse: collapse; }
        .signature-grid td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 5px;
        }
        .sig-label { font-size: 7px; color: #64748b; margin-bottom: 40px; font-weight: 500; }
        .sig-line {
            border-top: 1.5px solid #374151;
            display: inline-block;
            min-width: 110px;
            padding-top: 4px;
        }
        .sig-name { font-size: 7.5px; font-weight: 700; color: #1e293b; white-space: nowrap; }
        .sig-title { font-size: 6px; color: #64748b; margin-top: 1px; }

        /* === FOOTER === */
        .footer {
            margin: 12px 20px 0;
            padding: 6px 10px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            font-size: 6px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- === HEADER === -->
    <div class="main-header">
        <div class="main-header-top">
            <div class="main-header-left">
                <div class="org-name">Klinik Terapi & Sekolah Khusus Anak Mandiri</div>
                <div class="doc-title">E-STATEMENT</div>
                <div class="doc-subtitle">Rekening Koran</div>
            </div>
            <div class="main-header-right">
                <div class="period-box">
                    <div class="period-label">Periode</div>
                    <div class="period-value">{{ $month->locale('id')->isoFormat('MMMM YYYY') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-accent"></div>

    <!-- === ACCOUNT INFO === -->
    <div class="account-info">
        <div class="info-cell-left">
            <div class="info-row-inner">
                <span class="info-label" style="width: 100px;">Nama Bank</span>
                <span class="info-value bank-name">{{ $wallet->name }}</span>
            </div>
            <div class="info-row-inner">
                <span class="info-label" style="width: 100px;">Pemilik Rekening</span>
                <span class="info-value">{{ $wallet->owner_name ?? '-' }}</span>
            </div>
        </div>
        <div class="info-cell-right">
            <div class="info-row-inner" style="justify-content: flex-end;">
                <span class="info-value">No. Rekening</span>
                <span class="info-label">{{ $wallet->account_number ?? '-' }}</span>
            </div>
            <div class="info-row-inner" style="justify-content: flex-end;">
                <span class="info-value">Cetak Tanggal</span>
                <span class="info-label">{{ now()->locale('id')->isoFormat('DD MMMM YYYY') }}</span>
            </div>
        </div>
    </div>

    <!-- === BALANCE SUMMARY === -->
    <div class="balance-summary">
        <div class="balance-card beginning">
            <div class="card-label">Saldo Awal</div>
            <div class="card-value">Rp {{ number_format($openingBalance ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="balance-card income">
            <div class="card-label">Total Pemasukan</div>
            <div class="card-value">+Rp {{ number_format($income, 0, ',', '.') }}</div>
        </div>
        <div class="balance-card expense">
            <div class="card-label">Total Pengeluaran</div>
            <div class="card-value">-Rp {{ number_format($expense, 0, ',', '.') }}</div>
        </div>
        <div class="balance-card end">
            <div class="card-label">Saldo Akhir</div>
            <div class="card-value">Rp {{ number_format($currentBalance, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- === TRANSACTION TABLE === -->
    @if(count($allTransactions) > 0)
    <div class="table-section">
        <div class="table-title">Rincian Transaksi ({{ count($allTransactions) }} Transaksi)</div>
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width:28px">No</th>
                    <th style="width:65px">Tanggal</th>
                    <th>Keterangan</th>
                    <th style="width:75px">Kategori</th>
                    <th class="text-right" style="width:85px">Pemasukan</th>
                    <th class="text-right" style="width:85px">Pengeluaran</th>
                    <th class="text-right" style="width:85px">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($allTransactions as $index => $r)
                @php
                    $isIncome = $r->type === 'income';
                    $isSubsidi = !empty($r->is_subsidi);
                    $rowClass = $isSubsidi ? 'row-subsidi' : ($isIncome ? 'row-income' : 'row-expense');
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->date)->format('d-m-Y') }}</td>
                    <td class="note-cell">{{ $r->keterangan ?? '-' }}</td>
                    <td class="category-cell">{{ $r->category_name ?? '-' }}</td>
                    <td class="text-right col-income">
                        @if($isIncome){{ number_format($r->amount, 0, ',', '.') }}@endif
                    </td>
                    <td class="text-right col-expense">
                        @if(!$isIncome){{ number_format($r->amount, 0, ',', '.') }}@endif
                    </td>
                    <td class="text-right col-saldo">
                        Rp {{ number_format($r->saldo, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-center" style="padding-right:8px">TOTAL</td>
                    <td class="text-right col-income">+{{ number_format($income, 0, ',', '.') }}</td>
                    <td class="text-right col-expense">-{{ number_format($expense, 0, ',', '.') }}</td>
                    <td class="text-right col-saldo">Rp {{ number_format($currentBalance, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <p>Tidak ada transaksi pada bulan {{ $month->locale('id')->isoFormat('MMMM YYYY') }}.</p>
    </div>
    @endif

    <!-- === SIGNATURES === -->
    <div class="signature-section">
        <table class="signature-grid">
            <tr>
                <td>
                    <div class="sig-label">Disusun oleh,</div>
                    <div class="sig-line">
                        <span class="sig-name">M. Baihaqi Maulana, S. Tr. Kom</span>
                        <span class="sig-title">Administrator</span>
                    </div>
                </td>
                <td>
                    <div class="sig-label">Mengetahui,</div>
                    <div class="sig-line">
                        <span class="sig-name">Rovaldi Rama, S.E.</span>
                        <span class="sig-title">Kepala Sekolah</span>
                    </div>
                </td>
                <td>
                    <div class="sig-label">Disetujui,</div>
                    <div class="sig-line">
                        <span class="sig-name">Rovanita Rama, S.E., M.H.</span>
                        <span class="sig-title">Kepala Yayasan</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- === FOOTER === -->
    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh sistem &mdash; {{ config('app.name') }} &copy; {{ now()->year }}
    </div>

</body>
</html>
