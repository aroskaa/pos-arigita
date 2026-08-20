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

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
        {{-- Preset Pills & Search Row --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between border-b border-slate-100 pb-4">
            {{-- Date Presets --}}
            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    wire:click="setDatePreset('all')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-xs font-semibold transition {{ $datePreset === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    Semua Waktu
                </button>

                <button
                    type="button"
                    wire:click="setDatePreset('today')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-xs font-semibold transition {{ $datePreset === 'today' ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}"
                >
                    Hari Ini
                </button>

                <button
                    type="button"
                    wire:click="setDatePreset('7days')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-xs font-semibold transition {{ $datePreset === '7days' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}"
                >
                    7 Hari Terakhir
                </button>

                <button
                    type="button"
                    wire:click="setDatePreset('this_month')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-xs font-semibold transition {{ $datePreset === 'this_month' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                >
                    Bulan Ini
                </button>

                <button
                    type="button"
                    wire:click="setDatePreset('last_month')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-xs font-semibold transition {{ $datePreset === 'last_month' ? 'bg-amber-600 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}"
                >
                    Bulan Lalu
                </button>
            </div>

            {{-- Search Box --}}
            <div class="relative w-full lg:w-80">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari invoice / customer..."
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-11 pr-4 py-2.5 text-sm outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition"
                >
            </div>
        </div>

        {{-- Custom Date Inputs Row --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 items-end pt-1">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">
                    Dari Tanggal:
                </label>
                <div class="relative">
                    <input
                        wire:key="start-date-{{ $filterKey }}"
                        type="date"
                        wire:model.live="startDate"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-800 outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition"
                    >
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">
                    Sampai Tanggal:
                </label>
                <div class="relative">
                    <input
                        wire:key="end-date-{{ $filterKey }}"
                        type="date"
                        wire:model.live="endDate"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-medium text-slate-800 outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition"
                    >
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-200 hover:text-slate-900 transition"
                >
                    Reset Filter
                </button>
            </div>
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
                                
                                <button
                                    type="button"
                                    onclick="printReceipt('{{ route('sales.receipt', ['sale' => $sale, 'is_copy' => 1]) }}')"
                                    class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800"
                                >
                                    Struk
                                </button>
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
            {{ $sales->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>
