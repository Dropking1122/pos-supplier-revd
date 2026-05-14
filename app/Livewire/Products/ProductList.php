<?php
namespace App\Livewire\Products;
use App\Models\Product;
use App\Models\SaleDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ProductList extends Component {
    use WithPagination, WithFileUploads;
    public $search = '';
    public $showModal = false;
    public $editId = null;
    public $kode_barang = '', $nama_barang = '', $jenis_barang = '', $kuantitas = 0;
    public $modal_awal = 0, $harga_grosir = 0, $harga_ecer = 0, $harga_satuan = '', $stock_minimum = 5;
    public $sortField = 'nama_barang', $sortDirection = 'asc';
    public $filterLowStock = false;

    public $showRestockModal = false;
    public $restockId = null, $restockNama = '', $restockJumlah = 0;

    public $showImportModal = false;
    public $importFile = null;
    public $importErrors = [];
    public $importSuccess = 0;

    public $showDeleteModal = false;
    public $deleteId = null;
    public $deleteNama = '';
    public $deleteKode = '';
    public $deleteStok = 0;

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

    protected $messages = [
        'kode_barang.required' => 'Kode barang wajib diisi.',
        'nama_barang.required' => 'Nama barang wajib diisi.',
        'kuantitas.min'        => 'Kuantitas tidak boleh negatif.',
        'modal_awal.min'       => 'Modal awal tidak boleh negatif.',
        'harga_grosir.min'     => 'Harga grosir tidak boleh negatif.',
        'harga_ecer.min'       => 'Harga ecer tidak boleh negatif.',
        'stock_minimum.min'    => 'Stok minimum tidak boleh negatif.',
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
        $uniqueRule = $this->editId
            ? 'unique:products,kode_barang,'.$this->editId
            : 'unique:products,kode_barang';
        $this->validate(array_merge($this->rules, [
            'kode_barang' => ['required', 'string', 'max:100', $uniqueRule],
        ]), array_merge($this->messages, [
            'kode_barang.unique' => 'Kode barang sudah digunakan produk lain.',
        ]));

        $modal   = (float) $this->modal_awal;
        $grosir  = (float) $this->harga_grosir;
        $ecer    = (float) $this->harga_ecer;

        if ($grosir > 0 && $grosir < $modal) {
            $this->addError('harga_grosir', 'Harga grosir (Rp '.number_format($grosir,0,',','.').'.) lebih rendah dari modal (Rp '.number_format($modal,0,',','.').') — akan menyebabkan kerugian.');
            return;
        }
        if ($ecer > 0 && $ecer < $modal) {
            $this->addError('harga_ecer', 'Harga ecer (Rp '.number_format($ecer,0,',','.').'.) lebih rendah dari modal (Rp '.number_format($modal,0,',','.').') — akan menyebabkan kerugian.');
            return;
        }
        if ($ecer > 0 && $grosir > 0 && $ecer < $grosir) {
            $this->addError('harga_ecer', 'Harga ecer tidak boleh lebih rendah dari harga grosir.');
            return;
        }

        if ($this->editId) {
            Product::findOrFail($this->editId)->update($this->only(['kode_barang','nama_barang','jenis_barang','kuantitas','modal_awal','harga_grosir','harga_ecer','harga_satuan','stock_minimum']));
            $this->dispatch('toast', type: 'success', message: 'Produk '.$this->nama_barang.' berhasil diperbarui.');
        } else {
            Product::create($this->only(['kode_barang','nama_barang','jenis_barang','kuantitas','modal_awal','harga_grosir','harga_ecer','harga_satuan','stock_minimum']));
            $this->dispatch('toast', type: 'success', message: 'Produk '.$this->nama_barang.' berhasil ditambahkan.');
        }
        $this->showModal = false;
    }
    public function confirmDelete($id) {
        $p = Product::findOrFail($id);
        $this->deleteId   = $id;
        $this->deleteNama = $p->nama_barang;
        $this->deleteKode = $p->kode_barang;
        $this->deleteStok = $p->kuantitas;
        $this->showDeleteModal = true;
    }
    public function deleteProduct() {
        if (!$this->deleteId) return;
        $p = Product::findOrFail($this->deleteId);
        $nama = $p->nama_barang;
        $p->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->dispatch('toast', type: 'success', message: 'Produk "'.$nama.'" berhasil dihapus.');
    }
    public function delete($id) {
        $p = Product::findOrFail($id);
        $nama = $p->nama_barang;
        $p->delete();
        $this->dispatch('toast', type: 'success', message: 'Produk "'.$nama.'" berhasil dihapus.');
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
        $p = Product::findOrFail($this->restockId);
        $p->increment('kuantitas', $this->restockJumlah);
        $this->dispatch('toast', type: 'success', message: 'Restock "'.$p->nama_barang.'" berhasil. Stok ditambah '.$this->restockJumlah.' unit.');
        $this->showRestockModal = false;
    }

    public function importProducts()
    {
        $this->validate(['importFile' => 'required|file|mimes:xlsx,xls,csv|max:5120'], [
            'importFile.required' => 'Pilih file terlebih dahulu.',
            'importFile.mimes'    => 'Format file harus .xlsx, .xls, atau .csv.',
            'importFile.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $path = $this->importFile->getRealPath();
        $ext  = strtolower($this->importFile->getClientOriginalExtension());

        $rows = [];
        if ($ext === 'csv') {
            $handle = fopen($path, 'r');
            $header = array_map('trim', fgetcsv($handle) ?: []);
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 2 && array_filter($row)) {
                    $rows[] = array_combine(array_slice($header, 0, count($row)), array_map('trim', $row));
                }
            }
            fclose($handle);
        } else {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($path);
            $data   = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            $header = array_map('trim', array_shift($data) ?: []);
            foreach ($data as $row) {
                $row = array_map(fn($v) => trim((string)($v ?? '')), $row);
                if (array_filter($row)) {
                    $rows[] = array_combine(array_slice($header, 0, count($row)), $row);
                }
            }
        }

        $imported = 0;
        $errors   = [];
        foreach ($rows as $i => $row) {
            $rowNum = $i + 2;
            $kode   = trim($row['kode_barang'] ?? '');
            $nama   = trim($row['nama_barang'] ?? '');
            if (!$kode || !$nama) {
                $errors[] = "Baris {$rowNum}: kode_barang dan nama_barang wajib diisi.";
                continue;
            }
            $data = [
                'kode_barang'   => $kode,
                'nama_barang'   => $nama,
                'jenis_barang'  => $row['jenis_barang'] ?? '',
                'kuantitas'     => (int) ($row['kuantitas'] ?? 0),
                'harga_satuan'  => $row['harga_satuan'] ?? '',
                'modal_awal'    => (float) str_replace([',', '.'], ['', ''], $row['modal_awal'] ?? 0),
                'harga_grosir'  => (float) str_replace([',', '.'], ['', ''], $row['harga_grosir'] ?? 0),
                'harga_ecer'    => (float) str_replace([',', '.'], ['', ''], $row['harga_ecer'] ?? 0),
                'stock_minimum' => (int) ($row['stock_minimum'] ?? 5),
            ];
            Product::updateOrCreate(['kode_barang' => $kode], $data);
            $imported++;
        }

        $this->importFile    = null;
        $this->importErrors  = $errors;
        $this->importSuccess = $imported;

        if ($imported > 0 && !$errors) {
            $this->showImportModal = false;
            $this->dispatch('toast', type: 'success', title: 'Import Berhasil', message: "{$imported} produk berhasil diimport/diperbarui.");
        } elseif ($imported > 0) {
            $this->dispatch('toast', type: 'warning', title: 'Import Sebagian', message: "{$imported} produk berhasil, ".count($errors)." baris gagal.");
        } else {
            $this->dispatch('toast', type: 'error', title: 'Import Gagal', message: 'Tidak ada data yang berhasil diimport. Periksa format file.');
        }
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
