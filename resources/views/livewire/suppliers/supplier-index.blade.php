<div>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">
                Supplier
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Kelola data supplier untuk kebutuhan pembelian dan barang masuk.
            </p>
        </div>

        <button
            type="button"
            wire:click="create"
            wire:loading.attr="disabled"
            class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
        >
            + Tambah Supplier
        </button>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari supplier..."
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
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Supplier</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Kontak</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Alamat</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr wire:key="supplier-{{ $supplier->id }}" class="border-b border-slate-100">
                            <td class="px-4 py-4">
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        {{ $supplier->name }}
                                    </p>

                                    @if ($supplier->note)
                                        <p class="text-xs text-slate-500">
                                            {{ Str::limit($supplier->note, 45) }}
                                        </p>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $supplier->phone ?: '-' }}
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $supplier->address ? Str::limit($supplier->address, 55) : '-' }}
                            </td>

                            <td class="px-4 py-4">
                                @if ($supplier->is_active)
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Aktif
                                    </span>
                                @else
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $supplier->id }})"
                                        wire:loading.attr="disabled"
                                        class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="toggleStatus({{ $supplier->id }})"
                                        wire:loading.attr="disabled"
                                        class="rounded-xl {{ $supplier->is_active ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }} px-3 py-2 text-xs font-semibold disabled:opacity-50"
                                    >
                                        {{ $supplier->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada data supplier.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $suppliers->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-2xl rounded-3xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            {{ $supplierId ? 'Edit Supplier' : 'Tambah Supplier' }}
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Data supplier digunakan pada proses pembelian barang.
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
                            Nama Supplier
                        </label>

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
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Nomor HP
                        </label>

                        <input
                            type="text"
                            inputmode="numeric"
                            wire:model.live.debounce.300ms="phone"
                            placeholder="82123456789"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Alamat
                        </label>

                        <textarea
                            wire:model.live="address"
                            rows="3"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        ></textarea>

                        @error('address')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Catatan
                        </label>

                        <textarea
                            wire:model.live="note"
                            rows="3"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        ></textarea>

                        @error('note')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3">
                        <input
                            type="checkbox"
                            wire:model.live="is_active"
                            class="h-5 w-5 rounded border-slate-300 text-blue-600"
                        >

                        <span class="text-sm font-medium text-slate-700">
                            Supplier aktif
                        </span>
                    </label>
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
                        Simpan Supplier
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>