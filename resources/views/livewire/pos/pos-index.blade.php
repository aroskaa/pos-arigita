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

                <input
                    id="product-search-input"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Scan barcode atau cari produk..."
                    autocomplete="off"
                    class="w-full rounded-2xl border border-slate-200 px-5 py-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >

                @if ($loadedCustomerOrderId)
                    <div class="mt-4 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3">
                        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-sm font-bold text-blue-900">
                                    Order pelanggan sedang diproses
                                </p>

                                <p class="text-xs text-blue-700">
                                    Nomor Order: {{ $loadedCustomerOrderNumber }}
                                </p>
                            </div>

                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-blue-700">
                                Loaded to POS
                            </span>
                        </div>
                    </div>
                @endif

                @if (! empty($orderLoadWarnings))
                    <div class="mt-4 space-y-2">
                        @foreach ($orderLoadWarnings as $warning)
                            <div class="rounded-2xl bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                                {{ $warning }}
                            </div>
                        @endforeach
                    </div>
                @endif

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
                            wire:key="pos-product-card-{{ $product->id }}"
                            wire:click.prevent="addToCart({{ $product->id }})"
                            wire:loading.attr="disabled"
                            wire:target="addToCart({{ $product->id }})"
                            class="rounded-3xl border border-slate-200 bg-white p-5 text-left shadow-sm transition hover:border-blue-300 hover:bg-blue-50 disabled:pointer-events-none disabled:opacity-60"
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

                {{-- customer modal --}}
                <div class="mt-4">
                    <button
                        type="button"
                        wire:click="openCustomerModal"
                        class="flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-left hover:border-blue-300 hover:bg-blue-50"
                    >
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-400">
                                Customer
                            </p>

                            <p class="mt-1 text-sm font-bold text-slate-900">
                                {{ $customerName ?: 'Walk-in Customer' }}
                            </p>
                        </div>

                        <span class="text-sm font-semibold text-blue-700">
                            Atur
                        </span>
                    </button>                    
                </div>
                @error('customerName')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror

                @error('customerPhone')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-5 max-h-[420px] space-y-4 overflow-y-auto pr-2">
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
                                        id="cart-quantity-{{ $item['product_id'] }}"
                                        type="text"
                                        inputmode="numeric"
                                        wire:key="cart-quantity-{{ $item['product_id'] }}-{{ $item['quantity'] }}"
                                        value="{{ $item['quantity'] }}"
                                        wire:change="updateQuantity({{ $item['product_id'] }}, $event.target.value)"
                                        x-on:keydown.enter.prevent="
                                            document.getElementById('product-search-input')?.focus()
                                        "
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

                            <div class="mt-4">
                                <label class="mb-2 block text-xs font-semibold uppercase text-slate-400">
                                    Diskon Item
                                </label>

                                 <div
                                    class="relative"
                                    wire:key="item-discount-{{ $item['product_id'] }}"
                                    x-data="{
                                        raw: {{ $item['discount_amount'] ?? 0 }},
                                        display: '',
                                        maxLimit: {{ $item['subtotal'] }},
                                        format(value) {
                                            const number = String(value ?? '').replace(/\D/g, '');

                                            if (number === '') {
                                                this.display = '';
                                                this.raw = 0;
                                                return;
                                            }

                                            let val = parseInt(number, 10);
                                            if (val > this.maxLimit) {
                                                val = this.maxLimit;
                                            }

                                            this.raw = val;
                                            this.display = new Intl.NumberFormat('id-ID').format(val);
                                        },
                                        applyDiscount() {
                                            const id = this.$el.closest('[wire\\:id]').getAttribute('wire:id');
                                            window.Livewire.find(id).updateItemDiscount({{ $item['product_id'] }}, this.raw);
                                        }
                                    }"
                                    x-init="format(raw)"
                                    x-effect="
                                        if (document.activeElement !== $refs.itemDiscountInput) {
                                            raw = {{ $item['discount_amount'] ?? 0 }};
                                            format(raw);
                                        }
                                    "
                                >
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">
                                        Rp
                                    </span>

                                    <input
                                        x-ref="itemDiscountInput"
                                        type="text"
                                        inputmode="numeric"
                                        x-model="display"
                                        x-on:input="format($event.target.value)"
                                        x-on:blur="applyDiscount()"
                                        x-on:keydown.enter.prevent="applyDiscount(); document.getElementById('product-search-input')?.focus()"
                                        placeholder="0"
                                        class="w-full rounded-xl border border-slate-200 px-3 py-2 pl-9 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    >
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center text-sm text-slate-500">
                            Keranjang masih kosong.
                        </div>
                    @endforelse
                </div>

                {{-- <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Data Customer</h4>
                        <p class="text-xs text-slate-500">Opsional untuk transaksi walk-in.</p>
                    </div>

                    <div class="mt-4 space-y-3">
                        <select
                            wire:model="customerType"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        >
                            <option value="personal">Perorangan</option>
                            <option value="store">Toko</option>
                        </select>

                        <input
                            type="text"
                            wire:model="customerName"
                            placeholder="Nama customer / nama toko"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        >

                        <input
                            type="text"
                            wire:model="customerPhone"
                            placeholder="No. HP"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        >

                        <textarea
                            wire:model="customerAddress"
                            rows="2"
                            placeholder="Alamat, opsional untuk delivery"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"
                        ></textarea>
                    </div>
                </div> --}}

                <div class="mt-6 border-t border-slate-100 pt-5">
                    <div class="space-y-3">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Diskon Global
                            </label>                             <div
                                x-data="{
                                    raw: {{ $globalDiscount ?: 0 }},
                                    display: '',
                                    format(value) {
                                        const number = String(value ?? '').replace(/\D/g, '');

                                        if (number === '') {
                                            this.display = '';
                                            this.raw = 0;
                                            return;
                                        }

                                        const val = parseInt(number, 10);
                                        this.raw = val;
                                        this.display = new Intl.NumberFormat('id-ID').format(val);
                                    },
                                    applyDiscount() {
                                        const id = this.$el.closest('[wire\\:id]').getAttribute('wire:id');
                                        window.Livewire.find(id).set('globalDiscount', this.raw);
                                    }
                                }"
                                x-init="format(raw)"
                                x-effect="
                                    if (document.activeElement !== $refs.globalDiscountInput) {
                                        raw = {{ $globalDiscount ?: 0 }};
                                        format(raw);
                                    }
                                "
                                class="relative"
                            >
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-500">
                                    Rp
                                </span>

                                <input
                                    x-ref="globalDiscountInput"
                                    type="text"
                                    inputmode="numeric"
                                    x-model="display"
                                    x-on:input="format($event.target.value)"
                                    x-on:blur="applyDiscount()"
                                    x-on:keydown.enter.prevent="applyDiscount(); document.getElementById('product-search-input')?.focus()"
                                    placeholder="0"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 pl-12 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Metode Pembayaran
                            </label>

                            <select
                                wire:model.live="paymentMethod"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                                <option value="cash">Tunai</option>
                                <option value="qris">QRIS</option>
                                <option value="debit">Debit</option>
                                <option value="transfer">Transfer</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-5 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-500">Subtotal</span>
                        <span class="text-xl font-bold text-slate-900">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </span>
                    </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-500">Total Diskon</span>
                            <span class="text-sm font-bold text-red-600">
                                - Rp {{ number_format($discountTotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                            <span class="text-base font-bold text-slate-900">Grand Total</span>
                            <span class="text-2xl font-bold text-blue-700">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </span>
                        </div>
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
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-blue-700">
                                Customer
                            </span>

                            <span class="text-sm font-bold text-blue-900">
                                {{ $customerName ?: 'Walk-in Customer' }}
                            </span>
                        </div>

                        {{-- @if ($customerPhone)
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-sm font-semibold text-blue-700">
                                    No. HP
                                </span>

                                <span class="text-sm font-bold text-blue-900">
                                    {{ $customerPhone }}
                                </span>
                            </div>
                        @endif --}}

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-blue-700">Total Product</span>
                            <span class="text-sm font-bold text-blue-900">{{ count($cart) }}</span>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-blue-700">Grand Total</span>
                            <span class="text-xl font-bold text-blue-900">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </span>
                        </div>

                        @if ($discountTotal > 0)
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-sm font-semibold text-blue-700">Diskon</span>
                                <span class="text-sm font-bold text-red-700">
                                    - Rp {{ number_format($discountTotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-blue-700">Pembayaran</span>
                            <span class="text-sm font-bold uppercase text-blue-900">
                                {{ $paymentMethod }}
                            </span>
                        </div>
                    </div>

                    @if ($paymentMethod === 'cash')
                        <div class="mt-4">
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Uang Diterima
                            </label>
                            <div
                                x-data="{
                                    raw: 0,
                                    display: '',
                                    grandTotal: {{ (int) $grandTotal }},
                                    format(value) {
                                        const number = String(value ?? '').replace(/\D/g, '');

                                        if (number === '') {
                                            this.display = '';
                                            this.raw = 0;
                                            return;
                                        }

                                        const val = parseInt(number, 10);
                                        this.raw = val;
                                        this.display = new Intl.NumberFormat('id-ID').format(val);
                                    },
                                    applyPaid() {
                                        const id = this.$el.closest('[wire\\:id]').getAttribute('wire:id');
                                        window.Livewire.find(id).set('paidAmount', this.raw);
                                    },
                                    get change() {
                                        return Math.max(0, this.raw - this.grandTotal);
                                    },
                                    get isEnough() {
                                        return this.raw >= this.grandTotal;
                                    }
                                }"
                                x-init="format(raw)"
                                class="space-y-3"
                            >
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-500">
                                        Rp
                                    </span>

                                    <input
                                        x-ref="paidAmountInput"
                                        type="text"
                                        inputmode="numeric"
                                        x-model="display"
                                        x-on:input="format($event.target.value); applyPaid()"
                                        x-on:keydown.enter.prevent="applyPaid()"
                                        placeholder="0"
                                        autofocus
                                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 pl-12 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    >
                                </div>

                                <div class="flex gap-2">
                                    <template x-for="amount in [{{ (int) $grandTotal }}, {{ (int) ceil($grandTotal / 10000) * 10000 }}, {{ (int) ceil($grandTotal / 50000) * 50000 }}, {{ (int) ceil($grandTotal / 100000) * 100000 }}]" :key="amount">
                                        <button
                                            type="button"
                                            x-show="amount >= grandTotal"
                                            x-on:click="raw = amount; format(amount); applyPaid()"
                                            x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(amount)"
                                            class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition"
                                        ></button>
                                    </template>
                                </div>

                                <div
                                    x-show="raw > 0"
                                    x-cloak
                                    class="rounded-2xl p-4 transition-colors"
                                    x-bind:class="isEnough ? 'bg-green-50' : 'bg-red-50'"
                                >
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="text-sm font-semibold"
                                            x-bind:class="isEnough ? 'text-green-700' : 'text-red-700'"
                                        >Kembalian</span>
                                        <span
                                            class="text-lg font-bold"
                                            x-bind:class="isEnough ? 'text-green-900' : 'text-red-900'"
                                            x-text="isEnough
                                                ? 'Rp ' + new Intl.NumberFormat('id-ID').format(change)
                                                : 'Kurang Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal - raw)"
                                        ></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

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

    @if ($showChangeModal && $completedSaleId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-md rounded-3xl bg-white shadow-2xl">
                <div class="p-8 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900">Transaksi Berhasil</h3>
                    <p class="mt-1 text-sm text-slate-500">Invoice {{ $completedInvoiceNumber }}</p>

                    <div class="mt-6 rounded-2xl bg-amber-50 border border-amber-200 p-5">
                        <p class="text-sm font-semibold text-amber-700 mb-1">Kembalian untuk customer</p>
                        <p class="text-3xl font-bold text-amber-900">
                            Rp {{ number_format($completedChangeAmount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-3 border-t border-slate-100 px-6 py-5">
                    <button
                        type="button"
                        wire:click="closeChangeModal"
                        class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Lanjut
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showCustomerModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-xl rounded-3xl bg-white shadow-2xl">

                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            Data Customer
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Opsional untuk transaksi walk-in.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeCustomerModal"
                        class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-200"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-4 p-6" wire:key="customer-form-{{ $customerFormKey }}">

                    {{-- field rusak --}}
                    {{-- <div>
                        {{-- <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Cari Customer Lama
                            </label>

                            <input
                                type="text"
                                wire:model.live="customerSearch"
                                placeholder="Cari berdasarkan nama atau nomor HP..."
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >

                            @if ($customerSearch)
                                <div class="mt-3 space-y-2">
                                    @forelse ($customers as $customer)
                                        <button
                                            type="button"
                                            wire:click="selectCustomer({{ $customer->id }})"
                                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-left hover:border-blue-300 hover:bg-blue-50"
                                        >
                                            <p class="text-sm font-bold text-slate-900">
                                                {{ $customer->name }}
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                {{ $customer->phone ?: 'Tanpa nomor HP' }}
                                            </p>
                                        </button>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                                            Customer tidak ditemukan. Isi data di bawah untuk membuat customer baru.
                                        </div>
                                    @endforelse
                                </div>
                            @endif
                        </div>
                        <div class="flex justify-end">
                            <button
                                type="button"
                                wire:click="resetCustomer"
                                class="text-xs font-semibold text-red-600 hover:text-red-700"
                            >
                                Gunakan Walk-in Customer
                            </button>
                    </div> --}}

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Tipe Customer
                        </label>

                        <select
                            wire:model.live="customerType"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"
                        >
                            <option value="personal">Perorangan</option>
                            <option value="store">Toko</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Nama Customer / Toko
                        </label>

                        <div class="relative">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="customerName"
                                placeholder="Ketik nama customer atau toko..."
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >

                            @if ($customerName && ! $selectedCustomerId && $customers->count() > 0)
                                <div class="absolute z-50 mt-2 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                                    @foreach ($customers as $customer)
                                        <button
                                            type="button"
                                            wire:click="selectCustomer({{ $customer->id }})"
                                            class="w-full px-4 py-3 text-left hover:bg-blue-50"
                                        >
                                            <p class="text-sm font-bold text-slate-900">
                                                {{ $customer->name }}
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                {{ $customer->phone ?: 'Tanpa nomor HP' }}
                                            </p>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @error('customerName')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        @if ($selectedCustomerId)
                            <div class="mt-2 flex items-center justify-between rounded-xl bg-blue-50 px-3 py-2">
                                <p class="text-xs font-semibold text-blue-700">
                                    Customer lama dipilih
                                </p>

                                <button
                                    type="button"
                                    wire:click="resetCustomer"
                                    class="text-xs font-semibold text-red-600 hover:text-red-700"
                                >
                                    Ubah / Walk-in
                                </button>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Nomor HP
                        </label>

                        <input
                            type="text"
                            inputmode="numeric"
                            wire:model.live="customerPhone"
                            placeholder="82123456789"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>
                    @error('customerPhone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Alamat
                        </label>

                        <textarea
                            wire:model.live="customerAddress"
                            rows="3"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"
                        ></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-5">
                    <button
                        type="button"
                        wire:click="saveCustomerData"
                        wire:loading.attr="disabled"
                        class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
                    >
                        Selesai
                    </button>
                </div>

            </div>
        </div>
    @endif

    @if ($showReceiptModal && $completedSaleId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-md rounded-3xl bg-white shadow-2xl">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-xl font-bold text-slate-900">
                        Transaksi Selesai
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Invoice {{ $completedInvoiceNumber }} berhasil disimpan.
                    </p>
                </div>

                <div class="p-6">
                    <div class="rounded-2xl bg-green-50 px-4 py-3 text-sm text-green-700">
                        Pilih print struk untuk membuka halaman struk, atau buka detail invoice untuk cek transaksi lengkap.
                    </div>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-100 px-6 py-5 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        wire:click="closeReceiptModal"
                        class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                    >
                        Transaksi Baru
                    </button>

                    <a
                        href="{{ route('sales.show', $completedSaleId) }}"
                        class="rounded-2xl bg-blue-50 px-5 py-3 text-center text-sm font-semibold text-blue-700 hover:bg-blue-100"
                    >
                        Detail Invoice
                    </a>

                    <a
                        href="{{ route('sales.receipt', ['sale' => $completedSaleId, 'print' => 1, 'redirect' => 'detail']) }}"
                        target="_blank"
                        class="rounded-2xl bg-blue-600 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-blue-700"
                    >
                        Print Struk
                    </a>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('clear-product-search', () => {
                const searchInput = document.getElementById('product-search-input');

                if (searchInput) {
                    searchInput.value = '';
                }
            });

            Livewire.on('focus-quantity', (event) => {
                const productId = event.productId;

                setTimeout(() => {
                    const input = document.getElementById(`cart-quantity-${productId}`);

                    if (input) {
                        input.focus();
                        input.select();
                    }
                }, 100);
            });
        });

        let barcodeBuffer = '';
        let barcodeTimer = null;
        let barcodeLastKeyTime = 0;

        document.addEventListener('keydown', function (event) {
            const activeElement = document.activeElement;
            const tagName = activeElement?.tagName;

            const isTypingField = ['INPUT', 'TEXTAREA', 'SELECT'].includes(tagName);

            /**
             * Scanner mode hanya aktif saat user TIDAK sedang mengetik di input biasa.
             * Jadi search product, form customer, quantity, dan input lain tidak akan terganggu.
             */
            if (isTypingField) {
                return;
            }

            const now = Date.now();
            const diff = now - barcodeLastKeyTime;
            barcodeLastKeyTime = now;

            if (event.key === 'Enter') {
                if (barcodeBuffer.length >= 5) {
                    event.preventDefault();

                    Livewire.dispatch('barcode-scanned', {
                        barcode: barcodeBuffer
                    });

                    barcodeBuffer = '';
                }

                return;
            }

            if (event.key.length === 1) {
                if (diff > 80) {
                    barcodeBuffer = '';
                }

                barcodeBuffer += event.key;

                clearTimeout(barcodeTimer);

                barcodeTimer = setTimeout(() => {
                    barcodeBuffer = '';
                }, 200);
            }
        });
    </script>
</div>
