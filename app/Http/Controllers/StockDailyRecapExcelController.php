<?php

namespace App\Http\Controllers;

use App\Services\StockDailyRecapService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockDailyRecapExcelController extends Controller
{
    public function __invoke(Request $request, StockDailyRecapService $service): StreamedResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
        ]);

        $recapDate = Carbon::parse($validated['date']);
        $generatedAt = now();
        $generatedBy = $request->user()->name;

        $rows = $service->generate($validated['date']);
        $adjustments = $service->adjustments($validated['date']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Stok Harian');
        $sheet->setShowGridLines(true);

        // Header Title
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'CV ARI GITA GROSIR - LAPORAN REKAP STOK HARIAN');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Subtitle
        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'Tanggal Rekap: ' . $recapDate->translatedFormat('d F Y'));
        $sheet->getStyle('A2')->getFont()->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('555555'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Metadata
        $sheet->setCellValue('A4', 'Tanggal Dicetak: ' . $generatedAt->translatedFormat('d F Y, H:i') . ' WIB');
        $sheet->setCellValue('G4', 'Oleh: ' . $generatedBy);
        $sheet->getStyle('A4:G4')->getFont()->setSize(10)->setItalic(true);

        // Table Header
        $headers = [
            'No',
            'Nama Produk',
            'SKU',
            'Satuan',
            'Stok Awal',
            'Barang Masuk',
            'Barang Keluar',
            'Stok Akhir',
            'Average Cost (HPP)',
            'Total Nilai Stok Akhir',
        ];

        $headerRow = 6;
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . $headerRow, $header);
            $columnIndex++;
        }

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '1E3A8A'],
                ],
            ],
        ];

        $sheet->getStyle('A6:J6')->applyFromArray($headerStyle);
        $sheet->getRowDimension(6)->setRowHeight(25);

        // Populate Data Rows
        $currentRow = 7;
        $startRow = $currentRow;

        foreach ($rows as $row) {
            $nilaiStokAkhir = $row['stok_akhir'] * $row['average_cost'];

            $sheet->setCellValue('A' . $currentRow, $row['no']);
            $sheet->setCellValue('B' . $currentRow, $row['name']);
            $sheet->setCellValue('C' . $currentRow, $row['sku']);
            $sheet->setCellValue('D' . $currentRow, $row['unit']);
            $sheet->setCellValue('E' . $currentRow, $row['stok_awal']);
            $sheet->setCellValue('F' . $currentRow, $row['barang_masuk']);
            $sheet->setCellValue('G' . $currentRow, $row['barang_keluar']);
            $sheet->setCellValue('H' . $currentRow, $row['stok_akhir']);
            $sheet->setCellValue('I' . $currentRow, $row['average_cost']);
            $sheet->setCellValue('J' . $currentRow, $nilaiStokAkhir);

            // Alignments & Number Formats
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle('E' . $currentRow . ':H' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('E' . $currentRow . ':H' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->getStyle('H' . $currentRow)->getFont()->setBold(true);

            $sheet->getStyle('I' . $currentRow . ':J' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I' . $currentRow . ':J' . $currentRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle('J' . $currentRow)->getFont()->setBold(true);

            // Borders
            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

            $currentRow++;
        }

        $endRow = $currentRow - 1;

        // Total Row
        if ($endRow >= $startRow) {
            $sheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
            $sheet->setCellValue('A' . $currentRow, 'TOTAL:');
            $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->setCellValue('E' . $currentRow, '=SUM(E' . $startRow . ':E' . $endRow . ')');
            $sheet->setCellValue('F' . $currentRow, '=SUM(F' . $startRow . ':F' . $endRow . ')');
            $sheet->setCellValue('G' . $currentRow, '=SUM(G' . $startRow . ':G' . $endRow . ')');
            $sheet->setCellValue('H' . $currentRow, '=SUM(H' . $startRow . ':H' . $endRow . ')');
            $sheet->setCellValue('I' . $currentRow, '-');
            $sheet->setCellValue('J' . $currentRow, '=SUM(J' . $startRow . ':J' . $endRow . ')');

            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');

            $sheet->getStyle('E' . $currentRow . ':H' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('E' . $currentRow . ':H' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');

            $sheet->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('J' . $currentRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');

            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $currentRow . ':J' . $currentRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);
        }

        // Section: Stock Adjustments if present
        if ($adjustments->count() > 0) {
            $currentRow += 3;
            $sheet->setCellValue('A' . $currentRow, 'Penyesuaian Stok (Stock Adjustment) Pada Tanggal Ini');
            $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true)->setSize(12);

            $currentRow++;
            $adjHeaders = ['No', 'Nama Produk', 'SKU', 'Stok Sistem', 'Stok Fisik', 'Selisih', 'Catatan', 'Petugas', 'Waktu'];
            $adjCol = 'A';
            foreach ($adjHeaders as $adjH) {
                $sheet->setCellValue($adjCol . $currentRow, $adjH);
                $adjCol++;
            }

            $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '475569']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $currentRow++;
            foreach ($adjustments as $idx => $adj) {
                $sheet->setCellValue('A' . $currentRow, $idx + 1);
                $sheet->setCellValue('B' . $currentRow, $adj['product_name']);
                $sheet->setCellValue('C' . $currentRow, $adj['sku']);
                $sheet->setCellValue('D' . $currentRow, $adj['system_stock']);
                $sheet->setCellValue('E' . $currentRow, $adj['physical_stock']);
                $sheet->setCellValue('F' . $currentRow, $adj['difference']);
                $sheet->setCellValue('G' . $currentRow, $adj['note'] ?? '-');
                $sheet->setCellValue('H' . $currentRow, $adj['created_by']);
                $sheet->setCellValue('I' . $currentRow, $adj['created_at']);

                $sheet->getStyle('A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('D' . $currentRow . ':F' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('D' . $currentRow . ':F' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('F' . $currentRow)->getFont()->setBold(true);

                $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

                $currentRow++;
            }
        }

        // Auto-fit Column Widths
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'rekap-stok-' . $recapDate->format('Y-m-d') . '.xlsx';

        return response()->stream(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
}
