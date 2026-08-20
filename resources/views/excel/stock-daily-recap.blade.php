<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    @verbatim
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Rekap Stok Harian</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    @endverbatim
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .title { font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 5px; }
        .subtitle { font-size: 12px; text-align: center; color: #555; margin-bottom: 15px; }
        .meta-table { margin-bottom: 15px; }
        .meta-table td { font-size: 11px; padding: 3px; }
        table.data-table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        table.data-table th { background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #1e3a8a; padding: 8px; text-align: center; }
        table.data-table td { border: 1px solid #cbd5e1; padding: 6px; font-size: 11px; }
        table.data-table tr:nth-child(even) { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .bg-total { background-color: #e2e8f0; font-weight: bold; }
    </style>
</head>
<body>

    <div class="title">CV ARI GITA GROSIR - LAPORAN REKAP STOK HARIAN</div>
    <div class="subtitle">Tanggal Rekap: {{ $recapDate->translatedFormat('d F Y') }}</div>

    <table class="meta-table">
        <tr>
            <td><strong>Tanggal Dicetak:</strong> {{ $generatedAt->translatedFormat('d F Y, H:i') }} WIB</td>
            <td><strong>Oleh:</strong> {{ $generatedBy }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 250px;">Nama Produk</th>
                <th style="width: 120px;">SKU</th>
                <th style="width: 70px;">Satuan</th>
                <th style="width: 90px;">Stok Awal</th>
                <th style="width: 90px;">Barang Masuk</th>
                <th style="width: 90px;">Barang Keluar</th>
                <th style="width: 90px;">Stok Akhir</th>
                <th style="width: 120px;">Average Cost (HPP)</th>
                <th style="width: 140px;">Total Nilai Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalStokAwal = 0;
                $totalMasuk = 0;
                $totalKeluar = 0;
                $totalStokAkhir = 0;
                $totalNilaiInventaris = 0;
            @endphp

            @forelse ($rows as $row)
                @php
                    $nilaiStokAkhir = $row['stok_akhir'] * $row['average_cost'];
                    $totalStokAwal += $row['stok_awal'];
                    $totalMasuk += $row['barang_masuk'];
                    $totalKeluar += $row['barang_keluar'];
                    $totalStokAkhir += $row['stok_akhir'];
                    $totalNilaiInventaris += $nilaiStokAkhir;
                @endphp
                <tr>
                    <td class="text-center">{{ $row['no'] }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td class="text-center">{{ $row['sku'] }}</td>
                    <td class="text-center">{{ $row['unit'] }}</td>
                    <td class="text-right">{{ number_format($row['stok_awal'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['barang_masuk'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['barang_keluar'], 0, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($row['stok_akhir'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($row['average_cost'], 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($nilaiStokAkhir, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data produk.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-total">
                <td colspan="4" class="text-right font-bold">TOTAL:</td>
                <td class="text-right">{{ number_format($totalStokAwal, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalMasuk, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalKeluar, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalStokAkhir, 0, ',', '.') }}</td>
                <td class="text-right">-</td>
                <td class="text-right font-bold">Rp {{ number_format($totalNilaiInventaris, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    @if ($adjustments->count() > 0)
        <br>
        <div style="font-weight: bold; font-size: 14px; margin-bottom: 8px;">Penyesuaian Stok (Stock Adjustment) Pada Tanggal Ini</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 250px;">Nama Produk</th>
                    <th style="width: 120px;">SKU</th>
                    <th style="width: 90px;">Stok Sistem</th>
                    <th style="width: 90px;">Stok Fisik</th>
                    <th style="width: 90px;">Selisih</th>
                    <th style="width: 200px;">Catatan</th>
                    <th style="width: 120px;">Petugas</th>
                    <th style="width: 80px;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($adjustments as $index => $adj)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $adj['product_name'] }}</td>
                        <td class="text-center">{{ $adj['sku'] }}</td>
                        <td class="text-right">{{ number_format($adj['system_stock'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($adj['physical_stock'], 0, ',', '.') }}</td>
                        <td class="text-right font-bold {{ $adj['difference'] < 0 ? 'text-red' : '' }}">
                            {{ $adj['difference'] > 0 ? '+' : '' }}{{ number_format($adj['difference'], 0, ',', '.') }}
                        </td>
                        <td>{{ $adj['note'] ?? '-' }}</td>
                        <td>{{ $adj['created_by'] }}</td>
                        <td class="text-center">{{ $adj['created_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>
