<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    Route::middleware('role:owner,admin')->group(function () {
        Route::get('/admin/products', function () {
            return view('pages.products.index');
        })->name('products.index');
    });

    Route::middleware('role:owner,admin,cashier')->group(function () {
        Route::get('/pos', function () {
            return view('pages.pos.index');
        })->name('pos.index');
    });

    Route::middleware('role:owner,admin')->group(function () {
        Route::get('/reports', function () {
            return view('pages.reports.index');
        })->name('reports.index');
    });
});

require __DIR__.'/auth.php';
