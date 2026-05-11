<?php
namespace App\Livewire\Sales;
use App\Models\{Customer, Product, Sale, SaleDetail, Debt, Setting};
use Livewire\Component;
use Illuminate\Support\Str;
class SaleCreate extends Component {
    public $customer_id = '', $payment_type = 'cash', $due_date = '', $notes = '';
    public $items = [];
    public $productSearch = '', $productResults = [], $showProductSearch = false;
    public function mount() { $this->items = []; }
    public function updatedProductSearch() {
        if (strlen($this->productSearch) >= 2) {
            $this->productResults = Product::where('nama_barang','like',"%{$this->productSearch}%")
                ->orWhere('kode_barang','like',"%{$this->productSearch}%")
                ->where('kuantitas','>',0)->take(5)->get()->toArray();
            $this->showProductSearch = true;
        } else { $this->productResults = []; $this->showProductSearch = false; }
    }
    public function selectProduct($productId) {
        $product = Product::findOrFail($productId);
        foreach ($this->items as $i => $item) {
            if ($item['product_id'] == $productId) {
                $this->items[$i]['quantity']++;
                $this->recalcItem($i);
                $this->productSearch = ''; $this->showProductSearch = false; return;
            }
        }
        $this->items[] = [
            'product_id'=>$product->id,'nama_barang'=>$product->nama_barang,
            'kode_barang'=>$product->kode_barang,'price_type'=>'ecer',
            'unit_price'=>$product->harga_ecer,'quantity'=>1,
            'harga_ecer'=>$product->harga_ecer,'harga_grosir'=>$product->harga_grosir,
            'stok'=>$product->kuantitas,'subtotal'=>$product->harga_ecer,
        ];
        $this->productSearch = ''; $this->showProductSearch = false;
    }
    public function updatePriceType($index) {
        $item = $this->items[$index];
        $this->items[$index]['unit_price'] = $item['price_type'] === 'grosir' ? $item['harga_grosir'] : $item['harga_ecer'];
        $this->recalcItem($index);
    }
    public function recalcItem($index) {
        $this->items[$index]['subtotal'] = $this->items[$index]['unit_price'] * $this->items[$index]['quantity'];
    }
    public function removeItem($index) { array_splice($this->items, $index, 1); }
    public function getTotal() { return collect($this->items)->sum('subtotal'); }
    public function save() {
        if (empty($this->items)) { session()->flash('error','Tambahkan minimal 1 barang!'); return; }
        $invoiceNumber = 'INV-'.date('Ymd').'-'.strtoupper(Str::random(5));
        $total = $this->getTotal();
        $amountPaid = $this->payment_type === 'cash' ? $total : 0;
        $status = $this->payment_type === 'cash' ? 'paid' : 'unpaid';
        $sale = Sale::create([
            'invoice_number'=>$invoiceNumber,'customer_id'=>$this->customer_id ?: null,
            'total_amount'=>$total,'amount_paid'=>$amountPaid,
            'payment_type'=>$this->payment_type,'status'=>$status,
            'due_date'=>$this->payment_type === 'tempo' ? $this->due_date : null,'notes'=>$this->notes,
        ]);
        foreach ($this->items as $item) {
            SaleDetail::create([
                'sale_id'=>$sale->id,'product_id'=>$item['product_id'],'price_type'=>$item['price_type'],
                'unit_price'=>$item['unit_price'],'quantity'=>$item['quantity'],'subtotal'=>$item['subtotal'],
            ]);
            Product::findOrFail($item['product_id'])->decrement('kuantitas',$item['quantity']);
        }
        if ($this->payment_type === 'tempo' && $this->customer_id) {
            Debt::create([
                'customer_id'=>$this->customer_id,'sale_id'=>$sale->id,
                'total_hutang'=>$total,'total_bayar'=>0,'sisa_hutang'=>$total,
                'jatuh_tempo'=>$this->due_date,'status'=>'belum_lunas',
            ]);
        }
        session()->flash('message','Transaksi berhasil disimpan! Invoice: '.$invoiceNumber);
        return redirect()->route('sales.index');
    }
    public function render() {
        $customers = Customer::orderBy('name')->get();
        $setting = Setting::getSettings();
        return view('livewire.sales.sale-create', compact('customers','setting'));
    }
}
