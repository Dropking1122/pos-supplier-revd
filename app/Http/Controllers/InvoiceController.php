<?php
namespace App\Http\Controllers;
use App\Models\{Sale, Setting};
class InvoiceController extends Controller {
    public function show($id) {
        $sale = Sale::with(['customer','details.product'])->findOrFail($id);
        $setting = Setting::getSettings();
        return view('sales.invoice', compact('sale','setting'));
    }
}
