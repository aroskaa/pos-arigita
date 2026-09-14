@php
    use App\Models\Setting;

    $companyName    = Setting::get('company_name',    'CV Ari Gita Grosir');
    $companyTagline = Setting::get('company_tagline', 'Grosir Minuman');
    $companyPhone   = Setting::get('company_phone',   '');
    $companyEmail   = Setting::get('company_email',   '');
    $companyAddress = Setting::get('company_address', '');
    $companyCity    = Setting::get('company_city',    '');

    $bankName       = Setting::get('invoice_bank_name',    'BCA');
    $bankAccount    = Setting::get('invoice_bank_account', '');
    $bankHolder     = Setting::get('invoice_bank_holder',  $companyName);
    $invoiceTerms   = Setting::get('invoice_terms',        '');
    $invoiceFooter  = Setting::get('invoice_footer_note',  'Dokumen invoice ini sah dan diproses secara terkomputerisasi.');

    $baseSubtotal     = $sale->items->sum(fn ($i) => (int) $i->quantity * (float) ($i->base_price ?: $i->unit_price));
    $totalTierSavings = $sale->items->sum(fn ($i) => $i->tier_savings ?? 0);

    // Pre-formatted strings for @json() usage in JS
    $fmtSubtotal      = 'Rp ' . number_format($baseSubtotal, 0, ',', '.');
    $fmtTierSavings   = $totalTierSavings > 0 ? 'Rp ' . number_format($totalTierSavings, 0, ',', '.') : '';
    $fmtDiscount      = $sale->discount_total > 0 ? 'Rp ' . number_format($sale->discount_total, 0, ',', '.') : '';
    $fmtGrandTotal    = 'Rp ' . number_format($sale->grand_total, 0, ',', '.');
    $fmtPaid          = 'Rp ' . number_format($sale->paid_amount, 0, ',', '.');
    $fmtChange        = 'Rp ' . number_format($sale->change_amount, 0, ',', '.');
    $fmtSaleDate      = $sale->sale_date->format('d F Y');
    $fmtSaleTime      = $sale->sale_date->format('H:i');
    $fmtPaymentMethod = strtoupper($sale->payment_method);
    $customerName     = $sale->customer?->name ?? 'Walk-in Customer';
    $customerPhone    = $sale->customer?->phone ?? '';
    $customerAddr     = $sale->customer?->address ?? '';
    $cashierName      = $sale->cashier->name;
@endphp

<x-pos-layout>
    <x-slot name="title">
        {{ $sale->invoice_number }}
    </x-slot>

    <x-slot name="header">
        Detail Invoice
    </x-slot>

    <div class="mx-auto max-w-5xl" x-data="{ showPreviewModal: false }">
        <div class="print-invoice rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <div>
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-900">
                            {{ $sale->invoice_number }}
                        </h2>

                        <p class="mt-2 text-sm text-slate-500">
                            {{ $sale->sale_date->format('d M Y H:i') }}
                        </p>
                    </div>

                    @if ($sale->customerOrder)
                        <div class="w-full rounded-2xl bg-blue-50 px-4 py-3 text-left text-sm text-blue-700 md:w-auto md:max-w-sm md:text-right">
                            <p class="font-semibold">
                                Website Order Pelanggan
                            </p>

                            <p class="mt-1">
                                Nomor Order:
                                <span class="font-bold">
                                    {{ $sale->customerOrder->order_number }}
                                </span>
                            </p>
                        </div>
                    @endif
                </div>

                @if ($sale->status === 'cancelled')
                    <div class="mt-4 rounded-2xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                        Invoice ini telah dibatalkan.

                        @if ($sale->cancel_note)
                            <span class="mt-1 block font-normal">
                                Alasan: {{ $sale->cancel_note }}
                            </span>
                        @endif

                        @if ($sale->canceller || $sale->cancelled_at)
                            <span class="mt-1 block font-bold">
                                Dibatalkan oleh {{ $sale->canceller?->name ?? '-' }}
                                pada {{ $sale->cancelled_at?->format('d M Y H:i') ?? '-' }}.
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Customer
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-900">
                        {{ $sale->customer?->name ?? 'Walk-in Customer' }}
                    </p>

                    @if($sale->customer?->phone)
                        <p class="no-print text-xs text-slate-500">
                            {{ $sale->customer->phone }}
                        </p>
                    @endif

                    @if($sale->customer?->address)
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $sale->customer->address }}
                        </p>
                    @endif
                </div>

                <div class="md:text-right">
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Kasir
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-900">
                        {{ $sale->cashier->name }}
                    </p>

                    <p class="text-xs text-slate-500">
                        {{ $sale->sale_date->format('d M Y H:i') }}
                    </p>

                    <p class="mt-1 text-xs font-semibold uppercase text-slate-500">
                        {{ strtoupper($sale->payment_method) }}
                    </p>
                </div>
            </div>

            <div class="mt-8 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">
                                Produk
                            </th>

                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">
                                Qty
                            </th>

                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">
                                Harga
                            </th>

                            <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">
                                Diskon
                            </th>

                            <th class="px-4 py-3 text-right text-sm font-semibold text-slate-600">
                                Subtotal
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($sale->items as $item)
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3 text-sm">
                                    <div>
                                        <p class="font-semibold text-slate-900">
                                            {{ $item->product->name }}
                                        </p>

                                        <p class="text-xs text-slate-500">
                                            {{ $item->product->sku }}
                                        </p>
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-sm text-slate-600">
                                    {{ number_format($item->quantity, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-4 text-sm text-slate-600">
                                    @if ((float) $item->base_price > (float) $item->unit_price)
                                        <span class="line-through text-slate-400 text-xs block">Rp {{ number_format($item->base_price, 0, ',', '.') }}</span>
                                        Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-sm text-red-600">
                                    @if ($item->discount_amount > 0)
                                        - Rp {{ number_format($item->discount_amount, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-right font-bold text-slate-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-end">
                <div class="w-full max-w-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-500">
                            Subtotal
                        </span>

                        <span class="font-semibold text-slate-900">
                            Rp {{ number_format($sale->subtotal, 0, ',', '.') }}
                        </span>
                    </div>

                    @if ($totalTierSavings > 0)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">Potongan Grosir</span>
                            <span class="font-semibold text-emerald-600">- Rp {{ number_format($totalTierSavings, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if ($sale->discount_total > 0)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">
                                Diskon
                            </span>

                            <span class="font-semibold text-red-600">
                                - Rp {{ number_format($sale->discount_total, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                        <span class="text-lg font-bold text-slate-900">
                            Grand Total
                        </span>

                        <span class="{{ $sale->status === 'cancelled' ? 'text-red-600 line-through' : 'text-slate-900' }}">
                            Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                        </span>
                    </div>

                    @if ($sale->status === 'cancelled')
                        <p class="mt-2 text-xs font-semibold text-red-600">
                            Nominal invoice ini tidak dihitung sebagai pendapatan aktif karena transaksi telah dibatalkan.
                        </p>
                    @endif
                </div>
            </div>

            <div class="no-print mt-8 flex flex-wrap items-center gap-3">
                <button
                    onclick="window.print()"
                    class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Print Invoice
                </button>

                <a
                    href="{{ route('sales.index') }}"
                    class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                >
                    Kembali
                </a>

                <button
                    type="button"
                    @click="showPreviewModal = true"
                    class="rounded-2xl bg-blue-50 px-5 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-100 transition"
                >
                    Preview Struk
                </button>

                <button
                    type="button"
                    onclick="printReceipt('{{ route('sales.receipt', ['sale' => $sale, 'is_copy' => 1]) }}')"
                    class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800 transition"
                >
                    Print Struk
                </button>
            </div>
        </div>

        {{-- RECEIPT PREVIEW MODAL --}}
        <div
            x-show="showPreviewModal"
            x-cloak
            x-data="{
                paperSize: localStorage.getItem('pos_receipt_paper_size') || '58',
                baseUrl: '{{ route('sales.receipt', ['sale' => $sale, 'is_copy' => 1, 'embed' => 1]) }}',
                printUrl: '{{ route('sales.receipt', ['sale' => $sale, 'is_copy' => 1]) }}',
                setPaper(size) {
                    this.paperSize = size;
                    localStorage.setItem('pos_receipt_paper_size', size);
                },
                get iframeSrc() {
                    return this.baseUrl + '&paper=' + this.paperSize;
                }
            }"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs no-print"
        >
            <div
                @click.away="showPreviewModal = false"
                class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl transition-all"
            >
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            Preview Struk Penjualan
                        </h3>
                        <p class="text-xs text-slate-500">
                            Tampilan thermal receipt (<span x-text="paperSize + 'mm'"></span>)
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="showPreviewModal = false"
                        class="rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-500 hover:bg-slate-200"
                    >
                        ✕
                    </button>
                </div>

                <div class="border-b border-slate-100 bg-slate-50 px-6 py-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-600">Ukuran Kertas:</span>
                        <div class="flex rounded-xl bg-slate-200/80 p-1">
                            <button
                                type="button"
                                @click="setPaper('58')"
                                :class="paperSize === '58' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 font-medium'"
                                class="rounded-lg px-3 py-1 text-xs transition"
                            >
                                58mm (Kecil)
                            </button>
                            <button
                                type="button"
                                @click="setPaper('80')"
                                :class="paperSize === '80' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 font-medium'"
                                class="rounded-lg px-3 py-1 text-xs transition"
                            >
                                80mm (Standar)
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center bg-slate-100/60 p-4 max-h-[65vh] overflow-y-auto">
                    <iframe
                        :src="iframeSrc"
                        :class="paperSize === '58' ? 'w-[64mm]' : 'w-[86mm]'"
                        class="h-[460px] border-0 bg-transparent transition-all"
                    ></iframe>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-4">
                    <button
                        type="button"
                        @click="showPreviewModal = false"
                        class="rounded-2xl bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                    >
                        Tutup
                    </button>

                    <button
                        type="button"
                        @click="printReceipt(printUrl, paperSize)"
                        class="rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PRINT STYLES: Formal landscape invoice when window.print() is called ── --}}
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 14mm 16mm;
            }

            aside, header, nav, .no-print {
                display: none !important;
            }

            body, html {
                background: #ffffff !important;
            }

            main, section {
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Reset the outer wrapper so invoice fills the page */
            .mx-auto {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .print-invoice {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
            }

            /* ── INVOICE HEADER ── */
            .print-invoice::before {
                content: '';
                display: block;
                width: 100%;
                height: 6px;
                background: linear-gradient(90deg, #1d4ed8 0%, #3b82f6 60%, #60a5fa 100%);
                margin-bottom: 20px;
                border-radius: 3px;
            }

            /* Company name + invoice number top strip */
            .print-header-company {
                display: flex !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
                margin-bottom: 20px;
                padding-bottom: 16px;
                border-bottom: 1.5px solid #e2e8f0;
            }

            .print-header-company .company-block .company-name {
                font-size: 18pt !important;
                font-weight: 900 !important;
                color: #0f172a !important;
                letter-spacing: -0.5px;
            }

            .print-header-company .company-block .company-sub {
                font-size: 9pt !important;
                color: #64748b !important;
                margin-top: 2px;
            }

            .print-header-company .invoice-block {
                text-align: right;
            }

            .print-header-company .invoice-block .invoice-label {
                font-size: 22pt !important;
                font-weight: 900 !important;
                color: #1d4ed8 !important;
                letter-spacing: 3px;
                text-transform: uppercase;
            }

            .print-header-company .invoice-block .invoice-num {
                font-size: 11pt !important;
                font-weight: 700 !important;
                color: #1e293b !important;
                margin-top: 2px;
            }

            .print-header-company .invoice-block .invoice-date {
                font-size: 9pt !important;
                color: #64748b !important;
            }
        }
    </style>

    {{-- Hidden print-only header block injected before invoice content --}}
    <script>
        window.addEventListener('beforeprint', function () {
            // Inject a formal header above .print-invoice during print
            var existing = document.getElementById('_print_invoice_header');
            if (existing) existing.remove();

            var companyName    = @json($companyName);
            var companyTagline = @json($companyTagline);
            var companyAddress = @json($companyAddress);
            var companyCity    = @json($companyCity);
            var companyPhone   = @json($companyPhone);
            var companyEmail   = @json($companyEmail);
            var bankName       = @json($bankName);
            var bankAccount    = @json($bankAccount);
            var bankHolder     = @json($bankHolder);
            var invoiceTerms   = @json($invoiceTerms);
            var invoiceFooter  = @json($invoiceFooter);
            var invoiceNum     = @json($sale->invoice_number);
            var invoiceDate    = @json($fmtSaleDate);
            var invoiceTime    = @json($fmtSaleTime);
            var paymentMethod  = @json($fmtPaymentMethod);
            var status         = @json($sale->status);
            var customerName   = @json($customerName);
            var customerPhone  = @json($customerPhone);
            var customerAddr   = @json($customerAddr);
            var cashierName    = @json($cashierName);
            var subtotal       = @json($fmtSubtotal);
            var tierSavings    = @json($fmtTierSavings);
            var discount       = @json($fmtDiscount);
            var grandTotal     = @json($fmtGrandTotal);
            var paid           = @json($fmtPaid);
            var change         = @json($fmtChange);

            var addressParts = [companyAddress, companyCity].filter(Boolean).join(', ');
            var contactParts = [companyPhone ? '📞 ' + companyPhone : '', companyEmail ? '✉ ' + companyEmail : ''].filter(Boolean).join('   ');

            var bankHtml = bankAccount ? `
                <tr><td style="color:#64748b;font-size:8pt;padding:2px 8px 2px 0;width:100px">Bank</td><td style="font-size:8.5pt;font-weight:700;color:#1e293b">${bankName}</td></tr>
                <tr><td style="color:#64748b;font-size:8pt;padding:2px 8px 2px 0">No. Rekening</td><td style="font-size:9pt;font-weight:900;color:#1d4ed8;letter-spacing:1px">${bankAccount}</td></tr>
                <tr><td style="color:#64748b;font-size:8pt;padding:2px 8px 2px 0">Atas Nama</td><td style="font-size:8.5pt;font-weight:700;color:#1e293b">${bankHolder}</td></tr>
            ` : '';

            var termsHtml = invoiceTerms ? `<div style="margin-top:8px;padding:6px 8px;border:1px solid #fcd34d;background:#fffbeb;border-radius:6px"><p style="margin:0;font-size:7.5pt;color:#92400e;font-weight:700;text-transform:uppercase;letter-spacing:0.5px">Ketentuan</p><p style="margin:2px 0 0;font-size:7.5pt;color:#78350f;line-height:1.4">${invoiceTerms}</p></div>` : '';

            var tierRow    = tierSavings   ? `<tr><td style="padding:1.5px 0;font-size:9pt;color:#64748b">Potongan Grosir</td><td style="text-align:right;font-weight:600;font-size:9pt;color:#059669">- ${tierSavings}</td></tr>` : '';
            var discRow    = discount      ? `<tr><td style="padding:1.5px 0;font-size:9pt;color:#64748b">Diskon</td><td style="text-align:right;font-weight:600;font-size:9pt;color:#dc2626">- ${discount}</td></tr>` : '';
            var cancelledStyle = status === 'cancelled' ? 'text-decoration:line-through;color:#dc2626' : 'color:#0f172a';
            var logoSrc    = @json(asset('images/logo-ag.png'));

            var html = `
            <div id="_print_invoice_header" style="font-family:'Segoe UI',Arial,sans-serif;margin-bottom:0;-webkit-print-color-adjust:exact;print-color-adjust:exact">

                <!-- Top rule -->
                <div style="height:5px;background:linear-gradient(90deg,#1d4ed8,#60a5fa);border-radius:3px;margin-bottom:18px"></div>

                <!-- Company + Invoice Title Row -->
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;padding-bottom:14px;border-bottom:1.5px solid #e2e8f0">
                    <div style="display:flex;align-items:center;gap:12px">
                        <img src="${logoSrc}" alt="Logo" style="height:48px;width:auto;object-fit:contain;flex-shrink:0">
                        <div>
                            <div style="font-size:17pt;font-weight:900;color:#0f172a;letter-spacing:-0.5px;line-height:1.1">${companyName}</div>
                            ${companyTagline ? `<div style="font-size:9pt;color:#64748b;margin-top:2px">${companyTagline}</div>` : ''}
                            ${addressParts ? `<div style="font-size:8pt;color:#94a3b8;margin-top:4px">${addressParts}</div>` : ''}
                            ${contactParts ? `<div style="font-size:8pt;color:#94a3b8;margin-top:1px">${contactParts}</div>` : ''}
                        </div>
                    </div>
                    <div style="text-align:right">
                        <div style="font-size:24pt;font-weight:900;color:#1d4ed8;letter-spacing:4px;text-transform:uppercase;line-height:1">INVOICE</div>
                        <div style="font-size:11pt;font-weight:800;color:#1e293b;margin-top:4px">${invoiceNum}</div>
                        <div style="font-size:9pt;color:#64748b;margin-top:2px">${invoiceDate} · ${invoiceTime} WIB</div>
                        <div style="margin-top:4px">
                            <span style="display:inline-block;padding:2px 8px;background:#f1f5f9;border-radius:5px;font-size:8pt;font-weight:700;color:#334155;text-transform:uppercase">${paymentMethod}</span>
                            ${status === 'cancelled'
                                ? '<span style="display:inline-block;margin-left:4px;padding:2px 8px;background:#fee2e2;border-radius:5px;font-size:8pt;font-weight:700;color:#b91c1c">DIBATALKAN</span>'
                                : '<span style="display:inline-block;margin-left:4px;padding:2px 8px;background:#d1fae5;border-radius:5px;font-size:8pt;font-weight:700;color:#065f46">LUNAS</span>'}
                        </div>
                    </div>
                </div>

                <!-- Bill To + Cashier -->
                <div style="display:flex;justify-content:space-between;margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid #f1f5f9">
                    <div>
                        <div style="font-size:7.5pt;font-weight:900;text-transform:uppercase;letter-spacing:1px;color:#94a3b8;margin-bottom:4px">Kepada Yth.</div>
                        <div style="font-size:11pt;font-weight:800;color:#0f172a">${customerName}</div>
                        ${customerPhone ? `<div style="font-size:8.5pt;color:#64748b;margin-top:2px">${customerPhone}</div>` : ''}
                        ${customerAddr  ? `<div style="font-size:8pt;color:#94a3b8;margin-top:2px;max-width:220px">${customerAddr}</div>` : ''}
                    </div>
                    <div style="text-align:right">
                        <div style="font-size:7.5pt;font-weight:900;text-transform:uppercase;letter-spacing:1px;color:#94a3b8;margin-bottom:4px">Kasir / Sales</div>
                        <div style="font-size:11pt;font-weight:800;color:#0f172a">${cashierName}</div>
                    </div>
                </div>
            </div>`;

            // Footer / bank summary block (appended after table)
            var footerHtml = `
            <div id="_print_invoice_footer" style="font-family:'Segoe UI',Arial,sans-serif;display:flex;justify-content:space-between;align-items:flex-start;margin-top:16px;gap:24px;-webkit-print-color-adjust:exact;print-color-adjust:exact">
                <!-- Bank info -->
                <div style="min-width:200px">
                    ${bankAccount ? `
                        <div style="font-size:7.5pt;font-weight:900;text-transform:uppercase;letter-spacing:1px;color:#94a3b8;margin-bottom:6px">Informasi Pembayaran</div>
                        <div style="padding:10px 12px;border:1px solid #e2e8f0;background:#f8fafc;border-radius:8px">
                            <table style="border-collapse:collapse">${bankHtml}</table>
                        </div>
                        ${termsHtml}
                    ` : ''}
                </div>

                <!-- Totals -->
                <div style="min-width:240px">
                    <table style="width:100%;border-collapse:collapse;font-size:9.5pt">
                        <tr><td style="padding:2px 0;color:#64748b">Subtotal</td><td style="text-align:right;font-weight:600;color:#1e293b">${subtotal}</td></tr>
                        ${tierRow}
                        ${discRow}
                        <tr><td colspan="2" style="border-top:2px solid #1e293b;padding-top:6px"></td></tr>
                        <tr>
                            <td style="font-size:12pt;font-weight:900;color:#0f172a;padding-bottom:6px">Total</td>
                            <td style="text-align:right;font-size:14pt;font-weight:900;${cancelledStyle}">${grandTotal}</td>
                        </tr>
                        <tr style="background:#f8fafc"><td style="padding:3px 6px;font-size:8.5pt;color:#64748b;border-radius:4px">Dibayar</td><td style="text-align:right;padding:3px 6px;font-size:8.5pt;font-weight:600;color:#1e293b">${paid}</td></tr>
                        <tr style="background:#f8fafc"><td style="padding:3px 6px;font-size:8.5pt;color:#64748b">Kembalian</td><td style="text-align:right;padding:3px 6px;font-size:8.5pt;font-weight:600;color:#1e293b">${change}</td></tr>
                    </table>
                </div>
            </div>

            <!-- Footer note -->
            <div style="margin-top:16px;padding-top:10px;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center">
                <p style="margin:0;font-size:7.5pt;color:#94a3b8;font-style:italic;max-width:500px">${invoiceFooter}</p>
                <p style="margin:0;font-size:7.5pt;color:#cbd5e1">Dicetak: ${new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'})} ${new Date().toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})}</p>
            </div>`;

            var invoice = document.querySelector('.print-invoice');
            if (invoice) {
                invoice.insertAdjacentHTML('afterbegin', html);
                invoice.insertAdjacentHTML('beforeend', footerHtml);
            }
        });

        window.addEventListener('afterprint', function () {
            var h = document.getElementById('_print_invoice_header');
            var f = document.getElementById('_print_invoice_footer');
            if (h) h.remove();
            if (f) f.remove();
        });
    </script>

</x-pos-layout>
