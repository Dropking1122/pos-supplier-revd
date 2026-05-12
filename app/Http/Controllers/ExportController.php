<?php
namespace App\Http\Controllers;

use App\Models\{Sale, Product, Setting};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Alignment, Border, Font};

class ExportController extends Controller
{
    public function invoiceExcel($id)
    {
        $sale    = Sale::with(['customer', 'details.product'])->findOrFail($id);
        $setting = Setting::getSettings();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Invoice');

        // ── Baris 1: Info perusahaan & invoice ───────────────────────────
        $sheet->setCellValue('A1', $setting->company_name);
        $sheet->setCellValue('A2', 'Invoice: ' . $sale->invoice_number);
        $sheet->setCellValue('A3', 'Tanggal: ' . $sale->created_at->format('d/m/Y H:i'));
        if ($sale->customer) {
            $sheet->setCellValue('A4', 'Customer: ' . $sale->customer->name);
        }
        $sheet->setCellValue('A5', 'Pembayaran: ' . ($sale->payment_type === 'cash' ? 'Cash / Tunai' : 'Tempo / Kredit'));

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1:A5')->getFont()->setColor(
            (new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('1e293b')
        );

        // ── Baris 7: Header kolom ─────────────────────────────────────────
        $headerRow = 7;
        $headers   = [
            'A' => 'NO',
            'B' => 'NAMA BARANG',
            'C' => 'KODE BARANG',
            'D' => 'TIPE',
            'E' => 'HARGA BELI',
            'F' => 'HARGA JUAL',
            'G' => 'STOK AWAL',
            'H' => 'SISA STOK',
            'I' => 'STOK TERJUAL',
            'J' => 'JUMLAH TOTAL MODAL',
            'K' => 'JUMLAH TOTAL JUAL',
            'L' => 'KEUNTUNGAN',
        ];

        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}{$headerRow}", $label);
        }

        // Style header: orange background, white bold text, border
        $headerRange = "A{$headerRow}:L{$headerRow}";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E36C09']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C0C0C0']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(32);

        // ── Data rows ─────────────────────────────────────────────────────
        $totalModal      = 0;
        $totalJual       = 0;
        $totalKeuntungan = 0;
        $dataStart       = $headerRow + 1;
        $rowIdx          = $dataStart;

        foreach ($sale->details as $detail) {
            $product  = $detail->product;
            $qty      = (int) $detail->quantity;
            $stockAwal = $detail->stock_before !== null
                ? (int) $detail->stock_before
                : ($product->kuantitas + $qty);
            $stockSisa      = $stockAwal - $qty;
            $hargaBeli      = (float) $product->modal_awal;
            $hargaJual      = (float) $detail->unit_price;
            $jumlahModal    = $hargaBeli * $qty;
            $jumlahJual     = (float) $detail->subtotal;
            $keuntungan     = $jumlahJual - $jumlahModal;

            $totalModal      += $jumlahModal;
            $totalJual       += $jumlahJual;
            $totalKeuntungan += $keuntungan;

            $sheet->setCellValue("A{$rowIdx}", $rowIdx - $dataStart + 1);
            $sheet->setCellValue("B{$rowIdx}", $product->nama_barang);
            $sheet->setCellValue("C{$rowIdx}", $product->kode_barang);
            $sheet->setCellValue("D{$rowIdx}", ucfirst($detail->price_type));
            $sheet->setCellValue("E{$rowIdx}", $hargaBeli);
            $sheet->setCellValue("F{$rowIdx}", $hargaJual);
            $sheet->setCellValue("G{$rowIdx}", $stockAwal);
            $sheet->setCellValue("H{$rowIdx}", $stockSisa);
            $sheet->setCellValue("I{$rowIdx}", $qty);
            $sheet->setCellValue("J{$rowIdx}", $jumlahModal);
            $sheet->setCellValue("K{$rowIdx}", $jumlahJual);
            $sheet->setCellValue("L{$rowIdx}", $keuntungan);

            // Format rupiah untuk kolom E,F,J,K,L
            $rpFormat = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"_);_(@_)';
            foreach (['E', 'F', 'J', 'K', 'L'] as $c) {
                $sheet->getStyle("{$c}{$rowIdx}")->getNumberFormat()->setFormatCode($rpFormat);
            }

            // Alternating row color: putih / abu muda
            $bgColor = ($rowIdx % 2 === 0) ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle("A{$rowIdx}:L{$rowIdx}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
            $sheet->getStyle("A{$rowIdx}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$rowIdx}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$rowIdx}:I{$rowIdx}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($rowIdx)->setRowHeight(22);
            $rowIdx++;
        }

        // ── Total row ─────────────────────────────────────────────────────
        $totalRow = $rowIdx;
        $sheet->setCellValue("A{$totalRow}", 'TOTAL');
        $sheet->setCellValue("J{$totalRow}", $totalModal);
        $sheet->setCellValue("K{$totalRow}", $totalJual);
        $sheet->setCellValue("L{$totalRow}", $totalKeuntungan);

        $rpFormat = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"_);_(@_)';
        foreach (['J', 'K', 'L'] as $c) {
            $sheet->getStyle("{$c}{$totalRow}")->getNumberFormat()->setFormatCode($rpFormat);
        }

        $sheet->getStyle("A{$totalRow}:L{$totalRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4f46e5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '3730a3']]],
        ]);
        $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension($totalRow)->setRowHeight(28);

        // ── Ringkasan keuangan (bawah) ────────────────────────────────────
        $sumRow = $totalRow + 2;
        $sheet->setCellValue("K{$sumRow}",     'Total Modal (HPP):');
        $sheet->setCellValue("L{$sumRow}",      $totalModal);
        $sheet->setCellValue("K" . ($sumRow+1), 'Total Penjualan:');
        $sheet->setCellValue("L" . ($sumRow+1),  $totalJual);
        $sheet->setCellValue("K" . ($sumRow+2), 'Keuntungan Bersih:');
        $sheet->setCellValue("L" . ($sumRow+2),  $totalKeuntungan);

        $rpFormat = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"_);_(@_)';
        foreach ([$sumRow, $sumRow+1, $sumRow+2] as $r) {
            $sheet->getStyle("L{$r}")->getNumberFormat()->setFormatCode($rpFormat);
        }
        $sheet->getStyle("K{$sumRow}:K" . ($sumRow+2))->getFont()->setBold(true);
        $sheet->getStyle("L" . ($sumRow+2))->getFont()->setBold(true)->setColor(
            (new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB($totalKeuntungan >= 0 ? '15803d' : 'b91c1c')
        );

        // ── Lebar kolom ───────────────────────────────────────────────────
        $colWidths = ['A'=>5,'B'=>28,'C'=>14,'D'=>8,'E'=>14,'F'=>14,'G'=>11,'H'=>11,'I'=>11,'J'=>20,'K'=>20,'L'=>18];
        foreach ($colWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // ── Sheet 2: Info Transaksi ───────────────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Info Transaksi');
        $sheet2->setCellValue('A1', 'INFO TRANSAKSI');
        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $infoData = [
            ['No. Invoice',    $sale->invoice_number],
            ['Tanggal',        $sale->created_at->format('d/m/Y H:i')],
            ['Customer',       $sale->customer?->name ?? 'Umum'],
            ['Pembayaran',     $sale->payment_type === 'cash' ? 'Cash / Tunai' : 'Tempo / Kredit'],
            ['Status',         $sale->status === 'paid' ? 'LUNAS' : ($sale->status === 'partial' ? 'SEBAGIAN' : 'BELUM BAYAR')],
            ['Total Tagihan',  'Rp ' . number_format($sale->total_amount, 0, ',', '.')],
            ['Sudah Dibayar',  'Rp ' . number_format($sale->amount_paid, 0, ',', '.')],
            ['Sisa Hutang',    'Rp ' . number_format($sale->total_amount - $sale->amount_paid, 0, ',', '.')],
            ['Toko',           $setting->company_name],
            ['Alamat',         $setting->company_address ?? ''],
            ['Telepon',        $setting->company_phone ?? ''],
        ];
        foreach ($infoData as $i => $row) {
            $r = $i + 3;
            $sheet2->setCellValue("A{$r}", $row[0]);
            $sheet2->setCellValue("B{$r}", $row[1]);
            $sheet2->getStyle("A{$r}")->getFont()->setBold(true);
        }
        $sheet2->getColumnDimension('A')->setWidth(18);
        $sheet2->getColumnDimension('B')->setWidth(30);

        $spreadsheet->setActiveSheetIndex(0);

        // ── Output ────────────────────────────────────────────────────────
        $filename = 'invoice-' . $sale->invoice_number . '.xlsx';
        $writer   = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function customerExcel(Request $request)
    {
        $customerId = $request->customer_id;
        if (!$customerId) abort(400, 'customer_id diperlukan.');

        $customer = \App\Models\Customer::findOrFail($customerId);
        $sales    = Sale::with(['details.product'])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
        $setting  = Setting::getSettings();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Customer');

        // ── Header toko & customer ─────────────────────────────────────────
        $sheet->setCellValue('A1', $setting->company_name);
        $sheet->setCellValue('A2', 'Rekap Transaksi Customer: ' . $customer->name);
        if ($customer->phone) $sheet->setCellValue('A3', 'Telepon: ' . $customer->phone);
        if ($customer->address) $sheet->setCellValue('A4', 'Alamat: ' . $customer->address);
        $sheet->setCellValue('A5', 'Dicetak: ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);

        $row = 7;

        $grandTotal      = 0;
        $grandModal      = 0;
        $grandProfit     = 0;

        foreach ($sales as $saleIdx => $sale) {
            // ── Judul tiap transaksi ─────────────────────────────────────
            $titleRange = "A{$row}:J{$row}";
            $sheet->setCellValue("A{$row}", '#' . ($saleIdx + 1) . '  ' . $sale->invoice_number);
            $sheet->setCellValue("F{$row}", $sale->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue("H{$row}", 'Status:');
            $sheet->setCellValue("I{$row}", $sale->status === 'paid' ? 'LUNAS' : ($sale->status === 'partial' ? 'SEBAGIAN' : 'BELUM BAYAR'));
            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4338ca']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;

            // ── Header kolom produk ─────────────────────────────────────
            $headers = ['A'=>'NAMA BARANG','B'=>'KODE','C'=>'TIPE','D'=>'HARGA BELI','E'=>'HARGA JUAL','F'=>'QTY','G'=>'STOK AWAL','H'=>'SISA STOK','I'=>'TOTAL MODAL','J'=>'TOTAL JUAL'];
            foreach ($headers as $col => $label) {
                $sheet->setCellValue("{$col}{$row}", $label);
            }
            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E36C09']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C0C0C0']]],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;

            // ── Data produk ─────────────────────────────────────────────
            $saleModal  = 0;
            $saleJual   = 0;
            $rpFmt = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"_);_(@_)';

            foreach ($sale->details as $detail) {
                $product    = $detail->product;
                $qty        = (int) $detail->quantity;
                $stockAwal  = $detail->stock_before !== null ? (int) $detail->stock_before : ($product->kuantitas + $qty);
                $stockSisa  = $stockAwal - $qty;
                $hargaBeli  = (float) $product->modal_awal;
                $hargaJual  = (float) $detail->unit_price;
                $totalModal = $hargaBeli * $qty;
                $totalJual  = (float) $detail->subtotal;

                $saleModal += $totalModal;
                $saleJual  += $totalJual;

                $sheet->setCellValue("A{$row}", $product->nama_barang);
                $sheet->setCellValue("B{$row}", $product->kode_barang);
                $sheet->setCellValue("C{$row}", ucfirst($detail->price_type));
                $sheet->setCellValue("D{$row}", $hargaBeli);
                $sheet->setCellValue("E{$row}", $hargaJual);
                $sheet->setCellValue("F{$row}", $qty);
                $sheet->setCellValue("G{$row}", $stockAwal);
                $sheet->setCellValue("H{$row}", $stockSisa);
                $sheet->setCellValue("I{$row}", $totalModal);
                $sheet->setCellValue("J{$row}", $totalJual);

                foreach (['D','E','I','J'] as $c) {
                    $sheet->getStyle("{$c}{$row}")->getNumberFormat()->setFormatCode($rpFmt);
                }
                $sheet->getStyle("F{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $bgColor = ($row % 2 === 0) ? 'F8FAFC' : 'FFFFFF';
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(20);
                $row++;
            }

            // ── Subtotal transaksi ──────────────────────────────────────
            $saleProfit = $saleJual - $saleModal;
            $sheet->setCellValue("H{$row}", 'Subtotal:');
            $sheet->setCellValue("I{$row}", $saleModal);
            $sheet->setCellValue("J{$row}", $saleJual);
            foreach (['I','J'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getNumberFormat()->setFormatCode($rpFmt);
            }
            $sheet->getStyle("H{$row}:J{$row}")->getFont()->setBold(true);
            $sheet->getStyle("H{$row}:J{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C7D2FE']]],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;

            $grandTotal  += $saleJual;
            $grandModal  += $saleModal;
            $grandProfit += $saleProfit;

            $row++; // spasi antar transaksi
        }

        // ── Grand Total ────────────────────────────────────────────────────
        $sheet->setCellValue("G{$row}", 'GRAND TOTAL');
        $sheet->setCellValue("I{$row}", $grandModal);
        $sheet->setCellValue("J{$row}", $grandTotal);
        $rpFmt = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"_);_(@_)';
        foreach (['I','J'] as $c) {
            $sheet->getStyle("{$c}{$row}")->getNumberFormat()->setFormatCode($rpFmt);
        }
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4f46e5']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '3730a3']]],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(26);

        // ── Lebar kolom ────────────────────────────────────────────────────
        $colWidths = ['A'=>28,'B'=>12,'C'=>8,'D'=>14,'E'=>14,'F'=>7,'G'=>11,'H'=>11,'I'=>18,'J'=>18];
        foreach ($colWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $filename = 'rekap-' . \Illuminate\Support\Str::slug($customer->name) . '-' . now()->format('Ymd') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function pdf(Request $request)
    {
        $sales        = $this->getSales($request);
        $setting      = Setting::getSettings();
        $totalRevenue = $sales->sum('total_amount');
        $totalProfit  = $sales->sum(
            fn($s) => $s->details->sum(fn($d) => $d->subtotal - ($d->quantity * ($d->product->modal_awal ?? 0)))
        );

        $pdf = Pdf::loadView('reports.pdf', compact('sales', 'setting', 'totalRevenue', 'totalProfit', 'request'))
            ->setPaper('a4', 'landscape');

        $period = $this->getPeriodLabel($request);
        return $pdf->download("laporan-penjualan-{$period}.pdf");
    }

    public function excel(Request $request)
    {
        $sales    = $this->getSales($request);
        $setting  = Setting::getSettings();
        $period   = $this->getPeriodLabel($request);
        $filename = "laporan-penjualan-{$period}.csv";

        $totalRevenue = $sales->sum('total_amount');
        $totalProfit  = $sales->sum(
            fn($s) => $s->details->sum(fn($d) => $d->subtotal - ($d->quantity * ($d->product->modal_awal ?? 0)))
        );
        $countAll     = $sales->count();
        $countPaid    = $sales->where('status', 'paid')->count();
        $countPartial = $sales->where('status', 'partial')->count();
        $countUnpaid  = $sales->where('status', 'unpaid')->count();
        $totalUnpaid  = $sales->sum(fn($s) => $s->total_amount - $s->amount_paid);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($sales, $setting, $request, $totalRevenue, $totalProfit, $countAll, $countPaid, $countPartial, $countUnpaid, $totalUnpaid) {
            $f = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // ── Company Header ──────────────────────────
            fputcsv($f, ['LAPORAN PENJUALAN']);
            fputcsv($f, ['Toko', $setting->company_name]);
            if ($setting->company_address) fputcsv($f, ['Alamat', $setting->company_address]);
            if ($setting->company_phone)   fputcsv($f, ['Telepon', $setting->company_phone]);
            fputcsv($f, ['Periode', $this->getPeriodDisplay($request)]);
            fputcsv($f, ['Dicetak Pada', now()->format('d/m/Y H:i')]);
            fputcsv($f, []);

            // ── Summary Section ─────────────────────────
            fputcsv($f, ['=== RINGKASAN ===']);
            fputcsv($f, ['Total Penjualan', 'Rp ' . number_format($totalRevenue, 0, ',', '.')]);
            fputcsv($f, ['Total Profit',    'Rp ' . number_format($totalProfit,  0, ',', '.')]);
            if ($totalRevenue > 0) {
                fputcsv($f, ['Margin Profit', number_format($totalProfit / $totalRevenue * 100, 1) . '%']);
            }
            fputcsv($f, ['Jumlah Transaksi',    $countAll]);
            fputcsv($f, ['Transaksi Lunas',      $countPaid]);
            fputcsv($f, ['Transaksi Sebagian',   $countPartial]);
            fputcsv($f, ['Transaksi Belum Bayar',$countUnpaid]);
            fputcsv($f, ['Total Piutang',  'Rp ' . number_format($totalUnpaid,  0, ',', '.')]);
            fputcsv($f, []);

            // ── Column Headers ──────────────────────────
            fputcsv($f, ['=== DETAIL TRANSAKSI ===']);
            fputcsv($f, [
                'No',
                'No. Invoice',
                'Tanggal',
                'Waktu',
                'Customer',
                'Tipe Pembayaran',
                'Total (Rp)',
                'Modal (Rp)',
                'Profit (Rp)',
                'Margin (%)',
                'Status Bayar',
                'Jatuh Tempo',
                'Sudah Dibayar (Rp)',
                'Sisa Hutang (Rp)',
            ]);

            // ── Data Rows ────────────────────────────────
            foreach ($sales as $i => $sale) {
                $modal  = $sale->details->sum(fn($d) => $d->quantity * ($d->product->modal_awal ?? 0));
                $profit = $sale->details->sum(fn($d) => $d->subtotal - ($d->quantity * ($d->product->modal_awal ?? 0)));
                $margin = $sale->total_amount > 0 ? round($profit / $sale->total_amount * 100, 1) : 0;
                $sisa   = $sale->total_amount - $sale->amount_paid;

                fputcsv($f, [
                    $i + 1,
                    $sale->invoice_number,
                    $sale->created_at->format('d/m/Y'),
                    $sale->created_at->format('H:i'),
                    $sale->customer?->name ?? 'Umum',
                    $sale->payment_type === 'cash' ? 'Cash' : 'Tempo',
                    $sale->total_amount,
                    $modal,
                    $profit,
                    $margin . '%',
                    $sale->status === 'paid' ? 'Lunas' : ($sale->status === 'partial' ? 'Sebagian' : 'Belum Bayar'),
                    $sale->due_date?->format('d/m/Y') ?? '-',
                    $sale->amount_paid,
                    $sisa > 0 ? $sisa : 0,
                ]);
            }

            // ── Totals Row ───────────────────────────────
            fputcsv($f, []);
            fputcsv($f, [
                '',
                '',
                '',
                '',
                'TOTAL',
                '',
                $totalRevenue,
                $sales->sum(fn($s) => $s->details->sum(fn($d) => $d->quantity * ($d->product->modal_awal ?? 0))),
                $totalProfit,
                ($totalRevenue > 0 ? number_format($totalProfit / $totalRevenue * 100, 1) : '0') . '%',
                '',
                '',
                $sales->sum('amount_paid'),
                $totalUnpaid > 0 ? $totalUnpaid : 0,
            ]);

            fputcsv($f, []);
            if ($setting->invoice_footer) {
                fputcsv($f, [$setting->invoice_footer]);
            }

            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function stockExcel(Request $request)
    {
        $date    = $request->date ?? now()->format('Y-m-d');
        $setting = Setting::getSettings();

        $soldData = DB::table('sale_details')
            ->join('sales', 'sales.id', '=', 'sale_details.sale_id')
            ->whereDate('sales.created_at', $date)
            ->select(
                'sale_details.product_id',
                DB::raw('SUM(sale_details.quantity) as total_qty'),
                DB::raw('SUM(sale_details.subtotal) as total_pendapatan')
            )
            ->groupBy('sale_details.product_id')
            ->get()
            ->keyBy('product_id');

        $products = Product::orderBy('nama_barang')->get()->map(function ($p) use ($soldData) {
            $sold              = $soldData->get($p->id);
            $p->terjual        = $sold ? (int)   $sold->total_qty       : 0;
            $p->pendapatan     = $sold ? (float)  $sold->total_pendapatan : 0;
            $p->stock_awal     = $p->kuantitas + $p->terjual;
            $p->keuntungan     = $p->pendapatan - ($p->terjual * (float) $p->modal_awal);
            return $p;
        });

        $label    = \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y');
        $filename = "stock-harian-{$date}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($products, $setting, $label, $date) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($f, ['LAPORAN STOCK HARIAN']);
            fputcsv($f, ['Toko',      $setting->company_name]);
            fputcsv($f, ['Tanggal',   $label]);
            fputcsv($f, ['Dicetak',   now()->format('d/m/Y H:i')]);
            fputcsv($f, []);

            $total_terjual    = $products->sum('terjual');
            $tidak_terjual    = $products->where('terjual', 0)->count();
            $total_pendapatan = $products->sum('pendapatan');
            $total_keuntungan = $products->sum('keuntungan');
            $low_stock        = $products->filter(fn($p) => $p->kuantitas <= $p->stock_minimum)->count();

            fputcsv($f, ['=== RINGKASAN ===']);
            fputcsv($f, ['Total Produk',          $products->count()]);
            fputcsv($f, ['Total Terjual (unit)',   $total_terjual]);
            fputcsv($f, ['Tidak Terjual (jenis)',  $tidak_terjual]);
            fputcsv($f, ['Total Pendapatan', 'Rp ' . number_format($total_pendapatan, 0, ',', '.')]);
            fputcsv($f, ['Total Keuntungan', 'Rp ' . number_format($total_keuntungan, 0, ',', '.')]);
            fputcsv($f, ['Stok Menipis',           $low_stock]);
            fputcsv($f, []);

            fputcsv($f, ['=== DETAIL STOCK ===']);
            fputcsv($f, [
                'No', 'Kode Barang', 'Nama Produk', 'Jenis',
                'Stock Awal', 'Terjual', 'Sisa Stock', 'Stock Minimum',
                'Harga Modal (Rp)', 'Harga Grosir (Rp)', 'Harga Ecer (Rp)',
                'Pendapatan (Rp)', 'Keuntungan (Rp)', 'Status',
            ]);

            foreach ($products as $i => $p) {
                $low    = $p->kuantitas <= $p->stock_minimum;
                $sold   = $p->terjual > 0;
                $status = match (true) {
                    $sold && $low  => 'Terjual · Low Stock',
                    $sold          => 'Terjual',
                    $low           => 'Stok Menipis',
                    default        => 'Tidak Terjual',
                };
                fputcsv($f, [
                    $i + 1,
                    $p->kode_barang,
                    $p->nama_barang,
                    $p->jenis_barang,
                    $p->stock_awal,
                    $p->terjual ?: '-',
                    $p->kuantitas,
                    $p->stock_minimum,
                    $p->modal_awal,
                    $p->harga_grosir,
                    $p->harga_ecer,
                    $p->pendapatan ?: '-',
                    $p->terjual > 0 ? $p->keuntungan : '-',
                    $status,
                ]);
            }

            fputcsv($f, []);
            fputcsv($f, [
                '', '', '', 'TOTAL',
                $products->sum('stock_awal'),
                $total_terjual,
                $products->sum('kuantitas'),
                '', '', '', '',
                $total_pendapatan,
                $total_keuntungan,
                '',
            ]);

            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getSales(Request $request)
    {
        $q = Sale::with(['customer', 'details.product'])->latest();
        if ($request->type === 'daily' && $request->date) {
            $q->whereDate('created_at', $request->date);
        } elseif ($request->type === 'monthly' && $request->month) {
            [$y, $m] = explode('-', $request->month);
            $q->whereYear('created_at', $y)->whereMonth('created_at', $m);
        } elseif ($request->type === 'yearly' && $request->year) {
            $q->whereYear('created_at', $request->year);
        }
        return $q->get();
    }

    private function getPeriodLabel(Request $request): string
    {
        if ($request->type === 'daily' && $request->date) {
            return $request->date;
        } elseif ($request->type === 'monthly' && $request->month) {
            return $request->month;
        } elseif ($request->type === 'yearly' && $request->year) {
            return $request->year;
        }
        return now()->format('Y-m-d');
    }

    private function getPeriodDisplay(Request $request): string
    {
        if ($request->type === 'daily' && $request->date) {
            return \Carbon\Carbon::parse($request->date)->isoFormat('D MMMM Y');
        } elseif ($request->type === 'monthly' && $request->month) {
            return \Carbon\Carbon::createFromFormat('Y-m', $request->month)->isoFormat('MMMM Y');
        } elseif ($request->type === 'yearly' && $request->year) {
            return 'Tahun ' . $request->year;
        }
        return now()->isoFormat('D MMMM Y');
    }
}
