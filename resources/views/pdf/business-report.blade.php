<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 18px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .header { border-bottom: 1px solid #d1d5db; padding-bottom: 10px; margin-bottom: 12px; }
        .company { font-size: 18px; font-weight: bold; }
        .title { margin-top: 8px; font-size: 15px; font-weight: bold; text-transform: uppercase; text-align: center; }
        .meta { width: 100%; margin-top: 8px; }
        .meta td { padding: 2px 0; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th { background: #eff6ff; border: 1px solid #9ca3af; padding: 5px; font-size: 8px; }
        table.data td { border: 1px solid #d1d5db; padding: 5px; font-size: 8px; vertical-align: top; }
        .right { text-align: right; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .summary { width: 60%; margin-top: 12px; border-collapse: collapse; }
        .summary td { border: 1px solid #d1d5db; padding: 6px; }
        .footer { margin-top: 12px; font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">CV Ari Gita Grosir</div>
        <div>Grosir Minuman</div>
        <div class="title">{{ $title }}</div>
        <table class="meta">
            <tr>
                <td style="width: 110px;">Periode</td>
                <td>: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</td>
                <td style="width: 110px;">Generate</td>
                <td>: {{ $generatedAt->format('d M Y H:i') }}</td>
            </tr>
            <tr>
                <td>Oleh</td>
                <td>: {{ $generatedBy }}</td>
                <td>Total Data</td>
                <td>: {{ $rows->count() }}</td>
            </tr>
        </table>
    </div>

    @if ($type === 'financial')
        <table class="summary">
            @foreach ($summary as $label => $value)
                <tr>
                    <td>{{ $label }}</td>
                    <td class="right bold">Rp {{ number_format($value, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>

        <table class="data">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Subtotal</th>
                    <th>Diskon</th>
                    <th>Total</th>
                    <th>Kasir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $sale)
                    <tr>
                        <td>{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->sale_date->format('d M Y H:i') }}</td>
                        <td>{{ $sale->customer?->name ?? 'Walk-in Customer' }}</td>
                        <td class="right">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                        <td class="right">Rp {{ number_format($sale->discount_total, 0, ',', '.') }}</td>
                        <td class="right bold">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                        <td>{{ $sale->cashier?->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif ($type === 'sales')
        <table class="data">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Metode</th>
                    <th>Item</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $sale)
                    <tr>
                        <td>{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->sale_date->format('d M Y H:i') }}</td>
                        <td>{{ $sale->customer?->name ?? 'Walk-in Customer' }}</td>
                        <td>{{ strtoupper($sale->payment_method) }}</td>
                        <td>{{ $sale->items->count() }}</td>
                        <td class="right bold">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif ($type === 'inventory')
        <table class="data">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>SKU</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th>Stok</th>
                    <th>Minimum</th>
                    <th>Avg Cost</th>
                    <th>Harga Jual</th>
                    <th>Nilai Stok</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->category?->name ?? '-' }}</td>
                        <td>{{ $product->unit?->abbreviation ?? '-' }}</td>
                        <td class="right">{{ number_format($product->stock, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($product->minimum_stock, 0, ',', '.') }}</td>
                        <td class="right">Rp {{ number_format($product->average_cost, 0, ',', '.') }}</td>
                        <td class="right">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                        <td class="right bold">Rp {{ number_format($product->stock * $product->average_cost, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif ($type === 'stock-movements')
        <table class="data">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Tipe</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Stok Awal</th>
                    <th>Stok Akhir</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $movement->product?->name ?? '-' }}</td>
                        <td>{{ str_replace('_', ' ', $movement->type) }}</td>
                        <td class="right">{{ number_format($movement->quantity_in, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($movement->quantity_out, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($movement->stock_before, 0, ',', '.') }}</td>
                        <td class="right bold">{{ number_format($movement->stock_after, 0, ',', '.') }}</td>
                        <td>{{ $movement->creator?->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Event</th>
                    <th>Aktivitas</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $log->event }}</td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->user?->name ?? 'Public' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dokumen ini digenerate otomatis dari sistem POS Ari Gita.
    </div>
</body>
</html>
