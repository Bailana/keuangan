<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tunggakan</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 14pt; }
        .info { margin-bottom: 15px; font-size: 9pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 5px 8px; text-align: left; border-bottom: 1px solid #ddd; font-size: 9pt; }
        th { background: #f8f8f8; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .status-paid { color: #059669; }
        .status-partial { color: #d97706; }
        .status-unpaid { color: #6b7280; }
        .status-overdue { color: #dc2626; }
        .total-row { font-weight: bold; background: #fef2f2; }
        .footer { margin-top: 20px; font-size: 8pt; color: #999; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN REKAP TUNGGAKAN</h1>
        <p>{{ config('app.name') }}</p>
    </div>

    <div class="info">
        <strong>Periode:</strong> {{ \Carbon\Carbon::create($year, $month, 1)->locale('id')->format('F Y') }}<br>
        @if($wallet)
        <strong>Dompet:</strong> {{ $wallet->name }}<br>
        @endif
        <strong>Dicetak:</strong> {{ $generatedDate }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anak</th>
                <th>Kelas</th>
                <th>Layanan</th>
                <th class="text-right">Tagihan</th>
                <th class="text-right">Dibayar</th>
                <th class="text-right">Tunggakan</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($children as $index => $child)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $child->name }}</td>
                <td>{{ $child->class_name ?? '-' }}</td>
                <td>{{ trim(implode(', ', $child->getServiceLabels())) }}</td>
                <td class="text-right">Rp {{ number_format($child->invoiceAmount, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($child->totalPaid, 0, ',', '.') }}</td>
                <td class="text-right" style="color: {{ $child->outstanding > 0 ? '#dc2626' : '#059669' }}">
                    Rp {{ number_format($child->outstanding, 0, ',', '.') }}
                </td>
                <td class="text-center">
                    @php
                        $statusClass = match($child->paymentStatus) {
                            'paid' => 'status-paid',
                            'partial' => 'status-partial',
                            'unpaid' => 'status-unpaid',
                            'overdue' => 'status-overdue',
                            default => '',
                        };
                        $statusText = match($child->paymentStatus) {
                            'paid' => 'Lunas',
                            'partial' => 'Sebagian',
                            'unpaid' => 'Belum Bayar',
                            'overdue' => 'Jatuh Tempo',
                            default => $child->paymentStatus,
                        };
                    @endphp
                    <span class="{{ $statusClass }}">{{ $statusText }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="text-align: right; font-weight: bold; margin-top: 10px;">
        Total Tunggakan: Rp {{ number_format($summary['totalOutstanding'] ?? 0, 0, ',', '.') }}
    </div>

    <div class="footer">
        Dicetak pada {{ $generatedDate }}
    </div>
</body>
</html>
