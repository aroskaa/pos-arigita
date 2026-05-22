<x-pos-layout>
    <x-slot name="title">
        {{ $sale->invoice_number }}
    </x-slot>

    <x-slot name="header">
        Detail Invoice
    </x-slot>

    <div class="mx-auto max-w-5xl">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900">
                        {{ $sale->invoice_number }}
                    </h2>

                    <p class="mt-2 text-sm text-slate-500">
                        {{ $sale->sale_date->format('d M Y H:i') }}
                    </p>
                </div>

                <div class="text-left lg:text-right">
                    <p class="text-sm text-slate-500">
                        Kasir
                    </p>

                    <h3 class="mt-1 text-lg font-bold text-slate-900">
                        {{ $sale->cashier->name }}
                    </h3>
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

                            <th class="px-4 py-3 text-right text-sm font-semibold text-slate-600">
                                Subtotal
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($sale->items as $item)
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-4">
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

                    <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                        <span class="text-lg font-bold text-slate-900">
                            Grand Total
                        </span>

                        <span class="text-2xl font-bold text-blue-700">
                            Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap items-center gap-3">
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
            </div>
        </div>
    </div>
</x-pos-layout>