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

                {{-- cashier redundant name display --}}
            </div>
            
                {{-- <div class="text-left lg:text-right">
                    <p class="text-sm text-slate-500">
                        Kasir
                    </p>

                    <h3 class="mt-1 text-lg font-bold text-slate-900">
                        {{ $sale->cashier->name }}
                    </h3>
                </div> --}}

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
                                    Rp {{ number_format($item->unit_price, 0, ',', '.') }}
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
                            Tampilan fisik thermal receipt (80mm)
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

                <div class="flex justify-center bg-slate-50/80 p-5 max-h-[70vh] overflow-y-auto">
                    <iframe
                        src="{{ route('sales.receipt', ['sale' => $sale, 'is_copy' => 1, 'embed' => 1]) }}"
                        class="h-[460px] w-[86mm] border-0 bg-transparent"
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
                        onclick="printReceipt('{{ route('sales.receipt', ['sale' => $sale, 'is_copy' => 1]) }}')"
                        class="rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            aside, header, .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            main, section {
                padding: 0 !important;
            }

            .print-invoice {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 24px !important;
            }

            .print-text-sm {
                font-size: 12px !important;
            }

            .print-title {
                font-size: 28px !important;
            }
        }
    </style>

</x-pos-layout>
