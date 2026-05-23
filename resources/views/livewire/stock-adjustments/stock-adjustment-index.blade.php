<div>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">
                Stock Adjustment
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Koreksi stok berdasarkan kondisi fisik barang dan catat alasannya untuk audit stok.
            </p>
        </div>

        <button
            type="button"
            wire:click="create"
            wire:loading.attr="disabled"
            class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
        >
            + Tambah Adjustment
        </button>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Cari produk, SKU, atau barcode..."
            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 md:max-w-sm"
        >

        @if (session()->has('success'))
            <div class="mt-4 rounded-2xl bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mt-4 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100 text-left">
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Produk</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Stok Sistem</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Stok Fisik</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Selisih</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Catatan</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">User</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Tanggal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($adjustments as $adjustment)
                        @php
                            $difference = $adjustment->stock_after - $adjustment->stock_before;
                        @endphp

                        <tr wire:key="adjustment-{{ $adjustment->id }}" class="border-b border-slate-100">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">
                                    {{ $adjustment->product->name }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    {{ $adjustment->product->sku }}
                                </p>
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ number_format($adjustment->stock_before, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-4 text-sm font-semibold text-slate-900">
                                {{ number_format($adjustment->stock_after, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $difference > 0 ? 'bg-blue-50 text-blue-700' : 'bg-red-50 text-red-700' }}">
                                    {{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $adjustment->note }}
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $adjustment->creator?->name ?? '-' }}
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $adjustment->created_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada data stock adjustment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $adjustments->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-3xl rounded-3xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            Tambah Stock Adjustment
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Masukkan stok fisik sesuai hasil pengecekan barang.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="$set('showModal', false)"
                        class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-200"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-5 p-6">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Produk
                        </label>

                        <div class="relative">
                            <input
                                id="adjustment-product-search"
                                type="text"
                                wire:model.live.debounce.250ms="productSearch"
                                wire:keydown.enter.prevent="selectFirstProduct"
                                placeholder="Ketik nama produk, SKU, atau barcode..."
                                autocomplete="off"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >

                            @if ($productSearch && ! $selectedProductId && $productSuggestions->count() > 0)
                                <div class="absolute z-50 mt-2 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                                    @foreach ($productSuggestions as $product)
                                        <button
                                            type="button"
                                            wire:key="adjustment-product-suggestion-{{ $product->id }}"
                                            wire:click="selectProduct({{ $product->id }})"
                                            class="w-full px-4 py-3 text-left hover:bg-blue-50"
                                        >
                                            <p class="text-sm font-bold text-slate-900">
                                                {{ $product->name }}
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                {{ $product->sku }} · Stok Sistem: {{ number_format($product->stock, 0, ',', '.') }}
                                            </p>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @error('selectedProductId')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($selectedProduct)
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase text-slate-400">
                                    Produk
                                </p>

                                <p class="mt-1 text-sm font-bold text-slate-900">
                                    {{ $selectedProduct['name'] }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    {{ $selectedProduct['sku'] }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-blue-50 p-4">
                                <p class="text-xs font-semibold uppercase text-blue-500">
                                    Stok Sistem
                                </p>

                                <p class="mt-1 text-2xl font-bold text-blue-900">
                                    {{ number_format($systemStock, 0, ',', '.') }}
                                    <span class="text-sm">{{ $selectedProduct['unit'] }}</span>
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase text-slate-400">
                                    Selisih
                                </p>

                                <p class="mt-1 text-2xl font-bold {{ ($difference ?? 0) >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                                    {{ ($difference ?? 0) > 0 ? '+' : '' }}{{ number_format($difference ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Stok Fisik
                        </label>

                        <input
                            id="adjustment-physical-stock"
                            type="number"
                            min="0"
                            wire:model.live="physicalStock"
                            x-on:keydown.enter.prevent="document.getElementById('adjustment-note')?.focus()"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        @error('physicalStock')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Alasan Adjustment
                        </label>

                        <textarea
                            id="adjustment-note"
                            wire:model.live="note"
                            rows="4"
                            placeholder="Contoh: Selisih stok opname, barang rusak, salah input stok sebelumnya..."
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        ></textarea>

                        @error('note')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-5">
                    <button
                        type="button"
                        wire:click="$set('showModal', false)"
                        class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        wire:click="saveAdjustment"
                        wire:loading.attr="disabled"
                        class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        Simpan Adjustment
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('focus-adjustment-product', () => {
                setTimeout(() => {
                    document.getElementById('adjustment-product-search')?.focus();
                }, 150);
            });

            Livewire.on('focus-adjustment-physical-stock', () => {
                setTimeout(() => {
                    const input = document.getElementById('adjustment-physical-stock');

                    if (input) {
                        input.focus();
                        input.select();
                    }
                }, 150);
            });
        });
    </script>
</div>