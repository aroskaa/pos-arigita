<div>
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">
                Rekap Stok Harian
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Generate laporan PDF stok awal, barang masuk, barang keluar, stok akhir, dan average cost per produk.
            </p>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Tanggal Rekap
                </label>

                <input
                    type="date"
                    wire:model.live="recapDate"
                    class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >
            </div>

            <div class="flex items-end">
                <a
                    href="{{ route('stock-daily-recaps.download', ['date' => $recapDate]) }}"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 shadow-sm transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download PDF Rekap
                </a>
            </div>

            <div class="flex items-end">
                <a
                    href="{{ route('stock-daily-recaps.excel', ['date' => $recapDate]) }}"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700 shadow-sm transition"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Excel Rekap
                </a>
            </div>
        </div>

        <div class="mt-6 rounded-2xl bg-blue-50 px-4 py-3 text-sm text-blue-700">
            Laporan dapat didownload dalam format <strong>PDF (A4 Landscape)</strong> untuk cetak dokumen atau <strong>Excel (.xls)</strong> untuk analisis spreadsheet data stok awal, barang masuk, barang keluar, stok akhir, HPP, serta histori adjustment.
        </div>
    </div>
</div>