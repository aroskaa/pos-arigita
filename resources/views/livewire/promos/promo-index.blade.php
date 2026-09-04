<div>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">Promo & Diskon</h3>
            <p class="mt-1 text-sm text-slate-500">Kelola promo produk, event diskon, dan pengaturan minimum order.</p>
        </div>

        <button
            type="button"
            wire:click="create"
            wire:loading.attr="disabled"
            class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
        >
            + Buat Promo
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mt-4 rounded-2xl bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="relative w-full md:max-w-xs">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari promo..."
                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100 text-left">
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Promo</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Diskon</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Periode</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Produk</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($promos as $promo)
                        <tr wire:key="promo-{{ $promo->id }}" class="border-b border-slate-100">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">{{ $promo->name }}</p>
                            </td>

                            <td class="px-4 py-4 text-sm font-semibold text-slate-900">
                                {{ $promo->discountLabel() }}
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $promo->starts_at?->format('d M Y H:i') }}
                                —
                                {{ $promo->ends_at?->format('d M Y H:i') ?? 'Tanpa batas' }}
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $promo->products->count() }} produk
                            </td>

                            <td class="px-4 py-4">
                                @if ($promo->is_active)
                                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">Aktif</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">Nonaktif</span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $promo->id }})"
                                        wire:loading.attr="disabled"
                                        class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="toggleActive({{ $promo->id }})"
                                        wire:loading.attr="disabled"
                                        class="rounded-xl px-3 py-2 text-xs font-semibold disabled:opacity-50 {{ $promo->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}"
                                    >
                                        {{ $promo->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="delete({{ $promo->id }})"
                                        wire:confirm="Yakin ingin menghapus promo ini?"
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
                                Belum ada promo. Klik "+ Buat Promo" untuk membuat promo baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $promos->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    {{-- Pengaturan Minimum Order --}}
    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div>
            <h4 class="text-lg font-bold text-slate-900">Pengaturan Minimum Order & Margin</h4>
            <p class="mt-1 text-sm text-slate-500">
                Batas minimal order pelanggan untuk keperluan pengiriman (isi 0 untuk menonaktifkan) dan margin laba minimum harga promo terhadap HPP.
            </p>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Minimal Total Order (Rp)</label>

                <div
                    x-data="{
                        raw: @entangle('minOrderTotal').live,
                        display: '',
                        format(value) {
                            const number = String(value ?? '').replace(/\D/g, '');

                            if (number === '') {
                                this.display = '';
                                this.raw = 0;
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

                <p class="mt-1 text-xs text-slate-500">Contoh: 500000 berarti pelanggan harus order minimal Rp 500.000.</p>

                @error('minOrderTotal')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Minimal Jumlah Unit</label>

                <div
                    x-data="{
                        raw: @entangle('minOrderQty').live,
                        display: '',
                        format(value) {
                            const number = String(value ?? '').replace(/\D/g, '');

                            if (number === '') {
                                this.display = '';
                                this.raw = 0;
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
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <p class="mt-1 text-xs text-slate-500">Contoh: 50 berarti pelanggan harus order minimal 50 unit (total semua produk).</p>

                @error('minOrderQty')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Margin Minimum Rp</label>

                <div
                    x-data="{
                        raw: @entangle('minMarginRp').live,
                        display: '',
                        format(value) {
                            const number = String(value ?? '').replace(/\D/g, '');

                            if (number === '') {
                                this.display = '';
                                this.raw = 0;
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
                        placeholder="500"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 pl-12 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <p class="mt-1 text-xs text-slate-500">Batas bawah margin laba harga promo terhadap HPP. Dipakai nilai yang lebih besar dari margin persen.</p>

                @error('minMarginRp')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Margin Minimum (%)</label>

                <div class="relative">
                    <input
                        type="number"
                        step="0.1"
                        min="0"
                        max="100"
                        wire:model="minMarginPct"
                        placeholder="2"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 pr-12 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-500">%</span>
                </div>

                <p class="mt-1 text-xs text-slate-500">Margin minimum sebagai persen HPP. Contoh: HPP 61.000 dengan margin 2% → harga promo minimal 62.220.</p>

                @error('minMarginPct')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-5 flex justify-end">
            <button
                type="button"
                wire:click="saveSettings"
                wire:loading.attr="disabled"
                class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
            >
                Simpan Pengaturan
            </button>
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-3xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            {{ $promoId ? 'Edit Promo' : 'Buat Promo' }}
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">Diskon otomatis berlaku di halaman order pelanggan dan POS selama periode aktif.</p>
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
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Promo</label>
                        <input
                            type="text"
                            wire:model="name"
                            placeholder="Contoh: Promo Gajian / Diskon Akhir Tahun"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Tipe Diskon</label>
                        <select
                            wire:model.live="type"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                            <option value="percent">Persen (%)</option>
                            <option value="fixed">Nominal (Rp)</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            {{ $type === 'percent' ? 'Nilai Diskon (%)' : 'Nilai Diskon (Rp)' }}
                        </label>

                        @if ($type === 'percent')
                            <input
                                type="number"
                                min="1"
                                max="100"
                                wire:model="value"
                                placeholder="0"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            >
                        @else
                            <div
                                x-data="{
                                    raw: @entangle('value').live,
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
                        @endif

                        @error('value')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Mulai</label>
                        <input
                            type="datetime-local"
                            wire:model="starts_at"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        @error('starts_at')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Selesai</label>
                        <input
                            type="datetime-local"
                            wire:model="ends_at"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >
                        <p class="mt-1 text-xs text-slate-500">Kosongkan jika tanpa batas waktu.</p>
                        @error('ends_at')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 flex items-center gap-3">
                        <input
                            type="checkbox"
                            wire:model="is_active"
                            id="promo-is-active"
                            class="h-5 w-5 rounded border-slate-300 text-blue-600"
                        >
                        <label for="promo-is-active" class="text-sm font-medium text-slate-700">Promo aktif</label>
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Pilih Produk
                            @if (count($productIds) > 0)
                                <span class="ml-1 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">
                                    {{ count($productIds) }} dipilih
                                </span>
                            @endif
                        </label>

                        <div class="relative mb-3">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="productSearch"
                                placeholder="Cari produk..."
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                            >
                        </div>

                        <div class="max-h-60 overflow-y-auto rounded-2xl border border-slate-200">
                            @forelse ($productOptions as $product)
                                <label
                                    wire:key="promo-product-{{ $product->id }}"
                                    class="flex cursor-pointer items-center gap-3 border-b border-slate-100 px-4 py-2.5 text-sm last:border-b-0 hover:bg-slate-50"
                                >
                                    <input
                                        type="checkbox"
                                        wire:model="productIds"
                                        value="{{ $product->id }}"
                                        class="h-4 w-4 rounded border-slate-300 text-blue-600"
                                    >
                                    <span class="font-medium text-slate-900">{{ $product->name }}</span>
                                    <span class="text-xs text-slate-400">{{ $product->sku }}</span>
                                </label>
                            @empty
                                <div class="px-4 py-6 text-center text-sm text-slate-500">
                                    Produk tidak ditemukan.
                                </div>
                            @endforelse
                        </div>

                        @error('productIds')
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
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                    >
                        Simpan Promo
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
