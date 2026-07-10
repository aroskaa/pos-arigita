<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportDownloadController extends Controller
{
    public function __invoke(Request $request, string $type)
    {
        abort_unless(in_array($type, [
            'financial',
            'sales',
            'inventory',
            'stock-movements',
            'activity-logs',
        ], true), 404);

        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'product_search' => ['nullable', 'string', 'max:255'],
            'movement_type' => ['nullable', 'string', 'max:255'],
            'log_search' => ['nullable', 'string', 'max:255'],
            'log_event' => ['nullable', 'string', 'max:255'],
        ]);

        $startDate = Carbon::parse($validated['start_date'] ?? now()->startOfMonth())->startOfDay();
        $endDate = Carbon::parse($validated['end_date'] ?? now())->endOfDay();
        $productSearch = $validated['product_search'] ?? null;
        $movementType = $validated['movement_type'] ?? null;
        $logSearch = $validated['log_search'] ?? null;
        $logEvent = $validated['log_event'] ?? null;

        $data = match ($type) {
            'financial' => $this->financialData($startDate, $endDate),
            'sales' => $this->salesData($startDate, $endDate),
            'inventory' => $this->inventoryData($productSearch),
            'stock-movements' => $this->stockMovementData($startDate, $endDate, $movementType, $productSearch),
            'activity-logs' => $this->activityLogData($startDate, $endDate, $logEvent, $logSearch),
        };

        $pdf = Pdf::loadView('pdf.business-report', [
            ...$data,
            'type' => $type,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now(),
            'generatedBy' => $request->user()->name,
        ])->setPaper('a4', $type === 'inventory' ? 'landscape' : 'portrait');

        return $pdf->download($type . '-' . $startDate->format('Y-m-d') . '-' . $endDate->format('Y-m-d') . '.pdf');
    }

    private function financialData(Carbon $startDate, Carbon $endDate): array
    {
        $sales = Sale::query()
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$startDate, $endDate]);

        $grossSales = (float) (clone $sales)->sum('subtotal');
        $discountTotal = (float) (clone $sales)->sum('discount_total');
        $netSales = (float) (clone $sales)->sum('grand_total');
        $costTotal = (float) SaleItem::query()
            ->whereHas('sale', fn (Builder $query) => $query
                ->where('status', 'completed')
                ->whereBetween('sale_date', [$startDate, $endDate]))
            ->selectRaw('COALESCE(SUM(quantity * cost_price), 0) as total_cost')
            ->value('total_cost');

        return [
            'title' => 'Laporan Keuangan',
            'summary' => [
                'Penjualan Kotor' => $grossSales,
                'Total Diskon' => $discountTotal,
                'Penjualan Bersih' => $netSales,
                'Harga Pokok Penjualan' => $costTotal,
                'Laba Kotor' => $netSales - $costTotal,
            ],
            'rows' => (clone $sales)
                ->with(['customer', 'cashier'])
                ->latest('sale_date')
                ->get(),
        ];
    }

    private function salesData(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'title' => 'Laporan Penjualan',
            'rows' => Sale::query()
                ->with(['customer', 'cashier', 'items.product'])
                ->where('status', 'completed')
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->latest('sale_date')
                ->get(),
        ];
    }

    private function inventoryData(?string $productSearch): array
    {
        return [
            'title' => 'Laporan Barang',
            'rows' => Product::query()
                ->with(['category', 'unit'])
                ->when($productSearch, function (Builder $query) use ($productSearch) {
                    $query->where(function (Builder $productQuery) use ($productSearch) {
                        $productQuery->where('name', 'like', '%' . $productSearch . '%')
                            ->orWhere('sku', 'like', '%' . $productSearch . '%')
                            ->orWhere('barcode', 'like', '%' . $productSearch . '%');
                    });
                })
                ->orderBy('name')
                ->get(),
        ];
    }

    private function stockMovementData(Carbon $startDate, Carbon $endDate, ?string $movementType, ?string $productSearch): array
    {
        return [
            'title' => 'Laporan Pergerakan Stok',
            'rows' => StockMovement::query()
                ->with(['product', 'creator'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($movementType, fn (Builder $query) => $query->where('type', $movementType))
                ->when($productSearch, function (Builder $query) use ($productSearch) {
                    $query->whereHas('product', function (Builder $productQuery) use ($productSearch) {
                        $productQuery->where('name', 'like', '%' . $productSearch . '%')
                            ->orWhere('sku', 'like', '%' . $productSearch . '%')
                            ->orWhere('barcode', 'like', '%' . $productSearch . '%');
                    });
                })
                ->latest()
                ->get(),
        ];
    }

    private function activityLogData(Carbon $startDate, Carbon $endDate, ?string $logEvent, ?string $logSearch): array
    {
        return [
            'title' => 'Laporan Aktivitas Sistem',
            'rows' => ActivityLog::query()
                ->with('user')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($logEvent, fn (Builder $query) => $query->where('event', $logEvent))
                ->when($logSearch, function (Builder $query) use ($logSearch) {
                    $query->where(function (Builder $logQuery) use ($logSearch) {
                        $logQuery->where('description', 'like', '%' . $logSearch . '%')
                            ->orWhere('event', 'like', '%' . $logSearch . '%')
                            ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', '%' . $logSearch . '%'));
                    });
                })
                ->latest()
                ->get(),
        ];
    }
}
