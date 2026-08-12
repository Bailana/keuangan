<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $payment->employee_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
            padding: 15px;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header img {
            width: 100%;
            height: auto;
            max-width: 100%;
        }

        .title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .employee-info {
            background: #f8fafc;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .employee-info table {
            width: 100%;
        }

        .employee-info td {
            padding: 3px 0;
        }

        .employee-info td:first-child {
            font-weight: 600;
            width: 120px;
            color: #555;
        }

        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #1e40af;
            margin: 12px 0 6px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #e5e7eb;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .salary-table td {
            padding: 5px 8px;
        }

        .salary-table tr td:first-child {
            color: #555;
        }

        .salary-table tr td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .salary-table .deductions td:first-child {
            color: #dc2626;
        }

        .salary-table .total-comp td {
            background: #f0fdf4;
            font-weight: 600;
            border-top: 2px solid #059669;
        }

        .salary-table .net-salary td {
            background: #eff6ff;
            font-weight: bold;
            font-size: 11pt;
            border-top: 3px solid #1e40af;
        }

        .salary-table .net-salary td:last-child {
            color: #1e40af;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8pt;
            color: #666;
        }

        .signatures {
            margin-top: 25px;
            width: 100%;
            table-layout: fixed;
        }

        .signature-cell {
            text-align: center;
            vertical-align: bottom;
            padding: 0 10px;
        }

        .signature-title {
            font-size: 9pt;
            font-weight: 600;
            color: #333;
            margin-bottom: 60px;
        }

        .signature-name {
            font-size: 9pt;
            font-weight: bold;
            color: #1e40af;
            border-top: 1px solid #333;
            padding-top: 5px;
            display: inline-block;
            min-width: 140px;
        }

        .signature-role {
            font-size: 8pt;
            color: #666;
            margin-top: 2px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            width: 250px;
            height: 250px;
            opacity: 0.08;
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body>
    <img src="{{ $logoPath }}" alt="Watermark" class="watermark">

    <div class="container">
        <div class="header">
            <img src="{{ $kopSuratPath }}" alt="Kop Surat">
        </div>

        <div class="title">Slip Gaji Karyawan</div>

        <div class="employee-info">
            <table>
                <tr>
                    <td>Nama Karyawan</td>
                    <td>: {{ $payment->employee_name }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: {{ $payment->position ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Periode</td>
                    <td>: {{ \Carbon\Carbon::create($payment->year, $payment->month, 1)->locale('id')->format('F') }} {{ $payment->year }}</td>
                </tr>
                <tr>
                    <td>Tanggal Bayar</td>
                    <td>: {{ $payment->paid_at?->format('d F Y') ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="section-title">Rincian Pendapatan</div>
        <table class="salary-table">
            <tr>
                <td>Gaji Pokok</td>
                <td>Rp {{ number_format($payment->base_salary, 0, ',', '.') }}</td>
            </tr>
            @if($payment->salary_extra > 0)
            <tr>
                <td>Tunjangan Lain</td>
                <td>Rp {{ number_format($payment->salary_extra, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($payment->session_bonus > 0)
            <tr>
                <td>Bonus Sesi</td>
                <td>Rp {{ number_format($payment->session_bonus, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-comp">
                <td><strong>Total Kompensasi</strong></td>
                <td><strong>Rp {{ number_format($payment->total_compensation, 0, ',', '.') }}</strong></td>
            </tr>
        </table>

        <div class="section-title">Potongan</div>
        <table class="salary-table">
            <tr class="deductions">
                <td>BPJS Kesehatan</td>
                <td>- Rp {{ number_format($payment->transport_allowance, 0, ',', '.') }}</td>
            </tr>
            <tr class="deductions">
                <td>BPJS Ketenagakerjaan</td>
                <td>- Rp {{ number_format($payment->deductions, 0, ',', '.') }}</td>
            </tr>
        </table>

        <table class="salary-table">
            <tr class="net-salary">
                <td><strong>TOTAL BERSIH DITERIMA</strong></td>
                <td><strong>Rp {{ number_format($payment->net_salary, 0, ',', '.') }}</strong></td>
            </tr>
        </table>

        <div class="footer">
            <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
        </div>

        <table class="signatures" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="25%" class="signature-cell">
                    <div class="signature-title">Disetujui oleh<br>Supervisor</div>
                    <div class="signature-name">Zaky Khairi</div>
                </td>
                <td width="25%" class="signature-cell">
                    <div class="signature-title">Kepala Sekolah</div>
                    <div class="signature-name">Rovaldi Rama, S.E.</div>
                </td>
                <td width="25%" class="signature-cell">
                    <div class="signature-title">Dibuat oleh<br>Administrasi</div>
                    <div class="signature-name">{{ $adminName }}</div>
                </td>
                <td width="25%" class="signature-cell">
                    <div class="signature-title">Diterima oleh<br>Karyawan</div>
                    <div class="signature-name">{{ $payment->employee_name }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
