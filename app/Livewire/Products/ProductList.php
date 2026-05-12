<?php
namespace App\Livewire\Products;
use App\Models\Product;
use App\Models\SaleDetail;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component {
    use WithPagination;
    public $search = '';
    public $showModal = false;
    public $editId = null;
    public $kode_barang = '', $nama_barang = '', $jenis_barang = '', $kuantitas = 0;
    public $modal_awal = 0, $harga_grosir = 0, $harga_ecer = 0, $harga_satuan = '', $stock_minimum = 5;
    public $sortField = 'nama_barang', $sortDirection = 'asc';
    public $filterLowStock = false;

    public $showRestockModal = false;
    public $restockId = null, $restockNama = '', $restockJumlah = 0;

    protected $queryString = ['filterLowStock' => ['except' => false]];

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
    public function updatingFilterLowStock() { $this->resetPage(); }

    public function sort($field) {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

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
    public function openRestock($id) {
        $p = Product::findOrFail($id);
        $this->restockId    = $id;
        $this->restockNama  = $p->nama_barang;
        $this->restockJumlah= 0;
        $this->showRestockModal = true;
    }
    public function restock() {
        $this->validate(['restockJumlah' => 'required|integer|min:1'], [
            'restockJumlah.min' => 'Jumlah restock minimal 1.',
        ]);
        Product::findOrFail($this->restockId)->increment('kuantitas', $this->restockJumlah);
        session()->flash('message', 'Restock berhasil! Stok ditambah '.$this->restockJumlah.' unit.');
        $this->showRestockModal = false;
    }
    public function render() {
        $sortableAggregates = ['total_terjual', 'total_pendapatan'];
        $query = Product::withSum('saleDetails as total_terjual', 'quantity')
            ->withSum('saleDetails as total_pendapatan', 'subtotal')
            ->where(function($q) {
                $q->where('nama_barang','like',"%{$this->search}%")
                  ->orWhere('kode_barang','like',"%{$this->search}%");
            })
            ->when($this->filterLowStock, fn($q) => $q->whereColumn('kuantitas','<=','stock_minimum'));

        if (in_array($this->sortField, $sortableAggregates)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $products = $query->paginate(10);

        $totalProduk    = Product::count();
        $totalTerjual   = SaleDetail::sum('quantity');
        $totalPendapatan= SaleDetail::sum('subtotal');
        $lowStockCount  = Product::whereColumn('kuantitas','<=','stock_minimum')->count();

        return view('livewire.products.product-list', compact(
            'products','totalProduk','totalTerjual','totalPendapatan','lowStockCount'
        ));
    }
}
