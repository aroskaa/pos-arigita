<?php

namespace App\Livewire\Reports;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReportIndex extends Component
{
    public string $tab = 'sales';

    public string $startDate;

    public string $endDate;

    public string $productSearch = '';

    public string $movementType = '';

    public string $logSearch = '';

    public string $logEvent = '';

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['sales', 'inventory', 'logs'], true)) {
            return;
        }

        $this->tab = $tab;
    }

    public function resetFilters(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->productSearch = '';
        $this->movementType = '';
        $this->logSearch = '';
        $this->logEvent = '';
    }

    public function render()
    {
        return view('livewire.reports.report-index', [
            'salesSummary' => $this->salesSummary(),
            'dailySales' => $this->dailySales(),
            'topProducts' => $this->topProducts(),
            'latestSales' => $this->latestSales(),
            'inventorySummary' => $this->inventorySummary(),
            'lowStockProducts' => $this->lowStockProducts(),
            'movementSummary' => $this->movementSummary(),
            'recentMovements' => $this->recentMovements(),
            'activityLogs' => $this->activityLogs(),
            'logEvents' => ActivityLog::query()
                ->select('event')
                ->distinct()
                ->orderBy('event')
                ->pluck('event'),
        ]);
    }

    private function salesQuery(): Builder
    {
        return Sale::query()
            ->where('status', 'completed')
            ->whereBetween('sale_date', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ]);
    }

    private function salesSummary(): array
    {
        $sales = $this->salesQuery();

        $transactionCount = (int) (clone $sales)->count();
        $grossSales = (float) (clone $sales)->sum('grand_total');
        $discountTotal = (float) (clone $sales)->sum('discount_total');
        $costTotal = (float) SaleItem::query()
            ->whereHas('sale', fn (Builder $query) => $this->applySalesDateFilter($query))
            ->selectRaw('COALESCE(SUM(quantity * cost_price), 0) as total_cost')
            ->value('total_cost');

        return [
            'gross_sales' => $grossSales,
            'discount_total' => $discountTotal,
            'cost_total' => $costTotal,
            'gross_profit' => $grossSales - $costTotal,
            'transaction_count' => $transactionCount,
            'average_transaction' => $transactionCount > 0 ? $grossSales / $transactionCount : 0,
        ];
    }

    private function dailySales()
    {
        return $this->salesQuery()
            ->selectRaw('DATE(sale_date) as sale_day, COUNT(*) as transaction_count, COALESCE(SUM(grand_total), 0) as total_sales')
            ->groupBy('sale_day')
            ->orderBy('sale_day')
            ->get();
    }

    private function topProducts()
    {
        return SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.sale_date', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ])
            ->selectRaw('products.name, products.sku, SUM(sale_items.quantity) as total_quantity, SUM(sale_items.subtotal) as total_sales')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_quantity')
            ->limit(8)
            ->get();
    }

    private function latestSales()
    {
        return $this->salesQuery()
            ->with(['cashier', 'customer'])
            ->latest('sale_date')
            ->limit(12)
            ->get();
    }

    private function inventorySummary(): array
    {
        $products = Product::query();

        $activeProducts = (int) (clone $products)->where('is_active', true)->count();
        $totalStock = (int) (clone $products)->sum('stock');
        $inventoryValue = (float) Product::query()
            ->selectRaw('COALESCE(SUM(stock * average_cost), 0) as inventory_value')
            ->value('inventory_value');
        $lowStockCount = (int) Product::query()
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->where('minimum_stock', '>', 0)
            ->where('is_active', true)
            ->count();

        return [
            'active_products' => $activeProducts,
            'total_stock' => $totalStock,
            'inventory_value' => $inventoryValue,
            'low_stock_count' => $lowStockCount,
        ];
    }

    private function lowStockProducts()
    {
        return Product::query()
            ->with(['category', 'unit'])
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->where('minimum_stock', '>', 0)
            ->where('is_active', true)
            ->when($this->productSearch, function (Builder $query) {
                $query->where(function (Builder $productQuery) {
                    $productQuery->where('name', 'like', '%' . $this->productSearch . '%')
                        ->orWhere('sku', 'like', '%' . $this->productSearch . '%')
                        ->orWhere('barcode', 'like', '%' . $this->productSearch . '%');
                });
            })
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(15)
            ->get();
    }

    private function movementSummary()
    {
        return StockMovement::query()
            ->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ])
            ->selectRaw('type, SUM(quantity_in) as total_in, SUM(quantity_out) as total_out, COUNT(*) as movement_count')
            ->groupBy('type')
            ->orderBy('type')
            ->get();
    }

    private function recentMovements()
    {
        return StockMovement::query()
            ->with(['product', 'creator'])
            ->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ])
            ->when($this->movementType, fn (Builder $query) => $query->where('type', $this->movementType))
            ->when($this->productSearch, function (Builder $query) {
                $query->whereHas('product', function (Builder $productQuery) {
                    $productQuery->where('name', 'like', '%' . $this->productSearch . '%')
                        ->orWhere('sku', 'like', '%' . $this->productSearch . '%')
                        ->orWhere('barcode', 'like', '%' . $this->productSearch . '%');
                });
            })
            ->latest()
            ->limit(15)
            ->get();
    }

    private function activityLogs()
    {
        return ActivityLog::query()
            ->with('user')
            ->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ])
            ->when($this->logEvent, fn (Builder $query) => $query->where('event', $this->logEvent))
            ->when($this->logSearch, function (Builder $query) {
                $query->where(function (Builder $logQuery) {
                    $logQuery->where('description', 'like', '%' . $this->logSearch . '%')
                        ->orWhere('event', 'like', '%' . $this->logSearch . '%')
                        ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', '%' . $this->logSearch . '%'));
                });
            })
            ->latest()
            ->limit(30)
            ->get();
    }

    private function applySalesDateFilter(Builder $query): Builder
    {
        return $query
            ->where('status', 'completed')
            ->whereBetween('sale_date', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ]);
    }
}
