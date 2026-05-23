<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StockDailyRecapService
{
    public function generate(string $date): Collection
    {
        $date = Carbon::parse($date);

        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $products = Product::query()
            ->with('unit')
            ->orderBy('name')
            ->get();

        $movementsOnDate = StockMovement::query()
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->selectRaw('
                product_id,
                COALESCE(SUM(quantity_in), 0) as total_in,
                COALESCE(SUM(quantity_out), 0) as total_out
            ')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $movementsAfterDate = StockMovement::query()
            ->where('created_at', '>', $endOfDay)
            ->selectRaw('
                product_id,
                COALESCE(SUM(quantity_in), 0) as total_in,
                COALESCE(SUM(quantity_out), 0) as total_out
            ')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $lastMovementsBeforeOrOnDate = StockMovement::query()
            ->where('created_at', '<=', $endOfDay)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->groupBy('product_id')
            ->map(fn ($items) => $items->last());

        return $products->map(function ($product, int $index) use (
            $movementsOnDate,
            $movementsAfterDate,
            $lastMovementsBeforeOrOnDate
        ) {
            $movementToday = $movementsOnDate->get($product->id);

            $barangMasuk = $movementToday ? (int) $movementToday->total_in : 0;
            $barangKeluar = $movementToday ? (int) $movementToday->total_out : 0;

            $movementAfter = $movementsAfterDate->get($product->id);

            $netAfterDate = $movementAfter
                ? ((int) $movementAfter->total_in - (int) $movementAfter->total_out)
                : 0;

            $stokAkhirHari = (int) $product->stock - $netAfterDate;

            $stokAwalHari = $stokAkhirHari - $barangMasuk + $barangKeluar;

            $lastMovement = $lastMovementsBeforeOrOnDate->get($product->id);

            $averageCost = $lastMovement
                ? (float) $lastMovement->average_cost_after
                : (float) $product->average_cost;

            return [
                'no' => $index + 1,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit?->abbreviation ?? '-',
                'stok_awal' => $stokAwalHari,
                'barang_keluar' => $barangKeluar,
                'barang_masuk' => $barangMasuk,
                'stok_akhir' => $stokAkhirHari,
                'average_cost' => $averageCost,
            ];
        });
    }

    public function adjustments(string $date): Collection
    {
        $date = Carbon::parse($date);

        return StockMovement::query()
            ->with(['product', 'creator'])
            ->where('type', 'adjustment')
            ->whereBetween('created_at', [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
            ])
            ->orderBy('created_at')
            ->get()
            ->map(function (StockMovement $movement) {
                return [
                    'product_name' => $movement->product->name,
                    'sku' => $movement->product->sku,
                    'system_stock' => $movement->stock_before,
                    'physical_stock' => $movement->stock_after,
                    'difference' => $movement->stock_after - $movement->stock_before,
                    'note' => $movement->note,
                    'created_by' => $movement->creator?->name ?? '-',
                    'created_at' => $movement->created_at->format('H:i'),
                ];
            });
    }
}