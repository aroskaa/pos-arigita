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
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Download PDF Rekap
                </a>
            </div>
        </div>

        <div class="mt-6 rounded-2xl bg-blue-50 px-4 py-3 text-sm text-blue-700">
            Laporan akan digenerate dalam format PDF A4 landscape dan berisi seluruh produk dalam satu tabel rekap.
        </div>
    </div>
</div>