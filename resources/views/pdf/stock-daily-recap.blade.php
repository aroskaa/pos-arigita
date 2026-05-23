<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Stok Harian</title>

    <style>
        @page {
            margin: 18px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111827;
        }

        .header {
            margin-bottom: 14px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 10px;
        }

        .company {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .subtitle {
            font-size: 11px;
            color: #4b5563;
        }

        .title {
            margin-top: 12px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .meta {
            margin-top: 10px;
            width: 100%;
            font-size: 10px;
        }

        .meta td {
            padding: 2px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        table.data th {
            background: #eff6ff;
            border: 1px solid #9ca3af;
            padding: 6px 5px;
            font-size: 9px;
            text-align: center;
        }

        table.data td {
            border: 1px solid #d1d5db;
            padding: 5px;
            font-size: 9px;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .product-name {
            font-weight: bold;
        }

        .sku {
            font-size: 8px;
            color: #6b7280;
        }

        .footer {
            margin-top: 12px;
            font-size: 9px;
            color: #6b7280;
        }

        .section-title {
            margin-top: 18px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }

        table.adjustment {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.adjustment th {
            background: #fff7ed;
            border: 1px solid #9ca3af;
            padding: 6px 5px;
            font-size: 9px;
            text-align: center;
        }

        table.adjustment td {
            border: 1px solid #d1d5db;
            padding: 5px;
            font-size: 9px;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">CV Ari Gita Grosir</div>
        <div class="subtitle">Rekapitulasi Persediaan Barang Harian</div>

        <div class="title">
            Rekap Stok {{ $recapDate->translatedFormat('l, d F Y') }}
        </div>

        <table class="meta">
            <tr>
                <td style="width: 120px;">Tanggal Rekap</td>
                <td>: {{ $recapDate->format('d M Y') }}</td>
                <td style="width: 120px;">Tanggal Generate</td>
                <td>: {{ $generatedAt->format('d M Y H:i') }}</td>
            </tr>
            <tr>
                <td>Digenerate Oleh</td>
                <td>: {{ $generatedBy }}</td>
                <td>Jumlah Produk</td>
                <td>: {{ $rows->count() }} produk</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th>Nama Barang</th>
                <th style="width: 85px;">Stok Awal Hari Ini</th>
                <th style="width: 85px;">Barang Keluar</th>
                <th style="width: 85px;">Barang Masuk</th>
                <th style="width: 85px;">Stok Akhir Hari Ini</th>
                <th style="width: 90px;">Avg Harga Jual</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td class="text-center">{{ $row['no'] }}</td>

                    <td>
                        <div class="product-name">{{ $row['name'] }}</div>
                        <div class="sku">{{ $row['sku'] }} · {{ $row['unit'] }}</div>
                    </td>

                    <td class="text-right">
                        {{ number_format($row['stok_awal'], 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($row['barang_keluar'], 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($row['barang_masuk'], 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($row['stok_akhir'], 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($row['average_cost'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($adjustments->count() > 0)
        <div class="section-title">
            Stock Adjustment
        </div>

        <table class="adjustment">
            <thead>
                <tr>
                    <th style="width: 28px;">No</th>
                    <th>Nama Barang</th>
                    <th style="width: 70px;">Stok Sistem</th>
                    <th style="width: 70px;">Stok Fisik</th>
                    <th style="width: 60px;">Selisih</th>
                    <th>Catatan</th>
                    <th style="width: 90px;">User</th>
                    <th style="width: 50px;">Jam</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($adjustments as $index => $adjustment)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>

                        <td>
                            <div class="product-name">{{ $adjustment['product_name'] }}</div>
                            <div class="sku">{{ $adjustment['sku'] }}</div>
                        </td>

                        <td class="text-right">
                            {{ number_format($adjustment['system_stock'], 0, ',', '.') }}
                        </td>

                        <td class="text-right">
                            {{ number_format($adjustment['physical_stock'], 0, ',', '.') }}
                        </td>

                        <td class="text-right">
                            {{ $adjustment['difference'] > 0 ? '+' : '' }}{{ number_format($adjustment['difference'], 0, ',', '.') }}
                        </td>

                        <td>
                            {{ $adjustment['note'] }}
                        </td>

                        <td>
                            {{ $adjustment['created_by'] }}
                        </td>

                        <td class="text-center">
                            {{ $adjustment['created_at'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    
    <div class="footer">
        Catatan: Barang masuk dan barang keluar merupakan total agregat seluruh transaksi stok pada tanggal rekap untuk masing-masing produk.
    </div>
</body>
</html>