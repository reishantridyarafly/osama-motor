<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Struk Penjualan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header,
        .footer {
            text-align: center;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <strong>{{ config('app.name') }}</strong><br>
        Jl. Contoh Alamat No. 123<br>
        Telp: 08xxxxxxxxxx
    </div>

    <div class="line"></div>

    <p>Kasir: {{ $stockOuts->first()->cashier->first_name ?? '-' }}</p>
    <p>Tanggal: {{ \Carbon\Carbon::parse($time)->format('d-m-Y H:i:s') }}</p>

    <div class="line"></div>

    <table>
        @php $total = 0; @endphp
        @foreach ($stockOuts as $item)
            <tr>
                <td>{{ $item->item->name }}</td>
                <td class="right">{{ $item->quantity }} x Rp {{ number_format($item->price_sale, 0, ',', '.') }}</td>
            </tr>
            @php $total += $item->quantity * $item->price_sale; @endphp
        @endforeach
    </table>

    <div class="line"></div>

    <p class="right"><strong>Total: Rp {{ number_format($total, 0, ',', '.') }}</strong></p>

    <div class="line"></div>

    <div class="footer">
        Terima kasih telah berbelanja!<br>
        Struk ini adalah bukti pembayaran sah.
    </div>
</body>

</html>
