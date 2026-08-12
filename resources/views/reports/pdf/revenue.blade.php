<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendapatan</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 14pt; }
        .info { margin-bottom: 15px; font-size: 9pt; }
        .section { margin-bottom: 20px; }
        .section h2 { font-size: 11pt; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 2px solid #059669; color: #059669; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 5px 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { font-weight: bold; background: #f8f8f8; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background: #d1fae5; }
        .footer { margin-top: 20px; font-size: 8pt; color: #999; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PENDAPATAN</h1>
        <p>{{ config('app.name') }}</p>
    </div>

    <div class="info">
        <strong>Periode:</strong> {{ $period }}<br>
        @if($wallet)
        <strong>Dompet:</strong> {{ $wallet->name }}<br>
        @endif
        <strong>Dicetak:</strong> {{ $generatedDate }}
    </div>

    <div class="section">
        <h2>PENDAPATAN PER KATEGORI</h2>
        <table>
            <thead><tr><th>Kategori</th><th class="text-right">Jumlah</th></tr></thead>
            <tbody>
                @foreach($incomeByCategory as $item)
                <tr>
                    <td>{{ $item->category->name ?? 'Umum' }}</td>
                    <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total</td>
                    <td class="text-right">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>PENDAPATAN PER ANAK</h2>
        <table>
            <thead><tr><th>Nama Anak</th><th class="text-right">Jumlah</th></tr></thead>
            <tbody>
                @foreach($incomeByChild as $item)
                <tr>
                    <td>{{ $item->child->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        Dicetak pada {{ $generatedDate }}
    </div>
</body>
</html>
