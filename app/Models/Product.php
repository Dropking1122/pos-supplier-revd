<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
    protected $fillable = ['kode_barang','nama_barang','jenis_barang','kuantitas','modal_awal','harga_grosir','harga_ecer','harga_satuan','stock_minimum'];
    protected $casts = ['modal_awal'=>'decimal:2','harga_grosir'=>'decimal:2','harga_ecer'=>'decimal:2'];
    public function saleDetails() { return $this->hasMany(SaleDetail::class); }
    public function isLowStock(): bool { return $this->kuantitas <= $this->stock_minimum; }
}
