<div>
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-900">POS Transaksi</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Cari produk berdasarkan nama, SKU, atau barcode.
                        </p>
                    </div>

                    <div class="rounded-2xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700">
                        {{ now()->format('d M Y') }}
                    </div>
                </div>

                <div class="mt-6">
                    <input
                        type="text"
                        wire:model.live="search"
                        placeholder="Scan barcode atau cari produk..."
                        autofocus
                        class="w-full rounded-2xl border border-slate-200 px-5 py-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                @if (session()->has('error'))
                    <div class="mt-4 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session()->has('success'))
                    <div class="mt-4 rounded-2xl bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 2xl:grid-cols-3">
                    @forelse ($products as $product)
                        <button
                            type="button"
                            wire:click="addToCart({{ $product->id }})"
                            class="rounded-3xl border border-slate-200 bg-white p-5 text-left shadow-sm transition hover:border-blue-300 hover:bg-blue-50"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $product->name }}</h4>
                                    <p class="mt-1 text-xs text-slate-500">{{ $product->sku }}</p>
                                </div>

                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                    {{ $product->stock }} {{ $product->unit->abbreviation }}
                                </span>
                            </div>

                            <div class="mt-5 flex items-end justify-between">
                                <div>
                                    <p class="text-xs text-slate-500">Harga mulai</p>
                                    <p class="text-lg font-bold text-blue-700">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </p>
                                </div>

                                <span class="text-sm font-semibold text-slate-500">
                                    + Tambah
                                </span>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500">
                            Produk tidak ditemukan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div>
            <div class="sticky top-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Keranjang</h3>
                        <p class="text-sm text-slate-500">Item transaksi berjalan.</p>
                    </div>

                    <button
                        type="button"
                        wire:click="clearCart"
                        class="rounded-xl bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100"
                    >
                        Kosongkan
                    </button>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($cart as $item)
                        <div wire:key="cart-item-{{ $item['product_id'] }}" 
                            class="rounded-2xl border border-slate-200 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h4 class="font-semibold text-slate-900">{{ $item['name'] }}</h4>
                                    <p class="text-xs text-slate-500">{{ $item['sku'] }}</p>
                                </div>

                                <button
                                    type="button"
                                    wire:click="removeItem({{ $item['product_id'] }})"
                                    class="text-sm font-bold text-red-600"
                                >
                                    ×
                                </button>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="decreaseQuantity({{ $item['product_id'] }})"
                                        class="h-8 w-8 rounded-xl bg-slate-100 font-bold text-slate-700"
                                    >
                                        -
                                    </button>

                                    <input
                                        type="text"
                                        inputmode="numeric"
                                        wire:key="cart-quantity-{{ $item['product_id'] }}-{{ $item['quantity'] }}"
                                        value="{{ $item['quantity'] }}"
                                        wire:change="updateQuantity({{ $item['product_id'] }}, $event.target.value)"
                                        class="w-20 rounded-xl border border-slate-200 px-3 py-2 text-center text-sm font-bold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    >

                                    <button
                                        type="button"
                                        wire:click="increaseQuantity({{ $item['product_id'] }})"
                                        class="h-8 w-8 rounded-xl bg-blue-50 font-bold text-blue-700"
                                    >
                                        +
                                    </button>
                                </div>

                                <div class="text-right">
                                    <p class="text-xs text-slate-500">
                                        Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                    </p>
                                    <p class="font-bold text-slate-900">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                            Keranjang masih kosong.
                        </div>
                    @endforelse
                </div>

                <div class="mt-6 border-t border-slate-100 pt-5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-500">Subtotal</span>
                        <span class="text-xl font-bold text-slate-900">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </span>
                    </div>

                    <button
                        type="button"
                        wire:click="openConfirmModal"
                        wire:loading.attr="disabled"
                        class="mt-5 w-full rounded-2xl bg-blue-600 px-5 py-4 text-sm font-bold text-white hover:bg-blue-700 disabled:opacity-50"
                        @disabled(count($cart) === 0)
                    >
                        Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-xl font-bold text-slate-900">
                        Konfirmasi Transaksi
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Pastikan seluruh produk dan quantity sudah sesuai sebelum transaksi diproses.
                    </p>
                </div>

                <div class="p-6">
                    <div class="rounded-2xl bg-blue-50 p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-blue-700">Total Product</span>
                            <span class="text-sm font-bold text-blue-900">{{ count($cart) }}</span>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-blue-700">Grand Total</span>
                            <span class="text-xl font-bold text-blue-900">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-slate-500">
                        Setelah dikonfirmasi, sistem akan menyimpan transaksi, membuat invoice, dan mengurangi stok produk secara otomatis.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-5">
                    <button
                        type="button"
                        wire:click="closeConfirmModal"
                        class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                    >
                        Cek Ulang
                    </button>

                    <button
                        type="button"
                        wire:click="saveTransaction"
                        wire:loading.attr="disabled"
                        class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        Ya, Proses Transaksi
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>