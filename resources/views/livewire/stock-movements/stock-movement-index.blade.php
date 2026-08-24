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
            {{-- Search Box --}}
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari produk, SKU, barcode..."
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                >
            </div>

            {{-- Custom Alpine Dropdown for Produk --}}
            <div x-data="{ open: false }" class="relative">
                <button
                    type="button"
                    @click="open = !open"
                    @click.outside="open = false"
                    class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-700 outline-none transition hover:border-slate-300 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                >
                    <span class="truncate">
                        @if ($productId && $selectedProduct = $products->firstWhere('id', (int)$productId))
                            {{ $selectedProduct->name }}
                        @else
                            Semua Produk
                        @endif
                    </span>
                    <svg class="h-4 w-4 shrink-0 text-slate-400 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute left-0 right-0 z-50 mt-2 max-h-60 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl"
                    style="display: none;"
                >
                    <button
                        type="button"
                        wire:click="$set('productId', '')"
                        @click="open = false"
                        class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm font-medium transition {{ empty($productId) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}"
                    >
                        <span>Semua Produk</span>
                        @if (empty($productId))
                            <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </button>

                    @foreach ($products as $product)
                        <button
                            type="button"
                            wire:click="$set('productId', '{{ $product->id }}')"
                            @click="open = false"
                            class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm font-medium transition {{ (string) $productId === (string) $product->id ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}"
                        >
                            <span class="truncate">{{ $product->name }}</span>
                            @if ((string) $productId === (string) $product->id)
                                <svg class="h-4 w-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Custom Alpine Dropdown for Tipe --}}
            <div x-data="{ open: false }" class="relative">
                <button
                    type="button"
                    @click="open = !open"
                    @click.outside="open = false"
                    class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-700 outline-none transition hover:border-slate-300 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                >
                    <span class="truncate">
                        @if ($type && isset($movementTypes[$type]))
                            {{ $movementTypes[$type] }}
                        @else
                            Semua Tipe
                        @endif
                    </span>
                    <svg class="h-4 w-4 shrink-0 text-slate-400 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute left-0 right-0 z-50 mt-2 max-h-60 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl"
                    style="display: none;"
                >
                    <button
                        type="button"
                        wire:click="$set('type', '')"
                        @click="open = false"
                        class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm font-medium transition {{ empty($type) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}"
                    >
                        <span>Semua Tipe</span>
                        @if (empty($type))
                            <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </button>

                    @foreach ($movementTypes as $key => $label)
                        <button
                            type="button"
                            wire:click="$set('type', '{{ $key }}')"
                            @click="open = false"
                            class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm font-medium transition {{ (string) $type === (string) $key ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}"
                        >
                            <span>{{ $label }}</span>
                            @if ((string) $type === (string) $key)
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Start Date --}}
            <div class="relative">
                <input
                    type="date"
                    wire:model.live="startDate"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-700 outline-none transition hover:border-slate-300 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 cursor-pointer"
                >
            </div>

            {{-- End Date --}}
            <div class="relative">
                <input
                    type="date"
                    wire:model.live="endDate"
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-700 outline-none transition hover:border-slate-300 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 cursor-pointer"
                >
            </div>

            {{-- Reset Button --}}
            <button
                type="button"
                wire:click="resetFilters"
                class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200 hover:text-slate-900 transition"
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