<x-pos-layout>
    <x-slot name="title">Dashboard - POS Ari Gita Grosir</x-slot>
    <x-slot name="header">Dashboard</x-slot>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">Penjualan Hari Ini</p>
            <h3 class="mt-3 text-2xl font-bold text-slate-900">Rp {{ number_format($todaySalesTotal, 0, ',', '.') }}</h3>
            <p class="mt-2 text-xs font-semibold text-emerald-700">{{ number_format($todayTransactionCount, 0, ',', '.') }} transaksi selesai</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">Order Baru</p>
            <h3 class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($pendingOrderCount, 0, ',', '.') }}</h3>
            <p class="mt-2 text-xs font-semibold text-red-700">Pending/preorder perlu diproses</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">Produk Aktif</p>
            <h3 class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($activeProductCount, 0, ',', '.') }}</h3>
            <p class="mt-2 text-xs text-slate-500">SKU aktif siap dijual</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">Stok Minimum</p>
            <h3 class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($lowStockCount, 0, ',', '.') }}</h3>
            <p class="mt-2 text-xs font-semibold text-amber-700">Produk perlu restock</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Ringkasan Penjualan 7 Hari</h3>
                    <p class="text-sm text-slate-500">Transaksi selesai berdasarkan tanggal penjualan.</p>
                </div>
                <span class="rounded-full bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700">
                    Live
                </span>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-left text-xs uppercase text-slate-400">
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Transaksi</th>
                            <th class="py-3 text-right">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($salesByDay as $day)
                            <tr class="border-b border-slate-100">
                                <td class="py-3 font-semibold text-slate-900">{{ \Carbon\Carbon::parse($day->sale_day)->format('d M Y') }}</td>
                                <td class="py-3 text-slate-600">{{ number_format($day->transaction_count, 0, ',', '.') }}</td>
                                <td class="py-3 text-right font-bold text-slate-900">Rp {{ number_format($day->total_sales, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-sm text-slate-500">Belum ada penjualan dalam 7 hari terakhir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">Order Baru</h3>
            <div class="mt-5 space-y-3">
                @forelse ($pendingOrders as $order)
                    <a href="{{ route('customer-orders.index') }}" class="block rounded-2xl border border-slate-100 p-4 hover:border-blue-200 hover:bg-blue-50">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-bold text-slate-900">{{ $order->order_number }}</p>
                                <p class="text-xs text-slate-500">{{ $order->customer_name }}</p>
                            </div>
                            <span class="text-xs font-semibold text-blue-700">Rp {{ number_format($order->estimated_total, 0, ',', '.') }}</span>
                        </div>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                        Tidak ada order pending.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">Stok Menipis</h3>
            <div class="mt-5 space-y-3">
                @forelse ($lowStockProducts as $product)
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 p-4">
                        <div>
                            <p class="font-bold text-slate-900">{{ $product->name }}</p>
                            <p class="text-xs text-slate-500">{{ $product->sku }}</p>
                        </div>
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                            {{ number_format($product->stock, 0, ',', '.') }} / {{ number_format($product->minimum_stock, 0, ',', '.') }} {{ $product->unit?->abbreviation }}
                        </span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                        Semua stok masih aman.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900">Aktivitas Terbaru</h3>
            <div class="mt-5 space-y-3">
                @forelse ($recentActivities as $activity)
                    <div class="rounded-2xl border border-slate-100 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ $activity->event }}</span>
                            <span class="text-xs text-slate-500">{{ $activity->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-slate-900">{{ $activity->description }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $activity->user?->name ?? 'Public' }}</p>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                        Belum ada aktivitas tercatat.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-pos-layout>
