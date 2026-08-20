<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoiceNumber ?? 'Belum ada nomor' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #333; padding: 30px; }

        .invoice-container { max-width: 800px; margin: 0 auto; }

        /* Header */
        .invoice-header { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .company-info h2 { font-size: 16px; color: #2c5282; margin-bottom: 5px; }
        .company-info p { font-size: 10px; color: #666; line-height: 1.6; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 28px; color: #2c5282; font-weight: bold; margin-bottom: 5px; }
        .invoice-title .number { font-size: 14px; color: #666; }

        /* Divider */
        .divider { height: 3px; background: #2c5282; margin: 20px 0; }

        /* Bill To / Invoice Details */
        .invoice-meta { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .bill-to h3 { font-size: 10px; text-transform: uppercase; color: #999; margin-bottom: 8px; }
        .bill-to p { font-size: 12px; margin: 3px 0; }
        .bill-to p strong { color: #333; }
        .invoice-details { text-align: right; }
        .invoice-details table { margin-left: auto; }
        .invoice-details td { padding: 4px 0; font-size: 11px; }
        .invoice-details td:first-child { color: #666; padding-right: 15px; }
        .invoice-details td:last-child { font-weight: bold; }

        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #f7fafc; padding: 10px; text-align: left; font-size: 10px; text-transform: uppercase; color: #666; border-bottom: 2px solid #e2e8f0; }
        .items-table th:last-child { text-align: right; }
        .items-table td { padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .items-table td:last-child { text-align: right; font-weight: bold; }
        .items-table tr:last-child td { border-bottom: none; }

        /* Totals */
        .invoice-footer { display: flex; justify-content: space-between; margin-top: 20px; }
        .payment-info { max-width: 50%; }
        .payment-info h4 { font-size: 10px; text-transform: uppercase; color: #999; margin-bottom: 8px; }
        .payment-info p { font-size: 11px; color: #666; margin: 4px 0; }
        .totals { text-align: right; }
        .totals table { margin-left: auto; }
        .totals td { padding: 6px 0; font-size: 11px; }
        .totals td:first-child { color: #666; padding-right: 20px; }
        .totals tr.total-row td { font-size: 14px; font-weight: bold; color: #2c5282; border-top: 2px solid #2c5282; padding-top: 10px; }

        /* Status Badge */
        .status-badge { display: inline-block; background: #fed7d7; color: #c53030; padding: 6px 16px; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-top: 15px; }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            width: 250px;
            height: 250px;
            opacity: 0.13;
            pointer-events: none;
            z-index: 0;
        }
        .invoice-container { position: relative; }

        /* Footer */
        .invoice-bottom { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Watermark -->
        <img src="{{ $logoPath }}" alt="Watermark" class="watermark">

        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h2>Klinik Terapi & Sekolah Anak Mandiri</h2>
                <p>Invoice Tagihan Pembayaran<br>
                {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</p>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <p class="number">{{ $invoiceNumber }}</p>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Meta Info -->
        <div class="invoice-meta">
            <div class="bill-to">
                <h3>Data Anak</h3>
                <p><strong>{{ $child->name }}</strong></p>
                <p>{{ $child->parent_name ?? '-' }}</p>
                <p>{{ $child->parent_whatsapp ?? '-' }}</p>
                <p>Kelas: {{ $child->class_name ?? '-' }}</p>
            </div>
            <div class="invoice-details">
                <table>
                    <tr><td>Tanggal Cetak</td><td>{{ $generatedDate }}</td></tr>
                    <tr><td>Jatuh Tempo</td><td>10 {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</td></tr>
                </table>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Layanan</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
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
                    <td>Rp {{ number_format((float)$therapy->price_per_session * $sesi, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @foreach($child->vocationalTypes as $vokasi)
                @php $sesi = $vokasi->pivot->monthly_sessions ?? 4; @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>Vokasi</td>
                    <td>{{ $vokasi->name }} ({{ $sesi }} sesi)</td>
                    <td>Rp {{ number_format((float)$vokasi->price_per_session * $sesi, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @if($child->isTakingSekolah())
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>Sekolah</td>
                    <td>SPP Bulanan (Kelas {{ $child->class_name ?? '-' }})</td>
                    <td>Rp {{ number_format((float)$child->spp_fee, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($child->has_parent_support)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>Parent Support</td>
                    <td>Pendampingan orang tua</td>
                    <td>Rp {{ number_format((float)config('settings.parent_support_fee', 25000), 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($child->has_subsidi && $child->subsidi_amount > 0)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>Subsidi</td>
                    <td>Potongan subsidi admin</td>
                    <td style="color: #38a169;">- Rp {{ number_format((float)$child->subsidi_amount, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Footer with Totals -->
        <div class="invoice-footer">
            <div class="payment-info">
                <h4>Informasi Pembayaran</h4>
                <p>Mohon pembayaran dilakukan sebelum tanggal jatuh tempo.</p>
                <p>Bukti transfer dapat diserahkan ke admin.</p>
            </div>
            <div class="totals">
                <table>
                    <tr>
                        <td>Total Tagihan</td>
                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    </tr>
                </table>
                <span class="status-badge">Belum Lunas</span>
            </div>
        </div>

        <div class="invoice-bottom">
            Klinik Terapi & Sekolah Anak Mandiri &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
