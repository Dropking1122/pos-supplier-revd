<?php
namespace App\Http\Controllers;
use App\Models\{Sale, Setting};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller {
    public function pdf(Request $request) {
        $sales = $this->getSales($request);
        $setting = Setting::getSettings();
        $totalRevenue = $sales->sum('total_amount');
        $totalProfit = $sales->sum(fn($s)=>$s->details->sum(fn($d)=>$d->subtotal-($d->quantity*($d->product->modal_awal??0))));
        $pdf = Pdf::loadView('reports.pdf', compact('sales','setting','totalRevenue','totalProfit','request'));
        return $pdf->download('laporan-penjualan.pdf');
    }
    public function excel(Request $request) {
        $sales = $this->getSales($request);
        $setting = Setting::getSettings();
        $filename = 'laporan-penjualan-'.now()->format('Ymd').'.csv';
        $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment; filename="'.$filename.'"'];
        $callback = function() use ($sales,$setting) {
            $f = fopen('php://output','w');
            fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($f, ['Laporan Penjualan - '.$setting->company_name]);
            fputcsv($f, []);
            fputcsv($f, ['Invoice','Tanggal','Customer','Total','Status']);
            foreach ($sales as $s) {
                fputcsv($f, [$s->invoice_number,$s->created_at->format('d/m/Y'),$s->customer?->name??'Umum',$s->total_amount,$s->status==='paid'?'Lunas':($s->status==='partial'?'Sebagian':'Belum Bayar')]);
            }
            fputcsv($f, []);
            fputcsv($f, ['Total Revenue','Rp '.number_format($sales->sum('total_amount'),0,',','.')]);
            fclose($f);
        };
        return response()->stream($callback,200,$headers);
    }
    private function getSales(Request $request) {
        $q = Sale::with(['customer','details.product'])->latest();
        if ($request->type === 'daily' && $request->date) $q->whereDate('created_at',$request->date);
        elseif ($request->type === 'monthly' && $request->month) { [$y,$m]=explode('-',$request->month); $q->whereYear('created_at',$y)->whereMonth('created_at',$m); }
        elseif ($request->type === 'yearly' && $request->year) $q->whereYear('created_at',$request->year);
        return $q->get();
    }
}
