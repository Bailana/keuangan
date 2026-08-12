<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - Tagihan {{ $child->name }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 30px; }
        .header { text-align: center; border-bottom: 3px solid #1e3a5f; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #1e3a5f; font-size: 18px; }
        .header p { margin: 3px 0 0; color: #6b7280; font-size: 10px; }
        .section { margin-bottom: 18px; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #1e3a5f; letter-spacing: 0.5px; margin-bottom: 6px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 4px 0; font-size: 11px; }
        .info-table td:first-child { width: 140px; color: #6b7280; font-weight: 500; }
        .info-table td:nth-child(2) { width: 12px; }
        .info-table td:last-child { color: #1f2937; }
        .subsidi-row { background: #f0fdf4; }
        .amount-box { background: #fff1f2; border: 2px solid #fca5a5; border-radius: 8px; padding: 15px; text-align: center; margin: 20px 0; }
        .amount-box .label { font-size: 10px; color: #991b1b; text-transform: uppercase; letter-spacing: 1px; }
        .amount-box .value { font-size: 28px; font-weight: 800; color: #dc2626; margin: 5px 0; }
        .amount-box .note { font-size: 9px; color: #991b1b; }
        .status-badge { display: inline-block; background: #dc2626; color: #fff; padding: 4px 14px; border-radius: 4px; font-weight: 700; font-size: 11px; text-transform: uppercase; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #9ca3af; text-align: center; }
        .due-date { background: #fff7ed; border: 1px solid #fde68a; border-radius: 6px; padding: 10px 15px; margin: 15px 0; font-size: 11px; color: #92400e; }
        .due-date strong { font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10px; }
        table th { background: #f3f4f6; padding: 8px; text-align: left; font-weight: 600; color: #374151; border: 1px solid #e5e7eb; }
        table td { padding: 8px; border: 1px solid #e5e7eb; }
        table tr:nth-child(even) { background: #f9fafb; }
        .total-row { font-weight: 700; background: #fee2e2 !important; }
        .total-row td { color: #dc2626; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Klinik Terapi & Sekolah Khusus Anak Mandiri</h1>
        <p>Invoice Tagihan Pembayaran</p>
    </div>

    <div class="section">
        <div class="section-title">Detail Invoice</div>
        <table class="info-table">
            <tr><td>No. Invoice</td><td>:</td><td><strong>{{ $invoiceNumber }}</strong></tr>
            <tr><td>Tanggal Cetak</td><td>:</td><td>{{ $generatedDate }}</tr>
            <tr><td>Periode</td><td>:</td><td>{{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Data Anak</div>
        <table class="info-table">
            <tr><td>Nama Anak</td><td>:</td><td><strong>{{ $child->name }}</strong></tr>
            <tr><td>Nama Orang Tua</td><td>:</td><td>{{ $child->parent_name ?? '-' }}</tr>
            <tr><td>No. WhatsApp</td><td>:</td><td>{{ $child->parent_whatsapp ?? '-' }}</tr>
            <tr><td>Kelas</td><td>:</td><td>{{ $child->class_name ?? '-' }}</tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Rincian Tagihan</div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Layanan</th>
                    <th>Keterangan</th>
                    <th style="text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($child->therapyTypes as $therapy)
                @php $sesi = $therapy->pivot->monthly_sessions ?? 4; @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>Terapi</td>
                    <td>{{ $therapy->name }} ({{ $sesi }} sesi)</td>
                    <td style="text-align: right;">Rp {{ number_format((float)$therapy->price_per_session * $sesi, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @foreach($child->vocationalTypes as $vokasi)
                @php $sesi = $vokasi->pivot->monthly_sessions ?? 4; @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>Vokasi</td>
                    <td>{{ $vokasi->name }} ({{ $sesi }} sesi)</td>
                    <td style="text-align: right;">Rp {{ number_format((float)$vokasi->price_per_session * $sesi, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @if($child->isTakingSekolah())
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>Sekolah</td>
                    <td>SPP Bulanan (Kelas {{ $child->class_name ?? '-' }})</td>
                    <td style="text-align: right;">Rp {{ number_format(config('settings.school_fee', 1000000), 0, ',', '.') }}</td>
                </tr>
                @endif
                        @if($child->has_subsidi && $child->subsidi_amount > 0)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>Subsidi</td>
                    <td>Potongan subsidi admin</td>
                    <td style="text-align: right; color: #059669;">- Rp {{ number_format((float)$child->subsidi_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">TOTAL TAGIHAN</td>
                    <td style="text-align: right;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="amount-box">
        <div class="label">Tagihan Bulan Ini</div>
        <div class="value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
        <div class="note">Status: <span class="status-badge">BELUM LUNAS</span></div>
    </div>

    <div class="due-date">
        <strong>⏰ Jatuh Tempo:</strong> Pembayaran paling lambat tanggal 10 {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
    </div>

    <div class="section">
        <div class="section-title">Catatan</div>
        <p style="margin:0; font-size:10px; color:#6b7280;">
            Mohon segera menyelesaikan pembayaran sebelum tanggal jatuh tempo.
            Pembayaran dapat dilakukan melalui transfer ke rekening Klinik Terapi & Sekolah Khusus Anak Mandiri.
            Bukti transfer dapat diserahkan ke admin setelah pembayaran dilakukan.
        </p>
    </div>

    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh sistem. Untuk informasi lebih lanjut, silakan hubungi admin.
        <br>Klinik Terapi & Sekolah Khusus Anak Mandiri &copy; {{ date('Y') }}
    </div>
</body>
</html>
