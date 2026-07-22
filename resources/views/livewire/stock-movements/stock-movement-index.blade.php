<div>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">
                Kartu Stok
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Pantau seluruh riwayat perubahan stok dari pembelian, penjualan, dan penyesuaian stok.
            </p>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-6">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari produk, SKU, atau barcode..."
                class="rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >

            <select
                wire:model.live="productId"
                class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >
                <option value="">Semua Produk</option>

                @foreach ($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>

            <select
                wire:model.live="type"
                class="rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >
                <option value="">Semua Tipe</option>

                @foreach ($movementTypes as $key => $label)
                    <option value="{{ $key }}">
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <input
                type="date"
                wire:model.live="startDate"
                class="rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
            >

            <input
                type="date"
                wire:model.live="endDate"
                class="rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
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
                    <tr class="border-b border-slate-100 text-left">
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Produk</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Tipe</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Masuk</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Keluar</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Stok</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Avg Cost</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">User</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($movements as $movement)
                        <tr wire:key="movement-{{ $movement->id }}" class="border-b border-slate-100">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">
                                    {{ $movement->product->name }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    {{ $movement->product->sku }}
                                </p>
                            </td>

                            <td class="px-4 py-4">
                                @php
                                    $typeLabel = $movementTypes[$movement->type] ?? $movement->type;
                                @endphp

                                <span class="rounded-full px-3 py-1 text-xs font-semibold
                                    @if ($movement->type === 'purchase')
                                        bg-blue-50 text-blue-700
                                    @elseif ($movement->type === 'sale')
                                        bg-red-50 text-red-700
                                    @elseif ($movement->type === 'adjustment')
                                        bg-yellow-50 text-yellow-700
                                    @elseif ($movement->type === 'order_cancel')
                                        bg-green-50 text-green-700
                                    @else
                                        bg-slate-100 text-slate-700
                                    @endif  
                                ">
                                    {{ $typeLabel }}
                                </span>
                            </td>

                            <td class="px-4 py-4 text-sm font-semibold text-blue-700">
                                {{ $movement->quantity_in > 0 ? number_format($movement->quantity_in, 0, ',', '.') : '-' }}
                            </td>

                            <td class="px-4 py-4 text-sm font-semibold text-red-700">
                                {{ $movement->quantity_out > 0 ? number_format($movement->quantity_out, 0, ',', '.') : '-' }}
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                <span class="font-semibold text-slate-900">
                                    {{ number_format($movement->stock_before, 0, ',', '.') }}
                                </span>
                                →
                                <span class="font-bold text-slate-900">
                                    {{ number_format($movement->stock_after, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                <span>
                                    Rp {{ number_format($movement->average_cost_before, 0, ',', '.') }}
                                </span>
                                →
                                <span class="font-semibold text-slate-900">
                                    Rp {{ number_format($movement->average_cost_after, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $movement->creator?->name ?? '-' }}
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $movement->created_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada riwayat pergerakan stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $movements->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</div>