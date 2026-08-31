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
            <div class="flex w-full flex-col gap-3 md:flex-row md:items-center">
                {{-- Search Box --}}
                <div class="relative w-full md:max-w-xs">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari produk..."
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                {{-- Custom Category Dropdown --}}
                <div x-data="{ open: false }" class="relative w-full md:w-64">
                    <button
                        type="button"
                        @click="open = !open"
                        @click.outside="open = false"
                        class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-700 outline-none transition hover:border-slate-300 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    >
                        <span class="truncate">
                            @if ($selectedCategory && $cat = $categories->firstWhere('id', (int)$selectedCategory))
                                {{ $cat->name }}
                            @else
                                Semua Kategori
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
                            wire:click="$set('selectedCategory', '')"
                            @click="open = false"
                            class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm font-medium transition {{ empty($selectedCategory) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}"
                        >
                            <span>Semua Kategori</span>
                            @if (empty($selectedCategory))
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>

                        @foreach ($categories as $cat)
                            <button
                                type="button"
                                wire:click="$set('selectedCategory', '{{ $cat->id }}')"
                                @click="open = false"
                                class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm font-medium transition {{ (string)$selectedCategory === (string)$cat->id ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}"
                            >
                                <span class="truncate">{{ $cat->name }}</span>
                                @if ((string)$selectedCategory === (string)$cat->id)
                                    <svg class="h-4 w-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
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
                                        wire:click="openBulkPrices({{ $product->id }})"
                                        wire:loading.attr="disabled"
                                        class="rounded-xl bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100 disabled:opacity-50"
                                    >
                                        Harga Grosir
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
            {{ $products->links('vendor.pagination.tailwind') }}
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
                            wire:model.live="name"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">SKU</label>

                        <div class="flex gap-2">
                            <input
                                type="text"
                                wire:model="sku"
                                placeholder="Auto-generate atau isi manual"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >

                            <button
                                type="button"
                                wire:click="generateSku"
                                wire:loading.attr="disabled"
                                wire:target="generateSku"
                                class="rounded-2xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-100 disabled:opacity-50"
                            >
                                Generate
                            </button>
                        </div>

                        @error('sku')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Barcode
                        </label>

                        <div class="flex gap-2">
                            <input
                                wire:key="barcode-input-{{ $barcode ?? 'empty' }}"
                                type="text"
                                wire:model.live="barcode"
                                placeholder="Scan barcode atau generate otomatis"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >

                            <button
                                type="button"
                                wire:click="generateBarcode"
                                wire:loading.attr="disabled"
                                wire:target="generateBarcode"
                                class="rounded-2xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-100 disabled:opacity-50"
                            >
                                Generate
                            </button>
                        </div>

                        <p class="mt-1 text-xs text-slate-500">
                            Kosongkan jika produk belum memiliki barcode. Gunakan scanner untuk mengisi langsung atau tombol Generate untuk kode internal.
                        </p>

                        @error('barcode')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div
                        x-data="{
                            open: false,
                            value: @entangle('categoryInput').live,
                            options: @js($categories->pluck('name')),
                            get filteredOptions() {
                                if (!this.value) return this.options;
                                return this.options.filter(i => i.toLowerCase().includes(this.value.toLowerCase()));
                            },
                            selectOption(opt) {
                                this.value = opt;
                                this.open = false;
                            }
                        }"
                        class="relative"
                        @click.outside="open = false"
                    >
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Kategori</label>

                        <input
                            type="text"
                            x-model="value"
                            @focus="open = true"
                            @input="open = true"
                            placeholder="Cari atau ketik kategori baru..."
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        <div
                            x-show="open && (filteredOptions.length > 0 || value)"
                            x-cloak
                            class="absolute z-50 mt-1 max-h-48 w-full overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-xl"
                        >
                            <template x-for="opt in filteredOptions" :key="opt">
                                <button
                                    type="button"
                                    @mousedown.prevent="selectOption(opt)"
                                    class="w-full text-left px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition"
                                    x-text="opt"
                                ></button>
                            </template>
                            <div x-show="filteredOptions.length === 0 && value" class="px-4 py-2.5 text-xs text-slate-400 italic">
                                Kategori baru "<span x-text="value"></span>" akan dibuat saat disimpan.
                            </div>
                        </div>

                        @error('categoryInput')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div
                        x-data="{
                            open: false,
                            value: @entangle('unitInput').live,
                            options: @js($units->pluck('name')),
                            get filteredOptions() {
                                if (!this.value) return this.options;
                                return this.options.filter(i => i.toLowerCase().includes(this.value.toLowerCase()));
                            },
                            selectOption(opt) {
                                this.value = opt;
                                this.open = false;
                            }
                        }"
                        class="relative"
                        @click.outside="open = false"
                    >
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Satuan</label>

                        <input
                            type="text"
                            x-model="value"
                            @focus="open = true"
                            @input="open = true"
                            placeholder="Cari atau ketik satuan baru..."
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        <div
                            x-show="open && (filteredOptions.length > 0 || value)"
                            x-cloak
                            class="absolute z-50 mt-1 max-h-48 w-full overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-xl"
                        >
                            <template x-for="opt in filteredOptions" :key="opt">
                                <button
                                    type="button"
                                    @mousedown.prevent="selectOption(opt)"
                                    class="w-full text-left px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition"
                                    x-text="opt"
                                ></button>
                            </template>
                            <div x-show="filteredOptions.length === 0 && value" class="px-4 py-2.5 text-xs text-slate-400 italic">
                                Satuan baru "<span x-text="value"></span>" akan dibuat saat disimpan.
                            </div>
                        </div>

                        @error('unitInput')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label class="block text-sm font-semibold text-slate-700">
                                {{ $productId ? 'Harga Beli Terakhir' : 'Harga Beli Awal' }}
                            </label>
                            @if ($productId && $average_cost)
                                <span class="rounded-lg bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">
                                    HPP: Rp {{ number_format($average_cost, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
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
                                @readonly($productId)
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 pl-12 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @if($productId) bg-slate-100 text-slate-500 cursor-not-allowed @endif"
                            >
                        </div>
                        @if ($productId)
                            <p class="mt-1 text-xs text-slate-500">
                                Harga beli & HPP diperbarui otomatis saat transaksi Pembelian dari supplier.
                            </p>
                        @endif
                        @error('purchase_price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label class="block text-sm font-semibold text-slate-700">Harga Jual</label>
                            @if ($purchase_price)
                                <button
                                    type="button"
                                    wire:click="$set('selling_price', {{ (int) ceil(($purchase_price * 1.05) / 100) * 100 }})"
                                    class="text-xs font-semibold text-blue-600 hover:text-blue-800 underline"
                                >
                                    Set +5% (Rp {{ number_format((int) ceil(($purchase_price * 1.05) / 100) * 100, 0, ',', '.') }})
                                </button>
                            @endif
                        </div>
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
                        @if ($selling_price && $purchase_price && $selling_price < $purchase_price)
                            <p class="mt-1 text-xs font-semibold text-amber-600">
                                ⚠️ Peringatan: Harga jual lebih rendah dari harga beli (rugi modal).
                            </p>
                        @endif
                        @error('selling_price')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Stok
                        </label>

                        <div
                            x-data="{
                                raw: @entangle('stock').live,
                                display: '',
                                isEdit: @js((bool) $productId),
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
                                @readonly($productId)
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100
                                    {{ $productId ? 'cursor-not-allowed bg-slate-100 text-slate-500' : 'bg-white text-slate-900' }}"
                            >
                        </div>

                        @if ($productId)
                            <p class="mt-1 text-xs text-slate-500">
                                Stok tidak dapat diubah dari form produk. Gunakan menu Stock Adjustment untuk koreksi stok.
                            </p>
                        @endif

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

    @if ($showBulkPriceModal)   
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-4xl rounded-3xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            Atur Harga Grosir
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $bulkProductName }}
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="$set('showBulkPriceModal', false)"
                        class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-200"
                    >
                        ✕
                    </button>
                </div>

                <div class="p-6">
                    <div class="rounded-2xl bg-blue-50 p-4 text-sm text-blue-700">
                        Harga grosir digunakan sistem POS untuk menentukan harga otomatis berdasarkan jumlah pembelian.
                        Kosongkan kolom maksimum jika harga berlaku untuk jumlah pembelian tanpa batas.
                    </div>

                    @error('bulkPrices')
                        <div class="mt-4 rounded-2xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="mt-5 space-y-4">
                        @foreach ($bulkPrices as $index => $price)
                            <div
                                wire:key="bulk-price-row-{{ $index }}"
                                class="grid grid-cols-1 gap-4 rounded-2xl border border-slate-200 p-4 md:grid-cols-4"
                            >
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                                        Minimal Qty
                                    </label>

                                    <input
                                        type="number"
                                        min="1"
                                        wire:model="bulkPrices.{{ $index }}.min_qty"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    >

                                    @error("bulkPrices.$index.min_qty")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                                        Maksimal Qty
                                    </label>

                                    <input
                                        type="number"
                                        min="1"
                                        wire:model="bulkPrices.{{ $index }}.max_qty"
                                        placeholder="Tanpa batas"
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    >

                                    @error("bulkPrices.$index.max_qty")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                                        Harga
                                    </label>

                                    <div
                                        x-data="{
                                            raw: @entangle('bulkPrices.' . $index . '.price').live,
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
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-500">
                                            Rp
                                        </span>

                                        <input
                                            type="text"
                                            inputmode="numeric"
                                            x-model="display"
                                            x-on:input="format($event.target.value)"
                                            placeholder="0"
                                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 pl-12 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                        >
                                    </div>

                                    @error("bulkPrices.$index.price")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-end">
                                    <button
                                        type="button"
                                        wire:click="removeBulkPriceRow({{ $index }})"
                                        class="w-full rounded-2xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 hover:bg-red-100"
                                    >
                                        Hapus Baris
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button
                        type="button"
                        wire:click="addBulkPriceRow"
                        class="mt-5 rounded-2xl bg-blue-50 px-5 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-100"
                    >
                        + Tambah Baris Harga
                    </button>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-5">
                    <button
                        type="button"
                        wire:click="$set('showBulkPriceModal', false)"
                        class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        wire:click="saveBulkPrices"
                        wire:loading.attr="disabled"
                        class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        Simpan Harga Grosir
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
