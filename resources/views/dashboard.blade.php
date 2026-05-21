<x-pos-layout>
    <x-slot name="title">Dashboard - POS Ari Gita Grosir</x-slot>
    <x-slot name="header">Dashboard</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
        <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
            <p class="text-sm text-slate-500">Penjualan Hari Ini</p>
            <h3 class="mt-3 text-2xl font-bold text-slate-900">Rp 0</h3>
            <p class="mt-2 text-xs text-blue-600">Data akan aktif setelah modul transaksi dibuat</p>
        </div>

        <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
            <p class="text-sm text-slate-500">Total Transaksi</p>
            <h3 class="mt-3 text-2xl font-bold text-slate-900">0</h3>
            <p class="mt-2 text-xs text-blue-600">Menampilkan transaksi harian</p>
        </div>

        <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
            <p class="text-sm text-slate-500">Produk Aktif</p>
            <h3 class="mt-3 text-2xl font-bold text-slate-900">0</h3>
            <p class="mt-2 text-xs text-blue-600">Menunggu data master produk</p>
        </div>

        <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
            <p class="text-sm text-slate-500">Stok Minimum</p>
            <h3 class="mt-3 text-2xl font-bold text-slate-900">0</h3>
            <p class="mt-2 text-xs text-blue-600">Akan membaca produk dengan stok menipis</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Ringkasan Operasional</h3>
                    <p class="text-sm text-slate-500">Monitoring awal transaksi, stok, dan aktivitas sistem.</p>
                </div>
                <span class="rounded-full bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-700">
                    Prototype
                </span>
            </div>

            <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                <p class="text-sm text-slate-500">
                    Modul laporan akan terisi setelah fitur transaksi penjualan, stock movement,
                    dan invoice selesai dibuat.
                </p>
            </div>
        </div>

        <div class="rounded-3xl bg-blue-600 p-6 shadow-sm text-white">
            <p class="text-sm text-blue-100">Role Aktif</p>
            <h3 class="mt-3 text-2xl font-bold capitalize">{{ auth()->user()->role }}</h3>
            <p class="mt-4 text-sm text-blue-100 leading-relaxed">
                Hak akses pengguna akan menentukan menu dan fitur yang dapat digunakan
                pada sistem POS CV Ari Gita Grosir.
            </p>
        </div>
    </div>
</x-pos-layout>