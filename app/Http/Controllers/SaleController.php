<?php

namespace App\Http\Controllers;

use App\Models\Sale;
// use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        return view('pages.sales.index');
    }

    public function show(Sale $sale)
    {
        $sale->load([
            'cashier',
            'customer',
            'items.product',
            'customerOrder',
            'canceller',
        ]);

        return view('pages.sales.show', compact('sale'));
    }
}
