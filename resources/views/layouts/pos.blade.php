<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'POS Ari Gita Grosir' }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50/70 text-slate-900 antialiased">
    <div class="min-h-screen flex">
        <!-- SIDEBAR -->
        <aside class="hidden lg:flex lg:w-72 lg:flex-col bg-white border-r border-slate-200/80 h-screen sticky top-0 overflow-hidden shadow-sm">
            <div class="h-20 shrink-0 flex items-center gap-3 px-6 border-b border-slate-100">
                <div class="h-10 w-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold shadow-md shadow-blue-500/20">
                    AG
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900 tracking-tight">Ari Gita POS</h1>
                    <p class="text-xs font-semibold text-blue-600">Grosir Minuman</p>
                </div>
            </div>

            <nav class="mt-4 flex-1 space-y-4 px-4 overflow-y-auto">
                {{-- UTAMA --}}
                <div>
                    <p class="mb-1 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Utama
                    </p>

                    <div class="space-y-0.5">
                        <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span>🏠</span>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>

                {{-- TRANSAKSI --}}
                <div>
                    <p class="mb-1 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Transaksi
                    </p>

                    <div class="space-y-0.5">
                        @if(auth()->user()->hasRole(['owner', 'admin', 'cashier']))
                            <a href="{{ route('pos.index') }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                    {{ request()->routeIs('pos.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span>🧾</span>
                                <span>POS Transaksi</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasRole(['owner', 'admin']))
                            <a href="{{ route('customer-orders.index') }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                    {{ request()->routeIs('customer-orders.index') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span>🛒</span>
                                <span class="flex-1">Order Pelanggan</span>
                                @if(($posNotifications['pending_orders_count'] ?? 0) > 0)
                                    <span class="rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-bold text-red-700">
                                        {{ $posNotifications['pending_orders_count'] }}
                                    </span>
                                @endif
                            </a>
                        @endif

                        <a href="{{ route('sales.index') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                {{ request()->routeIs('sales.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span>📄</span>
                            <span>Riwayat Transaksi</span>
                        </a>
                    </div>
                </div>

                @if(auth()->user()->hasRole(['owner', 'admin']))
                    {{-- MASTER DATA --}}
                    <div>
                        <p class="mb-1 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Master Data
                        </p>

                        <div class="space-y-0.5">
                            <a href="{{ route('products.index') }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                    {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span>📦</span>
                                <span class="flex-1">Produk & Stok</span>
                                @if(($posNotifications['low_stock_count'] ?? 0) > 0)
                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-700">
                                        {{ $posNotifications['low_stock_count'] }}
                                    </span>
                                @endif
                            </a>

                            <a href="{{ route('suppliers.index') }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                    {{ request()->routeIs('suppliers.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span>🚚</span>
                                <span>Supplier</span>
                            </a>

                            <a href="{{ route('purchases.index') }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                    {{ request()->routeIs('purchases.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span>📥</span>
                                <span>Pembelian</span>
                            </a>

                            @if(auth()->user()->isOwner())
                                <a href="{{ route('users.index') }}"
                                class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                        {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <span>👥</span>
                                    <span>Pengguna</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- PERSEDIAAN --}}
                    <div>
                        <p class="mb-1 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Persediaan
                        </p>

                        <div class="space-y-0.5">
                            <a href="{{ route('stock-movements.index') }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                    {{ request()->routeIs('stock-movements.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span>📋</span>
                                <span>Kartu Stok</span>
                            </a>

                            <a href="{{ route('stock-adjustments.index') }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                    {{ request()->routeIs('stock-adjustments.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span>🛠️</span>
                                <span>Stock Adjustment</span>
                            </a>

                            <a href="{{ route('stock-daily-recaps.index') }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                    {{ request()->routeIs('stock-daily-recaps.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span>🧮</span>
                                <span>Rekap Stok</span>
                            </a>
                        </div>
                    </div>

                    {{-- ANALITIK --}}
                    <div>
                        <p class="mb-1 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Analitik
                        </p>

                        <div class="space-y-0.5">
                            <a href="{{ route('reports.index') }}"
                            class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                                    {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-700 font-bold border border-blue-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span>📊</span>
                                <span>Laporan</span>
                            </a>
                        </div>
                    </div>
                @endif
            </nav>

            <div class="p-3 shrink-0 border-t border-slate-100 bg-white">
                <div class="rounded-xl bg-slate-50 border border-slate-200/60 p-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-slate-900 leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-xs font-semibold text-slate-500 capitalize mt-0.5">{{ auth()->user()->role }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 hover:text-red-700 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1">
            <header class="h-20 bg-white border-b border-slate-200/80 flex items-center justify-between px-6 lg:px-8 shadow-sm">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">{{ $header ?? 'Dashboard' }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">Sistem POS berbasis web untuk integrasi transaksi dan stok.</p>
                </div>

                <div class="hidden md:flex items-center gap-3">
                    @if(($posNotifications['total_count'] ?? 0) > 0)
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <div class="flex items-center gap-2 rounded-full bg-red-50 px-4 py-2 text-sm font-bold text-red-700 cursor-pointer hover:bg-red-100 transition">
                                <span>{{ $posNotifications['total_count'] }}</span>
                                <span>Notifikasi</span>
                                <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>

                            <div
                                x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute right-0 top-full mt-2 w-80 rounded-2xl border border-slate-200 bg-white shadow-xl z-50 p-1"
                            >
                                @if(($posNotifications['pending_orders_count'] ?? 0) > 0)
                                    <div class="border-b border-slate-100 p-4">
                                        <p class="mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Order Masuk</p>
                                        <div class="space-y-2">
                                            @foreach($posNotifications['latest_pending_orders'] as $order)
                                                <a href="{{ route('customer-orders.index') }}" class="flex items-center justify-between rounded-xl bg-red-50 px-3 py-2 hover:bg-red-100 transition">
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-900">{{ $order->customer_name ?: 'Walk-in' }}</p>
                                                        <p class="text-xs text-slate-500">{{ $order->order_number }}</p>
                                                    </div>
                                                    <span class="rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-bold uppercase text-red-700">{{ $order->status }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                        @if($posNotifications['pending_orders_count'] > 3)
                                            <a href="{{ route('customer-orders.index') }}" class="mt-2 block text-center text-xs font-bold text-blue-600 hover:text-blue-800">Lihat semua ({{ $posNotifications['pending_orders_count'] }})</a>
                                        @endif
                                    </div>
                                @endif

                                @if(($posNotifications['low_stock_count'] ?? 0) > 0)
                                    <div class="p-4">
                                        <p class="mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Stok Rendah</p>
                                        <div class="space-y-2">
                                            @foreach($posNotifications['low_stock_products'] as $product)
                                                <a href="{{ route('products.index') }}" class="flex items-center justify-between rounded-xl bg-amber-50 px-3 py-2 hover:bg-amber-100 transition">
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-900">{{ $product->name }}</p>
                                                        <p class="text-xs text-slate-500">Min: {{ $product->minimum_stock }} {{ $product->unit?->abbreviation }}</p>
                                                    </div>
                                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-700">{{ $product->stock }} {{ $product->unit?->abbreviation }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                        @if($posNotifications['low_stock_count'] > 3)
                                            <a href="{{ route('products.index') }}" class="mt-2 block text-center text-xs font-bold text-blue-600 hover:text-blue-800">Lihat semua ({{ $posNotifications['low_stock_count'] }})</a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700">
                            Operasional normal
                        </div>
                    @endif

                    <div class="rounded-full bg-blue-50 border border-blue-100 px-4 py-2 text-sm font-medium text-blue-700">
                        <div
                            x-data="{
                                time: '',
                                updateTime() {
                                    const now = new Date();

                                    this.time = new Intl.DateTimeFormat('id-ID', {
                                        day: '2-digit',
                                        month: 'short',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit',
                                        hour12: false,
                                        timeZone: 'Asia/Makassar',
                                    }).format(now).replace(/\./g, ':');
                                }
                            }"
                            x-init="updateTime(); setInterval(() => updateTime(), 1000)"
                            class="text-xs font-bold text-blue-700"
                        >
                            <span x-text="time"></span>
                        </div>
                    </div>
                </div>
            </header>

            <section class="p-6 lg:p-8">
                {{ $slot }}
            </section>
        </main>
    </div>

    @livewireScripts
</body>
</html>
