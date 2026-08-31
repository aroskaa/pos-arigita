@php
    $paperSize = $paperSize ?? request('paper', request('size', '58'));
    $paperSize = in_array((string) $paperSize, ['58', '80'], true) ? (string) $paperSize : '58';
    $is58 = ($paperSize === '58');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $sale->invoice_number }}</title>

    <style>
        @page {
            size: {{ $is58 ? '58mm auto' : '80mm auto' }};
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #f1f5f9;
            font-family: 'Consolas', 'Courier New', Courier, monospace;
            color: #000000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @if (request()->boolean('embed'))
            body {
                background: transparent !important;
            }

            .page-wrapper {
                min-height: auto !important;
                padding: 4px 0 !important;
                background: transparent !important;
            }

            .receipt {
                border-radius: 8px;
                box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.1) !important;
            }
        @endif

        .page-wrapper {
            min-height: 100vh;
            padding: 16px 8px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .receipt-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: {{ $is58 ? '58mm' : '80mm' }};
            max-width: 100%;
        }

        .receipt {
            width: {{ $is58 ? '58mm' : '80mm' }};
            max-width: {{ $is58 ? '58mm' : '80mm' }};
            background: #ffffff;
            padding: {{ $is58 ? '8px 5px' : '10px 8px' }};
            font-size: {{ $is58 ? '9.5px' : '11px' }};
            line-height: {{ $is58 ? '1.25' : '1.35' }};
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
            word-break: break-word;
            overflow-wrap: break-word;
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
            font-size: {{ $is58 ? '12.5px' : '15px' }};
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.2;
            letter-spacing: -0.2px;
        }

        .store-subtitle {
            margin-top: 2px;
            font-size: {{ $is58 ? '8.5px' : '10px' }};
            color: #1f2937;
        }

        .divider {
            margin: {{ $is58 ? '4px 0' : '6px 0' }};
            border-top: 1px dashed #000000;
        }

        .meta-row {
            display: flex;
            gap: 2px;
            font-size: {{ $is58 ? '9px' : '10.5px' }};
            line-height: {{ $is58 ? '1.25' : '1.35' }};
        }

        .meta-label {
            width: {{ $is58 ? '46px' : '56px' }};
            flex-shrink: 0;
        }

        .meta-value {
            flex: 1;
            min-width: 0;
            word-break: break-word;
        }

        .item {
            margin-bottom: {{ $is58 ? '4px' : '6px' }};
        }

        .item-name {
            font-weight: 700;
            font-size: {{ $is58 ? '9.5px' : '11px' }};
            word-break: break-word;
            line-height: 1.2;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            gap: 4px;
            font-size: {{ $is58 ? '9px' : '10.5px' }};
        }

        .item-detail span:last-child {
            text-align: right;
            white-space: nowrap;
            font-weight: 600;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 4px;
            margin-top: 1.5px;
            font-size: {{ $is58 ? '9px' : '10.5px' }};
        }

        .summary-row span:last-child {
            text-align: right;
            white-space: nowrap;
        }

        .summary-row.total {
            font-size: {{ $is58 ? '11px' : '12.5px' }};
            font-weight: 700;
            margin-top: 2px;
            margin-bottom: 2px;
        }

        .cancelled-box {
            margin-top: 6px;
            padding: 4px;
            border: 1px dashed #991b1b;
            color: #991b1b;
            text-align: center;
            font-weight: 700;
            font-size: {{ $is58 ? '9.5px' : '11px' }};
        }

        .footer {
            margin-top: 5px;
            text-align: center;
            font-size: {{ $is58 ? '8.5px' : '9.5px' }};
            line-height: 1.25;
            word-break: break-word;
        }

        .dev-actions {
            width: 100%;
            margin-top: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .paper-switcher {
            display: flex;
            background: #e2e8f0;
            padding: 3px;
            border-radius: 10px;
            gap: 3px;
        }

        .paper-btn {
            flex: 1;
            border: none;
            background: transparent;
            padding: 6px 8px;
            border-radius: 7px;
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .paper-btn.active {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .action-buttons {
            display: flex;
            gap: 6px;
        }

        .action-buttons a,
        .action-buttons button {
            flex: 1;
            border: none;
            border-radius: 10px;
            padding: 8px 10px;
            font-family: Arial, sans-serif;
            font-size: 11px;
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

        .print-tip {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 10px;
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #64748b;
            line-height: 1.4;
        }

        .print-tip strong {
            color: #1e293b;
        }

        @media print {
            @page {
                size: {{ $is58 ? '58mm auto' : '80mm auto' }};
                margin: 0;
            }

            html, body {
                width: {{ $is58 ? '58mm' : '80mm' }} !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }

            .page-wrapper {
                display: block !important;
                min-height: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            .receipt-wrapper {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .receipt {
                width: 100% !important;
                max-width: {{ $is58 ? '58mm' : '80mm' }} !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: {{ $is58 ? '2px 4px' : '4px 6px' }} !important;
            }

            .dev-actions,
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="receipt-wrapper">
            <div class="receipt">
                <div class="center">
                    <img src="{{ asset('images/logo-ag.png') }}" alt="Logo" style="width: 32px; height: auto; margin-bottom: 2px; display: inline-block;">
                    <div class="store-name">CV Ari Gita Grosir</div>
                    @if (request()->boolean('is_copy'))
                        <div class="store-subtitle bold" style="font-weight: 700; font-size: {{ $is58 ? '9px' : '10.5px' }}; margin-top: 2px;">[ STRUK COPY ]</div>
                    @endif
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
                                {{ number_format(($item->quantity * $item->unit_price), 0, ',', '.') }}
                            </span>
                        </div>

                        @if ($item->discount_amount > 0)
                            <div class="item-detail">
                                <span>Diskon item</span>
                                <span>- {{ number_format($item->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
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

            @if (!request()->boolean('embed'))
                <div class="dev-actions">
                    <div class="paper-switcher">
                        <button
                            type="button"
                            onclick="setPaperSize('58')"
                            class="paper-btn {{ $is58 ? 'active' : '' }}"
                        >
                            Ukuran 58mm (Kecil)
                        </button>

                        <button
                            type="button"
                            onclick="setPaperSize('80')"
                            class="paper-btn {{ !$is58 ? 'active' : '' }}"
                        >
                            Ukuran 80mm (Standar)
                        </button>
                    </div>

                    <div class="action-buttons">
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

                    <div class="print-tip">
                        <strong>💡 Tips Cetak Pas ke Kertas:</strong><br>
                        1. Margins: pilih <strong>None (Tanpa Margin)</strong><br>
                        2. Paper Size: pilih <strong>58mm</strong> / <strong>58 x 210mm</strong><br>
                        3. Scale: pilih <strong>100% / Fit to printable area</strong><br>
                        4. Options: uncheck <strong>Headers and Footers</strong>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function setPaperSize(size) {
            localStorage.setItem('pos_receipt_paper_size', size);
            const url = new URL(window.location.href);
            url.searchParams.set('paper', size);
            window.location.href = url.toString();
        }

        // Auto sync paper preference if not specified in URL query
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (!urlParams.has('paper') && !urlParams.has('size')) {
                const savedSize = localStorage.getItem('pos_receipt_paper_size');
                if (savedSize && savedSize !== '{{ $paperSize }}') {
                    const url = new URL(window.location.href);
                    url.searchParams.set('paper', savedSize);
                    window.location.replace(url.toString());
                }
            }
        })();
    </script>

    @if (request()->boolean('print'))
        <script>
            window.addEventListener('load', () => {
                const detailUrl = @json(route('sales.show', $sale));
                let redirected = false;

                const redirectToDetail = () => {
                    if (redirected || @json(request('redirect') !== 'detail')) {
                        return;
                    }

                    redirected = true;
                    window.location.href = detailUrl;
                };

                window.addEventListener('afterprint', redirectToDetail, { once: true });
                window.print();
                setTimeout(redirectToDetail, 1000);
            });
        </script>
    @endif
</body>
</html>
