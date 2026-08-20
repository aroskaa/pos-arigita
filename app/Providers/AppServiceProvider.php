<?php

namespace App\Providers;

use App\Models\CustomerOrder;
use App\Models\Product;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        View::composer('layouts.pos', function ($view): void {
            $pendingOrdersCount = CustomerOrder::query()
                ->whereIn('status', ['pending', 'preorder'])
                ->count();

            $lowStockCount = Product::query()
                ->where('is_active', true)
                ->where('minimum_stock', '>', 0)
                ->whereColumn('stock', '<=', 'minimum_stock')
                ->count();

            $view->with('posNotifications', [
                'pending_orders_count' => $pendingOrdersCount,
                'low_stock_count' => $lowStockCount,
                'total_count' => $pendingOrdersCount + $lowStockCount,
                'latest_pending_orders' => CustomerOrder::query()
                    ->whereIn('status', ['pending', 'preorder'])
                    ->latest()
                    ->limit(3)
                    ->get(),
                'low_stock_products' => Product::query()
                    ->with('unit')
                    ->where('is_active', true)
                    ->where('minimum_stock', '>', 0)
                    ->whereColumn('stock', '<=', 'minimum_stock')
                    ->orderBy('stock')
                    ->limit(3)
                    ->get(),
            ]);
        });
    }
}
