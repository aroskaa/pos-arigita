<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk {{ $sale->invoice_number }}</title>

    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f1f5f9;
            font-family: "Courier New", monospace;
            color: #111827;
        }

        .page-wrapper {
            min-height: 100vh;
            padding: 24px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .receipt {
            width: 80mm;
            max-width: 80mm;
            background: #ffffff;
            padding: 10px 8px;
            font-size: 11px;
            line-height: 1.35;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: 700;
        }

        .store-name {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .store-subtitle {
            margin-top: 2px;
            font-size: 10px;
        }

        .divider {
            margin: 7px 0;
            border-top: 1px dashed #111827;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .row span:first-child {
            flex: 1;
        }

        .row span:last-child {
            text-align: right;
            white-space: nowrap;
        }

        .meta-row {
            display: flex;
            gap: 4px;
        }

        .meta-label {
            width: 58px;
            flex-shrink: 0;
        }

        .meta-value {
            flex: 1;
            word-break: break-word;
        }

        .item {
            margin-bottom: 6px;
        }

        .item-name {
            font-weight: 700;
            word-break: break-word;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-top: 3px;
        }

        .summary-row.total {
            font-size: 13px;
            font-weight: 700;
        }

        .cancelled-box {
            margin-top: 8px;
            padding: 6px;
            border: 1px dashed #991b1b;
            color: #991b1b;
            text-align: center;
            font-weight: 700;
        }

        .footer {
            margin-top: 8px;
            text-align: center;
            font-size: 10px;
        }

        .dev-actions {
            width: 80mm;
            max-width: 80mm;
            margin-top: 16px;
            display: flex;
            gap: 8px;
        }

        .dev-actions a,
        .dev-actions button {
            flex: 1;
            border: none;
            border-radius: 12px;
            padding: 10px 12px;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .page-wrapper {
                display: block;
                min-height: auto;
                padding: 0;
            }

            .receipt {
                width: 80mm;
                max-width: 80mm;
                box-shadow: none;
                padding: 0 6px;
            }

            .dev-actions {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div>
            <div class="receipt">
                <div class="center">
                    <div class="store-name">CV Ari Gita Grosir</div>
                    <div class="store-subtitle">Grosir Minuman</div>
                    <div class="store-subtitle">Struk Transaksi Penjualan</div>
                </div>

                <div class="divider"></div>

                <div class="meta-row">
                    <div class="meta-label">Invoice</div>
                    <div class="meta-value">: {{ $sale->invoice_number }}</div>
                </div>

                @if ($sale->customerOrder)
                    <div class="meta-row">
                        <div class="meta-label">Order</div>
                        <div class="meta-value">: {{ $sale->customerOrder->order_number }}</div>
                    </div>
                @endif

                <div class="meta-row">
                    <div class="meta-label">Tanggal</div>
                    <div class="meta-value">: {{ $sale->sale_date->format('d/m/Y H:i') }}</div>
                </div>

                <div class="meta-row">
                    <div class="meta-label">Kasir</div>
                    <div class="meta-value">: {{ $sale->cashier?->name ?? '-' }}</div>
                </div>

                <div class="meta-row">
                    <div class="meta-label">Customer</div>
                    <div class="meta-value">: {{ $sale->customer?->name ?? 'Walk-in Customer' }}</div>
                </div>

                <div class="divider"></div>

                @foreach ($sale->items as $item)
                    <div class="item">
                        <div class="item-name">
                            {{ $item->product?->name ?? 'Produk tidak ditemukan' }}
                        </div>

                        <div class="item-detail">
                            <span>
                                {{ number_format($item->quantity, 0, ',', '.') }}
                                x
                                {{ number_format($item->unit_price, 0, ',', '.') }}
                            </span>

                            <span>
                                {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach

                <div class="divider"></div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
                </div>

                @if ($sale->discount_total > 0)
                    <div class="summary-row">
                        <span>Diskon</span>
                        <span>- Rp {{ number_format($sale->discount_total, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="summary-row total">
                    <span>Total</span>
                    <span>Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
                </div>

                <div class="summary-row">
                    <span>Bayar</span>
                    <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
                </div>

                <div class="summary-row">
                    <span>Kembali</span>
                    <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                </div>

                <div class="summary-row">
                    <span>Metode</span>
                    <span>{{ strtoupper($sale->payment_method) }}</span>
                </div>

                @if ($sale->status === 'cancelled')
                    <div class="cancelled-box">
                        TRANSAKSI DIBATALKAN
                    </div>

                    @if ($sale->cancel_note)
                        <div class="footer">
                            Alasan: {{ $sale->cancel_note }}
                        </div>
                    @endif
                @endif

                <div class="divider"></div>

                <div class="footer">
                    Terima kasih atas pembelian Anda.
                    <br>
                    Barang yang sudah dibeli harap diperiksa kembali.
                    <br>
                    {{ now()->format('d/m/Y H:i:s') }}
                </div>
            </div>

            <div class="dev-actions">
                <a
                    href="{{ route('sales.show', $sale) }}"
                    class="btn-secondary"
                >
                    Kembali
                </a>

                <button
                    type="button"
                    class="btn-primary"
                    onclick="window.print()"
                >
                    Preview Print
                </button>
            </div>
        </div>
    </div>
</body>
</html>