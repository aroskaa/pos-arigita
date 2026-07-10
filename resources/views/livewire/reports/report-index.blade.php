<div>
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <div class="inline-flex rounded-2xl bg-slate-100 p-1">
                    <button
                        type="button"
                        wire:click="setTab('sales')"
                        class="rounded-xl px-4 py-2 text-sm font-semibold {{ $tab === 'sales' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        Penjualan
                    </button>
                    <button
                        type="button"
                        wire:click="setTab('inventory')"
                        class="rounded-xl px-4 py-2 text-sm font-semibold {{ $tab === 'inventory' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        Persediaan
                    </button>
                    <button
                        type="button"
                        wire:click="setTab('logs')"
                        class="rounded-xl px-4 py-2 text-sm font-semibold {{ $tab === 'logs' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                    >
                        Log Sistem
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-3 xl:w-[560px]">
                <input
                    type="date"
                    wire:model.live="startDate"
                    class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >
                <input
                    type="date"
                    wire:model.live="endDate"
                    class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                >
                    Reset Filter
                </button>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap gap-3">
            @if ($tab === 'sales')
                <a
                    href="{{ route('reports.download', ['type' => 'financial', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                    class="rounded-2xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-100"
                >
                    Download Keuangan
                </a>
                <a
                    href="{{ route('reports.download', ['type' => 'sales', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                    class="rounded-2xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-100"
                >
                    Download Penjualan
                </a>
            @elseif ($tab === 'inventory')
                <a
                    href="{{ route('reports.download', ['type' => 'inventory', 'start_date' => $startDate, 'end_date' => $endDate, 'product_search' => $productSearch]) }}"
                    class="rounded-2xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-100"
                >
                    Download Barang
                </a>
                <a
                    href="{{ route('reports.download', ['type' => 'stock-movements', 'start_date' => $startDate, 'end_date' => $endDate, 'product_search' => $productSearch, 'movement_type' => $movementType]) }}"
                    class="rounded-2xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-100"
                >
                    Download Pergerakan Stok
                </a>
            @elseif ($tab === 'logs')
                <a
                    href="{{ route('reports.download', ['type' => 'activity-logs', 'start_date' => $startDate, 'end_date' => $endDate, 'log_search' => $logSearch, 'log_event' => $logEvent]) }}"
                    class="rounded-2xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-100"
                >
                    Download Log
                </a>
            @endif
        </div>
    </div>

    @if ($tab === 'sales')
        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Omzet</p>
                <h3 class="mt-3 text-2xl font-bold text-slate-900">Rp {{ number_format($salesSummary['gross_sales'], 0, ',', '.') }}</h3>
                <p class="mt-2 text-xs font-semibold text-emerald-700">{{ $salesSummary['transaction_count'] }} transaksi selesai</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Laba Kotor</p>
                <h3 class="mt-3 text-2xl font-bold text-slate-900">Rp {{ number_format($salesSummary['gross_profit'], 0, ',', '.') }}</h3>
                <p class="mt-2 text-xs text-slate-500">Berdasarkan cost price saat penjualan</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Rata-rata Transaksi</p>
                <h3 class="mt-3 text-2xl font-bold text-slate-900">Rp {{ number_format($salesSummary['average_transaction'], 0, ',', '.') }}</h3>
                <p class="mt-2 text-xs text-slate-500">Nilai rata-rata nota</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Diskon</p>
                <h3 class="mt-3 text-2xl font-bold text-slate-900">Rp {{ number_format($salesSummary['discount_total'], 0, ',', '.') }}</h3>
                <p class="mt-2 text-xs text-slate-500">Total diskon periode ini</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-5">
            <div class="xl:col-span-3 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">Penjualan Harian</h3>
                <div class="mt-5 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-left text-xs uppercase text-slate-400">
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Transaksi</th>
                                <th class="py-3 text-right">Omzet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dailySales as $day)
                                <tr class="border-b border-slate-100">
                                    <td class="py-3 font-semibold text-slate-900">{{ \Carbon\Carbon::parse($day->sale_day)->format('d M Y') }}</td>
                                    <td class="py-3 text-slate-600">{{ number_format($day->transaction_count, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right font-bold text-slate-900">Rp {{ number_format($day->total_sales, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-sm text-slate-500">Belum ada penjualan pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="xl:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">Produk Terlaris</h3>
                <div class="mt-5 space-y-3">
                    @forelse ($topProducts as $product)
                        <div class="rounded-2xl border border-slate-100 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-slate-900">{{ $product->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $product->sku }}</p>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                    {{ number_format($product->total_quantity, 0, ',', '.') }} terjual
                                </span>
                            </div>
                            <p class="mt-3 text-sm font-semibold text-slate-700">Rp {{ number_format($product->total_sales, 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                            Belum ada produk terjual.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">Transaksi Terbaru</h3>
            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase text-slate-400">
                            <th class="py-3">Invoice</th>
                            <th class="py-3">Customer</th>
                            <th class="py-3">Kasir</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestSales as $sale)
                            <tr class="border-b border-slate-100">
                                <td class="py-3 font-bold text-blue-700">{{ $sale->invoice_number }}</td>
                                <td class="py-3 text-slate-600">{{ $sale->customer?->name ?? 'Walk-in Customer' }}</td>
                                <td class="py-3 text-slate-600">{{ $sale->cashier?->name ?? '-' }}</td>
                                <td class="py-3 text-slate-600">{{ $sale->sale_date->format('d M Y H:i') }}</td>
                                <td class="py-3 text-right font-bold text-slate-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-sm text-slate-500">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($tab === 'inventory')
        <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Produk Aktif</p>
                <h3 class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($inventorySummary['active_products'], 0, ',', '.') }}</h3>
                <p class="mt-2 text-xs text-slate-500">SKU aktif di master produk</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Total Stok</p>
                <h3 class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($inventorySummary['total_stock'], 0, ',', '.') }}</h3>
                <p class="mt-2 text-xs text-slate-500">Akumulasi semua produk</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Nilai Persediaan</p>
                <h3 class="mt-3 text-2xl font-bold text-slate-900">Rp {{ number_format($inventorySummary['inventory_value'], 0, ',', '.') }}</h3>
                <p class="mt-2 text-xs text-slate-500">Stok x average cost</p>
            </div>
            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
                <p class="text-sm text-amber-700">Stok Menipis</p>
                <h3 class="mt-3 text-2xl font-bold text-amber-900">{{ number_format($inventorySummary['low_stock_count'], 0, ',', '.') }}</h3>
                <p class="mt-2 text-xs font-semibold text-amber-700">Perlu restock</p>
            </div>
        </div>

        <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Produk Stok Menipis</h3>
                    <p class="text-sm text-slate-500">Produk aktif dengan stok sama atau di bawah minimum stok.</p>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="productSearch"
                    placeholder="Cari produk..."
                    class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 lg:w-72"
                >
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase text-slate-400">
                            <th class="py-3">Produk</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3 text-right">Stok</th>
                            <th class="py-3 text-right">Minimum</th>
                            <th class="py-3 text-right">Estimasi Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lowStockProducts as $product)
                            <tr class="border-b border-slate-100">
                                <td class="py-3">
                                    <p class="font-bold text-slate-900">{{ $product->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $product->sku }}</p>
                                </td>
                                <td class="py-3 text-slate-600">{{ $product->category?->name ?? '-' }}</td>
                                <td class="py-3 text-right font-bold text-amber-700">{{ number_format($product->stock, 0, ',', '.') }} {{ $product->unit?->abbreviation }}</td>
                                <td class="py-3 text-right text-slate-600">{{ number_format($product->minimum_stock, 0, ',', '.') }}</td>
                                <td class="py-3 text-right font-semibold text-slate-900">Rp {{ number_format($product->stock * $product->average_cost, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-sm text-slate-500">Tidak ada produk stok menipis.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-5">
            <div class="xl:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900">Ringkasan Pergerakan</h3>
                <div class="mt-5 space-y-3">
                    @forelse ($movementSummary as $movement)
                        <div class="rounded-2xl border border-slate-100 p-4">
                            <div class="flex items-center justify-between">
                                <p class="font-bold capitalize text-slate-900">{{ str_replace('_', ' ', $movement->type) }}</p>
                                <span class="text-xs font-semibold text-slate-500">{{ $movement->movement_count }} aktivitas</span>
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                                <div class="rounded-xl bg-emerald-50 px-3 py-2 text-emerald-700">
                                    Masuk: <span class="font-bold">{{ number_format($movement->total_in, 0, ',', '.') }}</span>
                                </div>
                                <div class="rounded-xl bg-red-50 px-3 py-2 text-red-700">
                                    Keluar: <span class="font-bold">{{ number_format($movement->total_out, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                            Belum ada pergerakan stok pada periode ini.
                        </div>
                    @endforelse
                </div>
                @if ($movementSummary->hasPages())
                    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                        <button
                            type="button"
                            wire:click="previousPage('summaryPage')"
                            @if ($movementSummary->onFirstPage()) disabled @endif
                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
                        >
                            Sebelumnya
                        </button>
                        
                        <span class="text-xs font-bold text-slate-500">
                            Halaman {{ $movementSummary->currentPage() }} dari {{ $movementSummary->lastPage() }}
                        </span>

                        <button
                            type="button"
                            wire:click="nextPage('summaryPage')"
                            @if (!$movementSummary->hasMorePages()) disabled @endif
                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
                        >
                            Selanjutnya
                        </button>
                    </div>
                @endif
            </div>

            <div class="xl:col-span-3 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Pergerakan Stok Terbaru</h3>
                    <select
                        wire:model.live="movementType"
                        class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 lg:w-56"
                    >
                        <option value="">Semua tipe</option>
                        <option value="purchase">Purchase</option>
                        <option value="sale">Sale</option>
                        <option value="adjustment">Adjustment</option>
                        <option value="return">Return</option>
                        <option value="order_cancel">Order Cancel</option>
                    </select>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-left text-xs uppercase text-slate-400">
                                <th class="py-3">Produk</th>
                                <th class="py-3">Tipe</th>
                                <th class="py-3 text-right">Masuk</th>
                                <th class="py-3 text-right">Keluar</th>
                                <th class="py-3 text-right">Stok Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentMovements as $movement)
                                <tr class="border-b border-slate-100">
                                    <td class="py-3">
                                        <p class="font-bold text-slate-900">{{ $movement->product?->name ?? '-' }}</p>
                                        <p class="text-xs text-slate-500">{{ $movement->created_at->format('d M Y H:i') }}</p>
                                    </td>
                                    <td class="py-3 capitalize text-slate-600">{{ str_replace('_', ' ', $movement->type) }}</td>
                                    <td class="py-3 text-right font-semibold text-emerald-700">{{ number_format($movement->quantity_in, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right font-semibold text-red-700">{{ number_format($movement->quantity_out, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right font-bold text-slate-900">{{ number_format($movement->stock_after, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-sm text-slate-500">Belum ada pergerakan stok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @if ($recentMovements->hasPages())
                    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                        <button
                            type="button"
                            wire:click="previousPage('movementsPage')"
                            @if ($recentMovements->onFirstPage()) disabled @endif
                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
                        >
                            Sebelumnya
                        </button>
                        
                        <span class="text-xs font-bold text-slate-500">
                            Halaman {{ $recentMovements->currentPage() }} dari {{ $recentMovements->lastPage() }}
                        </span>

                        <button
                            type="button"
                            wire:click="nextPage('movementsPage')"
                            @if (!$recentMovements->hasMorePages()) disabled @endif
                            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
                        >
                            Selanjutnya
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if ($tab === 'logs')
        <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Log Aktivitas Sistem</h3>
                    <p class="text-sm text-slate-500">Audit operasional dari transaksi, order, stok, dan produk.</p>
                </div>
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:w-[560px]">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="logSearch"
                        placeholder="Cari aktivitas atau user..."
                        class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                    <select
                        wire:model.live="logEvent"
                        class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="">Semua event</option>
                        @foreach ($logEvents as $event)
                            <option value="{{ $event }}">{{ \App\Livewire\Reports\ReportIndex::formatEventName($event) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase text-slate-400">
                            <th class="py-3">Waktu</th>
                            <th class="py-3">Event</th>
                            <th class="py-3">Aktivitas</th>
                            <th class="py-3">User</th>
                            <th class="py-3">Metadata</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activityLogs as $log)
                            <tr class="border-b border-slate-100 align-top">
                                <td class="py-3 whitespace-nowrap text-slate-600">{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td class="py-3 whitespace-nowrap">
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                                        {{ \App\Livewire\Reports\ReportIndex::formatEventName($log->event) }}
                                    </span>
                                </td>
                                <td class="py-3 font-semibold text-slate-900 whitespace-nowrap">{{ $log->description }}</td>
                                <td class="py-3 text-slate-600 whitespace-nowrap">{{ $log->user?->name ?? 'Public' }}</td>
                                <td class="py-3 text-xs text-slate-500">
                                    @if ($log->metadata)
                                        <div class="max-w-[400px]">
                                            <code class="break-all line-clamp-2 text-xs text-slate-500" title="{{ json_encode($log->metadata, JSON_UNESCAPED_SLASHES) }}">
                                                {{ json_encode($log->metadata, JSON_UNESCAPED_SLASHES) }}
                                            </code>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-sm text-slate-500">Belum ada log aktivitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $activityLogs->links() }}
            </div>
        </div>
    @endif
</div>
