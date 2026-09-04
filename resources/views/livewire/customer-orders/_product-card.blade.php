<button
    type="button"
    wire:key="{{ $wireKey }}"
    wire:click="addToCart({{ $product->id }})"
    wire:loading.attr="disabled"
    wire:target="addToCart({{ $product->id }})"
    class="group flex h-full flex-col rounded-xl border border-slate-200 bg-white p-4 text-left transition hover:border-slate-400 hover:shadow-sm disabled:pointer-events-none disabled:opacity-60"
>
    <div class="flex items-start justify-between gap-2">
        <div class="min-w-0">
            <h3 class="text-sm font-semibold leading-snug text-slate-900">
                {{ $product->name }}
            </h3>

            <p class="mt-0.5 truncate text-[11px] font-medium tracking-wide text-slate-400">
                {{ $product->sku }}
            </p>
        </div>

        <span class="shrink-0 rounded-md bg-slate-100 px-1.5 py-0.5 text-[11px] font-semibold text-slate-500">
            {{ $product->unit?->abbreviation }}
        </span>
    </div>

    <div class="mt-auto pt-4">
        <div class="flex items-baseline gap-1.5">
            @if (isset($promoByProduct[$product->id]))
                <span class="text-base font-bold text-red-600">
                    Rp {{ number_format($promoByProduct[$product->id]['discounted_price'], 0, ',', '.') }}
                </span>

                <span class="text-xs text-slate-400 line-through">
                    Rp {{ number_format($promoByProduct[$product->id]['original_price'], 0, ',', '.') }}
                </span>
            @else
                <span class="text-base font-bold text-slate-900">
                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                </span>
            @endif
        </div>

        @if (isset($promoByProduct[$product->id]))
            <p class="mt-0.5 text-[11px] font-medium text-red-500">
                {{ $promoByProduct[$product->id]['promo']->name }} · {{ $promoByProduct[$product->id]['promo']->discountLabel() }}
            </p>
        @else
            <p class="mt-0.5 text-[11px]">&nbsp;</p>
        @endif

        @if (isset($bulkTierByProduct[$product->id]))
            <p class="text-[11px] text-slate-500">
                Grosir {{ $bulkTierByProduct[$product->id]['min_qty'] }}{{ $product->unit?->abbreviation }} → Rp {{ number_format($bulkTierByProduct[$product->id]['price'], 0, ',', '.') }}/{{ $product->unit?->abbreviation }}
            </p>
        @endif

        <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-2.5">
            <span class="text-[11px] text-slate-400">
                per {{ $product->unit?->abbreviation }}
            </span>

            <span class="text-xs font-semibold text-blue-600">
                + Tambah
            </span>
        </div>
    </div>
</button>
