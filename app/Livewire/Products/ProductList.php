<?php
namespace App\Livewire\Products;
use App\Models\Product;
use App\Models\SaleDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ProductList extends Component {
    use WithPagination;
    public $search = '';
    public $showModal = false;
    public $editId = null;
    public $kode_barang = '', $nama_barang = '', $jenis_barang = '', $kuantitas = 0;
    public $modal_awal = 0, $harga_grosir = 0, $harga_ecer = 0, $harga_satuan = '', $stock_minimum = 5;
    protected $rules = [
        'kode_barang'  => 'required|string|max:100',
        'nama_barang'  => 'required|string|max:255',
        'jenis_barang' => 'nullable|string',
        'kuantitas'    => 'required|integer|min:0',
        'modal_awal'   => 'required|numeric|min:0',
        'harga_grosir' => 'required|numeric|min:0',
        'harga_ecer'   => 'required|numeric|min:0',
        'harga_satuan' => 'nullable|string',
        'stock_minimum'=> 'required|integer|min:0',
    ];
    public function updatingSearch() { $this->resetPage(); }
    public function openCreate() {
        $this->reset(['editId','kode_barang','nama_barang','jenis_barang','kuantitas','modal_awal','harga_grosir','harga_ecer','harga_satuan','stock_minimum']);
        $this->stock_minimum = 5;
        $this->showModal = true;
    }
    public function openEdit($id) {
        $p = Product::findOrFail($id);
        $this->editId      = $id;
        $this->kode_barang = $p->kode_barang;
        $this->nama_barang = $p->nama_barang;
        $this->jenis_barang= $p->jenis_barang;
        $this->kuantitas   = $p->kuantitas;
        $this->modal_awal  = $p->modal_awal;
        $this->harga_grosir= $p->harga_grosir;
        $this->harga_ecer  = $p->harga_ecer;
        $this->harga_satuan= $p->harga_satuan;
        $this->stock_minimum=$p->stock_minimum;
        $this->showModal   = true;
    }
    public function save() {
        $this->validate();
        if ($this->editId) {
            Product::findOrFail($this->editId)->update($this->only(['kode_barang','nama_barang','jenis_barang','kuantitas','modal_awal','harga_grosir','harga_ecer','harga_satuan','stock_minimum']));
            session()->flash('message','Produk berhasil diupdate!');
        } else {
            Product::create($this->only(['kode_barang','nama_barang','jenis_barang','kuantitas','modal_awal','harga_grosir','harga_ecer','harga_satuan','stock_minimum']));
            session()->flash('message','Produk berhasil ditambahkan!');
        }
        $this->showModal = false;
    }
    public function delete($id) {
        Product::findOrFail($id)->delete();
        session()->flash('message','Produk berhasil dihapus!');
    }
    public function render() {
        $products = Product::withSum('saleDetails as total_terjual', 'quantity')
            ->withSum('saleDetails as total_pendapatan', 'subtotal')
            ->where(function($q) {
                $q->where('nama_barang','like',"%{$this->search}%")
                  ->orWhere('kode_barang','like',"%{$this->search}%");
            })
            ->paginate(10);

        $topProducts = Product::select('products.*')
            ->join('sale_details','products.id','=','sale_details.product_id')
            ->selectRaw('products.*, SUM(sale_details.quantity) as total_terjual, SUM(sale_details.subtotal) as total_pendapatan')
            ->groupBy('products.id','products.kode_barang','products.nama_barang','products.jenis_barang','products.kuantitas','products.modal_awal','products.harga_grosir','products.harga_ecer','products.harga_satuan','products.stock_minimum','products.created_at','products.updated_at')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $totalProduk    = Product::count();
        $totalTerjual   = SaleDetail::sum('quantity');
        $totalPendapatan= SaleDetail::sum('subtotal');
        $lowStockCount  = Product::whereColumn('kuantitas','<=','stock_minimum')->count();

        return view('livewire.products.product-list', compact(
            'products','topProducts','totalProduk','totalTerjual','totalPendapatan','lowStockCount'
        ));
    }
}
