<div>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">
                Order Pelanggan
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Kelola order yang masuk dari website pelanggan sebelum diproses ke POS.
            </p>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            {{-- Status Filter Pills --}}
            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    wire:click="setStatusFilter('')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-semibold transition {{ $status === '' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    <span>Semua Status</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $status === '' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $counts['all'] }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setStatusFilter('pending')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-semibold transition {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}"
                >
                    <span>Pending</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $status === 'pending' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-800' }}">
                        {{ $counts['pending'] }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setStatusFilter('preorder')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-semibold transition {{ $status === 'preorder' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}"
                >
                    <span>Preorder</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $status === 'preorder' ? 'bg-indigo-700 text-white' : 'bg-indigo-100 text-indigo-800' }}">
                        {{ $counts['preorder'] }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setStatusFilter('converted')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-semibold transition {{ $status === 'converted' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                >
                    <span>Converted</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $status === 'converted' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $counts['converted'] }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setStatusFilter('rejected')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-semibold transition {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}"
                >
                    <span>Rejected</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $status === 'rejected' ? 'bg-rose-700 text-white' : 'bg-rose-100 text-rose-800' }}">
                        {{ $counts['rejected'] }}
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setStatusFilter('cancelled')"
                    class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-semibold transition {{ $status === 'cancelled' ? 'bg-slate-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    <span>Cancelled</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $status === 'cancelled' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $counts['cancelled'] }}
                    </span>
                </button>
            </div>

            {{-- Search Box --}}
            <div class="relative w-full lg:w-80">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nomor order, nama, HP..."
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-11 pr-4 py-2.5 text-sm outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition"
                >
            </div>
        </div>

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
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Order</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Customer</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Kontak</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Item</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Estimasi</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Tanggal</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($orders as $order)
                        <tr wire:key="customer-order-{{ $order->id }}" class="border-b border-slate-100">
                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">
                                    {{ $order->order_number }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    {{ $order->customer_type === 'store' ? 'Toko' : 'Perorangan' }}
                                </p>
                            </td>

                            <td class="px-4 py-4">
                                <p class="font-semibold text-slate-900">
                                    {{ $order->customer_name }}
                                </p>

                                @if ($order->customer_address)
                                    <p class="text-xs text-slate-500">
                                        {{ Str::limit($order->customer_address, 45) }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-sm">
                                @php
                                    $waUrl = $this->whatsappUrl($order->customer_phone);
                                @endphp

                                @if ($waUrl)
                                    <a
                                        href="{{ $waUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="font-semibold text-blue-700 hover:underline"
                                    >
                                        {{ $order->customer_phone }}
                                    </a>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $order->items->count() }} item
                            </td>

                            <td class="px-4 py-4 text-sm font-bold text-slate-900">
                                Rp {{ number_format($order->estimated_total, 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold
                                    @if ($order->status === 'pending')
                                        bg-yellow-50 text-yellow-700
                                    @elseif ($order->status === 'preorder')
                                        bg-purple-50 text-purple-700
                                    @elseif ($order->status === 'converted')
                                        bg-blue-50 text-blue-700
                                    @elseif ($order->status === 'rejected')
                                        bg-red-50 text-red-700
                                    @else
                                        bg-slate-100 text-slate-700
                                    @endif
                                ">
                                    {{ $statuses[$order->status] ?? $order->status }}
                                </span>
                            </td>

                            <td class="px-4 py-4 text-sm text-slate-600">
                                {{ $order->created_at->format('d M Y H:i') }}
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="openDetail({{ $order->id }})"
                                        class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200"
                                    >
                                        Detail
                                    </button>

                                    @if ($order->status === 'pending')
                                        <button
                                            type="button"
                                            wire:click="processToPos({{ $order->id }})"
                                            class="rounded-xl bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                                        >
                                            Proses POS
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="openRejectModal({{ $order->id }})"
                                            class="rounded-xl bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100"
                                        >
                                            Tolak
                                        </button>
                                    @endif

                                    @if ($order->status === 'preorder')
                                        <button
                                            type="button"
                                            wire:click="processAvailableToPos({{ $order->id }})"
                                            class="rounded-xl bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                                        >
                                            Proses Stok Ada
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="markPreorderAsPending({{ $order->id }})"
                                            class="rounded-xl bg-purple-50 px-3 py-2 text-xs font-semibold text-purple-700 hover:bg-purple-100"
                                        >
                                            Jadikan Pending
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="openRejectModal({{ $order->id }})"
                                            class="rounded-xl bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100"
                                        >
                                            Tolak
                                        </button>
                                    @endif

                                    @if ($order->status === 'converted' && $order->sale?->status !== 'cancelled')
                                        <button
                                            type="button"
                                            wire:click="openCancelModal({{ $order->id }})"
                                            class="rounded-xl bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100"
                                        >
                                            Cancel
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">
                                Belum ada order pelanggan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $orders->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    @if ($showDetailModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-4xl rounded-3xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            Detail Order Pelanggan
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $detailOrder['order_number'] ?? '-' }}
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="$set('showDetailModal', false)"
                        class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-200"
                    >
                        ✕
                    </button>
                </div>

                <div class="max-h-[75vh] overflow-y-auto p-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase text-slate-400">Customer</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">
                                {{ $detailOrder['customer_name'] ?? '-' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ ($detailOrder['customer_type'] ?? '') === 'store' ? 'Toko' : 'Perorangan' }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase text-slate-400">WhatsApp</p>

                            @if (! empty($detailOrder['whatsapp_url']))
                                <a
                                    href="{{ $detailOrder['whatsapp_url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-1 block text-sm font-bold text-blue-700 hover:underline"
                                >
                                    {{ $detailOrder['customer_phone'] }}
                                </a>
                            @else
                                <p class="mt-1 text-sm font-bold text-slate-900">-</p>
                            @endif
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase text-slate-400">Status</p>
                            <p class="mt-1 text-sm font-bold text-slate-900">
                                {{ $statuses[$detailOrder['status'] ?? ''] ?? '-' }}
                            </p>
                        </div>
                    </div>

                    @if (! empty($detailOrder['converted_at']))
                        <div class="mt-4 rounded-2xl bg-blue-50 p-4">
                            <p class="text-xs font-semibold uppercase text-blue-500">
                                Informasi Konversi POS
                            </p>

                            @if (! empty($detailOrder['invoice_number']))
                                <p class="mt-2 text-sm text-blue-900">
                                    Invoice POS:
                                    <span class="font-bold">
                                        {{ $detailOrder['invoice_number'] }}
                                    </span>

                                    @if (! empty($detailOrder['sale_status']))
                                        <span class="ml-2 rounded-full bg-white px-2 py-1 text-xs font-semibold text-blue-700">
                                            {{ ucfirst($detailOrder['sale_status']) }}
                                        </span>
                                    @endif
                                </p>
                            @endif
                            
                            <p class="mt-1 text-sm text-blue-900">
                                Order ini telah diproses menjadi transaksi POS
                                oleh <span class="font-bold">{{ $detailOrder['converted_by'] ?? '-' }}</span>
                                pada <span class="font-bold">{{ $detailOrder['converted_at'] }}</span>.
                            </p>
                        </div>
                    @endif

                    @if (! empty($detailOrder['customer_address']))
                        <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase text-slate-400">Alamat</p>
                            <p class="mt-1 text-sm text-slate-700">
                                {{ $detailOrder['customer_address'] }}
                            </p>
                        </div>
                    @endif

                    @if (! empty($detailOrder['note']))
                        <div class="mt-4 rounded-2xl bg-blue-50 p-4">
                            <p class="text-xs font-semibold uppercase text-blue-500">Catatan Customer</p>
                            <p class="mt-1 text-sm text-blue-900">
                                {{ $detailOrder['note'] }}
                            </p>
                        </div>
                    @endif

                    <div class="mt-6 overflow-x-auto rounded-3xl border border-slate-200">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50 text-left">
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-600">Produk</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-600">Qty</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-600">Tersedia</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-600">Preorder</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-600">Harga</th>
                                    <th class="px-4 py-3 text-sm font-semibold text-slate-600">Subtotal</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach (($detailOrder['items'] ?? []) as $item)
                                    <tr class="border-b border-slate-100">
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-slate-900">
                                                {{ $item['product_name'] }}
                                            </p>

                                            <p class="text-xs text-slate-500">
                                                {{ $item['sku'] }}
                                            </p>
                                        </td>

                                        <td class="px-4 py-4 text-sm text-slate-600">
                                            {{ number_format($item['quantity'], 0, ',', '.') }}
                                        </td>

                                        <td class="px-4 py-4 text-sm font-semibold text-emerald-700">
                                            {{ number_format($item['available_quantity'] ?? 0, 0, ',', '.') }}
                                        </td>

                                        <td class="px-4 py-4 text-sm font-semibold {{ ($item['preorder_quantity'] ?? 0) > 0 ? 'text-purple-700' : 'text-slate-400' }}">
                                            {{ number_format($item['preorder_quantity'] ?? 0, 0, ',', '.') }}
                                        </td>

                                        <td class="px-4 py-4 text-sm text-slate-600">
                                            Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                        </td>

                                        <td class="px-4 py-4 text-sm font-bold text-slate-900">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <div class="w-full max-w-sm rounded-3xl bg-blue-50 p-5">
                            <p class="text-sm font-semibold text-blue-700">
                                Estimasi Total
                            </p>

                            <p class="mt-2 text-3xl font-bold text-blue-900">
                                Rp {{ number_format($detailOrder['estimated_total'] ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    @if (! empty($detailOrder['rejection_note']))
                        <div class="mt-4 rounded-2xl bg-red-50 p-4">
                            <p class="text-xs font-semibold uppercase text-red-500">Alasan Penolakan</p>
                            <p class="mt-1 text-sm text-red-900">
                                {{ $detailOrder['rejection_note'] }}
                            </p>
                        </div>
                    @endif

                    @if (! empty($detailOrder['cancel_note']))
                        <div class="mt-4 rounded-2xl bg-red-50 p-4">
                            <p class="text-xs font-semibold uppercase text-red-500">Alasan Pembatalan</p>
                            <p class="mt-1 text-sm text-red-900">
                                {{ $detailOrder['cancel_note'] }}
                            </p>

                            @if (! empty($detailOrder['cancelled_by']) || ! empty($detailOrder['cancelled_at']))
                                <p class="mt-2 text-xs text-red-700">
                                    Dibatalkan oleh {{ $detailOrder['cancelled_by'] ?? '-' }}
                                    pada {{ $detailOrder['cancelled_at'] ?? '-' }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-xl font-bold text-slate-900">
                        Tolak Order
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Masukkan alasan mengapa order pelanggan tidak dapat diproses.
                    </p>
                </div>

                <div class="p-6">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Alasan Penolakan
                    </label>

                    <textarea
                        wire:model.live="rejectionNote"
                        rows="4"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    ></textarea>

                    @error('rejectionNote')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-5">
                    <button
                        type="button"
                        wire:click="$set('showRejectModal', false)"
                        class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        wire:click="rejectOrder"
                        wire:loading.attr="disabled"
                        class="rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50"
                    >
                        Tolak Order
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showCancelModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
            <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-xl font-bold text-slate-900">
                        Cancel Converted Order
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Pembatalan akan mengembalikan stok seluruh item transaksi secara otomatis.
                    </p>
                </div>

                <div class="space-y-4 p-6">
                    @if ($cancelOrderPreview)
                        <div class="rounded-2xl bg-red-50 p-4">
                            <p class="text-xs font-semibold uppercase text-red-500">
                                Transaksi yang akan dibatalkan
                            </p>

                            <div class="mt-3 space-y-2 text-sm">
                                <div class="flex justify-between gap-3">
                                    <span class="text-red-700">Order</span>
                                    <span class="font-bold text-red-900">
                                        {{ $cancelOrderPreview['order_number'] }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-3">
                                    <span class="text-red-700">Invoice</span>
                                    <span class="font-bold text-red-900">
                                        {{ $cancelOrderPreview['invoice_number'] }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-3">
                                    <span class="text-red-700">Customer</span>
                                    <span class="font-bold text-red-900">
                                        {{ $cancelOrderPreview['customer_name'] }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-3">
                                    <span class="text-red-700">Total</span>
                                    <span class="font-bold text-red-900">
                                        Rp {{ number_format($cancelOrderPreview['grand_total'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">
                            Alasan Cancel Order
                        </label>

                        <textarea
                            wire:model.live="cancelReason"
                            rows="4"
                            placeholder="Contoh: Customer membatalkan pesanan melalui WhatsApp sebelum barang dikirim."
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        ></textarea>

                        @error('cancelReason')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-2xl bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                        Setelah diproses, invoice akan ditandai cancelled dan stok akan kembali melalui stock movement tipe
                        <strong>order_cancel</strong>.
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-5">
                    <button
                        type="button"
                        wire:click="$set('showCancelModal', false)"
                        class="rounded-2xl bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        wire:click="cancelConvertedOrder"
                        wire:loading.attr="disabled"
                        class="rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50"
                    >
                        Ya, Cancel Order
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
