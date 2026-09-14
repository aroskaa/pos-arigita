<div>
    @if (session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 shadow-xs">
            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 items-start">
        <!-- FORM TABS (LEFT COLUMN) -->
        <div class="lg:col-span-7 xl:col-span-7 space-y-6">
            <!-- TABS SELECTOR -->
            <div class="flex flex-wrap gap-2 rounded-2xl border border-slate-200/80 bg-white p-2 shadow-xs">
                <button
                    type="button"
                    wire:click="setTab('business')"
                    class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs sm:text-sm font-bold transition-all {{ $activeTab === 'business' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                >
                    <span>🏢</span>
                    <span>Identitas Usaha</span>
                </button>

                <button
                    type="button"
                    wire:click="setTab('receipt')"
                    class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs sm:text-sm font-bold transition-all {{ $activeTab === 'receipt' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                >
                    <span>🧾</span>
                    <span>Struk Thermal</span>
                </button>

                <button
                    type="button"
                    wire:click="setTab('invoice')"
                    class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs sm:text-sm font-bold transition-all {{ $activeTab === 'invoice' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                >
                    <span>📑</span>
                    <span>Invoice & Bank</span>
                </button>

                <button
                    type="button"
                    wire:click="setTab('rules')"
                    class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs sm:text-sm font-bold transition-all {{ $activeTab === 'rules' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                >
                    <span>⚖️</span>
                    <span>Order & Margin</span>
                </button>
            </div>

            <!-- TAB 1: IDENTITAS BISNIS -->
            @if ($activeTab === 'business')
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-7 shadow-xs space-y-6">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Identitas & Informasi Usaha</h3>
                        <p class="mt-1 text-xs text-slate-500">Data ini digunakan pada kop laporan, cetakan dokumen, serta informasi toko.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Nama Toko / Perusahaan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="company_name"
                                placeholder="Contoh: CV Ari Gita Grosir"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                            @error('company_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Slogan / Tagline Bisnis
                            </label>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="company_tagline"
                                placeholder="Contoh: Distributor Minuman Grosir Terpercaya"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                            @error('company_tagline') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Nomor Telepon / WhatsApp
                            </label>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="company_phone"
                                placeholder="081234567890"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                            @error('company_phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Email Resmi
                            </label>
                            <input
                                type="email"
                                wire:model.live.debounce.300ms="company_email"
                                placeholder="arigitagrosir@gmail.com"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                            @error('company_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Alamat Lengkap Toko / Gudang
                            </label>
                            <textarea
                                rows="3"
                                wire:model.live.debounce.300ms="company_address"
                                placeholder="Jl. Cargo Permai No. 88, Denpasar Utara"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            ></textarea>
                            @error('company_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Kota / Daerah
                            </label>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="company_city"
                                placeholder="Denpasar, Bali"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                            @error('company_city') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Jam Operasional
                            </label>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="company_hours"
                                placeholder="Senin - Sabtu: 08:00 - 17:00"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                            @error('company_hours') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- TAB 2: FORMAT STRUK THERMAL -->
            @if ($activeTab === 'receipt')
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-7 shadow-xs space-y-6">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Format & Layout Struk Kasir</h3>
                        <p class="mt-1 text-xs text-slate-500">Konfigurasikan tampilan struk belanja thermal yang dicetak saat transaksi di POS.</p>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Judul Header Nota <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="receipt_header_title"
                                placeholder="STRUK TRANSAKSI PENJUALAN"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                            @error('receipt_header_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Ukuran Kertas Thermal Default
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center gap-3 rounded-2xl border p-4 cursor-pointer transition {{ $receipt_default_paper === '58' ? 'border-blue-600 bg-blue-50/50 text-blue-900' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
                                    <input type="radio" wire:model.live="receipt_default_paper" value="58" class="h-4 w-4 text-blue-600">
                                    <div>
                                        <p class="text-sm font-bold">58mm (Kecil)</p>
                                        <p class="text-[11px] text-slate-500">Printer thermal portable / bluetooth</p>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 rounded-2xl border p-4 cursor-pointer transition {{ $receipt_default_paper === '80' ? 'border-blue-600 bg-blue-50/50 text-blue-900' : 'border-slate-200 bg-white hover:bg-slate-50' }}">
                                    <input type="radio" wire:model.live="receipt_default_paper" value="80" class="h-4 w-4 text-blue-600">
                                    <div>
                                        <p class="text-sm font-bold">80mm (Standar)</p>
                                        <p class="text-[11px] text-slate-500">Printer thermal kasir meja / POS USB</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 space-y-3">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Elemen yang Ditampilkan</p>

                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-sm font-semibold text-slate-700">Tampilkan Logo Toko</span>
                                <input type="checkbox" wire:model.live="receipt_show_logo" class="h-5 w-5 rounded border-slate-300 text-blue-600">
                            </label>

                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-sm font-semibold text-slate-700">Tampilkan Alamat Toko di Header</span>
                                <input type="checkbox" wire:model.live="receipt_show_address" class="h-5 w-5 rounded border-slate-300 text-blue-600">
                            </label>

                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-sm font-semibold text-slate-700">Tampilkan No. Telepon di Header</span>
                                <input type="checkbox" wire:model.live="receipt_show_phone" class="h-5 w-5 rounded border-slate-300 text-blue-600">
                            </label>

                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-sm font-semibold text-slate-700">Tampilkan Nama Kasir</span>
                                <input type="checkbox" wire:model.live="receipt_show_cashier" class="h-5 w-5 rounded border-slate-300 text-blue-600">
                            </label>

                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-sm font-semibold text-slate-700">Tampilkan Nama Pelanggan</span>
                                <input type="checkbox" wire:model.live="receipt_show_customer" class="h-5 w-5 rounded border-slate-300 text-blue-600">
                            </label>

                            <label class="flex items-center justify-between cursor-pointer">
                                <span class="text-sm font-semibold text-slate-700">Tampilkan Tanggal & Jam Cetak</span>
                                <input type="checkbox" wire:model.live="receipt_show_timestamp" class="h-5 w-5 rounded border-slate-300 text-blue-600">
                            </label>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Pesan Terima Kasih (Footer)
                            </label>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="receipt_footer_thanks"
                                placeholder="Terima kasih atas pembelian Anda."
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                            @error('receipt_footer_thanks') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Catatan Kebijakan Barang (Footer)
                            </label>
                            <textarea
                                rows="2"
                                wire:model.live.debounce.300ms="receipt_footer_notice"
                                placeholder="Barang yang sudah dibeli harap diperiksa kembali."
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            ></textarea>
                            @error('receipt_footer_notice') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- TAB 3: INVOICE & REKENING -->
            @if ($activeTab === 'invoice')
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-7 shadow-xs space-y-6">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Format Invoice & Rekening Pembayaran</h3>
                        <p class="mt-1 text-xs text-slate-500">Informasi rekening bank dan ketentuan pembayaran yang tertera pada faktur resmi.</p>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Nama Bank
                                </label>
                                <input
                                    type="text"
                                    wire:model="invoice_bank_name"
                                    placeholder="Contoh: BCA / Mandiri / BRI"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Nomor Rekening
                                </label>
                                <input
                                    type="text"
                                    wire:model="invoice_bank_account"
                                    placeholder="Contoh: 1234567890"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Atas Nama
                                </label>
                                <input
                                    type="text"
                                    wire:model="invoice_bank_holder"
                                    placeholder="Contoh: CV Ari Gita Grosir"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Ketentuan Pembayaran (Terms)
                            </label>
                            <textarea
                                rows="3"
                                wire:model="invoice_terms"
                                placeholder="Pembayaran transfer wajib menyertakan nomor invoice pada berita transfer..."
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            ></textarea>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Catatan Kaki Invoice (Footer)
                            </label>
                            <textarea
                                rows="2"
                                wire:model="invoice_footer_note"
                                placeholder="Dokumen invoice ini sah dan diproses secara terkomputerisasi."
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            ></textarea>
                        </div>
                    </div>
                </div>
            @endif

            <!-- TAB 4: ATURAN ORDER & MARGIN (DIPINDAHKAN DARI PROMO) -->
            @if ($activeTab === 'rules')
                <div class="rounded-3xl border border-slate-200/80 bg-white p-6 sm:p-7 shadow-xs space-y-6">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Pengaturan Minimum Order & Margin</h3>
                        <p class="mt-1 text-xs text-slate-500">
                            Batas minimal pemesanan pelanggan untuk pengiriman bebas ongkir serta margin laba minimum harga diskon terhadap HPP.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Minimal Total Order (Rp)
                            </label>
                            <div
                                x-data="{
                                    raw: @entangle('min_order_total').live,
                                    display: '',
                                    format(value) {
                                        const number = String(value ?? '').replace(/\D/g, '');
                                        if (number === '') { this.display = ''; this.raw = 0; return; }
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
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 pl-12 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                            </div>
                            <p class="mt-1 text-xs text-slate-400">Isi 0 untuk menonaktifkan syarat nominal minimal.</p>
                            @error('min_order_total') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Minimal Jumlah Unit
                            </label>
                            <div
                                x-data="{
                                    raw: @entangle('min_order_qty').live,
                                    display: '',
                                    format(value) {
                                        const number = String(value ?? '').replace(/\D/g, '');
                                        if (number === '') { this.display = ''; this.raw = 0; return; }
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
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                            </div>
                            <p class="mt-1 text-xs text-slate-400">Contoh: 100 berarti pelanggan harus order minimal 100 unit.</p>
                            @error('min_order_qty') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Margin Minimum (Rp)
                            </label>
                            <div
                                x-data="{
                                    raw: @entangle('min_margin_rp').live,
                                    display: '',
                                    format(value) {
                                        const number = String(value ?? '').replace(/\D/g, '');
                                        if (number === '') { this.display = ''; this.raw = 0; return; }
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
                                    placeholder="500"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 pl-12 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                            </div>
                            <p class="mt-1 text-xs text-slate-400">Batas bawah laba promo terhadap HPP produk.</p>
                            @error('min_margin_rp') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Margin Minimum (%)
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    step="0.1"
                                    min="0"
                                    max="100"
                                    wire:model="min_margin_pct"
                                    placeholder="2"
                                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 pr-12 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                >
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-500">%</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-400">Persentase margin minimum dari modal HPP.</p>
                            @error('min_margin_pct') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- SAVE ACTION BAR -->
            <div class="flex items-center justify-between rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs">
                <div>
                    <p class="text-sm font-bold text-slate-900">Simpan Perubahan</p>
                    <p class="text-xs text-slate-500">Perubahan akan langsung diterapkan ke seluruh sistem.</p>
                </div>

                <button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-98 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>

        <!-- LIVE RECEIPT PREVIEW (RIGHT COLUMN) -->
        <div class="lg:col-span-5 xl:col-span-5 sticky top-24 space-y-4">
            <div class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h4 class="text-sm font-extrabold text-slate-900">Live Preview Struk</h4>
                    </div>

                    <!-- PAPER SIZE SWITCHER FOR PREVIEW -->
                    <div class="flex bg-slate-100 p-1 rounded-xl gap-1">
                        <button
                            type="button"
                            wire:click="setPreviewPaper('58')"
                            class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ $previewPaperSize === '58' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}"
                        >
                            58mm
                        </button>
                        <button
                            type="button"
                            wire:click="setPreviewPaper('80')"
                            class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ $previewPaperSize === '80' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}"
                        >
                            80mm
                        </button>
                    </div>
                </div>

                <!-- THERMAL RECEIPT CANVAS CONTAINER -->
                <div class="flex justify-center bg-slate-100/70 p-4 sm:p-6 rounded-2xl border border-slate-200/60 overflow-hidden">
                    <div
                        style="width: {{ $previewPaperSize === '58' ? '240px' : '320px' }}; font-family: 'Consolas', 'Courier New', Courier, monospace;"
                        class="bg-white p-4 shadow-md rounded-xl text-slate-900 text-xs transition-all duration-200 select-none"
                    >
                        <!-- HEADER -->
                        <div class="text-center space-y-1">
                            @if ($receipt_show_logo)
                                <div class="flex justify-center mb-1">
                                    <img src="{{ asset('images/logo-ag.png') }}" alt="Logo" class="h-7 w-auto object-contain">
                                </div>
                            @endif

                            <div class="font-extrabold uppercase text-sm tracking-tight leading-tight">
                                {{ $company_name ?: 'CV Ari Gita Grosir' }}
                            </div>

                            @if ($company_tagline)
                                <div class="text-[10px] text-slate-600 leading-tight">
                                    {{ $company_tagline }}
                                </div>
                            @endif

                            <div class="text-[10px] font-bold text-slate-800 uppercase tracking-wide pt-0.5">
                                {{ $receipt_header_title ?: 'STRUK TRANSAKSI PENJUALAN' }}
                            </div>

                            @if ($receipt_show_address && $company_address)
                                <div class="text-[9px] text-slate-500 leading-tight pt-0.5">
                                    {{ $company_address }}
                                </div>
                            @endif

                            @if ($receipt_show_phone && $company_phone)
                                <div class="text-[9px] text-slate-500">
                                    Telp: {{ $company_phone }}
                                </div>
                            @endif
                        </div>

                        <!-- DIVIDER -->
                        <div class="border-t border-dashed border-slate-400 my-2"></div>

                        <!-- META DATA -->
                        <div class="text-[10px] space-y-0.5">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Invoice</span>
                                <span class="font-bold">INV-20260914-0012</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Tanggal</span>
                                <span>14/09/2026 10:45</span>
                            </div>
                            @if ($receipt_show_cashier)
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Kasir</span>
                                    <span>{{ auth()->user()->name }}</span>
                                </div>
                            @endif
                            @if ($receipt_show_customer)
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Customer</span>
                                    <span>Toko Buana Sari</span>
                                </div>
                            @endif
                        </div>

                        <!-- DIVIDER -->
                        <div class="border-t border-dashed border-slate-400 my-2"></div>

                        <!-- SAMPLE ITEMS -->
                        <div class="space-y-1.5 text-[10px]">
                            <div>
                                <div class="font-bold truncate">Pocari Sweat 500ml Dus</div>
                                <div class="flex justify-between text-slate-600">
                                    <span>2 x 92.000</span>
                                    <span class="font-semibold text-slate-900">184.000</span>
                                </div>
                            </div>
                            <div>
                                <div class="font-bold truncate">Aqua 600ml Dus</div>
                                <div class="flex justify-between text-slate-600">
                                    <span>1 x 62.900</span>
                                    <span class="font-semibold text-slate-900">62.900</span>
                                </div>
                            </div>
                        </div>

                        <!-- DIVIDER -->
                        <div class="border-t border-dashed border-slate-400 my-2"></div>

                        <!-- TOTALS -->
                        <div class="text-[10px] space-y-1">
                            <div class="flex justify-between text-slate-600">
                                <span>Subtotal</span>
                                <span>Rp 246.900</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Diskon</span>
                                <span>- Rp 6.900</span>
                            </div>
                            <div class="flex justify-between font-extrabold text-xs pt-0.5 border-t border-slate-200">
                                <span>TOTAL</span>
                                <span>Rp 240.000</span>
                            </div>
                            <div class="flex justify-between text-slate-600 pt-0.5">
                                <span>Bayar (Tunai)</span>
                                <span>Rp 250.000</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span>Kembali</span>
                                <span>Rp 10.000</span>
                            </div>
                        </div>

                        <!-- DIVIDER -->
                        <div class="border-t border-dashed border-slate-400 my-2"></div>

                        <!-- FOOTER -->
                        <div class="text-center text-[9px] text-slate-600 space-y-1">
                            @if ($receipt_footer_thanks)
                                <p class="font-semibold">{{ $receipt_footer_thanks }}</p>
                            @endif

                            @if ($receipt_footer_notice)
                                <p class="text-slate-500 leading-tight">{{ $receipt_footer_notice }}</p>
                            @endif

                            @if ($receipt_show_timestamp)
                                <p class="text-[8px] text-slate-400 pt-0.5">{{ now()->format('d/m/Y H:i:s') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4 rounded-xl bg-blue-50/70 border border-blue-100 p-3 text-xs text-blue-700 flex items-start gap-2">
                    <span class="text-base">💡</span>
                    <span>Setiap perubahan pada form di samping akan langsung tercermin pada preview ini dan berlaku pada cetak struk POS kasir.</span>
                </div>
            </div>
        </div>
    </div>
</div>
