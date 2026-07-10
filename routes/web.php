    <?php

    use App\Http\Controllers\DashboardController;
    use App\Http\Controllers\ReportDownloadController;
    use App\Http\Controllers\SaleController;
    use App\Http\Controllers\StockDailyRecapPdfController;
    use Illuminate\Support\Facades\Route;

    Route::get('/', function () {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('welcome');
    });

    Route::get('/order', function () {
        return view('pages.customer-orders.create');
    })->middleware('customer.order.limit')->name('customer-orders.create');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', DashboardController::class)
            ->name('dashboard');

        Route::middleware('role:owner')->group(function () {
            Route::get('/users', function () {
                return view('pages.users.index');
            })->name('users.index');
        });

        Route::middleware('role:owner,admin')->group(function () {
            Route::get('/admin/products', function () {
                return view('pages.products.index');
            })->name('products.index');

            Route::get('/reports', function () {
                return view('pages.reports.index');
            })->name('reports.index');

            Route::get('/reports/download/{type}', ReportDownloadController::class)
                ->name('reports.download');

            Route::get('/suppliers', function () {
                return view('pages.suppliers.index');
            })->name('suppliers.index');

            Route::get('/purchases', function () {
                return view('pages.purchases.index');
            })->name('purchases.index');

            Route::get('/stock-movements', function () {
                return view('pages.stock-movements.index');
            })->name('stock-movements.index');

            Route::get('/stock-daily-recaps', function () {
                return view('pages.stock-daily-recaps.index');
            })->name('stock-daily-recaps.index');

            Route::get('/stock-daily-recaps/download', StockDailyRecapPdfController::class)
                ->name('stock-daily-recaps.download');

            Route::get('/stock-adjustments', function () {
                return view('pages.stock-adjustments.index');
            })->name('stock-adjustments.index');

            Route::get('/customer-orders', function () {
                return view('pages.customer-orders.index');
            })->name('customer-orders.index');
        });

        Route::middleware('role:owner,admin,cashier')->group(function () {
            Route::get('/pos', function () {
                return view('pages.pos.index');
            })->name('pos.index');

            Route::get('/sales', [SaleController::class, 'index'])
            ->name('sales.index');

            Route::get('/sales/{sale}', [SaleController::class, 'show'])
                ->name('sales.show');
            });

            Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])
            ->name('sales.receipt');
    });

    require __DIR__.'/auth.php';
