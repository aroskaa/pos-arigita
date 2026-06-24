<div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="rounded-3xl bg-blue-600 p-6 text-white shadow-lg">
            <p class="text-sm font-medium text-blue-100">
                Transaksi Hari Ini
            </p>

            <h3 class="mt-2 text-4xl font-bold">
                {{ $todaySales }}
            </h3>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200">
            <p class="text-sm font-medium text-slate-500">
                Pendapatan Hari Ini
            </p>

            <h3 class="mt-2 text-4xl font-bold text-slate-900">
                Rp {{ number_format($todayRevenue, 0, ',', '.') }}
            </h3>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Cari invoice / customer..."
                class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >

            <input
                wire:key="start-date-{{ $filterKey }}"
                type="date"
                wire:model.live="startDate"
                class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >

            <input
                wire:key="end-date-{{ $filterKey }}"
                type="date"
                wire:model.live="endDate"
                class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >

            <button
                type="button"
                wire:click="resetFilters"
                class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
            >
                Reset Filter
            </button>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">
                            Invoice
                        </th>

                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">
                            Kasir
                        </th>

                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">
                            Customer
                        </th>

                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">
                            Tanggal
                        </th>

                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-600">
                            Total
                        </th>

                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($sales as $sale)
                        <tr class="border-b border-slate-100">
                            <td class="px-4 py-4">
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        {{ $sale->invoice_number }}
                                    </p>

                                    @if ($sale->status === 'cancelled')
                                        <span class="mt-1 inline-flex rounded-full bg-red-50 px-2 py-1 text-xs font-semibold text-red-700">
                                            Cancelled
                                        </span>
                                    @endif

                                    <p class="text-xs text-slate-500">
                                        {{ $sale->items->count() }} item
                                    </p>
                                </div>
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $sale->cashier->name }}
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                <p class="font-semibold text-slate-900">
                                    {{ $sale->customer?->name ?? 'Walk-in Customer' }}
                                </p>

                                @if ($sale->customer?->phone)
                                    <p class="text-xs text-slate-500">
                                        {{ $sale->customer->phone }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $sale->sale_date->format('d M Y H:i') }}
                            </td>

                            <td class="px-4 py-4 text-sm font-bold text-slate-900">
                                <span class="{{ $sale->status === 'cancelled' ? 'text-red-600 line-through' : 'text-slate-900' }}">
                                    Rp {{ number_format($sale->grand_total, 0, ',', '.') }}
                                </span>

                                @if ($sale->status === 'cancelled')
                                    <p class="mt-1 text-xs font-semibold text-red-600">
                                        Tidak dihitung
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-right">
                                <a
                                    href="{{ route('sales.show', $sale) }}"
                                    class="rounded-xl bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100"
                                >
                                    Detail
                                </a>
                                
                                <a
                                    href="{{ route('sales.receipt', $sale) }}"
                                    target="_blank"
                                    class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800"
                                >
                                    Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $sales->links() }}
        </div>
    </div>
</div>
