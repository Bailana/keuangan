<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laba / Rugi</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16pt; }
        .header p { margin: 5px 0 0; color: #666; }
        .info { margin-bottom: 15px; font-size: 10pt; }
        .section { margin-bottom: 20px; }
        .section h2 { font-size: 12pt; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 2px solid #333; }
        .income h2 { color: #059669; border-color: #059669; }
        .expense h2 { color: #dc2626; border-color: #dc2626; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 6px 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { font-weight: bold; background: #f8f8f8; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background: #f0f0f0; }
        .net-profit { font-size: 13pt; font-weight: bold; margin-top: 10px; }
        .footer { margin-top: 30px; font-size: 9pt; color: #999; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN LABA / RUGI</h1>
        <p>{{ config('app.name') }}</p>
    </div>

    <div class="info">
        <strong>Periode:</strong> {{ $period }}<br>
        @if($wallet)
        <strong>Dompet:</strong> {{ $wallet->name }}<br>
        @endif
        <strong>Dicetak:</strong> {{ $generatedDate }}
    </div>

    <div class="section income">
        <h2>PENDAPATAN</h2>
        <table>
            <thead><tr><th>Kategori</th><th class="text-right">Jumlah</th></tr></thead>
            <tbody>
                @php
                    $incomeOrder = ['Terapi', 'Vokasi', 'SPP', 'Parent Support'];
                    $incomeFiltered = collect($incomeBreakdown)->filter(fn($v) => $v > 0)->sortBy(function($v, $k) use ($incomeOrder) {
                        $pos = array_search($k, $incomeOrder);
                        return $pos === false ? 999 : $pos;
                    });
                @endphp
                @forelse($incomeFiltered as $name => $amount)
                <tr>
                    <td>{{ $name }}</td>
                    <td class="text-right">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="2" style="text-align:center;color:#999;">Belum ada data pendapatan</td></tr>
                @endforelse
                <tr class="total-row">
                    <td>Total Pendapatan</td>
                    <td class="text-right">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section expense">
        <h2>BEBAN</h2>
        <table>
            <thead><tr><th>Kategori</th><th class="text-right">Jumlah</th></tr></thead>
            <tbody>
                @php
                    $expenseOrder = ['Gaji Karyawan', 'SPP', 'Terapi', 'Vokasi', 'Parent Support', 'BPJS Kesehatan', 'BPJS Ketenagakerjaan', 'Inklusi', 'Pulsa & Pascabayar', 'Internet', 'Listrik', 'Tunjangan', 'Lain-lain'];
                    $expenseOrdered = $expenseByCategory->sortBy(function($item) use ($expenseOrder) {
                        $name = $item->category->name ?? 'Umum';
                        $pos = array_search($name, $expenseOrder);
                        return $pos === false ? 999 : $pos;
                    });
                @endphp
                @foreach($expenseOrdered as $item)
                <tr>
                    <td>{{ $item->category->name ?? 'Umum' }}</td>
                    <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total Beban</td>
                    <td class="text-right">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="net-profit" style="color: {{ $netProfit >= 0 ? '#059669' : '#dc2626' }}">
        <strong>LABA / RUGI BERSIH:</strong> Rp {{ number_format(abs($netProfit), 0, ',', '.') }}
        <span style="font-size: 10pt; font-weight: normal;">({{ $netProfit >= 0 ? 'Laba' : 'Rugi' }} — Margin {{ number_format(abs($margin), 1) }}%)</span>
    </div>

    <div class="footer">
        Dicetak pada {{ $generatedDate }}
    </div>
</body>
</html>
