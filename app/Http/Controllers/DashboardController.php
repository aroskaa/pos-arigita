<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CustomerOrder;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if ($request->user()?->isCustomer()) {
            return redirect()->route('customer-orders.create');
        }

        $todaySales = Sale::query()
            ->where('status', 'completed')
            ->whereDate('sale_date', now()->toDateString());

        $lowStockProducts = Product::query()
            ->with('unit')
            ->where('is_active', true)
            ->where('minimum_stock', '>', 0)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->limit(3)
            ->get();

        return view('dashboard', [
            'user' => $request->user(),
            'todaySalesTotal' => (float) (clone $todaySales)->sum('grand_total'),
            'todayTransactionCount' => (int) (clone $todaySales)->count(),
            'activeProductCount' => Product::query()->where('is_active', true)->count(),
            'lowStockProducts' => $lowStockProducts,
            'lowStockCount' => Product::query()
                ->where('is_active', true)
                ->where('minimum_stock', '>', 0)
                ->whereColumn('stock', '<=', 'minimum_stock')
                ->count(),
            'pendingOrderCount' => CustomerOrder::query()
                ->whereIn('status', ['pending', 'preorder'])
                ->count(),
            'pendingOrders' => CustomerOrder::query()
                ->whereIn('status', ['pending', 'preorder'])
                ->latest()
                ->limit(3)
                ->get(),
            'salesByDay' => Sale::query()
                ->where('status', 'completed')
                ->where('sale_date', '>=', now()->subDays(6)->startOfDay())
                ->selectRaw('DATE(sale_date) as sale_day, COALESCE(SUM(grand_total), 0) as total_sales, COUNT(*) as transaction_count')
                ->groupBy('sale_day')
                ->orderBy('sale_day')
                ->get(),
            'recentActivities' => ActivityLog::query()
                ->with('user')
                ->latest()
                ->limit(15)
                ->get(),
        ]);
    }
}
