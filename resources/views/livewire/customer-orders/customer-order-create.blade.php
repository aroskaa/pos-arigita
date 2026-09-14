<div class="min-h-screen bg-slate-50">
    <nav style="position:sticky;top:0;z-index:100;width:100%;background:rgba(255,255,255,0.9);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border-bottom:1px solid rgba(226,232,240,0.9);box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div style="max-width:1320px;margin:0 auto;padding:12px 24px;display:flex;align-items:center;justify-content:space-between;">
            <!-- Brand -->
            <a href="/" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                <div style="width:38px;height:38px;border-radius:10px;background:#fff;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;padding:3px;box-shadow:0 2px 6px rgba(0,0,0,0.05);overflow:hidden;">
                    <img src="{{ asset('images/logo-ag.png') }}" alt="Ari Gita Grosir" style="width:100%;height:100%;object-fit:contain;">
                </div>
                <span style="font-size:1.1rem;font-weight:800;color:#0F172A;letter-spacing:-0.2px;">Ari Gita <span style="color:#2563EB;">Grosir</span></span>
            </a>
            <!-- Actions -->
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span style="font-size:0.9rem;font-weight:600;padding:8px 18px;border-radius:9999px;background:#2563EB;color:#fff;">Buat Order Baru</span>
                @auth
                    <a href="{{ route('customer-orders.history') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.9rem;font-weight:600;padding:8px 18px;border-radius:9999px;border:1px solid #e2e8f0;background:#f8fafc;color:#475569;text-decoration:none;">
                        <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Riwayat Order Saya
                    </a>
                    <span style="font-size:0.9rem;font-weight:600;padding:8px 18px;border-radius:9999px;background:#EFF6FF;color:#1D4ED8;">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="font-size:0.9rem;font-weight:600;padding:8px 18px;border-radius:9999px;border:1px solid #e2e8f0;background:#f8fafc;color:#475569;cursor:pointer;">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" style="font-size:0.9rem;font-weight:600;padding:8px 18px;border-radius:9999px;border:1px solid #e2e8f0;background:#f8fafc;color:#475569;text-decoration:none;">Masuk</a>
                    <a href="{{ route('register') }}" style="font-size:0.9rem;font-weight:600;padding:8px 18px;border-radius:9999px;background:#2563EB;color:#fff;text-decoration:none;">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 py-6 xl:grid-cols-3">
        <section class="xl:col-span-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            Pilih Produk
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Harga estimasi mengikuti aturan bulk pricing yang tersedia.
                        </p>
                    </div>
                </div>

                <div class="mt-5 flex items-stretch gap-3">
                    <input
                        id="customer-order-search"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari produk..."
                        autocomplete="off"
                        class="w-full rounded-2xl border border-slate-200 px-5 py-4 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >

                    <button
                        type="button"
                        wire:click="$toggle('filterPromoOnly')"
                        class="flex shrink-0 items-center gap-2 self-stretch rounded-xl border px-4 text-sm font-semibold transition
                                {{ $filterPromoOnly
                                    ? 'border-red-600 bg-red-600 text-white shadow-sm'
                                    : 'border-red-200 bg-red-50 text-red-600 hover:border-red-400 hover:bg-red-100' }}"
                    >
                        <span class="relative flex h-2 w-2">
                            @if ($filterPromoOnly)
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white opacity-75"></span>
                            @endif
                            <span class="relative inline-flex h-2 w-2 rounded-full {{ $filterPromoOnly ? 'bg-white' : 'bg-red-500' }}"></span>
                        </span>
                        Promo
                        @if ($promoProductCount > 0)
                            <span class="rounded-md px-1.5 py-0.5 text-[11px] font-bold {{ $filterPromoOnly ? 'bg-white/20 text-white' : 'bg-red-600 text-white' }}">
                                {{ $promoProductCount }}
                            </span>
                        @endif
                    </button>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-3 md:grid-cols-2 2xl:grid-cols-3">
                    @foreach ($featuredProducts as $product)
                        @include('livewire.customer-orders._product-card', ['wireKey' => 'customer-order-product-' . $product->id])
                    @endforeach

                    @forelse ($products as $product)
                        @include('livewire.customer-orders._product-card', ['wireKey' => 'customer-order-product-' . $product->id])
                    @empty
                        @if ($featuredProducts->isEmpty())
                            <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-sm text-slate-500">
                                {{ $filterPromoOnly ? 'Tidak ada produk promo saat ini.' : 'Produk tidak ditemukan.' }}
                            </div>
                        @endif
                    @endforelse
                </div>

                @if ($products->hasPages())
                    <div class="mt-6">
                        {{ $products->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        </section>

        <aside>
            <div class="sticky top-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">
                        Ringkasan Order
                    </h2>

                    {{-- <p class="mt-1 text-sm text-slate-500">
                        Order belum mengurangi stok sebelum diproses admin.
                    </p> --}}
                </div>

                @if ($showSuccess)
                    <div class="mt-5 rounded-2xl bg-green-50 p-4 text-sm text-green-700">
                        <p class="font-bold">Order berhasil dikirim.</p>

                        <p class="mt-1">
                            Nomor order: {{ $createdOrderNumber }}
                        </p>

                        <p class="mt-2">
                            Terima kasih. Mohon tunggu, admin kami akan menghubungi Anda dalam beberapa menit untuk konfirmasi pesanan.
                        </p>

                        <p class="mt-2 text-xs text-green-600">
                            Untuk mencegah duplikasi order, mohon tidak mengirim pesanan yang sama berulang kali dalam waktu dekat.
                        </p>
                    </div>
                @endif

                <style>
                    .cart-bubble-card {
                        position: relative;
                        background: #ffffff;
                        border: 1.5px solid #eef2f6;
                        border-radius: 22px;
                        padding: 16px;
                        box-shadow: 0 3px 12px -2px rgba(15, 23, 42, 0.04);
                        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
                    }
                    .cart-bubble-card:hover {
                        border-color: #cbd5e1;
                        box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.08);
                        transform: translateY(-1px);
                    }
                    .cart-bubble-delete {
                        width: 28px;
                        height: 28px;
                        border-radius: 9999px;
                        background: #f1f5f9;
                        border: 1px solid #e2e8f0;
                        color: #94a3b8;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        cursor: pointer;
                        transition: all 0.18s ease;
                        flex-shrink: 0;
                    }
                    .cart-bubble-delete:hover {
                        background: #fee2e2;
                        border-color: #fecaca;
                        color: #ef4444;
                        transform: scale(1.08);
                    }
                    .cart-bubble-stepper {
                        display: inline-flex;
                        align-items: center;
                        background: #f1f5f9;
                        border-radius: 9999px;
                        padding: 3px;
                        border: 1px solid #e2e8f0;
                    }
                    .cart-bubble-btn {
                        width: 28px;
                        height: 28px;
                        border-radius: 9999px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border: none;
                        cursor: pointer;
                        transition: all 0.15s ease;
                    }
                    .cart-bubble-btn-minus {
                        background: #ffffff;
                        color: #334155;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
                    }
                    .cart-bubble-btn-minus:hover {
                        background: #f8fafc;
                        color: #0f172a;
                    }
                    .cart-bubble-btn-minus:active {
                        transform: scale(0.92);
                    }
                    .cart-bubble-btn-plus {
                        background: #2563eb;
                        color: #ffffff;
                        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
                    }
                    .cart-bubble-btn-plus:hover {
                        background: #1d4ed8;
                    }
                    .cart-bubble-btn-plus:active {
                        transform: scale(0.92);
                    }
                    .cart-bubble-input {
                        width: 36px !important;
                        min-width: 36px !important;
                        max-width: 36px !important;
                        height: 28px !important;
                        line-height: 28px !important;
                        background: transparent !important;
                        border: none !important;
                        border-width: 0 !important;
                        box-shadow: none !important;
                        outline: none !important;
                        padding: 0 !important;
                        margin: 0 2px !important;
                        text-align: center !important;
                        font-size: 13px !important;
                        font-weight: 800 !important;
                        color: #0f172a !important;
                    }
                    .cart-bubble-input:focus {
                        border: none !important;
                        box-shadow: none !important;
                        outline: none !important;
                        --tw-ring-shadow: 0 0 #0000 !important;
                    }
                    .cart-scroll-bubble::-webkit-scrollbar {
                        width: 5px;
                    }
                    .cart-scroll-bubble::-webkit-scrollbar-track {
                        background: transparent;
                    }
                    .cart-scroll-bubble::-webkit-scrollbar-thumb {
                        background: #e2e8f0;
                        border-radius: 9999px;
                    }
                    .cart-scroll-bubble::-webkit-scrollbar-thumb:hover {
                        background: #cbd5e1;
                    }
                </style>

                <div class="mt-5 max-h-[380px] space-y-3.5 overflow-y-auto pr-1 cart-scroll-bubble">
                    @forelse ($cart as $item)
                        <div
                            wire:key="customer-order-cart-{{ $item['product_id'] }}"
                            class="cart-bubble-card"
                        >
                            <!-- Top: Product Header & Bubble Delete Button -->
                            <div class="flex items-start justify-between gap-2.5">
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-sm font-extrabold leading-snug tracking-tight text-slate-900">
                                        {{ $item['name'] }}
                                    </h4>

                                    <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 font-mono text-[10px] font-semibold text-slate-500">
                                            {{ $item['sku'] }}
                                        </span>

                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-[11px] font-bold text-blue-700">
                                            Rp {{ number_format($item['unit_price'], 0, ',', '.') }}<span class="ml-0.5 font-normal text-blue-500">/{{ $item['unit'] }}</span>
                                        </span>

                                        @if (isset($cartTierInfo[$item['product_id']]) && $cartTierInfo[$item['product_id']]['is_bulk'])
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-black text-emerald-800">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                                Grosir
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    wire:click="removeItem({{ $item['product_id'] }})"
                                    title="Hapus dari keranjang"
                                    class="cart-bubble-delete"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Bottom: Bubble Stepper & Subtotal -->
                            <div class="mt-3.5 flex items-center justify-between border-t border-slate-100 pt-3">
                                <div class="cart-bubble-stepper">
                                    <button
                                        type="button"
                                        wire:click="decreaseQuantity({{ $item['product_id'] }})"
                                        class="cart-bubble-btn cart-bubble-btn-minus"
                                        title="Kurangi kuantitas"
                                    >
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                        </svg>
                                    </button>

                                    <input
                                        id="customer-order-quantity-{{ $item['product_id'] }}"
                                        type="text"
                                        inputmode="numeric"
                                        value="{{ $item['quantity'] }}"
                                        wire:key="customer-order-quantity-{{ $item['product_id'] }}-{{ $item['quantity'] }}"
                                        wire:change="updateQuantity({{ $item['product_id'] }}, $event.target.value)"
                                        x-on:keydown.enter.prevent="document.getElementById('customer-order-search')?.focus()"
                                        class="cart-bubble-input"
                                    >

                                    <button
                                        type="button"
                                        wire:click="increaseQuantity({{ $item['product_id'] }})"
                                        class="cart-bubble-btn cart-bubble-btn-plus"
                                        title="Tambah kuantitas"
                                    >
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="text-right">
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Subtotal</span>
                                    <span class="text-base font-extrabold tracking-tight text-slate-900">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            @if (isset($cartTierInfo[$item['product_id']]) && $cartTierInfo[$item['product_id']]['next_min_qty'])
                                <div class="mt-3 flex items-center gap-2 rounded-xl border border-emerald-200/80 bg-emerald-50/80 px-2.5 py-1.5 text-xs text-emerald-800">
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white shadow-xs">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </span>
                                    <span class="text-[11px] leading-tight font-medium">
                                        Tambah <strong class="font-extrabold text-emerald-950">{{ $cartTierInfo[$item['product_id']]['next_min_qty'] - (int) $item['quantity'] }}</strong> lagi &rarr; <strong class="font-extrabold text-emerald-950">Rp {{ number_format($cartTierInfo[$item['product_id']]['next_price'], 0, ',', '.') }}</strong>/{{ $item['unit'] }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50/60 px-4 py-10 text-center">
                            <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl border border-slate-100 bg-white text-slate-400 shadow-sm">
                                <svg class="h-7 w-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                            <h5 class="text-sm font-bold text-slate-700">Keranjang Masih Kosong</h5>
                            <p class="mt-1 max-w-[200px] text-xs leading-relaxed text-slate-400">
                                Klik tombol <span class="font-semibold text-blue-600">+ Tambah</span> pada produk untuk memesan.
                            </p>
                        </div>
                    @endforelse
                </div>

                @error('cart')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-6 border-t border-slate-100 pt-5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-500">
                            Estimasi Total
                        </span>

                        <span class="text-xl font-bold text-slate-900">
                            Rp {{ number_format($estimatedTotal, 0, ',', '.') }}
                        </span>
                    </div>

                    @if ($minOrderTotal > 0 || $minOrderQty > 0)
                        <div class="mt-3 rounded-2xl p-4 text-sm {{ $this->meetsMinimumOrder() ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                            <p class="font-bold">{{ $this->meetsMinimumOrder() ? '✓ Gratis biaya pengiriman' : 'Info minimal order pengiriman' }}</p>

                            <p class="mt-1">
                                @if ($minOrderTotal > 0)
                                    Total minimal Rp {{ number_format($minOrderTotal, 0, ',', '.') }}.
                                @endif

                                @if ($minOrderQty > 0)
                                    Jumlah minimal {{ number_format($minOrderQty, 0, ',', '.') }} unit.
                                @endif
                            </p>

                            @if (! $this->meetsMinimumOrder())
                                <p class="mt-1 font-semibold">
                                    Order di bawah minimum tetap bisa dikirim, namun akan dikenakan biaya pengiriman.

                                    @if ($minOrderQty > 0 && $totalQuantity < $minOrderQty)
                                        (Tambah {{ number_format($minOrderQty - $totalQuantity, 0, ',', '.') }} unit lagi untuk bebas ongkir.)
                                    @elseif ($minOrderTotal > 0 && $estimatedTotal < $minOrderTotal)
                                        (Tambah Rp {{ number_format($minOrderTotal - $estimatedTotal, 0, ',', '.') }} lagi untuk bebas ongkir.)
                                    @endif
                                </p>
                            @else
                                <p class="mt-1">Order Anda sudah memenuhi minimal pengiriman.</p>
                            @endif
                        </div>
                    @endif
                </div>

                <div
                    class="mt-6 space-y-4 border-t border-slate-100 pt-5"
                    wire:key="customer-order-form-{{ $customerFormKey }}"
                >
                    @guest
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Tipe Pelanggan
                        </label>

                        <select
                            data-customer-order-input
                            wire:model.live="customerType"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="personal">Perorangan</option>
                            <option value="store">Toko</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Nama / Nama Toko
                        </label>

                        <input
                            data-customer-order-input
                            type="text"
                            wire:model.live="customerName"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        @error('customerName')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Nomor HP
                        </label>

                        <input
                            data-customer-order-input
                            type="text"
                            inputmode="numeric"
                            wire:model.live="customerPhone"
                            placeholder="82123456789"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        @error('customerPhone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Alamat
                        </label>

                        <textarea
                            data-customer-order-input
                            wire:model.live="customerAddress"
                            rows="3"
                            placeholder="Opsional, isi jika perlu pengiriman."
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        ></textarea>

                        @error('customerAddress')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endguest

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Catatan Order
                        </label>

                        <textarea
                            data-customer-order-input
                            wire:model.live="note"
                            rows="3"
                            placeholder="Contoh: dikirim siang, ambil sendiri, dan sebagainya."
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        ></textarea>
                    </div>

                    <button
                        type="button"
                        wire:click="submitOrder"
                        wire:loading.attr="disabled"
                        wire:target="submitOrder"
                        class="w-full rounded-2xl bg-blue-600 px-5 py-4 text-sm font-bold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="submitOrder">
                            Kirim Order
                        </span>

                        <span wire:loading wire:target="submitOrder">
                            Mengirim Order...
                        </span>
                    </button>
                </div>

                @error('submit')
                    <div class="mt-5 rounded-2xl bg-red-50 p-4 text-sm text-red-700">
                        {{ $message }}
                    </div>
                @enderror
                
            </div>
        </aside>
    </main>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('clear-customer-order-form', () => {
                setTimeout(() => {
                    document.querySelectorAll('[data-customer-order-input]').forEach((input) => {
                        if (input.tagName === 'SELECT') {
                            input.value = 'personal';
                        } else {
                            input.value = '';
                        }
                    });
                }, 100);
            });

            Livewire.on('focus-customer-order-quantity', (event) => {
                const productId = event.productId;

                setTimeout(() => {
                    const input = document.getElementById(`customer-order-quantity-${productId}`);

                    if (input) {
                        input.focus();
                        input.select();
                    }
                }, 100);
            });
        });
    </script>
</div>
