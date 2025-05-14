<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - Periode {{ $period }}</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 1cm;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .period-info {
            background: #f8f9fa;
            padding: 5px;
            border-radius: 3px;
            margin-bottom: 10px;
            border-left: 3px solid #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #dee2e6;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: white;
            font-weight: 600;
            font-size: 9px;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .total-row td {
            background-color: #2c3e50 !important;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }

        .price-column {
            text-align: right;
        }

        .quantity-column {
            text-align: center;
        }

        .no-data {
            text-align: center;
            padding: 10px;
            font-style: italic;
            color: #6c757d;
        }

        .summary-section {
            margin-top: 20px;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin-bottom: 20px;
        }

        .summary-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <h2>PERIODE {{ strtoupper($period) }}</h2>
    </div>

    <div class="period-info">
        <strong>Periode Laporan:</strong> {{ $period }}
    </div>

    <table>
        <tr>
            <th>Tanggal</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Harga Jual</th>
            <th>Total Penjualan</th>
        </tr>
        @forelse ($stockOuts as $stockOut)
            <tr>
                <td>{{ \Carbon\Carbon::parse($stockOut->date)->translatedFormat('l, d F Y') }}</td>
                <td>{{ $stockOut->item->name }}</td>
                <td class="quantity-column">{{ $stockOut->quantity }}</td>
                <td class="price-column">{{ 'Rp ' . number_format($stockOut->item->price, 0, ',', '.') }}</td>
                <td class="price-column">
                    {{ 'Rp ' . number_format($stockOut->quantity * $stockOut->item->price, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="no-data">Data tidak tersedia</td>
            </tr>
        @endforelse
        <tr class="total-row">
            <td colspan="2" style="text-align: right;">TOTAL</td>
            <td class="quantity-column">{{ $total_quantity }}</td>
            <td></td>
            <td class="price-column">
                {{ 'Rp ' .
                    number_format(
                        $stockOuts->sum(function ($stockOut) {
                            return $stockOut->quantity * $stockOut->item->price;
                        }),
                        0,
                        ',',
                        '.',
                    ) }}
            </td>
        </tr>
    </table>

    <div class="summary-section">
        <div class="summary-title">Ringkasan Penjualan:</div>
        <table>
            <tr>
                <td style="width: 200px">Total Item Terjual</td>
                <td>: {{ $total_quantity }} unit</td>
            </tr>
            <tr>
                <td>Total Penjualan</td>
                <td>:
                    {{ 'Rp ' .
                        number_format(
                            $stockOuts->sum(function ($stockOut) {
                                return $stockOut->quantity * $stockOut->unit_price;
                            }),
                            0,
                            ',',
                            '.',
                        ) }}
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
