<div>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">
                Pembelian Barang
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Catat barang masuk dari supplier dan perbarui stok secara otomatis.
            </p>
        </div>

        <button
            type="button"
            wire:click="create"
            wire:loading.attr="disabled"
            class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
        >
            + Tambah Pembelian
        </button>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Cari invoice atau supplier..."
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
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Invoice</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Supplier</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Item</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr wire:key="purchase-{{ $purchase->id }}" class="border-b border-slate-100">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">
                                    {{ $purchase->invoice_number }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    {{ $purchase->creator->name }}
                                </p>
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $purchase->supplier?->name ?? 'Tanpa Supplier' }}
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $purchase->purchase_date->format('d M Y') }}
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $purchase->items->count() }} item
                            </td>

                            <td class="px-4 py-4 text-sm font-bold text-slate-900">
                                Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada data pembelian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $purchases->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-5xl rounded-3xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            Tambah Pembelian Barang
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Pembelian akan menambah stok dan menghitung ulang average cost produk.
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

                <div class="max-h-[75vh] overflow-y-auto p-6">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Supplier
                            </label>

                            <select
                                wire:model="supplier_id"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                                <option value="">Tanpa supplier</option>

                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('supplier_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Tanggal Pembelian
                            </label>

                            <input
                                type="date"
                                wire:model="purchase_date"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >

                            @error('purchase_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5">
                        <h4 class="text-sm font-bold text-slate-900">
                            Tambah Produk Pembelian
                        </h4>

                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-semibold text-slate-700">
                                    Produk
                                </label>

                                <select
                                    wire:model="selectedProductId"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                                    <option value="">Pilih produk</option>

                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} - {{ $product->sku }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('selectedProductId')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">
                                    Qty
                                </label>

                                <input
                                    type="number"
                                    min="1"
                                    wire:model="quantity"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >

                                @error('quantity')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">
                                    Harga Beli
                                </label>

                                <input
                                    type="number"
                                    min="0"
                                    wire:model="unit_cost"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >

                                @error('unit_cost')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="addItem"
                            class="mt-4 rounded-2xl bg-blue-50 px-5 py-3 text-sm font-semibold text-blue-700 hover:bg-blue-100"
                        >
                            + Tambah Produk
                        </button>
                    </div>

                    <div class="mt-6 overflow-x-auto rounded-3xl border border-slate-200">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50 text-left">
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-600">Produk</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-600">Qty</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-600">Harga Beli</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-600">Subtotal</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($items as $item)
                                    <tr wire:key="purchase-item-{{ $item['product_id'] }}" class="border-b border-slate-100">
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-slate-900">
                                                {{ $item['name'] }}
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                {{ $item['sku'] }}
                                            </p>
                                        </td>

                                        <td class="px-4 py-4 text-sm text-slate-600">
                                            {{ number_format($item['quantity'], 0, ',', '.') }}
                                        </td>

                                        <td class="px-4 py-4 text-sm text-slate-600">
                                            Rp {{ number_format($item['unit_cost'], 0, ',', '.') }}
                                        </td>

                                        <td class="px-4 py-4 text-sm font-bold text-slate-900">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </td>

                                        <td class="px-4 py-4 text-right">
                                            <button
                                                type="button"
                                                wire:click="removeItem({{ $item['product_id'] }})"
                                                class="rounded-xl bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                            Belum ada produk pembelian.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Catatan
                            </label>

                            <textarea
                                wire:model="note"
                                rows="4"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            ></textarea>
                        </div>

                        <div class="rounded-3xl bg-blue-50 p-5">
                            <p class="text-sm font-semibold text-blue-700">
                                Total Pembelian
                            </p>

                            <p class="mt-2 text-3xl font-bold text-blue-900">
                                Rp {{ number_format($totalAmount, 0, ',', '.') }}
                            </p>

                            <p class="mt-3 text-xs text-blue-700">
                                Setelah disimpan, stok produk akan bertambah dan average cost dihitung ulang otomatis.
                            </p>
                        </div>
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
                        wire:click="savePurchase"
                        wire:loading.attr="disabled"
                        class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        Simpan Pembelian
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>