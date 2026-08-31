<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

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

    public function receipt(Request $request, Sale $sale)
    {
        $sale->load([
            'cashier',
            'customer',
            'customerOrder',
            'items.product',
            'canceller',
        ]);

        $paper = $request->query('paper', $request->query('size', '58'));
        $paperSize = in_array((string) $paper, ['58', '80'], true) ? (string) $paper : '58';

        return view('pages.sales.receipt', [
            'sale' => $sale,
            'paperSize' => $paperSize,
        ]);
    }
}
