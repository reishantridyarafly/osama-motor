<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Struk Penjualan</title>
    <style>
        @page {
            margin: 0;
            size: 80mm 297mm;
        }

        body {
            font-family: "Courier New", Courier, monospace;
            font-size: 10px;
            margin: 0;
            padding: 15px;
            width: 72mm;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
            padding-top: 20px;
            width: 100%;
        }

        .header .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header .address {
            font-size: 9px;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .transaction-info {
            margin: 12px 0;
            width: 100%;
        }

        .transaction-info p {
            margin: 4px 0;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 10px 0;
            width: 100%;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin: 10px 0;
        }

        table tr {
            margin-bottom: 5px;
        }

        table td {
            padding: 3px 0;
            word-wrap: break-word;
        }

        .item-name {
            width: 60%;
            overflow-wrap: break-word;
            word-wrap: break-word;
            hyphens: auto;
        }

        .item-details {
            width: 40%;
            text-align: right;
        }

        .total-section {
            margin: 12px 0;
            text-align: right;
            font-size: 12px;
            width: 100%;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 9px;
            width: 100%;
            word-wrap: break-word;
            line-height: 1.4;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">{{ config('app.name') }}</div>
        <div class="address">
            Jl. Raya Susukan No.83, Ciawigebang, Kec. Ciawigebang, Kabupaten Kuningan, Jawa Barat 45591
        </div>
    </div>

    <div class="line"></div>

    <div class="transaction-info">
        <p><strong>Kasir:</strong> {{ $stockOuts->first()->cashier->first_name ?? '-' }}</p>
        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($time)->format('d-m-Y H:i:s') }}</p>
    </div>

    <div class="line"></div>

    <table>
        @php $total = 0; @endphp
        @foreach ($stockOuts as $item)
            <tr>
                <td class="item-name">{{ $item->item->name }}</td>
                <td class="item-details">
                    {{ $item->quantity }}x{{ number_format($item->price_sale, 0, ',', '.') }}
                    <strong>{{ number_format($item->quantity * $item->price_sale, 0, ',', '.') }}</strong>
                </td>
            </tr>
            @php $total += $item->quantity * $item->price_sale; @endphp
        @endforeach
    </table>

    <div class="line"></div>

    <div class="total-section">
        <strong>Total: Rp {{ number_format($total, 0, ',', '.') }}</strong>
    </div>

    <div class="line"></div>

    <div class="footer">
        Terima kasih telah berbelanja! Barang yang sudah dibeli tidak dapat dikembalikan. Struk ini adalah bukti
        pembayaran yang sah
    </div>
</body>

</html>
