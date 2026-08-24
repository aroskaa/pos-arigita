<div class="min-h-screen">
    {{-- Header --}}
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold text-blue-700">
                    CV Ari Gita Grosir
                </p>

                <h1 class="mt-1 text-3xl font-bold text-slate-900">
                    Riwayat Order Saya
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Lacak status pesanan, rincian produk, dan histori order grosir Anda.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('customer-orders.create') }}"
                    class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-200 hover:text-slate-900"
                >
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Buat Order Baru
                </a>

                <div class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm">
                    Riwayat Order Saya
                </div>

                @auth
                    <div class="rounded-2xl bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-700">
                        {{ auth()->user()->name }}
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8">
        {{-- Search & Status Filter Section --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                {{-- Status Filter Tabs --}}
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <button
                        type="button"
                        wire:click="setStatusFilter('all')"
                        class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold transition {{ $statusFilter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        <span>Semua</span>
                        <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $statusFilter === 'all' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-700' }}">
                            {{ $counts['all'] }}
                        </span>
                    </button>

                    <button
                        type="button"
                        wire:click="setStatusFilter('pending')"
                        class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold transition {{ $statusFilter === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}"
                    >
                        <span>Menunggu Konfirmasi</span>
                        <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-800' }}">
                            {{ $counts['pending'] }}
                        </span>
                    </button>

                    <button
                        type="button"
                        wire:click="setStatusFilter('converted')"
                        class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold transition {{ $statusFilter === 'converted' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                    >
                        <span>Diproses / Selesai</span>
                        <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $statusFilter === 'converted' ? 'bg-emerald-700 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $counts['converted'] }}
                        </span>
                    </button>

                    <button
                        type="button"
                        wire:click="setStatusFilter('rejected')"
                        class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold transition {{ $statusFilter === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}"
                    >
                        <span>Ditolak</span>
                        <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $statusFilter === 'rejected' ? 'bg-rose-700 text-white' : 'bg-rose-100 text-rose-800' }}">
                            {{ $counts['rejected'] }}
                        </span>
                    </button>

                    <button
                        type="button"
                        wire:click="setStatusFilter('cancelled')"
                        class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold transition {{ $statusFilter === 'cancelled' ? 'bg-slate-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        <span>Dibatalkan</span>
                        <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $statusFilter === 'cancelled' ? 'bg-slate-700 text-white' : 'bg-slate-200 text-slate-700' }}">
                            {{ $counts['cancelled'] }}
                        </span>
                    </button>
                </div>

                {{-- Search Box --}}
                <div class="relative w-full sm:w-64 lg:w-72 shrink-0">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari order / produk..."
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 pl-10 pr-9 py-2 text-sm outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100 transition"
                    >
                    @if ($search)
                        <button
                            type="button"
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Order List --}}
        <div class="mt-6 space-y-4">
            @forelse ($orders as $order)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-slate-300">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="rounded-2xl bg-blue-50 px-3.5 py-1.5 font-mono text-sm font-bold text-blue-700">
                                {{ $order->order_number }}
                            </span>

                            <span class="text-xs text-slate-500">
                                {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        {{-- Status Badges --}}
                        <div>
                            @if ($order->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3.5 py-1.5 text-xs font-semibold text-amber-700 border border-amber-200">
                                    <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Konfirmasi Admin
                                </span>
                            @elseif ($order->status === 'converted')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    Diproses / Selesai
                                </span>
                            @elseif ($order->status === 'rejected')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3.5 py-1.5 text-xs font-semibold text-rose-700 border border-rose-200">
                                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                    Order Ditolak
                                </span>
                            @elseif ($order->status === 'cancelled')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3.5 py-1.5 text-xs font-semibold text-slate-600 border border-slate-200">
                                    <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                    Dibatalkan
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Order Overview Body --}}
                    <div class="mt-4 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="space-y-1 text-sm">
                            <p class="text-slate-700">
                                <span class="font-medium text-slate-500">Penerima:</span>
                                <strong class="text-slate-900">{{ $order->customer_name }}</strong>
                                <span class="text-slate-400">({{ $order->customer_phone }})</span>
                            </p>

                            @if ($order->customer_address)
                                <p class="text-xs text-slate-500">
                                    <span class="font-medium">Alamat:</span> {{ $order->customer_address }}
                                </p>
                            @endif

                            @if ($order->note)
                                <p class="text-xs italic text-slate-500">
                                    <span class="font-medium">Catatan:</span> "{{ $order->note }}"
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="text-left sm:text-right">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                    Estimasi Total
                                </p>
                                <p class="text-xl font-bold text-slate-900">
                                    Rp {{ number_format($order->estimated_total, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $order->items->count() }} jenis produk ({{ $order->items->sum('quantity') }} unit)
                                </p>
                            </div>

                            <button
                                type="button"
                                wire:click="toggleDetail({{ $order->id }})"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition"
                            >
                                <span>{{ $selectedOrderId === $order->id ? 'Sembunyikan Detail' : 'Lihat Detail Produk' }}</span>
                                <svg class="h-4 w-4 transform transition-transform {{ $selectedOrderId === $order->id ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Rejection / Cancellation Notes Alert --}}
                    @if ($order->status === 'rejected' && $order->rejection_note)
                        <div class="mt-4 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800">
                            <strong class="font-bold block mb-1">Alasan Penolakan dari Admin:</strong>
                            {{ $order->rejection_note }}
                        </div>
                    @endif

                    @if ($order->status === 'cancelled' && $order->cancel_note)
                        <div class="mt-4 rounded-2xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-700">
                            <strong class="font-bold block mb-1">Catatan Pembatalan:</strong>
                            {{ $order->cancel_note }}
                        </div>
                    @endif

                    {{-- Expandable Item Details Table --}}
                    @if ($selectedOrderId === $order->id)
                        <div class="mt-5 border-t border-slate-100 pt-5">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">
                                Rincian Produk Pesanan
                            </h4>

                            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-slate-50/50">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-100 text-slate-600 font-semibold border-b border-slate-200">
                                        <tr>
                                            <th class="px-4 py-3">Produk</th>
                                            <th class="px-4 py-3 text-center">Jumlah</th>
                                            <th class="px-4 py-3 text-right">Harga Estimasi</th>
                                            <th class="px-4 py-3 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 text-slate-700">
                                        @foreach ($order->items as $item)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <strong class="font-semibold text-slate-900 block">{{ $item->product?->name ?? 'Produk Dihapus' }}</strong>
                                                    <span class="text-[10px] text-slate-400">{{ $item->product?->sku }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-center font-medium">
                                                    {{ number_format($item->quantity, 0, ',', '.') }} {{ $item->unit_abbreviation }}
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    Rp {{ number_format($item->estimated_unit_price, 0, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3 text-right font-bold text-slate-900">
                                                    Rp {{ number_format($item->estimated_subtotal, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-slate-100/80 font-bold border-t border-slate-200 text-slate-900">
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-right">Total Estimasi:</td>
                                            <td class="px-4 py-3 text-right text-blue-700 font-extrabold text-sm">
                                                Rp {{ number_format($order->estimated_total, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            @if ($order->sale)
                                <div class="mt-3 flex items-center justify-between rounded-2xl bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-800">
                                    <span>Penjualan Resmi Dibuat (Invoice: <strong>#{{ $order->sale->invoice_number }}</strong>)</span>
                                    <span class="font-bold text-emerald-700">Total Faktur: Rp {{ number_format($order->sale->grand_total, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-sm">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-4">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900">
                        Belum Ada Riwayat Pesanan
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        @if ($statusFilter !== 'all' || $search !== '')
                            Tidak ada order yang cocok dengan filter atau kata kunci pencarian Anda.
                        @else
                            Anda belum pernah membuat order barang grosir.
                        @endif
                    </p>

                    <div class="mt-6 flex justify-center gap-3">
                        @if ($statusFilter !== 'all' || $search !== '')
                            <button
                                type="button"
                                wire:click="$set('search', ''); $set('statusFilter', 'all')"
                                class="rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                            >
                                Reset Filter
                            </button>
                        @endif

                        <a
                            href="{{ route('customer-orders.create') }}"
                            class="rounded-2xl bg-blue-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700"
                        >
                            Buat Order Sekarang
                        </a>
                    </div>
                </div>
            @endforelse

            {{-- Pagination Links --}}
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>
    </main>
</div>
