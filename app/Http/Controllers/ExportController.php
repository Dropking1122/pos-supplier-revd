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

        // ── Keterangan kolom ─────────────────────────────────────────────
        $this->writeLegend($sheet, $sumRow + 4, 'L', [
            ['E', 'Harga Beli',          'Modal / HPP (Harga Pokok Penjualan) per unit barang. Harga saat toko membeli barang dari supplier.'],
            ['F', 'Harga Jual',          'Harga jual aktual per unit pada saat transaksi ini terjadi (bisa harga grosir atau harga ecer, tergantung tipe penjualan).'],
            ['G', 'Stok Awal',           'Perkiraan stok sebelum transaksi ini diproses. Rumus: Sisa Stok + Stok Terjual.'],
            ['H', 'Sisa Stok',           'Jumlah stok setelah transaksi selesai. Rumus: Stok Awal − Stok Terjual.'],
            ['I', 'Stok Terjual',        'Jumlah unit barang yang dibeli customer dalam transaksi ini.'],
            ['J', 'Jumlah Total Modal',  'Total biaya pengadaan untuk item ini. Rumus: Harga Beli × Stok Terjual.'],
            ['K', 'Jumlah Total Jual',   'Total pendapatan dari item ini (= Subtotal). Rumus: Harga Jual × Stok Terjual.'],
            ['L', 'Keuntungan',          'Profit bersih per item dalam transaksi. Rumus: Jumlah Total Jual − Jumlah Total Modal. Nilai minus berarti rugi.'],
        ]);

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
        $customerSlug = $sale->customer ? \Illuminate\Support\Str::slug($sale->customer->name) : 'umum';
        $filename = 'invoice-' . $customerSlug . '-' . $sale->invoice_number . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $writer->save($tempFile);
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ])->deleteFileAfterSend(true);
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
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $writer->save($tempFile);
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ])->deleteFileAfterSend(true);
    }

    public function customerPdf(Request $request)
    {
        $customerId = $request->customer_id;
        if (!$customerId) abort(400, 'customer_id diperlukan.');

        $customer = \App\Models\Customer::findOrFail($customerId);
        $sales    = Sale::with(['details.product'])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
        $setting  = Setting::getSettings();

        $grandTotal  = 0;
        $grandModal  = 0;
        $grandProfit = 0;

        foreach ($sales as $sale) {
            foreach ($sale->details as $detail) {
                $qty       = (int) $detail->quantity;
                $modal     = (float) ($detail->product->modal_awal ?? 0);
                $jual      = (float) $detail->subtotal;
                $grandModal  += $modal * $qty;
                $grandTotal  += $jual;
                $grandProfit += $jual - ($modal * $qty);
            }
        }

        $pdf = Pdf::loadView('exports.customer-pdf', compact('customer', 'sales', 'setting', 'grandTotal', 'grandModal', 'grandProfit'))
            ->setPaper('a4', 'landscape');

        $filename = 'rekap-' . \Illuminate\Support\Str::slug($customer->name) . '-' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    public function productExport()
    {
        $products = Product::orderBy('nama_barang')->get();
        $setting  = Setting::getSettings();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Barang');

        $rpFmt = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"_);_(@_)';

        $sheet->setCellValue('A1', 'DATA BARANG - ' . strtoupper($setting->company_name));
        $sheet->setCellValue('A2', 'Dicetak: ' . now()->format('d/m/Y H:i') . '  |  Total: ' . $products->count() . ' produk');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A2')->getFont()->setSize(9)->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('64748b'));

        $headerRow = 4;
        $headers = ['A'=>'KODE BARANG','B'=>'NAMA BARANG','C'=>'JENIS','D'=>'SATUAN',
                    'E'=>'STOK','F'=>'STOK MIN','G'=>'MODAL (Rp)','H'=>'GROSIR (Rp)','I'=>'ECER (Rp)'];
        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}{$headerRow}", $label);
        }
        $sheet->getStyle("A{$headerRow}:I{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4f46e5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '3730a3']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(26);

        $row = $headerRow + 1;
        foreach ($products as $p) {
            $isLow = $p->kuantitas <= $p->stock_minimum;
            $sheet->setCellValue("A{$row}", $p->kode_barang);
            $sheet->setCellValue("B{$row}", $p->nama_barang);
            $sheet->setCellValue("C{$row}", $p->jenis_barang ?? '');
            $sheet->setCellValue("D{$row}", $p->harga_satuan ?? '');
            $sheet->setCellValue("E{$row}", $p->kuantitas);
            $sheet->setCellValue("F{$row}", $p->stock_minimum);
            $sheet->setCellValue("G{$row}", $p->modal_awal);
            $sheet->setCellValue("H{$row}", $p->harga_grosir);
            $sheet->setCellValue("I{$row}", $p->harga_ecer);

            foreach (['G','H','I'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getNumberFormat()->setFormatCode($rpFmt);
            }
            $bg = $isLow ? 'FFF1F2' : ($row % 2 === 0 ? 'F8FAFC' : 'FFFFFF');
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
            if ($isLow) {
                $sheet->getStyle("E{$row}")->getFont()->setBold(true)->setColor(
                    (new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('dc2626')
                );
            }
            $sheet->getStyle("A{$row}:D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("E{$row}:F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        foreach (['A'=>18,'B'=>32,'C'=>14,'D'=>10,'E'=>8,'F'=>9,'G'=>16,'H'=>16,'I'=>16] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $filename = 'data-barang-' . now()->format('Ymd-His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $writer->save($tempFile);
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function productImportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        $headers = ['kode_barang','nama_barang','jenis_barang','kuantitas','harga_satuan','modal_awal','harga_grosir','harga_ecer','stock_minimum'];
        foreach ($headers as $i => $h) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$col}1", $h);
        }
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4f46e5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $samples = [
            ['BRG001','Beras Premium 5kg','Sembako',100,'karung',45000,50000,55000,20],
            ['BRG002','Minyak Goreng 1L','Sembako',200,'botol',13000,15000,16000,50],
            ['BRG003','Gula Pasir 1kg','Sembako',150,'kg',13500,15000,16000,30],
        ];
        foreach ($samples as $i => $s) {
            $row = $i + 2;
            foreach ($s as $j => $val) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($j + 1);
                $sheet->setCellValue("{$col}{$row}", $val);
            }
            $bg = $row % 2 === 0 ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bg);
        }

        foreach ([18,30,14,10,12,14,14,14,12] as $i => $width) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $writer   = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $writer->save($tempFile);
        return response()->download($tempFile, 'template-import-produk.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
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
        $filename = "laporan-penjualan-{$period}.xlsx";

        $totalRevenue = $sales->sum('total_amount');
        $totalProfit  = $sales->sum(
            fn($s) => $s->details->sum(fn($d) => $d->subtotal - ($d->quantity * ($d->product->modal_awal ?? 0)))
        );
        $countAll     = $sales->count();
        $countPaid    = $sales->where('status', 'paid')->count();
        $countPartial = $sales->where('status', 'partial')->count();
        $countUnpaid  = $sales->where('status', 'unpaid')->count();
        $totalUnpaid  = $sales->sum(fn($s) => $s->total_amount - $s->amount_paid);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Penjualan');

        $rpFmt = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"_);_(@_)';

        // ── Company Header ──────────────────────────
        $sheet->setCellValue('A1', 'LAPORAN PENJUALAN');
        $sheet->setCellValue('A2', 'Toko: ' . $setting->company_name);
        $sheet->setCellValue('A3', 'Periode: ' . $this->getPeriodDisplay($request));
        $sheet->setCellValue('A4', 'Dicetak: ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A4')->getFont()->setSize(10);

        // ── Ringkasan ──────────────────────────────
        $sheet->setCellValue('A6', 'RINGKASAN');
        $sheet->mergeCells('A6:B6');
        $sheet->getStyle('A6:B6')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e293b']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical'   => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);
        $sheet->getRowDimension(6)->setRowHeight(20);
        $summaryData = [
            ['Total Penjualan', $totalRevenue],
            ['Total Profit',    $totalProfit],
            ['Margin Profit',   $totalRevenue > 0 ? round($totalProfit / $totalRevenue * 100, 1) / 100 : 0],
            ['Jumlah Transaksi', $countAll],
            ['Transaksi Lunas', $countPaid],
            ['Transaksi Sebagian', $countPartial],
            ['Transaksi Belum Bayar', $countUnpaid],
            ['Total Piutang', $totalUnpaid],
        ];
        foreach ($summaryData as $i => $row) {
            $r = $i + 7;
            $sheet->setCellValue("A{$r}", $row[0]);
            $sheet->setCellValue("B{$r}", $row[1]);
            $sheet->getStyle("A{$r}")->getFont()->setBold(true);
            if (in_array($row[0], ['Total Penjualan', 'Total Profit', 'Total Piutang'])) {
                $sheet->getStyle("B{$r}")->getNumberFormat()->setFormatCode($rpFmt);
            } elseif ($row[0] === 'Margin Profit') {
                $sheet->getStyle("B{$r}")->getNumberFormat()->setFormatCode('0.0%');
            }
        }

        // ── Column Headers ──────────────────────────
        $hRow = 16;
        $headers = ['No', 'No. Invoice', 'Tanggal', 'Waktu', 'Customer', 'Tipe Pembayaran', 'Total (Rp)', 'Modal (Rp)', 'Profit (Rp)', 'Margin (%)', 'Status Bayar', 'Jatuh Tempo', 'Sudah Dibayar (Rp)', 'Sisa Hutang (Rp)'];
        foreach ($headers as $ci => $label) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ci + 1);
            $sheet->setCellValue("{$col}{$hRow}", $label);
        }
        $sheet->getStyle("A{$hRow}:N{$hRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4f46e5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '3730a3']]],
        ]);
        $sheet->getRowDimension($hRow)->setRowHeight(22);

        // ── Data Rows ────────────────────────────────
        $rowIdx = $hRow + 1;
        $grandModal = 0;
        foreach ($sales as $i => $sale) {
            $modal  = $sale->details->sum(fn($d) => $d->quantity * ($d->product->modal_awal ?? 0));
            $profit = $sale->details->sum(fn($d) => $d->subtotal - ($d->quantity * ($d->product->modal_awal ?? 0)));
            $margin = $sale->total_amount > 0 ? $profit / $sale->total_amount : 0;
            $sisa   = $sale->total_amount - $sale->amount_paid;
            $grandModal += $modal;

            $rowData = [
                $i + 1,
                $sale->invoice_number,
                $sale->created_at->format('d/m/Y'),
                $sale->created_at->format('H:i'),
                $sale->customer?->name ?? 'Umum',
                $sale->payment_type === 'cash' ? 'Cash' : 'Tempo',
                $sale->total_amount,
                $modal,
                $profit,
                $margin,
                $sale->status === 'paid' ? 'Lunas' : ($sale->status === 'partial' ? 'Sebagian' : 'Belum Bayar'),
                $sale->due_date?->format('d/m/Y') ?? '-',
                $sale->amount_paid,
                $sisa > 0 ? $sisa : 0,
            ];

            foreach ($rowData as $ci => $val) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ci + 1);
                $sheet->setCellValue("{$col}{$rowIdx}", $val);
            }

            foreach (['G', 'H', 'I', 'M', 'N'] as $c) {
                $sheet->getStyle("{$c}{$rowIdx}")->getNumberFormat()->setFormatCode($rpFmt);
            }
            $sheet->getStyle("J{$rowIdx}")->getNumberFormat()->setFormatCode('0.0%');

            $bg = ($rowIdx % 2 === 0) ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle("A{$rowIdx}:N{$rowIdx}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
            $rowIdx++;
        }

        // ── Totals Row ───────────────────────────────
        $sheet->setCellValue("E{$rowIdx}", 'TOTAL');
        $sheet->setCellValue("G{$rowIdx}", $totalRevenue);
        $sheet->setCellValue("H{$rowIdx}", $grandModal);
        $sheet->setCellValue("I{$rowIdx}", $totalProfit);
        $sheet->setCellValue("J{$rowIdx}", $totalRevenue > 0 ? $totalProfit / $totalRevenue : 0);
        $sheet->setCellValue("M{$rowIdx}", $sales->sum('amount_paid'));
        $sheet->setCellValue("N{$rowIdx}", $totalUnpaid > 0 ? $totalUnpaid : 0);
        foreach (['G', 'H', 'I', 'M', 'N'] as $c) {
            $sheet->getStyle("{$c}{$rowIdx}")->getNumberFormat()->setFormatCode($rpFmt);
        }
        $sheet->getStyle("J{$rowIdx}")->getNumberFormat()->setFormatCode('0.0%');
        $sheet->getStyle("A{$rowIdx}:N{$rowIdx}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4f46e5']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '3730a3']]],
        ]);
        $sheet->getRowDimension($rowIdx)->setRowHeight(24);

        // ── Column widths ─────────────────────────────
        $colWidths = ['A'=>5,'B'=>18,'C'=>12,'D'=>8,'E'=>20,'F'=>14,'G'=>16,'H'=>16,'I'=>16,'J'=>10,'K'=>14,'L'=>14,'M'=>18,'N'=>16];
        foreach ($colWidths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // ── Sheet Keterangan (sheet terpisah) ─────────────────────────────
        $kSheet = $spreadsheet->createSheet();
        $kSheet->setTitle('Keterangan');
        $kSheet->getColumnDimension('A')->setWidth(8);
        $kSheet->getColumnDimension('B')->setWidth(24);
        $kSheet->getColumnDimension('C')->setWidth(90);
        $this->writeLegend($kSheet, 1, 'C', [
            ['G', 'Total (Rp)',           'Total nilai penjualan yang ditagihkan ke customer. Nominal sebelum dikurangi modal.'],
            ['H', 'Modal / HPP (Rp)',     'Total Harga Pokok Penjualan semua item. Rumus: Σ (Harga Beli × Qty) untuk setiap produk dalam transaksi.'],
            ['I', 'Profit (Rp)',          'Keuntungan bersih transaksi. Rumus: Total Penjualan − Modal (HPP). Nilai minus berarti rugi.'],
            ['J', 'Margin (%)',           'Persentase keuntungan terhadap penjualan. Rumus: (Profit ÷ Total Penjualan) × 100%. Semakin tinggi semakin baik.'],
            ['K', 'Status Bayar',         'Lunas = sudah dibayar penuh  |  Sebagian = ada cicilan masuk, masih ada sisa  |  Belum Bayar = belum ada pembayaran sama sekali.'],
            ['L', 'Jatuh Tempo',          'Tanggal batas pembayaran untuk transaksi tempo/kredit. Kosong (-) jika tipe pembayaran adalah cash.'],
            ['M', 'Sudah Dibayar (Rp)',   'Total nominal yang sudah diterima dari customer, termasuk semua riwayat cicilan yang tercatat.'],
            ['N', 'Sisa Hutang (Rp)',     'Sisa tagihan yang belum dibayar. Rumus: Total − Sudah Dibayar. Bernilai 0 jika status sudah Lunas.'],
        ], $setting->invoice_footer ?: '');

        // Kembali ke sheet utama
        $spreadsheet->setActiveSheetIndex(0);

        $writer   = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $writer->save($tempFile);
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ])->deleteFileAfterSend(true);
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

        $label    = \Carbon\Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y');
        $filename = "stock-harian-{$date}.xlsx";

        $total_terjual    = $products->sum('terjual');
        $tidak_terjual    = $products->where('terjual', 0)->count();
        $total_pendapatan = $products->sum('pendapatan');
        $total_keuntungan = $products->sum('keuntungan');
        $low_stock        = $products->filter(fn($p) => $p->kuantitas <= $p->stock_minimum)->count();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stock Harian');

        $rpFmt = '_("Rp"* #,##0_);_("Rp"* \(#,##0\);_("Rp"* "-"_);_(@_)';

        // ── Header ──────────────────────────────────
        $sheet->setCellValue('A1', 'LAPORAN STOCK HARIAN');
        $sheet->setCellValue('A2', 'Toko: ' . $setting->company_name);
        $sheet->setCellValue('A3', 'Tanggal: ' . $label);
        $sheet->setCellValue('A4', 'Dicetak: ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // ── Ringkasan ──────────────────────────────
        $sheet->setCellValue('A6', 'RINGKASAN');
        $sheet->mergeCells('A6:B6');
        $sheet->getStyle('A6:B6')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e293b']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical'   => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);
        $sheet->getRowDimension(6)->setRowHeight(20);
        $summaryRows = [
            ['Total Produk', $products->count()],
            ['Total Terjual (unit)', $total_terjual],
            ['Tidak Terjual (jenis)', $tidak_terjual],
            ['Total Pendapatan', $total_pendapatan],
            ['Total Keuntungan', $total_keuntungan],
            ['Stok Menipis', $low_stock],
        ];
        foreach ($summaryRows as $i => $row) {
            $r = $i + 7;
            $sheet->setCellValue("A{$r}", $row[0]);
            $sheet->setCellValue("B{$r}", $row[1]);
            $sheet->getStyle("A{$r}")->getFont()->setBold(true);
            if (in_array($row[0], ['Total Pendapatan', 'Total Keuntungan'])) {
                $sheet->getStyle("B{$r}")->getNumberFormat()->setFormatCode($rpFmt);
            }
        }

        // ── Column Headers ──────────────────────────
        $hRow = 14;
        $colHeaders = ['No', 'Kode Barang', 'Nama Produk', 'Jenis', 'Stock Awal', 'Terjual', 'Sisa Stock', 'Stock Min', 'Harga Modal', 'Harga Grosir', 'Harga Ecer', 'Pendapatan', 'Keuntungan', 'Status'];
        foreach ($colHeaders as $ci => $label2) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ci + 1);
            $sheet->setCellValue("{$col}{$hRow}", $label2);
        }
        $sheet->getStyle("A{$hRow}:N{$hRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E36C09']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C0C0C0']]],
        ]);
        $sheet->getRowDimension($hRow)->setRowHeight(22);

        // ── Data Rows ────────────────────────────────
        $rowIdx = $hRow + 1;
        foreach ($products as $i => $p) {
            $low    = $p->kuantitas <= $p->stock_minimum;
            $sold   = $p->terjual > 0;
            $status = match (true) {
                $sold && $low => 'Terjual · Low Stock',
                $sold         => 'Terjual',
                $low          => 'Stok Menipis',
                default       => 'Tidak Terjual',
            };

            $rowData = [
                $i + 1, $p->kode_barang, $p->nama_barang, $p->jenis_barang,
                $p->stock_awal, $p->terjual ?: 0, $p->kuantitas, $p->stock_minimum,
                (float) $p->modal_awal, (float) $p->harga_grosir, (float) $p->harga_ecer,
                $p->pendapatan, $p->terjual > 0 ? $p->keuntungan : 0, $status,
            ];
            foreach ($rowData as $ci => $val) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ci + 1);
                $sheet->setCellValue("{$col}{$rowIdx}", $val);
            }
            foreach (['I', 'J', 'K', 'L', 'M'] as $c) {
                $sheet->getStyle("{$c}{$rowIdx}")->getNumberFormat()->setFormatCode($rpFmt);
            }
            $bg = $low ? 'FFF1F2' : (($rowIdx % 2 === 0) ? 'F8FAFC' : 'FFFFFF');
            $sheet->getStyle("A{$rowIdx}:N{$rowIdx}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $rowIdx++;
        }

        // ── Totals Row ───────────────────────────────
        $sheet->setCellValue("D{$rowIdx}", 'TOTAL');
        $sheet->setCellValue("E{$rowIdx}", $products->sum('stock_awal'));
        $sheet->setCellValue("F{$rowIdx}", $total_terjual);
        $sheet->setCellValue("G{$rowIdx}", $products->sum('kuantitas'));
        $sheet->setCellValue("L{$rowIdx}", $total_pendapatan);
        $sheet->setCellValue("M{$rowIdx}", $total_keuntungan);
        foreach (['L', 'M'] as $c) {
            $sheet->getStyle("{$c}{$rowIdx}")->getNumberFormat()->setFormatCode($rpFmt);
        }
        $sheet->getStyle("A{$rowIdx}:N{$rowIdx}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4f46e5']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '3730a3']]],
        ]);
        $sheet->getRowDimension($rowIdx)->setRowHeight(24);

        // ── Column widths ─────────────────────────────
        $colWidths = ['A'=>5,'B'=>14,'C'=>28,'D'=>12,'E'=>10,'F'=>9,'G'=>10,'H'=>10,'I'=>16,'J'=>16,'K'=>16,'L'=>16,'M'=>16,'N'=>18];
        foreach ($colWidths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // ── Sheet Keterangan (sheet terpisah) ─────────────────────────────
        $kSheet = $spreadsheet->createSheet();
        $kSheet->setTitle('Keterangan');
        $kSheet->getColumnDimension('A')->setWidth(8);
        $kSheet->getColumnDimension('B')->setWidth(20);
        $kSheet->getColumnDimension('C')->setWidth(90);
        $this->writeLegend($kSheet, 1, 'C', [
            ['E', 'Stock Awal',    'Perkiraan jumlah stok di awal hari. Rumus: Sisa Stok saat ini + Total Terjual hari ini. (Estimasi — bisa berbeda jika ada transaksi setelah laporan dicetak.)'],
            ['F', 'Terjual',       'Total unit barang yang berhasil terjual sepanjang tanggal laporan ini.'],
            ['G', 'Sisa Stock',    'Jumlah stok yang masih tersisa saat laporan ini dicetak. Rumus: Stock Awal − Terjual.'],
            ['H', 'Stock Min',     'Batas stok minimum yang ditentukan. Jika Sisa Stock ≤ nilai ini, barang perlu segera direstock (ditandai merah).'],
            ['I', 'Harga Modal',   'Harga beli / HPP (Harga Pokok Penjualan) per unit barang dari supplier.'],
            ['J', 'Harga Grosir',  'Harga jual untuk pembelian partai besar / grosir.'],
            ['K', 'Harga Ecer',    'Harga jual untuk pembelian satuan / ecer.'],
            ['L', 'Pendapatan',    'Total pemasukan dari penjualan barang ini hari ini. Rumus: Harga Jual × Qty Terjual. Bernilai 0 jika tidak ada penjualan.'],
            ['M', 'Keuntungan',    'Profit bersih dari penjualan barang ini. Rumus: Pendapatan − (Harga Modal × Qty Terjual). Bernilai 0 jika tidak ada penjualan hari ini.'],
            ['N', 'Status',        'Terjual = ada penjualan hari ini  |  Stok Menipis = stok ≤ minimum, perlu restock  |  Terjual · Low Stock = terjual tapi stok sudah menipis  |  Tidak Terjual = tidak ada transaksi hari ini.'],
        ]);

        // Kembali ke sheet utama
        $spreadsheet->setActiveSheetIndex(0);

        $writer   = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');
        $writer->save($tempFile);
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Tulis section keterangan kolom ke worksheet, mulai dari $startRow.
     * $entries = [ ['Kolom', 'Nama Kolom', 'Keterangan / Rumus'], ... ]
     */
    private function writeLegend(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        int  $startRow,
        string $lastDataCol,
        array  $entries,
        string $footerNote = ''
    ): void {
        $row = $startRow;

        // ── Judul seksi ───────────────────────────────────────────────────
        $sheet->setCellValue("A{$row}", 'KETERANGAN KOLOM');
        $sheet->mergeCells("A{$row}:{$lastDataCol}{$row}");
        $sheet->getStyle("A{$row}:{$lastDataCol}{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e293b']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                            'indent'     => 1],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(22);
        $row++;

        // ── Sub-header ────────────────────────────────────────────────────
        $sheet->setCellValue("A{$row}", 'Kolom');
        $sheet->setCellValue("B{$row}", 'Nama Kolom');
        $sheet->setCellValue("C{$row}", 'Keterangan / Rumus Perhitungan');
        $sheet->mergeCells("C{$row}:{$lastDataCol}{$row}");
        $sheet->getStyle("A{$row}:{$lastDataCol}{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4f46e5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                             'color'       => ['rgb' => '3730a3']]],
        ]);
        $sheet->getStyle("C{$row}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)->setIndent(1);
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        // ── Baris keterangan ─────────────────────────────────────────────
        foreach ($entries as $idx => [$colLetter, $colName, $desc]) {
            $bg = ($idx % 2 === 0) ? 'F8FAFC' : 'FFFFFF';

            $sheet->setCellValue("A{$row}", $colLetter);
            $sheet->setCellValue("B{$row}", $colName);
            $sheet->setCellValue("C{$row}", $desc);
            $sheet->mergeCells("C{$row}:{$lastDataCol}{$row}");

            $sheet->getStyle("A{$row}:{$lastDataCol}{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,
                                                 'color'       => ['rgb' => 'E5E7EB']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'font'      => ['size' => 9],
            ]);
            // Kolom letter: bold + ungu
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)
                ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('4338ca'));
            $sheet->getStyle("A{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            // Nama kolom: bold
            $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setSize(9);
            // Keterangan: wrap
            $sheet->getStyle("C{$row}")->getAlignment()
                ->setWrapText(true)->setIndent(1);
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        // ── Footnote ─────────────────────────────────────────────────────
        $note = $footerNote ?: 'Laporan ini digenerate otomatis oleh sistem. Data bersumber dari database transaksi real-time.';
        $sheet->setCellValue("A{$row}", '  * ' . $note);
        $sheet->mergeCells("A{$row}:{$lastDataCol}{$row}");
        $sheet->getStyle("A{$row}:{$lastDataCol}{$row}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 8, 'color' => ['rgb' => '94a3b8']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical'   => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(15);
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
            return \Carbon\Carbon::parse($request->date)->locale('id')->isoFormat('D MMMM Y');
        } elseif ($request->type === 'monthly' && $request->month) {
            return \Carbon\Carbon::createFromFormat('Y-m', $request->month)->locale('id')->isoFormat('MMMM Y');
        } elseif ($request->type === 'yearly' && $request->year) {
            return 'Tahun ' . $request->year;
        }
        return now()->locale('id')->isoFormat('D MMMM Y');
    }
}
