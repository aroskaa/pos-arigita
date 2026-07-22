@if ($paginator->hasPages())
    <div class="flex items-center justify-between border-t border-slate-100 pt-4">
        @if ($paginator->onFirstPage())
            <button
                type="button"
                disabled
                class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
            >
                Sebelumnya
            </button>
        @else
            <button
                type="button"
                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                wire:loading.attr="disabled"
                class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
            >
                Sebelumnya
            </button>
        @endif

        <span class="text-xs font-bold text-slate-500">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
        </span>

        @if ($paginator->hasMorePages())
            <button
                type="button"
                wire:click="nextPage('{{ $paginator->getPageName() }}')"
                wire:loading.attr="disabled"
                class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
            >
                Selanjutnya
            </button>
        @else
            <button
                type="button"
                disabled
                class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50"
            >
                Selanjutnya
            </button>
        @endif
    </div>
@endif
