<div>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">Produk & Stok</h3>
            <p class="mt-1 text-sm text-slate-500">Kelola produk, stok, barcode, dan harga jual.</p>
        </div>

        <button
            type="button"
            wire:click="create"
            wire:loading.attr="disabled"
            class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
        >
            + Tambah Produk
        </button>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Cari produk..."
                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 md:max-w-sm"
            >
        </div>

        @if (session()->has('success'))
            <div class="mt-4 rounded-2xl bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100 text-left">
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Produk</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Kategori</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Stok</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Harga</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($products as $product)
                        <tr wire:key="product-{{ $product->id }}" class="border-b border-slate-100">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                <p class="text-xs text-slate-500">{{ $product->sku }}</p>
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $product->category->name }}
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-slate-900">{{ $product->stock }}</span>
                                    <span class="text-xs text-slate-500">{{ $product->unit->abbreviation }}</span>

                                    @if ($product->stock <= $product->minimum_stock)
                                        <span class="rounded-full bg-red-50 px-2 py-1 text-[10px] font-bold text-red-700">
                                            STOK MENIPIS
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4 text-sm font-semibold text-slate-900">
                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-4">
                                @if ($product->is_active)
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Aktif</span>
                                @else
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Nonaktif</span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $product->id }})"
                                        wire:loading.attr="disabled"
                                        class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="delete({{ $product->id }})"
                                        wire:confirm="Yakin ingin menghapus produk ini?"
                                        wire:loading.attr="disabled"
                                        class="rounded-xl bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100 disabled:opacity-50"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada data produk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-3xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            {{ $productId ? 'Edit Produk' : 'Tambah Produk' }}
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">Kelola informasi produk dan stok barang.</p>
                    </div>

                    <button
                        type="button"
                        wire:click="$set('showModal', false)"
                        class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-200"
                    >
                        ✕
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Produk</label>
                        <input
                            type="text"
                            wire:model="name"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">SKU</label>
                        <input
                            type="text"
                            wire:model="sku"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('sku')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Barcode</label>
                        <input
                            type="text"
                            wire:model="barcode"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('barcode')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Kategori</label>
                        <select
                            wire:model="category_id"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Satuan</label>
                        <select
                            wire:model="unit_id"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="">Pilih satuan</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Harga Beli</label>
                        <div
                            x-data="{
                                raw: @entangle('purchase_price').live,
                                display: '',
                                format(value) {
                                    const number = String(value ?? '').replace(/\D/g, '');

                                    if (number === '') {
                                        this.display = '';
                                        this.raw = null;
                                        return;
                                    }

                                    this.raw = parseInt(number, 10);
                                    this.display = new Intl.NumberFormat('id-ID').format(parseInt(number, 10));
                                }
                            }"
                            x-init="format(raw); $watch('raw', value => format(value))"
                            class="relative"
                        >
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-500">Rp</span>
                            <input
                                type="text"
                                inputmode="numeric"
                                x-model="display"
                                x-on:input="format($event.target.value)"
                                placeholder="0"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 pl-12 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                        </div>
                        @error('purchase_price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Harga Jual</label>
                        <div
                            x-data="{
                                raw: @entangle('selling_price').live,
                                display: '',
                                format(value) {
                                    const number = String(value ?? '').replace(/\D/g, '');

                                    if (number === '') {
                                        this.display = '';
                                        this.raw = null;
                                        return;
                                    }

                                    this.raw = parseInt(number, 10);
                                    this.display = new Intl.NumberFormat('id-ID').format(parseInt(number, 10));
                                }
                            }"
                            x-init="format(raw); $watch('raw', value => format(value))"
                            class="relative"
                        >
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-500">Rp</span>
                            <input
                                type="text"
                                inputmode="numeric"
                                x-model="display"
                                x-on:input="format($event.target.value)"
                                placeholder="0"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 pl-12 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                        </div>
                        @error('selling_price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Stok</label>
                        <div
                            x-data="{
                                raw: @entangle('stock').live,
                                display: '',
                                format(value) {
                                    const number = String(value ?? '').replace(/\D/g, '');

                                    if (number === '') {
                                        this.display = '';
                                        this.raw = null;
                                        return;
                                    }

                                    this.raw = parseInt(number, 10);
                                    this.display = new Intl.NumberFormat('id-ID').format(parseInt(number, 10));
                                }
                            }"
                            x-init="format(raw); $watch('raw', value => format(value))"
                            class="relative"
                        >
                            <input
                                type="text"
                                inputmode="numeric"
                                x-model="display"
                                x-on:input="format($event.target.value)"
                                placeholder="0"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 pl-12 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                        </div>
                        @error('stock')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Minimum Stok</label>
                        <div
                            x-data="{
                                raw: @entangle('minimum_stock').live,
                                display: '',
                                format(value) {
                                    const number = String(value ?? '').replace(/\D/g, '');

                                    if (number === '') {
                                        this.display = '';
                                        this.raw = null;
                                        return;
                                    }

                                    this.raw = parseInt(number, 10);
                                    this.display = new Intl.NumberFormat('id-ID').format(parseInt(number, 10));
                                }
                            }"
                            x-init="format(raw); $watch('raw', value => format(value))"
                            class="relative"
                        >
                            <input
                                type="text"
                                inputmode="numeric"
                                x-model="display"
                                x-on:input="format($event.target.value)"
                                placeholder="0"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 pl-12 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                        </div>
                        @error('minimum_stock')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi</label>
                        <textarea
                            wire:model="description"
                            rows="4"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        ></textarea>
                    </div>

                    <div class="md:col-span-2 flex items-center gap-3">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            class="h-5 w-5 rounded border-slate-300 text-blue-600"
                        >
                        <label class="text-sm font-medium text-slate-700">Produk aktif</label>
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
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        Simpan Produk
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
