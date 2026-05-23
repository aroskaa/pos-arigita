<?php

namespace App\Http\Controllers;

use App\Services\StockDailyRecapService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StockDailyRecapPdfController extends Controller
{
    public function __invoke(Request $request, StockDailyRecapService $service)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
        ]);

        $recapDate = Carbon::parse($validated['date']);
        $generatedAt = now();

        $rows = $service->generate($validated['date']);
        $adjustments = $service->adjustments($validated['date']);

        $pdf = Pdf::loadView('pdf.stock-daily-recap', [
            'rows' => $rows,
            'adjustments' => $adjustments,
            'recapDate' => $recapDate,
            'generatedAt' => $generatedAt,
            'generatedBy' => $request->user()->name,
        ])->setPaper('a4', 'landscape');

        $fileName = 'rekap-stok-' . $recapDate->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }
}
