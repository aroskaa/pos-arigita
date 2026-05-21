<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'POS Ari Gita Grosir' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen flex">
        <aside class="hidden lg:flex lg:w-72 lg:flex-col bg-white border-r border-slate-200">
            <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-100">
                <div class="h-11 w-11 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-bold">
                    AG
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900">Ari Gita POS</h1>
                    <p class="text-xs text-slate-500">Grosir Minuman</p>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>🏠</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('pos.index') }}"
                   class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('pos.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>🧾</span>
                    <span>POS Transaksi</span>
                </a>

                @if(auth()->user()->hasRole(['owner', 'admin']))
                    <a href="{{ route('products.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>📦</span>
                        <span>Produk & Stok</span>
                    </a>

                    <a href="{{ route('reports.index') }}"
                       class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>📊</span>
                        <span>Laporan</span>
                    </a>
                @endif
            </nav>

            <div class="p-4 border-t border-slate-100">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</p>

                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button class="text-sm font-medium text-red-600 hover:text-red-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-1">
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-6 lg:px-8">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $header ?? 'Dashboard' }}</h2>
                    <p class="text-sm text-slate-500">Sistem POS berbasis web untuk integrasi transaksi dan stok.</p>
                </div>

                <div class="hidden md:flex items-center gap-3">
                    <div class="rounded-full bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700">
                        {{ now()->format('d M Y') }}
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